<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AuthenticationLogService
{
    /**
     * Ensure the user_login_logs table exists dynamically with all required columns.
     */
    public function ensureTableExists(): void
    {
        if (!Schema::hasTable('user_login_logs')) {
            try {
                Schema::create('user_login_logs', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->string('email', 191)->index();
                    $table->string('name', 191)->nullable();
                    $table->string('role', 30)->default('user')->index();
                    $table->string('event', 30)->index();
                    $table->string('status', 20)->default('success')->index();
                    $table->string('login_method', 50)->default('email_password');
                    $table->string('ip_address', 45)->nullable()->index();
                    $table->text('user_agent')->nullable();
                    $table->string('device', 50)->nullable();
                    $table->string('platform', 50)->nullable();
                    $table->string('browser', 50)->nullable();
                    $table->string('failure_reason', 255)->nullable();
                    $table->string('description', 255)->nullable();
                    $table->timestamp('created_at')->useCurrent()->index();
                    $table->timestamp('updated_at')->nullable();
                });
            } catch (\Throwable $e) {
                Log::warning('Could not automatically create user_login_logs table: ' . $e->getMessage());
            }
        } elseif (!Schema::hasColumn('user_login_logs', 'description')) {
            try {
                Schema::table('user_login_logs', function (Blueprint $table) {
                    $table->string('description', 255)->nullable()->after('failure_reason');
                });
            } catch (\Throwable $e) {
                Log::warning('Could not add description column to user_login_logs: ' . $e->getMessage());
            }
        }
    }

    /**
     * Log a successful login attempt.
     */
    public function logLogin(User $user, Request $request, string $method = 'email_password'): ?UserLoginLog
    {
        return $this->record([
            'user_id'      => $user->id,
            'email'        => $user->email,
            'name'         => $user->name,
            'role'         => $user->role ?? ($user->isAdmin() ? 'admin' : 'user'),
            'event'        => 'login',
            'status'       => 'success',
            'login_method' => $method,
            'description'  => 'Berhasil masuk ke dalam sistem',
            'request'      => $request,
        ]);
    }

    /**
     * Log a user logout.
     */
    public function logLogout(?User $user, Request $request): ?UserLoginLog
    {
        if (!$user) {
            return null;
        }

        return $this->record([
            'user_id'      => $user->id,
            'email'        => $user->email,
            'name'         => $user->name,
            'role'         => $user->role ?? ($user->isAdmin() ? 'admin' : 'user'),
            'event'        => 'logout',
            'status'       => 'success',
            'login_method' => 'session',
            'description'  => 'Mengakhiri sesi dan keluar dari sistem',
            'request'      => $request,
        ]);
    }

    /**
     * Log a failed login attempt.
     */
    public function logFailed(string $email, Request $request, ?string $reason = 'Password atau kredensial salah'): ?UserLoginLog
    {
        $existingUser = User::where('email', $email)->first();

        return $this->record([
            'user_id'        => $existingUser?->id,
            'email'          => $email,
            'name'           => $existingUser?->name,
            'role'           => $existingUser?->role ?? 'user',
            'event'          => 'login_failed',
            'status'         => 'failed',
            'login_method'   => 'email_password',
            'failure_reason' => $reason,
            'description'    => "Percobaan login ditolak ({$reason})",
            'request'        => $request,
        ]);
    }

    /**
     * Log a new user registration.
     */
    public function logRegister(User $user, Request $request, string $method = 'email_password'): ?UserLoginLog
    {
        return $this->record([
            'user_id'      => $user->id,
            'email'        => $user->email,
            'name'         => $user->name,
            'role'         => $user->role ?? 'user',
            'event'        => 'register',
            'status'       => 'success',
            'login_method' => $method,
            'description'  => 'Pendaftaran akun baru berhasil',
            'request'      => $request,
        ]);
    }

    /**
     * Log an order checkout initiation by a user.
     */
    public function logCheckout(User $user, Order $order, ?Request $request = null): ?UserLoginLog
    {
        $pkgName = $order->package?->name ?? 'Langganan';
        $formattedAmount = 'Rp ' . number_format($order->amount, 0, ',', '.');

        return $this->record([
            'user_id'      => $user->id,
            'email'        => $user->email,
            'name'         => $user->name,
            'role'         => $user->role ?? 'user',
            'event'        => 'checkout',
            'status'       => 'pending',
            'login_method' => 'kasera_pay',
            'description'  => "Membuat pesanan {$pkgName} (#{$order->order_no}) senilai {$formattedAmount}",
            'request'      => $request ?? request(),
        ]);
    }

    /**
     * Log a successful payment confirmation from webhook.
     */
    public function logPaymentSuccess(Order $order, int $paidAmount, ?string $paymentMethod = null): ?UserLoginLog
    {
        $user = $order->user;
        $pkgName = $order->package?->name ?? 'Langganan';
        $formattedAmount = 'Rp ' . number_format($paidAmount ?: $order->amount, 0, ',', '.');
        $channel = $paymentMethod ? strtoupper($paymentMethod) : 'Kasera Pay';

        return $this->record([
            'user_id'      => $order->user_id,
            'email'        => $user?->email ?? "user_{$order->user_id}@jalurgaib.com",
            'name'         => $user?->name ?? 'User Pembeli',
            'role'         => $user?->role ?? 'user',
            'event'        => 'payment_success',
            'status'       => 'success',
            'login_method' => $paymentMethod ?? 'kasera_pay',
            'description'  => "Pembayaran {$formattedAmount} berhasil dikonfirmasi ({$channel}) untuk pesanan #{$order->order_no} ({$pkgName})",
            'request'      => request(),
        ]);
    }

    /**
     * Log an order cancellation by user.
     */
    public function logPaymentCanceled(User $user, Order $order, ?Request $request = null): ?UserLoginLog
    {
        $pkgName = $order->package?->name ?? 'Langganan';

        return $this->record([
            'user_id'      => $user->id,
            'email'        => $user->email,
            'name'         => $user->name,
            'role'         => $user->role ?? 'user',
            'event'        => 'payment_canceled',
            'status'       => 'canceled',
            'login_method' => 'session',
            'description'  => "Membatalkan pesanan #{$order->order_no} ({$pkgName})",
            'request'      => $request ?? request(),
        ]);
    }

    /**
     * Log a failed payment attempt.
     */
    public function logPaymentFailed(Order $order, string $reason): ?UserLoginLog
    {
        $user = $order->user;
        $pkgName = $order->package?->name ?? 'Langganan';

        return $this->record([
            'user_id'        => $order->user_id,
            'email'          => $user?->email ?? "user_{$order->user_id}@jalurgaib.com",
            'name'           => $user?->name ?? 'User Pembeli',
            'role'           => $user?->role ?? 'user',
            'event'          => 'payment_failed',
            'status'         => 'failed',
            'login_method'   => 'kasera_pay',
            'failure_reason' => $reason,
            'description'    => "Pembayaran pesanan #{$order->order_no} ({$pkgName}) gagal: {$reason}",
            'request'        => request(),
        ]);
    }

    /**
     * Internal persist helper with telemetry parsing.
     */
    protected function record(array $data): ?UserLoginLog
    {
        try {
            $this->ensureTableExists();

            $email = strtolower(trim($data['email'] ?? ''));
            $event = $data['event'] ?? 'login';

            // Prevent duplicate logs from concurrent triggers within 2 seconds
            if (!empty($email)) {
                $recentDuplicate = UserLoginLog::where('email', $email)
                    ->where('event', $event)
                    ->where('created_at', '>=', now()->subSeconds(2))
                    ->exists();

                if ($recentDuplicate) {
                    return null;
                }
            }

            /** @var Request $request */
            $request = $data['request'] ?? request();
            $userAgent = $request->userAgent() ?? '';
            $telemetry = $this->parseUserAgent($userAgent);

            return UserLoginLog::create([
                'user_id'        => $data['user_id'] ?? null,
                'email'          => $email,
                'name'           => $data['name'] ?? null,
                'role'           => $data['role'] ?? 'user',
                'event'          => $event,
                'status'         => $data['status'] ?? 'success',
                'login_method'   => $data['login_method'] ?? 'email_password',
                'ip_address'     => $request->ip() ?: '127.0.0.1',
                'user_agent'     => $userAgent,
                'device'         => $telemetry['device'],
                'platform'       => $telemetry['platform'],
                'browser'        => $telemetry['browser'],
                'failure_reason' => $data['failure_reason'] ?? null,
                'description'    => $data['description'] ?? null,
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[AuthenticationLogService Error] Failed to record activity log: ' . $e->getMessage(), [
                'email' => $data['email'] ?? null,
                'event' => $data['event'] ?? null,
            ]);
            return null;
        }
    }

    /**
     * Parse client User-Agent string into platform, browser, and device.
     */
    public function parseUserAgent(?string $ua): array
    {
        if (empty($ua)) {
            return [
                'platform' => 'Server / Webhook',
                'browser'  => 'API Client',
                'device'   => 'Server',
            ];
        }

        // 1. Device Type
        $isMobile = (bool) preg_match('/(android|iphone|ipod|mobile|blackberry|iemobile|opera mini)/i', $ua);
        $isTablet = (bool) preg_match('/(ipad|tablet|playbook|silk)/i', $ua);
        $device = $isTablet ? 'Tablet' : ($isMobile ? 'Mobile' : 'Desktop');

        // 2. Platform / OS
        $platform = 'Unknown';
        if (preg_match('/windows nt 10/i', $ua))        $platform = 'Windows 10/11';
        elseif (preg_match('/windows/i', $ua))         $platform = 'Windows';
        elseif (preg_match('/android/i', $ua))         $platform = 'Android';
        elseif (preg_match('/iphone/i', $ua))          $platform = 'iOS (iPhone)';
        elseif (preg_match('/ipad/i', $ua))            $platform = 'iPadOS (iPad)';
        elseif (preg_match('/macintosh|mac os x/i', $ua)) $platform = 'macOS';
        elseif (preg_match('/linux/i', $ua))           $platform = 'Linux';
        elseif (preg_match('/guzzle|curl|postman/i', $ua)) $platform = 'API / Webhook';

        // 3. Browser
        $browser = 'Unknown';
        if (preg_match('/edg/i', $ua))                 $browser = 'Microsoft Edge';
        elseif (preg_match('/samsungbrowser/i', $ua))  $browser = 'Samsung Internet';
        elseif (preg_match('/opera|opr/i', $ua))       $browser = 'Opera';
        elseif (preg_match('/chrome|crios/i', $ua))    $browser = 'Google Chrome';
        elseif (preg_match('/firefox|fxios/i', $ua))   $browser = 'Mozilla Firefox';
        elseif (preg_match('/safari/i', $ua))          $browser = 'Apple Safari';
        elseif (preg_match('/guzzle|curl/i', $ua))     $browser = 'HTTP Client';

        return [
            'platform' => $platform,
            'browser'  => $browser,
            'device'   => $device,
        ];
    }
}
