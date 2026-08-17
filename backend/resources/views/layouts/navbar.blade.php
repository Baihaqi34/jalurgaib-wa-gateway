<header class="h-20 px-6 border-b border-mono-700/80 bg-mono-900/80 backdrop-blur-xl sticky top-0 z-30 flex items-center justify-between">
    
    <!-- Mobile Menu Toggle Button & Page Title -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2.5 rounded-xl bg-mono-850 border border-mono-700 text-mono-300 md:hidden hover:bg-mono-800 hover:text-white transition shadow-sm">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div>
            <h2 class="text-base font-bold text-mono-50 tracking-tight flex items-center gap-2">
                <span>@yield('header_title', 'JalurGaib WA Gateway')</span>
            </h2>
            <p class="text-xs text-mono-400">@yield('header_subtitle', 'Sistem Gateway & Pengelolaan Pesan WhatsApp')</p>
        </div>
    </div>

    <!-- Right Badges & Controls -->
    <div class="flex items-center gap-3">
        
        <!-- Live Go Service Indicator -->
        <div class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-mono-850 border border-mono-700 text-xs shadow-sm">
            <span class="w-2 h-2 rounded-full bg-white shadow-[0_0_8px_#ffffff] animate-pulse"></span>
            <span class="text-mono-300 font-mono text-[11px] font-semibold tracking-wide">Go Engine: 8080</span>
        </div>

        <!-- Role Badge -->
        <span class="px-3.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-mono-850 text-mono-200 border border-mono-700 shadow-sm">
            {{ auth()->user()->isAdmin() ? '👑 Super Admin' : '👤 Tenant User' }}
        </span>

        <!-- Logout Action with Confirmation -->
        <form action="{{ route('logout') }}" method="POST" @submit.prevent="AppSwal.confirm('Keluar dari Akun?', 'Anda akan mengakhiri sesi aktif saat ini.', 'Ya, Keluar', true).then(ok => { if (ok) { AppSwal.loading('Mengakhiri Sesi...', 'Mohon tunggu sebentar...'); $el.submit(); } })">
            @csrf
            <button type="submit" class="px-3.5 py-1.5 bg-mono-850 hover:bg-mono-700 border border-mono-700 hover:border-mono-600 text-mono-300 hover:text-white rounded-xl text-xs font-semibold transition shadow-sm flex items-center gap-1.5" title="Logout">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="hidden sm:inline">Keluar</span>
            </button>
        </form>
    </div>
</header>
