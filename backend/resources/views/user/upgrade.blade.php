@extends('layouts.app')

@section('title', 'Upgrade & Kelola Paket — JalurGaib WA Gateway')
@section('header_title', 'Upgrade & Kelola Paket Langganan')
@section('header_subtitle', 'Tingkatkan kuota pesan harian & slot perangkat WhatsApp sesuai kebutuhan bisnis Anda')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-500/50 text-emerald-200 text-xs flex items-center gap-3 shadow-lg">
            <span class="text-base flex-shrink-0">✓</span>
            <span class="font-medium leading-relaxed">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-red-950/80 border border-red-500/50 text-red-200 text-xs flex items-center gap-3 shadow-lg">
            <span class="text-base flex-shrink-0">✕</span>
            <span class="font-medium leading-relaxed">{{ session('error') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="p-4 rounded-2xl bg-amber-950/80 border border-amber-500/50 text-amber-200 text-xs flex items-center gap-3 shadow-lg">
            <span class="text-base flex-shrink-0">ℹ</span>
            <span class="font-medium leading-relaxed">{{ session('info') }}</span>
        </div>
    @endif

    <!-- CURRENT PLAN SUMMARY BANNER -->
    <div class="mono-card p-6 border-mono-600/80 relative overflow-hidden">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 relative z-10">
            <div class="flex items-start gap-4">
                <div class="plan-star-box w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center justify-center font-bold text-xl shadow-md flex-shrink-0">
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
                    @if($user->package_expires_at)
                        <div class="mt-2 text-[11px] font-mono flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full {{ $user->isPackageExpired() ? 'bg-red-500' : 'bg-emerald-400' }}"></span>
                            <span class="{{ $user->isPackageExpired() ? 'text-red-400 font-bold' : 'text-mono-300' }}">
                                {{ $user->isPackageExpired() ? 'Paket Berakhir pada:' : 'Masa Aktif hingga:' }}
                                {{ $user->package_expires_at->format('d M Y, H:i') }} WIB
                                ({{ $user->package_expires_at->diffForHumans() }})
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Current Quota Badges -->
            <div class="quota-summary-box flex items-center gap-3 p-3 rounded-2xl border border-mono-700/80 bg-mono-950/80">
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
                    $isSelected = (request('package_id') == $pkg->id && !$isCurrent);
                    $isComingSoon = ($pkg->status === 'coming_soon');
                @endphp
                <div class="mono-card p-6 flex flex-col justify-between relative transition-all duration-300
                    {{ $isCurrent ? 'border-mono-400 bg-mono-900/90 shadow-xl ring-1 ring-mono-400' : ($isSelected ? 'border-mono-200 ring-2 ring-mono-300 bg-mono-900/90 shadow-2xl' : 'hover:border-mono-600') }}
                    {{ $isComingSoon ? 'opacity-75' : '' }}">

                    <!-- Floating Badges -->
                    <div class="flex items-center justify-between gap-2 mb-4">
                        <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-full border
                            {{ $isCurrent ? 'bg-mono-50 text-mono-950 border-mono-50 font-extrabold' : ($isSelected ? 'bg-mono-50 text-mono-950 border-mono-50 font-extrabold shadow-sm' : 'bg-mono-950 text-mono-300 border-mono-700') }}">
                            {{ $isComingSoon ? 'COMING SOON' : ($pkg->badge ?? ($pkg->price == 0 ? 'Gratis' : 'Paket')) }}
                        </span>

                        @if($isCurrent)
                            <span class="text-[10px] font-mono font-extrabold text-mono-200 bg-mono-800 px-2 py-0.5 rounded-md border border-mono-700">
                                ✓ Sedang Digunakan
                            </span>
                        @elseif($isSelected)
                            <span class="text-[10px] font-mono font-extrabold text-mono-950 bg-mono-100 px-2.5 py-0.5 rounded-md shadow-sm">
                                ★ Pilihan Anda
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
                            <div class="w-full py-2.5 px-4 rounded-xl bg-mono-800 border border-mono-700 text-mono-200 font-bold text-xs text-center flex items-center justify-center gap-2 mb-2">
                                <span>✓</span>
                                <span>Paket Aktif Anda Saat Ini</span>
                            </div>
                            @if(!$pkg->isFree())
                                <form action="{{ route('user.checkout.store') }}" method="POST" @submit="submitWithLoading('Mempersiapkan pembayaran...')">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <button type="submit" class="w-full py-2 px-3 rounded-xl bg-mono-900 hover:bg-mono-800 border border-mono-700 text-mono-300 hover:text-white font-mono text-[11px] font-bold text-center transition flex items-center justify-center gap-1.5">
                                        <span>⚡</span>
                                        <span>Perpanjang Langganan (+30 Hari)</span>
                                    </button>
                                </form>
                            @endif
                        @elseif($isComingSoon)
                            <div class="w-full py-3 px-4 rounded-xl bg-mono-950 border border-mono-800 text-mono-500 font-bold text-xs text-center cursor-not-allowed">
                                ⏳ Segera Hadir
                            </div>
                        @else
                            @if($pkg->price > 0)
                                <form action="{{ route('user.checkout.store') }}" method="POST" @submit="submitWithLoading('Mempersiapkan checkout Kasera...')">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <button type="submit"
                                            class="w-full py-3 px-4 rounded-xl mono-btn-primary font-extrabold text-xs text-center flex items-center justify-center gap-2 shadow-md hover:scale-[1.02] transition">
                                        <span>⚡</span>
                                        <span>Bayar Otomatis (QRIS / VA) →</span>
                                    </button>
                                </form>
                                @php
                                    $waText = urlencode("Halo Admin WA Gateway, saya ingin konsultasi upgrade paket akun '" . auth()->user()->email . "' ke paket *" . $pkg->name . "* (Rp " . number_format($pkg->price, 0, ',', '.') . "/bln).");
                                @endphp
                                <div class="mt-2 text-center">
                                    <a href="https://wa.me/6281234567890?text={{ $waText }}"
                                       target="_blank"
                                       class="text-[11px] text-mono-400 hover:text-mono-200 transition">
                                        💬 Butuh bantuan CS? Chat via WA
                                    </a>
                                </div>
                            @else
                                <form action="{{ route('user.checkout.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}">
                                    <button type="submit"
                                            class="w-full py-3 px-4 rounded-xl bg-mono-800 hover:bg-mono-700 text-mono-100 font-bold text-xs text-center transition">
                                        Pilih Paket Gratis
                                    </button>
                                </form>
                            @endif
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

    <!-- BILLING & ORDER HISTORY TABLE -->
    @if(isset($recentOrders) && $recentOrders->isNotEmpty())
        <div class="mono-card p-6 border-mono-700/80">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">RIWAYAT TRANSAKSI</div>
                    <h3 class="text-base font-extrabold text-mono-50 mt-0.5">Riwayat Pembayaran & Invoice</h3>
                </div>
                <div class="text-xs text-mono-400 font-mono">
                    Verifikasi Otomatis via Kasera Pay
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-mono-800 text-[11px] font-mono text-mono-400 uppercase">
                            <th class="py-3 px-4">No. Order</th>
                            <th class="py-3 px-4">Paket</th>
                            <th class="py-3 px-4">Nominal</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Waktu & Batas Bayar</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-mono-800/60 font-mono">
                        @foreach($recentOrders as $ord)
                            @php
                                $createdAtWib = $ord->created_at->timezone('Asia/Jakarta');
                                $expiresMinutes = (int) config('services.kasera.expires_in_minutes', 60);
                                $expiresAtWib = (clone $createdAtWib)->addMinutes($expiresMinutes);
                                $nowWib = now()->timezone('Asia/Jakarta');
                                $isOverdue = $nowWib->greaterThan($expiresAtWib);
                            @endphp
                            <tr class="hover:bg-mono-900/40 transition">
                                <td class="py-3 px-4 font-bold text-mono-100">
                                    <span class="inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-receipt text-mono-500 text-[10px]"></i>
                                        <span>{{ $ord->order_no }}</span>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-mono-200 font-sans font-semibold">{{ $ord->package->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-mono-100 font-bold">Rp {{ number_format($ord->amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-mono-400 uppercase text-[11px]">{{ $ord->payment_method ?? ($ord->isPending() ? 'Kasera Pay' : '-') }}</td>
                                <td class="py-3 px-4">
                                    @if($ord->isPaid())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check text-[9px]"></i> Lunas
                                        </span>
                                    @elseif($ord->isPending())
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1">
                                            <i class="fa-regular fa-clock text-[9px]"></i> Menunggu
                                        </span>
                                    @elseif($ord->status === 'expired')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-500/20 text-zinc-400 border border-zinc-500/40 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-hourglass-end text-[9px]"></i> Kedaluwarsa
                                        </span>
                                    @elseif($ord->status === 'canceled')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-zinc-600/20 text-zinc-500 border border-zinc-600/40 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-ban text-[9px]"></i> Dibatalkan
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/40 inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-xmark text-[9px]"></i> {{ ucfirst($ord->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-[11px]">
                                    <div class="font-bold text-mono-100 flex items-center gap-1.5">
                                        <i class="fa-regular fa-calendar text-mono-400 text-[10px]"></i>
                                        <span>{{ $createdAtWib->format('d/m/Y H:i') }} WIB</span>
                                    </div>
                                    @if($ord->isPending())
                                        <div class="text-[10px] text-amber-400 font-medium mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-stopwatch text-[9px]"></i>
                                            <span>Batas: {{ $expiresAtWib->format('H:i') }} WIB</span>
                                            @if(!$isOverdue)
                                                <span class="text-mono-400 font-normal">({{ $nowWib->diffInMinutes($expiresAtWib, false) }} mnt tersisa)</span>
                                            @else
                                                <span class="text-red-400 font-bold">(Waktu Habis)</span>
                                            @endif
                                        </div>
                                    @elseif($ord->status === 'expired')
                                        <div class="text-[10px] text-mono-500 font-medium mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-hourglass-end text-[9px]"></i>
                                            <span>Batas s/d {{ $expiresAtWib->format('H:i') }} WIB</span>
                                        </div>
                                    @elseif($ord->isPaid())
                                        <div class="text-[10px] text-emerald-400 font-medium mt-1 flex items-center gap-1">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                            <span>Lunas {{ $ord->paid_at ? $ord->paid_at->timezone('Asia/Jakarta')->format('d/m H:i') : $ord->updated_at->timezone('Asia/Jakarta')->format('d/m H:i') }} WIB</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    @if($ord->isPending())
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($ord->checkout_url)
                                                <a href="{{ $ord->checkout_url }}" target="_blank" class="px-2.5 py-1 rounded-lg mono-btn-primary font-bold text-[11px] inline-flex items-center gap-1.5 shadow-sm" title="Buka tautan pembayaran Kasera">
                                                    <span>Bayar</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                                </a>
                                            @endif
                                            @if($ord->package_id)
                                                <form action="{{ route('user.checkout.store') }}" method="POST" class="inline" @submit="submitWithLoading('Mempersiapkan link baru...')">
                                                    @csrf
                                                    <input type="hidden" name="package_id" value="{{ $ord->package_id }}">
                                                    <button type="submit" class="px-2 py-1 rounded-lg bg-mono-800 hover:bg-mono-700 text-mono-200 hover:text-white text-[11px] font-bold border border-mono-700 transition inline-flex items-center gap-1" title="Jika link sebelumnya kedaluwarsa, klik di sini untuk membuat link pembayaran baru">
                                                        <i class="fa-solid fa-arrows-rotate text-[10px]"></i>
                                                        <span>Link Baru</span>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('user.checkout.cancel', $ord->order_no) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan pesanan ini?');">
                                                @csrf
                                                <button type="submit" class="px-1.5 py-1 rounded-lg text-mono-400 hover:text-red-400 hover:bg-red-500/10 text-[11px] transition" title="Batalkan Pesanan">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(in_array($ord->status, ['expired', 'canceled', 'failed']))
                                        @if($ord->package_id)
                                            <form action="{{ route('user.checkout.store') }}" method="POST" class="inline" @submit="submitWithLoading('Mempersiapkan checkout baru...')">
                                                @csrf
                                                <input type="hidden" name="package_id" value="{{ $ord->package_id }}">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-mono-800 hover:bg-mono-700 text-mono-200 hover:text-white text-[11px] font-bold border border-mono-700 transition inline-flex items-center gap-1.5">
                                                    <span>Pesan Ulang</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-mono-500 text-[11px]">-</span>
                                        @endif
                                    @elseif($ord->isPaid())
                                        <span class="text-emerald-400 font-semibold text-[11px] inline-flex items-center gap-1">
                                            <i class="fa-solid fa-circle-check"></i> Selesai
                                        </span>
                                    @else
                                        <span class="text-mono-500 text-[11px]">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

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
