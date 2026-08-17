<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function __construct(private readonly WhatsAppService $waService) {}

    private function getUser(Request $request): User
    {
        $user = $request->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }
        return $user;
    }

    public function index(Request $request): JsonResponse
    {
        $keys = $this->getUser($request)->apiKeys()->latest()->get()->makeVisible('key');
        return response()->json(['success' => true, 'data' => $keys]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $user = $this->getUser($request);
        $apiKey = $user->apiKeys()->create([
            'name'       => $validated['name'],
            'key'        => ApiKey::generateKey(),
            'is_active'  => true,
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'API key berhasil dibuat.',
            'data'    => $apiKey->makeVisible('key'),
        ], 201);
    }

    public function revoke(Request $request, ApiKey $apiKey): JsonResponse
    {
        $user = $this->getUser($request);

        if ($apiKey->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki akses ke API key milik akun lain.');
        }

        $apiKey->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'API key revoked.',
        ]);
    }

    /**
     * Verify an API key and return authorized devices belonging strictly to that key's owner.
     */
    public function verify(Request $request): JsonResponse
    {
        $key = $request->header('X-API-Key')
            ?? $request->input('key')
            ?? $request->query('api_key');

        if (!$key) {
            return response()->json([
                'success' => false,
                'message' => 'Header X-API-Key atau parameter key diperlukan.'
            ], 400);
        }

        $apiKey = ApiKey::where('key', $key)->with('user.devices')->first();

        if (!$apiKey || !$apiKey->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'API Key tidak valid, tidak aktif, atau sudah kedaluwarsa.'
            ], 401);
        }

        // Sync live statuses of this user's devices
        $devices = $apiKey->user->devices;
        foreach ($devices as $dev) {
            $this->waService->getDeviceStatus($dev);
        }

        return response()->json([
            'success' => true,
            'message' => 'API Key valid!',
            'data' => [
                'name'         => $apiKey->name,
                'user'         => [
                    'id'    => $apiKey->user->id,
                    'name'  => $apiKey->user->name,
                    'email' => $apiKey->user->email,
                ],
                'last_used_at' => $apiKey->last_used_at,
                'devices'      => $devices->fresh()->makeHidden(['created_at', 'updated_at']),
            ]
        ]);
    }
}
