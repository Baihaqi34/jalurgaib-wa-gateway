@extends('layouts.app')

@section('title', 'Log Aktivitas — JalurGaib WA Gateway Admin')
@section('header_title', 'Log Aktivitas')
@section('header_subtitle', 'Audit trail telemetri real-time: autentikasi, pembayaran, transaksi paket, dan keamanan akun')

@section('content')
<div class="space-y-6">

    <!-- KPI Telemetry Widgets (Bento Grid 2x4) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Hari Ini -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Aktivitas Hari Ini</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-mono-200 group-hover:text-mono-50 transition">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums">
                    {{ number_format($stats['total_today'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 truncate">
                    Total event tercatat
                </div>
            </div>
        </div>

        <!-- Login Berhasil Hari Ini -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Login Berhasil</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-xs text-emerald-400 group-hover:scale-105 transition">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold text-emerald-400 font-mono tracking-tight tabular-nums">
                    {{ number_format($stats['login_today'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-emerald-400/80 font-medium mt-0.5 truncate">
                    Sesi pengguna masuk
                </div>
            </div>
        </div>

        <!-- Pembayaran Lunas Hari Ini -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Pembayaran Lunas</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-xs text-emerald-400 group-hover:scale-105 transition">
                    <i class="fa-solid fa-credit-card"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold text-emerald-400 font-mono tracking-tight tabular-nums">
                    {{ number_format($stats['payment_today'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-emerald-400/80 font-medium mt-0.5 truncate">
                    Transaksi berhasil
                </div>
            </div>
        </div>

        <!-- Percobaan Gagal / Batal Hari Ini -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Gagal / Dibatalkan</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-xs text-rose-400 group-hover:scale-105 transition">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
            </div>
            <div class="mt-2.5 sm:mt-3">
                <div class="text-lg sm:text-2xl font-extrabold {{ $stats['failed_today'] > 0 ? 'text-rose-400' : 'text-mono-300' }} font-mono tracking-tight tabular-nums">
                    {{ number_format($stats['failed_today'], 0, ',', '.') }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 truncate">
                    Login gagal / batal order
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar (Unified 44px Control Bar) -->
    <div class="mono-card p-4 sm:p-5">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <!-- Left: Search Box -->
            <div class="relative flex-1 min-w-[240px] max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-mono-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Cari email, nama, deskripsi, atau IP..." 
                       class="w-full h-10 pl-9 pr-8 bg-mono-950 border border-mono-700 rounded-xl text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition">
                @if($search)
                    <a href="{{ route('admin.logs.index', array_filter(['event' => $event, 'status' => $status, 'role' => $role])) }}" 
                       class="absolute inset-y-0 right-0 pr-3 flex items-center text-mono-400 hover:text-white text-xs">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>

            <!-- Middle: Filter Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-none pb-1 lg:pb-0">
                <a href="{{ route('admin.logs.index', array_filter(['search' => $search, 'role' => $role])) }}" 
                   class="h-9 px-3 rounded-xl text-xs font-bold transition inline-flex items-center justify-center whitespace-nowrap {{ !$event ? 'bg-mono-50 text-mono-950 font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    Semua
                </a>
                <a href="{{ route('admin.logs.index', array_filter(['event' => 'payment', 'search' => $search, 'role' => $role])) }}" 
                   class="h-9 px-3 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $event === 'payment' ? 'bg-emerald-400 text-mono-950 font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <i class="fa-solid fa-credit-card text-[10px]"></i> Pembayaran
                </a>
                <a href="{{ route('admin.logs.index', array_filter(['event' => 'auth', 'search' => $search, 'role' => $role])) }}" 
                   class="h-9 px-3 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $event === 'auth' ? 'bg-mono-800 text-mono-100 font-extrabold border border-mono-600 shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <i class="fa-solid fa-arrow-right-to-bracket text-[10px]"></i> Login & Logout
                </a>
                <a href="{{ route('admin.logs.index', array_filter(['event' => 'payment_success', 'search' => $search, 'role' => $role])) }}" 
                   class="h-9 px-3 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $event === 'payment_success' ? 'bg-emerald-400 text-mono-950 font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Lunas
                </a>
                <a href="{{ route('admin.logs.index', array_filter(['event' => 'checkout', 'search' => $search, 'role' => $role])) }}" 
                   class="h-9 px-3 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $event === 'checkout' ? 'bg-sky-400 text-mono-950 font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span> Checkout
                </a>
                <a href="{{ route('admin.logs.index', array_filter(['event' => 'login_failed', 'search' => $search, 'role' => $role])) }}" 
                   class="h-9 px-3 rounded-xl text-xs font-bold transition inline-flex items-center justify-center gap-1.5 whitespace-nowrap {{ $event === 'login_failed' ? 'bg-rose-500 text-white font-extrabold shadow-sm' : 'bg-mono-950 hover:bg-mono-800 text-mono-300 border border-mono-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span> Gagal
                </a>
            </div>

            <!-- Right: Role Selector & Clean Action -->
            <div class="flex items-center gap-2 self-start lg:self-auto shrink-0">
                <select name="role" 
                        onchange="this.form.submit()" 
                        class="h-9 px-3 rounded-xl text-xs font-bold bg-mono-950 border border-mono-700 text-mono-200 focus:outline-none">
                    <option value="all" {{ !$role || $role === 'all' ? 'selected' : '' }}>Semua Role</option>
                    <option value="user" {{ $role === 'user' ? 'selected' : '' }}>User Client</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Super Admin</option>
                </select>

                <!-- Clear Logs > 30 Days Button -->
                <form action="{{ route('admin.logs.clear-old') }}" method="POST" class="inline"
                      @submit.prevent="AppSwal.confirm('Bersihkan Log Lama?', 'Log yang berusia lebih dari 30 hari akan dihapus secara permanen untuk mengoptimalkan database.', 'Ya, Bersihkan', false).then(ok => { if (ok) { AppSwal.loading('Membersihkan Data...', ''); $el.submit(); } })">
                    @csrf
                    <button type="submit" 
                            class="h-9 px-3 rounded-xl text-xs font-bold bg-mono-850 hover:bg-mono-800 border border-mono-700 text-mono-300 hover:text-white transition inline-flex items-center gap-1.5 shadow-sm"
                            title="Bersihkan log lebih dari 30 hari">
                        <i class="fa-solid fa-broom text-xs"></i>
                        <span class="hidden sm:inline">Bersihkan &gt;30 Hari</span>
                    </button>
                </form>
            </div>

        </form>
    </div>

    <!-- Logs Telemetry Table Card -->
    <div class="mono-card overflow-hidden">
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs min-w-[980px]">
                <thead class="bg-mono-950/60 border-b border-mono-800 text-[11px] font-mono text-mono-400 uppercase">
                    <tr>
                        <th class="py-3.5 px-5 font-bold tracking-wider">PENGGUNA</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">EVENT</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">DESKRIPSI AKTIVITAS</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">METODE / GATEWAY</th>
                        <th class="py-3.5 px-4 font-bold tracking-wider">PERANGKAT & IP</th>
                        <th class="py-3.5 px-5 font-bold tracking-wider text-right">WAKTU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800/80">
                    @forelse($logs as $log)
                        <tr class="hover:bg-mono-900/60 transition group">
                            
                            <!-- Pengguna -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-mono-850 border border-mono-700 text-mono-200 font-bold flex items-center justify-center text-xs flex-shrink-0 shadow-sm">
                                        {{ strtoupper(substr($log->name ?: ($log->email ?: '?'), 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-mono-50 truncate flex items-center gap-1.5">
                                            <span>{{ $log->name ?: 'Tidak Diketahui' }}</span>
                                            @if($log->role === 'admin')
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30 uppercase">Admin</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-mono-400 font-mono truncate max-w-[200px]">{{ $log->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Event & Status -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if($log->event === 'payment_success')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shadow-sm">
                                        <i class="fa-solid fa-circle-check text-[10px]"></i>
                                        <span>Pembayaran Lunas</span>
                                    </span>
                                @elseif($log->event === 'checkout')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-500/15 text-sky-400 border border-sky-500/30 shadow-sm">
                                        <i class="fa-solid fa-cart-shopping text-[10px]"></i>
                                        <span>Checkout Tagihan</span>
                                    </span>
                                @elseif($log->event === 'payment_canceled')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-mono-800 text-mono-300 border border-mono-700">
                                        <i class="fa-solid fa-ban text-[10px]"></i>
                                        <span>Tagihan Dibatalkan</span>
                                    </span>
                                @elseif($log->event === 'payment_failed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                        <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                        <span>Pembayaran Gagal</span>
                                    </span>
                                @elseif($log->event === 'login')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-[10px]"></i>
                                        <span>Login</span>
                                    </span>
                                @elseif($log->event === 'logout')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-mono-800 text-mono-300 border border-mono-700">
                                        <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i>
                                        <span>Logout</span>
                                    </span>
                                @elseif($log->event === 'login_failed')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                        <i class="fa-solid fa-triangle-exclamation text-[10px]"></i>
                                        <span>Login Gagal</span>
                                    </span>
                                @elseif($log->event === 'register')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-indigo-500/15 text-indigo-400 border border-indigo-500/30">
                                        <i class="fa-solid fa-user-plus text-[10px]"></i>
                                        <span>Registrasi</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-mono-800 text-mono-300 border border-mono-700">
                                        <span>{{ $log->getEventLabel() }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Deskripsi Aktivitas -->
                            <td class="py-3.5 px-4 font-sans text-xs text-mono-200">
                                <div class="font-medium text-mono-100 max-w-sm leading-relaxed">
                                    {{ $log->description ?: ($log->failure_reason ?: $log->getEventLabel()) }}
                                </div>
                            </td>

                            <!-- Metode / Channel -->
                            <td class="py-3.5 px-4 font-mono text-[11px] text-mono-300 whitespace-nowrap">
                                @if(in_array($log->login_method, ['kasera_pay', 'qris', 'va', 'mandiri_va', 'bca_va', 'bni_va', 'bri_va']))
                                    <span class="inline-flex items-center gap-1.5 text-emerald-400 font-bold bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded">
                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                        <span>Kasera Pay</span>
                                    </span>
                                @elseif($log->login_method === 'google_firebase')
                                    <span class="inline-flex items-center gap-1 text-mono-200">
                                        <i class="fa-brands fa-google text-red-400 text-xs"></i>
                                        <span>Google Auth</span>
                                    </span>
                                @elseif($log->login_method === 'session')
                                    <span class="text-mono-400">Web Session</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-mono-300">
                                        <i class="fa-solid fa-key text-amber-400 text-[10px]"></i>
                                        <span>Password</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Perangkat & IP -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-lg bg-mono-950 border border-mono-800 flex items-center justify-center text-xs" title="Device: {{ $log->device ?? 'Desktop' }}">
                                        <i class="{{ $log->getDeviceIcon() }}"></i>
                                    </span>
                                    <span class="w-6 h-6 rounded-lg bg-mono-950 border border-mono-800 flex items-center justify-center text-xs" title="OS: {{ $log->platform ?? 'Unknown' }}">
                                        <i class="{{ $log->getPlatformIcon() }}"></i>
                                    </span>
                                    <span class="w-6 h-6 rounded-lg bg-mono-950 border border-mono-800 flex items-center justify-center text-xs" title="Browser: {{ $log->browser ?? 'Unknown' }}">
                                        <i class="{{ $log->getBrowserIcon() }}"></i>
                                    </span>
                                    <span class="px-2 py-0.5 rounded bg-mono-950 border border-mono-800 text-[10px] font-mono text-mono-300">
                                        {{ $log->ip_address ?: '127.0.0.1' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Waktu & Tanggal -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="font-bold text-mono-100 text-xs">
                                    {{ $log->created_at->diffForHumans() }}
                                </div>
                                <div class="text-[10px] text-mono-400 font-mono mt-0.5">
                                    {{ $log->created_at->format('d M Y, H:i:s') }}
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-mono-400">
                                <div class="w-12 h-12 rounded-2xl bg-mono-850 border border-mono-700 flex items-center justify-center mx-auto mb-3 text-mono-300 text-lg shadow-inner">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div class="font-bold text-mono-200 text-sm">Belum Ada Data Log Aktivitas</div>
                                <div class="text-xs text-mono-500 mt-1 max-w-sm mx-auto">
                                    Aktivitas login, logout, checkout, dan pembayaran pengguna akan otomatis tercatat dan muncul di sini secara real-time.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($logs->hasPages())
            <div class="p-4 border-t border-mono-800 bg-mono-950/40 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                <div class="text-mono-400 font-mono">
                    Menampilkan <strong>{{ $logs->firstItem() }}</strong> - <strong>{{ $logs->lastItem() }}</strong> dari <strong>{{ $logs->total() }}</strong> log aktivitas
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
