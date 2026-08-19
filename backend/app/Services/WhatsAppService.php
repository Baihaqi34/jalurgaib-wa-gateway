<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.wa_service.url', config('wa_service.url', 'http://127.0.0.1:8080')), '/');
        $this->apiKey  = config('services.wa_service.api_key', config('wa_service.api_key', ''));
    }

    /**
     * Connect (or reconnect) a device with the Go WA service.
     */
    public function connectDevice(Device $device): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->post("{$this->baseUrl}/api/device/connect", [
                    'device_id' => $device->device_id,
                ]);

            $data = $response->json();

            if (!is_array($data)) {
                return [
                    'success' => false,
                    'message' => 'Go service tidak merespon format JSON yang valid (HTTP ' . $response->status() . '). Pastikan Go WhatsApp service aktif di port 8080.',
                ];
            }

            if ($response->successful() && ($data['success'] ?? false)) {
                $device->update(['status' => 'pending']);
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('[WA] connectDevice error', ['error' => $e->getMessage()]);
            return [
                'success' => false, 
                'message' => 'Gagal terhubung ke Go WhatsApp engine: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Disconnect a device from the Go WA service.
     */
    public function disconnectDevice(Device $device): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(10)
                ->post("{$this->baseUrl}/api/device/disconnect", [
                    'device_id' => $device->device_id,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                $device->update(['status' => 'disconnected']);
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('[WA] disconnectDevice error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get device status from the Go WA service and sync with MySQL DB.
     */
    public function getDeviceStatus(Device $device): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(10)
                ->get("{$this->baseUrl}/api/device/status", [
                    'device_id' => $device->device_id,
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['data'])) {
                $statusData = $data['data'];
                $newStatus = 'disconnected';

                if (!empty($statusData['logged_in'])) {
                    $newStatus = 'connected';
                } elseif (!empty($statusData['connected'])) {
                    $newStatus = 'pending';
                }

                $device->update([
                    'status' => $newStatus,
                    'jid'    => !empty($statusData['logged_in']) ? ($statusData['jid'] ?? null) : null,
                ]);
            }

            return $data ?? ['success' => false, 'message' => 'No response'];
        } catch (\Throwable $e) {
            Log::error('[WA] getDeviceStatus error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send a text message through the Go WA service.
     * This also handles the anti-ban delay logic.
     */
    public function sendText(Message $message, Device $device): array
    {
        Log::info('[WA Service] Requesting send-text to Go engine', [
            'url'       => "{$this->baseUrl}/api/message/send-text",
            'device_id' => $device->device_id,
            'to'        => $message->to,
            'message'   => $message->message,
            'media_url' => $message->media_url,
        ]);

        try {
            $payload = [
                'device_id' => $device->device_id,
                'to'        => $message->to,
                'message'   => $message->message,
                'min_delay' => (int) ($message->min_delay ?? 1000),
                'max_delay' => (int) ($message->max_delay ?? max(($message->min_delay ?? 1000) + 1000, 4000)),
            ];

            if (!empty($message->media_url)) {
                $payload['media_url'] = $message->media_url;
            }

            $response = Http::withHeaders($this->headers())
                ->timeout(60) // Long timeout because Go service adds delay
                ->post("{$this->baseUrl}/api/message/send-text", $payload);

            $data = $response->json();

            Log::info('[WA Service] Go Engine response', [
                'status_code' => $response->status(),
                'response'    => $data,
            ]);

            if ($response->successful() && ($data['success'] ?? false)) {
                $message->update([
                    'status'        => 'sent',
                    'wa_message_id' => $data['data']['message_id'] ?? null,
                    'sent_at'       => now(),
                ]);
            } else {
                $rawMsg = is_array($data) ? ($data['message'] ?? 'Unknown error') : ('HTTP ' . $response->status() . ' - ' . $response->body());
                $errMsg = $rawMsg;

                if (str_contains($rawMsg, '463')) {
                    $errMsg = 'WhatsApp Server membatasi chat ke nomor ini (Error 463). Pastikan nomor tujuan pernah membalas chat / simpan nomor di kontak HP pengirim.';
                } elseif (str_contains($rawMsg, 'no LID found') || str_contains($rawMsg, 'tidak terdaftar')) {
                    $errMsg = 'Nomor tidak terdaftar di WhatsApp atau salah format.';
                }

                $message->update([
                    'status'        => 'failed',
                    'error_message' => $errMsg,
                ]);
            }

            return is_array($data) ? $data : ['success' => false, 'message' => $errMsg];
        } catch (\Throwable $e) {
            Log::error('[WA Service] sendText exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $message->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Check if the Go WA service is healthy.
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/api/health");
            return $response->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    private function headers(): array
    {
        return [
            'X-API-Key'    => $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept'       => 'application/json',
        ];
    }
}
