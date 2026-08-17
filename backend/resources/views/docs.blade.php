@extends('layouts.app')

@php
    $user = auth()->user();
    $activeKeyObj = $user ? $user->apiKeys()->where('is_active', true)->first() : null;
    if (!$activeKeyObj && $user) {
        $activeKeyObj = $user->apiKeys()->first();
    }
    $userApiKey = $activeKeyObj ? $activeKeyObj->key : 'wag_YOUR_SECRET_API_KEY';

    $activeDeviceObj = $user ? $user->devices()->first() : null;
    $userDeviceId = $activeDeviceObj ? $activeDeviceObj->device_id : 'dev_GadV4eCt8wJF';
@endphp

@section('title', 'Dokumentasi REST API — JalurGaib WA Gateway')
@section('header_title', 'Dokumentasi Integrasi REST API')
@section('header_subtitle', 'Panduan integrasi lengkap untuk aplikasi Laravel, PHP, Node.js, Python & cURL')

@section('content')
<div class="space-y-8 w-full" x-data="docsApp()">

    <!-- Overview Banner -->
    <div class="mono-card p-8 w-full">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-800 text-mono-200 border border-mono-700 shadow-sm">RESTful API v1.0</span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-800 text-mono-200 border border-mono-700 shadow-sm">Anti-Ban Engine</span>
                </div>
                <h2 class="text-2xl font-extrabold text-mono-50 tracking-tight">Integrasikan WhatsApp ke Aplikasi Anda</h2>
                <p class="text-xs text-mono-400 max-w-3xl leading-relaxed font-medium">
                    Kirimkan pesan WhatsApp notifikasi, OTP, tagihan, ataupun broadcast dari backend aplikasi Anda (Laravel, Node.js, PHP, Python) menggunakan REST API JalurGaib WA Gateway dengan proteksi antrean cerdas.
                </p>
                @if($user && $activeKeyObj)
                    <div class="inline-flex items-center gap-2 text-[11px] font-mono text-mono-200 bg-mono-900 border border-mono-700 px-3 py-1 rounded-lg mt-1">
                        <span class="w-2 h-2 rounded-full bg-mono-300"></span>
                        <span>API Key terdeteksi dari akun Anda: <strong>{{ substr($userApiKey, 0, 12) }}...{{ substr($userApiKey, -6) }}</strong></span>
                    </div>
                @endif
            </div>

            <!-- Base URL with Copy -->
            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 space-y-2 w-full lg:w-auto lg:min-w-[320px] shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <span class="text-mono-400 text-[10px] uppercase font-bold tracking-wider">Base API URL:</span>
                    <button @click="copyText('{{ asset('/api') }}', 'base_url')" class="text-[11px] text-mono-200 hover:text-white font-bold flex items-center gap-1">
                        <span x-text="copiedKey === 'base_url' ? '✓ Tersalin' : '📋 Salin'"></span>
                    </button>
                </div>
                <div class="text-mono-50 font-mono font-bold text-xs select-all bg-mono-900 px-3.5 py-2.5 rounded-xl border border-mono-800 shadow-inner">
                    {{ asset('/api') }}
                </div>
            </div>
        </div>
    </div>

    <!-- 1. AUTENTIKASI -->
    <div class="mono-card p-8 w-full space-y-4">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h3 class="text-base font-extrabold text-mono-50 flex items-center gap-2">
                <span>🔑</span> 1. Autentikasi Header API Key
            </h3>
            <button @click="copyText('X-API-Key: {{ $userApiKey }}', 'auth_header')" class="px-3.5 py-1.5 mono-btn-secondary text-[11px] font-bold flex items-center gap-1.5 shadow-sm">
                <span x-text="copiedKey === 'auth_header' ? '✓ Tersalin!' : '📋 Salin Header'"></span>
            </button>
        </div>
        <p class="text-xs text-mono-400 leading-relaxed font-medium">
            Setiap request API ke endpoint privat wajib menyertakan header <code class="text-mono-100 font-mono font-bold bg-mono-800 px-1.5 py-0.5 rounded">X-API-Key</code> dengan token API Key aktif milik akun Anda (berformat <code class="text-mono-100 font-mono font-bold bg-mono-800 px-1.5 py-0.5 rounded">wag_...</code> yang digenerate di menu API Keys).
        </p>

        <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 font-mono text-xs text-mono-300 space-y-1.5 shadow-inner">
            <div class="text-mono-500">// Header Wajib Setiap Request:</div>
            <div><span class="text-mono-100 font-semibold">Content-Type</span>: application/json</div>
            <div><span class="text-mono-100 font-semibold">Accept</span>: application/json</div>
            <div><span class="text-mono-100 font-semibold">X-API-Key</span>: {{ $userApiKey }}</div>
        </div>
    </div>

    <!-- 2. KHUSUS APLIKASI LARAVEL (HTTP CLIENT INTEGRATION) -->
    <div class="mono-card p-8 w-full space-y-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center font-bold text-xl shadow-sm">
                    🔴
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-mono-50">Panduan Integrasi Aplikasi Laravel</h3>
                    <p class="text-xs text-mono-400">Menggunakan Laravel HTTP Client (Guzzle Wrapper) bawaan</p>
                </div>
            </div>
            <span class="px-3.5 py-1 bg-mono-800 text-mono-200 border border-mono-700 rounded-full text-[10px] font-bold uppercase font-mono shadow-sm">Laravel 9 / 10 / 11+</span>
        </div>

        <!-- Langkah 1: .env config -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-mono-300 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-mono-800 border border-mono-700 flex items-center justify-center text-[10px] text-white font-bold">1</span>
                    Tambahkan ke file <code>.env</code> project Laravel Anda:
                </h4>
                <button @click="copyElementText('laravel-env-code', 'lar_env')" class="px-3 py-1 mono-btn-secondary text-[11px] font-bold shadow-sm">
                    <span x-text="copiedKey === 'lar_env' ? '✓ Tersalin!' : '📋 Salin .env'"></span>
                </button>
            </div>
            <div class="p-4 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-100 overflow-x-auto shadow-inner">
                <pre id="laravel-env-code">WA_GATEWAY_URL={{ asset('/api') }}
WA_GATEWAY_API_KEY={{ $userApiKey }}
WA_GATEWAY_DEVICE_ID={{ $userDeviceId }}</pre>
            </div>
        </div>

        <!-- Langkah 2: config/services.php -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-mono-300 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-mono-800 border border-mono-700 flex items-center justify-center text-[10px] text-white font-bold">2</span>
                    Daftarkan di <code>config/services.php</code>:
                </h4>
                <button @click="copyElementText('laravel-config-code', 'lar_conf')" class="px-3 py-1 mono-btn-secondary text-[11px] font-bold shadow-sm">
                    <span x-text="copiedKey === 'lar_conf' ? '✓ Tersalin!' : '📋 Salin Config'"></span>
                </button>
            </div>
            <div class="p-4 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-100 overflow-x-auto shadow-inner">
                <pre id="laravel-config-code">'wa_gateway' => [
    'url'       => env('WA_GATEWAY_URL', '{{ asset('/api') }}'),
    'api_key'   => env('WA_GATEWAY_API_KEY', '{{ $userApiKey }}'),
    'device_id' => env('WA_GATEWAY_DEVICE_ID', '{{ $userDeviceId }}'),
],</pre>
            </div>
        </div>

        <!-- Langkah 3: Controller / Service Class -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h4 class="text-xs font-bold text-mono-300 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-mono-800 border border-mono-700 flex items-center justify-center text-[10px] text-white font-bold">3</span>
                    Contoh Service / Controller Laravel untuk Kirim WhatsApp:
                </h4>
                <button @click="copyElementText('laravel-service-code', 'lar_code')" class="px-4 py-1.5 mono-btn-primary text-xs font-bold shadow-sm">
                    <span x-text="copiedKey === 'lar_code' ? '✓ Tersalin!' : '📋 Salin Kode Laravel'"></span>
                </button>
            </div>
            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-200 overflow-x-auto leading-relaxed shadow-inner">
<pre id="laravel-service-code">&lt;?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Kirim pesan teks WhatsApp melalui JalurGaib WA Gateway
     */
    public static function sendMessage(string $to, string $message, ?string $deviceId = null): array
    {
        $url      = config('services.wa_gateway.url') . '/messages/send';
        $apiKey   = config('services.wa_gateway.api_key', '{{ $userApiKey }}');
        $deviceId = $deviceId ?? config('services.wa_gateway.device_id', '{{ $userDeviceId }}');

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Accept'    => 'application/json',
            ])->timeout(10)->post($url, [
                'device_id' => $deviceId,
                'to'        => $to,
                'message'   => $message,
                'min_delay' => 1000,
                'max_delay' => 3000,
            ]);

            if ($response->successful()) {
                return [
                    'success'    => true,
                    'message_id' => $response->json('data.message_id'),
                ];
            }

            Log::error('[JalurGaib WA Gateway Error]', $response->json() ?? []);
            return ['success' => false, 'error' => $response->json('message')];
        } catch (\Exception $e) {
            Log::error('[JalurGaib WA Gateway Exception] ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}</pre>
            </div>
        </div>
    </div>

    <!-- 3. ENDPOINT: KIRIM PESAN -->
    <div class="mono-card p-8 w-full space-y-6">
        <div>
            <h3 class="text-base font-extrabold text-mono-50 flex items-center gap-2">
                <span>💬</span> 3. Endpoint Pengiriman Pesan
            </h3>
            <p class="text-xs text-mono-400 mt-1">Mengirim pesan teks ke WhatsApp secara tunggal atau broadcast massal.</p>
        </div>

        <!-- 3.1 Single Message -->
        <div class="space-y-3 pt-2">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-mono-50 text-mono-950 rounded-xl text-xs font-bold font-mono shadow-sm">POST</span>
                    <span class="text-xs font-mono font-bold text-mono-50">/api/messages/send</span>
                </div>
                <button @click="copyElementText('single-payload-code', 'single_payload')" class="px-3 py-1 mono-btn-secondary text-[11px] font-bold shadow-sm">
                    <span x-text="copiedKey === 'single_payload' ? '✓ Tersalin!' : '📋 Salin Payload JSON'"></span>
                </button>
            </div>
            <p class="text-xs text-mono-400">Kirim 1 pesan teks ke satu nomor tujuan (Otomatis masuk ke antrean delivery queue).</p>

            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono shadow-inner">
                <pre id="single-payload-code" class="text-mono-200">{
  "device_id": "{{ $userDeviceId }}",
  "to": "6281234567890",
  "message": "Halo! Pesan konfirmasi pesanan #1042 Anda telah diproses.",
  "min_delay": 1000,
  "max_delay": 3000
}</pre>
            </div>
        </div>

        <!-- 3.2 Bulk Messages -->
        <div class="space-y-3 pt-5 border-t border-mono-800">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-mono-50 text-mono-950 rounded-xl text-xs font-bold font-mono shadow-sm">POST</span>
                    <span class="text-xs font-mono font-bold text-mono-50">/api/messages/send-bulk</span>
                </div>
                <button @click="copyElementText('bulk-payload-code', 'bulk_payload')" class="px-3 py-1 mono-btn-secondary text-[11px] font-bold shadow-sm">
                    <span x-text="copiedKey === 'bulk_payload' ? '✓ Tersalin!' : '📋 Salin Payload JSON'"></span>
                </button>
            </div>
            <p class="text-xs text-mono-400">Kirim broadcast pesan ke banyak nomor sekaligus (Didistribusikan berkala dengan anti-ban delay).</p>

            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono shadow-inner">
                <pre id="bulk-payload-code" class="text-mono-200">{
  "device_id": "{{ $userDeviceId }}",
  "recipients": [
    "6281234567890",
    "085123456789",
    "628987654321"
  ],
  "message": "Pengumuman: Layanan kami akan mengalami maintenance pada pukul 00:00 WIB.",
  "min_delay": 2000,
  "max_delay": 5000
}</pre>
            </div>
        </div>
    </div>

    <!-- 4. ENDPOINT: KELOLA PERANGKAT -->
    <div class="mono-card p-8 w-full space-y-6">
        <div>
            <h3 class="text-base font-extrabold text-mono-50 flex items-center gap-2">
                <span>📱</span> 4. Endpoint Manajemen Perangkat WhatsApp
            </h3>
            <p class="text-xs text-mono-400 mt-1">Daftarkan nomor baru, ambil QR Code pairing, dan pantau status koneksi.</p>
        </div>

        <!-- List Devices -->
        <div class="space-y-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-mono-800 text-mono-200 border border-mono-700 rounded-xl text-xs font-bold font-mono shadow-sm">GET</span>
                    <span class="text-xs font-mono font-bold text-mono-50">/api/devices</span>
                </div>
                <button @click="copyText('GET {{ asset('/api/devices') }}', 'get_dev')" class="px-3 py-1 mono-btn-secondary text-[11px] font-bold shadow-sm">
                    <span x-text="copiedKey === 'get_dev' ? '✓ Tersalin!' : '📋 Salin Endpoint'"></span>
                </button>
            </div>
            <p class="text-xs text-mono-400">Mendapatkan seluruh daftar perangkat WhatsApp terdaftar milik akun Anda beserta status koneksinya.</p>
        </div>

        <!-- Register Device -->
        <div class="space-y-2.5 pt-4 border-t border-mono-800">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-mono-50 text-mono-950 rounded-xl text-xs font-bold font-mono shadow-sm">POST</span>
                    <span class="text-xs font-mono font-bold text-mono-50">/api/devices</span>
                </div>
                <button @click="copyElementText('reg-device-code', 'reg_dev')" class="px-3 py-1 mono-btn-secondary text-[11px] font-bold shadow-sm">
                    <span x-text="copiedKey === 'reg_dev' ? '✓ Tersalin!' : '📋 Salin Payload'"></span>
                </button>
            </div>
            <p class="text-xs text-mono-400">Mendaftarkan perangkat/nomor WhatsApp baru ke akun Anda.</p>
            <div class="p-4 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-200 shadow-inner">
                <pre id="reg-device-code">{
  "name": "CS Support Utama",
  "phone_number": "628123456789"
}</pre>
            </div>
        </div>

        <!-- Connect Device -->
        <div class="space-y-2.5 pt-4 border-t border-mono-800">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-mono-50 text-mono-950 rounded-xl text-xs font-bold font-mono shadow-sm">POST</span>
                <span class="text-xs font-mono font-bold text-mono-50">/api/devices/{device_id}/connect</span>
            </div>
            <p class="text-xs text-mono-400">Menghasilkan string QR Code WhatsApp untuk di-scan oleh aplikasi WhatsApp HP.</p>
        </div>
    </div>

    <!-- 5. CONTOH KODE MULTI-BAHASA SDK -->
    <div class="mono-card p-8 w-full space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h3 class="text-base font-extrabold text-mono-50 flex items-center gap-2">
                <span>💻</span> 5. Contoh Kode Multi-Bahasa
            </h3>
            
            <!-- Language Selector Tabs -->
            <div class="flex gap-1.5 p-1 bg-mono-950 border border-mono-700 rounded-2xl shadow-inner">
                <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs transition">cURL</button>
                <button @click="selectedLang = 'js'" :class="selectedLang === 'js' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs transition">Node.js</button>
                <button @click="selectedLang = 'php'" :class="selectedLang === 'php' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs transition">PHP</button>
                <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs transition">Python</button>
            </div>
        </div>

        <!-- cURL -->
        <div x-show="selectedLang === 'curl'" class="space-y-2">
            <div class="flex justify-end">
                <button @click="copyElementText('curl-code-block', 'curl_code')" class="px-3 py-1 mono-btn-secondary text-xs font-bold shadow-sm">
                    <span x-text="copiedKey === 'curl_code' ? '✓ Tersalin!' : '📋 Salin cURL'"></span>
                </button>
            </div>
            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-200 overflow-x-auto shadow-inner">
                <pre id="curl-code-block">curl -X POST "{{ asset('/api/messages/send') }}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-API-Key: {{ $userApiKey }}" \
  -d '{
    "device_id": "{{ $userDeviceId }}",
    "to": "6281234567890",
    "message": "Halo! Pesan ini dikirim melalui JalurGaib WA Gateway.",
    "min_delay": 1000,
    "max_delay": 3000
  }'</pre>
            </div>
        </div>

        <!-- Node.js / JS -->
        <div x-show="selectedLang === 'js'" class="space-y-2">
            <div class="flex justify-end">
                <button @click="copyElementText('node-code-block', 'node_code')" class="px-3 py-1 mono-btn-secondary text-xs font-bold shadow-sm">
                    <span x-text="copiedKey === 'node_code' ? '✓ Tersalin!' : '📋 Salin Node.js'"></span>
                </button>
            </div>
            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-200 overflow-x-auto shadow-inner">
                <pre id="node-code-block">const axios = require('axios');

async function sendWhatsAppMessage() {
    try {
        const response = await axios.post('{{ asset('/api/messages/send') }}', {
            device_id: '{{ $userDeviceId }}',
            to: '6281234567890',
            message: 'Halo! Pesan dari Node.js Backend via JalurGaib WA Gateway.',
            min_delay: 1000,
            max_delay: 3000
        }, {
            headers: {
                'Content-Type': 'application/json',
                'X-API-Key': '{{ $userApiKey }}'
            }
        });

        console.log('Success:', response.data);
    } catch (error) {
        console.error('Error:', error.response?.data || error.message);
    }
}

sendWhatsAppMessage();</pre>
            </div>
        </div>

        <!-- PHP Native -->
        <div x-show="selectedLang === 'php'" class="space-y-2">
            <div class="flex justify-end">
                <button @click="copyElementText('php-code-block', 'php_code')" class="px-3 py-1 mono-btn-secondary text-xs font-bold shadow-sm">
                    <span x-text="copiedKey === 'php_code' ? '✓ Tersalin!' : '📋 Salin PHP'"></span>
                </button>
            </div>
            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-200 overflow-x-auto shadow-inner">
<pre id="php-code-block">&lt;?php

$payload = [
    'device_id' => '{{ $userDeviceId }}',
    'to'        => '6281234567890',
    'message'   => 'Halo! Pesan dari sistem PHP via JalurGaib WA Gateway.',
    'min_delay' => 1000,
    'max_delay' => 3000
];

$ch = curl_init('{{ asset('/api/messages/send') }}');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Accept: application/json',
        'X-API-Key: {{ $userApiKey }}'
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;</pre>
            </div>
        </div>

        <!-- Python -->
        <div x-show="selectedLang === 'python'" class="space-y-2">
            <div class="flex justify-end">
                <button @click="copyElementText('python-code-block', 'py_code')" class="px-3 py-1 mono-btn-secondary text-xs font-bold shadow-sm">
                    <span x-text="copiedKey === 'py_code' ? '✓ Tersalin!' : '📋 Salin Python'"></span>
                </button>
            </div>
            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 text-xs font-mono text-mono-200 overflow-x-auto shadow-inner">
                <pre id="python-code-block">import requests

url = "{{ asset('/api/messages/send') }}"
headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-API-Key": "{{ $userApiKey }}"
}
payload = {
    "device_id": "{{ $userDeviceId }}",
    "to": "6281234567890",
    "message": "Halo! Pesan dari aplikasi Python via JalurGaib WA Gateway.",
    "min_delay": 1000,
    "max_delay": 3000
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())</pre>
            </div>
        </div>

    </div>

</div>

<script>
    function docsApp() {
        return {
            selectedLang: 'curl',
            copiedKey: null,

            copyText(text, key) {
                navigator.clipboard.writeText(text);
                this.copiedKey = key;
                AppSwal.toastSuccess('Berhasil disalin ke clipboard!');
                setTimeout(() => {
                    if (this.copiedKey === key) this.copiedKey = null;
                }, 2000);
            },

            copyElementText(elementId, key) {
                const el = document.getElementById(elementId);
                if (el) {
                    const text = el.innerText || el.textContent;
                    this.copyText(text, key);
                }
            }
        }
    }
</script>
@endsection
