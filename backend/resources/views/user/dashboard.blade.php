@extends('layouts.app')

@section('title', 'Dashboard Overview — JalurGaib WA Gateway')
@section('header_title', 'Dashboard Overview')
@section('header_subtitle', 'Pusat Telemetri WhatsApp, Kuota & Ringkasan Akun Tenant')

@section('content')
<div x-data="userDashboardApp()" x-init="initApp()" class="space-y-8 font-sans">

    <!-- SUSPENSION ANNOUNCEMENT BANNER -->
    @if(auth()->user()->isSuspended())
        <div class="p-6 bg-mono-900 border-2 border-mono-500 rounded-2xl text-mono-200 shadow-2xl space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-mono-50 text-mono-950 flex items-center justify-center text-xl font-bold shadow-md">
                    <i class="fa-solid fa-ban text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-mono-50 tracking-tight">Akun Anda Sedang Ditangguhkan (Suspended)</h3>
                    <p class="text-xs text-mono-400">Seluruh layanan JalurGaib WA Gateway, pengiriman pesan, dan akses REST API dinonaktifkan sementara.</p>
                </div>
            </div>
            @if(auth()->user()->suspend_reason)
                <div class="p-4 bg-mono-950 rounded-xl border border-mono-700 text-xs">
                    <span class="font-bold text-mono-300 block mb-1">Catatan dari Administrator:</span>
                    <p class="text-mono-100 leading-relaxed font-mono">"{{ auth()->user()->suspend_reason }}"</p>
                </div>
            @endif
        </div>
    @endif

    <!-- 1. BENTO TELEMETRY KPI CARDS (4-Column Uniform Bento) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Connected Devices -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-mono-400 uppercase tracking-wider font-mono">Perangkat WA</span>
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-lg"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50 tabular-nums">
                <span x-text="stats.connected_dev">0</span>
                <span class="text-sm font-semibold text-mono-400">/ <span x-text="stats.total_devices">0</span> Aktif</span>
            </div>
            <div class="text-xs text-mono-400 mt-2 font-medium">Perangkat WhatsApp terhubung</div>
        </div>

        <!-- Sent Messages -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-mono-400 uppercase tracking-wider font-mono">Pesan Terkirim</span>
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-check-double text-mono-200"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50 tabular-nums" x-text="stats.sent_messages">0</div>
            <div class="text-xs text-mono-300 font-semibold mt-2">Berhasil di-deliver ke nomor tujuan</div>
        </div>

        <!-- Pending Messages in Queue -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-mono-400 uppercase tracking-wider font-mono">Antrean Queue</span>
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-hourglass-half text-amber-400"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-mono-200 tabular-nums" x-text="stats.pending_messages">0</div>
            <div class="text-xs text-mono-400 mt-2 font-medium">Diproses dengan proteksi delay</div>
        </div>

        <!-- Active API Keys -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[11px] font-bold text-mono-400 uppercase tracking-wider font-mono">API Keys</span>
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">
                    <i class="fa-solid fa-key text-mono-200"></i>
                </div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50 tabular-nums" x-text="stats.total_api_keys">0</div>
            <div class="text-xs text-mono-400 mt-2 font-medium">Token aktif integrasi REST API</div>
        </div>
    </div>

    <!-- 2. PACKAGE TIER & KUOTA SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- Active Package Card -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Paket Aktif Anda</div>
                        <div class="text-lg font-extrabold text-mono-50 mt-0.5">{{ $package->name }}</div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full text-[10px] font-bold bg-mono-50 text-mono-950 shadow-sm font-extrabold inline-flex items-center gap-1">
                        <i class="fa-solid fa-crown text-[10px] text-amber-500"></i>
                        <span>{{ $package->badge ?? ($package->price == 0 ? 'Gratis' : 'Aktif') }}</span>
                    </div>
                </div>
                @if($package->description)
                    <p class="text-xs text-mono-400 mb-4 font-medium leading-relaxed">{{ $package->description }}</p>
                @endif
                @if(!empty($package->benefits) && is_array($package->benefits))
                    <ul class="space-y-2 mb-4">
                        @foreach($package->benefits as $benefit)
                            <li class="text-xs text-mono-300 flex items-start gap-2">
                                <i class="fa-solid fa-check text-mono-100 text-[11px] mt-0.5 flex-shrink-0"></i>
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
                <a href="{{ route('user.upgrade') }}" class="inline-flex items-center gap-1.5 text-[11px] font-extrabold text-mono-200 hover:text-white transition">
                    <span>Upgrade Paket</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Quota Progress Bars -->
        <div class="lg:col-span-2 mono-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-5">
                    <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Kuota & Pemakaian Hari Ini</div>
                    <a href="{{ route('user.upgrade') }}" class="text-[11px] font-bold text-mono-300 hover:text-white flex items-center gap-1.5 transition">
                        <i class="fa-solid fa-crown text-amber-400 text-xs"></i>
                        <span>Lihat Semua Paket</span>
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
                            <i class="fa-solid fa-comments text-mono-400"></i>
                            <span>Kuota Pesan Harian</span>
                        </span>
                        <span class="text-xs font-mono font-bold text-mono-300 tabular-nums">
                            <span class="{{ $msgPct >= 90 ? 'text-mono-50' : 'text-mono-300' }}">{{ number_format($todaySent, 0, ',', '.') }}</span>
                            <span class="text-mono-500"> / {{ number_format($dailyLimit, 0, ',', '.') }}</span>
                        </span>
                    </div>
                    <div class="h-2.5 bg-mono-800 rounded-full overflow-hidden border border-mono-700">
                        <div class="h-full rounded-full transition-all duration-500 {{ $msgColor }}"
                             style="width: {{ $msgPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-[10px] text-mono-500 font-medium font-mono">Reset otomatis setiap pukul 00:00 WIB</span>
                        <span class="text-[10px] font-bold {{ $remainingDaily <= 0 ? 'text-red-400' : 'text-mono-400' }}">
                            {{ $remainingDaily <= 0 ? 'Kuota Habis' : number_format($remainingDaily, 0, ',', '.') . ' pesan tersisa' }}
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
                            <i class="fa-solid fa-mobile-screen-button text-mono-400"></i>
                            <span>Slot Perangkat WhatsApp</span>
                        </span>
                        <span class="text-xs font-mono font-bold text-mono-300 tabular-nums">
                            <span class="{{ $devPct >= 100 ? 'text-mono-50' : 'text-mono-300' }}">{{ $devicesCount }}</span>
                            <span class="text-mono-500"> / {{ $maxDevices }}</span>
                        </span>
                    </div>
                    <div class="h-2.5 bg-mono-800 rounded-full overflow-hidden border border-mono-700">
                        <div class="h-full rounded-full transition-all duration-500 {{ $devColor }}"
                             style="width: {{ $devPct }}%"></div>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-[10px] text-mono-500 font-medium">Nomor WhatsApp yang terdaftar</span>
                        <span class="text-[10px] font-bold {{ $devicesCount >= $maxDevices ? 'text-red-400' : 'text-mono-400' }}">
                            {{ $devicesCount >= $maxDevices ? 'Slot Penuh' : ($maxDevices - $devicesCount) . ' slot tersisa' }}
                        </span>
                    </div>
                </div>
            </div>

            @if($msgPct >= 80 || $devPct >= 100)
                <div class="mt-2 p-3.5 bg-mono-950 border border-mono-700 rounded-xl flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold text-mono-200 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400 text-xs"></i>
                            <span>Kuota Anda hampir mencapai batas</span>
                        </p>
                        <p class="text-[10px] text-mono-400 mt-0.5">Tingkatkan kuota pesan dan slot perangkat WhatsApp sekarang.</p>
                    </div>
                    <a href="{{ route('user.upgrade') }}" class="px-3.5 py-1.5 mono-btn-primary text-[11px] font-bold whitespace-nowrap shadow-sm inline-flex items-center gap-1.5">
                        <span>Upgrade</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- 3. QUICK ACTION BENTO GRID -->
    <div>
        <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest mb-3">Aksi Cepat Menu WhatsApp</div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Kirim Pesan Shortcut -->
            <a href="{{ route('user.messages') }}" class="mono-card p-5 hover:border-mono-500 transition group flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-200 flex items-center justify-center text-base group-hover:scale-105 transition flex-shrink-0">
                    <i class="fa-solid fa-paper-plane text-mono-100"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-mono-100 group-hover:text-white transition flex items-center gap-1">
                        <span>Kirim Pesan</span>
                        <i class="fa-solid fa-arrow-right text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </h4>
                    <p class="text-[11px] text-mono-400 mt-1 leading-relaxed">Kirimkan pesan tunggal atau broadcast massal via WhatsApp.</p>
                </div>
            </a>

            <!-- Perangkat Shortcut -->
            <a href="{{ route('user.devices') }}" class="mono-card p-5 hover:border-mono-500 transition group flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-200 flex items-center justify-center text-base group-hover:scale-105 transition flex-shrink-0">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-lg"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-mono-100 group-hover:text-white transition flex items-center gap-1">
                        <span>Perangkat WhatsApp</span>
                        <i class="fa-solid fa-arrow-right text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </h4>
                    <p class="text-[11px] text-mono-400 mt-1 leading-relaxed">Scan QR code, hubungkan nomor baru & cek status koneksi.</p>
                </div>
            </a>

            <!-- Riwayat Shortcut -->
            <a href="{{ route('user.history') }}" class="mono-card p-5 hover:border-mono-500 transition group flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-200 flex items-center justify-center text-base group-hover:scale-105 transition flex-shrink-0">
                    <i class="fa-solid fa-clock-rotate-left text-mono-100"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-mono-100 group-hover:text-white transition flex items-center gap-1">
                        <span>Riwayat Pesan</span>
                        <i class="fa-solid fa-arrow-right text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </h4>
                    <p class="text-[11px] text-mono-400 mt-1 leading-relaxed">Cek log pengiriman pesan terkirim, pending, atau gagal.</p>
                </div>
            </a>

            <!-- API Keys Shortcut -->
            <a href="{{ route('user.api-keys') }}" class="mono-card p-5 hover:border-mono-500 transition group flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-200 flex items-center justify-center text-base group-hover:scale-105 transition flex-shrink-0">
                    <i class="fa-solid fa-key text-mono-100"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-mono-100 group-hover:text-white transition flex items-center gap-1">
                        <span>API Key & SDK</span>
                        <i class="fa-solid fa-arrow-right text-[10px] opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </h4>
                    <p class="text-[11px] text-mono-400 mt-1 leading-relaxed">Generate token REST API & integrasikan dengan website Anda.</p>
                </div>
            </a>
        </div>
    </div>

    <!-- 4. SIDE-BY-SIDE BENTO PANELS (Recent Messages & Connected Devices) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Recent Messages Stream -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane text-mono-400 text-xs"></i>
                        <h3 class="text-xs font-bold text-mono-100 font-mono uppercase tracking-wider">Aktivitas Pesan Terakhir</h3>
                    </div>
                    <a href="{{ route('user.history') }}" class="text-[11px] font-bold text-mono-400 hover:text-white transition inline-flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-mono-800/60">
                    @forelse($recentMessages as $msg)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-mono-800 border border-mono-700 flex items-center justify-center text-mono-300 flex-shrink-0">
                                    <i class="fa-regular fa-comment-dots text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-mono font-bold text-mono-100 truncate">{{ $msg->to }}</div>
                                    <div class="text-[11px] text-mono-400 truncate max-w-[130px] sm:max-w-xs">{{ Str::limit($msg->message, 45) }}</div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 ml-3">
                                @if($msg->status === 'sent')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-check text-[9px]"></i> Terkirim
                                    </span>
                                @elseif(in_array($msg->status, ['pending', 'processing']))
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-hourglass-half text-[9px]"></i> Antrean
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-xmark text-[9px]"></i> Gagal
                                    </span>
                                @endif
                                <div class="text-[10px] text-mono-500 font-mono mt-0.5 tabular-nums">
                                    {{ $msg->created_at->timezone('Asia/Jakarta')->format('d/m H:i') }} WIB
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-mono-500 text-xs">
                            <i class="fa-regular fa-message text-2xl mb-2 block text-mono-600"></i>
                            <span>Belum ada pesan yang dikirimkan.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-mono-800 mt-4">
                <a href="{{ route('user.messages') }}" class="w-full py-2.5 px-4 rounded-xl mono-btn-primary font-bold text-xs text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                    <span>Kirim Pesan Sekarang</span>
                </a>
            </div>
        </div>

        <!-- Right: Connected Devices -->
        <div class="mono-card p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-400 text-sm"></i>
                        <h3 class="text-xs font-bold text-mono-100 font-mono uppercase tracking-wider">Perangkat WhatsApp Aktif</h3>
                    </div>
                    <a href="{{ route('user.devices') }}" class="text-[11px] font-bold text-mono-400 hover:text-white transition inline-flex items-center gap-1">
                        <span>Kelola Perangkat</span>
                        <i class="fa-solid fa-arrow-right text-[9px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-mono-800/60">
                    @forelse($devices as $dev)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-mono-800 border border-mono-700 flex items-center justify-center text-emerald-400 flex-shrink-0">
                                    <i class="fa-solid fa-mobile-screen-button"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-mono-100 truncate max-w-[130px] sm:max-w-xs">{{ $dev->name }}</div>
                                    <div class="text-[11px] text-mono-400 font-mono truncate max-w-[130px] sm:max-w-xs">{{ $dev->phone_number ?? 'Belum ada nomor' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($dev->status === 'connected')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle text-[7px] text-emerald-400 animate-pulse"></i> Terhubung
                                    </span>
                                @elseif($dev->status === 'scan_qr')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-qrcode text-[9px]"></i> Perlu Scan
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-mono-800 text-mono-400 border border-mono-700 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-power-off text-[9px]"></i> Terputus
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-mono-500 text-xs">
                            <i class="fa-solid fa-mobile-screen-button text-2xl mb-2 block text-mono-600"></i>
                            <span>Belum ada perangkat yang ditambahkan.</span>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-4 border-t border-mono-800 mt-4">
                <a href="{{ route('user.devices') }}" class="w-full py-2.5 px-4 rounded-xl mono-btn-secondary font-bold text-xs text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah & Hubungkan Perangkat WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function userDashboardApp() {
        return {
            stats: {
                total_devices: {{ $devicesCount }},
                connected_dev: {{ $devices->where('status', 'connected')->count() }},
                sent_messages: {{ $todaySent }},
                pending_messages: 0,
                total_api_keys: {{ $user->apiKeys()->count() }},
            },
            async initApp() {
                try {
                    let res = await fetch("{{ route('user.stats') }}");
                    let json = await res.json();
                    if (json.success && json.data) {
                        this.stats = json.data;
                    }
                } catch(e) {
                    console.error("Gagal load stats", e);
                }
            }
        }
    }
</script>
@endsection
