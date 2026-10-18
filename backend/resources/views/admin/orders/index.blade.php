@extends('layouts.app')

@section('title', 'Riwayat Transaksi — JalurGaib WA Gateway Admin')
@section('header_title', 'Riwayat Transaksi & Pembayaran')
@section('header_subtitle', 'Pantau arus pembayaran langganan gateway masuk via payment gateway Kasera Pay')

@section('content')
<div class="space-y-6">

    <!-- KPI Telemetry Widgets -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="mono-card p-5">
            <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">TOTAL PENDAPATAN</div>
            <div class="text-xl font-extrabold text-mono-50 font-mono mt-1">
                Rp {{ number_format($stats['total_volume'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-emerald-400 font-medium mt-1">✓ Berhasil Ditransaksikan</div>
        </div>
        <div class="mono-card p-5">
            <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">TRANSAKSI LUNAS</div>
            <div class="text-xl font-extrabold text-emerald-400 font-mono mt-1">
                {{ number_format($stats['paid_count'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-mono-400 font-medium mt-1">Pembayaran Sukses</div>
        </div>
        <div class="mono-card p-5">
            <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">MENUNGGU PEMBAYARAN</div>
            <div class="text-xl font-extrabold text-amber-400 font-mono mt-1">
                {{ number_format($stats['pending_count'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-mono-400 font-medium mt-1">Menunggu Checkout Buyer</div>
        </div>
        <div class="mono-card p-5">
            <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">GAGAL / KADALUARSA</div>
            <div class="text-xl font-extrabold text-mono-400 font-mono mt-1">
                {{ number_format($stats['failed_count'], 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-mono-500 font-medium mt-1">Tidak Diselesaikan</div>
        </div>
    </div>

    <!-- Filter & Toolbar -->
    <div class="mono-card p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <a href="{{ route('admin.orders.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !$status ? 'bg-mono-50 text-mono-950 font-extrabold' : 'bg-mono-950 text-mono-300 hover:text-white border border-mono-800' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'paid' ? 'bg-emerald-400 text-mono-950 font-extrabold' : 'bg-mono-950 text-mono-300 hover:text-white border border-mono-800' }}">
                Lunas
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'pending' ? 'bg-amber-400 text-mono-950 font-extrabold' : 'bg-mono-950 text-mono-300 hover:text-white border border-mono-800' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.orders.index', ['status' => 'failed']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $status === 'failed' ? 'bg-red-400 text-mono-950 font-extrabold' : 'bg-mono-950 text-mono-300 hover:text-white border border-mono-800' }}">
                Gagal / Batal
            </a>
        </div>
        <div class="text-xs text-mono-400 font-mono">
            Gateway: <strong>Kasera Pay</strong> (Webhook & Verification Active)
        </div>
    </div>

    <!-- Orders Table -->
    <div class="mono-card p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-mono-800 text-[11px] font-mono text-mono-400 uppercase">
                        <th class="py-3 px-4">No. Order</th>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Paket</th>
                        <th class="py-3 px-4">Nominal</th>
                        <th class="py-3 px-4">Kasera Ref ID</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800/60 font-mono">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-mono-900/40 transition">
                            <td class="py-3 px-4 font-bold text-mono-50">{{ $ord->order_no }}</td>
                            <td class="py-3 px-4 font-sans font-medium text-mono-200">
                                <div>{{ $ord->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-[10px] text-mono-400 font-mono">{{ $ord->user->email ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-sans font-bold text-mono-200">{{ $ord->package->name ?? '-' }}</td>
                            <td class="py-3 px-4 font-bold text-mono-50">Rp {{ number_format($ord->amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-mono-400 text-[11px]">{{ $ord->kasera_transaction_id ?? '-' }}</td>
                            <td class="py-3 px-4">
                                @if($ord->isPaid())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">Lunas</span>
                                @elseif($ord->isPending())
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">Menunggu</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/40">{{ ucfirst($ord->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-mono-400 text-[11px]">
                                {{ $ord->created_at->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-mono-500 font-sans">
                                Belum ada transaksi pembayaran yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="mt-4 pt-4 border-t border-mono-800">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
