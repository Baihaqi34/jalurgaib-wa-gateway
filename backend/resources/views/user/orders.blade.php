@extends('layouts.app')

@section('title', 'Riwayat Pembayaran & Invoice — JalurGaib WA Gateway')
@section('header_title', 'Riwayat Pembayaran & Invoice')
@section('header_subtitle', 'Pantau Status Transaksi, Batas Waktu Bayar & Tagihan')

@section('content')
<div class="space-y-6 font-sans max-w-7xl">

    <!-- KPI Telemetry Stats (4 Bento Cards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Orders -->
        <div class="mono-card p-4 sm:p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">TOTAL INVOICE</div>
                <div class="text-xl sm:text-2xl font-extrabold text-mono-50 font-mono mt-1 tabular-nums">{{ $stats['total'] }}</div>
                <div class="text-[11px] text-mono-400 mt-0.5">Keseluruhan pesanan</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-mono-850 border border-mono-700/80 flex items-center justify-center text-mono-300 flex-shrink-0">
                <i class="fa-solid fa-receipt text-sm"></i>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="mono-card p-4 sm:p-5 flex items-center justify-between border-amber-500/30">
            <div>
                <div class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-widest">MENUNGGU BAYAR</div>
                <div class="text-xl sm:text-2xl font-extrabold text-amber-300 font-mono mt-1 tabular-nums">{{ $stats['pending'] }}</div>
                <div class="text-[11px] text-mono-400 mt-0.5">Harus diselesaikan</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 flex-shrink-0">
                <i class="fa-regular fa-clock text-sm"></i>
            </div>
        </div>

        <!-- Paid Orders -->
        <div class="mono-card p-4 sm:p-5 flex items-center justify-between border-emerald-500/30">
            <div>
                <div class="text-[10px] font-mono font-bold text-emerald-400 uppercase tracking-widest">LUNAS BERHASIL</div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-300 font-mono mt-1 tabular-nums">{{ $stats['paid'] }}</div>
                <div class="text-[11px] text-mono-400 mt-0.5">Paket telah aktif</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 flex-shrink-0">
                <i class="fa-solid fa-circle-check text-sm"></i>
            </div>
        </div>

        <!-- Expired / Canceled -->
        <div class="mono-card p-4 sm:p-5 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">KEDALUWARSA / BATAL</div>
                <div class="text-xl sm:text-2xl font-extrabold text-mono-400 font-mono mt-1 tabular-nums">{{ $stats['expired'] }}</div>
                <div class="text-[11px] text-mono-500 mt-0.5">Waktu habis</div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-mono-850 border border-mono-700/80 flex items-center justify-center text-mono-400 flex-shrink-0">
                <i class="fa-solid fa-hourglass-end text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Action Control Bar -->
    <div class="mono-card p-4 sm:p-5">
        <form method="GET" action="{{ route('user.orders') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                <!-- Search by No. Order -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-mono-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Order (cth: JG-20261030...)" class="w-full mono-input pl-10 pr-4 py-2.5 text-xs text-mono-100 font-mono">
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-52">
                    <select name="status" onchange="this.form.submit()" class="w-full mono-input px-3.5 py-2.5 text-xs text-mono-100 font-medium">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas (Berhasil)</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Kedaluwarsa (Expired)</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2.5 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Filter</span>
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('user.orders') }}" class="px-3.5 py-2.5 rounded-xl bg-mono-800 hover:bg-mono-700 border border-mono-700 text-mono-300 text-xs font-semibold transition flex items-center justify-center" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </a>
                @endif

                <a href="{{ route('user.upgrade') }}" class="px-4 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 hover:text-white border border-amber-500/40 text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-crown text-xs"></i>
                    <span>Beli / Upgrade Paket</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="mono-card p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-mono-800 border border-mono-700 flex items-center justify-center text-mono-200">
                    <i class="fa-solid fa-file-invoice text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-mono-100">Daftar Tagihan & Transaksi</h3>
                    <p class="text-[11px] text-mono-400">Verifikasi otomatis via Payment Gateway Kasera Pay</p>
                </div>
            </div>
            <div class="text-xs text-mono-400 font-mono tabular-nums">
                Total: {{ $orders->total() }} Transaksi
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-mono-800 text-[11px] font-mono text-mono-400 uppercase tracking-wider">
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
                    @forelse($orders as $ord)
                        @php
                            $createdAtWib = $ord->created_at->timezone('Asia/Jakarta');
                            $expiresMinutes = (int) config('services.kasera.expires_in_minutes', 60);
                            $expiresAtWib = (clone $createdAtWib)->addMinutes($expiresMinutes);
                            $nowWib = now()->timezone('Asia/Jakarta');
                            $isOverdue = $nowWib->greaterThan($expiresAtWib);
                            $isStillPending = $ord->isPending() && !$isOverdue;
                        @endphp
                        <tr class="hover:bg-mono-900/40 transition">
                            <!-- No. Order -->
                            <td class="py-3.5 px-4 font-bold text-mono-100 whitespace-nowrap">
                                <span class="inline-flex items-center gap-2">
                                    <i class="fa-solid fa-receipt text-mono-500 text-xs"></i>
                                    <span class="text-xs">{{ $ord->order_no }}</span>
                                </span>
                            </td>

                            <!-- Paket -->
                            <td class="py-3.5 px-4 text-mono-200 font-sans font-semibold whitespace-nowrap">
                                {{ $ord->package->name ?? '-' }}
                            </td>

                            <!-- Nominal -->
                            <td class="py-3.5 px-4 text-mono-100 font-bold whitespace-nowrap tabular-nums">
                                Rp {{ number_format($ord->amount, 0, ',', '.') }}
                            </td>

                            <!-- Metode -->
                            <td class="py-3.5 px-4 text-mono-400 uppercase text-[11px] whitespace-nowrap">
                                {{ $ord->payment_method ?? ($ord->isPending() ? 'Kasera Pay' : '-') }}
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($ord->isPaid())
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Lunas
                                    </span>
                                @elseif($isStillPending)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1.5">
                                        <i class="fa-regular fa-clock text-[9px]"></i> Menunggu
                                    </span>
                                @elseif($ord->status === 'expired' || $isOverdue)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-500/20 text-zinc-400 border border-zinc-500/40 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-hourglass-end text-[9px]"></i> Kedaluwarsa
                                    </span>
                                @elseif($ord->status === 'canceled')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-zinc-600/20 text-zinc-500 border border-zinc-600/40 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-ban text-[9px]"></i> Dibatalkan
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/40 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> {{ ucfirst($ord->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Waktu & Batas Bayar (Langsung Jam Batas Selesai, Tanpa Countdown Menit) -->
                            <td class="py-3.5 px-4 text-[11px] whitespace-nowrap">
                                <div class="font-bold text-mono-100 flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-mono-400 text-[10px]"></i>
                                    <span>{{ $createdAtWib->format('d/m/Y H:i') }} WIB</span>
                                </div>
                                @if($isStillPending)
                                    <div class="text-[11px] text-amber-400 font-semibold mt-1 flex items-center gap-1.5">
                                        <i class="fa-regular fa-clock text-[10px]"></i>
                                        <span>Batas: {{ $expiresAtWib->format('H:i') }} WIB</span>
                                    </div>
                                @elseif($ord->status === 'expired' || $isOverdue)
                                    <div class="text-[10px] text-mono-500 font-medium mt-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-hourglass-end text-[9px]"></i>
                                        <span>Batas: {{ $expiresAtWib->format('H:i') }} WIB</span>
                                    </div>
                                @elseif($ord->isPaid())
                                    <div class="text-[10px] text-emerald-400 font-medium mt-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                        <span>Lunas {{ $ord->paid_at ? $ord->paid_at->timezone('Asia/Jakarta')->format('d/m H:i') : $ord->updated_at->timezone('Asia/Jakarta')->format('d/m H:i') }} WIB</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Aksi (Satu Tombol: Jika ada waktu -> Bayar, jika expired -> Pesan Ulang) -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                @if($isStillPending)
                                    @if($ord->checkout_url)
                                        <a href="{{ $ord->checkout_url }}" target="_blank" class="px-3 py-1.5 rounded-lg mono-btn-primary font-bold text-xs inline-flex items-center gap-1.5 shadow-sm" title="Buka tautan pembayaran Kasera Pay">
                                            <span>Bayar</span>
                                            <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    @endif
                                @elseif($ord->status === 'expired' || $isOverdue || in_array($ord->status, ['canceled', 'failed']))
                                    <a href="{{ route('user.upgrade') }}" class="px-3 py-1.5 rounded-lg bg-mono-800 hover:bg-mono-700 text-mono-200 hover:text-white text-xs font-bold border border-mono-700 transition inline-flex items-center gap-1.5 shadow-sm" title="Buat pesanan baru">
                                        <span>Pesan Ulang</span>
                                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </a>
                                @elseif($ord->isPaid())
                                    <span class="text-emerald-400 font-semibold text-xs inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> Selesai
                                    </span>
                                @else
                                    <span class="text-mono-500 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-mono-400">
                                <div class="w-12 h-12 rounded-2xl bg-mono-850 border border-mono-800 flex items-center justify-center mx-auto text-mono-500 mb-3 text-lg">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div class="font-bold text-mono-200 text-sm">Belum Ada Riwayat Pembayaran</div>
                                <p class="text-xs text-mono-500 mt-1 max-w-sm mx-auto">Anda belum pernah melakukan transaksi langganan paket WhatsApp Gateway.</p>
                                <a href="{{ route('user.upgrade') }}" class="inline-flex items-center gap-1.5 px-4 py-2 mt-4 rounded-xl mono-btn-primary text-xs font-bold shadow-sm">
                                    <i class="fa-solid fa-crown text-xs"></i>
                                    <span>Pilih Paket Langganan</span>
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="pt-4 border-t border-mono-800/80 mt-4">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
