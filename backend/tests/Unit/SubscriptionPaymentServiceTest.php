<?php

namespace Tests\Unit;

use App\Services\KaseraPayService;
use App\Services\SubscriptionPaymentService;
use PHPUnit\Framework\TestCase;

class SubscriptionPaymentServiceTest extends TestCase
{
    private SubscriptionPaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $mockKasera = $this->createMock(KaseraPayService::class);
        $this->service = new SubscriptionPaymentService($mockKasera);
    }

    public function test_calculate_clean_amount_returns_zero_for_free_tier(): void
    {
        $pricing = $this->service->calculateCleanAmount(0);

        $this->assertSame(0, $pricing['billed_amount']);
        $this->assertSame(0, $pricing['base_price']);
        $this->assertSame(0, $pricing['fee_kasera']);
        $this->assertSame(0, $pricing['unique_code']);
    }

    public function test_calculate_clean_amount_is_completely_static_and_deterministic(): void
    {
        $run1 = $this->service->calculateCleanAmount(75000);
        $run2 = $this->service->calculateCleanAmount(75000);

        // Harus statis 100%, tidak berubah-ubah atau acak
        $this->assertSame($run1['billed_amount'], $run2['billed_amount']);
        $this->assertSame($run1['fee_kasera'], $run2['fee_kasera']);
        $this->assertSame(0, $run1['unique_code']);
        $this->assertSame(0, $run2['unique_code']);
    }

    public function test_calculate_clean_amount_starter_pro_75k(): void
    {
        // Net: 75.000
        // Gross = ceil((75000 + 250) / 0.993) = ceil(75250 / 0.993) = 75781
        $pricing = $this->service->calculateCleanAmount(75000);

        $this->assertSame(75781, $pricing['billed_amount']);
        $this->assertSame(75000, $pricing['base_price']);
        $this->assertSame(0, $pricing['unique_code']);

        // Verifikasi potongan Kasera (0.7% + 250):
        // ceil(75781 * 0.007) + 250 = 531 + 250 = 781
        $this->assertSame(781, $pricing['fee_kasera']);

        // Saldo bersih diterima merchant minimal persis sesuai harga pokok (75.000)
        $cleanReceived = $pricing['billed_amount'] - $pricing['fee_kasera'];
        $this->assertGreaterThanOrEqual(75000, $cleanReceived);
    }

    public function test_calculate_clean_amount_enterprise_250k(): void
    {
        // Net: 250.000
        // Gross = ceil((250000 + 250) / 0.993) = ceil(250250 / 0.993) = 252015
        $pricing = $this->service->calculateCleanAmount(250000);

        $this->assertSame(252015, $pricing['billed_amount']);
        $this->assertSame(250000, $pricing['base_price']);
        $this->assertSame(0, $pricing['unique_code']);

        // Verifikasi potongan Kasera (0.7% + 250):
        // ceil(252015 * 0.007) + 250 = 1765 + 250 = 2015
        $this->assertSame(2015, $pricing['fee_kasera']);

        $cleanReceived = $pricing['billed_amount'] - $pricing['fee_kasera'];
        $this->assertGreaterThanOrEqual(250000, $cleanReceived);
    }

    public function test_calculate_clean_amount_small_transaction_under_100k_uses_same_static_rule(): void
    {
        // Semua pembayaran statis 0.7% + Rp250
        // Net: 50.000
        // Gross = ceil((50000 + 250) / 0.993) = ceil(50250 / 0.993) = 50605
        $pricing = $this->service->calculateCleanAmount(50000);

        $this->assertSame(50605, $pricing['billed_amount']);
        $this->assertSame(50000, $pricing['base_price']);
        $this->assertSame(0, $pricing['unique_code']);

        // Kasera fee: ceil(50605 * 0.007) + 250 = 355 + 250 = 605
        $this->assertSame(605, $pricing['fee_kasera']);

        $cleanReceived = $pricing['billed_amount'] - $pricing['fee_kasera'];
        $this->assertSame(50000, $cleanReceived);
    }
}
