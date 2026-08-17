<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'key',
        'is_active',
        'last_used_at',
        'expires_at',
    ];

    protected $hidden = ['key'];

    protected $casts = [
        'is_active'    => 'boolean',
        'last_used_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generate(?int $userId = null, ?string $name = 'Default API Key'): self
    {
        return static::create([
            'user_id'   => $userId,
            'name'      => $name ?? 'Default API Key',
            'key'       => static::generateKey(),
            'is_active' => true,
        ]);
    }

    public static function generateKey(): string
    {
        return 'wag_' . Str::random(48);
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        return true;
    }
}
