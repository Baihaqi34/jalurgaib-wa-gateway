@extends('layouts.app')

@section('title', 'Users — JalurGaib WA Gateway Admin')
@section('header_title', 'Users')
@section('header_subtitle', 'Kelola akun pengguna, hak akses role, paket kuota langganan, dan status penangguhan (Suspend / Ban)')

@section('content')
<div x-data="userCrudApp()" class="space-y-6">

    <!-- Bento KPI Telemetry Grid (Quiet Luxury Noir) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Users -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Total Users</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-mono-300 group-hover:text-mono-50 transition">
                    <i class="fa-solid fa-users"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-xl sm:text-2xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums">
                    {{ number_format($kpis['total_users'] ?? $users->total()) }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-mono-400"></span> Akun terdaftar di sistem
                </div>
            </div>
        </div>

        <!-- Active Users -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Akun Aktif</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-mono-100 group-hover:scale-105 transition">
                    <i class="fa-solid fa-circle-check"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-xl sm:text-2xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums">
                    {{ number_format($kpis['active_users'] ?? 0) }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Status normal & aktif
                </div>
            </div>
        </div>

        <!-- Suspended Users -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Suspended / Ban</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-mono-400 group-hover:text-mono-200 transition">
                    <i class="fa-solid fa-ban"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-xl sm:text-2xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums">
                    {{ number_format($kpis['suspended_users'] ?? 0) }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Dibatasi sementara
                </div>
            </div>
        </div>

        <!-- Total Messages Sent -->
        <div class="mono-card p-4 sm:p-5 flex flex-col justify-between relative overflow-hidden transition hover:border-mono-600 group">
            <div class="flex items-center justify-between text-mono-400">
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold tracking-wider">Pesan Terkirim</span>
                <span class="w-7 h-7 sm:w-8 sm:h-8 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-xs text-mono-300 group-hover:text-mono-50 transition">
                    <i class="fa-solid fa-paper-plane"></i>
                </span>
            </div>
            <div class="mt-3">
                <div class="text-xl sm:text-2xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums">
                    {{ number_format($kpis['total_messages_sent'] ?? 0) }}
                </div>
                <div class="text-[10px] sm:text-[11px] text-mono-400 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-mono-400"></span> Total lalu lintas gateway
                </div>
            </div>
        </div>
    </div>

    <!-- Unified 44px Control & Filter Bar -->
    <div class="mono-card p-4 sm:p-5">
        <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            
            <!-- Filters Group -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 flex-1 max-w-3xl">
                <!-- Search Input (44px) -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-mono-500 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email pengguna..."
                           class="w-full h-11 pl-9 pr-4 rounded-xl bg-mono-950/80 border border-mono-700 hover:border-mono-600 focus:border-mono-400 text-mono-100 text-xs placeholder-mono-500 font-medium outline-none transition">
                </div>

                <!-- Role Filter (44px) -->
                <select name="role" onchange="this.form.submit()"
                        class="h-11 px-3.5 rounded-xl bg-mono-950/80 border border-mono-700 hover:border-mono-600 focus:border-mono-400 text-mono-200 text-xs font-medium outline-none transition cursor-pointer">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>👑 Super Admin</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>👤 Tenant User</option>
                </select>

                <!-- Action Filter Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="h-11 px-4 rounded-xl mono-btn-secondary text-xs font-bold shadow-sm inline-flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-filter text-[10px]"></i> Filter
                    </button>
                    @if(request()->filled('search') || request()->filled('role'))
                        <a href="{{ route('admin.users.index') }}" class="h-11 px-3 rounded-xl bg-mono-950 hover:bg-mono-800 border border-mono-800 text-mono-400 hover:text-mono-200 text-xs font-bold inline-flex items-center justify-center transition" title="Reset Filter">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </form>

            <!-- Create Button (44px) -->
            <button @click="openCreateModal()" class="h-11 px-5 rounded-xl mono-btn-primary text-xs font-extrabold inline-flex items-center justify-center gap-2 shadow-sm whitespace-nowrap">
                <i class="fa-solid fa-plus text-[11px]"></i> Tambah Akun Baru
            </button>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="mono-card overflow-hidden">
        <!-- Responsive Horizontal Scroll Wrapper -->
        <div class="overflow-x-auto scrollbar-thin">
            <table class="w-full text-left text-xs min-w-[920px]">
                <thead class="text-mono-400 bg-mono-950/60 border-b border-mono-800 font-mono text-[11px]">
                    <tr>
                        <th class="py-3.5 px-5 font-bold uppercase tracking-wider">NAMA & EMAIL</th>
                        <th class="py-3.5 px-3 font-bold uppercase tracking-wider">ROLE</th>
                        <th class="py-3.5 px-3 font-bold uppercase tracking-wider">STATUS AKUN</th>
                        <th class="py-3.5 px-3 font-bold uppercase tracking-wider">PAKET AKTIF</th>
                        <th class="py-3.5 px-3 font-bold uppercase tracking-wider text-center">DEVICE</th>
                        <th class="py-3.5 px-3 font-bold uppercase tracking-wider text-center">API KEYS</th>
                        <th class="py-3.5 px-3 font-bold uppercase tracking-wider text-center">PESAN TERKIRIM</th>
                        <th class="py-3.5 px-5 font-bold uppercase tracking-wider text-right">AKSI & KONTROL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800/80">
                    @forelse($users as $user)
                        <tr class="hover:bg-mono-900/60 transition group">
                            <!-- User Name & Email -->
                            <td class="py-3.5 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-mono-800 border border-mono-700 flex items-center justify-center text-xs font-bold text-mono-200 shrink-0 font-mono">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-mono-50 flex items-center gap-1.5 truncate">
                                            <span>{{ $user->name }}</span>
                                            @if($user->isAdmin())
                                                <span class="text-[10px] text-amber-400" title="Super Admin">👑</span>
                                            @endif
                                        </div>
                                        <div class="text-mono-400 font-mono text-[11px] truncate mt-0.5">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role Badge (Fixed, Clean, Non-wrapping) -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @if($user->isAdmin())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-mono-50 text-mono-950 shadow-sm border border-mono-200">
                                        <span>👑</span> Super Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-mono-800 text-mono-300 border border-mono-700 shadow-sm">
                                        <span>👤</span> Tenant User
                                    </span>
                                @endif
                            </td>

                            <!-- Status Akun -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @if($user->isSuspended())
                                    <div>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-950 text-mono-300 border border-mono-600 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span> Suspended
                                        </span>
                                        @if($user->suspend_reason)
                                            <div class="text-[10px] text-mono-400 mt-1 truncate max-w-[150px] font-mono" title="{{ $user->suspend_reason }}">
                                                "{{ $user->suspend_reason }}"
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-mono-50 text-mono-950 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-mono-950"></span> Aktif
                                    </span>
                                @endif
                            </td>

                            <!-- Paket Aktif (With Dynamic Reset Cycle) -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @php $pkg = $user->package; @endphp
                                @if($pkg)
                                    <div class="flex flex-col gap-1 items-start">
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-mono-800 border border-mono-700 text-mono-200">
                                                {{ $pkg->badge ?? $pkg->name }}
                                            </span>
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-mono {{ ($pkg->reset_cycle ?? 'daily') === 'monthly' ? 'bg-mono-100 text-mono-950 font-bold' : 'bg-mono-900 text-mono-400 border border-mono-800' }}">
                                                {{ ($pkg->reset_cycle ?? 'daily') === 'monthly' ? 'Bulan' : 'Hari' }}
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-mono-400 font-mono">
                                            {{ $pkg->max_devices }} dev · {{ number_format($pkg->daily_message_limit, 0, ',', '.') }} msg/{{ ($pkg->reset_cycle ?? 'daily') === 'monthly' ? 'bulan' : 'hari' }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-[10px] text-mono-500 italic">—</span>
                                @endif
                            </td>

                            <!-- Telemetry Counts (Tabular numbers) -->
                            <td class="py-3.5 px-3 text-center font-bold text-mono-100 text-xs sm:text-sm font-mono tabular-nums">
                                {{ $user->devices_count }}
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-mono-100 text-xs sm:text-sm font-mono tabular-nums">
                                {{ $user->api_keys_count }}
                            </td>
                            <td class="py-3.5 px-3 text-center font-bold text-mono-100 text-xs sm:text-sm font-mono tabular-nums">
                                {{ number_format($user->messages_count ?? 0) }}
                            </td>

                            <!-- Actions & Controls (Fixed Single Line) -->
                            <td class="py-3.5 px-5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center justify-end gap-1.5">
                                    
                                    <!-- Edit Button -->
                                    <button @click="openEditModal({{ json_encode($user) }})"
                                            class="h-8 px-3 rounded-lg mono-btn-secondary text-[11px] font-bold shadow-sm inline-flex items-center justify-center gap-1 transition"
                                            title="Edit Akun Pengguna">
                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                    </button>

                                    <!-- Suspend / Activate Toggle -->
                                    @if($user->id !== auth()->id())
                                        @if($user->isSuspended())
                                            <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline" @submit.prevent="handleActivate($event, '{{ $user->name }}')">
                                                @csrf
                                                <button type="submit"
                                                        class="h-8 px-3 rounded-lg bg-mono-50 hover:bg-mono-200 text-mono-950 text-[11px] font-extrabold transition shadow-sm inline-flex items-center justify-center gap-1"
                                                        title="Aktifkan Kembali Akun">
                                                    <i class="fa-solid fa-check text-[10px]"></i> Aktifkan
                                                </button>
                                            </form>
                                        @else
                                            <button @click="openSuspendModal({{ json_encode($user) }})"
                                                    class="h-8 px-3 rounded-lg bg-mono-800 hover:bg-mono-700 border border-mono-600 text-mono-200 text-[11px] font-bold transition shadow-sm inline-flex items-center justify-center gap-1"
                                                    title="Suspend Akun">
                                                <i class="fa-solid fa-ban text-[10px] text-amber-400"></i> Suspend
                                            </button>
                                        @endif

                                        <!-- Delete Button with SweetAlert2 Confirmation -->
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" @submit.prevent="handleDelete($event, '{{ $user->name }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="h-8 px-2.5 rounded-lg bg-mono-950 hover:bg-mono-800 border border-mono-700 text-mono-400 hover:text-white text-[11px] font-bold transition shadow-sm inline-flex items-center justify-center"
                                                    title="Hapus Akun Permanen">
                                                <i class="fa-solid fa-trash-can text-[10px]"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-mono-500 font-semibold">
                                <div class="text-3xl mb-2 text-mono-600"><i class="fa-solid fa-user-slash"></i></div>
                                Tidak ada data pengguna yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-4 border-t border-mono-800 bg-mono-950/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- MODAL CREATE USER -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl my-8 max-h-[90vh] overflow-y-auto" @click.outside="showCreateModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-mono-800 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-mono-50">➕ Tambah Akun Baru</h3>
                    <p class="text-xs text-mono-400 font-medium mt-0.5">Buat akun untuk Super Admin (Aplikator) atau Tenant Pengguna</p>
                </div>
                <button type="button" @click="showCreateModal = false" class="w-8 h-8 rounded-xl bg-mono-800 hover:bg-mono-700 text-mono-400 hover:text-mono-100 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" @submit="submitWithLoading('Menyimpan akun baru...')" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Nama Lengkap / Nama Bisnis</label>
                    <input type="text" name="name" required placeholder="Contoh: Toko Online Abadi" class="w-full h-10 mono-input px-3.5 font-medium rounded-xl">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Alamat Email</label>
                    <input type="email" name="email" required placeholder="nama@domain.com" class="w-full h-10 mono-input px-3.5 font-medium rounded-xl">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full h-10 mono-input px-3.5 font-medium rounded-xl">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Role Akses Akun</label>
                        <select name="role" required class="w-full h-10 mono-input px-3 font-medium rounded-xl">
                            <option value="user">👤 Tenant User</option>
                            <option value="admin">👑 Super Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Paket Layanan</label>
                        <select name="package_id" class="w-full h-10 mono-input px-3 font-medium rounded-xl">
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}" {{ $pkg->slug === 'free' ? 'selected' : '' }}>
                                    📦 {{ $pkg->name }} ({{ number_format($pkg->daily_message_limit, 0, ',', '.') }} msg/{{ ($pkg->reset_cycle ?? 'daily') === 'monthly' ? 'bulan' : 'hari' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showCreateModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs rounded-xl font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm rounded-xl font-extrabold">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT USER -->
    <div x-show="showEditModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-6 sm:p-7 max-w-lg w-full shadow-2xl my-8 max-h-[90vh] overflow-y-auto" @click.outside="showEditModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-mono-800 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-mono-50">✏️ Edit Akun Pengguna</h3>
                    <p class="text-xs text-mono-400 font-medium mt-0.5">Perbarui nama, email, role, atau ganti tier paket</p>
                </div>
                <button type="button" @click="showEditModal = false" class="w-8 h-8 rounded-xl bg-mono-800 hover:bg-mono-700 text-mono-400 hover:text-mono-100 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form :action="editActionUrl" method="POST" @submit="submitWithLoading('Memperbarui akun...')" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editForm.name" required class="w-full h-10 mono-input px-3.5 font-medium rounded-xl">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Alamat Email</label>
                    <input type="email" name="email" x-model="editForm.email" required class="w-full h-10 mono-input px-3.5 font-medium rounded-xl">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full h-10 mono-input px-3.5 font-medium rounded-xl">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Role Akses Akun</label>
                        <select name="role" x-model="editForm.role" required class="w-full h-10 mono-input px-3 font-medium rounded-xl">
                            <option value="user">👤 Tenant User</option>
                            <option value="admin">👑 Super Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Tier / Paket Layanan</label>
                        <select name="package_id" x-model="editForm.package_id" class="w-full h-10 mono-input px-3 font-medium rounded-xl">
                            <option value="">— Tidak Diubah —</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}">
                                    📦 {{ $pkg->name }} ({{ number_format($pkg->daily_message_limit, 0, ',', '.') }} msg/{{ ($pkg->reset_cycle ?? 'daily') === 'monthly' ? 'bulan' : 'hari' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs rounded-xl font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm rounded-xl font-extrabold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SUSPEND / BAN USER -->
    <div x-show="showSuspendModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-6 sm:p-7 max-w-md w-full shadow-2xl my-8" @click.outside="showSuspendModal = false">
            <div class="flex items-center justify-between pb-4 border-b border-mono-800 mb-5">
                <div>
                    <h3 class="text-base font-extrabold text-mono-50 flex items-center gap-2">
                        <span class="text-amber-400">⛔</span> Suspend Akun Pengguna
                    </h3>
                    <p class="text-xs text-mono-400 font-medium mt-0.5">Pengguna tidak akan bisa mengirim pesan, scan device, atau menggunakan API Key.</p>
                </div>
                <button type="button" @click="showSuspendModal = false" class="w-8 h-8 rounded-xl bg-mono-800 hover:bg-mono-700 text-mono-400 hover:text-mono-100 flex items-center justify-center transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <form :action="suspendActionUrl" method="POST" @submit="submitWithLoading('Menangguhkan akun...')" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Akun Target:</label>
                    <div class="p-3 bg-mono-950 rounded-xl text-mono-100 font-bold flex items-center justify-between border border-mono-800">
                        <span x-text="suspendUser.name"></span>
                        <span class="text-mono-400 font-mono text-[11px]" x-text="suspendUser.email"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Alasan Penangguhan / Pengumuman:</label>
                    <textarea name="reason" rows="3" required placeholder="Contoh: Akun ditangguhkan sementara karena terindikasi mengirim spam massal tanpa jeda anti-ban."
                              class="w-full mono-input p-3 font-medium rounded-xl"></textarea>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showSuspendModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs rounded-xl font-bold">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-mono-100 hover:bg-white text-mono-950 text-xs shadow-sm rounded-xl font-extrabold">Tangguhkan Akun</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function userCrudApp() {
        return {
            showCreateModal: false,
            showEditModal: false,
            showSuspendModal: false,
            editForm: { id: null, name: '', email: '', role: 'user', package_id: '' },
            editActionUrl: '',
            suspendUser: { id: null, name: '', email: '' },
            suspendActionUrl: '',

            openCreateModal() {
                this.showCreateModal = true;
            },

            openEditModal(user) {
                this.editForm = {
                    id: user.id,
                    name: user.name,
                    email: user.email,
                    role: user.role || 'user',
                    package_id: user.package_id ? String(user.package_id) : ''
                };
                this.editActionUrl = `/admin/users/${user.id}`;
                this.showEditModal = true;
            },

            openSuspendModal(user) {
                this.suspendUser = {
                    id: user.id,
                    name: user.name,
                    email: user.email
                };
                this.suspendActionUrl = `/admin/users/${user.id}/suspend`;
                this.showSuspendModal = true;
            },

            submitWithLoading(msg) {
                if (window.AppSwal && typeof AppSwal.loading === 'function') {
                    AppSwal.loading('Menyimpan Data...', msg);
                }
            },

            async handleDelete(event, name) {
                if (!window.AppSwal || typeof AppSwal.confirm !== 'function') {
                    if (confirm(`Hapus akun ${name}? Seluruh data perangkat dan riwayat pesan akan dihapus permanen.`)) {
                        event.target.submit();
                    }
                    return;
                }

                const confirmed = await AppSwal.confirm(
                    `Hapus Akun ${name}?`,
                    `Seluruh data perangkat, riwayat pesan, dan API Key milik ${name} akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.`,
                    'Ya, Hapus Akun',
                    true
                );

                if (confirmed) {
                    AppSwal.loading('Menghapus Akun...', 'Sedang membersihkan seluruh data terkait...');
                    event.target.submit();
                }
            },

            async handleActivate(event, name) {
                if (!window.AppSwal || typeof AppSwal.confirm !== 'function') {
                    if (confirm(`Aktifkan kembali akun ${name}?`)) {
                        event.target.submit();
                    }
                    return;
                }

                const confirmed = await AppSwal.confirm(
                    `Aktifkan Kembali Akun ${name}?`,
                    `Status penangguhan akan dicabut dan pengguna dapat kembali menggunakan seluruh layanan WhatsApp.`,
                    'Ya, Aktifkan',
                    false
                );

                if (confirmed) {
                    AppSwal.loading('Mengaktifkan Akun...', 'Memulihkan hak akses pengguna...');
                    event.target.submit();
                }
            }
        }
    }
</script>
@endsection
