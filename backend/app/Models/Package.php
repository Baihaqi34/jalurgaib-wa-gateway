<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'max_devices',
        'daily_message_limit',
        'benefits',
        'status',
        'badge',
        'sort_order',
    ];

    protected $casts = [
        'benefits'            => 'array',
        'price'               => 'integer',
        'max_devices'         => 'integer',
        'daily_message_limit' => 'integer',
        'sort_order'          => 'integer',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isComingSoon(): bool
    {
        return $this->status === 'coming_soon';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isFree(): bool
    {
        return $this->price === 0;
    }

    /**
     * Get default Free package or create a fallback instance.
     */
    public static function getDefaultPackage(): self
    {
        $package = self::where('slug', 'free')->first();
        if (!$package) {
            $package = self::where('price', 0)->where('status', 'active')->first();
        }
        if (!$package) {
            // Fallback object in memory if table is not seeded yet
            $package = new self([
                'name'                => 'Free Tier',
                'slug'                => 'free',
                'description'         => 'Paket gratis standar untuk memulai gateway',
                'price'               => 0,
                'max_devices'         => 2,
                'daily_message_limit' => 50,
                'benefits'            => [
                    '2 Slot Perangkat WhatsApp',
                    '50 Kuota Pesan per Hari',
                    'Akses REST API & Webhook',
                    'Multi-Device Delivery',
                ],
                'status'              => 'active',
                'badge'               => 'Gratis',
            ]);
        }
        return $package;
    }
}
