<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function __construct(private readonly WhatsAppService $waService) {}

    private function getUser(Request $request): User
    {
        $user = $request->user() ?? auth('web')->user() ?? auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated. Silakan sediakan API Key atau login kembali.');
        }
        return $user;
    }

    /**
     * List all devices for the authenticated user ONLY.
     * GET /api/devices
     */
    public function index(Request $request): JsonResponse
    {
        $user = $this->getUser($request);
        $devices = $user->devices()->latest()->get();

        foreach ($devices as $device) {
            $this->waService->getDeviceStatus($device);
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar perangkat berhasil diambil.',
            'data'    => DeviceResource::collection($devices->fresh()),
        ]);
    }

    /**
     * Create and register a new device under the authenticated user.
     * POST /api/devices
     */
    public function store(StoreDeviceRequest $request): JsonResponse
    {
        $user = $this->getUser($request);

        if (!$user->canAddDevice()) {
            $pkg = $user->getPackage();
            return response()->json([
                'success'         => false,
                'message'         => "Batas maksimal perangkat untuk paket {$pkg->name} (Maks {$user->getMaxDevices()} nomor) telah tercapai. Silakan upgrade paket Anda.",
                'error_code'      => 'DEVICE_LIMIT_REACHED',
                'current_devices' => $user->devices()->count(),
                'max_devices'     => $user->getMaxDevices(),
            ], 403);
        }

        $validated = $request->validated();
        $phone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $device = $user->devices()->create([
            'name'         => $validated['name'],
            'device_id'    => 'dev_' . Str::random(12),
            'phone_number' => $phone ?: $validated['phone_number'],
            'status'       => 'disconnected',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Perangkat berhasil ditambahkan. Silakan panggil endpoint /connect atau /scan untuk menghubungkan.',
            'data'    => new DeviceResource($device),
        ], 201);
    }

    /**
     * Get detail of a single device.
     * GET /api/devices/{device}
     */
    public function show(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        // Auto sync live status from Go engine
        $this->waService->getDeviceStatus($device);

        return response()->json([
            'success' => true,
            'message' => 'Detail perangkat berhasil diambil.',
            'data'    => new DeviceResource($device->fresh()),
        ]);
    }

    /**
     * Update device metadata (name / phone number).
     * PUT/PATCH /api/devices/{device}
     */
    public function update(UpdateDeviceRequest $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        $validated = $request->validated();
        $updateData = [];

        if (isset($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }

        if (!empty($validated['phone_number'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $updateData['phone_number'] = $phone ?: $validated['phone_number'];
        }

        if (!empty($updateData)) {
            $device->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data perangkat berhasil diperbarui.',
            'data'    => new DeviceResource($device->fresh()),
        ]);
    }

    /**
     * Delete a device.
     * DELETE /api/devices/{device}
     */
    public function destroy(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        // Disconnect first from Go WhatsApp engine
        $this->waService->disconnectDevice($device);
        $device->delete();

        return response()->json([
            'success' => true,
            'message' => 'Perangkat WhatsApp berhasil dihapus.',
        ]);
    }

    /**
     * Connect the device (initiate WhatsApp pairing).
     * POST /api/devices/{device}/connect
     */
    public function connect(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        $result = $this->waService->connectDevice($device);
        $isSuccess = is_array($result) && ($result['success'] ?? false);

        if ($isSuccess && isset($result['data'])) {
            $result['data']['device_id'] = $device->device_id;
            $result['data']['qr_image_url'] = url("/api/devices/{$device->device_id}/qr");
            $result['data']['status_url'] = url("/api/devices/{$device->device_id}/status");
        }

        return response()->json($result, $isSuccess ? 200 : 400);
    }

    /**
     * Dedicated Scan Endpoint (JSON payload or HTML viewer).
     * GET /api/devices/{device}/scan
     */
    public function scan(Request $request, Device $device)
    {
        $this->authorizeDevice($request, $device);

        // 1. Check if device is already connected
        $statusCheck = $this->waService->getDeviceStatus($device);
        if ($device->isConnected()) {
            if ($request->wantsJson() || $request->query('format') !== 'html') {
                return response()->json([
                    'success'   => true,
                    'status'    => 'connected',
                    'message'   => 'Perangkat sudah terhubung ke WhatsApp.',
                    'device'    => new DeviceResource($device),
                ]);
            }

            return response()->make("
                <div style='font-family:sans-serif; text-align:center; padding:50px;'>
                    <h2 style='color:#10b981;'>Perangkat Sudah Terhubung!</h2>
                    <p>Device <strong>{$device->name}</strong> ({$device->phone_number}) dalam kondisi Online.</p>
                </div>
            ", 200, ['Content-Type' => 'text/html']);
        }

        // 2. Request connection & QR from Go engine
        $connectResult = $this->waService->connectDevice($device);
        $qrCode = $connectResult['data']['qr_code'] ?? $connectResult['data']['qr'] ?? null;
        $qrBase64 = $connectResult['data']['qr_base64'] ?? null;

        // If client specifically requests HTML view (e.g. opened in browser)
        if ($request->query('format') === 'html' || (!$request->wantsJson() && !$request->is('api/*'))) {
            $qrImgTag = $qrBase64
                ? "<img src='{$qrBase64}' style='width:280px;height:280px;border-radius:12px;background:#fff;padding:12px;' />"
                : "<p style='color:#ef4444;'>Gagal memuat QR Code. Coba muat ulang halaman.</p>";

            $html = "
            <!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Scan WhatsApp - {$device->name}</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; }
                    .card { background: #1e293b; padding: 2rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.5); text-align: center; max-width: 380px; width: 90%; }
                    h2 { margin-top: 0; font-size: 1.25rem; }
                    p { color: #94a3b8; font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.4; }
                    .badge { display: inline-block; padding: 0.25rem 0.75rem; background: #334155; border-radius: 9999px; font-size: 0.75rem; color: #38bdf8; margin-bottom: 1rem; font-family: monospace; }
                </style>
            </head>
            <body>
                <div class='card'>
                    <div class='badge'>Device ID: {$device->device_id}</div>
                    <h2>Tautkan WhatsApp</h2>
                    <p>Buka WhatsApp di ponsel Anda > Setelan > Perangkat Tertaut > Tautkan Perangkat, lalu scan kode QR di bawah ini:</p>
                    <div>{$qrImgTag}</div>
                    <p style='margin-top:1.5rem; font-size:0.75rem;'>Halaman ini otomatis merefresh saat QR berganti.</p>
                </div>
                <script>
                    setTimeout(() => window.location.reload(), 20000);
                </script>
            </body>
            </html>
            ";

            return response()->make($html, 200, ['Content-Type' => 'text/html']);
        }

        return response()->json([
            'success'        => true,
            'status'         => 'waiting_qr',
            'device_id'      => $device->device_id,
            'qr_code'        => $qrCode,
            'qr_base64'      => $qrBase64,
            'qr_image_url'   => url("/api/devices/{$device->device_id}/qr"),
            'status_url'     => url("/api/devices/{$device->device_id}/status"),
            'instructions'   => 'Scan QR code dengan aplikasi WhatsApp ponsel Anda (Setelan > Perangkat Tertaut > Tautkan Perangkat).',
        ]);
    }

    /**
     * Direct SVG QR Code Image stream.
     * GET /api/devices/{device}/qr
     */
    public function qrImage(Request $request, Device $device): Response
    {
        $this->authorizeDevice($request, $device);

        $connectResult = $this->waService->connectDevice($device);
        $qrCode = $connectResult['data']['qr_code'] ?? $connectResult['data']['qr'] ?? null;

        if (!$qrCode) {
            $svgFallback = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300"><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#ef4444">QR Code tidak tersedia / Sudah Terhubung</text></svg>';
            return response($svgFallback, 200, ['Content-Type' => 'image/svg+xml']);
        }

        $svg = $connectResult['data']['qr_svg'] ?? $this->waService->generateQrSvg($qrCode, 300);

        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'no-cache, private, must-revalidate',
        ]);
    }

    /**
     * Get real-time device status from the Go service.
     * GET /api/devices/{device}/status
     */
    public function status(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        $result = $this->waService->getDeviceStatus($device);

        return response()->json([
            'success'   => true,
            'message'   => 'Status perangkat berhasil diperbarui.',
            'data'      => new DeviceResource($device->fresh()),
            'engine'    => $result['data'] ?? [],
        ]);
    }

    /**
     * Disconnect the device.
     * POST /api/devices/{device}/disconnect
     */
    public function disconnect(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        $result = $this->waService->disconnectDevice($device);
        $isSuccess = is_array($result) && ($result['success'] ?? false);

        return response()->json([
            'success' => $isSuccess,
            'message' => $isSuccess ? 'Perangkat WhatsApp berhasil diputuskan.' : ($result['message'] ?? 'Gagal memutuskan perangkat.'),
            'data'    => new DeviceResource($device->fresh()),
        ], $isSuccess ? 200 : 400);
    }

    /**
     * Ensure the authenticated user owns this device (or is super admin).
     */
    private function authorizeDevice(Request $request, Device $device): void
    {
        $user = $this->getUser($request);
        if ($device->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki hak akses ke perangkat WhatsApp milik pengguna lain.');
        }
    }
}
