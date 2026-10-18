@extends('layouts.app')

@section('title', 'Profil & Keamanan Akun — JalurGaib WA Gateway')
@section('header_title', 'Profil & Keamanan Akun')
@section('header_subtitle', 'Kelola Informasi Akun, Email & Pembaruan Password Login')

@section('content')
<div class="space-y-8 font-sans max-w-6xl">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl text-emerald-300 text-xs flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-emerald-400 text-sm"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-400 hover:text-emerald-200">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-300 text-xs space-y-1 shadow-sm">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fa-solid fa-circle-exclamation text-red-400 text-sm"></i>
                <span>Terdapat kendala pada input Anda:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 pl-2 text-mono-300">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Profile Bento Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left: User Identity Card -->
        <div class="space-y-5">
            <!-- Account Overview -->
            <div class="mono-card p-6 text-center flex flex-col items-center">
                <div class="w-20 h-20 rounded-2xl bg-mono-800 border-2 border-mono-600 text-mono-50 flex items-center justify-center text-3xl font-extrabold shadow-md mb-4">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h3 class="text-base font-extrabold text-mono-50 tracking-tight">{{ $user->name }}</h3>
                <p class="text-xs text-mono-400 font-mono mt-0.5">{{ $user->email }}</p>

                <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-mono-850 text-mono-200 border border-mono-700 shadow-sm inline-flex items-center gap-1.5">
                        @if($user->isAdmin())
                            <i class="fa-solid fa-crown text-amber-400 text-[10px]"></i>
                            <span>Super Admin</span>
                        @else
                            <i class="fa-solid fa-user-check text-mono-400 text-[10px]"></i>
                            <span>Tenant User</span>
                        @endif
                    </span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 inline-flex items-center gap-1">
                        <i class="fa-solid fa-circle text-[6px] text-emerald-400 animate-pulse"></i>
                        <span>{{ ucfirst($user->status ?? 'Active') }}</span>
                    </span>
                </div>

                <div class="w-full mt-6 pt-5 border-t border-mono-800 text-left space-y-3 text-xs font-mono">
                    <div class="flex items-center justify-between">
                        <span class="text-mono-500">Paket Langganan:</span>
                        <span class="font-bold text-mono-100 font-sans">{{ $package->name ?? 'Free Tier' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-mono-500">Bergabung Sejak:</span>
                        <span class="text-mono-300">{{ $user->created_at->timezone('Asia/Jakarta')->format('d M Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-mono-500">Slot WhatsApp:</span>
                        <span class="text-mono-300">{{ $user->devices()->count() }} / {{ $user->getMaxDevices() }}</span>
                    </div>
                </div>
            </div>

            <!-- Security Recommendation -->
            <div class="mono-card p-5 space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-mono-100">
                    <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                    <span>Keamanan Akun</span>
                </div>
                <p class="text-[11px] text-mono-400 leading-relaxed">
                    Gunakan kombinasi password yang kuat dengan minimal 8 karakter serta campuran huruf besar, huruf kecil, dan angka.
                </p>
            </div>
        </div>

        <!-- Right: Forms Column (2-Column Spanned) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card 1: Informasi Profil -->
            <div class="mono-card p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-mono-800">
                    <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 flex items-center justify-center text-mono-200">
                        <i class="fa-solid fa-user-pen text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-mono-50">Informasi Dasar Profil</h4>
                        <p class="text-xs text-mono-400 mt-0.5">Perbarui nama pengguna dan alamat email akun Anda</p>
                    </div>
                </div>

                <form action="{{ route('user.profile.update') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-user text-mono-400"></i>
                            <span>Nama Lengkap</span>
                            <span class="text-red-400">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full mono-input px-4 py-2.5 text-xs text-mono-100 font-medium">
                    </div>

                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-envelope text-mono-400"></i>
                            <span>Alamat Email</span>
                            <span class="text-red-400">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full mono-input px-4 py-2.5 text-xs text-mono-100 font-mono">
                        <p class="text-[11px] text-mono-500 mt-1">Email ini digunakan untuk login dan menerima invoice pembayaran.</p>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-2 shadow-md hover:scale-[1.01] transition">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Ubah Password Login -->
            <div class="mono-card p-6 sm:p-8" x-data="{ showCurrent: false, showNew: false, showConfirm: false }">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-mono-800">
                    <div class="w-10 h-10 rounded-xl bg-mono-800 border border-mono-700 flex items-center justify-center text-mono-200">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-mono-50">Ubah Password Login</h4>
                        <p class="text-xs text-mono-400 mt-0.5">Pastikan akun Anda tetap aman dengan memperbarui password berkala</p>
                    </div>
                </div>

                <form action="{{ route('user.profile.password') }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    @method('PUT')

                    <!-- Password Saat Ini -->
                    <div>
                        <label class="block text-mono-300 font-bold mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-key text-mono-400"></i>
                            <span>Password Saat Ini</span>
                            <span class="text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showCurrent ? 'text' : 'password'" name="current_password" required placeholder="Masukkan password Anda saat ini" class="w-full mono-input px-4 py-2.5 pr-10 text-xs text-mono-100 font-mono">
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-mono-400 hover:text-white transition">
                                <i :class="showCurrent ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password Baru -->
                        <div>
                            <label class="block text-mono-300 font-bold mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-mono-400"></i>
                                <span>Password Baru</span>
                                <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input :type="showNew ? 'text' : 'password'" name="password" required minlength="8" placeholder="Minimal 8 karakter" class="w-full mono-input px-4 py-2.5 pr-10 text-xs text-mono-100 font-mono">
                                <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-mono-400 hover:text-white transition">
                                    <i :class="showNew ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div>
                            <label class="block text-mono-300 font-bold mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-check-double text-mono-400"></i>
                                <span>Konfirmasi Password Baru</span>
                                <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" required minlength="8" placeholder="Ulangi password baru" class="w-full mono-input px-4 py-2.5 pr-10 text-xs text-mono-100 font-mono">
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-mono-400 hover:text-white transition">
                                    <i :class="showConfirm ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'" class="text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-2 shadow-md hover:scale-[1.01] transition">
                            <i class="fa-solid fa-lock text-xs"></i>
                            <span>Perbarui Password Login</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
