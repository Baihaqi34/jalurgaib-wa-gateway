<?php

namespace App\Support;

class KaseraPaySignature
{
    /**
     * Default timestamp tolerance in seconds (5 minutes) to protect against replay attacks.
     */
    public const TOLERANCE_SECONDS = 300;

    /**
     * Verify the Kasera-Signature-V1 webhook header over the raw request payload.
     *
     * @param string $rawBody The exact un-parsed request body content
     * @param string|null $header The raw 'Kasera-Signature-V1' header
     * @param string $secret The webhook signing secret (whsec_...)
     * @param int|null $now Current unix timestamp (injectable for testing)
     * @param int $tolerance Maximum allowed age of signature in seconds
     * @return bool
     */
    public static function verify(
        string $rawBody,
        ?string $header,
        string $secret,
        ?int $now = null,
        int $tolerance = self::TOLERANCE_SECONDS
    ): bool {
        if ($header === null || trim($header) === '' || trim($secret) === '') {
            return false;
        }

        $parts = array_map('trim', explode(',', $header));

        $timestamp = null;
        $signatures = [];

        foreach ($parts as $part) {
            if (str_starts_with($part, 't=')) {
                $timestamp = substr($part, 2);
            } elseif (str_starts_with($part, 'v1=')) {
                $signatures[] = substr($part, 3);
            }
        }

        if ($timestamp === null || !ctype_digit($timestamp) || empty($signatures)) {
            return false;
        }

        $now ??= time();

        // Anti-replay protection: Reject deliveries older than tolerance window
        if (abs($now - (int) $timestamp) > $tolerance) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);

        $matched = false;
        // Compare every signature using constant-time hash_equals without early return
        // to support 24-hour key rotation grace window without timing leakage
        foreach ($signatures as $sig) {
            $matched = hash_equals($expected, $sig) || $matched;
        }

        return $matched;
    }
}
