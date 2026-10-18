@extends('layouts.app')

@section('title', 'API Key & SDK — JalurGaib WA Gateway')
@section('header_title', 'API Key & Integrasi SDK')
@section('header_subtitle', 'Kelola Token Rahasia, Pengaturan Webhook & Panduan Integrasi REST API')

@section('content')
<div x-data="userApiKeysApp()" class="space-y-8 font-sans max-w-6xl">

    <!-- Top Action Card -->
    <div class="mono-card p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-key text-mono-200 text-lg"></i>
                <h3 class="text-base font-extrabold text-mono-50 tracking-tight">Kunci Akses API (API Keys)</h3>
            </div>
            <p class="text-xs text-mono-400 mt-1">
                Gunakan header <code class="px-1.5 py-0.5 rounded bg-mono-950 border border-mono-800 text-mono-200 font-mono text-[11px]">X-API-Key: {token}</code> untuk melakukan request ke seluruh endpoint Gateway.
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-between sm:justify-end">
            <a href="{{ route('docs') }}" class="px-3.5 py-2.5 rounded-xl bg-mono-850 hover:bg-mono-800 border border-mono-700 text-mono-300 hover:text-white text-xs font-semibold transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-book-bookmark text-xs"></i>
                <span>Buka Dokumentasi</span>
            </a>

            <button type="button" @click="createApiKey()" class="px-4 py-2.5 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-2 shadow-md hover:scale-[1.01] transition">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Generate API Key Baru</span>
            </button>
        </div>
    </div>

    <!-- Active API Keys Table -->
    <div class="mono-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-xs font-bold text-mono-100 font-mono uppercase tracking-wider">Daftar API Key Aktif</h4>
            <span class="text-xs text-mono-400 font-mono tabular-nums">{{ count($apiKeys) }} Kunci Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-mono-800 text-[11px] font-mono text-mono-400 uppercase">
                        <th class="py-3 px-4">Nama / Label</th>
                        <th class="py-3 px-4">Token API Key</th>
                        <th class="py-3 px-4">Tanggal Dibuat</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800/60 font-mono">
                    @forelse($apiKeys as $key)
                        <tr class="hover:bg-mono-900/40 transition">
                            <td class="py-3.5 px-4 font-sans font-bold text-mono-100">
                                {{ $key->name }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded bg-mono-950 border border-mono-800 text-[11px] text-mono-300 font-mono select-all">
                                        {{ Str::mask($key->key, '*', 8, -6) }}
                                    </span>
                                    <button type="button" @click="copyText('{{ $key->key }}')" class="p-1 text-mono-400 hover:text-white transition" title="Salin Full API Key">
                                        <i class="fa-regular fa-copy text-xs"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-mono-400 text-[11px] tabular-nums">
                                {{ $key->created_at->timezone('Asia/Jakarta')->format('d/m/Y H:i') }} WIB
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button type="button" @click="revokeApiKey({{ $key->id }})" class="px-2.5 py-1 rounded-lg bg-mono-800 hover:bg-red-500/20 text-mono-400 hover:text-red-400 border border-mono-700 hover:border-red-500/40 text-[11px] font-semibold transition inline-flex items-center gap-1">
                                    <i class="fa-solid fa-ban text-[10px]"></i>
                                    <span>Revoke</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-mono-500 font-sans">
                                <i class="fa-solid fa-key text-3xl mb-2 block text-mono-600"></i>
                                <span class="text-xs">Belum ada API Key yang digenerate. Klik tombol di atas untuk membuat token baru.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Interactive SDK Integration Snippets -->
    <div class="mono-card p-6 sm:p-8" x-data="{ sdkTab: 'curl' }">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-mono-800">
            <div>
                <h4 class="text-sm font-bold text-mono-50 flex items-center gap-2">
                    <i class="fa-solid fa-code text-mono-400"></i>
                    <span>Contoh Integrasi Kode SDK</span>
                </h4>
                <p class="text-xs text-mono-400 mt-0.5">Salin cuplikan kode di bawah ini ke dalam backend atau aplikasi Anda</p>
            </div>

            <!-- Language Tabs -->
            <div class="flex gap-1.5 p-1 bg-mono-950 border border-mono-700 rounded-xl">
                <button type="button" @click="sdkTab = 'curl'" :class="sdkTab === 'curl' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3 py-1 rounded-lg text-xs font-mono transition">cURL</button>
                <button type="button" @click="sdkTab = 'php'" :class="sdkTab === 'php' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3 py-1 rounded-lg text-xs font-mono transition">PHP / Laravel</button>
                <button type="button" @click="sdkTab = 'js'" :class="sdkTab === 'js' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3 py-1 rounded-lg text-xs font-mono transition">Node.js</button>
                <button type="button" @click="sdkTab = 'py'" :class="sdkTab === 'py' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3 py-1 rounded-lg text-xs font-mono transition">Python</button>
            </div>
        </div>

        @php
            $sampleKey = $apiKeys->first()->key ?? 'wag_your_api_key_here';
            $sampleDevice = auth()->user()->devices()->where('status', 'connected')->first()->device_id ?? 'dev_your_device_id';
        @endphp

        <!-- cURL Snippet -->
        <div x-show="sdkTab === 'curl'" class="relative">
            <pre class="p-4 bg-mono-950 rounded-xl border border-mono-800 text-xs font-mono text-mono-200 overflow-x-auto leading-relaxed"><code>curl -X POST "{{ url('/api/messages/send') }}" \
  -H "X-API-Key: {{ $sampleKey }}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "device_id": "{{ $sampleDevice }}",
    "to": "081234567890",
    "message": "Halo! Ini adalah pesan otomatis dari JalurGaib WA Gateway 🚀",
    "delay": 2
  }'</code></pre>
            <button type="button" @click="copyCode($el.previousElementSibling.innerText)" class="absolute top-3 right-3 px-2.5 py-1 rounded bg-mono-800 hover:bg-mono-700 text-mono-300 text-[11px] font-mono border border-mono-700 inline-flex items-center gap-1 transition">
                <i class="fa-regular fa-copy"></i>
                <span>Salin</span>
            </button>
        </div>

        <!-- PHP / Laravel Snippet -->
        <div x-show="sdkTab === 'php'" x-cloak class="relative">
            <pre class="p-4 bg-mono-950 rounded-xl border border-mono-800 text-xs font-mono text-mono-200 overflow-x-auto leading-relaxed"><code>use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'X-API-Key' => '{{ $sampleKey }}',
    'Accept'    => 'application/json',
])->post('{{ url('/api/messages/send') }}', [
    'device_id' => '{{ $sampleDevice }}',
    'to'        => '081234567890',
    'message'   => 'Halo! Pesanan Anda telah kami terima.',
    'delay'     => 2,
]);

if ($response->successful()) {
    $data = $response->json();
    // Pesan berhasil terkirim
}</code></pre>
            <button type="button" @click="copyCode($el.previousElementSibling.innerText)" class="absolute top-3 right-3 px-2.5 py-1 rounded bg-mono-800 hover:bg-mono-700 text-mono-300 text-[11px] font-mono border border-mono-700 inline-flex items-center gap-1 transition">
                <i class="fa-regular fa-copy"></i>
                <span>Salin</span>
            </button>
        </div>

        <!-- Node.js Snippet -->
        <div x-show="sdkTab === 'js'" x-cloak class="relative">
            <pre class="p-4 bg-mono-950 rounded-xl border border-mono-800 text-xs font-mono text-mono-200 overflow-x-auto leading-relaxed"><code>const axios = require('axios');

async function kirimPesan() {
    try {
        const response = await axios.post('{{ url('/api/messages/send') }}', {
            device_id: '{{ $sampleDevice }}',
            to: '081234567890',
            message: 'Halo via Node.js Gateway!',
            delay: 2
        }, {
            headers: {
                'X-API-Key': '{{ $sampleKey }}',
                'Content-Type': 'application/json'
            }
        });
        console.log(response.data);
    } catch (error) {
        console.error(error.response ? error.response.data : error.message);
    }
}
kirimPesan();</code></pre>
            <button type="button" @click="copyCode($el.previousElementSibling.innerText)" class="absolute top-3 right-3 px-2.5 py-1 rounded bg-mono-800 hover:bg-mono-700 text-mono-300 text-[11px] font-mono border border-mono-700 inline-flex items-center gap-1 transition">
                <i class="fa-regular fa-copy"></i>
                <span>Salin</span>
            </button>
        </div>

        <!-- Python Snippet -->
        <div x-show="sdkTab === 'py'" x-cloak class="relative">
            <pre class="p-4 bg-mono-950 rounded-xl border border-mono-800 text-xs font-mono text-mono-200 overflow-x-auto leading-relaxed"><code>import requests

url = "{{ url('/api/messages/send') }}"
headers = {
    "X-API-Key": "{{ $sampleKey }}",
    "Content-Type": "application/json"
}
payload = {
    "device_id": "{{ $sampleDevice }}",
    "to": "081234567890",
    "message": "Halo dari Python Gateway Client!",
    "delay": 2
}

response = requests.post(url, json=payload, headers=headers)
print(response.json())</code></pre>
            <button type="button" @click="copyCode($el.previousElementSibling.innerText)" class="absolute top-3 right-3 px-2.5 py-1 rounded bg-mono-800 hover:bg-mono-700 text-mono-300 text-[11px] font-mono border border-mono-700 inline-flex items-center gap-1 transition">
                <i class="fa-regular fa-copy"></i>
                <span>Salin</span>
            </button>
        </div>
    </div>
</div>

<script>
    function userApiKeysApp() {
        return {
            async createApiKey() {
                const { value: keyName } = await CustomSwal.fire({
                    title: 'Generate API Key Baru',
                    text: 'Beri label untuk API Key ini (misal: "Server Backend Toko"):',
                    input: 'text',
                    inputPlaceholder: 'Contoh: Laravel Backend Server',
                    showCancelButton: true,
                    confirmButtonText: 'Generate Key',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) return 'Nama API Key tidak boleh kosong!';
                    }
                });

                if (!keyName) return;

                AppSwal.loading('Membuat API Key...', 'Men-generate token rahasia...');
                try {
                    let res = await fetch('/api/api-keys', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-API-Key': document.querySelector('meta[name="api-key"]')?.content || ''
                        },
                        body: JSON.stringify({ name: keyName })
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        await AppSwal.success('Berhasil!', 'API Key baru telah digenerate.');
                        window.location.reload();
                    } else {
                        AppSwal.error('Gagal', json.message || 'Gagal membuat API Key.');
                    }
                } catch(e) {
                    AppSwal.close();
                    AppSwal.error('Error', 'Terjadi kesalahan jaringan.');
                }
            },

            async revokeApiKey(id) {
                const confirmed = await AppSwal.confirm(
                    'Revoke API Key?',
                    'Aplikasi yang menggunakan API Key ini tidak akan dapat mengakses endpoint lagi.',
                    'Ya, Revoke Key',
                    true
                );
                if (!confirmed) return;

                AppSwal.loading('Merevoke API Key...', 'Mematikan akses token...');
                try {
                    let res = await fetch(`/api/api-keys/${id}/revoke`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-API-Key': document.querySelector('meta[name="api-key"]')?.content || ''
                        }
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('API Key berhasil dinonaktifkan.');
                        window.location.reload();
                    } else {
                        AppSwal.toastError(json.message || 'Gagal merevoke key.');
                    }
                } catch(e) {
                    AppSwal.close();
                    AppSwal.toastError('Gagal menghubungi server.');
                }
            },

            copyText(text) {
                navigator.clipboard.writeText(text);
                AppSwal.toastSuccess('API Key disalin ke clipboard!');
            },

            copyCode(text) {
                navigator.clipboard.writeText(text);
                AppSwal.toastSuccess('Cuplikan kode disalin ke clipboard!');
            }
        }
    }
</script>
@endsection
