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
            <div class="mb-5 pending-alert-card rounded-2xl p-4 sm:p-5 border transition-all duration-200">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3.5">
                        <div class="pending-alert-icon w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-base shadow-sm">
                            <i class="fa-regular fa-clock"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="pending-alert-tag text-[10px] font-mono uppercase tracking-wider font-extrabold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span>Menunggu Pembayaran</span>
                            </div>
                            <h4 class="pending-alert-title text-sm sm:text-base font-extrabold mt-0.5 truncate">
                                Invoice #{{ $globalPendingOrder->order_no }} &bull; {{ $globalPendingOrder->package->name ?? 'Paket' }} (Rp {{ number_format($globalPendingOrder->amount, 0, ',', '.') }})
                            </h4>
                            <div class="pending-alert-meta text-xs font-mono mt-1 flex flex-wrap items-center gap-2">
                                <span>Dibuat: <strong class="pending-alert-highlight">{{ $createdAtWib->format('d/m/Y H:i') }} WIB</strong></span>
                                <span class="opacity-40">&bull;</span>
                                <span>Batas Bayar: <strong class="pending-alert-highlight">{{ $expiresAtWib->format('H:i') }} WIB</strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                        @if($globalPendingOrder->checkout_url)
                            <a href="{{ $globalPendingOrder->checkout_url }}" target="_blank" class="px-3.5 py-2 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-1.5 shadow-md hover:scale-[1.02] transition" title="Buka tautan pembayaran Kasera Pay">
                                <span>Bayar Sekarang</span>
                                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        @endif

                        <form id="cancel-order-form-{{ $globalPendingOrder->order_no }}" action="{{ route('user.checkout.cancel', $globalPendingOrder->order_no) }}" method="POST" class="inline">
                            @csrf
                            <button type="button" onclick="confirmCancelOrder('{{ $globalPendingOrder->order_no }}')" class="pending-cancel-btn px-3 py-2 rounded-xl text-xs font-semibold transition inline-flex items-center gap-1.5 shadow-sm" title="Batalkan tagihan ini agar dapat memilih paket lain">
                                <i class="fa-solid fa-ban text-[10px]"></i>
                                <span>Batalkan</span>
                            </button>
                        </form>

                        <a href="{{ route('user.orders') }}" class="pending-history-btn px-3 py-2 rounded-xl text-xs font-semibold transition inline-flex items-center gap-1.5" title="Buka riwayat pembayaran & invoice">
                            <i class="fa-solid fa-receipt text-xs"></i>
                            <span class="hidden sm:inline">Riwayat</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endif
