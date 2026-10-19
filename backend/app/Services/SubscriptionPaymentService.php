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

        // Hitung nominal tagihan agar merchant terima utuh/bersih:
        // - Transaksi <= 100k: Biaya Kasera Rp500 disamarkan dengan kode unik satuan/puluhan (11-89).
        // - Transaksi > 100k: Gross-up 0.7% + Rp250 agar nominal masuk ke dompet Kasera tetap 100% utuh.
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
                    'name'     => "Biaya Layanan & Kode Verifikasi",
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
     * Hitung nominal tagihan ke pembeli agar saldo yang masuk ke Kasera diterima bersih 100% oleh merchant.
     * Sesuai dokumentasi resmi Kasera Pay (https://pay.kasera.id/docs/fees):
     *
     * 1. Kasera Pay TIDAK menyediakan opsi otomatis "bebankan fee ke pembeli" di sisi API-nya.
     *    Semua pemotongan MDR/fee dilakukan langsung dari nominal transaksi yang dibayarkan pembeli.
     *    Oleh sebab itu, merchant/backend kitalah yang harus mengatur perhitungannya sebelum dikirim ke Kasera.
     *
     * 2. Transaksi <= Rp100.000:
     *    Biaya resmi Kasera (QRIS) adalah Rp500 flat.
     *    Agar penambahan Rp500 tidak terlihat kaku/mencolok sebagai "biaya admin",
     *    disamarkan dengan kode unik rupiah satuan/puluhan (misal Rp 11 s/d 89).
     *    Contoh: Rp50.000 + Rp500 + Rp43 = Rp50.543.
     *
     * 3. Transaksi > Rp100.000:
     *    Biaya resmi Kasera (QRIS) adalah 0.7% + Rp250.
     *    Formula Gross-Up: Gross = ceil((Net + Rp250) / (1 - 0.007)).
     *    Hasil desimal persentase secara alami sudah menghasilkan nominal acak tidak mencolok (contoh: Rp151.310).
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
        if ($netPrice <= 100000) {
            // Skenario Transaksi <= Rp100.000 (Fee Kasera Rp500 flat)
            // Tambahkan kode unik puluhan/satuan (antara 11 - 89) agar angka tidak kaku/mencolok
            $flatFee = 500;
            $uniqueCode = random_int(11, 89);
            $billedAmount = $netPrice + $flatFee + $uniqueCode;

            return [
                'billed_amount' => $billedAmount,
                'base_price'    => $netPrice,
                'fee_kasera'    => $flatFee,
                'unique_code'   => $uniqueCode,
                'note'          => "Nominal Rp" . number_format($netPrice, 0, ',', '.') . " + fee Rp500 + kode unik {$uniqueCode} (agar tidak mencolok)",
            ];
        }

        // Skenario Transaksi > Rp100.000 (Fee Kasera QRIS 0.7% + Rp250)
        // Rumus Gross-Up agar merchant terima utuh: Gross = ceil((Net + 250) / (1 - 0.007))
        $percentRate = 0.007; // 0.7%
        $flatFee = 250;
        $divisor = 1.0 - $percentRate; // 0.993
        $grossCandidate = (int) ceil(($netPrice + $flatFee) / $divisor);

        // Jika angka akhirnya terlalu bulat (kelipatan 100), tambahkan kode unik kecil puluhan (11-49)
        $uniqueCode = 0;
        if ($grossCandidate % 100 === 0) {
            $uniqueCode = random_int(11, 49);
            $grossCandidate += $uniqueCode;
        }

        // Estimasi fee yang dipotong oleh Kasera dari grossCandidate
        $estimatedKaseraFee = (int) (ceil($grossCandidate * $percentRate) + $flatFee);

        return [
            'billed_amount' => $grossCandidate,
            'base_price'    => $netPrice,
            'fee_kasera'    => $estimatedKaseraFee,
            'unique_code'   => $uniqueCode,
            'note'          => "Formula Gross-Up Kasera: (Net + Rp250) / 0.993 agar merchant terima 100% bersih",
        ];
    }
}
