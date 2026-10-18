@extends('layouts.app')

@section('title', 'Kirim Pesan WhatsApp — JalurGaib WA Gateway')
@section('header_title', 'Kirim Pesan WhatsApp')
@section('header_subtitle', 'Pengiriman Pesan Tunggal & Broadcast Massal dengan Anti-Ban Delay')

@section('content')
<div x-data="userMessagesApp()" class="space-y-8 font-sans max-w-5xl">

    <!-- Header Telemetry Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="mono-card p-4 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Sisa Kuota Hari Ini</div>
                <div class="text-xl font-extrabold text-mono-50 tabular-nums mt-0.5">
                    {{ number_format($remainingDaily, 0, ',', '.') }} <span class="text-xs text-mono-400 font-normal">/ {{ number_format($dailyLimit, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-mono-800 border border-mono-700 flex items-center justify-center text-mono-200">
                <i class="fa-solid fa-comments text-sm"></i>
            </div>
        </div>

        <div class="mono-card p-4 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Perangkat Siap Kirim</div>
                <div class="text-xl font-extrabold text-emerald-400 tabular-nums mt-0.5">
                    {{ $devices->count() }} <span class="text-xs text-mono-400 font-normal">perangkat online</span>
                </div>
            </div>
            <div class="w-9 h-9 rounded-xl bg-mono-800 border border-mono-700 flex items-center justify-center text-emerald-400">
                <i class="fa-brands fa-whatsapp text-lg"></i>
            </div>
        </div>

        <div class="mono-card p-4 flex items-center justify-between">
            <div>
                <div class="text-[10px] font-mono font-bold text-mono-400 uppercase tracking-widest">Proteksi Pengiriman</div>
                <div class="text-sm font-extrabold text-mono-100 mt-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-shield-halved text-emerald-400 text-xs"></i>
                    <span>Anti-Ban Humanized</span>
                </div>
            </div>
            <a href="{{ route('user.upgrade') }}" class="text-[11px] font-bold text-mono-400 hover:text-white transition flex items-center gap-1">
                <span>Kuota</span> <i class="fa-solid fa-arrow-right text-[9px]"></i>
            </a>
        </div>
    </div>

    <!-- Main Message Form Bento Card -->
    <div class="mono-card p-5 sm:p-7 lg:p-8">
        
        <!-- Mode Switcher -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 pb-5 border-b border-mono-800">
            <div>
                <h3 class="text-base font-extrabold text-mono-50 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane text-mono-300 text-sm"></i>
                    <span>Formulir Pengiriman Pesan</span>
                </h3>
                <p class="text-xs text-mono-400 mt-0.5">Pilih mode pengiriman pesan tunggal atau broadcast banyak kontak sekaligus</p>
            </div>

            <div class="flex gap-1.5 p-1 bg-mono-950 border border-mono-700 rounded-xl flex-shrink-0">
                <button type="button" @click="sendMode = 'single'" :class="sendMode === 'single' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-user text-[11px]"></i>
                    <span>Tunggal</span>
                </button>
                <button type="button" @click="sendMode = 'bulk'" :class="sendMode === 'bulk' ? 'bg-mono-50 text-mono-950 font-bold shadow-sm' : 'text-mono-400 hover:text-white'" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-users text-[11px]"></i>
                    <span>Massal (Bulk)</span>
                </button>
            </div>
        </div>

        <!-- Warm-up & Old Number Advisory -->
        <div class="p-4 bg-mono-900 border border-mono-700/80 rounded-xl mb-6 space-y-2 text-xs">
            <div class="flex items-center gap-2 font-bold text-mono-100">
                <i class="fa-solid fa-lightbulb text-amber-400"></i>
                <span>Saran Efektif: Prioritaskan Nomor WhatsApp yang Sudah Memiliki Riwayat Chat (Aged Number)</span>
            </div>
            <p class="text-mono-300 text-[11px] leading-relaxed">
                Nomor WhatsApp yang sudah lama aktif dan sering dipakai berkomunikasi dua arah memiliki reputasi tinggi di server WhatsApp, sehingga pengiriman pesan tidak mudah dibatasi (Error 463).
            </p>
        </div>

        <!-- The Form -->
        <form @submit.prevent="submitMessage()" class="space-y-5 text-xs">
            
            <!-- Perangkat WhatsApp Dropdown -->
            <div>
                <label class="block text-mono-300 font-bold mb-2 flex items-center gap-1.5">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                    <span>Pilih Perangkat WhatsApp Pengirim</span>
                    <span class="text-red-400">*</span>
                </label>
                @if($devices->isEmpty())
                    <div class="p-4 bg-mono-950 border border-mono-800 rounded-xl text-mono-400 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-amber-400"></i>
                            <span>Tidak ada perangkat WhatsApp yang sedang terhubung online.</span>
                        </div>
                        <a href="{{ route('user.devices') }}" class="px-3 py-1.5 mono-btn-primary font-bold text-xs rounded-lg inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-qrcode text-xs"></i>
                            <span>Hubungkan Perangkat</span>
                        </a>
                    </div>
                @else
                    <select x-model="form.device_id" required class="w-full mono-input px-4 py-3 text-mono-100 font-medium">
                        <option value="">-- Pilih Nomor Pengirim --</option>
                        @foreach($devices as $d)
                            <option value="{{ $d->device_id }}">{{ $d->name }} ({{ $d->phone_number ?? $d->device_id }}) [Online]</option>
                        @endforeach
                    </select>
                @endif
            </div>

            <!-- Single Recipient Input -->
            <div x-show="sendMode === 'single'">
                <label class="block text-mono-300 font-bold mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-address-book text-mono-400"></i>
                    <span>Nomor Tujuan WhatsApp</span>
                    <span class="text-red-400">*</span>
                </label>
                <input type="text" x-model="form.to" placeholder="Contoh: 081234567890 atau 6281234567890" class="w-full mono-input px-4 py-3 font-mono">
                <p class="text-[11px] text-mono-500 mt-1 font-mono">Mendukung format awalan 08xxx maupun 628xxx</p>
            </div>

            <!-- Bulk Recipients Input -->
            <div x-show="sendMode === 'bulk'" x-cloak>
                <label class="block text-mono-300 font-bold mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-list-ol text-mono-400"></i>
                    <span>Daftar Nomor Tujuan (Satu nomor per baris)</span>
                    <span class="text-red-400">*</span>
                </label>
                <textarea x-model="form.recipients_raw" rows="4" placeholder="081234567890&#10;085123456789&#10;628987654321" class="w-full mono-input px-4 py-3 font-mono"></textarea>
                <p class="text-[11px] text-mono-500 mt-1 font-mono">Setiap nomor akan diantrekan dengan delay otomatis untuk menjaga keamanan nomor Anda.</p>
            </div>

            <!-- Message Caption Textarea -->
            <div>
                <label class="block text-mono-300 font-bold mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-comment-dots text-mono-400"></i>
                    <span>Isi Pesan WhatsApp</span>
                    <span class="text-red-400">*</span>
                </label>
                <textarea x-model="form.message" rows="5" required placeholder="Tuliskan isi pesan atau keterangan broadcast Anda di sini..." class="w-full mono-input px-4 py-3 font-medium leading-relaxed"></textarea>
            </div>

            <!-- Optional Image Upload -->
            <div class="p-4 bg-mono-950 rounded-xl border border-mono-700/80 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-mono-300 font-bold text-xs flex items-center gap-2">
                        <i class="fa-regular fa-image text-mono-400"></i>
                        <span>Lampiran Gambar / Media <span class="text-mono-500 font-normal">(Opsional)</span></span>
                    </label>
                    <button type="button" x-show="form.image_preview" @click="clearImage()" class="text-red-400 hover:text-red-300 text-[11px] font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-trash-can text-[10px]"></i>
                        <span>Hapus Gambar</span>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 items-start">
                    <div class="flex-1 w-full">
                        <input type="file" accept="image/*" @change="handleImageUpload($event)" class="w-full text-xs text-mono-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-mono-800 file:text-mono-200 hover:file:bg-mono-700 cursor-pointer">
                        <p class="text-[11px] text-mono-500 mt-1 font-mono">Format yang didukung: JPG, PNG, WEBP (Maksimal 10MB)</p>
                    </div>
                    <template x-if="form.image_preview">
                        <div class="w-20 h-20 rounded-xl border border-mono-700 overflow-hidden bg-mono-900 flex-shrink-0">
                            <img :src="form.image_preview" class="w-full h-full object-cover">
                        </div>
                    </template>
                </div>
            </div>

            <!-- Anti-Ban Delay Control -->
            <div class="p-4 bg-mono-950 rounded-xl border border-mono-700/80">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-mono-300 flex items-center gap-1.5">
                        <i class="fa-solid fa-stopwatch text-mono-400"></i>
                        <span>Anti-Ban Min Delay:</span>
                    </span>
                    <span class="font-mono font-bold text-mono-100 tabular-nums" x-text="form.min_delay + ' ms'"></span>
                </div>
                <input type="range" min="500" max="5000" step="500" x-model="form.min_delay" class="w-full accent-white cursor-pointer">
                <div class="flex items-center justify-between text-[10px] text-mono-500 font-mono mt-1">
                    <span>500ms (Cepat)</span>
                    <span>2500ms (Direkomendasikan)</span>
                    <span>5000ms (Paling Aman)</span>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex items-center justify-end gap-3">
                <button type="submit" :disabled="loading || {{ $devices->isEmpty() ? 'true' : 'false' }}" class="px-6 py-3 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-2 shadow-lg hover:scale-[1.01] transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <template x-if="!loading">
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </template>
                    <template x-if="loading">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                    </template>
                    <span x-text="loading ? 'Mengirimkan Pesan...' : 'Kirim Pesan Sekarang'"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function userMessagesApp() {
        return {
            sendMode: 'single',
            loading: false,
            form: {
                device_id: '{{ $devices->first()->device_id ?? "" }}',
                to: '',
                recipients_raw: '',
                message: '',
                image_base64: null,
                image_preview: null,
                min_delay: 2000
            },
            handleImageUpload(e) {
                const file = e.target.files[0];
                if (!file) return;
                if (file.size > 10 * 1024 * 1024) {
                    AppSwal.toastError('Ukuran file maksimal adalah 10MB.');
                    e.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (event) => {
                    this.form.image_base64 = event.target.result;
                    this.form.image_preview = event.target.result;
                };
                reader.readAsDataURL(file);
            },
            clearImage() {
                this.form.image_base64 = null;
                this.form.image_preview = null;
            },
            async submitMessage() {
                if (!this.form.device_id) {
                    AppSwal.toastError('Pilih perangkat WhatsApp pengirim terlebih dahulu.');
                    return;
                }
                if (!this.form.message.trim()) {
                    AppSwal.toastError('Isi pesan tidak boleh kosong.');
                    return;
                }

                if (this.sendMode === 'single') {
                    if (!this.form.to.trim()) {
                        AppSwal.toastError('Nomor tujuan tidak boleh kosong.');
                        return;
                    }
                    this.loading = true;
                    AppSwal.loading('Mengirimkan Pesan...', 'Menghubungi engine WhatsApp...');
                    try {
                        let res = await fetch('/api/messages/send', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-API-Key': document.querySelector('meta[name="api-key"]')?.content || ''
                            },
                            body: JSON.stringify({
                                device_id: this.form.device_id,
                                to: this.form.to,
                                message: this.form.message,
                                image: this.form.image_base64,
                                delay: Math.round(this.form.min_delay / 1000)
                            })
                        });
                        let json = await res.json();
                        AppSwal.close();
                        this.loading = false;

                        if (json.success) {
                            AppSwal.toastSuccess('Pesan berhasil diproses dan dikirim!');
                            this.form.to = '';
                            this.form.message = '';
                            this.clearImage();
                        } else {
                            AppSwal.error('Gagal Mengirim', json.message || 'Pesan gagal dikirim.');
                        }
                    } catch(e) {
                        AppSwal.close();
                        this.loading = false;
                        AppSwal.error('Error Jaringan', 'Gagal menghubungi server WhatsApp.');
                    }
                } else {
                    let lines = this.form.recipients_raw.split('\n').map(s => s.trim()).filter(s => s.length > 0);
                    if (lines.length === 0) {
                        AppSwal.toastError('Masukkan minimal 1 nomor tujuan.');
                        return;
                    }
                    this.loading = true;
                    AppSwal.loading('Mengantrekan Broadcast...', `Memproses ${lines.length} nomor tujuan...`);
                    try {
                        let res = await fetch('/api/messages/send-bulk', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-API-Key': document.querySelector('meta[name="api-key"]')?.content || ''
                            },
                            body: JSON.stringify({
                                device_id: this.form.device_id,
                                recipients: lines,
                                message: this.form.message,
                                image: this.form.image_base64,
                                delay: Math.round(this.form.min_delay / 1000)
                            })
                        });
                        let json = await res.json();
                        AppSwal.close();
                        this.loading = false;

                        if (json.success) {
                            AppSwal.toastSuccess(`Berhasil mengantrekan ${lines.length} pesan broadcast!`);
                            this.form.recipients_raw = '';
                            this.form.message = '';
                            this.clearImage();
                        } else {
                            AppSwal.error('Gagal Broadcast', json.message || 'Gagal memproses pengiriman massal.');
                        }
                    } catch(e) {
                        AppSwal.close();
                        this.loading = false;
                        AppSwal.error('Error Jaringan', 'Gagal menghubungi server WhatsApp.');
                    }
                }
            }
        }
    }
</script>
@endsection
