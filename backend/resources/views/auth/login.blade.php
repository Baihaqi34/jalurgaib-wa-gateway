<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — JalurGaib WA Gateway</title>
    <meta name="description" content="Masuk ke portal JalurGaib WA Gateway untuk mengelola perangkat WhatsApp, mengirim pesan otomatis, dan memantau integrasi REST API.">
    <link rel="canonical" href="{{ url('/login') }}">
    <!-- Adaptive Favicon Engine (Light logo for Dark Browser, Dark logo for Light Browser) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo-white.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo-black.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo-white.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo-black.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-white.png') }}" id="dynamic-favicon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#09090b">
    <script>
        (function() {
            const whiteFav = "{{ asset('images/logo-white.png') }}";
            const blackFav = "{{ asset('images/logo-black.png') }}";
            function syncFavicon() {
                const isDarkBrowser = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                const link = document.getElementById('dynamic-favicon');
                if (link) {
                    link.href = isDarkBrowser ? whiteFav : blackFav;
                }
            }
            syncFavicon();
            if (window.matchMedia) {
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', syncFavicon);
            }
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
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

        .logo-box {
            width: 4rem;
            height: 4rem;
            max-width: 4rem;
            max-height: 4rem;
            flex-shrink: 0;
        }

        .jalurgaib-logo {
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
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
    <div class="fixed top-[-15%] left-[-10%] w-[50vw] h-[50vw] rounded-full bg-white/[0.015] blur-[140px] pointer-events-none"></div>
    <div class="fixed bottom-[-15%] right-[-10%] w-[50vw] h-[50vw] rounded-full bg-white/[0.015] blur-[140px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex items-center justify-center logo-box w-16 h-16 rounded-3xl bg-mono-900 border border-mono-700 p-2 shadow-xl shadow-black/40 mb-4 ring-1 ring-mono-700 hover:border-mono-500 hover:scale-105 transition-all duration-200" title="Kembali ke Beranda">
                <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo w-full h-full object-contain">
            </a>
            <h1 class="text-3xl font-extrabold tracking-tight text-mono-50">JalurGaib WA Gateway</h1>
            <p class="text-mono-400 text-xs mt-1.5 font-medium">Masuk ke portal pengelolaan gateway WhatsApp Anda</p>
        </div>

        <!-- Login Card -->
        <div class="mono-card p-8 shadow-2xl">

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-mono-850 border border-mono-600 rounded-xl text-mono-200 text-xs flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4 flex-shrink-0 text-mono-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 p-3.5 bg-mono-850 border border-mono-600 rounded-xl text-mono-300 text-xs font-medium">
                    {{ session('info') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 bg-mono-850 border border-mono-600 rounded-xl text-mono-100 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form id="login-form" action="{{ route('login.post') }}" method="POST" onsubmit="handleLoginSubmit(event)" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-mono-300 mb-1.5">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com"
                           class="w-full mono-input px-4 py-2.5 text-sm placeholder-mono-500 font-medium">
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="block text-xs font-bold text-mono-300">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required placeholder="••••••••"
                               class="w-full mono-input px-4 py-2.5 pr-10 text-sm placeholder-mono-500 font-medium">
                        <button type="button" onclick="togglePasswordVisibility('password', 'toggle-password-icon')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-mono-500 hover:text-mono-200 transition-colors cursor-pointer" tabindex="-1" title="Lihat/Sembunyikan sandi">
                            <i id="toggle-password-icon" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 cursor-pointer text-mono-400 font-medium">
                        <input type="checkbox" name="remember" value="1" checked class="rounded bg-mono-950 border-mono-700 text-mono-100 focus:ring-0">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="w-full mt-2 py-3 px-4 mono-btn-primary text-sm font-bold shadow-md cursor-pointer">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-mono-800"></div>
                </div>
                <div class="relative flex justify-center text-xs uppercase">
                    <span class="bg-[#121215] px-3 text-mono-400 font-mono font-semibold tracking-wider text-[11px]">
                        atau masuk dengan
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
                <span>Lanjutkan dengan Google</span>
            </button>

            <div class="mt-6 pt-5 border-t border-mono-700/80 text-center text-xs text-mono-400">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="text-mono-50 font-bold hover:underline">Daftar Akun Baru</a>
            </div>
        </div>

        <p class="text-center text-xs text-mono-500 mt-6 font-medium">
            JalurGaib WA Gateway &copy; 2026 • Multi-Tenant Developer Platform
        </p>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
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

        // Check URL parameters for registered flash notice or prefilled email
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const emailParam = params.get('email');
            if (emailParam) {
                const emailInput = document.getElementById('email');
                if (emailInput) {
                    emailInput.value = emailParam;
                }
            }
            if (params.get('registered') === '1') {
                Swal.fire({
                    icon: 'info',
                    title: 'Verifikasi Email Dikirim!',
                    html: `
                        <div class="text-left text-xs text-mono-300 space-y-2 leading-relaxed">
                            <p>Akun Anda telah terdaftar. Kami telah mengirimkan <b>link verifikasi resmi</b> ke inbox/spam email Anda.</p>
                            <p class="text-amber-400 font-semibold">Silakan klik link verifikasi tersebut sebelum melakukan login.</p>
                        </div>
                    `,
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'swal2-dark-custom',
                        confirmButton: 'mono-btn-primary px-5 py-2 text-xs font-bold'
                    }
                });
            }
        });

        async function handleLoginSubmit(e) {
            // If Firebase is not configured, fallback to native Laravel POST
            if (!firebaseConfig.apiKey || firebaseConfig.apiKey.trim() === '') {
                Swal.fire({
                    title: 'Memverifikasi...',
                    text: 'Sedang memeriksa kredensial akun...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    customClass: { popup: 'swal2-dark-custom' },
                    didOpen: () => Swal.showLoading()
                });
                return true;
            }

            e.preventDefault();

            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const email = emailInput ? emailInput.value.trim() : '';
            const password = passwordInput ? passwordInput.value : '';

            if (!email || !password) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Belum Lengkap',
                    text: 'Silakan masukkan email dan kata sandi Anda.',
                    customClass: { popup: 'swal2-dark-custom' }
                });
                return;
            }

            Swal.fire({
                title: 'Memverifikasi...',
                text: 'Sedang memeriksa status akun & verifikasi email...',
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: { popup: 'swal2-dark-custom' },
                didOpen: () => Swal.showLoading()
            });

            try {
                // 1. Authenticate with Firebase Email/Password
                const userCredential = await firebase.auth().signInWithEmailAndPassword(email, password);

                // Reload user to ensure we have the freshest emailVerified state from Google
                await userCredential.user.reload();
                const isVerified = userCredential.user.emailVerified;

                if (!isVerified) {
                    await firebase.auth().signOut();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Email Belum Diverifikasi',
                        html: `
                            <div class="text-left text-xs leading-relaxed space-y-3 text-mono-300">
                                <p>Alamat email <b>${email}</b> belum diverifikasi.</p>
                                <p>Silakan buka inbox atau folder spam email Anda dan klik link konfirmasi resmi yang telah kami kirimkan.</p>
                                <p class="text-mono-400 text-[11px]">Belum menerima link verifikasi dari Google?</p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Kirim Ulang Link',
                        cancelButtonText: 'Tutup',
                        customClass: {
                            popup: 'swal2-dark-custom',
                            confirmButton: 'mono-btn-primary px-4 py-2 text-xs font-bold',
                            cancelButton: 'bg-mono-800 text-mono-300 px-4 py-2 text-xs rounded-xl hover:bg-mono-700'
                        }
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            await resendVerificationEmail(email, password);
                        }
                    });
                    return;
                }

                // 2. Email is verified! Get ID Token and authenticate with Laravel session
                const idToken = await userCredential.user.getIdToken(true);

                const response = await fetch("{{ route('auth.firebase') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ id_token: idToken })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Masuk!',
                        text: 'Mengarahkan ke Dashboard...',
                        showConfirmButton: false,
                        timer: 1000,
                        customClass: { popup: 'swal2-dark-custom' }
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Masuk',
                        text: data.message || 'Terjadi kesalahan saat memproses sesi login.',
                        customClass: { popup: 'swal2-dark-custom' }
                    });
                }

            } catch (error) {
                console.warn("Firebase sign-in result:", error);

                // If user doesn't exist in Firebase (e.g. admin@jalurgaib.com seeded in Laravel),
                // fallback to native Laravel authentication!
                if (error.code === 'auth/user-not-found' || error.code === 'auth/invalid-credential') {
                    document.getElementById('login-form').submit();
                    return;
                }

                let errorMsg = error.message;
                if (error.code === 'auth/wrong-password') {
                    errorMsg = 'Kata sandi yang Anda masukkan salah.';
                } else if (error.code === 'auth/too-many-requests') {
                    errorMsg = 'Terlalu banyak percobaan gagal. Silakan tunggu beberapa saat lagi.';
                } else if (error.code === 'auth/invalid-email') {
                    errorMsg = 'Format alamat email tidak valid.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Masuk',
                    text: errorMsg,
                    customClass: { popup: 'swal2-dark-custom' }
                });
            }
        }

        async function resendVerificationEmail(email, password) {
            Swal.fire({
                title: 'Mengirim Ulang...',
                text: 'Sedang mengirimkan link verifikasi baru ke email Anda...',
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: { popup: 'swal2-dark-custom' },
                didOpen: () => Swal.showLoading()
            });

            try {
                const cred = await firebase.auth().signInWithEmailAndPassword(email, password);
                await cred.user.sendEmailVerification();
                await firebase.auth().signOut();

                Swal.fire({
                    icon: 'success',
                    title: 'Link Terkirim!',
                    text: 'Link verifikasi baru telah dikirimkan ke ' + email + '. Silakan cek inbox atau folder spam Anda.',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'swal2-dark-custom',
                        confirmButton: 'mono-btn-primary px-5 py-2 text-xs font-bold'
                    }
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Mengirim Ulang',
                    text: err.message || 'Terjadi kesalahan saat mengirim ulang email verifikasi.',
                    customClass: { popup: 'swal2-dark-custom' }
                });
            }
        }

        async function handleGoogleSignIn() {
            if (!firebaseConfig.apiKey || firebaseConfig.apiKey.trim() === '') {
                Swal.fire({
                    icon: 'info',
                    title: 'Firebase Belum Dikonfigurasi',
                    html: '<div class="text-left text-xs leading-relaxed text-mono-300">Untuk mengaktifkan login Google Firebase, silakan lengkapi variabel berikut di file <code>.env</code> Anda:<br><br><pre class="bg-mono-950 p-2.5 rounded-lg border border-mono-700 font-mono text-[11px] text-mono-200">FIREBASE_API_KEY=...\nFIREBASE_AUTH_DOMAIN=...\nFIREBASE_PROJECT_ID=...</pre></div>',
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

                Swal.fire({
                    title: 'Memverifikasi Akun...',
                    text: 'Sedang memproses masuk...',
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
                        id_token: idToken
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil Masuk!',
                        text: data.message || 'Mengarahkan ke Dashboard...',
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
