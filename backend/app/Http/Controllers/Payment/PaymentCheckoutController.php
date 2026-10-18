<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Package;
use App\Services\SubscriptionPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentCheckoutController extends Controller
{
    public function __construct(
        private readonly SubscriptionPaymentService $paymentService
    ) {}

    /**
     * Initiate checkout for a package subscription.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
        ]);

        $user = $request->user();
        $package = Package::findOrFail($validated['package_id']);

        try {
            $result = $this->paymentService->initiateCheckout($user, $package);

            if ($result['is_free']) {
                return redirect()->route('user.upgrade')
                    ->with('success', 'Paket gratis berhasil diaktifkan untuk akun Anda.');
            }

            return redirect()->away($result['checkout_url']);

        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            if ($e instanceof \Illuminate\Http\Client\RequestException && $e->response) {
                $errorData = $e->response->json();
                if (!empty($errorData['error']['message'])) {
                    $errorMessage = $errorData['error']['message'];
                }
            }

            Log::error('Payment Checkout Error: ' . $errorMessage, [
                'user_id'    => $user->id,
                'package_id' => $package->id,
                'exception'  => $e->getMessage(),
            ]);

            return back()->with('error', 'Terjadi kendala saat menghubungi gateway pembayaran: ' . $errorMessage);
        }
    }

    /**
     * Cancel an existing pending order so user can generate a fresh one.
     */
    public function cancel(Request $request, string $order_no): RedirectResponse
    {
        $user = $request->user();
        $order = Order::where('user_id', $user->id)
            ->where('order_no', $order_no)
            ->where('status', 'pending')
            ->firstOrFail();

        $order->update(['status' => 'canceled']);

        return redirect()->route('user.upgrade')
            ->with('success', "Pesanan {$order->order_no} telah dibatalkan. Anda dapat membuat pembayaran baru.");
    }
}
