@extends('layouts.app')

@section('title', 'Riwayat Transaksi — JalurGaib WA Gateway Admin')
@section('header_title', 'Riwayat Transaksi')
@section('header_subtitle', 'Pantau arus pembayaran langganan gateway masuk via payment gateway Kasera Pay')

@section('content')
<div class="space-y-6">

    <!-- KPI Telemetry Widgets (Bento Grid 2x4) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Pendapatan -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Total Volume</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-mono-200 group-hover:text-mono-50 transition">
                    <i class="fa-solid fa-wallet"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums truncate">
                    Rp {{ number_format($stats['total_volume'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-emerald-400 font-medium mt-0.5 flex items-center gap-1.5 truncate">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Transaksi lunas
                </div>
            </div>
        </div>

        <!-- Transaksi Lunas -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Lunas</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-emerald-400 group-hover:scale-105 transition">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold text-emerald-400 font-mono tracking-tight tabular-nums">
                    {{ number_format($stats['paid_count'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 truncate">
                    Pembayaran sukses
                </div>
            </div>
        </div>

        <!-- Menunggu Pembayaran -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Menunggu</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-amber-400 group-hover:scale-105 transition">
                    <i class="fa-solid fa-clock"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold text-amber-400 font-mono tracking-tight tabular-nums">
                    {{ number_format($stats['pending_count'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 truncate">
                    Menunggu checkout
                </div>
            </div>
        </div>

        <!-- Gagal / Batal -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Batal / Expired</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-mono-400 group-hover:text-mono-200 transition">
                    <i class="fa-solid fa-ban"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold text-mono-300 font-mono tracking-tight tabular-nums">
                    {{ number_format($stats['failed_count'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-500 font-medium mt-0.5 truncate">
                    Tidak diselesaikan
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar (Unified 44px Control Bar) -->
    <div class="mono-card p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Filter Pills (Horizontal scrollable on mobile) -->
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-none pb-1 sm:pb-0">
                <a href="{{ route('admin.orders.index') }}" 
                   class="h-9 px-3.5 rounded-xl text-xs font-bold transition inline-flex items-center justify-center whitespace-nowrap {{ !$status ? 'bg-mono-50 text-mono-950 font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    Semua Status
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" 
                   class="h-9 px-3.5 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $status === 'paid' ? 'bg-emerald-400 text-mono-950 font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Lunas
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
                   class="h-9 px-3.5 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $status === 'pending' ? 'bg-amber-400 text-mono-950 font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Menunggu
                </a>
                <a href="{{ route('admin.orders.index', ['status' => 'failed']) }}" 
                   class="h-9 px-3.5 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $status === 'failed' ? 'bg-mono-800 text-mono-100 font-extrabold border border-mono-600 shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-mono-400"></span> Gagal / Batal
                </a>
            </div>

            <!-- Gateway Info Badge -->
            <div class="flex items-center gap-2 text-xs text-mono-400 font-mono self-start sm:self-auto shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_6px_#34d399] animate-pulse"></span>
                <span>Gateway: <strong class="text-mono-200">Kasera Pay</strong></span>
            </div>
        </div>
    </div>

    <!-- Orders Table Card -->
    <div class="mono-card overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs min-w-[880px]">
                <thead class="bg-mono-950/60 border-b border-mono-800 text-[11px] font-mono text-mono-400 uppercase">
                    <tr>
                        <th class="py-3.5 px-5 font-bold tracking-wider">NO. ORDER</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">PENGGUNA</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">PAKET</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">NOMINAL</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">KASERA REF ID</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">STATUS</th>
                        <th class="py-3.5 px-5 font-bold tracking-wider text-right">WAKTU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800/80">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-mono-900/60 transition group">
                            <!-- Order Number -->
                            <td class="py-3.5 px-5 font-bold text-mono-50 font-mono whitespace-nowrap">
                                <span class="px-2 py-1 rounded bg-mono-950 border border-mono-800 text-mono-200 text-[11px]">
                                    {{ $ord->order_no }}
                                </span>
                            </td>

                            <!-- User Info -->
                            <td class="py-3.5 px-4 font-sans font-medium text-mono-200">
                                <div class="font-bold text-mono-50 flex items-center gap-1.5">
                                    <span>{{ $ord->user->name ?? 'User Terhapus' }}</span>
                                </div>
                                <div class="text-[10px] text-mono-400 font-mono truncate max-w-[170px] mt-0.5">{{ $ord->user->email ?? '-' }}</div>
                            </td>

                            <!-- Package Name -->
                            <td class="py-3.5 px-4 font-sans font-bold text-mono-200 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-mono-800 border border-mono-700 text-mono-200">
                                    {{ $ord->package->name ?? '-' }}
                                </span>
                            </td>

                            <!-- Amount -->
                            <td class="py-3.5 px-4 font-bold text-mono-50 font-mono tabular-nums whitespace-nowrap text-sm">
                                Rp {{ number_format($ord->amount, 0, ',', '.') }}
                            </td>

                            <!-- Kasera Ref ID -->
                            <td class="py-3.5 px-4 text-mono-400 font-mono text-[11px] whitespace-nowrap">
                                <span class="truncate max-w-[150px] inline-block" title="{{ $ord->kasera_transaction_id ?? '-' }}">
                                    {{ $ord->kasera_transaction_id ?? '—' }}
                                </span>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($ord->isPaid())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-mono-50 text-mono-950 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-mono-950"></span> Lunas
                                    </span>
                                @elseif($ord->isPending())
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-mono-950 text-amber-300 border border-amber-500/40 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-mono-950 text-mono-400 border border-mono-700 shadow-sm">
                                        {{ ucfirst($ord->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Created At -->
                            <td class="py-3.5 px-5 text-mono-400 font-mono text-[11px] text-right whitespace-nowrap tabular-nums">
                                {{ $ord->created_at->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-mono-500 font-sans">
                                <div class="text-3xl mb-2 text-mono-600"><i class="fa-solid fa-receipt"></i></div>
                                Belum ada transaksi pembayaran yang tercatat pada filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="p-4 border-t border-mono-800 bg-mono-950/40">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
