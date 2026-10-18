<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Payment\KaseraPayWebhookController;
use App\Http\Controllers\Payment\PaymentCheckoutController;
use App\Http\Controllers\Payment\PaymentStatusController;

use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Models\Testimonial;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Landing Page (Monochromatic JalurGaib)
Route::get('/', function () {
    if (!file_exists(public_path('images/logo.png')) && file_exists(public_path('process_logo.php'))) {
        @include_once public_path('process_logo.php');
    }

    $packages = \App\Models\Package::whereIn('status', ['active', 'coming_soon'])
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'asc')
        ->get();

    $testimonials = collect();
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('testimonials')) {
            $testimonials = Testimonial::where('is_featured', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('id', 'desc')
                ->get();
        }
    } catch (\Throwable $e) {
        $testimonials = collect();
    }

    if ($testimonials->isEmpty()) {
        $testimonials = collect([
            (object)[
                'name' => 'Budi Pratama',
                'role_or_company' => 'CTO di KirimCepat Logistik',
                'avatar' => null,
                'initials' => 'BP',
                'rating' => 5,
                'content' => 'Integrasi API JalurGaib luar biasa cepat. Pengiriman OTP dan resi otomatis kami naik tingkat keberhasilannya hingga 99.8% tanpa delay.',
            ],
            (object)[
                'name' => 'Sarah Anggraini',
                'role_or_company' => 'Founder & Owner TokoModis.id',
                'avatar' => null,
                'initials' => 'SA',
                'rating' => 5,
                'content' => 'Dulu tim CS kami kewalahan balas ribuan broadcast promo manual. Sejak pakai JalurGaib, sistem webhook otomatis handle notifikasi order dalam hitungan detik!',
            ],
            (object)[
                'name' => 'Dian Kusuma',
                'role_or_company' => 'Lead DevOps di Finvest Tech',
                'avatar' => null,
                'initials' => 'DK',
                'rating' => 5,
                'content' => 'Multi-session engine Baileys-nya sangat stabil. Latensi webhook ke endpoint internal kami rata-rata hanya 120ms. Dashboard-nya clean dan modern.',
            ],
            (object)[
                'name' => 'Hendro Santoso',
                'role_or_company' => 'Operational Manager di WarungKopiku',
                'avatar' => null,
                'initials' => 'HS',
                'rating' => 5,
                'content' => 'Setup QR cuma butuh 10 detik. Fitur auto-reconnect membuat kami tenang tidak takut koneksi putus tiba-tiba di jam sibuk operasional restoran.',
            ],
        ]);
    }

    return view('welcome', compact('packages', 'testimonials'));
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
    Route::get('login',          [AuthController::class, 'showLogin'])->name('login');
    Route::post('login',         [AuthController::class, 'login'])->name('login.post');
    Route::get('register',       [AuthController::class, 'showRegister'])->name('register');
    Route::post('register',      [AuthController::class, 'register'])->name('register.post');
    Route::post('auth/firebase', [AuthController::class, 'firebaseAuth'])->name('auth.firebase');
});

// Kasera Payment Gateway Server-to-Server Webhook (CSRF Exempted in bootstrap/app.php)
Route::post('kasera-pay/webhook', KaseraPayWebhookController::class)->name('kasera.webhook');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // ─── USER / CLIENT TENANT DASHBOARD ──────────────────────────────────────
    Route::get('dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('messages',  [UserDashboardController::class, 'messages'])->name('user.messages');
    Route::get('devices',   [UserDashboardController::class, 'devices'])->name('user.devices');
    Route::get('history',   [UserDashboardController::class, 'history'])->name('user.history');
    Route::get('api-keys',  [UserDashboardController::class, 'apiKeys'])->name('user.api-keys');
    Route::get('upgrade',   [UserDashboardController::class, 'upgrade'])->name('user.upgrade');
    Route::get('profile',   [UserProfileController::class, 'edit'])->name('user.profile');
    Route::put('profile',   [UserProfileController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('profile/password', [UserProfileController::class, 'updatePassword'])->name('user.profile.password');
    Route::post('checkout', [PaymentCheckoutController::class, 'store'])->name('user.checkout.store');
    Route::post('checkout/cancel/{order_no}', [PaymentCheckoutController::class, 'cancel'])->name('user.checkout.cancel');
    Route::get('payment/finish', [PaymentStatusController::class, 'finish'])->name('user.payment.finish');
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

        // Orders & Payment Transactions from Kasera Pay
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');

        // Testimonials Management (Landing Page Reviews)
        Route::resource('testimonials', AdminTestimonialController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::post('testimonials/{testimonial}/toggle', [AdminTestimonialController::class, 'toggle'])->name('testimonials.toggle');

        // Admin Internal Management APIs
        Route::get('api/stats',    [AdminDashboardController::class, 'getGlobalStats'])->name('stats');
        Route::get('api/health',   [AdminDashboardController::class, 'getSystemHealth'])->name('health');
        Route::get('api/devices',  [AdminDashboardController::class, 'getGlobalDevices'])->name('devices');
    });
});
