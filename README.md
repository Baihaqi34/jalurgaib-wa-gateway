# WA Gateway — Anti-Ban WhatsApp Gateway

A production-grade WhatsApp Gateway built with **Laravel 11** (Backend API) + **Go + whatsmeow** (WhatsApp microservice).

## Architecture

```
┌─────────────────────────────────────┐
│         Your App / Client           │
│   (uses API Key to call Laravel)    │
└────────────────┬────────────────────┘
                 │ HTTPS + X-API-Key
┌────────────────▼────────────────────┐
│         Laravel 11 Backend          │
│  - API Gateway                      │
│  - Queue Manager (Redis)            │
│  - Rate limiting                    │
│  - Device & Message management      │
└────────────────┬────────────────────┘
                 │ Internal HTTP + X-API-Key
┌────────────────▼────────────────────┐
│         Go WA Microservice          │
│  - whatsmeow (WA protocol)          │
│  - Multi-device session manager     │
│  - Anti-ban random delay            │
│  - Auto-reconnect on disconnect     │
│  - SQLite session persistence       │
└─────────────────────────────────────┘
```

## Anti-Ban Features

| Feature | Description |
|---|---|
| **Random delay** | Each message waits 1–4 seconds before sending (configurable) |
| **Queue system** | Messages go through Redis queue — no bursting |
| **Bulk stagger** | Bulk messages are delayed 5 seconds apart |
| **Auto-reconnect** | Go service auto-reconnects on disconnect |
| **Session persistence** | SQLite stores session, no re-scan needed on restart |
| **Per-device isolation** | Each device is its own whatsmeow client instance |

## Setup

### Prerequisites
- PHP 8.3+
- Composer
- Go 1.21+
- MySQL 8.0+
- Redis 7+
- GCC (required for go-sqlite3 CGO)

### 1. Laravel Setup

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
# Edit .env with your DB credentials
php artisan migrate
php artisan queue:work redis --queue=default
```

### 2. Go Service Setup

```bash
cd wa-service
cp .env.example .env
# Edit .env — set INTERNAL_API_KEY (must match WA_SERVICE_API_KEY in Laravel .env)
go mod tidy
go run main.go
```

### 3. Connect a Device

```bash
# 1. Register user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name": "Admin", "email": "admin@example.com", "password": "password"}'

# 2. Login to get token
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "admin@example.com", "password": "password"}'

# 3. Create a device
curl -X POST http://localhost:8000/api/devices \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "My WA Number", "phone_number": "6281234567890"}'

# 4. Scan QR via API (returns raw QR string, base64 SVG Data URI, or HTML view)
curl -X GET http://localhost:8000/api/devices/1/scan \
  -H "Authorization: Bearer YOUR_TOKEN"

# 5. Send a message
curl -X POST http://localhost:8000/api/messages/send \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"device_id": "dev_xyz", "to": "6281234567890", "message": "Hello!"}'
```

## API Reference

### Auth
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/auth/register` | Register user |
| POST | `/api/auth/login` | Login, get token |
| POST | `/api/auth/logout` | Logout |

### Devices (CRUD & Scan API)
| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/devices` | List all user devices with live synced status |
| POST | `/api/devices` | Create new device (`name`, `phone_number`) |
| GET | `/api/devices/{id}` | Get detail of single device (by numeric ID or string `device_id`) |
| PUT | `/api/devices/{id}` | Update device label/phone |
| DELETE | `/api/devices/{id}` | Delete device & terminate active session |
| GET | `/api/devices/{id}/status` | Get live connection status from WA engine |
| GET | `/api/devices/{id}/scan` | **Scan via API** (returns raw QR string, base64 SVG Data URI, or HTML view) |
| GET | `/api/devices/{id}/qr` | Direct SVG QR Code image stream (`image/svg+xml`) |
| POST | `/api/devices/{id}/connect` | Initiate pairing session & trigger QR generation |
| POST | `/api/devices/{id}/disconnect` | Disconnect WhatsApp session |

### Messages
| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/messages/send` | Send single message |
| POST | `/api/messages/send-bulk` | Send bulk (queued) |
| GET | `/api/messages` | Message history |

## Folder Structure

```
wa-gateway/
├── backend/          # Laravel 11 API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Http/Requests/ (StoreDeviceRequest, UpdateDeviceRequest)
│   │   ├── Http/Resources/ (DeviceResource)
│   │   ├── Jobs/SendWhatsAppMessage.php
│   │   ├── Models/ (Device, Message, ApiKey)
│   │   └── Services/WhatsAppService.php
│   └── ...
└── wa-service/       # Go whatsmeow microservice
    ├── main.go
    ├── go.mod
    ├── Makefile
    └── data/         # SQLite session files
```
