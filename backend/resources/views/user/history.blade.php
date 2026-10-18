@extends('layouts.app')

@section('title', 'Riwayat Pesan WhatsApp — JalurGaib WA Gateway')
@section('header_title', 'Riwayat Pesan WhatsApp')
@section('header_subtitle', 'Log Pengiriman Pesan, Status Antrean & Audit Pengiriman')

@section('content')
<div class="space-y-6 font-sans max-w-7xl">

    <!-- Filter & Search Control Bar (44px Unified Controls) -->
    <div class="mono-card p-5">
        <form method="GET" action="{{ route('user.history') }}" class="flex flex-col sm:flex-row gap-3 items-stretch sm:items-center justify-between">
            
            <div class="flex-1 flex flex-col sm:flex-row gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-mono-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tujuan (cth: 0812...) atau isi pesan..." class="w-full mono-input pl-10 pr-4 py-2.5 text-xs text-mono-100 font-medium">
                </div>

                <!-- Status Filter -->
                <div class="w-full sm:w-48">
                    <select name="status" onchange="this.form.submit()" class="w-full mono-input px-3.5 py-2.5 text-xs text-mono-100 font-medium">
                        <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>Semua Status</option>
                        <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Terkirim (Sent)</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Antrean (Pending)</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Gagal (Failed)</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center justify-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span>Terapkan Filter</span>
                </button>

                @if(request('search') || request('status'))
                    <a href="{{ route('user.history') }}" class="px-3.5 py-2.5 rounded-xl bg-mono-800 hover:bg-mono-700 border border-mono-700 text-mono-300 text-xs font-semibold transition flex items-center justify-center" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- High-Density Data Table -->
    <div class="mono-card p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-mono-400 text-sm"></i>
                <h3 class="text-xs font-bold text-mono-100 uppercase font-mono tracking-wider">Log Riwayat Pesan</h3>
            </div>
            <div class="text-xs text-mono-400 font-mono tabular-nums">
                Total: {{ $messages->total() }} Pesan
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-mono-800 text-[11px] font-mono text-mono-400 uppercase">
                        <th class="py-3 px-4">Waktu (WIB)</th>
                        <th class="py-3 px-4">Perangkat</th>
                        <th class="py-3 px-4">Nomor Tujuan</th>
                        <th class="py-3 px-4">Isi Pesan</th>
                        <th class="py-3 px-4">Media</th>
                        <th class="py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800/60 font-mono">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-mono-900/40 transition">
                            <!-- Waktu WIB -->
                            <td class="py-3.5 px-4 text-mono-300 whitespace-nowrap text-[11px] tabular-nums">
                                <div class="font-bold text-mono-100">
                                    {{ $msg->created_at->timezone('Asia/Jakarta')->format('d/m/Y') }}
                                </div>
                                <div class="text-[10px] text-mono-400 mt-0.5">
                                    {{ $msg->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB
                                </div>
                            </td>

                            <!-- Perangkat -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="font-sans font-bold text-mono-200">{{ $msg->device->name ?? '-' }}</div>
                                <div class="text-[10px] text-mono-400">{{ $msg->device->phone_number ?? '-' }}</div>
                            </td>

                            <!-- Nomor Tujuan -->
                            <td class="py-3.5 px-4 font-bold text-mono-100 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-md bg-mono-850 border border-mono-700/80 text-[11px]">
                                    {{ $msg->to }}
                                </span>
                            </td>

                            <!-- Isi Pesan -->
                            <td class="py-3.5 px-4 text-mono-300 font-sans text-xs max-w-md">
                                <div class="line-clamp-2 leading-relaxed">
                                    {{ $msg->message }}
                                </div>
                                @if($msg->error_message)
                                    <div class="text-[10px] text-red-400 font-mono mt-1 flex items-center gap-1">
                                        <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                                        <span>{{ Str::limit($msg->error_message, 60) }}</span>
                                    </div>
                                @endif
                            </td>

                            <!-- Media Attachment -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($msg->media_url)
                                    <a href="{{ $msg->media_url }}" target="_blank" class="px-2 py-1 rounded bg-mono-800 hover:bg-mono-700 border border-mono-700 text-mono-300 text-[10px] inline-flex items-center gap-1">
                                        <i class="fa-regular fa-image"></i>
                                        <span>Gambar</span>
                                    </a>
                                @else
                                    <span class="text-mono-600 text-[10px]">-</span>
                                @endif
                            </td>

                            <!-- Status Pill -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($msg->status === 'sent')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-[9px]"></i> Terkirim
                                    </span>
                                @elseif(in_array($msg->status, ['pending', 'processing']))
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-hourglass-half text-[9px]"></i> Antrean
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-red-500/20 text-red-300 border border-red-500/40 inline-flex items-center gap-1">
                                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Gagal
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-mono-500 font-sans">
                                <i class="fa-regular fa-comment-dots text-3xl mb-2 block text-mono-600"></i>
                                <span class="text-xs">Tidak ada data riwayat pesan yang sesuai dengan filter.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($messages->hasPages())
            <div class="mt-6 pt-4 border-t border-mono-800">
                {{ $messages->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
