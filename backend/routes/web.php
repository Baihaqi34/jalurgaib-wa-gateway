<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Landing Page (Monochromatic JalurGaib.wa)
Route::get('/', function () {
    if (!file_exists(public_path('images/logo.png')) && file_exists(public_path('process_logo.php'))) {
        @include_once public_path('process_logo.php');
    }

    $packages = \App\Models\Package::whereIn('status', ['active', 'coming_soon'])
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    return view('welcome', compact('packages'));
})->name('landing');

// Fallback direct logo route
Route::get('images/{filename}', function ($filename) {
    $filePath = public_path('images/' . $filename);
    if (!file_exists($filePath) && file_exists(public_path('process_logo.php'))) {
        @include_once public_path('process_logo.php');
    }

    if (file_exists($filePath)) {
        return response()->file($filePath, ['Content-Type' => 'image/png']);
    }

    return abort(404);
})->where('filename', '.*');
// Route::get('/coba', function () {

//     $response = Http::withHeaders([
//         'X-API-Key' => 'wag_QaGY4a1cfUtANvovwAUFM3KG5bDPhP5Kc6M2sbK86q0m6n9D',
//         'Accept'    => 'application/json',
//     ])->post('http://localhost:8000/api/messages/send', [
//         'device_id' => 'dev_trArD5x6fg1G',
//         'to'        => '6283141847231',
//         'message'   => 'Halo! Pesanan #1042 Anda sedang dikirim via kurir 🚚',
//         'delay'     => 8,
//     ]);

//     if ($response->successful()) {
//         return response()->json([
//             'success' => true,
//             'data' => $response->json(),
//         ]);
//     }

//     return response()->json([
//         'success' => false,
//         'status' => $response->status(),
//         'error' => $response->json(),
//     ], $response->status());

// })->name('coba');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register',[AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // ─── USER / CLIENT TENANT DASHBOARD ──────────────────────────────────────
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('upgrade',   [UserDashboardController::class, 'upgrade'])->name('user.upgrade');
    Route::get('user/api/stats', [UserDashboardController::class, 'getStats'])->name('user.stats');
    Route::get('docs', function () { return view('docs'); })->name('docs');

    // ─── ADMIN / APLIKATOR DASHBOARD (Super Admin) ───────────────────────────
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('docs', function () { return view('docs'); })->name('docs');
        
        // Resource CRUD for User Management
        Route::resource('users', AdminUserController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('users/{user}/suspend',  [AdminUserController::class, 'suspend'])->name('users.suspend');
        Route::post('users/{user}/activate', [AdminUserController::class, 'activate'])->name('users.activate');

        // Resource CRUD for Package & Tier Management
        Route::resource('packages', AdminPackageController::class)->only(['index', 'store', 'update', 'destroy']);

        // Admin Internal Management APIs
        Route::get('api/stats',    [AdminDashboardController::class, 'getGlobalStats'])->name('stats');
        Route::get('api/health',   [AdminDashboardController::class, 'getSystemHealth'])->name('health');
        Route::get('api/devices',  [AdminDashboardController::class, 'getGlobalDevices'])->name('devices');
    });
});
