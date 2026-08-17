<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-mono-900/95 backdrop-blur-2xl border-r border-mono-700/80 flex flex-col transition-transform duration-300 md:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <!-- Sidebar Brand -->
    <div class="h-20 px-6 flex items-center gap-3 border-b border-mono-700/80">
        <div class="w-10 h-10 rounded-2xl bg-mono-950 border border-mono-700 flex items-center justify-center p-1.5 shadow-md">
            <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo w-full h-full object-contain">
        </div>
        <div>
            <div class="font-extrabold text-mono-50 tracking-tight leading-tight text-base">JalurGaib WA Gateway</div>
            <div class="text-[10px] font-bold text-mono-400 font-mono tracking-widest uppercase">👤 TENANT PORTAL</div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        
        <div class="px-3 pb-2 text-[10px] font-extrabold text-mono-500 uppercase tracking-widest">
            Menu WhatsApp
        </div>

        <!-- Dashboard / Panel WhatsApp -->
        <a href="{{ route('user.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('user.dashboard') ? 'bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <span class="text-base">📱</span>
            <span>Portal WhatsApp Saya</span>
        </a>

        <!-- Upgrade Paket -->
        <a href="{{ route('user.upgrade') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('user.upgrade') ? 'bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <span class="text-base">⭐</span>
            <span>Upgrade Paket</span>
        </a>

        <!-- Dokumentasi REST API -->
        <a href="{{ route('docs') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition {{ request()->routeIs('docs') ? 'bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <span class="text-base">📖</span>
            <span>Dokumentasi REST API</span>
        </a>
    </div>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-mono-700/80 bg-mono-950">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-mono-800 border border-mono-600 text-mono-100 font-extrabold text-xs flex items-center justify-center shadow-sm">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="text-left">
                    <div class="text-xs font-bold text-mono-100 truncate max-w-[110px]">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-mono-400 font-mono font-semibold">Tenant User</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" @submit.prevent="AppSwal.confirm('Keluar dari Akun?', 'Anda akan mengakhiri sesi aktif saat ini.', 'Ya, Keluar', true).then(ok => { if (ok) { AppSwal.loading('Mengakhiri Sesi...', ''); $el.submit(); } })">
                @csrf
                <button type="submit" class="p-2 text-mono-400 hover:text-white hover:bg-mono-800 rounded-xl transition" title="Logout">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</aside>
