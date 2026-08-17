@extends('layouts.app')

@section('title', 'Kelola Paket & Tier — JalurGaib WA Gateway Admin')
@section('header_title', 'Manajemen Paket Langganan & Tier')
@section('header_subtitle', 'Atur batasan kuota harian pesan, batas jumlah perangkat WhatsApp, benefit, dan status paket (Active / Coming Soon)')

@section('content')
<div x-data="packageCrudApp()" class="space-y-6">

    <!-- Header Actions Toolbar -->
    <div class="mono-card p-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-extrabold text-mono-50">Daftar Tier & Paket Layanan</h2>
                <p class="text-xs text-mono-400 font-medium mt-0.5">Semua paket yang aktif akan otomatis memberlakukan limit perangkat & kuota harian kepada pengguna.</p>
            </div>

            <!-- Create Button -->
            <button @click="openCreateModal()" class="w-full sm:w-auto px-5 py-2.5 mono-btn-primary text-xs font-bold flex items-center justify-center gap-2 shadow-sm">
                <span>➕</span> Buat Paket Baru
            </button>
        </div>
    </div>

    <!-- Package Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($packages as $pkg)
            <div class="mono-card p-6 flex flex-col justify-between relative overflow-hidden transition hover:border-mono-500">
                
                <!-- Status Badge Corner -->
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2">
                        @if($pkg->status === 'active')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-50 text-mono-950 flex items-center gap-1.5 shadow-sm font-extrabold">
                                <span class="w-1.5 h-1.5 rounded-full bg-mono-950"></span> Active
                            </span>
                        @elseif($pkg->status === 'coming_soon')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-800 text-mono-200 border border-mono-600 flex items-center gap-1.5 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-mono-400 animate-pulse"></span> Coming Soon
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-950 text-mono-400 border border-mono-800 flex items-center gap-1.5 shadow-sm">
                                Inactive
                            </span>
                        @endif

                        @if($pkg->badge)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-mono-900 border border-mono-700 text-mono-300">
                                {{ $pkg->badge }}
                            </span>
                        @endif
                    </div>

                    <div class="text-[11px] font-mono text-mono-400 font-bold">
                        👥 {{ $pkg->users_count }} User
                    </div>
                </div>

                <!-- Package Info -->
                <div>
                    <h3 class="text-base font-extrabold text-mono-50 tracking-tight">{{ $pkg->name }}</h3>
                    <div class="text-mono-400 text-xs font-mono mt-0.5">slug: <span class="text-mono-300">{{ $pkg->slug }}</span></div>
                    
                    @if($pkg->description)
                        <p class="text-xs text-mono-400 mt-2 font-medium leading-relaxed">{{ $pkg->description }}</p>
                    @endif

                    <!-- Price Tag -->
                    <div class="my-4 p-4 rounded-2xl bg-mono-950/80 border border-mono-800">
                        <div class="text-[10px] uppercase font-mono text-mono-400 font-bold tracking-wider">Harga Paket</div>
                        <div class="text-xl font-extrabold text-mono-50 font-mono mt-0.5">
                            @if($pkg->price == 0)
                                <span class="text-mono-50">GRATIS</span>
                                <span class="text-xs font-normal text-mono-400 font-sans">/ selamanya</span>
                            @else
                                <span>Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                <span class="text-xs font-normal text-mono-400 font-sans">/ bulan</span>
                            @endif
                        </div>
                    </div>

                    <!-- Quota Specifications -->
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="p-3 bg-mono-900 border border-mono-800 rounded-xl">
                            <div class="text-[10px] text-mono-400 font-mono uppercase font-bold">Max Perangkat</div>
                            <div class="text-sm font-extrabold text-mono-100 mt-0.5">{{ $pkg->max_devices }} Nomor</div>
                        </div>
                        <div class="p-3 bg-mono-900 border border-mono-800 rounded-xl">
                            <div class="text-[10px] text-mono-400 font-mono uppercase font-bold">Limit Pesan/Hari</div>
                            <div class="text-sm font-extrabold text-mono-100 mt-0.5">{{ number_format($pkg->daily_message_limit, 0, ',', '.') }} Pesan</div>
                        </div>
                    </div>

                    <!-- Benefit List -->
                    <div class="mb-5">
                        <div class="text-[11px] font-bold text-mono-300 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <span>✨</span> Fitur & Benefit:
                        </div>
                        <ul class="space-y-1.5">
                            @if(!empty($pkg->benefits) && is_array($pkg->benefits))
                                @foreach($pkg->benefits as $benefit)
                                    <li class="text-xs text-mono-300 flex items-start gap-2">
                                        <span class="text-mono-100 font-bold">✓</span>
                                        <span class="leading-tight">{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            @else
                                <li class="text-xs text-mono-500 italic">Belum ada daftar benefit khusus.</li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Card Action Footer -->
                <div class="pt-4 border-t border-mono-800 flex items-center justify-between gap-2 mt-auto">
                    <button @click="openEditModal({{ json_encode($pkg) }})" class="flex-1 py-2 mono-btn-secondary text-xs font-bold shadow-sm">
                        ✏️ Edit Paket
                    </button>

                    <form action="{{ route('admin.packages.destroy', $pkg) }}" method="POST" class="inline" @submit.prevent="handleDelete($event, '{{ $pkg->name }}', {{ $pkg->users_count }})">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 bg-mono-950 hover:bg-mono-800 border border-mono-700 text-mono-400 hover:text-white rounded-xl text-xs transition" title="Hapus Paket">
                            🗑️
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-3 mono-card p-12 text-center text-mono-400">
                <div class="text-3xl mb-2">📦</div>
                <p class="font-bold">Belum ada data paket langganan.</p>
                <p class="text-xs text-mono-500 mt-1">Klik tombol "Buat Paket Baru" untuk menambahkan paket Free atau Pro.</p>
            </div>
        @endforelse
    </div>

    <!-- MODAL CREATE PACKAGE -->
    <div x-show="showCreateModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-7 max-w-lg w-full shadow-2xl my-8 max-h-[90vh] overflow-y-auto" @click.outside="showCreateModal = false">
            <h3 class="text-base font-extrabold text-mono-50 mb-1">➕ Buat Paket Baru</h3>
            <p class="text-xs text-mono-400 mb-5 font-medium">Buat paket baru untuk kebutuhan Starter, Pro, Enterprise, atau Coming Soon</p>

            <form action="{{ route('admin.packages.store') }}" method="POST" @submit="submitWithLoading('Menyimpan paket baru...')" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Nama Paket</label>
                        <input type="text" name="name" required placeholder="Contoh: Starter Pro" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Slug Identifier (Opsional)</label>
                        <input type="text" name="slug" placeholder="starter-pro (otomatis)" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Harga (Rp)</label>
                        <input type="number" name="price" required min="0" value="0" placeholder="0 untuk Gratis" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Max Perangkat</label>
                        <input type="number" name="max_devices" required min="1" max="100" value="2" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Limit Pesan/Hari</label>
                        <input type="number" name="daily_message_limit" required min="1" value="50" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Status Paket</label>
                        <select name="status" required class="w-full mono-input px-4 py-2.5 font-medium">
                            <option value="active">🟢 Active (Bisa diakses/digunakan)</option>
                            <option value="coming_soon">⏳ Coming Soon (Segera Hadir / Preview)</option>
                            <option value="inactive">⚪ Inactive (Nonaktif / Arsip)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Badge Label (Opsional)</label>
                        <input type="text" name="badge" placeholder="Contoh: Populer / Gratis / Segera Hadir" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Deskripsi Singkat</label>
                    <input type="text" name="description" placeholder="Deskripsi peruntukan paket ini..." class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Daftar Benefit & Keunggulan (1 baris per benefit)</label>
                    <textarea name="benefits" rows="4" placeholder="2 Slot WhatsApp Device&#10;50 Pesan WhatsApp / Hari&#10;REST API & Webhook Akses&#10;Support Prioritas" class="w-full mono-input px-4 py-2.5 font-medium"></textarea>
                    <p class="text-[10px] text-mono-400 mt-1">Tulis setiap keunggulan paket di baris baru (enter).</p>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showCreateModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm">Simpan Paket</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT PACKAGE -->
    <div x-show="showEditModal" class="fixed inset-0 bg-black/85 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto" x-transition.opacity>
        <div class="bg-mono-900 border border-mono-700 rounded-3xl p-7 max-w-lg w-full shadow-2xl my-8 max-h-[90vh] overflow-y-auto" @click.outside="showEditModal = false">
            <h3 class="text-base font-extrabold text-mono-50 mb-1">✏️ Edit Paket Layanan</h3>
            <p class="text-xs text-mono-400 mb-5 font-medium">Ubah batasan kuota, status paket, dan daftar benefit</p>

            <form :action="editActionUrl" method="POST" @submit="submitWithLoading('Menyimpan perubahan...')" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Nama Paket</label>
                        <input type="text" name="name" x-model="editForm.name" required class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Slug Identifier</label>
                        <input type="text" name="slug" x-model="editForm.slug" required class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Harga (Rp)</label>
                        <input type="number" name="price" x-model="editForm.price" required min="0" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Max Perangkat</label>
                        <input type="number" name="max_devices" x-model="editForm.max_devices" required min="1" max="100" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Limit Pesan/Hari</label>
                        <input type="number" name="daily_message_limit" x-model="editForm.daily_message_limit" required min="1" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Status Paket</label>
                        <select name="status" x-model="editForm.status" required class="w-full mono-input px-4 py-2.5 font-medium">
                            <option value="active">🟢 Active (Bisa diakses/digunakan)</option>
                            <option value="coming_soon">⏳ Coming Soon (Segera Hadir / Preview)</option>
                            <option value="inactive">⚪ Inactive (Nonaktif / Arsip)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Badge Label (Opsional)</label>
                        <input type="text" name="badge" x-model="editForm.badge" placeholder="Contoh: Populer / Gratis" class="w-full mono-input px-4 py-2.5 font-medium">
                    </div>
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Deskripsi Singkat</label>
                    <input type="text" name="description" x-model="editForm.description" class="w-full mono-input px-4 py-2.5 font-medium">
                </div>

                <div>
                    <label class="block text-mono-300 font-bold mb-1.5">Daftar Benefit & Keunggulan (1 baris per benefit)</label>
                    <textarea name="benefits" x-model="editForm.benefits_text" rows="4" class="w-full mono-input px-4 py-2.5 font-medium"></textarea>
                    <p class="text-[10px] text-mono-400 mt-1">Tulis setiap keunggulan paket di baris baru (enter).</p>
                </div>

                <div class="flex gap-3 pt-3">
                    <button type="button" @click="showEditModal = false" class="flex-1 py-2.5 mono-btn-secondary text-xs">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 mono-btn-primary text-xs shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function packageCrudApp() {
        return {
            showCreateModal: false,
            showEditModal: false,
            editForm: {
                id: null,
                name: '',
                slug: '',
                description: '',
                price: 0,
                max_devices: 2,
                daily_message_limit: 50,
                status: 'active',
                badge: '',
                benefits_text: ''
            },
            editActionUrl: '',

            openCreateModal() {
                this.showCreateModal = true;
            },

            openEditModal(pkg) {
                let benefitsText = '';
                if (Array.isArray(pkg.benefits)) {
                    benefitsText = pkg.benefits.join('\n');
                }

                this.editForm = {
                    id: pkg.id,
                    name: pkg.name,
                    slug: pkg.slug,
                    description: pkg.description || '',
                    price: pkg.price || 0,
                    max_devices: pkg.max_devices || 2,
                    daily_message_limit: pkg.daily_message_limit || 50,
                    status: pkg.status || 'active',
                    badge: pkg.badge || '',
                    benefits_text: benefitsText
                };
                this.editActionUrl = `/admin/packages/${pkg.id}`;
                this.showEditModal = true;
            },

            submitWithLoading(msg) {
                AppSwal.loading('Menyimpan Data...', msg);
            },

            async handleDelete(event, name, usersCount) {
                let textDesc = `Apakah Anda yakin ingin menghapus paket '${name}'?`;
                if (usersCount > 0) {
                    textDesc += ` Perhatian: Ada ${usersCount} pengguna yang terhubung ke paket ini. Mereka akan dialihkan otomatis ke paket Free standar.`;
                }

                const confirmed = await AppSwal.confirm(
                    `Hapus Paket ${name}?`,
                    textDesc,
                    'Ya, Hapus Paket',
                    true
                );

                if (confirmed) {
                    AppSwal.loading('Menghapus Paket...', 'Sedang memproses...');
                    event.target.submit();
                }
            }
        }
    }
</script>
@endsection
