@extends('layouts.docs')

@php
    $user = auth()->user();
    $activeKeyObj = $user ? $user->apiKeys()->where('is_active', true)->first() : null;
    if (!$activeKeyObj && $user) {
        $activeKeyObj = $user->apiKeys()->first();
    }
    $userApiKey = $activeKeyObj ? $activeKeyObj->key : 'wag_YOUR_SECRET_API_KEY';

    $activeDeviceObj = $user ? $user->devices()->first() : null;
    $userDeviceId = $activeDeviceObj ? $activeDeviceObj->device_id : 'dev_GadV4eCt8wJF';
    $baseUrl = url('/api');
@endphp

@section('title', 'Dokumentasi REST API — JalurGaib WA Gateway')

@section('content')
<div class="space-y-8 w-full" x-data="docsApp()">

    <!-- GLOBAL LANGUAGE BAR & QUICK BANNER -->
    <div id="overview" data-doc-section class="mono-card p-5 sm:p-6 w-full scroll-mt-24 space-y-4">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold bg-mono-800 text-mono-300 border border-mono-700">REST API v1.0</span>
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Live Production</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-mono-50 tracking-tight">Dokumentasi REST API JalurGaib</h1>
                <p class="text-xs text-mono-400 mt-1">Integrasi WhatsApp Gateway aman & cepat dengan Anti-Ban Human Delay.</p>
            </div>

            <!-- Base URL Box -->
            <div class="bg-mono-950 px-4 py-2.5 rounded-xl border border-mono-800 flex items-center justify-between gap-4 w-full sm:w-auto">
                <div>
                    <div class="text-[9px] font-mono uppercase text-mono-400 font-bold">Base URL</div>
                    <code class="text-xs font-mono font-bold text-mono-100">{{ $baseUrl }}</code>
                </div>
                <button @click="copyText('{{ $baseUrl }}', 'base_url')" class="text-xs text-mono-400 hover:text-white font-bold p-1">
                    <span x-text="copiedKey === 'base_url' ? '✓ Tersalin' : '📋'"></span>
                </button>
            </div>
        </div>

        <!-- Global Language Switcher -->
        <div class="pt-3 border-t border-mono-800/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="text-xs text-mono-400 font-medium">
                Pilih bahasa pemrograman default untuk semua contoh kode:
            </div>
            <div class="docs-tab-nav">
                <button type="button" @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                <button type="button" @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel / PHP</button>
                <button type="button" @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                <button type="button" @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
            </div>
        </div>
    </div>

    <!-- 1. AUTENTIKASI -->
    <div id="auth" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-3 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <h2 class="text-sm sm:text-base font-extrabold text-mono-50 flex items-center gap-2">
                <span>🔑</span> Autentikasi Header API Key
            </h2>
            <button @click="copyText('X-API-Key: {{ $userApiKey }}', 'auth_header')" class="px-3 py-1 mono-btn-secondary text-[11px] font-bold">
                <span x-text="copiedKey === 'auth_header' ? '✓ Tersalin!' : '📋 Salin Header'"></span>
            </button>
        </div>
        <p class="text-xs text-mono-400">Sertakan header <code>X-API-Key</code> di setiap request ke endpoint privat.</p>
        
        <div class="p-3.5 code-terminal font-mono text-xs space-y-1">
            <div><span class="text-sky-400 font-semibold">Content-Type</span>: <span class="text-emerald-400">application/json</span></div>
            <div><span class="text-sky-400 font-semibold">Accept</span>: <span class="text-emerald-400">application/json</span></div>
            <div><span class="text-sky-400 font-semibold">X-API-Key</span>: <span class="text-amber-300 font-bold">{{ $userApiKey }}</span></div>
        </div>
    </div>

    <!-- 2. PANDUAN LARAVEL CLIENT -->
    <div id="laravel" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <i class="fa-brands fa-laravel text-red-500 text-lg"></i>
                <h2 class="text-sm sm:text-base font-extrabold text-mono-50">Integrasi Cepat Laravel HTTP Client</h2>
            </div>
            <span class="text-[10px] font-mono bg-red-500/10 text-red-400 px-2.5 py-0.5 rounded-full border border-red-500/20 font-bold">Laravel 9/10/11+</span>
        </div>
        <p class="text-xs text-mono-400">Cukup simpan konfigurasi di file <code>.env</code> & <code>config/services.php</code>:</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
            <div class="space-y-1.5">
                <div class="flex justify-between items-center text-mono-400 font-mono text-[11px]">
                    <span>.env</span>
                    <button @click="copyElementText('code-env', 'c_env')" class="hover:text-white" x-text="copiedKey === 'c_env' ? '✓' : 'Salin'"></button>
                </div>
                <div class="p-3 code-terminal"><pre id="code-env">WA_URL={{ $baseUrl }}
WA_KEY={{ $userApiKey }}
WA_DEVICE={{ $userDeviceId }}</pre></div>
            </div>

            <div class="space-y-1.5">
                <div class="flex justify-between items-center text-mono-400 font-mono text-[11px]">
                    <span>config/services.php</span>
                    <button @click="copyElementText('code-srv', 'c_srv')" class="hover:text-white" x-text="copiedKey === 'c_srv' ? '✓' : 'Salin'"></button>
                </div>
                <div class="p-3 code-terminal"><pre id="code-srv">'wa' => [
    'url'    => env('WA_URL'),
    'key'    => env('WA_KEY'),
    'device' => env('WA_DEVICE'),
],</pre></div>
            </div>
        </div>
    </div>

    <!-- 3. KIRIM PESAN TUNGGAL -->
    <div id="send-message" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <!-- Header -->
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-post">POST</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/messages/send</span>
            </div>
            <span class="text-[11px] font-mono text-mono-400">Kirim 1 Pesan Teks / Gambar</span>
        </div>
        <p class="text-xs text-mono-400">Memasukkan pesan tunggal ke antrean pengiriman dengan delay proteksi WhatsApp.</p>

        <!-- Multi-Language Code Tabs -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel / PHP</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-send-single')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-send-single">
                <!-- cURL -->
                <pre x-show="selectedLang === 'curl'">curl -X POST "{{ $baseUrl }}/messages/send" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: {{ $userApiKey }}" \
  -d '{
    "device_id": "{{ $userDeviceId }}",
    "to": "6281234567890",
    "message": "Halo! Pesanan #1042 Anda telah diproses.",
    "media_url": "https://example.com/invoice.jpg"
  }'</pre>

                <!-- Laravel -->
                <pre x-show="selectedLang === 'laravel'">use Illuminate\Support\Facades\Http;

$res = Http::withHeaders([
    'X-API-Key' => config('services.wa.key', '{{ $userApiKey }}'),
])->post('{{ $baseUrl }}/messages/send', [
    'device_id' => '{{ $userDeviceId }}',
    'to'        => '6281234567890',
    'message'   => 'Halo! Pesanan #1042 Anda telah diproses.',
    'media_url' => 'https://example.com/invoice.jpg', // Opsional
]);

$data = $res->json();</pre>

                <!-- Node.js -->
                <pre x-show="selectedLang === 'node'">const axios = require('axios');

const res = await axios.post('{{ $baseUrl }}/messages/send', {
  device_id: '{{ $userDeviceId }}',
  to: '6281234567890',
  message: 'Halo! Pesanan #1042 Anda telah diproses.',
  media_url: 'https://example.com/invoice.jpg'
}, {
  headers: { 'X-API-Key': '{{ $userApiKey }}' }
});</pre>

                <!-- Python -->
                <pre x-show="selectedLang === 'python'">import requests

res = requests.post(
    "{{ $baseUrl }}/messages/send",
    headers={"X-API-Key": "{{ $userApiKey }}"},
    json={
        "device_id": "{{ $userDeviceId }}",
        "to": "6281234567890",
        "message": "Halo! Pesanan #1042 Anda telah diproses."
    }
)</pre>
            </div>
        </div>

        <!-- Sample Response -->
        <div class="text-[11px] space-y-1">
            <span class="font-mono text-mono-400 font-bold uppercase text-[10px]">Contoh Response (200 OK):</span>
            <div class="p-3 code-terminal text-xs"><pre>{ "success": true, "message": "Pesan berhasil dijadwalkan", "data": { "message_id": 4821, "status": "pending" } }</pre></div>
        </div>
    </div>

    <!-- 4. KIRIM PESAN BULK -->
    <div id="send-bulk" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-post">POST</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/messages/send-bulk</span>
            </div>
            <span class="text-[11px] font-mono text-mono-400">Broadcast Banyak Nomor Sekaligus</span>
        </div>
        <p class="text-xs text-mono-400">Kirim pesan yang sama ke daftar array nomor penerima secara bertahap.</p>

        <!-- Multi-Language Code Tabs -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel / PHP</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-send-bulk')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-send-bulk">
                <!-- cURL -->
                <pre x-show="selectedLang === 'curl'">curl -X POST "{{ $baseUrl }}/messages/send-bulk" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: {{ $userApiKey }}" \
  -d '{
    "device_id": "{{ $userDeviceId }}",
    "recipients": ["6281234567890", "085123456789"],
    "message": "Pengumuman: Promo diskon 20% berlaku hari ini!",
    "min_delay": 2000,
    "max_delay": 5000
  }'</pre>

                <!-- Laravel -->
                <pre x-show="selectedLang === 'laravel'">$res = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->post('{{ $baseUrl }}/messages/send-bulk', [
        'device_id'  => '{{ $userDeviceId }}',
        'recipients' => ['6281234567890', '628987654321'],
        'message'    => 'Pengumuman: Promo diskon 20% berlaku hari ini!',
    ]);</pre>

                <!-- Node.js -->
                <pre x-show="selectedLang === 'node'">const res = await axios.post('{{ $baseUrl }}/messages/send-bulk', {
  device_id: '{{ $userDeviceId }}',
  recipients: ['6281234567890', '628987654321'],
  message: 'Pengumuman: Promo diskon 20% berlaku hari ini!'
}, {
  headers: { 'X-API-Key': '{{ $userApiKey }}' }
});</pre>

                <!-- Python -->
                <pre x-show="selectedLang === 'python'">res = requests.post(
    "{{ $baseUrl }}/messages/send-bulk",
    headers={"X-API-Key": "{{ $userApiKey }}"},
    json={
        "device_id": "{{ $userDeviceId }}",
        "recipients": ["6281234567890", "628987654321"],
        "message": "Pengumuman: Promo diskon 20% berlaku hari ini!"
    }
)</pre>
            </div>
        </div>
    </div>

    <!-- 5. DAFTAR PERANGKAT -->
    <div id="devices-list" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-get">GET</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/devices</span>
            </div>
            <span class="text-[11px] font-mono text-mono-400">Ambil Semua Perangkat & Status Live</span>
        </div>
        <p class="text-xs text-mono-400">Mendapatkan seluruh daftar nomor WhatsApp milik Anda beserta status sinkronisasi live.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-dev-list')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-dev-list">
                <pre x-show="selectedLang === 'curl'">curl -X GET "{{ $baseUrl }}/devices" \
  -H "X-API-Key: {{ $userApiKey }}"</pre>
                <pre x-show="selectedLang === 'laravel'">$devices = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->get('{{ $baseUrl }}/devices')
    ->json('data');</pre>
                <pre x-show="selectedLang === 'node'">const res = await axios.get('{{ $baseUrl }}/devices', {
  headers: { 'X-API-Key': '{{ $userApiKey }}' }
});</pre>
                <pre x-show="selectedLang === 'python'">res = requests.get("{{ $baseUrl }}/devices", headers={"X-API-Key": "{{ $userApiKey }}"})</pre>
            </div>
        </div>
    </div>

    <!-- 6. TAMBAH PERANGKAT BARU -->
    <div id="devices-create" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-post">POST</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/devices</span>
            </div>
            <span class="text-[11px] font-mono text-mono-400">Daftarkan Nomor Baru</span>
        </div>
        <p class="text-xs text-mono-400">Mendaftarkan slot perangkat baru di akun Anda (dibatasi kuota paket aktif).</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-dev-create')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-dev-create">
                <pre x-show="selectedLang === 'curl'">curl -X POST "{{ $baseUrl }}/devices" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: {{ $userApiKey }}" \
  -d '{ "name": "CS Support Shift Pagi", "phone_number": "081234567890" }'</pre>
                <pre x-show="selectedLang === 'laravel'">$res = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->post('{{ $baseUrl }}/devices', [
        'name'         => 'CS Support Shift Pagi',
        'phone_number' => '081234567890',
    ]);</pre>
                <pre x-show="selectedLang === 'node'">const res = await axios.post('{{ $baseUrl }}/devices', {
  name: 'CS Support Shift Pagi',
  phone_number: '081234567890'
}, { headers: { 'X-API-Key': '{{ $userApiKey }}' } });</pre>
                <pre x-show="selectedLang === 'python'">res = requests.post(
    "{{ $baseUrl }}/devices",
    headers={"X-API-Key": "{{ $userApiKey }}"},
    json={"name": "CS Support Shift Pagi", "phone_number": "081234567890"}
)</pre>
            </div>
        </div>
    </div>

    <!-- 7. SCAN QR CODE VIA API -->
    <div id="devices-scan" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-get">GET</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/devices/{id}/scan</span>
            </div>
            <span class="text-[11px] font-mono text-emerald-400 font-bold">QR JSON & Base64 SVG</span>
        </div>
        <p class="text-xs text-mono-400">Mengambil kode QR pairing WhatsApp aktif dalam format string dan Base64 Data URI siap pakai di tag <code>&lt;img&gt;</code>.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-dev-scan')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-dev-scan">
                <pre x-show="selectedLang === 'curl'">curl -X GET "{{ $baseUrl }}/devices/{{ $userDeviceId }}/scan" \
  -H "X-API-Key: {{ $userApiKey }}"</pre>
                <pre x-show="selectedLang === 'laravel'">$qr = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->get("{{ $baseUrl }}/devices/{{ $userDeviceId }}/scan")
    ->json('qr_base64'); // Gunakan langsung di &lt;img src="..."&gt;</pre>
                <pre x-show="selectedLang === 'node'">const res = await axios.get('{{ $baseUrl }}/devices/{{ $userDeviceId }}/scan', {
  headers: { 'X-API-Key': '{{ $userApiKey }}' }
});
const qrSvg = res.data.qr_base64;</pre>
                <pre x-show="selectedLang === 'python'">res = requests.get("{{ $baseUrl }}/devices/{{ $userDeviceId }}/scan", headers={"X-API-Key": "{{ $userApiKey }}"})
qr_base64 = res.json().get('qr_base64')</pre>
            </div>
        </div>

        <div class="text-[11px] text-mono-400 bg-mono-950 p-3 rounded-xl border border-mono-800">
            💡 <strong>Direct SVG Stream:</strong> Panggil endpoint <code>GET {{ $baseUrl }}/devices/{id}/qr</code> untuk langsung menghasilkan raw image SVG.
        </div>
    </div>

    <!-- 8. CEK STATUS LIVE KONEKSI -->
    <div id="devices-status" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-get">GET</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/devices/{id}/status</span>
            </div>
            <span class="text-[11px] font-mono text-sky-400 font-bold">Live Whatsmeow Status</span>
        </div>
        <p class="text-xs text-mono-400">Cek status koneksi real-time (<code>connected</code>, <code>scan_qr</code>, atau <code>disconnected</code>).</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-dev-status')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-dev-status">
                <pre x-show="selectedLang === 'curl'">curl -X GET "{{ $baseUrl }}/devices/{{ $userDeviceId }}/status" \
  -H "X-API-Key: {{ $userApiKey }}"</pre>
                <pre x-show="selectedLang === 'laravel'">$status = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->get("{{ $baseUrl }}/devices/{{ $userDeviceId }}/status")
    ->json('status'); // 'connected' | 'disconnected' | 'scan_qr'</pre>
                <pre x-show="selectedLang === 'node'">const res = await axios.get('{{ $baseUrl }}/devices/{{ $userDeviceId }}/status', {
  headers: { 'X-API-Key': '{{ $userApiKey }}' }
});</pre>
                <pre x-show="selectedLang === 'python'">res = requests.get("{{ $baseUrl }}/devices/{{ $userDeviceId }}/status", headers={"X-API-Key": "{{ $userApiKey }}"})</pre>
            </div>
        </div>
    </div>

    <!-- 9. DETAIL PERANGKAT -->
    <div id="devices-detail" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-get">GET</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/devices/{id}</span>
            </div>
            <span class="text-[11px] font-mono text-mono-400">Detail Spesifik Perangkat</span>
        </div>
        <p class="text-xs text-mono-400">Mendapatkan data status live dan informasi detail 1 nomor WhatsApp berdasarkan ID numerik atau string <code>device_id</code>.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-dev-show')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-dev-show">
                <pre x-show="selectedLang === 'curl'">curl -X GET "{{ $baseUrl }}/devices/{{ $userDeviceId }}" \
  -H "X-API-Key: {{ $userApiKey }}"</pre>
                <pre x-show="selectedLang === 'laravel'">$res = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->get('{{ $baseUrl }}/devices/{{ $userDeviceId }}');
$device = $res->json('data');</pre>
                <pre x-show="selectedLang === 'node'">const res = await axios.get('{{ $baseUrl }}/devices/{{ $userDeviceId }}', {
  headers: { 'X-API-Key': '{{ $userApiKey }}' }
});</pre>
                <pre x-show="selectedLang === 'python'">res = requests.get("{{ $baseUrl }}/devices/{{ $userDeviceId }}", headers={"X-API-Key": "{{ $userApiKey }}"})</pre>
            </div>
        </div>
    </div>

    <!-- 10. EDIT / UPDATE PERANGKAT -->
    <div id="devices-update" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-put">PUT</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/devices/{id}</span>
            </div>
            <span class="text-[11px] font-mono text-mono-400">Update Nama / Label</span>
        </div>
        <p class="text-xs text-mono-400">Memperbarui nama label atau metadata perangkat tanpa memutus koneksi WhatsApp yang sedang berjalan.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-dev-update')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-dev-update">
                <pre x-show="selectedLang === 'curl'">curl -X PUT "{{ $baseUrl }}/devices/{{ $userDeviceId }}" \
  -H "Content-Type: application/json" \
  -H "X-API-Key: {{ $userApiKey }}" \
  -d '{ "name": "CS Support Shift Siang" }'</pre>
                <pre x-show="selectedLang === 'laravel'">$res = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->put('{{ $baseUrl }}/devices/{{ $userDeviceId }}', [
        'name' => 'CS Support Shift Siang',
    ]);</pre>
                <pre x-show="selectedLang === 'node'">const res = await axios.put('{{ $baseUrl }}/devices/{{ $userDeviceId }}', {
  name: 'CS Support Shift Siang'
}, { headers: { 'X-API-Key': '{{ $userApiKey }}' } });</pre>
                <pre x-show="selectedLang === 'python'">res = requests.put(
    "{{ $baseUrl }}/devices/{{ $userDeviceId }}",
    headers={"X-API-Key": "{{ $userApiKey }}"},
    json={"name": "CS Support Shift Siang"}
)</pre>
            </div>
        </div>
    </div>

    <!-- 11. HAPUS PERANGKAT -->
    <div id="devices-delete" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div class="flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2.5">
                <span class="method-badge method-delete">DEL</span>
                <span class="text-xs sm:text-sm font-mono font-bold text-mono-100">/api/devices/{id}</span>
            </div>
            <span class="text-[11px] font-mono text-rose-400">Disconnect & Hapus Slot</span>
        </div>
        <p class="text-xs text-mono-400">Menghapus perangkat dari database akun Anda dan otomatis memutus sesi aktif WhatsApp di engine.</p>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <div class="docs-tab-nav">
                    <button @click="selectedLang = 'curl'" :class="selectedLang === 'curl' ? 'docs-tab-btn active' : 'docs-tab-btn'">cURL</button>
                    <button @click="selectedLang = 'laravel'" :class="selectedLang === 'laravel' ? 'docs-tab-btn active' : 'docs-tab-btn'">Laravel</button>
                    <button @click="selectedLang = 'node'" :class="selectedLang === 'node' ? 'docs-tab-btn active' : 'docs-tab-btn'">Node.js</button>
                    <button @click="selectedLang = 'python'" :class="selectedLang === 'python' ? 'docs-tab-btn active' : 'docs-tab-btn'">Python</button>
                </div>
                <button @click="copyActiveSnippet('code-dev-delete')" class="text-xs text-mono-400 hover:text-white font-bold">📋 Salin</button>
            </div>

            <div class="p-4 code-terminal text-xs overflow-x-auto shadow-inner" id="code-dev-delete">
                <pre x-show="selectedLang === 'curl'">curl -X DELETE "{{ $baseUrl }}/devices/{{ $userDeviceId }}" \
  -H "X-API-Key: {{ $userApiKey }}"</pre>
                <pre x-show="selectedLang === 'laravel'">$res = Http::withHeaders(['X-API-Key' => '{{ $userApiKey }}'])
    ->delete('{{ $baseUrl }}/devices/{{ $userDeviceId }}');</pre>
                <pre x-show="selectedLang === 'node'">const res = await axios.delete('{{ $baseUrl }}/devices/{{ $userDeviceId }}', {
  headers: { 'X-API-Key': '{{ $userApiKey }}' }
});</pre>
                <pre x-show="selectedLang === 'python'">res = requests.delete("{{ $baseUrl }}/devices/{{ $userDeviceId }}", headers={"X-API-Key": "{{ $userApiKey }}"})</pre>
            </div>
        </div>
    </div>

    <!-- 10. HTTP STATUS CODES -->
    <div id="error-codes" data-doc-section class="mono-card p-5 sm:p-6 w-full space-y-4 scroll-mt-24">
        <div>
            <h2 class="text-sm sm:text-base font-extrabold text-mono-50 flex items-center gap-2">
                <span>⚠️</span> Standar HTTP Status & Error
            </h2>
            <p class="text-xs text-mono-400 mt-0.5">Format response konsisten dengan field <code>success</code>, <code>message</code>, dan <code>data</code>.</p>
        </div>

        <div class="overflow-x-auto border border-mono-800 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-mono-950 text-mono-400 font-mono text-[10px] uppercase border-b border-mono-800">
                    <tr>
                        <th class="py-2.5 px-3.5">Status</th>
                        <th class="py-2.5 px-3.5">Keterangan</th>
                        <th class="py-2.5 px-3.5">Solusi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800/60 font-medium text-mono-300 text-[11px]">
                    <tr class="hover:bg-mono-900/40">
                        <td class="py-2 px-3.5 font-mono font-bold text-emerald-400">200 / 201</td>
                        <td class="py-2 px-3.5">Sukses diproses</td>
                        <td class="py-2 px-3.5 text-mono-400">Ambil data dari <code>data</code>.</td>
                    </tr>
                    <tr class="hover:bg-mono-900/40">
                        <td class="py-2 px-3.5 font-mono font-bold text-amber-400">401 Unauthorized</td>
                        <td class="py-2 px-3.5">API Key salah / tidak aktif</td>
                        <td class="py-2 px-3.5 text-mono-400">Periksa header <code>X-API-Key</code>.</td>
                    </tr>
                    <tr class="hover:bg-mono-900/40">
                        <td class="py-2 px-3.5 font-mono font-bold text-amber-400">404 Not Found</td>
                        <td class="py-2 px-3.5">Perangkat tidak ditemukan</td>
                        <td class="py-2 px-3.5 text-mono-400">Pastikan <code>device_id</code> terdaftar.</td>
                    </tr>
                    <tr class="hover:bg-mono-900/40">
                        <td class="py-2 px-3.5 font-mono font-bold text-rose-400">422 Unprocessable</td>
                        <td class="py-2 px-3.5">Validasi gagal (nomor invalid)</td>
                        <td class="py-2 px-3.5 text-mono-400">Format nomor internasional (628...).</td>
                    </tr>
                    <tr class="hover:bg-mono-900/40">
                        <td class="py-2 px-3.5 font-mono font-bold text-rose-400">429 Too Many Requests</td>
                        <td class="py-2 px-3.5">Kuota pesan habis</td>
                        <td class="py-2 px-3.5 text-mono-400">Upgrade paket langganan.</td>
                    </tr>
                    <tr class="hover:bg-mono-900/40">
                        <td class="py-2 px-3.5 font-mono font-bold text-red-500">503 Unavailable</td>
                        <td class="py-2 px-3.5">Engine WhatsApp offline</td>
                        <td class="py-2 px-3.5 text-mono-400">Scan QR ulang perangkat Anda.</td>
                    </tr>
                </tbody>
            </table>
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
                    this.copyText(text.trim(), key);
                }
            },

            copyActiveSnippet(containerId) {
                const container = document.getElementById(containerId);
                if (!container) return;
                const preElements = container.querySelectorAll('pre');
                let activeText = '';
                preElements.forEach(pre => {
                    if (pre.offsetParent !== null || pre.style.display !== 'none') {
                        activeText = pre.innerText || pre.textContent;
                    }
                });
                if (activeText) {
                    this.copyText(activeText.trim(), containerId);
                }
            }
        }
    }
</script>
@endsection
