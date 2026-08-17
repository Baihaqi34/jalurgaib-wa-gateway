<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru — JalurGaib WA Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
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
            <h1 class="text-3xl font-extrabold tracking-tight text-mono-50">Daftar Akun Baru</h1>
            <p class="text-mono-400 text-xs mt-1.5 font-medium">Dapatkan akses instan & API Key JalurGaib WA Gateway</p>
        </div>

        <!-- Register Card -->
        <div class="mono-card p-8 shadow-2xl">

            @if($errors->any())
                <div class="mb-5 p-3.5 bg-mono-850 border border-mono-600 rounded-xl text-mono-200 text-xs flex items-center gap-2 font-medium">
                    <svg class="w-4 h-4 flex-shrink-0 text-mono-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" onsubmit="Swal.fire({title: 'Membuat Akun...', text: 'Sedang memproses registrasi & generate API Key...', allowOutsideClick: false, showConfirmButton: false, customClass: {popup: 'swal2-dark-custom'}, didOpen: () => Swal.showLoading()})" class="space-y-4">
                @csrf

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
                    <button type="submit" class="w-full py-3 px-4 mono-btn-primary text-sm font-bold shadow-md">
                        Daftar & Dapatkan API Key
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-mono-700/80 text-center text-xs text-mono-400">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="text-mono-50 font-bold hover:underline">Masuk ke Portal</a>
            </div>
        </div>

        <p class="text-center text-xs text-mono-500 mt-6 font-medium">
            JalurGaib WA Gateway &copy; 2026 • Multi-Tenant Developer Platform
        </p>
    </div>
</body>
</html>
