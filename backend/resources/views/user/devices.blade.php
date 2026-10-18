@extends('layouts.app')

@section('title', 'Perangkat WhatsApp Saya — JalurGaib WA Gateway')
@section('header_title', 'Perangkat WhatsApp')
@section('header_subtitle', 'Kelola Koneksi WhatsApp, Scan QR Code & Pantau Status Perangkat')

@section('content')
<div x-data="userDevicesApp()" x-init="initDevices()" class="space-y-8 font-sans max-w-6xl">

    <!-- Top Telemetry & Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 mono-card">
        <div>
            <div class="flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-emerald-400 text-xl"></i>
                <h3 class="text-base font-extrabold text-mono-50 tracking-tight">Daftar Perangkat Terdaftar</h3>
            </div>
            <p class="text-xs text-mono-400 mt-1">
                Kapasitas Slot Perangkat: 
                <span class="font-mono font-bold text-mono-100 tabular-nums">{{ $devicesCount }}</span> dari <span class="font-mono font-bold text-mono-100 tabular-nums">{{ $maxDevices }}</span> perangkat aktif
            </p>
        </div>

        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-between sm:justify-end">
            <button type="button" @click="fetchDevices()" class="px-3.5 py-2.5 rounded-xl bg-mono-850 hover:bg-mono-800 border border-mono-700 text-mono-300 hover:text-white text-xs font-semibold transition inline-flex items-center gap-1.5" title="Refresh Status">
                <i class="fa-solid fa-arrows-rotate text-xs" :class="refreshing ? 'fa-spin' : ''"></i>
                <span>Segarkan</span>
            </button>

            @if($devicesCount < $maxDevices)
                <button type="button" @click="addDeviceModal()" class="px-4 py-2.5 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-2 shadow-md hover:scale-[1.01] transition">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Perangkat</span>
                </button>
            @else
                <a href="{{ route('user.upgrade') }}" class="px-4 py-2.5 rounded-xl mono-btn-secondary font-bold text-xs inline-flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-crown text-amber-400 text-xs"></i>
                    <span>Upgrade Slot Perangkat</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Devices Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <template x-for="d in devices" :key="d.id">
            <div class="mono-card p-6 flex flex-col justify-between hover:border-mono-600 transition">
                <div>
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-emerald-400 text-lg shadow-sm">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-mono-50" x-text="d.name"></h4>
                                <div class="text-[11px] font-mono text-mono-300 font-semibold flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-phone text-[9px] text-mono-500"></i>
                                    <span x-text="d.phone_number || 'Belum Terisi'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            <template x-if="d.status === 'connected'">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Online</span>
                                </span>
                            </template>
                            <template x-if="d.status === 'scan_qr'">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-qrcode text-[10px]"></i>
                                    <span>Scan QR</span>
                                </span>
                            </template>
                            <template x-if="d.status === 'disconnected' || !d.status">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-mono-800 text-mono-400 border border-mono-700 inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-power-off text-[10px]"></i>
                                    <span>Offline</span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Telemetry Details -->
                    <div class="p-3.5 bg-mono-950 rounded-xl border border-mono-800 space-y-2 mb-4 text-xs font-mono">
                        <div class="flex items-center justify-between">
                            <span class="text-mono-500 text-[11px]">Nomor WhatsApp:</span>
                            <span class="text-mono-100 font-bold tabular-nums" x-text="d.phone_number || 'Belum Terhubung'"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-mono-500 text-[11px]">Device ID:</span>
                            <span class="text-mono-400 text-[11px]" x-text="d.device_id"></span>
                        </div>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="pt-4 border-t border-mono-800 flex items-center justify-between gap-2 mt-auto">
                    <div>
                        <button type="button" @click="deleteDevice(d.id)" class="p-2 text-mono-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Hapus Perangkat">
                            <i class="fa-regular fa-trash-can text-xs"></i>
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <template x-if="d.status !== 'connected'">
                            <button type="button" @click="connectDevice(d.id)" class="px-3.5 py-1.5 rounded-lg mono-btn-primary font-bold text-xs inline-flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-qrcode text-xs"></i>
                                <span>Scan QR Code</span>
                            </button>
                        </template>

                        <template x-if="d.status === 'connected'">
                            <button type="button" @click="disconnectDevice(d.id)" class="px-3 py-1.5 rounded-lg bg-mono-800 hover:bg-mono-700 border border-mono-700 text-mono-300 hover:text-white text-xs font-semibold transition inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-power-off text-[11px]"></i>
                                <span>Putuskan</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="devices.length === 0" class="col-span-full mono-card p-12 text-center text-mono-400">
            <i class="fa-brands fa-whatsapp text-4xl mb-3 block text-mono-600"></i>
            <h4 class="text-sm font-bold text-mono-200">Belum ada perangkat yang terdaftar</h4>
            <p class="text-xs text-mono-500 mt-1 mb-5">Tambahkan perangkat baru untuk mulai menghubungkan WhatsApp Anda.</p>
            <button type="button" @click="addDeviceModal()" class="px-4 py-2 mono-btn-primary text-xs font-bold rounded-xl inline-flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Perangkat Baru</span>
            </button>
        </div>
    </div>

    <!-- QR SCAN MODAL -->
    <div x-show="qrModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" x-cloak>
        <div @click.away="closeQrModal()" class="w-full max-w-sm mono-card p-6 space-y-4 text-center border-mono-600 shadow-2xl">
            <div class="flex items-center justify-between pb-3 border-b border-mono-800">
                <div class="font-bold text-xs text-mono-100 flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
                    <span>Pindai QR Code WhatsApp</span>
                </div>
                <button type="button" @click="closeQrModal()" class="text-mono-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <p class="text-[11px] text-mono-400 leading-relaxed">
                Buka WhatsApp di HP Anda &gt; <strong>Setelan</strong> &gt; <strong>Perangkat Tertaut</strong> &gt; <strong>Tautkan Perangkat</strong>.
            </p>

            <div class="w-64 h-64 mx-auto p-3 bg-white rounded-2xl flex items-center justify-center shadow-lg">
                <div id="qrcode-container" class="w-full h-full flex items-center justify-center"></div>
            </div>

            <div class="text-[11px] font-mono text-mono-400 flex items-center justify-center gap-2">
                <i class="fa-solid fa-spinner fa-spin text-mono-300"></i>
                <span>Menunggu scan dari HP Anda...</span>
            </div>

            <button type="button" @click="closeQrModal()" class="w-full py-2.5 rounded-xl bg-mono-800 hover:bg-mono-700 text-mono-200 text-xs font-semibold transition">
                Tutup Jendela
            </button>
        </div>
    </div>
</div>

<script>
    function userDevicesApp() {
        return {
            devices: @json($devices),
            refreshing: false,
            qrModalOpen: false,
            activeDeviceId: null,
            pollTimer: null,

            getApiHeaders(contentType = false) {
                const h = {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'X-API-Key': document.querySelector('meta[name="api-key"]')?.content || ''
                };
                if (contentType) h['Content-Type'] = 'application/json';
                return h;
            },

            async initDevices() {
                // Initial loaded from blade
            },

            async fetchDevices() {
                this.refreshing = true;
                try {
                    let res = await fetch('/api/devices', {
                        headers: this.getApiHeaders(false)
                    });
                    let json = await res.json();
                    if (json.success && json.data) {
                        this.devices = json.data;
                    }
                } catch(e) {
                    console.error("Gagal refresh", e);
                } finally {
                    this.refreshing = false;
                }
            },

            async addDeviceModal() {
                const { value: formValues } = await CustomSwal.fire({
                    title: 'Tambah Perangkat WhatsApp',
                    html: `
                        <div class="text-left font-sans space-y-3.5 pt-1">
                            <p class="text-mono-400 text-xs leading-relaxed">
                                Masukkan nama label dan nomor WhatsApp aktif yang akan dihubungkan ke gateway:
                            </p>
                            <div>
                                <label class="block font-bold text-mono-200 text-[11px] uppercase tracking-wider mb-1.5">
                                    Nama / Label Perangkat <span class="text-red-400">*</span>
                                </label>
                                <input id="swal-input-name" class="swal2-input !m-0 !w-full !box-border !bg-mono-950 !border-mono-700 !text-mono-100 !text-xs !rounded-xl !py-2.5 !px-3 font-sans focus:!border-mono-400" placeholder="Contoh: CS Utama / Notifikasi Toko">
                            </div>
                            <div>
                                <label class="block font-bold text-mono-200 text-[11px] uppercase tracking-wider mb-1.5">
                                    Nomor HP WhatsApp <span class="text-red-400">*</span>
                                </label>
                                <input id="swal-input-phone" class="swal2-input !m-0 !w-full !box-border !bg-mono-950 !border-mono-700 !text-mono-100 !text-xs !rounded-xl !py-2.5 !px-3 font-mono focus:!border-mono-400" placeholder="Contoh: 081234567890 atau 6281234567890">
                                <span class="block text-mono-500 text-[10px] mt-1 font-mono">Format awalan 08xxx atau 628xxx</span>
                            </div>
                        </div>
                    `,
                    focusConfirm: false,
                    showCancelButton: true,
                    confirmButtonText: 'Lanjutkan & Scan QR',
                    cancelButtonText: 'Batal',
                    didOpen: () => {
                        const nameEl = document.getElementById('swal-input-name');
                        if (nameEl) nameEl.focus();
                    },
                    preConfirm: () => {
                        const name = document.getElementById('swal-input-name')?.value?.trim();
                        const phone = document.getElementById('swal-input-phone')?.value?.trim();

                        if (!name) {
                            Swal.showValidationMessage('Nama perangkat tidak boleh kosong!');
                            return false;
                        }
                        if (!phone) {
                            Swal.showValidationMessage('Nomor HP WhatsApp tidak boleh kosong!');
                            return false;
                        }
                        if (phone.length < 8) {
                            Swal.showValidationMessage('Nomor HP terlalu pendek, masukkan nomor yang valid.');
                            return false;
                        }

                        return { name, phone_number: phone };
                    }
                });

                if (!formValues) return;

                AppSwal.loading('Menyiapkan Perangkat...', 'Membuat slot perangkat baru...');
                try {
                    let res = await fetch('/api/devices', {
                        method: 'POST',
                        headers: this.getApiHeaders(true),
                        body: JSON.stringify({
                            name: formValues.name,
                            phone_number: formValues.phone_number
                        })
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success && json.data) {
                        AppSwal.toastSuccess('Perangkat berhasil dibuat! Silakan scan QR code.');
                        await this.fetchDevices();
                        this.connectDevice(json.data.id);
                    } else {
                        AppSwal.error('Gagal', json.message || 'Gagal membuat perangkat.');
                    }
                } catch(e) {
                    AppSwal.close();
                    AppSwal.error('Error', 'Terjadi kesalahan jaringan.');
                }
            },

            async connectDevice(id) {
                this.activeDeviceId = id;
                AppSwal.loading('Menghubungkan Engine...', 'Meminta QR Code dari whatsmeow Go service...');
                try {
                    let res = await fetch(`/api/devices/${id}/connect`, {
                        method: 'POST',
                        headers: this.getApiHeaders(false)
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        const qrCode = json.data?.qr || json.data?.qr_code;
                        if (qrCode) {
                            this.showQrModal(qrCode);
                            this.startPollingStatus(id);
                        } else if (json.data?.status === 'connected' || json.data?.logged_in === true) {
                            AppSwal.toastSuccess('Perangkat sudah terhubung!');
                            this.fetchDevices();
                        } else {
                            // If waiting_qr or pending without immediate QR string, open modal and poll
                            this.showQrModal(null);
                            this.startPollingStatus(id);
                        }
                    } else {
                        AppSwal.error('Gagal Koneksi', json.message || 'Gagal memulai koneksi.');
                    }
                } catch(e) {
                    AppSwal.close();
                    AppSwal.error('Error', 'Gagal menghubungi server.');
                }
            },

            showQrModal(qrString) {
                this.qrModalOpen = true;
                this.$nextTick(() => {
                    const container = document.getElementById('qrcode-container');
                    if (!container) return;
                    container.innerHTML = '';
                    if (qrString) {
                        new QRCode(container, {
                            text: qrString,
                            width: 230,
                            height: 230,
                            colorDark: "#000000",
                            colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.M
                        });
                    } else {
                        container.innerHTML = `
                            <div class="flex flex-col items-center justify-center h-full text-mono-400 gap-2 font-sans">
                                <i class="fa-solid fa-spinner fa-spin text-2xl text-emerald-400"></i>
                                <span class="text-xs">Menyiapkan QR Code...</span>
                            </div>
                        `;
                    }
                });
            },

            closeQrModal() {
                this.qrModalOpen = false;
                if (this.pollTimer) {
                    clearInterval(this.pollTimer);
                    this.pollTimer = null;
                }
            },

            startPollingStatus(id) {
                if (this.pollTimer) clearInterval(this.pollTimer);
                this.pollTimer = setInterval(async () => {
                    try {
                        let res = await fetch(`/api/devices/${id}/status`, {
                            headers: this.getApiHeaders(false)
                        });
                        let json = await res.json();
                        if (json.success && json.data) {
                            if (json.data.status === 'connected' || json.data.logged_in === true) {
                                this.closeQrModal();
                                AppSwal.toastSuccess('WhatsApp Berhasil Terhubung!');
                                this.fetchDevices();
                            } else {
                                const newQr = json.data.qr || json.data.qr_code;
                                if (newQr && document.getElementById('qrcode-container')?.querySelector('.fa-spinner')) {
                                    this.showQrModal(newQr);
                                }
                            }
                        }
                    } catch(e) {}
                }, 2500);
            },

            async disconnectDevice(id) {
                const confirmed = await AppSwal.confirm(
                    'Putuskan Sesi WhatsApp?',
                    'Perangkat akan logout dari server WhatsApp.',
                    'Ya, Putuskan',
                    true
                );
                if (!confirmed) return;

                AppSwal.loading('Memutuskan Sesi...', 'Mohon tunggu...');
                try {
                    let res = await fetch(`/api/devices/${id}/disconnect`, {
                        method: 'POST',
                        headers: this.getApiHeaders(false)
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('Sesi berhasil diputuskan.');
                    } else {
                        AppSwal.toastError(json.message || 'Gagal memutuskan sesi.');
                    }
                    this.fetchDevices();
                } catch(e) {
                    AppSwal.close();
                    AppSwal.toastError('Gagal menghubungi server.');
                }
            },

            async deleteDevice(id) {
                const confirmed = await AppSwal.confirm(
                    'Hapus Perangkat?',
                    'Perangkat ini akan dihapus permanen dari akun Anda.',
                    'Ya, Hapus',
                    true
                );
                if (!confirmed) return;

                AppSwal.loading('Menghapus Perangkat...', 'Mohon tunggu...');
                try {
                    let res = await fetch(`/api/devices/${id}`, {
                        method: 'DELETE',
                        headers: this.getApiHeaders(false)
                    });
                    let json = await res.json();
                    AppSwal.close();

                    if (json.success) {
                        AppSwal.toastSuccess('Perangkat berhasil dihapus.');
                    } else {
                        AppSwal.toastError(json.message || 'Gagal menghapus perangkat.');
                    }
                    this.fetchDevices();
                } catch(e) {
                    AppSwal.close();
                    AppSwal.toastError('Gagal menghubungi server.');
                }
            }
        }
    }
</script>
@endsection
