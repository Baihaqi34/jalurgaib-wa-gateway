<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderFulfillmentService;
use App\Support\KaseraPaySignature;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KaseraPayWebhookController extends Controller
{
    public function __construct(
        private readonly OrderFulfillmentService $fulfillmentService
    ) {}

    /**
     * Handle incoming Kasera Pay webhook delivery.
     */
    public function __invoke(Request $request): Response
    {
        // 1. MUST use raw body content, never re-encoded or deserialized json
        $rawBody = $request->getContent();
        $signatureHeader = $request->header('Kasera-Signature-V1');
        $webhookSecret = (string) config('services.kasera.webhook_secret');

        // 2. Timing-safe HMAC verification and anti-replay tolerance check
        $isValid = KaseraPaySignature::verify(
            rawBody: $rawBody,
            header: $signatureHeader,
            secret: $webhookSecret,
        );

        if (!$isValid) {
            Log::warning('Kasera Pay Webhook Signature Verification Failed', [
                'ip'        => $request->ip(),
                'signature' => $signatureHeader,
            ]);
            // Return 400 immediately to reject unauthorized calls without info leak
            return response('Invalid signature', 400);
        }

        $payload = json_decode($rawBody, true);
        if (!is_array($payload)) {
            return response('Malformed JSON body', 400);
        }

        $eventId = $payload['id'] ?? $request->header('Kasera-Event-Id');
        if (!is_string($eventId) || trim($eventId) === '') {
            return response('Missing event id', 400);
        }

        $eventType = $payload['type'] ?? '';

        // Handle ping / test event if sent from dashboard
        if ($eventType === 'test.ping') {
            Log::info("Kasera Pay Webhook Ping Test Succeeded (Event {$eventId})");
            return response('pong', 200);
        }

        // 3. Atomically deduplicate delivery using unique index in database
        // and fulfill order within the same transaction.
        try {
            DB::transaction(function () use ($eventId, $eventType, $payload): void {
                // insertOrIgnore returns 1 on first insert, 0 if duplicate event_id already exists
                $isFirstDelivery = DB::table('kasera_pay_events')->insertOrIgnore([
                    'event_id'   => $eventId,
                    'event_type' => $eventType,
                    'created_at' => now(),
                ]) === 1;

                if (!$isFirstDelivery) {
                    Log::info("Duplicate Kasera Pay webhook event received ({$eventId}). Skipping duplicate execution.");
                    return;
                }

                // Only process when payment is confirmed paid
                if ($eventType === 'payment.paid') {
                    $eventData = $payload['data'] ?? [];
                    $transactionId = $eventData['payment_request_id'] ?? null;
                    $externalId = $eventData['external_id'] ?? null;
                    $paidAmount = (int) ($eventData['amount'] ?? 0);
                    $paymentMethod = $eventData['payment_method'] ?? null;
                    $paidAt = $eventData['paid_at'] ?? now();

                    // Find order by Kasera transaction id or external order number
                    $order = Order::query()
                        ->when($transactionId, fn($q) => $q->where('kasera_transaction_id', $transactionId))
                        ->when(!$transactionId && $externalId, fn($q) => $q->where('order_no', $externalId))
                        ->first();

                    if (!$order) {
                        Log::error("Kasera Pay Webhook: Order not found for transaction {$transactionId} / external_id {$externalId}");
                        return;
                    }

                    $this->fulfillmentService->fulfill(
                        order: $order,
                        paidAmount: $paidAmount,
                        paymentMethod: $paymentMethod,
                        paidAt: $paidAt,
                        extraMetadata: [
                            'webhook_event_id' => $eventId,
                            'webhook_data'     => $eventData,
                        ]
                    );
                }
            });
        } catch (\Throwable $e) {
            Log::error('Kasera Pay Webhook Processing Error: ' . $e->getMessage(), [
                'event_id'  => $eventId,
                'exception' => $e,
            ]);

            // Returning 500 will cause Kasera to retry delivery with exponential backoff
            return response('Error processing webhook', 500);
        }

        // Return 200 OK so Kasera knows delivery succeeded
        return response('OK', 200);
    }
}
