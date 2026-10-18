@extends('layouts.app')

@section('title', 'Kelola Testimoni — JalurGaib WA Gateway Admin')
@section('header_title', 'Manajemen Testimoni Landing Page')
@section('header_subtitle', 'Kelola ulasan klien, rating kepuasan, dan testimoni pengguna yang tampil di halaman depan JalurGaib')

@section('content')
<div x-data="testimonialCrudApp()" class="space-y-6">

    <!-- Telemetry Bento KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="mono-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold text-mono-400 tracking-wider">Total Testimoni</p>
                <h3 class="text-2xl font-extrabold text-mono-50 mt-1 font-mono">{{ $stats['total'] }}</h3>
                <p class="text-[11px] text-mono-400 mt-0.5">Semua ulasan tersimpan</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-mono-800/80 border border-mono-700 flex items-center justify-center text-mono-200 text-lg shadow-inner">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>

        <div class="mono-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold text-mono-400 tracking-wider">Tampil di Landing Page</p>
                <h3 class="text-2xl font-extrabold text-mono-50 mt-1 font-mono">{{ $stats['featured'] }}</h3>
                <p class="text-[11px] text-mono-400 mt-0.5">Aktif dilihat pengunjung</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-mono-800/80 border border-mono-700 flex items-center justify-center text-mono-200 text-lg shadow-inner">
                <i class="fa-solid fa-eye"></i>
            </div>
        </div>

        <div class="mono-card p-5 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-mono uppercase font-bold text-mono-400 tracking-wider">Rata-Rata Rating</p>
                <div class="flex items-center gap-2 mt-1">
                    <h3 class="text-2xl font-extrabold text-mono-50 font-mono">{{ $stats['avg_rating'] }}</h3>
                    <div class="flex text-amber-400 text-xs">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
                <p class="text-[11px] text-mono-400 mt-0.5">Kepuasan pengguna API</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-mono-800/80 border border-mono-700 flex items-center justify-center text-mono-200 text-lg shadow-inner">
                <i class="fa-solid fa-star-half-stroke"></i>
            </div>
        </div>
    </div>

    <!-- Control Bar & Actions -->
    <div class="mono-card p-5">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="flex-1 max-w-md relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-mono-400 text-xs"></i>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Cari nama klien, bisnis, atau cuplikan ulasan..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-mono-900 border border-mono-700/80 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition">
            </div>

            <div class="flex items-center gap-3">
                <button @click="openCreateModal()" class="px-5 py-2.5 mono-btn-primary text-xs font-bold flex items-center justify-center gap-2 shadow-sm rounded-xl">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Testimoni</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Testimonials Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($testimonials as $item)
            <div x-show="filterItem('{{ addslashes($item->name) }} {{ addslashes($item->role_or_company ?? '') }} {{ addslashes($item->content) }}')"
                 class="mono-card p-6 flex flex-col justify-between relative group hover:border-mono-500 transition-all duration-200">
                
                <div>
                    <!-- Top Bar: Rating & Status -->
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="flex items-center gap-1 text-amber-400 text-xs">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $item->rating)
                                    <i class="fa-solid fa-star"></i>
                                @else
                                    <i class="fa-regular fa-star text-mono-600"></i>
                                @endif
                            @endfor
                            <span class="ml-1 text-[11px] font-mono font-bold text-mono-300">({{ $item->rating }}.0)</span>
                        </div>

                        <!-- Toggle Featured Status -->
                        <form action="{{ route('admin.testimonials.toggle', $item->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    title="Klik untuk mengubah status visibilitas di landing page"
                                    class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold uppercase transition flex items-center gap-1.5 {{ $item->is_featured ? 'bg-mono-100 text-mono-950 font-extrabold' : 'bg-mono-900 text-mono-400 border border-mono-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->is_featured ? 'bg-mono-950' : 'bg-mono-500' }}"></span>
                                {{ $item->is_featured ? 'Ditampilkan' : 'Disembunyikan' }}
                            </button>
                        </form>
                    </div>

                    <!-- Review Content -->
                    <p class="text-xs text-mono-200 leading-relaxed italic mb-5 line-clamp-4">
                        “{{ $item->content }}”
                    </p>
                </div>

                <div class="pt-4 border-t border-mono-800 flex items-center justify-between gap-3">
                    <!-- Client Profile -->
                    <div class="flex items-center gap-3 min-w-0">
                        @if($item->avatar)
                            <img src="{{ $item->avatar }}" alt="{{ $item->name }}" class="w-9 h-9 rounded-xl object-cover border border-mono-700 flex-shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-xl bg-mono-800 border border-mono-700 text-mono-200 font-mono font-extrabold text-xs flex items-center justify-center flex-shrink-0">
                                {{ $item->initials }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <div class="text-xs font-bold text-mono-50 truncate">{{ $item->name }}</div>
                            <div class="text-[10px] text-mono-400 truncate">{{ $item->role_or_company ?: 'Pengguna Terverifikasi' }}</div>
                        </div>
                    </div>

                    <!-- Actions: Edit & Delete -->
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button @click="openEditModal(@js($item))"
                                class="p-2 rounded-lg text-mono-400 hover:text-mono-100 hover:bg-mono-800 transition"
                                title="Edit Ulasan">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>

                        <form action="{{ route('admin.testimonials.destroy', $item->id) }}"
                              method="POST"
                              class="inline"
                              @submit.prevent="AppSwal.confirm('Hapus Testimoni?', 'Ulasan dari {{ addslashes($item->name) }} akan dihapus secara permanen.', 'Ya, Hapus', true).then(ok => { if (ok) { AppSwal.loading('Menghapus...', ''); $el.submit(); } })">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-2 rounded-lg text-mono-400 hover:text-red-400 hover:bg-mono-800 transition"
                                    title="Hapus Ulasan">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @empty
            <div class="col-span-full mono-card p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-mono-800 border border-mono-700 flex items-center justify-center text-mono-400 text-xl mx-auto mb-3">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h4 class="text-sm font-bold text-mono-100">Belum Ada Testimoni</h4>
                <p class="text-xs text-mono-400 max-w-sm mx-auto mt-1 mb-5">Tambahkan ulasan perdana klien Anda agar landing page terlihat kredibel dan meyakinkan calon pelanggan.</p>
                <button @click="openCreateModal()" class="px-5 py-2.5 mono-btn-primary text-xs font-bold inline-flex items-center gap-2 rounded-xl">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Ulasan Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div x-show="modalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <!-- Backdrop -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="modalOpen = false"
                 class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal Content -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform mono-card shadow-2xl rounded-2xl border border-mono-700 bg-mono-900 z-10">

                <div class="flex items-center justify-between pb-4 border-b border-mono-800">
                    <div>
                        <h3 class="text-sm font-extrabold text-mono-50" x-text="isEdit ? 'Edit Ulasan Klien' : 'Tambah Testimoni Baru'"></h3>
                        <p class="text-xs text-mono-400 mt-0.5">Testimoni aktif akan langsung muncul di halaman utama.</p>
                    </div>
                    <button @click="modalOpen = false" class="p-1.5 text-mono-400 hover:text-white rounded-lg hover:bg-mono-800 transition">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>

                <form :action="isEdit ? '{{ url('admin/testimonials') }}/' + form.id : '{{ route('admin.testimonials.store') }}'"
                      method="POST"
                      class="mt-5 space-y-4 font-sans">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-mono-300 mb-1.5">Nama Klien / User *</label>
                        <input type="text"
                               name="name"
                               x-model="form.name"
                               required
                               placeholder="Contoh: Budi Pratama"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-mono font-bold uppercase text-mono-300 mb-1.5">Peran / Jabatan / Bisnis</label>
                            <input type="text"
                                   name="role_or_company"
                                   x-model="form.role_or_company"
                                   placeholder="Contoh: CTO di KirimCepat"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-mono font-bold uppercase text-mono-300 mb-1.5">Rating Bintang *</label>
                            <select name="rating"
                                    x-model="form.rating"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 focus:outline-none focus:border-mono-400 transition">
                                <option value="5">⭐⭐⭐⭐⭐ (5 Bintang - Sempurna)</option>
                                <option value="4">⭐⭐⭐⭐ (4 Bintang - Sangat Baik)</option>
                                <option value="3">⭐⭐⭐ (3 Bintang - Cukup)</option>
                                <option value="2">⭐⭐ (2 Bintang - Kurang)</option>
                                <option value="1">⭐ (1 Bintang)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-mono-300 mb-1.5">URL Foto Avatar (Opsional)</label>
                        <input type="url"
                               name="avatar"
                               x-model="form.avatar"
                               placeholder="https://images.unsplash.com/... (atau kosongkan untuk inisial)"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition">
                        <p class="text-[10px] text-mono-500 mt-1">Jika dikosongkan, avatar akan otomatis di-generate dari inisial nama.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-mono font-bold uppercase text-mono-300 mb-1.5">Isi Ulasan / Testimoni *</label>
                        <textarea name="content"
                                  x-model="form.content"
                                  rows="4"
                                  required
                                  placeholder="Tulis ulasan klien mengenai kehandalan API, webhook, atau kemudahan setup WhatsApp Gateway..."
                                  class="w-full px-3.5 py-2.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition leading-relaxed"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-2">
                        <div>
                            <label class="block text-xs font-mono font-bold uppercase text-mono-300 mb-1.5">Urutan Prioritas</label>
                            <input type="number"
                                   name="sort_order"
                                   x-model="form.sort_order"
                                   min="0"
                                   class="w-full px-3.5 py-2 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 focus:outline-none focus:border-mono-400 transition">
                            <p class="text-[10px] text-mono-500 mt-1">Angka lebih kecil tampil lebih awal.</p>
                        </div>

                        <div class="flex items-center gap-3 pt-3">
                            <input type="hidden" name="is_featured" value="0">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox"
                                       name="is_featured"
                                       value="1"
                                       x-model="form.is_featured"
                                       class="w-4 h-4 rounded bg-mono-950 border-mono-700 text-mono-50 focus:ring-mono-400 focus:ring-offset-mono-900">
                                <span class="text-xs font-bold text-mono-200">Tampilkan di Landing</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-mono-800">
                        <button type="button" @click="modalOpen = false" class="px-4 py-2 text-xs font-bold text-mono-400 hover:text-white rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 mono-btn-primary text-xs font-bold rounded-xl shadow-sm">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Terbitkan Testimoni'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function testimonialCrudApp() {
    return {
        modalOpen: false,
        isEdit: false,
        searchQuery: '',
        form: {
            id: null,
            name: '',
            role_or_company: '',
            rating: 5,
            avatar: '',
            content: '',
            sort_order: 0,
            is_featured: true
        },
        openCreateModal() {
            this.isEdit = false;
            this.form = {
                id: null,
                name: '',
                role_or_company: '',
                rating: 5,
                avatar: '',
                content: '',
                sort_order: 0,
                is_featured: true
            };
            this.modalOpen = true;
        },
        openEditModal(item) {
            this.isEdit = true;
            this.form = {
                id: item.id,
                name: item.name,
                role_or_company: item.role_or_company || '',
                rating: item.rating,
                avatar: item.avatar || '',
                content: item.content,
                sort_order: item.sort_order || 0,
                is_featured: Boolean(item.is_featured)
            };
            this.modalOpen = true;
        },
        filterItem(text) {
            if (!this.searchQuery.trim()) return true;
            return text.toLowerCase().includes(this.searchQuery.toLowerCase());
        }
    };
}
</script>
@endpush
@endsection
