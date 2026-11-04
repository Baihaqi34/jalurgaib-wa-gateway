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
        'reset_cycle',
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
        'reset_cycle'         => 'string',
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

    public function isMonthly(): bool
    {
        return ($this->reset_cycle ?? 'daily') === 'monthly';
    }

    public function isDaily(): bool
    {
        return !$this->isMonthly();
    }

    public function getCycleLabelAttribute(): string
    {
        return $this->isMonthly() ? 'Bulan' : 'Hari';
    }

    public function getFormattedLimitAttribute(): string
    {
        $limit = number_format($this->daily_message_limit, 0, ',', '.');
        return "{$limit} msg/" . ($this->isMonthly() ? 'bulan' : 'hari');
    }

    /**
     * Generate default dynamic benefits list tailored to package capacity & cycle.
     */
    public function generateDefaultBenefits(): array
    {
        $cycleLabel = $this->isMonthly() ? 'Bulan' : 'Hari';
        $resetTime = $this->isMonthly() ? 'Awal Bulan (Siklus Kalender)' : '24 Jam (Pukul 00:00 WIB)';

        return [
            "{$this->max_devices} Device WhatsApp terhubung",
            number_format($this->daily_message_limit, 0, ',', '.') . " Pesan / {$cycleLabel}",
            "Reset Kuota Setiap {$resetTime}",
            'REST API Key Terisolasi & Webhook',
            'Anti-Ban Human Delay & Antrean Cerdas',
        ];
    }

    /**
     * Get effective benefits list.
     * If custom benefits is empty, returns dynamic auto-generated list.
     * Also normalizes any mismatch in cycle wording (e.g. 'Pesan / Hari' vs 'Pesan / Bulan').
     */
    public function getEffectiveBenefitsAttribute(): array
    {
        if (!empty($this->benefits) && is_array($this->benefits) && count($this->benefits) > 0) {
            $cycleLabel = $this->isMonthly() ? 'Bulan' : 'Hari';
            $wrongLabel = $this->isMonthly() ? 'Hari' : 'Bulan';
            
            return array_values(array_map(function ($b) use ($cycleLabel, $wrongLabel) {
                // Fix any wording mismatch in user-entered benefits
                $fixed = preg_replace('/(\/\s*)(' . $wrongLabel . ')/i', '$1' . $cycleLabel, $b);
                $fixed = preg_replace('/(per\s*)(' . $wrongLabel . ')/i', '$1' . $cycleLabel, $fixed);
                return $fixed;
            }, $this->benefits));
        }

        return $this->generateDefaultBenefits();
    }

    /**
     * Get default Free package or create a fallback instance.
     * Guaranteed to ALWAYS return a package with price == 0.
     */
    public static function getDefaultPackage(): self
    {
        $package = self::where('slug', 'free')->where('price', 0)->first();
        if (!$package) {
            $package = self::where('price', 0)->where('status', 'active')->orderBy('sort_order', 'asc')->first();
        }
        if (!$package) {
            try {
                $package = self::firstOrCreate(
                    ['slug' => 'free'],
                    [
                        'name'                => 'Free Tier',
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
                        'sort_order'          => 1,
                    ]
                );
            } catch (\Throwable $e) {
                // Fallback object in memory if table is not migrated or write fails
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
        }
        return $package;
    }
}
