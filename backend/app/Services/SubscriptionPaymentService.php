<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionPaymentService
{
    public function __construct(
        private readonly KaseraPayService $kasera
    ) {}

    /**
     * Initiate checkout for a package subscription.
     * Generates a fresh order and requests a checkout session from Kasera Pay.
     *
     * @param User $user
     * @param Package $package
     * @return array{order: ?Order, checkout_url: string, is_free: bool}
     * @throws \RuntimeException
     */
    public function initiateCheckout(User $user, Package $package): array
    {
        if (!$package->isActive()) {
            throw new \RuntimeException('Paket yang dipilih saat ini belum aktif atau tidak tersedia.');
        }

        // Free tier does not require payment, activate immediately
        if ($package->isFree()) {
            $user->update([
                'package_id'         => $package->id,
                'package_expires_at' => null, // Free tier is lifetime
            ]);

            return [
                'order'        => null,
                'checkout_url' => route('user.dashboard'),
                'is_free'      => true,
            ];
        }

        // Verify Kasera Pay configuration
        if (!$this->kasera->isConfigured()) {
            throw new \RuntimeException('Payment Gateway Kasera Pay belum dikonfigurasi dengan API Key yang valid di .env (KASERA_PAY_KEY).');
        }

        // Expire any existing pending orders for this user and package to ensure fresh session
        Order::where('user_id', $user->id)
            ->where('package_id', $package->id)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        // Create fresh order record in DB with custom 'JG-' prefix
        $orderNo = 'JG-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $order = Order::create([
            'order_no'        => $orderNo,
            'user_id'         => $user->id,
            'package_id'      => $package->id,
            'amount'          => $package->price,
            'status'          => 'pending',
            'idempotency_key' => (string) Str::uuid(),
            'metadata'        => [
                'package_name'        => $package->name,
                'max_devices'         => $package->max_devices,
                'daily_message_limit' => $package->daily_message_limit,
            ],
        ]);

        $expiresInMinutes = (int) config('services.kasera.expires_in_minutes', 60);

        // Prepare Kasera Pay transaction payload
        $payload = [
            'amount'             => (int) $order->amount,
            'description'        => "Langganan {$package->name} - {$user->name}",
            'external_id'        => (string) $order->order_no,
            'expires_in_minutes' => $expiresInMinutes,
            'customer'           => [
                'name'  => (string) $user->name,
                'email' => (string) $user->email,
            ],
            'return_url'         => route('user.payment.finish', ['order_no' => $order->order_no]),
            'checkout'           => [
                'steps' => ['customer', 'payment_method', 'payment'],
            ],
        ];

        $transaction = $this->kasera->createTransaction($payload, $order->idempotency_key);

        $checkoutUrl = $transaction['checkout_url'] ?? null;
        if (empty($checkoutUrl)) {
            throw new \RuntimeException('Gagal mendapatkan checkout_url dari Kasera Pay.');
        }

        $order->update([
            'kasera_transaction_id' => $transaction['id'] ?? null,
            'checkout_url'          => $checkoutUrl,
        ]);

        return [
            'order'        => $order,
            'checkout_url' => $checkoutUrl,
            'is_free'      => false,
        ];
    }
}
