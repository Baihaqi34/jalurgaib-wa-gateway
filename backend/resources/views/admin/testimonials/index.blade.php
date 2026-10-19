@extends('layouts.app')

@section('title', 'Kelola Testimoni — JalurGaib WA Gateway Admin')
@section('header_title', 'Manajemen Testimoni')
@section('header_subtitle', 'Kelola ulasan klien, rating kepuasan, dan testimoni pengguna yang tampil di halaman depan JalurGaib')

@section('content')
<div x-data="testimonialCrudApp()" class="space-y-6">

    <!-- Telemetry Bento KPI Cards (3-col responsive) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
        <!-- Total Testimoni -->
        <div class="mono-card p-4 sm:p-5 flex items-center justify-between transition hover:border-mono-600 group">
            <div>
                <p class="text-[10px] sm:text-xs font-mono uppercase font-bold text-mono-400 tracking-wider">Total Testimoni</p>
                <h3 class="text-xl sm:text-2xl font-extrabold text-mono-50 mt-1 font-mono tabular-nums">{{ $stats['total'] }}</h3>
                <p class="text-[10px] sm:text-[11px] text-mono-400 mt-0.5">Semua ulasan tersimpan</p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-mono-200 text-sm sm:text-base group-hover:scale-105 transition shadow-sm">
                <i class="fa-solid fa-comments"></i>
            </div>
        </div>

        <!-- Tampil di Landing Page -->
        <div class="mono-card p-4 sm:p-5 flex items-center justify-between transition hover:border-mono-600 group">
            <div>
                <p class="text-[10px] sm:text-xs font-mono uppercase font-bold text-mono-400 tracking-wider">Aktif di Landing</p>
                <h3 class="text-xl sm:text-2xl font-extrabold text-mono-50 mt-1 font-mono tabular-nums">{{ $stats['featured'] }}</h3>
                <p class="text-[10px] sm:text-[11px] text-mono-400 mt-0.5">Dilihat calon pelanggan</p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-mono-100 text-sm sm:text-base group-hover:scale-105 transition shadow-sm">
                <i class="fa-solid fa-eye"></i>
            </div>
        </div>

        <!-- Rata-Rata Rating -->
        <div class="mono-card p-4 sm:p-5 flex items-center justify-between transition hover:border-mono-600 group">
            <div>
                <p class="text-[10px] sm:text-xs font-mono uppercase font-bold text-mono-400 tracking-wider">Rata-Rata Rating</p>
                <div class="flex items-center gap-2 mt-1">
                    <h3 class="text-xl sm:text-2xl font-extrabold text-mono-50 font-mono tabular-nums">{{ $stats['avg_rating'] }}</h3>
                    <div class="flex text-amber-400 text-xs">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                    </div>
                </div>
                <p class="text-[10px] sm:text-[11px] text-mono-400 mt-0.5">Kepuasan klien API</p>
            </div>
            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-amber-400 text-sm sm:text-base group-hover:scale-105 transition shadow-sm">
                <i class="fa-solid fa-star-half-stroke"></i>
            </div>
        </div>
    </div>

    <!-- Control Bar & Actions (Unified 44px Control Bar) -->
    <div class="mono-card p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Search Input (44px) -->
            <div class="flex-1 max-w-md relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-mono-400 text-xs">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Cari nama klien, bisnis, atau ulasan..."
                       class="w-full h-11 pl-9 pr-4 rounded-xl bg-mono-950/80 border border-mono-700 hover:border-mono-600 focus:border-mono-400 text-xs text-mono-100 placeholder-mono-500 outline-none transition font-medium">
            </div>

            <!-- Create Button (44px) -->
            <button @click="openCreateModal()" class="h-11 px-5 rounded-xl mono-btn-primary text-xs font-extrabold inline-flex items-center justify-center gap-2 shadow-sm whitespace-nowrap">
                <i class="fa-solid fa-plus text-[11px]"></i>
                <span>Tambah Testimoni</span>
            </button>
        </div>
    </div>

    <!-- Testimonials Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
        @forelse($testimonials as $item)
            <div x-show="filterItem('{{ addslashes($item->name) }} {{ addslashes($item->role_or_company ?? '') }} {{ addslashes($item->content) }}')"
                 class="mono-card p-5 sm:p-6 flex flex-col justify-between relative group hover:border-mono-500 transition-all duration-200">
                
                <div>
                    <!-- Top Bar: Rating & Status -->
                    <div class="flex items-center justify-between gap-2 mb-4">
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
                                    class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold uppercase transition flex items-center gap-1.5 shadow-sm whitespace-nowrap {{ $item->is_featured ? 'bg-mono-100 text-mono-950 font-extrabold' : 'bg-mono-950 text-mono-400 border border-mono-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $item->is_featured ? 'bg-mono-950' : 'bg-mono-500' }}"></span>
                                <span>{{ $item->is_featured ? 'Featured' : 'Draft' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Client Info -->
                    <div class="flex items-center gap-3 mb-3.5">
                        <div class="w-9 h-9 rounded-xl bg-mono-800 border border-mono-700 flex items-center justify-center text-xs font-bold text-mono-100 font-mono shrink-0 shadow-sm">
                            @if($item->avatar)
                                <img src="{{ $item->avatar }}" alt="{{ $item->name }}" class="w-full h-full object-cover rounded-xl">
                            @else
                                {{ $item->initials }}
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-mono-50 text-sm truncate">{{ $item->name }}</h4>
                            <p class="text-[11px] text-mono-400 truncate">{{ $item->role_or_company ?? 'Pengguna JalurGaib' }}</p>
                        </div>
                    </div>

                    <!-- Testimonial Content -->
                    <p class="text-xs text-mono-300 leading-relaxed italic bg-mono-950/60 p-3.5 rounded-xl border border-mono-800/80 mb-4">
                        "{{ $item->content }}"
                    </p>
                </div>

                <!-- Footer Actions -->
                <div class="pt-3 border-t border-mono-800 flex items-center justify-between text-xs mt-auto">
                    <span class="text-[10px] text-mono-500 font-mono">Urutan: #{{ $item->sort_order }}</span>
                    <div class="flex items-center gap-1.5">
                        <button @click="openEditModal({{ json_encode($item) }})"
                                class="h-8 px-3 rounded-lg mono-btn-secondary text-[11px] font-bold shadow-sm inline-flex items-center justify-center gap-1">
                            <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                        </button>
                        <form action="{{ route('admin.testimonials.destroy', $item->id) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus ulasan dari {{ addslashes($item->name) }} secara permanen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="h-8 px-2.5 rounded-lg bg-mono-950 hover:bg-mono-800 border border-mono-700 text-mono-400 hover:text-white text-[11px] font-bold transition shadow-sm inline-flex items-center justify-center"
                                    title="Hapus Testimoni">
                                <i class="fa-solid fa-trash-can text-[10px]"></i>
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
                <p class="text-xs text-mono-400 max-w-sm mx-auto mt-1 mb-5">Tambahkan ulasan klien Anda agar landing page terlihat meyakinkan.</p>
                <button @click="openCreateModal()" class="px-5 py-2.5 mono-btn-primary text-xs font-bold inline-flex items-center gap-2 rounded-xl shadow-sm">
                    <i class="fa-solid fa-plus text-xs"></i> Tambah Ulasan Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Form (Create / Edit) -->
    <div x-show="modalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center">
            <!-- Backdrop -->
            <div x-show="modalOpen"
                 x-transition.opacity
                 @click="modalOpen = false"
                 class="fixed inset-0 bg-black/85 backdrop-blur-sm transition-opacity"></div>

            <!-- Modal Content -->
            <div x-show="modalOpen"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full max-w-lg p-6 sm:p-7 my-8 overflow-y-auto max-h-[90vh] scrollbar-thin text-left align-middle transition-all transform mono-card shadow-2xl rounded-3xl border border-mono-700 bg-mono-900 z-10">

                <div class="flex items-center justify-between pb-4 border-b border-mono-800">
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold text-mono-50" x-text="isEdit ? 'Edit Ulasan Klien' : 'Tambah Testimoni Baru'"></h3>
                        <p class="text-xs text-mono-400 mt-0.5">Testimoni aktif akan langsung tampil di halaman depan.</p>
                    </div>
                    <button @click="modalOpen = false" class="w-8 h-8 rounded-xl bg-mono-800 hover:bg-mono-700 text-mono-400 hover:text-white flex items-center justify-center transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                </div>

                <form :action="isEdit ? '{{ url('admin/testimonials') }}/' + form.id : '{{ route('admin.testimonials.store') }}'"
                      method="POST"
                      class="mt-5 space-y-4 text-xs font-sans">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Nama Klien / Bisnis *</label>
                        <input type="text"
                               name="name"
                               x-model="form.name"
                               required
                               placeholder="Contoh: Budi Pratama"
                               class="w-full h-10 px-3.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition font-medium">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-mono-300 font-bold mb-1.5">Peran / Jabatan / Perusahaan</label>
                            <input type="text"
                                   name="role_or_company"
                                   x-model="form.role_or_company"
                                   placeholder="Contoh: CTO di KirimCepat"
                                   class="w-full h-10 px-3.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition font-medium">
                        </div>

                        <div>
                            <label class="block text-mono-300 font-bold mb-1.5">Rating Bintang *</label>
                            <select name="rating"
                                    x-model="form.rating"
                                    required
                                    class="w-full h-10 px-3 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 focus:outline-none focus:border-mono-400 transition font-medium">
                                <option value="5">⭐⭐⭐⭐⭐ (5 Bintang - Sempurna)</option>
                                <option value="4">⭐⭐⭐⭐ (4 Bintang - Sangat Baik)</option>
                                <option value="3">⭐⭐⭐ (3 Bintang - Cukup)</option>
                                <option value="2">⭐⭐ (2 Bintang - Kurang)</option>
                                <option value="1">⭐ (1 Bintang)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">URL Foto Avatar (Opsional)</label>
                        <input type="url"
                               name="avatar"
                               x-model="form.avatar"
                               placeholder="https://images.unsplash.com/... (atau kosongkan untuk inisial)"
                               class="w-full h-10 px-3.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition font-medium">
                        <p class="text-[10px] text-mono-400 mt-1">Jika dikosongkan, avatar otomatis diambil dari inisial nama.</p>
                    </div>

                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5">Isi Ulasan / Testimoni *</label>
                        <textarea name="content"
                                  x-model="form.content"
                                  rows="4"
                                  required
                                  placeholder="Tulis ulasan klien mengenai kehandalan API, webhook, atau kemudahan setup WhatsApp Gateway..."
                                  class="w-full p-3.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition leading-relaxed font-medium"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-center pt-1">
                        <div>
                            <label class="block text-mono-300 font-bold mb-1.5">Urutan Prioritas</label>
                            <input type="number"
                                   name="sort_order"
                                   x-model="form.sort_order"
                                   min="0"
                                   class="w-full h-10 px-3.5 rounded-xl bg-mono-950 border border-mono-700 text-xs text-mono-100 focus:outline-none focus:border-mono-400 transition font-medium">
                            <p class="text-[10px] text-mono-400 mt-1">Angka lebih kecil tampil lebih awal.</p>
                        </div>

                        <div class="pt-2 sm:pt-4">
                            <input type="hidden" name="is_featured" value="0">
                            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                <input type="checkbox"
                                       name="is_featured"
                                       value="1"
                                       x-model="form.is_featured"
                                       class="w-4 h-4 rounded bg-mono-950 border-mono-700 text-mono-50 focus:ring-0">
                                <span class="text-xs font-bold text-mono-200">Tampilkan di Landing Page</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-mono-800">
                        <button type="button" @click="modalOpen = false" class="flex-1 sm:flex-none px-4 py-2.5 mono-btn-secondary text-xs rounded-xl font-bold">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 mono-btn-primary text-xs font-extrabold rounded-xl shadow-sm">
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
