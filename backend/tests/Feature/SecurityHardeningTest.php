<?php

namespace Tests\Feature;

use App\Models\ApiKey;
use App\Models\Device;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed standard default package
        Package::create([
            'name'                => 'Starter Free',
            'badge'               => 'Free',
            'price'               => 0,
            'max_devices'         => 1,
            'daily_message_limit' => 50,
            'status'              => 'active',
            'is_default'          => true,
            'reset_cycle'         => 'daily',
        ]);
    }

    /**
     * Test CR-01: Firebase auth fail-closed when JWT is unverified/malformed.
     */
    public function test_firebase_unverified_token_is_rejected(): void
    {
        // Fake unverified JWT payload (header.payload.signature)
        $fakePayload = base64_encode(json_encode([
            'email'          => 'attacker@example.com',
            'name'           => 'Attacker',
            'sub'            => 'fake-firebase-uid-12345',
            'email_verified' => true,
        ]));
        $fakeToken = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.{$fakePayload}.fakesignature";

        $response = $this->postJson(route('auth.firebase.callback'), [
            'idToken' => $fakeToken,
        ]);

        // Must fail closed with 422 Unprocessable Entity
        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertDatabaseMissing('users', [
            'email' => 'attacker@example.com',
        ]);
    }

    /**
     * Test CR-02: Self-registration with admin email prefix is strictly blocked.
     */
    public function test_cannot_register_with_admin_prefix_email(): void
    {
        $response = $this->post(route('register'), [
            'name'                  => 'Fake Admin',
            'email'                 => 'admin@mycompany.com',
            'password'              => 'P@ssword12345',
            'password_confirmation' => 'P@ssword12345',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', [
            'email' => 'admin@mycompany.com',
        ]);
    }

    /**
     * Test CR-02: User.isAdmin() strictly checks role attribute, never email pattern.
     */
    public function test_is_admin_strictly_checks_role_not_email(): void
    {
        $userWithAdminEmail = User::create([
            'name'     => 'Regular User',
            'email'    => 'admin@external-domain.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $adminUser = User::create([
            'name'     => 'Real Admin',
            'email'    => 'staff@external-domain.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        $this->assertFalse($userWithAdminEmail->isAdmin());
        $this->assertTrue($adminUser->isAdmin());
    }

    /**
     * Test CR-02: Non-admin profile update cannot set email to admin prefix.
     */
    public function test_profile_update_blocks_admin_email_for_non_admins(): void
    {
        $user = User::create([
            'name'     => 'Regular User',
            'email'    => 'john@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $response = $this->actingAs($user)->put(route('user.profile.update'), [
            'name'  => 'John Updated',
            'email' => 'admin@tenant.com',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals('john@example.com', $user->fresh()->email);
    }

    /**
     * Test CR-03 & MD-02: Uploading PHP script or SVG into message send is strictly rejected.
     */
    public function test_file_upload_blocks_php_and_svg_and_enforces_image_mime(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name'     => 'API User',
            'email'    => 'api@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $apiKey = ApiKey::create([
            'user_id' => $user->id,
            'name'    => 'Test Key',
            'key'     => 'jg_sec_test_key_1234567890abcdef',
            'status'  => 'active',
        ]);

        $device = Device::create([
            'user_id'   => $user->id,
            'device_id' => 'dev_test_123',
            'name'      => 'Test Phone',
            'phone'     => '628123456789',
            'status'    => 'connected',
        ]);

        // 1. Attempt upload of PHP file disguised as image
        $phpFile = UploadedFile::fake()->create('shell.php', 10, 'application/x-php');
        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->post('/api/message/send', [
                'device_id' => $device->device_id,
                'to'        => '628987654321',
                'message'   => 'Test payload',
                'file'      => $phpFile,
            ]);

        $response->assertStatus(422);

        // 2. Attempt upload of SVG file (XSS vector)
        $svgFile = UploadedFile::fake()->create('vector.svg', 10, 'image/svg+xml');
        $response = $this->withHeader('X-API-Key', $apiKey->key)
            ->post('/api/message/send', [
                'device_id' => $device->device_id,
                'to'        => '628987654321',
                'message'   => 'Test payload',
                'file'      => $svgFile,
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test HI-01: Path traversal attempts via /images/{filename} route return 404.
     */
    public function test_path_traversal_via_images_route_returns_404(): void
    {
        // Traversal targeting .env
        $response1 = $this->get('/images/..%2f..%2f.env');
        $this->assertContains($response1->getStatusCode(), [404, 400]);

        $response2 = $this->get('/images/%2e%2e/%2e%2e/.env');
        $this->assertContains($response2->getStatusCode(), [404, 400]);

        $response3 = $this->get('/images/....//.env');
        $this->assertContains($response3->getStatusCode(), [404, 400]);
    }

    /**
     * Test MD-03: Suspended user is immediately blocked from logging in.
     */
    public function test_suspended_user_cannot_login_to_web(): void
    {
        $suspendedUser = User::create([
            'name'           => 'Banned User',
            'email'          => 'banned@example.com',
            'password'       => Hash::make('secret123'),
            'role'           => 'user',
            'status'         => 'suspended',
            'suspend_reason' => 'Violation of terms',
        ]);

        $response = $this->post(route('login'), [
            'email'    => 'banned@example.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test MD-05: API key passed via query parameter (?api_key=...) is rejected.
     */
    public function test_api_key_via_query_param_is_rejected(): void
    {
        $user = User::create([
            'name'     => 'Tenant User',
            'email'    => 'tenant@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'user',
        ]);

        $apiKey = ApiKey::create([
            'user_id' => $user->id,
            'name'    => 'Tenant Key',
            'key'     => 'jg_secret_live_key_9999999999',
            'status'  => 'active',
        ]);

        // Passing API key via query parameter must return 401 Unauthorized
        $response = $this->getJson("/api/devices?api_key={$apiKey->key}");
        $response->assertStatus(401);

        // Passing API key via X-API-Key header must succeed (200 OK)
        $validResponse = $this->withHeader('X-API-Key', $apiKey->key)
            ->getJson('/api/devices');
        $validResponse->assertStatus(200);
    }
}
