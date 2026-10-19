<header class="h-16 px-4 sm:px-6 border-b border-mono-700/80 bg-mono-900/80 backdrop-blur-xl sticky top-0 z-30 flex items-center justify-between transition-colors">
    
    <!-- Left: Mobile Toggle & Page Breadcrumb Header -->
    <div class="flex items-center gap-3 min-w-0">
        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="w-9 h-9 rounded-xl bg-mono-850 border border-mono-700 text-mono-300 md:hidden hover:bg-mono-800 hover:text-white transition shadow-sm flex items-center justify-center flex-shrink-0" title="Buka Menu">
            <i class="fa-solid fa-bars text-sm"></i>
        </button>

        <!-- Breadcrumb & Header Info -->
        <div class="min-w-0 flex items-center gap-2">
            <div class="hidden sm:flex items-center gap-1.5 text-mono-400 text-[11px] font-mono uppercase tracking-wider font-bold">
                <span>JalurGaib</span>
                <span class="text-mono-600">/</span>
            </div>
            <div class="min-w-0">
                <h2 class="text-xs sm:text-sm font-extrabold text-mono-50 tracking-tight flex items-center gap-2 truncate max-w-[135px] xs:max-w-[180px] sm:max-w-xs md:max-w-md lg:max-w-none">
                    <span class="truncate">@yield('header_title', 'Dashboard Overview')</span>
                </h2>
            </div>
            @hasSection('header_subtitle')
                <div class="hidden xl:inline-block text-[11px] text-mono-400 font-medium pl-2.5 border-l border-mono-700/80 truncate max-w-sm">
                    @yield('header_subtitle')
                </div>
            @endif
        </div>
    </div>

    <!-- Right: Telemetry, Theme Switcher & Profile Dropdown -->
    <div class="flex items-center gap-2.5 sm:gap-3 flex-shrink-0">
        
        <!-- Go Engine Status Widget -->
        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-mono-850 border border-mono-700/80 text-xs shadow-sm" title="WhatsApp Engine Gateway Service">
            <span class="w-2 h-2 rounded-full bg-emerald-400 shadow-[0_0_8px_#34d399] animate-pulse"></span>
            <span class="text-mono-300 font-mono text-[11px] font-semibold tracking-wide">Engine 8080</span>
        </div>

        <!-- Dark / Light Mode Toggle Button -->
        <button type="button" 
                onclick="toggleTheme()" 
                class="w-9 h-9 rounded-xl bg-mono-850 hover:bg-mono-800 border border-mono-700/80 text-mono-300 hover:text-white flex items-center justify-center transition shadow-sm" 
                title="Ganti Mode Gelap / Terang">
            <!-- Sun icon in dark mode -->
            <i class="fa-solid fa-sun text-amber-400 text-xs theme-icon-sun"></i>
            <!-- Moon icon in light mode -->
            <i class="fa-solid fa-moon text-indigo-500 text-xs theme-icon-moon"></i>
        </button>

        <!-- User Profile Pill & Dropdown -->
        <div class="relative" x-data="{ profileDropdown: false }" @click.outside="profileDropdown = false">
            <button type="button" 
                    @click="profileDropdown = !profileDropdown" 
                    class="flex items-center gap-2 p-1.5 pr-2.5 rounded-xl bg-mono-850 hover:bg-mono-800 border border-mono-700/80 transition text-xs shadow-sm group">
                <div class="w-7 h-7 rounded-lg bg-mono-800 border border-mono-600 text-mono-100 font-extrabold flex items-center justify-center text-xs shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="text-left hidden sm:block">
                    <div class="font-bold text-mono-100 text-xs truncate max-w-[110px] leading-tight">{{ auth()->user()->name }}</div>
                </div>
                <i class="fa-solid fa-chevron-down text-[10px] text-mono-400 transition-transform duration-200" :class="profileDropdown ? 'rotate-180' : ''"></i>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="profileDropdown" 
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 mt-2 w-64 mono-card p-2 shadow-2xl z-50 border-mono-700 space-y-1 font-sans"
                 x-cloak>
                
                <!-- User Header Box -->
                <div class="p-3 bg-mono-950 rounded-xl border border-mono-800 mb-1">
                    <div class="font-bold text-mono-100 text-xs truncate">{{ auth()->user()->name }}</div>
                    <div class="font-mono text-mono-400 text-[10px] truncate mt-0.5">{{ auth()->user()->email }}</div>
                    <div class="mt-2 flex items-center gap-1.5">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-mono-850 text-mono-200 border border-mono-700 inline-flex items-center gap-1">
                            @if(auth()->user()->isAdmin())
                                <i class="fa-solid fa-crown text-amber-400 text-[8px]"></i>
                                <span>Super Admin</span>
                            @else
                                <i class="fa-solid fa-user-check text-mono-400 text-[8px]"></i>
                                <span>Tenant User</span>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Navigation Items -->
                <a href="{{ route('user.profile') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-mono-200 hover:text-white hover:bg-mono-800/80 transition">
                    <i class="fa-solid fa-user-gear text-mono-400 w-4 text-center"></i>
                    <span>Edit Profil & Password</span>
                </a>

                @if(!auth()->user()->isAdmin())
                    <a href="{{ route('user.upgrade') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-mono-200 hover:text-white hover:bg-mono-800/80 transition">
                        <i class="fa-solid fa-crown text-amber-400 w-4 text-center"></i>
                        <span>Upgrade Paket Langganan</span>
                    </a>
                @endif

                <a href="{{ route('docs') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-mono-200 hover:text-white hover:bg-mono-800/80 transition">
                    <i class="fa-solid fa-book-bookmark text-mono-400 w-4 text-center"></i>
                    <span>Dokumentasi REST API</span>
                </a>

                <div class="border-t border-mono-800 my-1"></div>

                <!-- Logout Form -->
                <form action="{{ route('logout') }}" method="POST" @submit.prevent="AppSwal.confirm('Keluar dari Akun?', 'Anda akan mengakhiri sesi aktif saat ini.', 'Ya, Keluar', true).then(ok => { if (ok) { AppSwal.loading('Mengakhiri Sesi...', 'Mohon tunggu sebentar...'); $el.submit(); } })">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-semibold text-red-400 hover:text-red-300 hover:bg-red-500/10 transition text-left">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                        <span>Keluar Akun</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
