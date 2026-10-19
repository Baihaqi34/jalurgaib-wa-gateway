@extends('layouts.app')

@section('title', 'Admin Dashboard Global — JalurGaib WA Gateway')
@section('header_title', 'Dashboard Global')
@section('header_subtitle', 'Pantau performa Go Engine, Queue Worker, Perangkat WA, dan status server')

@section('content')
<div x-data="adminDashboardApp()" x-init="initDashboard()" class="space-y-6 sm:space-y-8">

    <!-- Global Key Metrics Cards (Modern Bento Grid: 2 cols on mobile, 4 on desktop) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">
        
        <!-- Total Users -->
        <div class="mono-card p-4 sm:p-6 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-mono-400 uppercase tracking-wider font-mono">Total Users</span>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-xs sm:text-sm shadow-sm group-hover:scale-105 transition">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div>
                <div class="text-xl sm:text-3xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums" x-text="stats.total_users">{{ $stats['total_users'] }}</div>
                <div class="text-[10px] sm:text-xs text-mono-400 mt-1 sm:mt-1.5 font-medium truncate">Admin & Tenant terdaftar</div>
            </div>
        </div>

        <!-- Global WA Devices -->
        <div class="mono-card p-4 sm:p-6 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-mono-400 uppercase tracking-wider font-mono">Perangkat WA</span>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-mono-800 border border-mono-700 text-emerald-400 flex items-center justify-center text-xs sm:text-base shadow-sm group-hover:scale-105 transition">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
            </div>
            <div>
                <div class="text-xl sm:text-3xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums">
                    <span x-text="stats.connected_dev">{{ $stats['connected_dev'] }}</span>
                    <span class="text-xs sm:text-sm font-semibold text-mono-400">/ <span x-text="stats.total_devices">{{ $stats['total_devices'] }}</span></span>
                </div>
                <div class="text-[10px] sm:text-xs text-mono-300 font-semibold mt-1 sm:mt-1.5 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Online & Terhubung</span>
                </div>
            </div>
        </div>

        <!-- Sent Messages Global -->
        <div class="mono-card p-4 sm:p-6 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-mono-400 uppercase tracking-wider font-mono">Pesan Terkirim</span>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-xs sm:text-sm shadow-sm group-hover:scale-105 transition">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
            </div>
            <div>
                <div class="text-xl sm:text-3xl font-extrabold text-mono-50 font-mono tracking-tight tabular-nums" x-text="stats.sent_messages">{{ $stats['sent_messages'] }}</div>
                <div class="text-[10px] sm:text-xs text-mono-400 mt-1 sm:mt-1.5 font-medium truncate">Berhasil di-deliver</div>
            </div>
        </div>

        <!-- Queue Latency -->
        <div class="mono-card p-4 sm:p-6 flex flex-col justify-between transition hover:border-mono-600 group">
            <div class="flex items-center justify-between mb-2 sm:mb-3">
                <span class="text-[10px] sm:text-xs font-bold text-mono-400 uppercase tracking-wider font-mono">Antrean Pesan</span>
                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl bg-mono-800 border border-mono-700 text-amber-400 flex items-center justify-center text-xs sm:text-sm shadow-sm group-hover:scale-105 transition">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div>
                <div class="text-xl sm:text-2xl font-extrabold text-mono-100 font-mono tracking-tight truncate tabular-nums" x-text="stats.oldest_wait_time || '0 detik'">{{ $stats['queue']['oldest_job_wait'] ?? '0 detik' }}</div>
                <div class="text-[10px] sm:text-xs text-mono-400 mt-1 sm:mt-1.5 font-medium truncate">
                    <span x-text="stats.pending_messages">{{ $stats['pending_messages'] }}</span> pesan dalam antrean
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION: SERVICE GO & QUEUE WORKER LIVE MONITOR -->
    <div id="monitor-section" class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

        <!-- Go WhatsApp Engine Health Card -->
        <div class="mono-card p-5 sm:p-7 space-y-4 sm:space-y-5">
            <div class="flex flex-col xs:flex-row xs:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-mono-50 text-mono-950 flex items-center justify-center text-sm font-bold shadow-sm shrink-0">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-extrabold text-mono-50 truncate">WhatsApp Go Service (:8080)</h3>
                        <p class="text-xs text-mono-400 truncate">Mesin protokol whatsmeow internal</p>
                    </div>
                </div>
                <button @click="loadDiagnostics()" class="h-9 px-3.5 mono-btn-secondary text-xs font-bold flex items-center justify-center gap-1.5 self-start xs:self-auto shrink-0 shadow-sm rounded-xl">
                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                    <span>Ping</span>
                </button>
            </div>

            <div class="p-4 sm:p-5 bg-mono-950 rounded-2xl border border-mono-700/80 space-y-3 text-xs font-mono">
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Status Layanan:</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase shadow-sm whitespace-nowrap"
                          :class="diagnostics.go_service.online ? 'bg-mono-50 text-mono-950 font-extrabold' : 'bg-mono-800 text-mono-300 border border-mono-600'"
                          x-text="diagnostics.go_service.status_label"></span>
                </div>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Kecepatan Ping:</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.go_service.response_ms + ' ms'"></span>
                </div>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Endpoint:</span>
                    <span class="text-mono-200 font-semibold truncate max-w-[150px] sm:max-w-xs text-right" x-text="diagnostics.go_service.url"></span>
                </div>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Sesi WA Terhubung:</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.go_service.device_count + ' perangkat'"></span>
                </div>
            </div>

            <!-- Active Sessions List in Go Engine -->
            <div x-show="diagnostics.go_service.active_devices && diagnostics.go_service.active_devices.length > 0">
                <h4 class="text-xs font-bold text-mono-400 uppercase tracking-wider mb-2 font-mono">Daftar Sesi WA Aktif di Engine:</h4>
                <div class="space-y-2 max-h-44 overflow-y-auto scrollbar-thin">
                    <template x-for="sess in diagnostics.go_service.active_devices" :key="sess.device_id">
                        <div class="p-3 bg-mono-950 border border-mono-700/80 rounded-xl flex justify-between items-center text-[11px] font-mono shadow-sm gap-2">
                            <span class="text-mono-200 font-bold truncate" x-text="sess.device_id"></span>
                            <span class="text-mono-400 truncate max-w-[140px] sm:max-w-[200px] text-right" x-text="sess.jid || 'Connected'"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Queue Worker & Waiting Duration Monitor Card -->
        <div class="mono-card p-5 sm:p-7 space-y-4 sm:space-y-5">
            <div class="flex flex-col xs:flex-row xs:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 text-amber-400 flex items-center justify-center text-sm font-bold shadow-sm shrink-0">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm sm:text-base font-extrabold text-mono-50 truncate">Queue Worker & Antrean</h3>
                        <p class="text-xs text-mono-400 truncate">Liveness dan latensi pesan antrean</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-850 text-mono-200 border border-mono-700 shadow-sm self-start xs:self-auto shrink-0 whitespace-nowrap"
                      x-text="diagnostics.queue.worker_status"></span>
            </div>

            <div class="p-4 sm:p-5 bg-mono-950 rounded-2xl border border-mono-700/80 space-y-3 text-xs font-mono">
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Pesan Mengantre di DB:</span>
                    <span class="text-mono-100 font-bold" x-text="stats.pending_messages + ' pesan'"></span>
                </div>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Waktu Antre Terlama:</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.queue.msg_wait_time"></span>
                </div>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Jobs di Tabel 'jobs':</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.queue.pending_jobs + ' job'"></span>
                </div>
                <div class="flex justify-between items-center gap-2">
                    <span class="text-mono-400 shrink-0">Failed Jobs:</span>
                    <span :class="diagnostics.queue.failed_jobs > 0 ? 'text-amber-400 font-bold' : 'text-mono-400'" x-text="diagnostics.queue.failed_jobs + ' error'"></span>
                </div>
            </div>

            <div class="p-4 bg-mono-950 rounded-xl border border-mono-700/80 text-xs text-mono-400 space-y-1">
                <div class="flex items-center gap-2 text-mono-200 font-bold">
                    <i class="fa-solid fa-shield-halved text-emerald-400 text-xs"></i>
                    <span>Kebijakan Privasi Pesan</span>
                </div>
                <p class="text-[11px] leading-relaxed">Log konten pesan individual seluruh tenant disembunyikan untuk menjaga kerahasiaan. Admin hanya memantau statistik & antrean pengiriman.</p>
            </div>
        </div>

    </div>

    <!-- SECTION: SEMUA PERANGKAT WA GLOBAL -->
    <div id="devices-section" class="mono-card p-5 sm:p-7 space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-extrabold text-mono-50">Semua Perangkat WhatsApp Global</h3>
                <p class="text-xs text-mono-400 mt-0.5">Monitoring status koneksi perangkat WhatsApp milik seluruh tenant</p>
            </div>
            <button @click="loadDevices()" class="h-9 px-4 mono-btn-secondary text-xs font-bold transition shadow-sm inline-flex items-center justify-center gap-1.5 self-start sm:self-auto rounded-xl">
                <i class="fa-solid fa-arrows-rotate text-xs"></i>
                <span>Refresh Perangkat</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="d in devices" :key="d.id">
                <div class="bg-mono-950 border border-mono-700/80 p-4 sm:p-5 rounded-2xl space-y-3.5 shadow-sm hover:border-mono-500 transition">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <h4 class="font-bold text-mono-50 text-sm truncate" x-text="d.name"></h4>
                            <p class="text-xs text-mono-400 font-mono mt-0.5 truncate" x-text="d.phone_number || '-'"></p>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase shadow-sm whitespace-nowrap shrink-0"
                              :class="d.status === 'connected' ? 'bg-mono-50 text-mono-950 font-extrabold' : (d.status === 'pending' ? 'bg-mono-800 text-mono-200 border border-mono-600' : 'bg-mono-900 text-mono-400 border border-mono-700')"
                              x-text="d.status"></span>
                    </div>

                    <div class="pt-2.5 border-t border-mono-800 text-[11px] space-y-1.5 font-medium">
                        <div class="flex justify-between items-center text-mono-400 gap-2">
                            <span class="shrink-0">Pemilik Akun:</span>
                            <span class="text-mono-100 font-bold truncate text-right max-w-[140px] sm:max-w-[170px]" x-text="d.user ? d.user.name : '-'"></span>
                        </div>
                        <div class="flex justify-between items-center text-mono-400 gap-2">
                            <span class="shrink-0">Device ID:</span>
                            <span class="font-mono text-mono-200 font-semibold truncate text-right max-w-[140px] sm:max-w-[170px]" x-text="d.device_id"></span>
                        </div>
                        <div class="flex justify-between items-center text-mono-400 gap-2" x-show="d.jid">
                            <span class="shrink-0">WA JID:</span>
                            <span class="font-mono text-mono-300 truncate max-w-[140px] sm:max-w-[170px] text-right" x-text="d.jid"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

</div>

<script>
    function adminDashboardApp() {
        return {
            stats: @json($stats),
            diagnostics: @json($diagnostics),
            devices: @json($devices),

            initDashboard() {
                setInterval(() => {
                    this.loadDiagnostics();
                    this.loadStats();
                }, 3500);
            },

            async loadDiagnostics() {
                try {
                    let res = await fetch('/admin/api/health');
                    let json = await res.json();
                    if (json.success) {
                        this.diagnostics = json.data;
                    }
                } catch(e) {}
            },

            async loadStats() {
                try {
                    let res = await fetch('/admin/api/stats');
                    let json = await res.json();
                    if (json.success) {
                        this.stats = json.data;
                    }
                } catch(e) {}
            },

            async loadDevices() {
                try {
                    let res = await fetch('/admin/api/devices');
                    let json = await res.json();
                    if (json.success) this.devices = json.data;
                } catch(e) {}
            }
        }
    }
</script>
@endsection
