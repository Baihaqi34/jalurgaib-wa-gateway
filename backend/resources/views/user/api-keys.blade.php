@extends('layouts.app')

@section('title', 'API Key — JalurGaib WA Gateway')
@section('header_title', 'API Key')
@section('header_subtitle', 'Kelola Token Rahasia & Kunci Akses Autentikasi Gateway')

@section('content')
<div x-data="userApiKeysApp()" class="space-y-6 font-sans max-w-6xl">

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
            <a href="{{ route('docs') }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-mono-850 hover:bg-mono-800 border border-mono-700 text-mono-300 hover:text-white text-xs font-semibold transition inline-flex items-center gap-1.5">
                <i class="fa-solid fa-book-bookmark text-xs"></i>
                <span>Buka Dokumentasi</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-mono-400"></i>
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

    <!-- Integration Documentation Guide Card -->
    <div class="mono-card p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-mono-700/80">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-mono-200 flex-shrink-0">
                <i class="fa-solid fa-book-bookmark text-sm"></i>
            </div>
            <div>
                <h4 class="text-xs font-bold text-mono-100">Panduan Integrasi & Contoh Kodingan Lengkap</h4>
                <p class="text-[11px] text-mono-400 mt-0.5">Lihat daftar endpoint REST API, parameter JSON, webhook, dan contoh kode PHP/Laravel, Node.js, Python, & cURL di halaman dokumentasi.</p>
            </div>
        </div>
        <a href="{{ route('docs') }}" target="_blank" class="px-4 py-2 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-1.5 shadow-sm whitespace-nowrap">
            <span>Buka Dokumentasi REST API</span>
            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
        </a>
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
