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
use App\Http\Controllers\User\UserOrderController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Payment\KaseraPayWebhookController;
use App\Http\Controllers\Payment\PaymentCheckoutController;
use App\Http\Controllers\Payment\PaymentStatusController;

use App\Http\Controllers\Admin\AdminTestimonialController;
use App\Http\Controllers\Admin\AdminUserLogController;
use App\Models\Testimonial;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Landing Page (Monochromatic JalurGaib)
Route::get('/', function () {

    $favPath = public_path('favicon.ico');
    if (!file_exists($favPath) || filesize($favPath) < 100) {
        if (file_exists(public_path('images/logo.png'))) {
            @copy(public_path('images/logo.png'), $favPath);
            @copy(public_path('images/logo.png'), public_path('favicon.png'));
            @copy(public_path('images/logo.png'), public_path('apple-touch-icon.png'));
        }
    }

    $staleView = storage_path('framework/views/a5faac23e1d6bb34174bcb7baadc2395.php');
    if (file_exists($staleView)) {
        @unlink($staleView);
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

// Fallback direct logo route with strict path traversal prevention
Route::get('images/{filename}', function ($filename) {
    $cleanName = basename($filename);
    
    // Strict validation: Reject directory separators or invalid image names
    if ($cleanName !== $filename || !preg_match('/^[a-zA-Z0-9_\-\.]+\.(png|jpg|jpeg|svg|ico|webp)$/i', $cleanName)) {
        return abort(404);
    }

    $filePath = public_path('images/' . $cleanName);
    if (file_exists($filePath)) {
        $mime = match (strtolower(pathinfo($filePath, PATHINFO_EXTENSION))) {
            'png'   => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'svg'   => 'image/svg+xml',
            'ico'   => 'image/x-icon',
            'webp'  => 'image/webp',
            default => 'application/octet-stream',
        };
        return response()->file($filePath, ['Content-Type' => $mime]);
    }

    return abort(404);
})->where('filename', '[a-zA-Z0-9_\-\.]+');

// Favicon & SEO Assets direct handler
Route::get('favicon.ico', function () {
    $icoPath = public_path('favicon.ico');
    $logoPath = public_path('images/logo.png');

    if (!file_exists($icoPath) || filesize($icoPath) < 100) {
        if (file_exists($logoPath)) {
            @copy($logoPath, $icoPath);
        }
    }

    if (file_exists($icoPath) && filesize($icoPath) > 0) {
        return response()->file($icoPath, [
            'Content-Type'  => 'image/x-icon',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    if (file_exists($logoPath)) {
        return response()->file($logoPath, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }

    return abort(404);
});

Route::get('favicon.png', function () {
    $logoPath = public_path('images/logo.png');
    if (file_exists($logoPath)) {
        return response()->file($logoPath, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
    return abort(404);
});

Route::get('apple-touch-icon.png', function () {
    $logoPath = public_path('images/logo.png');
    if (file_exists($logoPath)) {
        return response()->file($logoPath, [
            'Content-Type'  => 'image/png',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
    return abort(404);
});

Route::get('sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (file_exists($path)) {
        return response()->file($path, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
    return abort(404);
});

// Guest Authentication Routes (with Rate Limiting against brute-force)
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('login',                   [AuthController::class, 'showLogin'])->name('login');
    Route::post('login',                  [AuthController::class, 'login'])->name('login.post');
    Route::get('register',                [AuthController::class, 'showRegister'])->name('register');
    Route::post('register',               [AuthController::class, 'register'])->name('register.post');
    Route::post('auth/firebase',          [AuthController::class, 'firebaseAuth'])->name('auth.firebase');
    Route::post('auth/firebase-register', [AuthController::class, 'firebaseRegister'])->name('auth.firebase.register');
});

// Kasera Payment Gateway Server-to-Server Webhook (CSRF Exempted in bootstrap/app.php)
Route::post('kasera-pay/webhook', KaseraPayWebhookController::class)->name('kasera.webhook');

// Standalone Developer REST API Documentation (Terbuka di Halaman Baru Mandiri)
Route::get('docs', function () { return view('docs'); })->name('docs');

// Legal Pages for Google Firebase OAuth Branding & Verification
Route::get('privacy-policy', function () { return view('legal.privacy'); })->name('privacy');
Route::get('privacy', function () { return redirect()->route('privacy'); });

Route::get('terms-and-conditions', function () { return view('legal.terms'); })->name('terms');
Route::get('terms', function () { return redirect()->route('terms'); });
Route::get('tos', function () { return redirect()->route('terms'); });

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
    Route::get('orders',    [UserOrderController::class, 'index'])->name('user.orders');
    Route::get('profile',   [UserProfileController::class, 'edit'])->name('user.profile');
    Route::put('profile',   [UserProfileController::class, 'updateProfile'])->name('user.profile.update');
    Route::put('profile/password', [UserProfileController::class, 'updatePassword'])->name('user.profile.password');
    Route::post('checkout', [PaymentCheckoutController::class, 'store'])->name('user.checkout.store');
    Route::post('checkout/cancel/{order_no}', [PaymentCheckoutController::class, 'cancel'])->name('user.checkout.cancel');
    Route::get('payment/finish', [PaymentStatusController::class, 'finish'])->name('user.payment.finish');
    Route::get('user/api/stats', [UserDashboardController::class, 'getStats'])->name('user.stats');

    // ─── ADMIN / APLIKATOR DASHBOARD (Super Admin) ───────────────────────────
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('docs', function () { return redirect()->route('docs'); })->name('docs');
        
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

        // User Activity Logs (Login, Logout, Pembayaran, Checkout)
        Route::get('logs', [AdminUserLogController::class, 'index'])->name('logs.index');
        Route::post('logs/clear-old', [AdminUserLogController::class, 'clearOld'])->name('logs.clear-old');

        // Admin Internal Management APIs
        Route::get('api/stats',    [AdminDashboardController::class, 'getGlobalStats'])->name('stats');
        Route::get('api/health',   [AdminDashboardController::class, 'getSystemHealth'])->name('health');
        Route::get('api/devices',  [AdminDashboardController::class, 'getGlobalDevices'])->name('devices');
    });
});
