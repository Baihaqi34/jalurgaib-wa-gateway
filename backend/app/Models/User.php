<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'email_verified_at', 'suspend_reason', 'package_id', 'package_expires_at', 'firebase_uid', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'package_expires_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return strtolower($this->role ?? '') === 'admin' || str_contains(strtolower($this->email), 'admin@');
    }

    public function isUser(): bool
    {
        return !$this->isAdmin();
    }

    public function isSuspended(): bool
    {
        return in_array(strtolower($this->status ?? 'active'), ['suspended', 'banned']);
    }

    public function isActive(): bool
    {
        return !$this->isSuspended();
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isPackageExpired(): bool
    {
        return $this->package_expires_at !== null && $this->package_expires_at->isPast();
    }

    public function getPackage(): Package
    {
        if ($this->package) {
            if ($this->package_expires_at && $this->package_expires_at->isPast()) {
                return Package::getDefaultPackage();
            }
            return $this->package;
        }

        return Package::getDefaultPackage();
    }

    public function getMaxDevices(): int
    {
        if ($this->isAdmin()) {
            return 9999;
        }
        return $this->getPackage()->max_devices ?? 2;
    }

    public function getMessageLimit(): int
    {
        if ($this->isAdmin()) {
            return 999999;
        }
        return $this->getPackage()->daily_message_limit ?? 50;
    }

    public function getDailyMessageLimit(): int
    {
        return $this->getMessageLimit();
    }

    public function getLimitCycle(): string
    {
        return $this->getPackage()->reset_cycle ?? 'daily';
    }

    public function isMonthlyLimit(): bool
    {
        return $this->getLimitCycle() === 'monthly';
    }

    public function canAddDevice(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        $currentDevicesCount = $this->devices()->count();
        return $currentDevicesCount < $this->getMaxDevices();
    }

    public function getCurrentCycleSentMessagesCount(): int
    {
        $query = Message::whereHas('device', fn($q) => $q->where('user_id', $this->id));

        if ($this->isMonthlyLimit()) {
            return $query->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        return $query->whereDate('created_at', today())->count();
    }

    public function getTodaySentMessagesCount(): int
    {
        return $this->getCurrentCycleSentMessagesCount();
    }

    public function getRemainingMessages(): int
    {
        if ($this->isAdmin()) {
            return 999999;
        }
        $limit = $this->getMessageLimit();
        $used = $this->getCurrentCycleSentMessagesCount();
        return max(0, $limit - $used);
    }

    public function getRemainingDailyMessages(): int
    {
        return $this->getRemainingMessages();
    }

    public function canSendMessages(int $count = 1): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return ($this->getCurrentCycleSentMessagesCount() + $count) <= $this->getMessageLimit();
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function getOrCreateApiKey(): string
    {
        $activeKey = $this->apiKeys()->where('is_active', true)->first();
        if ($activeKey) {
            return $activeKey->key;
        }

        $newKey = ApiKey::generate($this->id, 'Default Web Key');
        return $newKey->key;
    }
}
