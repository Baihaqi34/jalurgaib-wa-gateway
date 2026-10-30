<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionPaymentService
{
    public const DEFAULT_PERCENT_FEE = 0.007; // 0.7% MDR
    public const DEFAULT_FIXED_FEE = 250;     // Rp250 flat fee

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

        // Hitung nominal tagihan agar merchant terima utuh/bersih:
        // Skema statis untuk seluruh transaksi: Gross-up 0,7% + Rp250 per pembayaran berhasil.
        $pricing = $this->calculateCleanAmount((int) $package->price);

        $order = Order::create([
            'order_no'        => $orderNo,
            'user_id'         => $user->id,
            'package_id'      => $package->id,
            'amount'          => $pricing['billed_amount'],
            'status'          => 'pending',
            'idempotency_key' => (string) Str::uuid(),
            'metadata'        => [
                'package_name'        => $package->name,
                'max_devices'         => $package->max_devices,
                'daily_message_limit' => $package->daily_message_limit,
                'base_price'          => $pricing['base_price'],
                'kasera_fee'          => $pricing['fee_kasera'],
                'unique_code'         => $pricing['unique_code'],
                'billed_amount'       => $pricing['billed_amount'],
                'pricing_note'        => $pricing['note'],
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
            'order_items'        => [
                [
                    'name'     => "Paket {$package->name}",
                    'price'    => (int) $pricing['base_price'],
                    'quantity' => 1,
                ],
                [
                    'name'     => "Biaya Layanan",
                    'price'    => (int) ($pricing['billed_amount'] - $pricing['base_price']),
                    'quantity' => 1,
                ],
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

    /**
     * Hitung nominal tagihan ke pembeli secara statis agar saldo yang masuk ke Kasera diterima bersih 100% oleh merchant.
     * Sesuai ketentuan Kasera Pay:
     * - Fee: 0,7% + Rp250 per pembayaran berhasil (statis untuk semua pembayaran).
     *
     * Formula Gross-Up Statis:
     * Gross = ceil((Net + Rp250) / (1 - 0.007))
     *
     * Seluruh transaksi menggunakan skema perhitungan statis ini secara konsisten tanpa kode unik acak (random jitter).
     *
     * @param int $netPrice Harga pokok paket
     * @return array{
     *     billed_amount: int,
     *     base_price: int,
     *     fee_kasera: int,
     *     unique_code: int,
     *     note: string
     * }
     */
    public function calculateCleanAmount(int $netPrice): array
    {
        if ($netPrice <= 0) {
            return [
                'billed_amount' => 0,
                'base_price'    => 0,
                'fee_kasera'    => 0,
                'unique_code'   => 0,
                'note'          => 'Paket gratis tanpa biaya transaksi',
            ];
        }

        $percentRate = (float) config('services.kasera.fee_percent', self::DEFAULT_PERCENT_FEE);
        $flatFee = (int) config('services.kasera.fee_fixed', self::DEFAULT_FIXED_FEE);
        $divisor = 1.0 - $percentRate;

        if ($divisor <= 0) {
            throw new \InvalidArgumentException('Fee percent tidak boleh 100% atau lebih.');
        }

        // Perhitungan Gross-Up statis: Gross = ceil((Net + flatFee) / (1 - percentRate))
        $billedAmount = (int) ceil(($netPrice + $flatFee) / $divisor);

        // Estimasi fee yang dipotong oleh Kasera dari billedAmount
        $estimatedKaseraFee = (int) (ceil($billedAmount * $percentRate) + $flatFee);

        return [
            'billed_amount' => $billedAmount,
            'base_price'    => $netPrice,
            'fee_kasera'    => $estimatedKaseraFee,
            'unique_code'   => 0,
            'note'          => "Perhitungan statis fee Kasera: (Net + Rp{$flatFee}) / " . (1 - $percentRate) . " (0,7% + Rp{$flatFee})",
        ];
    }
}
