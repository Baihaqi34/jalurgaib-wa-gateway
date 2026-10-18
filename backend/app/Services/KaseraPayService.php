<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KaseraPayService
{
    private readonly string $normalizedBaseUrl;

    public function __construct(
        private readonly string $key,
        string $baseUrl = 'https://pay.kasera.id/v1'
    ) {
        $trimmed = rtrim(trim($baseUrl), '/');
        // Ensure the base URL always ends with /v1 without duplicating
        if (!str_ends_with($trimmed, '/v1')) {
            $trimmed .= '/v1';
        }
        $this->normalizedBaseUrl = $trimmed;
    }

    /**
     * Create a payment request with a stable Idempotency-Key.
     * Replaying the same key with identical payload returns the existing transaction (HTTP 200).
     */
    public function createTransaction(array $payload, string $idempotencyKey): array
    {
        $response = $this->request()
            ->withHeaders([
                'Idempotency-Key' => substr($idempotencyKey, 0, 255),
            ])
            ->post('transactions', $payload);

        return $this->decode($response);
    }

    /**
     * Retrieve status and details of an existing transaction from Kasera Pay.
     */
    public function retrieveTransaction(string $transactionId): array
    {
        $response = $this->request()->get("transactions/{$transactionId}");

        return $this->decode($response);
    }

    /**
     * List payment methods available for this merchant account.
     */
    public function listPaymentMethods(): array
    {
        $response = $this->request()->get('payment_methods');

        return $this->decode($response);
    }

    /**
     * Check whether this service instance has an API key configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->key) && !str_starts_with($this->key, 'kp_test_your_sandbox');
    }

    /**
     * Base HTTP client instance with auth, timeouts, and safe retry.
     */
    private function request(): PendingRequest
    {
        return Http::baseUrl($this->normalizedBaseUrl)
            ->withToken($this->key)
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            // Retry 2 times with 200ms delay. Safe because Idempotency-Key is sent on write requests.
            ->retry(2, 200, throw: false);
    }

    /**
     * Decode JSON response or throw an HTTP client exception.
     */
    private function decode(Response $response): array
    {
        if ($response->failed()) {
            Log::error('Kasera Pay API Error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            $response->throw();
        }

        return $response->json() ?? [];
    }
}
