<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = null;

        // 1. Check if external client provided X-API-Key or api_key query param
        $key = $request->header('X-API-Key')
            ?? $request->query('api_key');

        if ($key) {
            $apiKey = ApiKey::where('key', $key)->with('user')->first();

            if (!$apiKey || !$apiKey->isValid()) {
                return response()->json(['success' => false, 'message' => 'Invalid or expired API key'], 401);
            }

            // Update last used timestamp
            $apiKey->update(['last_used_at' => now()]);
            $user = $apiKey->user;
        } elseif (auth()->check()) {
            // 2. Web Session Authentication
            $user = auth()->user();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid X-API-Key header or log in.'
            ], 401);
        }

        // 3. Enforce Ban / Suspend Status
        if ($user->isSuspended() && !$user->isAdmin()) {
            $reason = $user->suspend_reason ?: 'Akun Anda ditangguhkan oleh administrator karena indikasi pelanggaran aturan layanan.';
            return response()->json([
                'success'   => false,
                'message'   => 'Akses Ditolak: ' . $reason,
                'suspended' => true,
                'reason'    => $reason,
            ], 403);
        }

        $request->setUserResolver(fn() => $user);
        auth()->setUser($user);

        return $next($request);
    }
}
