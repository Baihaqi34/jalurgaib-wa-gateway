<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-mono-900/95 backdrop-blur-2xl border-r border-mono-700/80 flex flex-col transition-transform duration-300 md:translate-x-0"
       :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <!-- Sidebar Brand -->
    <div class="h-16 px-5 flex items-center justify-between border-b border-mono-700/80">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-9 h-9 rounded-xl bg-mono-950 border border-mono-700 flex items-center justify-center p-1.5 shadow-sm flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo w-full h-full object-contain">
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="font-extrabold text-mono-50 tracking-tight text-sm truncate">JalurGaib</span>
                    <span class="px-1.5 py-0.5 rounded text-[9px] font-mono font-bold bg-mono-800 text-mono-300 border border-mono-700 tracking-wide uppercase">Gateway</span>
                </div>
                <div class="text-[10px] font-mono font-semibold text-mono-400 tracking-wider uppercase flex items-center gap-1 mt-0.5">
                    <i class="fa-solid fa-shield-halved text-[9px] text-mono-300"></i>
                    <span>Super Admin</span>
                </div>
            </div>
        </div>

        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="md:hidden p-1.5 rounded-lg text-mono-400 hover:text-white hover:bg-mono-800 transition" title="Tutup Menu">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <div class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto font-sans">
        
        <div class="px-3 pb-2 text-[10px] font-extrabold text-mono-500 uppercase tracking-widest font-mono">
            Menu Utama
        </div>

        <!-- Ringkasan Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
           class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.dashboard') ? 'active bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <i class="fa-solid fa-chart-line text-sm w-4 text-center {{ request()->routeIs('admin.dashboard') ? 'text-mono-950' : 'text-mono-400' }}"></i>
            <span>Dashboard Global</span>
        </a>

        <!-- Manajemen Pengguna (CRUD) -->
        <a href="{{ route('admin.users.index') }}"
           class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.users.*') ? 'active bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <i class="fa-solid fa-users text-sm w-4 text-center {{ request()->routeIs('admin.users.*') ? 'text-mono-950' : 'text-mono-400' }}"></i>
            <span>Data Pengguna (CRUD)</span>
        </a>

        <!-- Manajemen Paket & Tier (CRUD) -->
        <a href="{{ route('admin.packages.index') }}"
           class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.packages.*') ? 'active bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <i class="fa-solid fa-cubes text-sm w-4 text-center {{ request()->routeIs('admin.packages.*') ? 'text-mono-950' : 'text-mono-400' }}"></i>
            <span>Kelola Paket & Tier</span>
        </a>

        <!-- Riwayat Transaksi & Pembayaran -->
        <a href="{{ route('admin.orders.index') }}"
           class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.orders.*') ? 'active bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <i class="fa-solid fa-credit-card text-sm w-4 text-center {{ request()->routeIs('admin.orders.*') ? 'text-mono-950' : 'text-mono-400' }}"></i>
            <span>Riwayat Transaksi</span>
        </a>

        <!-- Kelola Testimoni Landing Page -->
        <a href="{{ route('admin.testimonials.index') }}"
           class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.testimonials.*') ? 'active bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <i class="fa-solid fa-comments text-sm w-4 text-center {{ request()->routeIs('admin.testimonials.*') ? 'text-mono-950' : 'text-mono-400' }}"></i>
            <span>Testimoni Landing</span>
        </a>

        <!-- Dokumentasi REST API -->
        <a href="{{ route('admin.docs') }}"
           class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.docs') ? 'active bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <i class="fa-solid fa-book-bookmark text-sm w-4 text-center {{ request()->routeIs('admin.docs') ? 'text-mono-950' : 'text-mono-400' }}"></i>
            <span>Dokumentasi REST API</span>
        </a>

        <div class="px-3 pt-4 pb-2 text-[10px] font-extrabold text-mono-500 uppercase tracking-widest font-mono">
            Pengaturan Akun
        </div>

        <!-- Profil & Password -->
        <a href="{{ route('user.profile') }}"
           class="sidebar-nav-item flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('user.profile') ? 'active bg-mono-50 text-mono-950 shadow-md font-extrabold' : 'text-mono-300 hover:text-white hover:bg-mono-800/60' }}">
            <i class="fa-solid fa-user-gear text-sm w-4 text-center {{ request()->routeIs('user.profile') ? 'text-mono-950' : 'text-mono-400' }}"></i>
            <span>Profil & Password</span>
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
                    <div class="text-[10px] text-mono-400 font-mono font-semibold">Super Admin</div>
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
