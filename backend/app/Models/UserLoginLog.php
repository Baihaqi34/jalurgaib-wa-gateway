<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserLoginLog extends Model
{
    use HasFactory;

    protected $table = 'user_login_logs';

    protected $fillable = [
        'user_id',
        'email',
        'name',
        'role',
        'event',
        'status',
        'login_method',
        'ip_address',
        'user_agent',
        'device',
        'platform',
        'browser',
        'failure_reason',
        'description',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for searching logs by user email, name, description, or IP address.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (empty($term)) {
            return $query;
        }

        $clean = trim($term);
        return $query->where(function (Builder $q) use ($clean) {
            $q->where('email', 'like', "%{$clean}%")
              ->orWhere('name', 'like', "%{$clean}%")
              ->orWhere('description', 'like', "%{$clean}%")
              ->orWhere('ip_address', 'like', "%{$clean}%");
        });
    }

    /**
     * Scope for filtering by event type.
     */
    public function scopeFilterEvent(Builder $query, ?string $event): Builder
    {
        if (empty($event) || $event === 'all') {
            return $query;
        }

        if ($event === 'auth') {
            return $query->whereIn('event', ['login', 'logout', 'login_failed', 'register']);
        }

        if ($event === 'payment') {
            return $query->whereIn('event', ['payment_success', 'checkout', 'payment_canceled', 'payment_failed']);
        }

        return $query->where('event', $event);
    }

    /**
     * Scope for filtering by status (success, failed, pending, canceled).
     */
    public function scopeFilterStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status) || $status === 'all') {
            return $query;
        }

        return $query->where('status', $status);
    }

    /**
     * Scope to order recent logs first.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
    }

    /**
     * Human-friendly event name label.
     */
    public function getEventLabel(): string
    {
        return match ($this->event) {
            'login'            => 'Login Berhasil',
            'logout'           => 'Keluar (Logout)',
            'login_failed'     => 'Login Gagal',
            'register'         => 'Registrasi Akun',
            'payment_success'  => 'Pembayaran Lunas',
            'checkout'         => 'Checkout Tagihan',
            'payment_canceled' => 'Tagihan Dibatalkan',
            'payment_failed'   => 'Pembayaran Gagal',
            default            => ucfirst(str_replace('_', ' ', $this->event)),
        };
    }

    /**
     * Check if this log is related to payment transactions.
     */
    public function isPaymentEvent(): bool
    {
        return in_array($this->event, ['payment_success', 'checkout', 'payment_canceled', 'payment_failed']);
    }

    /**
     * Font Awesome icon for client platform/OS.
     */
    public function getPlatformIcon(): string
    {
        $platform = strtolower($this->platform ?? '');
        if (str_contains($platform, 'win')) return 'fa-brands fa-windows text-sky-400';
        if (str_contains($platform, 'mac') || str_contains($platform, 'ios') || str_contains($platform, 'iphone') || str_contains($platform, 'ipad')) return 'fa-brands fa-apple text-mono-200';
        if (str_contains($platform, 'android')) return 'fa-brands fa-android text-emerald-400';
        if (str_contains($platform, 'linux')) return 'fa-brands fa-linux text-amber-400';
        return 'fa-solid fa-laptop text-mono-400';
    }

    /**
     * Font Awesome icon for browser.
     */
    public function getBrowserIcon(): string
    {
        $browser = strtolower($this->browser ?? '');
        if (str_contains($browser, 'chrome')) return 'fa-brands fa-chrome text-amber-400';
        if (str_contains($browser, 'firefox')) return 'fa-brands fa-firefox-browser text-orange-400';
        if (str_contains($browser, 'safari')) return 'fa-brands fa-safari text-sky-400';
        if (str_contains($browser, 'edge')) return 'fa-brands fa-edge text-blue-400';
        if (str_contains($browser, 'opera')) return 'fa-brands fa-opera text-red-400';
        return 'fa-solid fa-globe text-mono-400';
    }

    /**
     * Font Awesome icon for device type.
     */
    public function getDeviceIcon(): string
    {
        $device = strtolower($this->device ?? '');
        if ($device === 'mobile') return 'fa-solid fa-mobile-screen text-mono-300';
        if ($device === 'tablet') return 'fa-solid fa-tablet-screen-button text-mono-300';
        return 'fa-solid fa-desktop text-mono-300';
    }
}
