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

#[Fillable(['name', 'email', 'password', 'role', 'status', 'suspend_reason', 'package_id', 'package_expires_at'])]
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

    public function getPackage(): Package
    {
        if ($this->package) {
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

    public function getDailyMessageLimit(): int
    {
        if ($this->isAdmin()) {
            return 999999;
        }
        return $this->getPackage()->daily_message_limit ?? 50;
    }

    public function canAddDevice(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        $currentDevicesCount = $this->devices()->count();
        return $currentDevicesCount < $this->getMaxDevices();
    }

    public function getTodaySentMessagesCount(): int
    {
        return Message::whereHas('device', fn($q) => $q->where('user_id', $this->id))
            ->whereDate('created_at', today())
            ->count();
    }

    public function getRemainingDailyMessages(): int
    {
        if ($this->isAdmin()) {
            return 999999;
        }
        $limit = $this->getDailyMessageLimit();
        $used = $this->getTodaySentMessagesCount();
        return max(0, $limit - $used);
    }

    public function canSendMessages(int $count = 1): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return ($this->getTodaySentMessagesCount() + $count) <= $this->getDailyMessageLimit();
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }
}
