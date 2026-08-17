@extends('layouts.app')

@section('title', 'Manajemen Pengguna (CRUD) — JalurGaib WA Gateway Admin')
@section('header_title', 'Manajemen Akun Pengguna & Admin')
@section('header_subtitle', 'Kelola akun, role, dan status penangguhan (Suspend / Ban) pengguna')

@section('content')
<div x-data="userCrudApp()" class="space-y-6">

    <!-- Header Actions & Search Toolbar -->
    <div class="mono-card p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            
            <!-- Filters -->
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                           class="w-full mono-input px-4 py-2 text-xs placeholder-mono-500 font-medium">
                </div>

                <select name="role" onchange="this.form.submit()" class="mono-input px-3.5 py-2 text-xs font-medium">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>👑 Admin (Aplikator)</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>👤 User (Tenant)</option>
                </select>

                <button type="submit" class="px-4 py-2 mono-btn-secondary text-xs font-bold shadow-sm">
                    Filter
                </button>
            </form>

            <!-- Create Button -->
            <button @click="openCreateModal()" class="w-full sm:w-auto px-5 py-2.5 mono-btn-primary text-xs font-bold flex items-center justify-center gap-2 shadow-sm">
                <span>➕</span> Tambah Akun Baru
            </button>
        </div>
    </div>

    <!-- Users Table Card -->
    <div class="mono-card p-7">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-mono-400 border-b border-mono-700 pb-3.5 font-mono">
                    <tr>
                        <th class="pb-3.5 font-bold uppercase tracking-wider">NAMA & EMAIL</th>
                        <th class="pb-3.5 font-bold uppercase tracking-wider">ROLE</th>
                        <th class="pb-3.5 font-bold uppercase tracking-wider">STATUS AKUN</th>
                        <th class="pb-3.5 font-bold uppercase tracking-wider">PAKET AKTIF</th>
                        <th class="pb-3.5 font-bold uppercase tracking-wider text-center">DEVICE</th>
                        <th class="pb-3.5 font-bold uppercase tracking-wider text-center">API KEYS</th>
                        <th class="pb-3.5 font-bold uppercase tracking-wider text-center">PESAN TERKIRIM</th>
                        <th class="pb-3.5 font-bold uppercase tracking-wider text-right">AKSI & KONTROL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mono-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-mono-900 transition">
                            <td class="py-4">
                                <div class="font-bold text-mono-50 flex items-center gap-2">
                                    <span>{{ $user->isAdmin() ? '👑' : '👤' }}</span>
                                    <span>{{ $user->name }}</span>
                                </div>
                                <div class="text-mono-400 font-mono text-[11px] mt-0.5">{{ $user->email }}</div>
                            </td>
                            <td class="py-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm bg-mono-800 text-mono-200 border border-mono-700">
                                    {{ $user->isAdmin() ? '👑 Super Admin' : '👤 User Tenant' }}
                                </span>
                            </td>
                            <td class="py-4">
                                @if($user->isSuspended())
                                    <div>
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-950 text-mono-200 border border-mono-600 flex items-center gap-1.5 max-w-fit shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-mono-400 animate-pulse"></span> Suspended
                                        </span>
                                        @if($user->suspend_reason)
                                            <div class="text-[10px] text-mono-400 mt-1 truncate max-w-[160px]" title="{{ $user->suspend_reason }}">
                                                "{{ $user->suspend_reason }}"
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-50 text-mono-950 flex items-center gap-1.5 max-w-fit shadow-sm font-extrabold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-mono-950"></span> Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4">
                                @php $pkg = $user->package; @endphp
                                @if($pkg)
                                    <div class="flex flex-col gap-0.5">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-mono-900 border border-mono-700 text-mono-200 max-w-fit">
                                            {{ $pkg->badge ?? $pkg->name }}
                                        </span>
                                        <span class="text-[10px] text-mono-500 font-mono">{{ $pkg->max_devices }} dev · {{ number_format($pkg->daily_message_limit, 0, ',', '.') }} msg/hari</span>
                                    </div>
                                @else
                                    <span class="text-[10px] text-mono-500 italic">—</span>
                                @endif
                            </td>
                            <td class="py-4 text-center font-bold text-mono-100 text-sm">{{ $user->devices_count }}</td>
                            <td class="py-4 text-center font-bold text-mono-100 text-sm">{{ $user->api_keys_count }}</td>
                            <td class="py-4 text-center font-bold text-mono-100 text-sm">{{ $user->messages_count ?? 0 }}</td>
                            <td class="py-4 text-right">
                                <div class="flex items-center justify-end gap-2 flex-wrap">
                                    
                                    <!-- Edit Button -->
                                    <button @click="openEditModal({{ json_encode($user) }})" class="px-3 py-1.5 mono-btn-secondary text-[11px] font-bold shadow-sm">
                                        Edit
                                    </button>

                                    <!-- Suspend / Activate Toggle -->
                                    @if($user->id !== auth()->id())
                                        @if($user->isSuspended())
                                            <form action="{{ route('admin.users.activate', $user) }}" method="POST" class="inline" @submit.prevent="handleActivate($event, '{{ $user->name }}')">
                                                @csrf
                                                <button type="submit" class="px-3 py-1.5 bg-mono-50 hover:bg-mono-200 text-mono-950 rounded-xl text-[11px] font-bold transition shadow-sm">
                                                    ✓ Aktifkan
                                                </button>
                                            </form>
                                        @else
                                            <button @click="openSuspendModal({{ json_encode($user) }})" class="px-3 py-1.5 bg-mono-800 hover:bg-mono-700 border border-mono-600 text-mono-200 rounded-xl text-[11px] font-bold transition shadow-sm">
                                                ⛔ Suspend
                                            </button>
                                        @endif

                                        <!-- Delete Button with SweetAlert2 Confirmation -->
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" @submit.prevent="handleDelete($event, '{{ $user->name }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-mono-950 hover:bg-mono-800 border border-mono-700 text-mono-400 hover:text-white rounded-xl text-[11px] font-bold transition shadow-sm">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-mono-500 font-semibold">
                                Tidak ada data pengguna yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>
    </div>

    <!-- MODAL CREATE USER -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-7 max-w-md w-full shadow-2xl" @click.outside="showCreateModal = false">
            <h3 class="text-base font-extrabold text-mono-50 mb-1">➕ Tambah Akun Baru</h3>
            <p class="text-xs text-mono-400 mb-5 font-medium">Buat akun untuk Super Admin (Aplikator) atau Tenant Pengguna</p>

            <form action="{{ route('admin.users.store') }}" method="POST" @submit="submitWithLoading('Menyimpan akun baru...')" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Nama Lengkap / Nama Bisnis</label>
                    <input type="text" name="name" required placeholder="Contoh: Toko Online Abadi" class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Alamat Email</label>
                    <input type="email" name="email" required placeholder="nama@domain.com" class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Role Akses Akun</label>
                    <select name="role" required class="w-full mono-input px-4 py-2.5 font-medium">
                        <option value="user">👤 User (Tenant Pengguna Gateway)</option>
                        <option value="admin">👑 Admin (Aplikator Super Admin)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Paket / Tier Layanan</label>
                    <select name="package_id" class="w-full mono-input px-4 py-2.5 font-medium">
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}" {{ $pkg->slug === 'free' ? 'selected' : '' }}>
                                📦 {{ $pkg->name }}
                                @if($pkg->price == 0) (Gratis) @else (Rp {{ number_format($pkg->price, 0, ',', '.') }}/bln) @endif
                                — Maks {{ $pkg->max_devices }} Device, {{ number_format($pkg->daily_message_limit, 0, ',', '.') }} Pesan/Hari
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showCreateModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT USER -->
    <div x-show="showEditModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-7 max-w-md w-full shadow-2xl" @click.outside="showEditModal = false">
            <h3 class="text-base font-extrabold text-mono-50 mb-1">✏️ Edit Akun Pengguna</h3>
            <p class="text-xs text-mono-400 mb-5 font-medium">Perbarui informasi nama, email, role, atau ganti password</p>

            <form :action="editActionUrl" method="POST" @submit="submitWithLoading('Memperbarui akun...')" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="editForm.name" required class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Alamat Email</label>
                    <input type="email" name="email" x-model="editForm.email" required class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Ganti Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Role Akses Akun</label>
                    <select name="role" x-model="editForm.role" required class="w-full mono-input px-4 py-2.5 font-medium">
                        <option value="user">👤 User (Tenant Pengguna Gateway)</option>
                        <option value="admin">👑 Admin (Aplikator Super Admin)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Upgrade / Ganti Paket Layanan</label>
                    <select name="package_id" x-model="editForm.package_id" class="w-full mono-input px-4 py-2.5 font-medium">
                        <option value="">— Tidak Diubah —</option>
                        @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}">
                                📦 {{ $pkg->name }}
                                @if($pkg->price == 0) (Gratis) @else (Rp {{ number_format($pkg->price, 0, ',', '.') }}/bln) @endif
                                — {{ $pkg->max_devices }} Dev, {{ number_format($pkg->daily_message_limit, 0, ',', '.') }} Pesan/Hari
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-mono-400 mt-1">Biarkan kosong jika tidak ingin mengganti paket user ini.</p>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL SUSPEND / BAN USER -->
    <div x-show="showSuspendModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-7 max-w-md w-full shadow-2xl" @click.outside="showSuspendModal = false">
            <h3 class="text-base font-extrabold text-mono-50 mb-1">⛔ Suspend / Tangguhkan Akun</h3>
            <p class="text-xs text-mono-400 mb-5 font-medium">Pengguna tidak akan bisa mengirim pesan, scan device, atau menggunakan API Key.</p>

            <form :action="suspendActionUrl" method="POST" @submit="submitWithLoading('Menangguhkan akun...')" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Akun Target:</label>
                    <div class="p-3.5 bg-mono-950 rounded-xl text-mono-100 font-bold flex items-center justify-between border border-mono-700">
                        <span x-text="suspendUser.name"></span>
                        <span class="text-mono-400 font-mono text-[11px]" x-text="suspendUser.email"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Alasan / Pengumuman Penangguhan:</label>
                    <textarea name="reason" rows="3" required placeholder="Contoh: Akun Anda ditangguhkan sementara karena terindikasi mengirimkan pesan spam massal tanpa jeda anti-ban."
                              class="w-full mono-input px-4 py-2.5 font-medium"></textarea>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showSuspendModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm">Tangguhkan Akun</button>
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
                AppSwal.loading('Menyimpan Data...', msg);
            },

            async handleDelete(event, name) {
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
