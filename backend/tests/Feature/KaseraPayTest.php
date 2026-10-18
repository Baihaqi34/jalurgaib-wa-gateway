<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use App\Services\KaseraPayService;
use App\Support\KaseraPaySignature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KaseraPayTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK_SECRET = 'whsec_test_secret_1234567890abcdef';

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('services.kasera.key', 'kp_test_sample_key_123');
        config()->set('services.kasera.base_url', 'https://pay.kasera.id');
        config()->set('services.kasera.webhook_secret', self::WEBHOOK_SECRET);
    }

    private function signPayload(string $body, string $secret = self::WEBHOOK_SECRET, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        $signature = hash_hmac('sha256', $timestamp . '.' . $body, $secret);

        return "t={$timestamp},v1={$signature}";
    }

    private function postWebhook(string $body, ?string $signatureHeader = null)
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
        ];

        if ($signatureHeader !== null) {
            $headers['HTTP_KASERA-SIGNATURE-V1'] = $signatureHeader;
        }

        return $this->call(
            method: 'POST',
            uri: '/kasera-pay/webhook',
            parameters: [],
            cookies: [],
            files: [],
            server: $headers,
            content: $body
        );
    }

    public function test_signature_verifier_accepts_valid_signature(): void
    {
        $body = '{"id":"evt_1","type":"payment.paid"}';
        $header = $this->signPayload($body);

        $this->assertTrue(KaseraPaySignature::verify($body, $header, self::WEBHOOK_SECRET));
    }

    public function test_signature_verifier_rejects_wrong_secret(): void
    {
        $body = '{"id":"evt_1","type":"payment.paid"}';
        $header = $this->signPayload($body, 'whsec_incorrect_secret');

        $this->assertFalse(KaseraPaySignature::verify($body, $header, self::WEBHOOK_SECRET));
    }

    public function test_signature_verifier_rejects_expired_timestamp_anti_replay(): void
    {
        $body = '{"id":"evt_1","type":"payment.paid"}';
        // Timestamp is 6 minutes ago (> 300 seconds tolerance)
        $expiredTime = time() - 360;
        $header = $this->signPayload($body, self::WEBHOOK_SECRET, $expiredTime);

        $this->assertFalse(KaseraPaySignature::verify($body, $header, self::WEBHOOK_SECRET));
    }

    public function test_signature_verifier_accepts_rotated_secret_with_multiple_v1(): void
    {
        $body = '{"id":"evt_1","type":"payment.paid"}';
        $t = time();
        $oldSig = hash_hmac('sha256', $t . '.' . $body, 'whsec_old_secret');
        $validSig = hash_hmac('sha256', $t . '.' . $body, self::WEBHOOK_SECRET);
        $header = "t={$t},v1={$oldSig},v1={$validSig}";

        $this->assertTrue(KaseraPaySignature::verify($body, $header, self::WEBHOOK_SECRET));
    }

    public function test_kasera_service_sends_authorization_and_idempotency_key(): void
    {
        Http::fake([
            'pay.kasera.id/v1/transactions' => Http::response([
                'id'           => 'payreq_xyz123',
                'status'       => 'pending',
                'checkout_url' => 'https://pay.kasera.id/p/tok_demo123',
            ], 201),
        ]);

        /** @var KaseraPayService $service */
        $service = app(KaseraPayService::class);
        $result = $service->createTransaction([
            'amount'      => 75000,
            'description' => 'Test Starter Pro Upgrade',
        ], 'idemp_key_unique_test_1');

        $this->assertSame('payreq_xyz123', $result['id']);
        $this->assertSame('https://pay.kasera.id/p/tok_demo123', $result['checkout_url']);

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer kp_test_sample_key_123')
                && $request->hasHeader('Idempotency-Key', 'idemp_key_unique_test_1')
                && $request['amount'] === 75000;
        });
    }

    public function test_webhook_returns_400_for_invalid_signature(): void
    {
        $body = json_encode(['id' => 'evt_fake', 'type' => 'payment.paid']);
        $invalidHeader = 't=' . time() . ',v1=fake_signature_hash';

        $response = $this->postWebhook($body, $invalidHeader);
        $response->assertStatus(400);
    }

    public function test_webhook_fulfills_order_and_activates_user_package(): void
    {
        $package = Package::create([
            'name'                => 'Starter Pro Test',
            'slug'                => 'starter-pro-test',
            'price'               => 75000,
            'max_devices'         => 5,
            'daily_message_limit' => 1000,
            'status'              => 'active',
        ]);

        $user = User::create([
            'name'     => 'Merchant John',
            'email'    => 'merchant@example.com',
            'password' => 'password123',
            'role'     => 'user',
        ]);

        $order = Order::create([
            'order_no'              => 'ORD-TEST-001',
            'user_id'               => $user->id,
            'package_id'            => $package->id,
            'amount'                => 75000,
            'status'                => 'pending',
            'kasera_transaction_id' => 'payreq_test_888',
        ]);

        $payload = [
            'id'       => 'evt_payreq_888_success',
            'type'     => 'payment.paid',
            'livemode' => false,
            'data'     => [
                'payment_request_id' => 'payreq_test_888',
                'external_id'        => 'ORD-TEST-001',
                'amount'             => 75000,
                'payment_method'     => 'qris',
                'paid_at'            => now()->toIso8601String(),
            ],
        ];

        $rawBody = json_encode($payload);
        $signature = $this->signPayload($rawBody);

        $response = $this->postWebhook($rawBody, $signature);
        $response->assertOk();

        // Verify order is now paid
        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame('qris', $order->payment_method);
        $this->assertNotNull($order->paid_at);

        // Verify user package is activated and expires 30 days ahead
        $user->refresh();
        $this->assertSame($package->id, $user->package_id);
        $this->assertNotNull($user->package_expires_at);
        $this->assertTrue($user->package_expires_at->isFuture());
    }

    public function test_webhook_deduplication_fulfills_only_once(): void
    {
        $package = Package::create([
            'name'                => 'Starter Pro Dedupe',
            'slug'                => 'starter-pro-dedupe',
            'price'               => 75000,
            'max_devices'         => 5,
            'daily_message_limit' => 1000,
            'status'              => 'active',
        ]);

        $user = User::create([
            'name'     => 'Dedupe User',
            'email'    => 'dedupe@example.com',
            'password' => 'password123',
            'role'     => 'user',
        ]);

        $order = Order::create([
            'order_no'              => 'ORD-DEDUPE-001',
            'user_id'               => $user->id,
            'package_id'            => $package->id,
            'amount'                => 75000,
            'status'                => 'pending',
            'kasera_transaction_id' => 'payreq_dedupe_999',
        ]);

        $payload = [
            'id'       => 'evt_dedupe_once_only',
            'type'     => 'payment.paid',
            'livemode' => false,
            'data'     => [
                'payment_request_id' => 'payreq_dedupe_999',
                'external_id'        => 'ORD-DEDUPE-001',
                'amount'             => 75000,
                'payment_method'     => 'mandiri_va',
                'paid_at'            => now()->toIso8601String(),
            ],
        ];

        $rawBody = json_encode($payload);
        $signature = $this->signPayload($rawBody);

        // First delivery: processes payment
        $res1 = $this->postWebhook($rawBody, $signature);
        $res1->assertOk();

        // Second delivery (identical event_id): should be deduplicated and safely answered with 200
        $res2 = $this->postWebhook($rawBody, $signature);
        $res2->assertOk();

        // Check DB event record only logged once
        $this->assertSame(1, DB::table('kasera_pay_events')->where('event_id', 'evt_dedupe_once_only')->count());
        $this->assertSame('paid', $order->fresh()->status);
    }

    public function test_webhook_rejects_underpayment_tampering(): void
    {
        $package = Package::create([
            'name'                => 'Enterprise Guard',
            'slug'                => 'enterprise-guard',
            'price'               => 250000,
            'max_devices'         => 15,
            'daily_message_limit' => 10000,
            'status'              => 'active',
        ]);

        $user = User::create([
            'name'     => 'Underpaid User',
            'email'    => 'underpaid@example.com',
            'password' => 'password123',
            'role'     => 'user',
        ]);

        $order = Order::create([
            'order_no'              => 'ORD-GUARD-001',
            'user_id'               => $user->id,
            'package_id'            => $package->id,
            'amount'                => 250000, // Expected 250,000 IDR
            'status'                => 'pending',
            'kasera_transaction_id' => 'payreq_guard_111',
        ]);

        // Attempt webhook where paid amount is only 1,000 IDR instead of 250,000
        $payload = [
            'id'       => 'evt_tampered_amount',
            'type'     => 'payment.paid',
            'livemode' => false,
            'data'     => [
                'payment_request_id' => 'payreq_guard_111',
                'external_id'        => 'ORD-GUARD-001',
                'amount'             => 1000, // TAMPERED UNDERPAYMENT
                'payment_method'     => 'qris',
                'paid_at'            => now()->toIso8601String(),
            ],
        ];

        $rawBody = json_encode($payload);
        $signature = $this->signPayload($rawBody);

        $response = $this->postWebhook($rawBody, $signature);
        $response->assertOk();

        // Verify order was NOT marked paid
        $order->refresh();
        $this->assertNotSame('paid', $order->status);
        $this->assertSame('failed', $order->status);

        // Verify user was NOT upgraded
        $user->refresh();
        $this->assertNull($user->package_id);
    }
}
