<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderFulfillmentService
{
    /**
     * Fulfill an order safely within a database transaction.
     * Prevents race conditions, double fulfillment, and price tampering.
     *
     * @param Order $order
     * @param int $paidAmount Gross amount received in whole IDR
     * @param string|null $paymentMethod Payment method used (e.g., qris, mandiri_va)
     * @param \DateTimeInterface|string|null $paidAt Timestamp of successful payment
     * @param array $extraMetadata Additional audit data
     * @return bool True if fulfilled or already fulfilled, false on mismatch/failure
     */
    public function fulfill(
        Order $order,
        int $paidAmount,
        ?string $paymentMethod = null,
        mixed $paidAt = null,
        array $extraMetadata = []
    ): bool {
        return DB::transaction(function () use ($order, $paidAmount, $paymentMethod, $paidAt, $extraMetadata) {
            // Lock the order row for update to prevent concurrent double fulfillment
            /** @var Order|null $lockedOrder */
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->first();

            if (!$lockedOrder) {
                Log::warning("Order not found during fulfillment: ID {$order->id}");
                return false;
            }

            // If already fulfilled, do nothing (idempotent)
            if ($lockedOrder->isPaid()) {
                Log::info("Order #{$lockedOrder->order_no} is already paid. Skipping double fulfillment.");
                return true;
            }

            // CRITICAL SECURITY CHECK: Verify payment amount matches or exceeds order amount
            if ($paidAmount < $lockedOrder->amount) {
                Log::error("Payment amount mismatch for order #{$lockedOrder->order_no}. Expected: {$lockedOrder->amount}, Received: {$paidAmount}");
                $lockedOrder->update([
                    'status'   => 'failed',
                    'metadata' => array_merge($lockedOrder->metadata ?? [], [
                        'failure_reason'       => 'Payment amount is less than order amount.',
                        'paid_amount_received' => $paidAmount,
                    ]),
                ]);
                return false;
            }

            $user = User::where('id', $lockedOrder->user_id)->lockForUpdate()->first();
            $package = $lockedOrder->package;

            if (!$user || !$package) {
                Log::error("User or Package missing for order #{$lockedOrder->order_no}");
                return false;
            }

            // Calculate new expiry date (30 days duration)
            // If user currently has the same package active with future expiry, extend from current expiry
            $currentExpiresAt = $user->package_expires_at;
            $now = Carbon::now();

            if ($user->package_id === $package->id && $currentExpiresAt && $currentExpiresAt->isFuture()) {
                $newExpiresAt = $currentExpiresAt->copy()->addDays(30);
            } else {
                $newExpiresAt = $now->copy()->addDays(30);
            }

            // 1. Update User Package & Expiry
            $user->update([
                'package_id'         => $package->id,
                'package_expires_at' => $newExpiresAt,
            ]);

            // 2. Mark Order as Paid
            $effectivePaidAt = $paidAt ? Carbon::parse($paidAt) : $now;
            $lockedOrder->update([
                'status'         => 'paid',
                'paid_at'        => $effectivePaidAt,
                'payment_method' => $paymentMethod ?? $lockedOrder->payment_method,
                'metadata'       => array_merge($lockedOrder->metadata ?? [], $extraMetadata, [
                    'activated_package_name' => $package->name,
                    'new_expires_at'         => $newExpiresAt->toIso8601String(),
                ]),
            ]);

            Log::info("Order #{$lockedOrder->order_no} successfully fulfilled. User {$user->email} upgraded to {$package->name} until {$newExpiresAt->toDateTimeString()}.");

            return true;
        });
    }
}
