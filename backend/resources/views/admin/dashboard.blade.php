@extends('layouts.app')

@section('title', 'Admin Dashboard Global — JalurGaib WA Gateway')
@section('header_title', 'Dashboard Monitoring Global Aplikator')
@section('header_subtitle', 'Pantau performa Go Engine, Queue Worker, Perangkat WA, dan status server')

@section('content')
<div x-data="adminDashboardApp()" x-init="initDashboard()" class="space-y-8">

    <!-- Global Key Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Total Users -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">Total Pengguna</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">👥</div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50" x-text="stats.total_users">{{ $stats['total_users'] }}</div>
            <div class="text-xs text-mono-400 mt-1.5 font-medium">Akun Admin & Tenant terdaftar</div>
        </div>

        <!-- Global WA Devices -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">Perangkat WA Global</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">📱</div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50">
                <span x-text="stats.connected_dev">{{ $stats['connected_dev'] }}</span>
                <span class="text-sm font-semibold text-mono-400">/ <span x-text="stats.total_devices">{{ $stats['total_devices'] }}</span> Online</span>
            </div>
            <div class="text-xs text-mono-300 font-semibold mt-1.5">Nomor WhatsApp terhubung</div>
        </div>

        <!-- Sent Messages Global -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">Pesan Terkirim</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">🚀</div>
            </div>
            <div class="text-3xl font-extrabold text-mono-50" x-text="stats.sent_messages">{{ $stats['sent_messages'] }}</div>
            <div class="text-xs text-mono-400 mt-1.5 font-medium">Total pesan berhasil di-deliver</div>
        </div>

        <!-- Queue Latency -->
        <div class="mono-card p-6">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-mono-400 uppercase tracking-wider">Waktu Tunggu Antrean</span>
                <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base shadow-sm">⏳</div>
            </div>
            <div class="text-2xl font-extrabold text-mono-100 truncate" x-text="stats.oldest_wait_time || '0 detik'">{{ $stats['queue']['oldest_job_wait'] ?? '0 detik' }}</div>
            <div class="text-xs text-mono-400 mt-1.5 font-medium"><span x-text="stats.pending_messages">{{ $stats['pending_messages'] }}</span> pesan dalam antrean</div>
        </div>
    </div>

    <!-- SECTION: SERVICE GO & QUEUE WORKER LIVE MONITOR -->
    <div id="monitor-section" class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Go WhatsApp Engine Health Card -->
        <div class="mono-card p-7 space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-mono-50 text-mono-950 flex items-center justify-center text-base font-bold shadow-sm">
                        ⚡
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-mono-50">WhatsApp Go Service (Port 8080)</h3>
                        <p class="text-xs text-mono-400">Mesin protokol whatsmeow internal</p>
                    </div>
                </div>
                <button @click="loadDiagnostics()" class="px-3.5 py-1.5 mono-btn-secondary text-xs font-semibold flex items-center gap-1.5">
                    🔄 Ping Service
                </button>
            </div>

            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 space-y-3 text-xs font-mono">
                <div class="flex justify-between items-center">
                    <span class="text-mono-400">Status Layanan:</span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm"
                          :class="diagnostics.go_service.online ? 'bg-mono-50 text-mono-950 font-extrabold' : 'bg-mono-800 text-mono-300 border border-mono-600'"
                          x-text="diagnostics.go_service.status_label"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mono-400">Kecepatan Respon (Ping):</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.go_service.response_ms + ' ms'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mono-400">Endpoint Internal:</span>
                    <span class="text-mono-200 font-semibold" x-text="diagnostics.go_service.url"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mono-400">Active WA Sesi Terhubung:</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.go_service.device_count + ' perangkat'"></span>
                </div>
            </div>

            <!-- Active Sessions List in Go Engine -->
            <div x-show="diagnostics.go_service.active_devices && diagnostics.go_service.active_devices.length > 0">
                <h4 class="text-xs font-bold text-mono-400 uppercase tracking-wider mb-2.5">Daftar Sesi WhatsApp Aktif di Mesin:</h4>
                <div class="space-y-2 max-h-44 overflow-y-auto">
                    <template x-for="sess in diagnostics.go_service.active_devices" :key="sess.device_id">
                        <div class="p-3 bg-mono-950 border border-mono-700 rounded-xl flex justify-between items-center text-[11px] font-mono shadow-sm">
                            <span class="text-mono-200 font-bold" x-text="sess.device_id"></span>
                            <span class="text-mono-400 truncate max-w-[180px]" x-text="sess.jid || 'Connected'"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Queue Worker & Waiting Duration Monitor Card -->
        <div class="mono-card p-7 space-y-5">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-mono-800 border border-mono-700 text-mono-100 flex items-center justify-center text-base font-bold shadow-sm">
                        ⏳
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-mono-50">Queue Worker & Antrean Pesan</h3>
                        <p class="text-xs text-mono-400">Liveness dan latensi pemrosesan pesan antrean</p>
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-mono-850 text-mono-200 border border-mono-700 shadow-sm"
                      x-text="diagnostics.queue.worker_status"></span>
            </div>

            <div class="p-5 bg-mono-950 rounded-2xl border border-mono-700 space-y-3 text-xs font-mono">
                <div class="flex justify-between">
                    <span class="text-mono-400">Pesan Mengantre di Database:</span>
                    <span class="text-mono-100 font-bold" x-text="stats.pending_messages + ' pesan'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mono-400">Lama Antrean Pesan Tertunda:</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.queue.msg_wait_time"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mono-400">Jobs Antrean di Tabel 'jobs':</span>
                    <span class="text-mono-100 font-bold" x-text="diagnostics.queue.pending_jobs + ' job'"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-mono-400">Failed Jobs:</span>
                    <span :class="diagnostics.queue.failed_jobs > 0 ? 'text-mono-100 font-bold underline' : 'text-mono-400'" x-text="diagnostics.queue.failed_jobs + ' error'"></span>
                </div>
            </div>

            <div class="p-4 bg-mono-950 rounded-xl border border-mono-700 text-xs text-mono-400 space-y-1">
                <div class="flex items-center gap-2 text-mono-200 font-bold">
                    <span>🛡️</span> Kebijakan Privasi Pesan
                </div>
                <p class="text-[11px] leading-relaxed">Log konten pesan individual seluruh tenant disembunyikan untuk menjaga privasi pengguna. Admin hanya memantau statistik & antrean pengiriman.</p>
            </div>
        </div>

    </div>

    <!-- SECTION: SEMUA PERANGKAT WA GLOBAL -->
    <div id="devices-section" class="mono-card p-8 space-y-5">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h3 class="text-base font-extrabold text-mono-50">Semua Perangkat WhatsApp Global</h3>
                <p class="text-xs text-mono-400 mt-0.5">Monitoring status koneksi perangkat WhatsApp milik seluruh tenant</p>
            </div>
            <button @click="loadDevices()" class="px-4 py-2 mono-btn-secondary text-xs font-bold transition shadow-sm">
                🔄 Refresh
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="d in devices" :key="d.id">
                <div class="bg-mono-950 border border-mono-700 p-5 rounded-2xl space-y-3.5 shadow-sm hover:border-mono-500 transition">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-bold text-mono-50 text-sm" x-text="d.name"></h4>
                            <p class="text-xs text-mono-400 font-mono mt-0.5" x-text="d.phone_number || '-'"></p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm"
                              :class="d.status === 'connected' ? 'bg-mono-50 text-mono-950 font-extrabold' : (d.status === 'pending' ? 'bg-mono-800 text-mono-200 border border-mono-600' : 'bg-mono-900 text-mono-400 border border-mono-700')"
                              x-text="d.status"></span>
                    </div>

                    <div class="pt-2.5 border-t border-mono-800 text-[11px] space-y-1.5 font-medium">
                        <div class="flex justify-between text-mono-400">
                            <span>Pemilik Akun:</span>
                            <span class="text-mono-100 font-bold" x-text="d.user ? d.user.name : '-'"></span>
                        </div>
                        <div class="flex justify-between text-mono-400">
                            <span>Device ID:</span>
                            <span class="font-mono text-mono-200 font-semibold" x-text="d.device_id"></span>
                        </div>
                        <div class="flex justify-between text-mono-400" x-show="d.jid">
                            <span>WA JID:</span>
                            <span class="font-mono text-mono-300 truncate max-w-[160px]" x-text="d.jid"></span>
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
