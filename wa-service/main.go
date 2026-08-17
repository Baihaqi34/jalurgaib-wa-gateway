package main

import (
	"context"
	"encoding/json"
	"fmt"
	"log"
	"math/rand"
	"net/http"
	"os"
	"os/signal"
	"strconv"
	"strings"
	"sync"
	"syscall"
	"time"

	"github.com/gorilla/mux"
	"github.com/joho/godotenv"
	_ "modernc.org/sqlite"
	"go.mau.fi/whatsmeow"
	"go.mau.fi/whatsmeow/proto/waE2E"
	"go.mau.fi/whatsmeow/store"
	"go.mau.fi/whatsmeow/store/sqlstore"
	"go.mau.fi/whatsmeow/types"
	"go.mau.fi/whatsmeow/types/events"
	waLog "go.mau.fi/whatsmeow/util/log"
	"google.golang.org/protobuf/proto"
)

// ─── Session Manager ──────────────────────────────────────────────────────────

type SessionManager struct {
	mu      sync.RWMutex
	clients map[string]*whatsmeow.Client // key: deviceID (e.g. "dev_xyz")
	store   *sqlstore.Container
}

var sessionMgr *SessionManager

func newSessionManager(dbPath string) *SessionManager {
	dbLog := waLog.Stdout("DB", "WARN", true)
	container, err := sqlstore.New(context.Background(), "sqlite", fmt.Sprintf("file:%s?_foreign_keys=on&_busy_timeout=10000&_journal_mode=WAL", dbPath), dbLog)
	if err != nil {
		log.Fatalf("Failed to create sqlstore: %v", err)
	}
	return &SessionManager{
		clients: make(map[string]*whatsmeow.Client),
		store:   container,
	}
}

func (sm *SessionManager) GetClient(deviceID string) (*whatsmeow.Client, bool) {
	sm.mu.Lock()
	defer sm.mu.Unlock()

	// 1. Direct match in memory
	if client, ok := sm.clients[deviceID]; ok {
		return client, true
	}

	// 2. Lookup from persistent mapping (data/devices.json)
	devMap := loadDeviceMap()
	if jidStr, ok := devMap[deviceID]; ok {
		jid, err := types.ParseJID(jidStr)
		if err == nil {
			devStore, err := sm.store.GetDevice(context.Background(), jid)
			if err == nil && devStore != nil && devStore.ID != nil {
				clientLog := waLog.Stdout("Client", "INFO", true)
				client := whatsmeow.NewClient(devStore, clientLog)
				setupEventHandler(client, deviceID)
				_ = client.Connect()
				sm.clients[deviceID] = client
				return client, true
			}
		}
	}

	// 3. Fallback to any active logged-in client in memory and map this deviceID to it
	for oldID, client := range sm.clients {
		if client != nil && (client.IsLoggedIn() || client.IsConnected()) {
			delete(sm.clients, oldID)
			sm.clients[deviceID] = client
			log.Printf("Mapped active WhatsApp session [%s] to requested device ID [%s]", oldID, deviceID)
			if client.Store.ID != nil {
				saveDeviceMap(deviceID, client.Store.ID.String())
			}
			return client, true
		}
	}

	// 4. Fallback to any paired device stored in SQLite database
	devices, err := sm.store.GetAllDevices(context.Background())
	if err == nil {
		for _, devStore := range devices {
			if devStore.ID != nil {
				clientLog := waLog.Stdout("Client", "INFO", true)
				client := whatsmeow.NewClient(devStore, clientLog)
				setupEventHandler(client, deviceID)
				if err := client.Connect(); err == nil {
					sm.clients[deviceID] = client
					saveDeviceMap(deviceID, devStore.ID.String())
					log.Printf("Auto-loaded paired WhatsApp session from DB for [%s]: %s", deviceID, devStore.ID.String())
					return client, true
				}
			}
		}
	}

	return nil, false
}

func (sm *SessionManager) AddClient(deviceID string, client *whatsmeow.Client) {
	sm.mu.Lock()
	defer sm.mu.Unlock()
	sm.clients[deviceID] = client
	if client.Store.ID != nil {
		saveDeviceMap(deviceID, client.Store.ID.String())
	}
}

func (sm *SessionManager) RemoveClient(deviceID string) {
	sm.mu.Lock()
	defer sm.mu.Unlock()
	if c, ok := sm.clients[deviceID]; ok {
		c.Disconnect()
		delete(sm.clients, deviceID)
	}
	removeDeviceMap(deviceID)
}

// ─── Device ID Mapping Persistence ──────────────────────────────────────────

func loadDeviceMap() map[string]string {
	data, err := os.ReadFile("./data/devices.json")
	if err != nil {
		return make(map[string]string)
	}
	var res map[string]string
	if err := json.Unmarshal(data, &res); err != nil {
		return make(map[string]string)
	}
	return res
}

func saveDeviceMap(deviceID, jidStr string) {
	_ = os.MkdirAll("./data", 0755)
	m := loadDeviceMap()
	m[deviceID] = jidStr
	data, _ := json.MarshalIndent(m, "", "  ")
	_ = os.WriteFile("./data/devices.json", data, 0644)
}

func removeDeviceMap(deviceID string) {
	m := loadDeviceMap()
	delete(m, deviceID)
	data, _ := json.MarshalIndent(m, "", "  ")
	_ = os.WriteFile("./data/devices.json", data, 0644)
}

// ─── Anti-Ban Rate Limiter ────────────────────────────────────────────────────

func randomDelay(minMs, maxMs int) {
	if minMs <= 0 && maxMs <= 0 {
		return
	}
	if minMs < 0 {
		minMs = 0
	}
	if maxMs < minMs {
		maxMs = minMs
	}
	diff := maxMs - minMs
	if diff <= 0 {
		time.Sleep(time.Duration(minMs) * time.Millisecond)
		return
	}
	delay := minMs + rand.Intn(diff)
	time.Sleep(time.Duration(delay) * time.Millisecond)
}

func corsMiddleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Access-Control-Allow-Origin", "*")
		w.Header().Set("Access-Control-Allow-Methods", "GET, POST, OPTIONS, PUT, DELETE")
		w.Header().Set("Access-Control-Allow-Headers", "Content-Type, X-API-Key, Authorization, Accept")

		if r.Method == http.MethodOptions {
			w.WriteHeader(http.StatusOK)
			return
		}

		next.ServeHTTP(w, r)
	})
}

func panicRecoveryMiddleware(next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		defer func() {
			if err := recover(); err != nil {
				log.Printf("[Panic Recovered] %v", err)
				respondJSON(w, http.StatusInternalServerError, APIResponse{
					Success: false,
					Message: fmt.Sprintf("Internal WA service error: %v", err),
				})
			}
		}()
		next.ServeHTTP(w, r)
	})
}

func validateAPIKey(r *http.Request) bool {
	key := r.Header.Get("X-API-Key")
	expected := os.Getenv("INTERNAL_API_KEY")
	return key == expected && expected != ""
}

func authMiddleware(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		if !validateAPIKey(r) {
			respondJSON(w, http.StatusUnauthorized, APIResponse{Success: false, Message: "Unauthorized"})
			return
		}
		next(w, r)
	}
}

// ─── HTTP Request/Response Types ─────────────────────────────────────────────

type ConnectRequest struct {
	DeviceID string `json:"device_id"`
}

type SendTextRequest struct {
	DeviceID string `json:"device_id"`
	To       string `json:"to"`
	Message  string `json:"message"`
	MinDelay int    `json:"min_delay"`
	MaxDelay int    `json:"max_delay"`
}

type APIResponse struct {
	Success bool        `json:"success"`
	Message string      `json:"message"`
	Data    interface{} `json:"data,omitempty"`
}

func respondJSON(w http.ResponseWriter, status int, payload APIResponse) {
	w.Header().Set("Access-Control-Allow-Origin", "*")
	w.Header().Set("Access-Control-Allow-Methods", "GET, POST, OPTIONS, PUT, DELETE")
	w.Header().Set("Access-Control-Allow-Headers", "Content-Type, X-API-Key, Authorization, Accept")
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(status)
	json.NewEncoder(w).Encode(payload)
}

func formatPhone(phone string) types.JID {
	phone = strings.ReplaceAll(phone, "+", "")
	phone = strings.ReplaceAll(phone, " ", "")
	phone = strings.ReplaceAll(phone, "-", "")
	if strings.HasPrefix(phone, "0") {
		phone = "62" + strings.TrimLeft(phone, "0")
	}
	return types.NewJID(phone, types.DefaultUserServer)
}

// ─── Handlers ─────────────────────────────────────────────────────────────────

func handleConnect(w http.ResponseWriter, r *http.Request) {
	var req ConnectRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil || req.DeviceID == "" {
		respondJSON(w, http.StatusBadRequest, APIResponse{Success: false, Message: "Invalid request body"})
		return
	}

	if client, exists := sessionMgr.GetClient(req.DeviceID); exists {
		if client.IsConnected() && client.IsLoggedIn() {
			respondJSON(w, http.StatusOK, APIResponse{
				Success: true,
				Message: "Device already connected",
				Data:    map[string]string{"status": "connected", "device_id": req.DeviceID},
			})
			return
		}
	}

	ctx := context.Background()
	devices, err := sessionMgr.store.GetAllDevices(ctx)
	var deviceStore *store.Device

	if err == nil {
		for _, d := range devices {
			if d.ID != nil {
				// Check if this device store is already assigned to active client
				alreadyActive := false
				sessionMgr.mu.RLock()
				for _, c := range sessionMgr.clients {
					if c.Store.ID != nil && c.Store.ID.String() == d.ID.String() {
						alreadyActive = true
						break
					}
				}
				sessionMgr.mu.RUnlock()

				if !alreadyActive {
					deviceStore = d
					break
				}
			}
		}
	}

	if deviceStore == nil {
		deviceStore = sessionMgr.store.NewDevice()
	}

	clientLog := waLog.Stdout("Client", "INFO", true)
	client := whatsmeow.NewClient(deviceStore, clientLog)
	setupEventHandler(client, req.DeviceID)

	if client.Store.ID == nil {
		// Needs QR Pairing
		qrChan, _ := client.GetQRChannel(ctx)
		if err := client.Connect(); err != nil {
			respondJSON(w, http.StatusInternalServerError, APIResponse{Success: false, Message: fmt.Sprintf("Connect error: %v", err)})
			return
		}

		var qrCode string
		select {
		case evt := <-qrChan:
			if evt.Event == "code" {
				qrCode = evt.Code
				log.Printf("[%s] QR Code: %s", req.DeviceID, evt.Code)
			}
		case <-time.After(5 * time.Second):
		}

		go func() {
			for evt := range qrChan {
				if evt.Event == "code" {
					log.Printf("[%s] QR Code refreshed: %s", req.DeviceID, evt.Code)
				}
			}
		}()

		sessionMgr.AddClient(req.DeviceID, client)
		respondJSON(w, http.StatusOK, APIResponse{
			Success: true,
			Message: "QR code generated. Scan the QR code image in your browser.",
			Data: map[string]string{
				"status":    "waiting_qr",
				"device_id": req.DeviceID,
				"qr_code":   qrCode,
			},
		})
	} else {
		// Already paired in SQLite storage
		if err := client.Connect(); err != nil {
			respondJSON(w, http.StatusInternalServerError, APIResponse{Success: false, Message: fmt.Sprintf("Reconnect error: %v", err)})
			return
		}
		sessionMgr.AddClient(req.DeviceID, client)
		respondJSON(w, http.StatusOK, APIResponse{
			Success: true,
			Message: "Device reconnected using existing session.",
			Data: map[string]string{
				"status":    "connected",
				"device_id": req.DeviceID,
				"jid":       client.Store.ID.String(),
			},
		})
	}
}

func handleStatus(w http.ResponseWriter, r *http.Request) {
	deviceID := r.URL.Query().Get("device_id")
	if deviceID == "" {
		respondJSON(w, http.StatusBadRequest, APIResponse{Success: false, Message: "device_id required"})
		return
	}

	client, exists := sessionMgr.GetClient(deviceID)
	if !exists {
		respondJSON(w, http.StatusOK, APIResponse{
			Success: true,
			Message: "Device not initialized",
			Data: map[string]interface{}{
				"device_id": deviceID,
				"connected": false,
				"logged_in": false,
			},
		})
		return
	}

	data := map[string]interface{}{
		"device_id": deviceID,
		"connected": client.IsConnected(),
		"logged_in": client.IsLoggedIn(),
	}
	if client.Store.ID != nil {
		data["jid"] = client.Store.ID.String()
	}

	respondJSON(w, http.StatusOK, APIResponse{Success: true, Message: "OK", Data: data})
}

func handleDisconnect(w http.ResponseWriter, r *http.Request) {
	var req ConnectRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil || req.DeviceID == "" {
		respondJSON(w, http.StatusBadRequest, APIResponse{Success: false, Message: "Invalid request"})
		return
	}

	sessionMgr.RemoveClient(req.DeviceID)
	respondJSON(w, http.StatusOK, APIResponse{Success: true, Message: "Device disconnected"})
}

func handleSendText(w http.ResponseWriter, r *http.Request) {
	var req SendTextRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		respondJSON(w, http.StatusBadRequest, APIResponse{Success: false, Message: "Invalid request body"})
		return
	}

	if req.DeviceID == "" || req.To == "" || req.Message == "" {
		respondJSON(w, http.StatusBadRequest, APIResponse{Success: false, Message: "device_id, to, and message are required"})
		return
	}

	client, exists := sessionMgr.GetClient(req.DeviceID)
	if !exists {
		respondJSON(w, http.StatusNotFound, APIResponse{Success: false, Message: "Device session not active. Connect device first."})
		return
	}

	if !client.IsConnected() {
		_ = client.Connect() // auto attempt reconnect if socket dropped
		time.Sleep(1 * time.Second)
	}

	if client.Store.ID == nil || !client.IsLoggedIn() {
		respondJSON(w, http.StatusUnauthorized, APIResponse{Success: false, Message: "Device is not logged in / paired to WhatsApp."})
		return
	}

	// ─── Anti-Ban: Apply random delay before sending ───────────────────────
	minDelay := 1000
	maxDelay := 3000
	if req.MinDelay > 0 {
		minDelay = req.MinDelay
	}
	if req.MaxDelay > 0 {
		maxDelay = req.MaxDelay
	}
	if maxDelay < minDelay {
		maxDelay = minDelay + 1000
	}
	randomDelay(minDelay, maxDelay)

	jid := formatPhone(req.To)
	msg := &waE2E.Message{
		Conversation: proto.String(req.Message),
	}

	ts, err := client.SendMessage(context.Background(), jid, msg)
	if err != nil {
		log.Printf("[%s] SendMessage error to %s: %v", req.DeviceID, req.To, err)
		respondJSON(w, http.StatusInternalServerError, APIResponse{Success: false, Message: fmt.Sprintf("Send error: %v", err)})
		return
	}

	log.Printf("[%s] Message sent successfully to %s (ID: %s)", req.DeviceID, req.To, ts.ID)
	respondJSON(w, http.StatusOK, APIResponse{
		Success: true,
		Message: "Message sent successfully",
		Data: map[string]interface{}{
			"message_id": ts.ID,
			"timestamp":  ts.Timestamp,
		},
	})
}

func handleHealth(w http.ResponseWriter, r *http.Request) {
	sessionMgr.mu.RLock()
	count := len(sessionMgr.clients)
	activeList := make([]map[string]interface{}, 0)
	for devID, client := range sessionMgr.clients {
		item := map[string]interface{}{
			"device_id": devID,
			"connected": client.IsConnected(),
			"logged_in": client.IsLoggedIn(),
		}
		if client.Store.ID != nil {
			item["jid"] = client.Store.ID.String()
		}
		activeList = append(activeList, item)
	}
	sessionMgr.mu.RUnlock()

	respondJSON(w, http.StatusOK, APIResponse{
		Success: true,
		Message: "WA Service running",
		Data: map[string]interface{}{
			"active_devices": count,
			"devices":        activeList,
			"uptime":         time.Now().Format(time.RFC3339),
		},
	})
}

func setupEventHandler(client *whatsmeow.Client, deviceID string) {
	client.AddEventHandler(func(evt interface{}) {
		switch v := evt.(type) {
		case *events.Message:
			log.Printf("[%s] Received message from %s: %s",
				deviceID,
				v.Info.Sender.String(),
				v.Message.GetConversation(),
			)
		case *events.Connected:
			log.Printf("[%s] Connected to WhatsApp", deviceID)
			if client.Store.ID != nil {
				saveDeviceMap(deviceID, client.Store.ID.String())
			}
		case *events.PairSuccess:
			log.Printf("[%s] Pair success! Logged in as %s", deviceID, v.ID.String())
			saveDeviceMap(deviceID, v.ID.String())
		case *events.LoggedOut:
			log.Printf("[%s] Logged out from WhatsApp", deviceID)
			removeDeviceMap(deviceID)
		case *events.Disconnected:
			log.Printf("[%s] Disconnected from WhatsApp — attempting reconnect...", deviceID)
			go func() {
				time.Sleep(3 * time.Second)
				_ = client.Connect()
			}()
		}
	})
}

func main() {
	_ = godotenv.Load()

	rand.Seed(time.Now().UnixNano())

	dbPath := os.Getenv("DB_PATH")
	if dbPath == "" {
		dbPath = "./data/whatsapp.db"
	}
	os.MkdirAll("./data", 0755)

	sessionMgr = newSessionManager(dbPath)

	// Auto-restore any existing paired WhatsApp sessions from SQLite database
	ctx := context.Background()
	devMap := loadDeviceMap()
	jidToDevID := make(map[string]string)
	for devID, jid := range devMap {
		jidToDevID[jid] = devID
	}

	devices, err := sessionMgr.store.GetAllDevices(ctx)
	if err == nil && len(devices) > 0 {
		clientLog := waLog.Stdout("Client", "INFO", true)
		for i, devStore := range devices {
			if devStore.ID != nil {
				deviceID, exists := jidToDevID[devStore.ID.String()]
				if !exists {
					deviceID = fmt.Sprintf("dev_auto_%d", i+1)
				}
				client := whatsmeow.NewClient(devStore, clientLog)
				setupEventHandler(client, deviceID)
				if err := client.Connect(); err != nil {
					log.Printf("Failed to auto-connect restored device [%s]: %v", deviceID, err)
				} else {
					sessionMgr.AddClient(deviceID, client)
					log.Printf("Auto-restored paired WhatsApp session: %s (%s)", deviceID, devStore.ID.String())
				}
			}
		}
	}

	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}

	r := mux.NewRouter()
	r.Use(corsMiddleware)
	r.Use(panicRecoveryMiddleware)

	api := r.PathPrefix("/api").Subrouter()

	api.HandleFunc("/health", handleHealth).Methods(http.MethodGet, http.MethodOptions)
	api.HandleFunc("/device/connect", authMiddleware(handleConnect)).Methods(http.MethodPost, http.MethodOptions)
	api.HandleFunc("/device/status", authMiddleware(handleStatus)).Methods(http.MethodGet, http.MethodOptions)
	api.HandleFunc("/device/disconnect", authMiddleware(handleDisconnect)).Methods(http.MethodPost, http.MethodOptions)
	api.HandleFunc("/message/send-text", authMiddleware(handleSendText)).Methods(http.MethodPost, http.MethodOptions)

	srv := &http.Server{
		Addr:         ":" + port,
		Handler:      r,
		ReadTimeout:  30 * time.Second,
		WriteTimeout: 30 * time.Second,
	}

	quit := make(chan os.Signal, 1)
	signal.Notify(quit, syscall.SIGINT, syscall.SIGTERM)

	go func() {
		log.Printf("WA Service starting on port %s", port)
		if err := srv.ListenAndServe(); err != nil && err != http.ErrServerClosed {
			log.Fatalf("Server error: %v", err)
		}
	}()

	<-quit
	log.Println("Shutting down gracefully...")

	shutdownCtx, cancel := context.WithTimeout(context.Background(), 10*time.Second)
	defer cancel()

	sessionMgr.mu.Lock()
	for id, client := range sessionMgr.clients {
		client.Disconnect()
		log.Printf("Disconnected device: %s", id)
	}
	sessionMgr.mu.Unlock()

	srv.Shutdown(shutdownCtx)
	log.Println("Service stopped")

	_ = strconv.Itoa(0)
}
