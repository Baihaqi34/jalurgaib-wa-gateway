<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function __construct(private readonly WhatsAppService $waService) {}

    private function getUser(Request $request): User
    {
        $user = $request->user() ?? auth('web')->user() ?? auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated. Silakan login kembali.');
        }
        return $user;
    }

    /**
     * List all devices for the authenticated user ONLY.
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
            'data'    => $devices->fresh(),
        ]);
    }

    /**
     * Create and register a new device under the authenticated user.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $user = $this->getUser($request);

        if (!$user->canAddDevice()) {
            $pkg = $user->getPackage();
            return response()->json([
                'success' => false,
                'message' => "Batas maksimal perangkat untuk paket {$pkg->name} (Maks {$user->getMaxDevices()} nomor) telah tercapai. Hubungi Admin untuk upgrade paket Anda.",
                'error_code' => 'DEVICE_LIMIT_REACHED',
                'current_devices' => $user->devices()->count(),
                'max_devices' => $user->getMaxDevices(),
            ], 403);
        }

        $device = $user->devices()->create([
            'name'         => $validated['name'],
            'device_id'    => 'dev_' . Str::random(12),
            'phone_number' => $validated['phone_number'],
            'status'       => 'disconnected',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device created. Use /connect to connect it.',
            'data'    => $device,
        ], 201);
    }

    /**
     * Connect the device (initiate WhatsApp pairing).
     */
    public function connect(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        $result = $this->waService->connectDevice($device);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Disconnect the device.
     */
    public function disconnect(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        $result = $this->waService->disconnectDevice($device);

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Get real-time device status from the Go service.
     */
    public function status(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        $result = $this->waService->getDeviceStatus($device);

        return response()->json($result);
    }

    /**
     * Delete a device.
     */
    public function destroy(Request $request, Device $device): JsonResponse
    {
        $this->authorizeDevice($request, $device);

        // Disconnect first
        $this->waService->disconnectDevice($device);
        $device->delete();

        return response()->json(['success' => true, 'message' => 'Device deleted']);
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
