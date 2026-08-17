<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Default Packages
        $freePackage = Package::updateOrCreate(
            ['slug' => 'free'],
            [
                'name'                => 'Free Tier',
                'description'         => 'Paket gratis untuk testing dan kebutuhan dasar integrasi WhatsApp.',
                'price'               => 0,
                'max_devices'         => 2,
                'daily_message_limit' => 50,
                'benefits'            => [
                    '2 Slot Perangkat WhatsApp Multi-Device',
                    '50 Kuota Pesan per Hari',
                    'Akses Full REST API & Webhook',
                    'Real-time Message Logs & Status',
                    'Community Support',
                ],
                'status'              => 'active',
                'badge'               => 'Gratis',
                'sort_order'          => 1,
            ]
        );

        Package::updateOrCreate(
            ['slug' => 'starter-pro'],
            [
                'name'                => 'Starter Pro',
                'description'         => 'Solusi ideal untuk UMKM, Online Shop, dan Notifikasi Otomatis.',
                'price'               => 75000,
                'max_devices'         => 5,
                'daily_message_limit' => 1000,
                'benefits'            => [
                    '5 Slot Perangkat WhatsApp Multi-Nomor',
                    '1.000 Kuota Pesan per Hari',
                    'Smart Anti-Ban Anti-Spam Queue Delay',
                    'Bulk Send Broadcast Feature',
                    'REST API, Webhook & Event Listener',
                    'Dukungan Prioritas via WhatsApp Chat',
                ],
                'status'              => 'coming_soon',
                'badge'               => 'Segera Hadir',
                'sort_order'          => 2,
            ]
        );

        Package::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name'                => 'Business Enterprise',
                'description'         => 'Infrastruktur skala besar dengan high throughput & priority queue.',
                'price'               => 250000,
                'max_devices'         => 15,
                'daily_message_limit' => 10000,
                'benefits'            => [
                    '15 Slot Perangkat WhatsApp Multi-Nomor',
                    '10.000 Kuota Pesan per Hari',
                    'Dedicated High Priority Queue Worker',
                    'Custom Webhook Endpoints & Analytics',
                    'Multi-Tenant Integration Support',
                    'Dukungan Teknis VIP 24/7 Langsung',
                ],
                'status'              => 'coming_soon',
                'badge'               => 'Coming Soon',
                'sort_order'          => 3,
            ]
        );

        // 2. Super Admin (Aplikator)
        $admin = User::firstOrCreate(
            ['email' => 'admin@gateway.local'],
            [
                'name'       => 'Super Admin Aplikator',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'package_id' => $freePackage->id,
            ]
        );
        if (!$admin->package_id) {
            $admin->update(['package_id' => $freePackage->id]);
        }
        if ($admin->apiKeys()->count() === 0) {
            $admin->apiKeys()->create([
                'name'      => 'Admin Master Key',
                'key'       => ApiKey::generateKey(),
                'is_active' => true,
            ]);
        }

        // 3. Demo Tenant User
        $user = User::firstOrCreate(
            ['email' => 'user@gateway.local'],
            [
                'name'       => 'Demo Tenant User',
                'password'   => Hash::make('password'),
                'role'       => 'user',
                'package_id' => $freePackage->id,
            ]
        );
        if (!$user->package_id) {
            $user->update(['package_id' => $freePackage->id]);
        }
        if ($user->apiKeys()->count() === 0) {
            $user->apiKeys()->create([
                'name'      => 'Production API Key',
                'key'       => ApiKey::generateKey(),
                'is_active' => true,
            ]);
        }
    }
}
