<?php

use App\Http\Controllers\Api\ApiKeyController;
use App\Http\Controllers\Api\DeviceController;
use App\Http\Controllers\Api\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WA Gateway API Routes
|--------------------------------------------------------------------------
*/

// Auth routes (Sanctum token-based)
Route::prefix('auth')->group(function () {
    Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::post('login',    [\App\Http\Controllers\Auth\LoginController::class,    'login']);
    Route::middleware('auth:sanctum')->post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout']);
});

// Public Health & Status Checks
Route::get('health', function (\App\Services\WhatsAppService $waService) {
    $goHealthy = $waService->isHealthy();
    return response()->json([
        'success'    => true,
        'laravel'    => 'online',
        'go_service' => $goHealthy ? 'online' : 'offline',
        'timestamp'  => now()->toIso8601String(),
    ]);
});

// Public API Key verification & creation
Route::post('api-keys/verify', [ApiKeyController::class, 'verify']);
Route::post('api-keys/generate', [ApiKeyController::class, 'store']);
Route::apiResource('api-keys', ApiKeyController::class)->only(['index', 'store', 'destroy']);
Route::post('api-keys/{apiKey}/revoke', [ApiKeyController::class, 'revoke']);

// 🔒 Protected Gateway Routes (Wajib Header X-API-Key yang valid)
Route::middleware('api_key')->group(function () {
    // Device management
    Route::apiResource('devices', DeviceController::class);
    Route::post('devices/{device}/connect',    [DeviceController::class, 'connect']);
    Route::post('devices/{device}/disconnect', [DeviceController::class, 'disconnect']);
    Route::get('devices/{device}/status',      [DeviceController::class, 'status']);

    // Messaging
    Route::post('messages/send',      [MessageController::class, 'sendText']);
    Route::post('messages/send-bulk', [MessageController::class, 'sendBulk']);
    Route::get('messages',            [MessageController::class, 'index']);
});


