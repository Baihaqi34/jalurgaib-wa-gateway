<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Device;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DeviceApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ApiKey $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        Package::create([
            'name'                => 'Starter Free',
            'slug'                => 'starter-free',
            'badge'               => 'Free',
            'price'               => 0,
            'max_devices'         => 5,
            'daily_message_limit' => 50,
            'status'              => 'active',
            'is_default'          => true,
            'reset_cycle'         => 'daily',
        ]);

        $this->user = User::create([
            'name'     => 'API Tester',
            'email'    => 'tester@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->apiKey = ApiKey::create([
            'user_id' => $this->user->id,
            'name'    => 'Default Key',
            'key'     => 'test-key-' . bin2hex(random_bytes(16)),
        ]);

        // Mock Go WhatsApp Engine
        Http::fake([
            '*/api/device/status*' => Http::response([
                'success' => true,
                'message' => 'OK',
                'data'    => [
                    'device_id' => 'dev_mock_123',
                    'connected' => false,
                    'logged_in' => false,
                ],
            ], 200),
            '*/api/device/connect' => Http::response([
                'success' => true,
                'message' => 'QR code generated.',
                'data'    => [
                    'status'    => 'waiting_qr',
                    'device_id' => 'dev_mock_123',
                    'qr_code'   => 'mock-whatsapp-pairing-code-string',
                ],
            ], 200),
            '*/api/device/disconnect' => Http::response([
                'success' => true,
                'message' => 'Device disconnected',
            ], 200),
        ]);
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->getJson('/api/devices');
        $response->assertStatus(401);
    }

    public function test_can_create_device(): void
    {
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->postJson('/api/devices', [
            'name'         => 'CS Toko Online',
            'phone_number' => '081234567890',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.name', 'CS Toko Online');
        $response->assertJsonPath('data.phone_number', '6281234567890');
        $this->assertDatabaseHas('devices', [
            'name'         => 'CS Toko Online',
            'phone_number' => '6281234567890',
            'user_id'      => $this->user->id,
        ]);
    }

    public function test_can_list_devices(): void
    {
        $this->user->devices()->create([
            'name'         => 'Device Alpha',
            'device_id'    => 'dev_alpha_1',
            'phone_number' => '628111111111',
            'status'       => 'disconnected',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->getJson('/api/devices');

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonCount(1, 'data');
    }

    public function test_can_show_device_using_numeric_id_and_string_device_id(): void
    {
        $device = $this->user->devices()->create([
            'name'         => 'Device Bravo',
            'device_id'    => 'dev_bravo_2',
            'phone_number' => '628222222222',
            'status'       => 'disconnected',
        ]);

        // 1. Test using numeric ID
        $resId = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->getJson("/api/devices/{$device->id}");

        $resId->assertStatus(200);
        $resId->assertJsonPath('data.device_id', 'dev_bravo_2');

        // 2. Test using string device_id
        $resDevId = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->getJson("/api/devices/{$device->device_id}");

        $resDevId->assertStatus(200);
        $resDevId->assertJsonPath('data.id', $device->id);
    }

    public function test_can_update_device(): void
    {
        $device = $this->user->devices()->create([
            'name'         => 'Device Old Name',
            'device_id'    => 'dev_update_3',
            'phone_number' => '628333333333',
            'status'       => 'disconnected',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->putJson("/api/devices/{$device->device_id}", [
            'name' => 'Device Updated Name',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Device Updated Name');
        $this->assertDatabaseHas('devices', [
            'id'   => $device->id,
            'name' => 'Device Updated Name',
        ]);
    }

    public function test_can_check_device_status(): void
    {
        $device = $this->user->devices()->create([
            'name'         => 'Device Delta',
            'device_id'    => 'dev_delta_4',
            'phone_number' => '628444444444',
            'status'       => 'disconnected',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->getJson("/api/devices/{$device->device_id}/status");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.device_id', 'dev_delta_4');
    }

    public function test_can_scan_device_via_api(): void
    {
        $device = $this->user->devices()->create([
            'name'         => 'Device Echo',
            'device_id'    => 'dev_echo_5',
            'phone_number' => '628555555555',
            'status'       => 'disconnected',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->getJson("/api/devices/{$device->device_id}/scan");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('status', 'waiting_qr');
        $this->assertNotEmpty($response->json('qr_code'));
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $response->json('qr_base64'));
    }

    public function test_can_get_direct_qr_image(): void
    {
        $device = $this->user->devices()->create([
            'name'         => 'Device Foxtrot',
            'device_id'    => 'dev_foxtrot_6',
            'phone_number' => '628666666666',
            'status'       => 'disconnected',
        ]);

        // Using X-API-Key header
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->get("/api/devices/{$device->device_id}/qr");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_can_delete_device(): void
    {
        $device = $this->user->devices()->create([
            'name'         => 'Device Golf',
            'device_id'    => 'dev_golf_7',
            'phone_number' => '628777777777',
            'status'       => 'disconnected',
        ]);

        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->deleteJson("/api/devices/{$device->device_id}");

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseMissing('devices', ['id' => $device->id]);
    }

    public function test_cross_tenant_isolation_is_enforced(): void
    {
        $otherUser = User::create([
            'name'     => 'Victim User',
            'email'    => 'victim@example.com',
            'password' => bcrypt('password123'),
        ]);

        $otherDevice = $otherUser->devices()->create([
            'name'         => 'Victim Device',
            'device_id'    => 'dev_victim_99',
            'phone_number' => '628999999999',
            'status'       => 'connected',
        ]);

        // Attacker attempts to access victim's device
        $response = $this->withHeaders([
            'X-API-Key' => $this->apiKey->key,
        ])->getJson("/api/devices/{$otherDevice->device_id}");

        $response->assertStatus(403);
    }
}
