<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'user_id',
        'package_id',
        'amount',
        'status',
        'idempotency_key',
        'kasera_transaction_id',
        'checkout_url',
        'payment_method',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount'   => 'integer',
        'paid_at'  => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
