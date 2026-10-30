@if(auth()->check() && !auth()->user()->isAdmin())
    @php
        $globalPendingOrder = auth()->user()->getActivePendingOrder();
    @endphp
    @if($globalPendingOrder)
        @php
            $createdAtWib = $globalPendingOrder->created_at->timezone('Asia/Jakarta');
            $expiresMinutes = (int) config('services.kasera.expires_in_minutes', 60);
            $expiresAtWib = (clone $createdAtWib)->addMinutes($expiresMinutes);
            $nowWib = now()->timezone('Asia/Jakarta');
            $isOverdue = $nowWib->greaterThan($expiresAtWib);
        @endphp
        @if(!$isOverdue)
            <div class="mb-5 mono-card p-4 sm:p-5 border-amber-500/40 bg-gradient-to-r from-amber-500/10 via-mono-900 to-mono-900 shadow-lg">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/40 text-amber-300 flex items-center justify-center flex-shrink-0 text-base shadow-sm">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-[10px] font-mono font-bold text-amber-400 uppercase tracking-wider">Menunggu Pembayaran</div>
                            <h4 class="text-sm font-extrabold text-mono-50 mt-0.5 truncate">
                                Invoice #{{ $globalPendingOrder->order_no }} &bull; {{ $globalPendingOrder->package->name ?? 'Paket' }} (Rp {{ number_format($globalPendingOrder->amount, 0, ',', '.') }})
                            </h4>
                            <div class="text-xs text-mono-300 font-mono mt-1 flex flex-wrap items-center gap-2">
                                <span>Dibuat Pada: <strong class="text-amber-300 font-bold">{{ $createdAtWib->format('d/m/Y H:i') }} WIB</strong></span>
                                <span class="text-mono-600">&bull;</span>
                                <span>Batas Pembayaran: <strong class="text-amber-300 font-bold">{{ $expiresAtWib->format('H:i') }} WIB</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 flex-shrink-0">
                        @if($globalPendingOrder->checkout_url)
                            <a href="{{ $globalPendingOrder->checkout_url }}" target="_blank" class="px-4 py-2 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-1.5 shadow-md hover:scale-[1.02] transition" title="Buka tautan pembayaran Kasera Pay">
                                <span>Bayar Sekarang</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        @endif
                        <a href="{{ route('user.orders') }}" class="px-3.5 py-2 rounded-xl bg-mono-850 hover:bg-mono-800 text-mono-200 hover:text-white border border-mono-700 text-xs font-semibold transition inline-flex items-center gap-1.5" title="Buka riwayat pembayaran & invoice">
                            <i class="fa-solid fa-receipt text-xs"></i>
                            <span>Semua Riwayat</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endif
