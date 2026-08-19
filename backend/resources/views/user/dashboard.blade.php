@extends('layouts.app')

@section('title', 'Tenant WhatsApp Portal — JalurGaib WA Gateway')
@section('header_title', 'Tenant WhatsApp Portal')
@section('header_subtitle', 'Panel Pengelolaan WhatsApp, Integrasi API & Pengiriman Pesan')

@section('content')
<div x-data="userApp()" x-init="initApp()" class="space-y-8">

    <!-- SUSPENSION ANNOUNCEMENT BANNER -->
    @if(auth()->user()->isSuspended())
        <div class="p-6 bg-mono-900 border-2 border-mono-500 rounded-3xl text-mono-200 shadow-2xl space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-mono-50 text-mono-950 flex items-center justify-center text-xl font-bold shadow-md">
                    ⛔
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-mono-50 tracking-tight">Akun Anda Sedang Ditangguhkan (Suspended)</h3>
                    <p class="text-xs text-mono-400">Seluruh layanan JalurGaib WA Gateway, pengiriman pesan, dan akses REST API dinonaktifkan sementara.</p>
                </div>
            </div>
            @if(auth()->user()->suspend_reason)
                <div class="p-4 bg-mono-950 rounded-2xl border border-mono-700 text-xs">
                    <span class="font-bold text-mono-300 block mb-1">📢 Catatan dari Administrator:</span>
                    <p class="text-mono-100 leading-relaxed font-mono">"{{ auth()->user()->suspend_reason }}"</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Personal Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Connected Devices -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">Perangkat WA</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">📱</div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50">
                <span x-text="stats.connected_dev">0</span>
                <span class="text-sm font-semibold text-mono-400">/ <span x-text="stats.total_devices">0</span> Aktif</span>
            </div>
            <div class="text-xs text-mono-400 mt-1.5 font-medium">Perangkat WhatsApp terhubung</div>
        </div>

        <!-- Sent Messages -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">Pesan Terkirim</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">✓</div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50" x-text="stats.sent_messages">0</div>
            <div class="text-xs text-mono-300 font-semibold mt-1.5">Berhasil di-deliver ke nomor tujuan</div>
        </div>

        <!-- Pending Messages in Queue -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">Antrean Queue</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">⏳</div>
            </div>
            <div class="text-3xl font-extrabold text-mono-200" x-text="stats.pending_messages">0</div>
            <div class="text-xs text-mono-400 mt-1.5 font-medium">Diproses dengan anti-ban delay</div>
        </div>

        <!-- Active API Keys -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">API Keys</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">🔑</div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50" x-text="stats.total_api_keys">0</div>
            <div class="text-xs text-mono-400 mt-1.5 font-medium">Token aktif integrasi REST API</div>
        </div>
    </div>

    <!-- PACKAGE TIER & QUOTA SECTION (BELOW STATS CARDS) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Active Package Card -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Paket Aktif Anda</div>
                        <div class="text-lg font-extrabold text-mono-50 mt-0.5">{{ $package->name }}</div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full text-[10px] font-bold bg-mono-50 text-mono-950 shadow-sm font-extrabold">
                        {{ $package->badge ?? ($package->price == 0 ? 'Gratis' : 'Aktif') }}
                    </div>
                </div>
                @if($package->description)
                    <p class="text-xs text-mono-400 mb-4 font-medium leading-relaxed">{{ $package->description }}</p>
                @endif
                @if(!empty($package->benefits) && is_array($package->benefits))
                    <ul class="space-y-1.5 mb-4">
                        @foreach($package->benefits as $benefit)
                            <li class="text-xs text-mono-300 flex items-start gap-2">
                                <span class="text-mono-100 font-bold flex-shrink-0">✓</span>
                                <span class="leading-tight">{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="pt-4 border-t border-mono-800 flex items-center justify-between mt-auto">
                <div class="text-[11px] text-mono-400 font-mono">
                    @if($package->price == 0)
                        GRATIS
                    @else
                        Rp {{ number_format($package->price, 0, ',', '.') }}<span class="text-mono-500">/bln</span>
                    @endif
                </div>
                <a href="{{ route('user.upgrade') }}" class="inline-flex items-center gap-1 text-[11px] font-extrabold text-mono-200 hover:text-white transition underline underline-offset-4">
                    Upgrade Paket →
                </a>
            </div>
        </div>

        <!-- Quota Progress Bars -->
        <div class="lg:col-span-2 mono-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Kuota & Pemakaian Hari Ini</div>
                    <a href="{{ route('user.upgrade') }}" class="text-[11px] font-bold text-mono-300 hover:text-white flex items-center gap-1 transition">
                        <span>⭐ Lihat Semua Paket</span>
                    </a>
                </div>

                <!-- Daily Messages Quota -->
                @php
                    $msgPct = $dailyLimit > 0 ? min(100, round(($todaySent / $dailyLimit) * 100)) : 0;
                    $msgColor = $msgPct >= 90 ? 'bg-mono-100' : ($msgPct >= 70 ? 'bg-mono-200' : 'bg-mono-400');
                @endphp
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-mono-200 flex items-center gap-2">
                            <span>💬</span> Kuota Pesan Harian
                        </span>
                        <span class="text-xs font-mono font-bold text-mono-300">
                            <span class="{{ $msgPct >= 90 ? 'text-mono-50' : 'text-mono-300' }}">{{ number_format($todaySent, 0, ',', '.') }}</span>
                            <span class="text-mono-500"> / {{ number_format($dailyLimit, 0, ',', '.') }}</span>
                        </span>
                    </div>
                    <div class="h-2.5 bg-mono-800 rounded-full overflow-hidden border border-mono-700">
                        <div class="h-full rounded-full transition-all duration-500 {{ $msgColor }}"
                             style="width: {{ $msgPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-1.5">
                        <span class="text-[10px] text-mono-500 font-medium">Reset setiap pukul 00:00 WIB</span>
                        <span class="text-[10px] font-bold {{ $remainingDaily <= 0 ? 'text-mono-200' : 'text-mono-400' }}">
                            {{ $remainingDaily <= 0 ? '⚠ Kuota Habis' : number_format($remainingDaily, 0, ',', '.') . ' pesan tersisa' }}
                        </span>
                    </div>
                </div>

                <!-- Device Slots -->
                @php
                    $devPct = $maxDevices > 0 ? min(100, round(($devicesCount / $maxDevices) * 100)) : 0;
                    $devColor = $devPct >= 100 ? 'bg-mono-100' : ($devPct >= 80 ? 'bg-mono-200' : 'bg-mono-400');
                @endphp
                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-mono-200 flex items-center gap-2">
                            <span>📱</span> Slot Perangkat WhatsApp
                        </span>
                        <span class="text-xs font-mono font-bold text-mono-300">
                            <span class="{{ $devPct >= 100 ? 'text-mono-50' : 'text-mono-300' }}">{{ $devicesCount }}</span>
                            <span class="text-mono-500"> / {{ $maxDevices }}</span>
                        </span>
                    </div>
                    <div class="h-2.5 bg-mono-800 rounded-full overflow-hidden border border-mono-700">
                        <div class="h-full rounded-full transition-all duration-500 {{ $devColor }}"
                             style="width: {{ $devPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-1.5">
                        <span class="text-[10px] text-mono-500 font-medium">Nomor WhatsApp yang terdaftar</span>
                        <span class="text-[10px] font-bold {{ $devicesCount >= $maxDevices ? 'text-mono-200' : 'text-mono-400' }}">
                            {{ $devicesCount >= $maxDevices ? '⚠ Slot Penuh' : ($maxDevices - $devicesCount) . ' slot tersisa' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Upgrade CTA if quota is near max -->
            @if($msgPct >= 80 || $devPct >= 100)
                <div class="mt-2 p-3.5 bg-mono-950 border border-mono-700 rounded-2xl flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold text-mono-200">💡 Kuota Anda hampir habis</p>
                        <p class="text-[10px] text-mono-400 mt-0.5">Tingkatkan kuota pesan dan slot perangkat WhatsApp sekarang.</p>
                    </div>
                    <a href="{{ route('user.upgrade') }}" class="px-4 py-2 mono-btn-secondary text-[11px] font-bold whitespace-nowrap shadow-sm">
                        Upgrade Paket →
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex gap-2 p-1.5 bg-mono-900 border border-mono-700 rounded-2xl max-w-fit flex-wrap shadow-md">
        <button @click="activeTab = 'send'" :class="activeTab === 'send' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white hover:bg-mono-800'" class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>💬</span> Kirim Pesan
        </button>
        <button @click="activeTab = 'devices'" :class="activeTab === 'devices' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white hover:bg-mono-800'" class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>📱</span> Perangkat Saya
        </button>
        <button @click="activeTab = 'api'" :class="activeTab === 'api' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white hover:bg-mono-800'" class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>🔑</span> API Key & SDK
        </button>
        <button @click="activeTab = 'history'" :class="activeTab === 'history' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white hover:bg-mono-800'" class="px-5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2">
            <span>📜</span> Riwayat Pesan
        </button>
    </div>

    <!-- TAB 1: KIRIM PESAN -->
    <div x-show="activeTab === 'send'" class="space-y-6" x-transition.opacity>
        <div class="mono-card p-8 max-w-3xl">
            <h3 class="text-base font-extrabold text-mono-50 mb-1 flex items-center gap-2">
                <span>Form Pengiriman Pesan WhatsApp</span>
            </h3>
            <p class="text-xs text-mono-400 mb-6">Kirimkan pesan tunggal atau broadcast massal dengan proteksi Anti-Ban</p>

            <!-- Mode Selector -->
            <div class="flex gap-2 p-1 bg-mono-950 border border-mono-700 rounded-xl mb-6 max-w-xs">
                <button @click="sendMode = 'single'" :class="sendMode === 'single' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="flex-1 py-1.5 rounded-lg text-xs transition">Pesan Tunggal</button>
                <button @click="sendMode = 'bulk'" :class="sendMode === 'bulk' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="flex-1 py-1.5 rounded-lg text-xs transition">Massal (Bulk)</button>
            </div>

            <form @submit.prevent="submitMessage()" class="space-y-5 text-xs">
                <div>
                    <label class="block text-mono-300 font-bold mb-2">Pilih Perangkat WhatsApp Pengirim</label>
                    <select x-model="form.device_id" required class="w-full mono-input px-4 py-3 text-mono-100 font-medium">
                        <option value="">-- Pilih Perangkat WhatsApp --</option>
                        <template x-for="d in devices" :key="d.id">
                            <option :value="d.device_id" x-text="`${d.name} (${d.device_id}) [${d.status}]`"></option>
                        </template>
                    </select>
                </div>

                <div x-show="sendMode === 'single'">
                    <label class="block text-mono-300 font-bold mb-2">Nomor Tujuan WhatsApp</label>
                    <input type="text" x-model="form.to" placeholder="Contoh: 081234567890 atau 6281234567890" class="w-full mono-input px-4 py-3 font-mono">
                </div>

                <div x-show="sendMode === 'bulk'">
                    <label class="block text-mono-300 font-bold mb-2">Daftar Nomor Tujuan (Satu nomor per baris)</label>
                    <textarea x-model="form.recipients_raw" rows="3" placeholder="081234567890&#10;085123456789&#10;628987654321" class="w-full mono-input px-4 py-3 font-mono"></textarea>
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-2">Isi Pesan WhatsApp</label>
                    <textarea x-model="form.message" rows="4" required placeholder="Tuliskan isi pesan Anda di sini..." class="w-full mono-input px-4 py-3 font-medium"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-5 pt-1">
                    <div class="p-3.5 bg-mono-950 rounded-xl border border-mono-700">
                        <label class="block text-mono-400 mb-1.5 font-semibold">Min Delay: <span class="text-mono-100 font-mono font-bold" x-text="form.min_delay + ' ms'"></span></label>
                        <input type="range" min="500" max="5000" step="500" x-model="form.min_delay" class="w-full accent-white cursor-pointer">
                    </div>
                    <div class="p-3.5 bg-mono-950 rounded-xl border border-mono-700">
                        <label class="block text-mono-400 mb-1.5 font-semibold">Max Delay: <span class="text-mono-100 font-mono font-bold" x-text="form.max_delay + ' ms'"></span></label>
                        <input type="range" min="1000" max="8000" step="500" x-model="form.max_delay" class="w-full accent-white cursor-pointer">
                    </div>
                </div>

                <button type="submit" :disabled="sending || isSuspended"
                        :class="isSuspended ? 'bg-mono-800 text-mono-500 border border-mono-700 cursor-not-allowed' : 'mono-btn-primary'"
                        class="w-full py-3.5 font-bold rounded-xl transition flex items-center justify-center gap-2 text-sm mt-2 shadow-md">
                    <span x-text="isSuspended ? '⛔ Akun Ditangguhkan (Tidak Dapat Mengirim Pesan)' : (sending ? 'Memproses Pengiriman...' : 'Kirim Pesan Sekarang')"></span>
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 2: PERANGKAT SAYA -->
    <div x-show="activeTab === 'devices'" class="space-y-6" x-transition.opacity>
        <div class="mono-card p-8">
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div>
                    <h3 class="text-base font-extrabold text-mono-50">Perangkat WhatsApp Terdaftar</h3>
                    <p class="text-xs text-mono-400 mt-0.5">Tautkan nomor WhatsApp Anda dengan scan QR Code untuk mulai mengirim pesan</p>
                </div>
                <button @click="showAddDeviceModal = true" class="px-4 py-2.5 mono-btn-primary text-xs font-bold flex items-center gap-2 shadow-sm">
                    <span>➕</span> Daftarkan Nomor Baru
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <template x-for="d in devices" :key="d.id">
                    <div class="bg-mono-950 border border-mono-700 p-5 rounded-2xl space-y-4 hover:border-mono-500 transition shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-mono-50 text-sm" x-text="d.name"></h4>
                                <p class="text-xs text-mono-400 font-mono mt-0.5" x-text="d.phone_number || '-'"></p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm"
                                  :class="d.status === 'connected' ? 'bg-mono-50 text-mono-950 font-extrabold' : (d.status === 'pending' ? 'bg-mono-800 text-mono-200 border border-mono-600' : 'bg-mono-900 text-mono-400 border border-mono-700')"
                                  x-text="d.status"></span>
                        </div>

                        <div class="pt-3 border-t border-mono-800 text-[11px] space-y-1 text-mono-400 font-mono">
                            <div>Device ID: <span class="text-mono-200 font-semibold" x-text="d.device_id"></span></div>
                            <div x-show="d.jid">JID: <span class="text-mono-200" x-text="d.jid"></span></div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button x-show="d.status !== 'connected'" @click="connectDevice(d)" class="flex-1 py-2.5 mono-btn-primary text-xs font-bold shadow-sm">
                                🔌 Scan QR
                            </button>
                            <button x-show="d.status === 'connected'" @click="disconnectDevice(d)" class="flex-1 py-2.5 bg-mono-800 hover:bg-mono-700 border border-mono-600 text-mono-200 rounded-xl text-xs font-bold transition">
                                Putuskan
                            </button>
                            <button @click="deleteDevice(d.id)" class="px-3 py-2.5 bg-mono-850 hover:bg-mono-800 border border-mono-700 rounded-xl text-xs text-mono-300 transition">
                                🗑️
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- TAB 3: API KEY & DOKUMENTASI -->
    <div x-show="activeTab === 'api'" class="space-y-6" x-transition.opacity>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- API Keys Management -->
            <div class="mono-card p-7 space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-mono-50">API Keys Akun Anda</h3>
                        <p class="text-xs text-mono-400 mt-0.5">Gunakan key ini pada header <code>X-API-Key</code> aplikasi Anda</p>
                    </div>
                    <button @click="createApiKey()" class="px-4 py-2 mono-btn-primary text-xs font-bold shadow-sm">
                        ✨ Generate Key Baru
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="k in apiKeys" :key="k.id">
                        <div class="p-4 bg-mono-950 border border-mono-700 rounded-2xl space-y-2.5 shadow-sm">
                            <div class="flex justify-between items-center text-xs">
                                <span class="font-bold text-mono-100" x-text="k.name"></span>
                                <button @click="revokeApiKey(k.id)" class="text-mono-400 hover:text-white font-semibold text-[11px] underline">Revoke</button>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" readonly :value="k.key" class="w-full mono-input px-3 py-2 text-xs font-mono text-mono-200 select-all">
                                <button @click="copyText(k.key)" class="px-3.5 py-2 mono-btn-secondary text-xs font-semibold">Copy</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Integration Banner Card -->
            <div class="mono-card p-7 flex flex-col justify-between space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-mono-800 text-mono-200 border border-mono-700 text-[11px] font-bold uppercase mb-3">
                        📖 Developer SDK & Docs
                    </div>
                    <h3 class="text-lg font-extrabold text-mono-50">Dokumentasi REST API Lengkap</h3>
                    <p class="text-xs text-mono-400 mt-2 leading-relaxed font-medium">
                        Pelajari panduan integrasi instan untuk <strong>Laravel</strong>, <strong>Node.js</strong>, <strong>PHP</strong>, dan <strong>Python</strong> dengan payload JSON lengkap & proteksi Anti-Ban.
                    </p>
                </div>
                <a href="{{ route('docs') }}" class="px-5 py-3 mono-btn-primary rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-sm">
                    <span>Buka Dokumentasi REST API</span> →
                </a>
            </div>

        </div>
    </div>

    <!-- TAB 4: RIWAYAT PESAN SAYA -->
    <div x-show="activeTab === 'history'" class="space-y-6" x-transition.opacity>
        <div class="mono-card p-8">
            <h3 class="text-base font-bold text-mono-50 mb-4">Riwayat Pengiriman Pesan Saya</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-mono-400 border-b border-mono-700 pb-3 font-mono">
                        <tr>
                            <th class="pb-3.5 font-bold uppercase tracking-wider">TUJUAN</th>
                            <th class="pb-3.5 font-bold uppercase tracking-wider">ISI PESAN</th>
                            <th class="pb-3.5 font-bold uppercase tracking-wider">DEVICE</th>
                            <th class="pb-3.5 font-bold uppercase tracking-wider">STATUS</th>
                            <th class="pb-3.5 font-bold uppercase tracking-wider">WAKTU</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-mono-800">
                        <template x-for="m in messages" :key="m.id">
                            <tr class="hover:bg-mono-900 transition">
                                <td class="py-3.5 font-mono font-bold text-mono-100" x-text="m.to"></td>
                                <td class="py-3.5 max-w-xs truncate text-mono-300" x-text="m.message"></td>
                                <td class="py-3.5 font-mono text-[11px] text-mono-400" x-text="m.device_id"></td>
                                <td class="py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase"
                                          :class="m.status === 'sent' ? 'bg-mono-50 text-mono-950 font-extrabold' : (m.status === 'pending' ? 'bg-mono-800 text-mono-200 border border-mono-700' : 'bg-mono-900 text-mono-400 border border-mono-800')"
                                          x-text="m.status"></span>
                                </td>
                                <td class="py-3.5 text-mono-400 font-mono text-[11px]" x-text="new Date(m.created_at).toLocaleTimeString('id-ID')"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Device -->
    <div x-show="showAddDeviceModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-7 max-w-md w-full shadow-2xl" @click.outside="showAddDeviceModal = false">
            <h3 class="text-base font-extrabold text-mono-50 mb-4">➕ Daftarkan Nomor WhatsApp Baru</h3>
            <form @submit.prevent="saveDevice()" class="space-y-4 text-xs">
                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Nama Device / Label</label>
                    <input type="text" x-model="newDevice.name" required placeholder="Contoh: CS WhatsApp Utama" class="w-full mono-input px-4 py-2.5 font-medium">
                </div>
                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Nomor Telepon</label>
                    <input type="text" x-model="newDevice.phone_number" required placeholder="Contoh: 6281234567890" class="w-full mono-input px-4 py-2.5 font-mono">
                </div>
                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showAddDeviceModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm">Daftarkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal QR Code Scanner -->
    <div x-show="showQrModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-7 max-w-sm w-full text-center shadow-2xl" @click.outside="showQrModal = false">
            <h3 class="text-base font-extrabold text-mono-50 mb-2">📷 Scan QR Code WhatsApp</h3>
            <p class="text-xs text-mono-400 mb-5 font-medium" x-text="qrMessage"></p>
            <div id="qrcode-container" class="bg-white p-4 rounded-2xl flex items-center justify-center max-w-[220px] mx-auto min-h-[220px] shadow-lg"></div>
            <button @click="showQrModal = false" class="mt-6 w-full py-2.5 mono-btn-secondary text-xs font-bold">Tutup</button>
        </div>
    </div>

</div>

<script>
    function userApp() {
        return {
            activeTab: 'send',
            sendMode: 'single',
            sending: false,
            isSuspended: {{ auth()->user()->isSuspended() ? 'true' : 'false' }},
            showAddDeviceModal: false,
            showQrModal: false,
            qrMessage: 'Menyiapkan QR Code...',
            connectingDeviceId: null,
            stats: { total_devices: 0, connected_dev: 0, total_messages: 0, sent_messages: 0, pending_messages: 0, total_api_keys: 0 },
            devices: [],
            messages: [],
            apiKeys: [],
            form: { device_id: '', to: '', recipients_raw: '', message: '', min_delay: 1000, max_delay: 3000 },
            newDevice: { name: '', phone_number: '' },

            async initApp() {
                this.loadStats();
                this.loadDevices();
                this.loadMessages();
                this.loadApiKeys();

                setInterval(() => {
                    this.loadDevices();
                    this.loadStats();
                }, 3000);
            },

            async loadStats() {
                try {
                    let res = await fetch('/user/api/stats');
                    let json = await res.json();
                    if (json.success) this.stats = json.data;
                } catch(e) {}
            },

            async loadDevices() {
                try {
                    let res = await fetch('/api/devices');
                    let json = await res.json();
                    if (json.success) {
                        this.devices = json.data;
                        if (this.devices.length > 0 && !this.form.device_id) {
                            this.form.device_id = this.devices[0].device_id;
                        }
                    }
                } catch(e) {}
            },

            getCsrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            },

            async loadMessages() {
                try {
                    let res = await fetch('/api/messages', {
                        headers: { 'Accept': 'application/json' }
                    });
                    let json = await res.json();
                    if (json.success) this.messages = json.data.data || json.data;
                } catch(e) {}
            },

            async loadApiKeys() {
                try {
                    let res = await fetch('/api/api-keys', {
                        headers: { 'Accept': 'application/json' }
                    });
                    let json = await res.json();
                    if (json.success) this.apiKeys = json.data;
                } catch(e) {}
            },

            async submitMessage() {
                if (!this.form.device_id) {
                    AppSwal.warning('Pilih Perangkat', 'Silakan pilih perangkat WhatsApp pengirim terlebih dahulu.');
                    return;
                }
                this.sending = true;
                AppSwal.loading('Memproses Pengiriman...', 'Mendaftarkan pesan ke antrean delivery queue...');

                try {
                    let url = this.sendMode === 'single' ? '/api/messages/send' : '/api/messages/send-bulk';
                    let payload = this.sendMode === 'single'
                        ? { device_id: this.form.device_id, to: this.form.to, message: this.form.message, min_delay: parseInt(this.form.min_delay), max_delay: parseInt(this.form.max_delay) }
                        : { device_id: this.form.device_id, recipients: this.form.recipients_raw.split('\n').map(s=>s.trim()).filter(Boolean), message: this.form.message, min_delay: parseInt(this.form.min_delay), max_delay: parseInt(this.form.max_delay) };

                    let res = await fetch(url, {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify(payload)
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('Pesan berhasil masuk antrean pengiriman!');
                        this.form.message = '';
                        this.form.to = '';
                        this.form.recipients_raw = '';
                        this.loadMessages();
                        this.loadStats();
                    } else {
                        AppSwal.error('Gagal Mengirim', json.message || 'Terjadi kesalahan saat memproses.');
                    }
                } catch(e) {
                    AppSwal.close();
                    AppSwal.error('Kesalahan Jaringan', 'Gagal terhubung ke server gateway.');
                } finally {
                    this.sending = false;
                }
            },

            async saveDevice() {
                if (!this.newDevice.name) {
                    AppSwal.warning('Input Kurang', 'Nama perangkat wajib diisi.');
                    return;
                }
                AppSwal.loading('Mendaftarkan Perangkat...', 'Menghubungkan device ke sistem...');

                try {
                    let res = await fetch('/api/devices', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify(this.newDevice)
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        this.showAddDeviceModal = false;
                        this.newDevice = { name: '', phone_number: '' };
                        AppSwal.toastSuccess('Perangkat WhatsApp berhasil ditambahkan!');
                        this.loadDevices();
                        this.loadStats();
                    } else {
                        AppSwal.error('Gagal', json.message || 'Gagal menambahkan perangkat.');
                    }
                } catch(e) {
                    AppSwal.close();
                    AppSwal.error('Error', 'Terjadi kesalahan jaringan.');
                }
            },

            async connectDevice(device) {
                this.connectingDeviceId = device.id;
                this.showQrModal = true;
                this.qrMessage = 'Menyiapkan QR Code WhatsApp...';
                this.$nextTick(() => {
                    let el = document.getElementById('qrcode-container');
                    if (el) el.innerHTML = '';
                });

                try {
                    let res = await fetch(`/api/devices/${device.id}/connect`, { 
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        }
                    });
                    let json = await res.json();
                    if (json.success) {
                        this.qrMessage = json.message || 'Buka WhatsApp di HP > Perangkat Tertaut > Scan QR Code:';
                        if (json.data && json.data.qr_code) {
                            this.$nextTick(() => {
                                let el = document.getElementById('qrcode-container');
                                if (el) {
                                    el.innerHTML = '';
                                    new QRCode(el, { text: json.data.qr_code, width: 200, height: 200 });
                                }
                            });
                        }
                        this.loadDevices();
                    } else {
                        this.qrMessage = json.message || 'Gagal memuat QR Code. Pastikan service WhatsApp aktif.';
                    }
                } catch(e) {
                    this.qrMessage = 'Terjadi gangguan koneksi ke server.';
                }
            },

            async disconnectDevice(device) {
                const confirmed = await AppSwal.confirm(
                    `Putuskan Sesi WhatsApp?`,
                    `Perangkat ${device.name} (${device.phone_number || device.device_id}) akan logout dari WhatsApp.`,
                    'Ya, Putuskan Sesi',
                    true
                );
                if (!confirmed) return;

                AppSwal.loading('Memutuskan Sesi...', 'Mengirim sinyal logout ke WhatsApp engine...');
                try {
                    let res = await fetch(`/api/devices/${device.id}/disconnect`, { 
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        }
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('Sesi WhatsApp berhasil diputuskan.');
                    } else {
                        AppSwal.toastError(json.message || 'Gagal memutuskan sesi.');
                    }
                    this.loadDevices();
                    this.loadStats();
                } catch(e) {
                    AppSwal.close();
                    AppSwal.toastError('Gagal menghubungi server.');
                }
            },

            async deleteDevice(id) {
                const confirmed = await AppSwal.confirm(
                    'Hapus Perangkat?',
                    'Perangkat ini akan dihapus dari akun Anda secara permanen.',
                    'Ya, Hapus',
                    true
                );
                if (!confirmed) return;

                AppSwal.loading('Menghapus Perangkat...', 'Mohon tunggu sebentar...');
                try {
                    let res = await fetch(`/api/devices/${id}`, { 
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        }
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('Perangkat berhasil dihapus.');
                    } else {
                        AppSwal.toastError(json.message || 'Gagal menghapus perangkat.');
                    }
                    this.loadDevices();
                    this.loadStats();
                } catch(e) {
                    AppSwal.close();
                    AppSwal.toastError('Gagal menghubungi server.');
                }
            },

            async createApiKey() {
                const { value: keyName } = await CustomSwal.fire({
                    title: 'Generate API Key Baru',
                    text: 'Beri label/nama untuk API Key ini (misal: "Aplikasi Toko Online"):',
                    input: 'text',
                    inputPlaceholder: 'Contoh: Laravel Backend Server',
                    showCancelButton: true,
                    confirmButtonText: 'Generate Key',
                    cancelButtonText: 'Batal',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Nama API Key tidak boleh kosong!';
                        }
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
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        },
                        body: JSON.stringify({ name: keyName })
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('API Key baru berhasil digenerate!');
                        this.loadApiKeys();
                        this.loadStats();
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
                            'X-CSRF-TOKEN': this.getCsrfToken()
                        }
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('API Key berhasil dinonaktifkan / direvoke.');
                    } else {
                        AppSwal.toastError(json.message || 'Gagal merevoke key.');
                    }
                    this.loadApiKeys();
                    this.loadStats();
                } catch(e) {
                    AppSwal.close();
                    AppSwal.toastError('Gagal menghubungi server.');
                }
            },

            copyText(text) {
                navigator.clipboard.writeText(text);
                AppSwal.toastSuccess('API Key disalin ke clipboard!');
            }
        }
    }
</script>
@endsection
