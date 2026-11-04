<nav class="space-y-5 text-xs font-sans" x-data="{ searchQuery: '' }">

    <!-- Quick Filter -->
    <div class="relative">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-mono-400 text-xs"></i>
        <input type="text" 
               x-model="searchQuery" 
               placeholder="Filter endpoint..." 
               class="w-full bg-mono-950 border border-mono-700 rounded-xl pl-8 pr-3 py-1.5 text-xs text-mono-100 placeholder-mono-500 focus:outline-none focus:border-mono-400 transition">
        <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2.5 top-2 text-mono-400 hover:text-white text-xs">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Section: Overview & Autentikasi -->
    <div class="space-y-1" x-show="!searchQuery || 'overview autentikasi base url api key'.includes(searchQuery.toLowerCase())">
        <div class="px-2 text-[10px] font-mono font-bold uppercase tracking-wider text-mono-400">
            Pengenalan
        </div>
        <div class="space-y-0.5">
            <a href="#overview" 
               @click="mobileDocsMenu = false; activeSection = 'overview'" 
               :class="activeSection === 'overview' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-compass text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Overview & Base URL</span>
                </span>
            </a>
            <a href="#auth" 
               @click="mobileDocsMenu = false; activeSection = 'auth'" 
               :class="activeSection === 'auth' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-key text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Header X-API-Key</span>
                </span>
            </a>
            <a href="#laravel" 
               @click="mobileDocsMenu = false; activeSection = 'laravel'" 
               :class="activeSection === 'laravel' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2">
                    <i class="fa-brands fa-laravel text-[11px] w-3.5 text-center text-red-400"></i>
                    <span>Panduan Laravel</span>
                </span>
                <span class="text-[9px] font-mono px-1.5 py-0.2 rounded bg-red-500/10 text-red-400 font-bold">SDK</span>
            </a>
        </div>
    </div>

    <!-- Section: Kirim Pesan -->
    <div class="space-y-1" x-show="!searchQuery || 'kirim pesan single bulk broadcast notifikasi'.includes(searchQuery.toLowerCase())">
        <div class="px-2 text-[10px] font-mono font-bold uppercase tracking-wider text-mono-400">
            Pengiriman Pesan
        </div>
        <div class="space-y-0.5">
            <a href="#send-message" 
               @click="mobileDocsMenu = false; activeSection = 'send-message'" 
               :class="activeSection === 'send-message' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-paper-plane text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Kirim Tunggal</span>
                </span>
                <span class="method-badge method-post text-[9px] px-1.5 py-0">POST</span>
            </a>
            <a href="#send-bulk" 
               @click="mobileDocsMenu = false; activeSection = 'send-bulk'" 
               :class="activeSection === 'send-bulk' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-envelopes-bulk text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Broadcast Massal</span>
                </span>
                <span class="method-badge method-post text-[9px] px-1.5 py-0">POST</span>
            </a>
        </div>
    </div>

    <!-- Section: Perangkat & QR -->
    <div class="space-y-1" x-show="!searchQuery || 'device perangkat scan qr live status connect disconnect crud'.includes(searchQuery.toLowerCase())">
        <div class="px-2 text-[10px] font-mono font-bold uppercase tracking-wider text-mono-400">
            Perangkat & Scan QR
        </div>
        <div class="space-y-0.5">
            <a href="#devices-list" 
               @click="mobileDocsMenu = false; activeSection = 'devices-list'" 
               :class="activeSection === 'devices-list' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-list-check text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Daftar Perangkat</span>
                </span>
                <span class="method-badge method-get text-[9px] px-1.5 py-0">GET</span>
            </a>
            <a href="#devices-create" 
               @click="mobileDocsMenu = false; activeSection = 'devices-create'" 
               :class="activeSection === 'devices-create' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-plus text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Tambah Perangkat</span>
                </span>
                <span class="method-badge method-post text-[9px] px-1.5 py-0">POST</span>
            </a>
            <a href="#devices-scan" 
               @click="mobileDocsMenu = false; activeSection = 'devices-scan'" 
               :class="activeSection === 'devices-scan' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-qrcode text-[11px] w-3.5 text-center text-emerald-400"></i>
                    <span>Scan QR (JSON/SVG)</span>
                </span>
                <span class="method-badge method-get text-[9px] px-1.5 py-0">GET</span>
            </a>
            <a href="#devices-status" 
               @click="mobileDocsMenu = false; activeSection = 'devices-status'" 
               :class="activeSection === 'devices-status' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-wave-square text-[11px] w-3.5 text-center text-sky-400"></i>
                    <span>Cek Status Live</span>
                </span>
                <span class="method-badge method-get text-[9px] px-1.5 py-0">GET</span>
            </a>
            <a href="#devices-detail" 
               @click="mobileDocsMenu = false; activeSection = 'devices-detail'" 
               :class="activeSection === 'devices-detail' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-mobile-screen text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Detail Perangkat</span>
                </span>
                <span class="method-badge method-get text-[9px] px-1.5 py-0">GET</span>
            </a>
            <a href="#devices-update" 
               @click="mobileDocsMenu = false; activeSection = 'devices-update'" 
               :class="activeSection === 'devices-update' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-pen text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Edit Perangkat</span>
                </span>
                <span class="method-badge method-put text-[9px] px-1.5 py-0">PUT</span>
            </a>
            <a href="#devices-delete" 
               @click="mobileDocsMenu = false; activeSection = 'devices-delete'" 
               :class="activeSection === 'devices-delete' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2 truncate">
                    <i class="fa-solid fa-trash text-[11px] w-3.5 text-center text-mono-400"></i>
                    <span>Hapus Perangkat</span>
                </span>
                <span class="method-badge method-delete text-[9px] px-1.5 py-0">DEL</span>
            </a>
        </div>
    </div>

    <!-- Section: HTTP Status & Error -->
    <div class="space-y-1" x-show="!searchQuery || 'error status format 200 401 404 422 429 503 response'.includes(searchQuery.toLowerCase())">
        <div class="px-2 text-[10px] font-mono font-bold uppercase tracking-wider text-mono-400">
            Standar Response
        </div>
        <div class="space-y-0.5">
            <a href="#error-codes" 
               @click="mobileDocsMenu = false; activeSection = 'error-codes'" 
               :class="activeSection === 'error-codes' ? 'docs-sidebar-link active' : 'docs-sidebar-link'">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-[11px] w-3.5 text-center text-amber-400"></i>
                    <span>HTTP Code & Error Format</span>
                </span>
            </a>
        </div>
    </div>

</nav>
