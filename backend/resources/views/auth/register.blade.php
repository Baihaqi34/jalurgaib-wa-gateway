<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru — JalurGaib WA Gateway</title>
    <meta name="description" content="Daftar akun JalurGaib WA Gateway gratis sekarang. Nikmati akses REST API WhatsApp, multi-device session, webhook, dan anti-ban engine.">
    <link rel="canonical" href="{{ url('/register') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#09090b">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        mono: {
                            950: '#09090b',
                            900: '#121215',
                            850: '#18181b',
                            800: '#202024',
                            700: '#27272a',
                            600: '#3f3f46',
                            500: '#71717a',
                            400: '#a1a1aa',
                            300: '#d4d4d8',
                            200: '#e4e4e7',
                            100: '#f4f4f5',
                            50: '#fafafa',
                        }
                    }
                }
            }
        }
    </script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Firebase SDK (Compat) -->
    <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.8.0/firebase-auth-compat.js"></script>
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #09090b;
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 255, 255, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 255, 255, 0.02) 0px, transparent 50%);
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #fafafa;
        }

        .mono-card {
            background: #121215;
            border: 1px solid #27272a;
            border-radius: 1.5rem;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
        }

        .jalurgaib-logo {
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .mono-input {
            background-color: #09090b;
            border: 1px solid #27272a;
            border-radius: 0.75rem;
            color: #fafafa;
            transition: all 0.2s ease;
        }
        .mono-input:focus {
            outline: none;
            border-color: #fafafa;
            box-shadow: 0 0 0 1px #fafafa;
        }

        .mono-btn-primary {
            background-color: #fafafa;
            color: #09090b;
            font-weight: 700;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.12);
        }
        .mono-btn-primary:hover {
            background-color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.18);
        }
        .mono-btn-primary:active {
            transform: translateY(0);
        }

        /* Custom Dark SweetAlert */
        .swal2-popup.swal2-dark-custom {
            background: #121215 !important;
            border: 1px solid #27272a !important;
            border-radius: 1.5rem !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8) !important;
            color: #fafafa !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .swal2-title { color: #ffffff !important; font-size: 1.15rem !important; font-weight: 700 !important; }
        .swal2-html-container { color: #a1a1aa !important; font-size: 0.85rem !important; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-mono-100 selection:text-mono-950 relative overflow-x-hidden">

    <!-- Subtle Monochromatic Ambient Glow -->
    <div class="fixed top-[-15%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-white/[0.015] blur-[140px] pointer-events-none"></div>
    <div class="fixed bottom-[-15%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-white/[0.015] blur-[140px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10 my-8">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-mono-900 border border-mono-700 p-2 shadow-xl shadow-black/40 mb-3 ring-1 ring-mono-700">
                <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo w-full h-full object-contain">
            </div>
            @if(isset($selectedPackage) && !$selectedPackage->isFree())
                <h1 class="text-3xl font-extrabold tracking-tight text-mono-50">Daftar & Berlangganan</h1>
                <p class="text-mono-400 text-xs mt-1.5 font-medium">Lengkapi pendaftaran akun untuk mengaktifkan paket {{ $selectedPackage->name }}</p>
            @else
                <h1 class="text-3xl font-extrabold tracking-tight text-mono-50">Daftar Akun Baru</h1>
                <p class="text-mono-400 text-xs mt-1.5 font-medium">Dapatkan akses instan & API Key JalurGaib WA Gateway</p>
            @endif
        </div>

        <!-- Register Card -->
        <div class="mono-card p-6 sm:p-8 shadow-2xl">

            <!-- Selected Package Preview Card -->
            @if(isset($selectedPackage))
                <div class="mb-5 p-4 bg-mono-950 border border-mono-700/80 rounded-2xl flex items-center justify-between shadow-sm">
                    <div class="min-w-0 pr-3">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] font-mono font-bold uppercase tracking-wider text-mono-400">Paket Pilihan:</span>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-mono-50 text-mono-950 shadow-sm">
                                {{ $selectedPackage->badge ?? ($selectedPackage->price == 0 ? 'Gratis' : 'Aktif') }}
                            </span>
                        </div>
                        <div class="text-base font-extrabold text-mono-50 mt-1 truncate">{{ $selectedPackage->name }}</div>
                        <div class="text-xs font-mono font-bold text-mono-300 mt-0.5">
                            @if($selectedPackage->price == 0)
                                Gratis Selamanya
                            @else
                                Rp {{ number_format($selectedPackage->price, 0, ',', '.') }} <span class="text-mono-500 font-normal">/ bulan</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <div class="w-10 h-10 rounded-xl bg-mono-850 border border-mono-700 flex items-center justify-center text-amber-400 text-sm shadow-sm">
                            <i class="fa-solid fa-crown"></i>
                        </div>
                        <a href="{{ route('register') }}" class="text-[10px] text-mono-400 hover:text-white underline font-mono" title="Batal memilih paket berbayar">Ganti</a>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-mono-850 border border-mono-600 rounded-xl text-mono-200 text-xs flex items-center gap-2 font-medium">
                    <i class="fa-solid fa-circle-exclamation text-red-400 text-sm flex-shrink-0"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" onsubmit="handleRegisterSubmit(event)" class="space-y-4">
                @csrf

                @if(isset($selectedPackage))
                    <input type="hidden" name="package_id" value="{{ $selectedPackage->id }}">
                @endif

                <div>
                    <label class="block text-xs font-bold text-mono-300 mb-1.5">Nama Lengkap / Instansi</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus placeholder="John Doe atau PT Sukses Mandiri"
                           class="w-full mono-input px-4 py-2.5 text-sm placeholder-mono-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-mono-300 mb-1.5">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@perusahaan.com"
                           class="w-full mono-input px-4 py-2.5 text-sm placeholder-mono-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-mono-300 mb-1.5">Kata Sandi</label>
                    <input type="password" id="password" name="password" required placeholder="Minimal 8 karakter"
                           class="w-full mono-input px-4 py-2.5 text-sm placeholder-mono-500 font-medium">
                </div>

                <div>
                    <label class="block text-xs font-bold text-mono-300 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi"
                           class="w-full mono-input px-4 py-2.5 text-sm placeholder-mono-500 font-medium">
                </div>

                <div class="pt-2">
                    @if(isset($selectedPackage) && !$selectedPackage->isFree())
                        <button type="submit" class="w-full py-3 px-4 mono-btn-primary text-sm font-bold shadow-md flex items-center justify-center gap-2">
                            <span>Daftar & Lanjut Bayar (Rp {{ number_format($selectedPackage->price, 0, ',', '.') }})</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    @else
                        <button type="submit" class="w-full py-3 px-4 mono-btn-primary text-sm font-bold shadow-md flex items-center justify-center gap-2">
                            <span>Daftar & Dapatkan API Key</span>
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    @endif
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-mono-800"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-[#121215] px-3 text-mono-400 font-mono font-semibold tracking-wider text-[11px]">
                        atau daftar dengan
                    </span>
                </div>
            </div>

            <!-- Google Sign-In Button -->
            <button type="button" id="btn-google-auth" onclick="handleGoogleSignIn()" class="w-full py-2.5 px-4 bg-mono-900 hover:bg-mono-850 active:bg-mono-800 border border-mono-700 hover:border-mono-600 rounded-xl text-mono-100 text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-3 shadow-sm hover:shadow cursor-pointer">
                <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.8-2.4 3.65v3.03h3.88c2.28-2.1 3.66-5.2 3.66-9.12z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.03c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.13C3.27 21.43 7.35 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.29c-.25-.72-.38-1.49-.38-2.29s.13-1.57.38-2.29V6.57H1.25C.45 8.16 0 9.96 0 12s.45 3.84 1.25 5.43l4.03-3.14z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.35 0 3.27 2.57 1.25 6.57l4.03 3.14c.95-2.83 3.6-4.96 6.72-4.96z"/>
                </svg>
                <span>Daftar dengan Google</span>
            </button>

            <div class="mt-6 pt-5 border-t border-mono-700/80 text-center text-xs text-mono-400">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-mono-50 font-bold hover:underline">Masuk ke Portal</a>
            </div>
        </div>

        <p class="text-center text-xs text-mono-500 mt-6 font-medium">
            JalurGaib WA Gateway &copy; 2026 • Multi-Tenant Developer Platform
        </p>
    </div>

    <script>
        function handleRegisterSubmit(e) {
            const isPaid = {{ (isset($selectedPackage) && !$selectedPackage->isFree()) ? 'true' : 'false' }};
            Swal.fire({
                title: isPaid ? 'Membuat Akun & Checkout...' : 'Membuat Akun...',
                text: isPaid ? 'Sedang mendaftarkan akun dan mengarahkan ke gateway pembayaran...' : 'Sedang memproses registrasi & generate API Key...',
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: { popup: 'swal2-dark-custom' },
                didOpen: () => Swal.showLoading()
            });
        }

        // Firebase Web SDK Configuration
        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}"
        };

        let firebaseApp = null;
        if (firebaseConfig.apiKey && firebaseConfig.apiKey.trim() !== '') {
            try {
                if (!firebase.apps.length) {
                    firebaseApp = firebase.initializeApp(firebaseConfig);
                } else {
                    firebaseApp = firebase.app();
                }
            } catch (e) {
                console.error("Firebase Init Error:", e);
            }
        }

        async function handleGoogleSignIn() {
            if (!firebaseConfig.apiKey || firebaseConfig.apiKey.trim() === '') {
                Swal.fire({
                    icon: 'info',
                    title: 'Firebase Belum Dikonfigurasi',
                    html: '<div class="text-left text-xs leading-relaxed text-mono-300">Untuk mengaktifkan pendaftaran/masuk via Google Firebase, silakan lengkapi variabel berikut di file <code>.env</code> Anda:<br><br><pre class="bg-mono-950 p-2.5 rounded-lg border border-mono-700 font-mono text-[11px] text-mono-200">FIREBASE_API_KEY=...\nFIREBASE_AUTH_DOMAIN=...\nFIREBASE_PROJECT_ID=...</pre></div>',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'swal2-dark-custom',
                        confirmButton: 'mono-btn-primary px-5 py-2 text-xs font-bold'
                    }
                });
                return;
            }

            try {
                Swal.fire({
                    title: 'Menghubungkan ke Google...',
                    text: 'Silakan pilih akun Google Anda di jendela popup.',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    customClass: { popup: 'swal2-dark-custom' },
                    didOpen: () => Swal.showLoading()
                });

                const provider = new firebase.auth.GoogleAuthProvider();
                provider.setCustomParameters({ prompt: 'select_account' });

                const result = await firebase.auth().signInWithPopup(provider);
                const idToken = await result.user.getIdToken();

                const isPaid = {{ (isset($selectedPackage) && !$selectedPackage->isFree()) ? 'true' : 'false' }};
                Swal.fire({
                    title: isPaid ? 'Menyiapkan Akun & Checkout...' : 'Memverifikasi Akun...',
                    text: isPaid ? 'Sedang menghubungkan ke gateway pembayaran...' : 'Sedang mendaftarkan akun...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    customClass: { popup: 'swal2-dark-custom' },
                    didOpen: () => Swal.showLoading()
                });

                const response = await fetch("{{ route('auth.firebase') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_token: idToken,
                        package_id: @json($selectedPackage->id ?? null)
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Mengarahkan...',
                        showConfirmButton: false,
                        timer: 1200,
                        customClass: { popup: 'swal2-dark-custom' }
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Masuk',
                        text: data.message || 'Terjadi kesalahan saat memproses data akun Google.',
                        confirmButtonText: 'Coba Lagi',
                        customClass: { popup: 'swal2-dark-custom' }
                    });
                }
            } catch (error) {
                console.error("Google Sign-In Error:", error);
                if (error.code === 'auth/popup-closed-by-user') {
                    Swal.close();
                    return;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Autentikasi Dibatalkan/Gagal',
                    text: error.message || 'Gagal login via Google.',
                    confirmButtonText: 'Tutup',
                    customClass: { popup: 'swal2-dark-custom' }
                });
            }
        }
    </script>
</body>
</html>
