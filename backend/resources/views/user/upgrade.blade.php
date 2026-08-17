@extends('layouts.app')

@section('title', 'Upgrade & Kelola Paket — JalurGaib WA Gateway')
@section('header_title', 'Upgrade & Kelola Paket Langganan')
@section('header_subtitle', 'Tingkatkan kuota pesan harian & slot perangkat WhatsApp sesuai kebutuhan bisnis Anda')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    <!-- CURRENT PLAN SUMMARY BANNER -->
    <div class="mono-card p-6 border-mono-600/80 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-mono-50 text-mono-950 flex items-center justify-center font-bold text-xl shadow-md flex-shrink-0">
                    ⭐
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Paket Aktif Akun Anda</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-mono-50 text-mono-950">
                            {{ $package->badge ?? ($package->price == 0 ? 'Gratis' : 'Aktif') }}
                        </span>
                    </div>
                    <h2 class="text-xl font-extrabold text-mono-50 mt-1">{{ $package->name }}</h2>
                    <p class="text-xs text-mono-400 mt-1 font-medium max-w-xl">
                        {{ $package->description ?? 'Layanan JalurGaib WA Gateway dengan slot perangkat dan limit kuota harian terdedikasi.' }}
                    </p>
                </div>
            </div>

            <!-- Current Quota Badges -->
            <div class="flex items-center gap-3 bg-mono-950/80 p-3 rounded-2xl border border-mono-700/80">
                <div class="text-center px-3 py-1">
                    <div class="text-mono-400 text-[10px] uppercase font-bold tracking-wider">Slot Device</div>
                    <div class="text-mono-50 font-extrabold text-base font-mono mt-0.5">{{ $devicesCount }} / {{ $maxDevices }}</div>
                </div>
                <div class="w-px h-8 bg-mono-700"></div>
                <div class="text-center px-3 py-1">
                    <div class="text-mono-400 text-[10px] uppercase font-bold tracking-wider">Pesan Hari Ini</div>
                    <div class="text-mono-50 font-extrabold text-base font-mono mt-0.5">{{ number_format($todaySent, 0, ',', '.') }} / {{ number_format($dailyLimit, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- PRICING & TIER GRID -->
    <div>
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-3">
            <div>
                <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">PILIHAN TIER & PAKET</div>
                <h3 class="text-lg font-extrabold text-mono-50 mt-0.5">Daftar Paket Kapasitas WhatsApp Gateway</h3>
            </div>
            <div class="text-xs text-mono-400 font-medium">
                💡 Semua paket berjalan di server cloud kami dengan proteksi Anti-Ban bawaan.
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($packages as $pkg)
                @php
                    $isCurrent = ($pkg->id === $package->id);
                    $isComingSoon = ($pkg->status === 'coming_soon');
                @endphp
                <div class="mono-card p-6 flex flex-col justify-between relative transition-all duration-300
                    {{ $isCurrent ? 'border-mono-400 bg-mono-900/90 shadow-xl ring-1 ring-mono-400' : 'hover:border-mono-600' }}
                    {{ $isComingSoon ? 'opacity-75' : '' }}">

                    <!-- Floating Badges -->
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full border
                            {{ $isCurrent ? 'bg-mono-50 text-mono-950 border-mono-50 font-extrabold' : 'bg-mono-950 text-mono-300 border-mono-700' }}">
                            {{ $isComingSoon ? 'COMING SOON' : ($pkg->badge ?? ($pkg->price == 0 ? 'Gratis' : 'Paket')) }}
                        </span>

                        @if($isCurrent)
                            <span class="text-[10px] font-mono font-extrabold text-mono-200 bg-mono-800 px-2 py-0.5 rounded-md border border-mono-700">
                                ✓ Sedang Digunakan
                            </span>
                        @endif
                    </div>

                    <!-- Package Info -->
                    <div class="mb-5">
                        <h4 class="text-lg font-extrabold text-mono-50 tracking-tight">{{ $pkg->name }}</h4>
                        @if($pkg->description)
                            <p class="text-xs text-mono-400 mt-1 leading-relaxed font-medium">{{ $pkg->description }}</p>
                        @endif

                        <!-- Price Tag -->
                        <div class="mt-4 pb-4 border-b border-mono-800 flex items-baseline gap-1.5">
                            @if($pkg->price == 0)
                                <span class="text-3xl font-extrabold font-mono text-mono-50">GRATIS</span>
                                <span class="text-xs text-mono-500 font-mono">/ selamanya</span>
                            @elseif($isComingSoon)
                                <span class="text-2xl font-extrabold font-mono text-mono-400">Segera</span>
                                <span class="text-xs text-mono-500 font-mono">/ bulan</span>
                            @else
                                <span class="text-2xl font-extrabold font-mono text-mono-50">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                <span class="text-xs text-mono-400 font-mono">/ bulan</span>
                            @endif
                        </div>
                    </div>

                    <!-- Capacity Metrics -->
                    <div class="grid grid-cols-2 gap-2 mb-5">
                        <div class="p-3 bg-mono-950 rounded-xl border border-mono-800 text-center">
                            <div class="text-xs font-mono font-extrabold text-mono-50">{{ $pkg->max_devices }} Perangkat</div>
                            <div class="text-[10px] text-mono-500 font-semibold uppercase mt-0.5">Slot WhatsApp</div>
                        </div>
                        <div class="p-3 bg-mono-950 rounded-xl border border-mono-800 text-center">
                            <div class="text-xs font-mono font-extrabold text-mono-50">{{ number_format($pkg->daily_message_limit, 0, ',', '.') }}</div>
                            <div class="text-[10px] text-mono-500 font-semibold uppercase mt-0.5">Pesan / Hari</div>
                        </div>
                    </div>

                    <!-- Benefits List -->
                    <div class="mb-6 flex-1">
                        <div class="text-[10px] font-mono font-bold text-mono-500 uppercase tracking-wider mb-2.5">Fitur & Manfaat:</div>
                        <ul class="space-y-2">
                            @if(!empty($pkg->benefits) && is_array($pkg->benefits))
                                @foreach($pkg->benefits as $benefit)
                                    <li class="text-xs text-mono-300 flex items-start gap-2 leading-relaxed">
                                        <span class="text-mono-100 font-bold flex-shrink-0">✓</span>
                                        <span>{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            @else
                                <li class="text-xs text-mono-300 flex items-start gap-2">
                                    <span class="text-mono-100 font-bold">✓</span>
                                    <span>Akses REST API & Webhook</span>
                                </li>
                                <li class="text-xs text-mono-300 flex items-start gap-2">
                                    <span class="text-mono-100 font-bold">✓</span>
                                    <span>Anti-Ban Humanized Delay</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Action Button -->
                    <div class="pt-4 border-t border-mono-800 mt-auto">
                        @if($isCurrent)
                            <div class="w-full py-3 px-4 rounded-xl bg-mono-800 border border-mono-700 text-mono-200 font-bold text-xs text-center flex items-center justify-center gap-2">
                                <span>✓</span>
                                <span>Paket Aktif Anda Saat Ini</span>
                            </div>
                        @elseif($isComingSoon)
                            <div class="w-full py-3 px-4 rounded-xl bg-mono-950 border border-mono-800 text-mono-500 font-bold text-xs text-center cursor-not-allowed">
                                ⏳ Segera Hadir
                            </div>
                        @else
                            @php
                                $waText = urlencode("Halo Admin WA Gateway, saya ingin upgrade paket akun '" . auth()->user()->email . "' ke paket *" . $pkg->name . "* (Rp " . number_format($pkg->price, 0, ',', '.') . "/bln). Mohon panduan pembayarannya.");
                            @endphp
                            <a href="https://wa.me/6281234567890?text={{ $waText }}"
                               target="_blank"
                               class="w-full py-3 px-4 rounded-xl mono-btn-primary font-bold text-xs text-center flex items-center justify-center gap-2 shadow-md hover:scale-[1.02] transition">
                                <span>💬</span>
                                <span>Hubungi CS untuk Upgrade →</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 mono-card p-10 text-center text-mono-400">
                    <p class="text-sm">Belum ada paket tambahan yang tersedia saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- CS CONTACT & HOW TO UPGRADE SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: How to Upgrade Steps -->
        <div class="lg:col-span-2 mono-card p-6 sm:p-8">
            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 rounded-xl bg-mono-800 text-mono-100 flex items-center justify-center text-lg font-bold">
                    🚀
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-mono-50">Langkah Mudah Upgrade Paket</h3>
                    <p class="text-xs text-mono-400">Proses aktivasi cepat tanpa perlu instalasi ulang atau re-scan QR WhatsApp</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                <div class="p-4 bg-mono-950 rounded-2xl border border-mono-800 space-y-2">
                    <div class="w-7 h-7 rounded-lg bg-mono-800 text-mono-100 font-mono font-bold text-xs flex items-center justify-center">
                        01
                    </div>
                    <h4 class="text-xs font-bold text-mono-100">Pilih Paket</h4>
                    <p class="text-[11px] text-mono-400 leading-relaxed">
                        Tentukan kapasitas slot device dan limit pesan harian yang Anda butuhkan.
                    </p>
                </div>

                <div class="p-4 bg-mono-950 rounded-2xl border border-mono-800 space-y-2">
                    <div class="w-7 h-7 rounded-lg bg-mono-800 text-mono-100 font-mono font-bold text-xs flex items-center justify-center">
                        02
                    </div>
                    <h4 class="text-xs font-bold text-mono-100">Hubungi Customer Service</h4>
                    <p class="text-[11px] text-mono-400 leading-relaxed">
                        Klik tombol chat WhatsApp CS kami untuk konfirmasi dan proses aktivasi.
                    </p>
                </div>

                <div class="p-4 bg-mono-950 rounded-2xl border border-mono-800 space-y-2">
                    <div class="w-7 h-7 rounded-lg bg-mono-800 text-mono-100 font-mono font-bold text-xs flex items-center justify-center">
                        03
                    </div>
                    <h4 class="text-xs font-bold text-mono-100">Langsung Aktif</h4>
                    <p class="text-[11px] text-mono-400 leading-relaxed">
                        Limit kuota dan slot perangkat akun Anda akan langsung ter-update otomatis.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right: CS Support Box -->
        <div class="mono-card p-6 sm:p-8 flex flex-col justify-between bg-mono-900 border-mono-700">
            <div>
                <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest mb-1">BANTUAN & CS</div>
                <h3 class="text-base font-extrabold text-mono-50">Butuh Paket Khusus / Custom?</h3>
                <p class="text-xs text-mono-300 mt-2 leading-relaxed">
                    Perusahaan Anda membutuhkan pengiriman ratusan ribu pesan atau dedicated worker cluster? Tim teknis kami siap membantu kebutuhan custom enterprise.
                </p>

                <div class="mt-5 space-y-2.5">
                    <div class="flex items-center gap-3 text-xs text-mono-200">
                        <span class="w-6 h-6 rounded-md bg-mono-950 flex items-center justify-center text-mono-400">📱</span>
                        <span>WhatsApp CS: <strong>+62 812-3456-7890</strong></span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-mono-200">
                        <span class="w-6 h-6 rounded-md bg-mono-950 flex items-center justify-center text-mono-400">✉</span>
                        <span>Email: <strong>support@gateway.local</strong></span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-mono-200">
                        <span class="w-6 h-6 rounded-md bg-mono-950 flex items-center justify-center text-mono-400">⚡</span>
                        <span>Jam Operasional: <strong>24/7 Priority Support</strong></span>
                    </div>
                </div>
            </div>

            <div class="pt-6 mt-6 border-t border-mono-800">
                <a href="https://wa.me/6281234567890?text={{ urlencode('Halo Admin JalurGaib WA Gateway, saya ingin konsultasi paket custom WhatsApp Gateway untuk akun: ' . auth()->user()->email) }}"
                   target="_blank"
                   class="w-full py-3 px-4 rounded-xl mono-btn-primary font-extrabold text-xs text-center flex items-center justify-center gap-2 shadow-lg">
                    <span>💬</span>
                    <span>Chat WhatsApp CS Sekarang</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
