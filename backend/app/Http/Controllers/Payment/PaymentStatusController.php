<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\KaseraPayService;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentStatusController extends Controller
{
    public function __construct(
        private readonly KaseraPayService $kasera,
        private readonly OrderFulfillmentService $fulfillmentService
    ) {}

    /**
     * Handle customer redirect return from Kasera Checkout.
     * Note: Does NOT blindly trust return URL. Verifies server-to-server against Kasera API if still pending.
     */
    public function finish(Request $request): RedirectResponse
    {
        $orderNo = $request->query('order_no');
        $user = $request->user();

        if (!$orderNo) {
            return redirect()->route('user.upgrade');
        }

        /** @var Order|null $order */
        $order = Order::where('order_no', $orderNo)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return redirect()->route('user.upgrade')
                ->with('error', 'Pesanan tidak ditemukan.');
        }

        // If already marked as paid (e.g. by webhook arriving first), show success
        if ($order->isPaid()) {
            return redirect()->route('user.upgrade')
                ->with('success', "Pembayaran untuk {$order->package->name} berhasil diverifikasi! Paket Anda sudah aktif.");
        }

        // If still pending and has transaction id, query Kasera server-to-server as safe fallback
        if ($order->isPending() && $order->kasera_transaction_id) {
            try {
                $transaction = $this->kasera->retrieveTransaction($order->kasera_transaction_id);

                if (($transaction['status'] ?? '') === 'succeeded') {
                    $this->fulfillmentService->fulfill(
                        order: $order,
                        paidAmount: (int) ($transaction['amount'] ?? 0),
                        paymentMethod: $transaction['payment_method'] ?? null,
                        paidAt: $transaction['paid_at'] ?? now(),
                        extraMetadata: ['verified_via' => 'server_return_check']
                    );

                    return redirect()->route('user.upgrade')
                        ->with('success', "Pembayaran berhasil dikonfirmasi! Paket {$order->package->name} Anda telah aktif.");
                }

                if (in_array($transaction['status'] ?? '', ['failed', 'expired', 'canceled'])) {
                    $order->update(['status' => $transaction['status']]);

                    return redirect()->route('user.upgrade')
                        ->with('error', "Status pembayaran Anda: {$transaction['status']}. Silakan coba kembali.");
                }

            } catch (\Throwable $e) {
                Log::warning("Server check for order {$order->order_no} returned error: {$e->getMessage()}");
            }
        }

        return redirect()->route('user.upgrade')
            ->with('info', "Menunggu konfirmasi pembayaran untuk pesanan #{$order->order_no}. Sistem akan memperbarui paket Anda otomatis setelah pembayaran diterima.");
    }
}
