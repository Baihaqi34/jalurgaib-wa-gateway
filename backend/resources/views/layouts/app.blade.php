<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'JalurGaib WA Gateway')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <style>
        :root {
            --mono-bg: #09090b;
            --mono-surface: #121215;
            --mono-elevated: #18181b;
            --mono-border: #27272a;
            --mono-border-light: #3f3f46;
            --mono-text: #fafafa;
            --mono-muted: #a1a1aa;
        }

        /* Dynamic Monochromatic Logo */
        .jalurgaib-logo {
            object-fit: contain;
            transition: filter 0.2s ease, transform 0.2s ease;
            filter: brightness(0); /* Black on light background */
        }
        html.dark .jalurgaib-logo,
        [data-theme="dark"] .jalurgaib-logo,
        .dark .jalurgaib-logo {
            filter: brightness(0) invert(1); /* White on dark background */
        }

        body {
            background-color: #09090b;
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 255, 255, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 255, 255, 0.02) 0px, transparent 50%);
            background-attachment: fixed;
            color: #fafafa;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Monochromatic Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #09090b;
        }
        ::-webkit-scrollbar-thumb {
            background: #27272a;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #3f3f46;
        }

        /* Monochromatic Cards */
        .mono-card {
            background: #121215;
            border: 1px solid #27272a;
            border-radius: 1.25rem;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .mono-card:hover {
            border-color: #3f3f46;
        }

        .gradient-border-card {
            background: #121215;
            border: 1px solid #27272a;
            border-radius: 1.25rem;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .gradient-border-card:hover {
            border-color: #3f3f46;
        }

        .gradient-text-main,
        .gradient-text-emerald {
            color: #fafafa;
            background: none;
            -webkit-text-fill-color: initial;
        }

        /* Monochromatic Inputs */
        .mono-input {
            background-color: #09090b;
            border: 1px solid #27272a;
            border-radius: 0.75rem;
            color: #fafafa;
            transition: all 0.2s ease;
        }
        .mono-input:focus {
            outline: none;
            border-color: #f4f4f5;
            box-shadow: 0 0 0 1px #f4f4f5;
        }

        /* Monochromatic Buttons */
        .mono-btn-primary {
            background-color: #fafafa;
            color: #09090b;
            font-weight: 700;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.1);
        }
        .mono-btn-primary:hover {
            background-color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.18);
        }
        .mono-btn-primary:active {
            transform: translateY(0);
        }

        .mono-btn-secondary {
            background-color: #18181b;
            color: #e4e4e7;
            border: 1px solid #27272a;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }
        .mono-btn-secondary:hover {
            background-color: #27272a;
            color: #ffffff;
            border-color: #3f3f46;
        }

        /* Monochromatic Badges */
        .mono-badge {
            background-color: #18181b;
            border: 1px solid #27272a;
            color: #e4e4e7;
            border-radius: 9999px;
            font-weight: 600;
        }

        /* Custom Monochromatic SweetAlert2 */
        .swal2-popup.swal2-dark-custom {
            background: #121215 !important;
            border: 1px solid #27272a !important;
            border-radius: 1.5rem !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8) !important;
            color: #fafafa !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            padding: 1.75rem !important;
        }
        .swal2-title {
            color: #ffffff !important;
            font-size: 1.15rem !important;
            font-weight: 700 !important;
        }
        .swal2-html-container {
            color: #a1a1aa !important;
            font-size: 0.85rem !important;
        }
        .swal2-confirm {
            border-radius: 0.75rem !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            padding: 0.6rem 1.25rem !important;
            background: #fafafa !important;
            color: #09090b !important;
            box-shadow: 0 4px 14px rgba(255, 255, 255, 0.12) !important;
        }
        .swal2-cancel {
            border-radius: 0.75rem !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            padding: 0.6rem 1.25rem !important;
            background: #18181b !important;
            color: #d4d4d8 !important;
            border: 1px solid #27272a !important;
        }
        .swal2-input, .swal2-textarea {
            background: #09090b !important;
            border: 1px solid #27272a !important;
            border-radius: 0.75rem !important;
            color: #fafafa !important;
            font-size: 0.85rem !important;
        }
        .swal2-input:focus, .swal2-textarea:focus {
            border-color: #fafafa !important;
            box-shadow: 0 0 0 1px #fafafa !important;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-mono-100 font-sans flex flex-col md:flex-row antialiased selection:bg-mono-100 selection:text-mono-950 relative overflow-x-hidden" x-data="{ sidebarOpen: false }">

    <!-- Subtle Monochromatic Background Glow -->
    <div class="fixed top-[-10%] left-[-10%] w-[45vw] h-[45vw] rounded-full bg-white/[0.015] blur-[140px] pointer-events-none z-0"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] rounded-full bg-white/[0.015] blur-[140px] pointer-events-none z-0"></div>

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-40 md:hidden" x-transition.opacity></div>

    <!-- SIDEBAR -->
    @if(auth()->check() && auth()->user()->isAdmin())
        @include('layouts.sidebar-admin')
    @else
        @include('layouts.sidebar-user')
    @endif

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0 md:pl-64">
        
        <!-- TOP NAVBAR -->
        @include('layouts.navbar')

        <!-- MAIN PAGE CONTENT -->
        <main class="flex-1 w-full px-6 py-6">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-mono-700/60 py-4 px-6 text-center text-xs text-mono-500 font-medium">
            JalurGaib WA Gateway &copy; {{ date('Y') }} • High-Performance Architecture (Laravel + Go Engine)
        </footer>
    </div>

    <!-- Global SweetAlert2 Engine & Flash Toasts -->
    <script>
        const CustomSwal = Swal.mixin({
            customClass: {
                popup: 'swal2-dark-custom',
                confirmButton: 'px-4 py-2 bg-white hover:bg-mono-200 text-mono-950 rounded-xl font-bold transition mx-1.5 shadow-md',
                cancelButton: 'px-4 py-2 bg-mono-850 hover:bg-mono-800 text-mono-300 rounded-xl font-semibold transition mx-1.5 border border-mono-700'
            },
            buttonsStyling: false
        });

        const ToastSwal = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            customClass: {
                popup: 'swal2-dark-custom !p-3.5 !rounded-2xl shadow-2xl'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        window.AppSwal = {
            success(title, text = '') {
                return CustomSwal.fire({
                    icon: 'success',
                    title: title,
                    text: text,
                    iconColor: '#fafafa'
                });
            },
            error(title, text = '') {
                return CustomSwal.fire({
                    icon: 'error',
                    title: title,
                    text: text,
                    iconColor: '#a1a1aa'
                });
            },
            warning(title, text = '') {
                return CustomSwal.fire({
                    icon: 'warning',
                    title: title,
                    text: text,
                    iconColor: '#d4d4d8'
                });
            },
            toastSuccess(message) {
                return ToastSwal.fire({
                    icon: 'success',
                    title: message,
                    iconColor: '#fafafa'
                });
            },
            toastError(message) {
                return ToastSwal.fire({
                    icon: 'error',
                    title: message,
                    iconColor: '#a1a1aa'
                });
            },
            toastWarning(message) {
                return ToastSwal.fire({
                    icon: 'warning',
                    title: message,
                    iconColor: '#d4d4d8'
                });
            },
            loading(title = 'Memproses...', text = 'Mohon tunggu sebentar.') {
                return CustomSwal.fire({
                    title: title,
                    text: text,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            close() {
                Swal.close();
            },
            async confirm(title, text, confirmText = 'Ya, Lanjutkan', isDanger = false) {
                const res = await CustomSwal.fire({
                    title: title,
                    text: text,
                    icon: isDanger ? 'warning' : 'question',
                    iconColor: '#fafafa',
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'swal2-dark-custom',
                        confirmButton: isDanger 
                            ? 'px-4 py-2 bg-white hover:bg-mono-200 text-mono-950 rounded-xl font-bold transition mx-1.5 shadow-md' 
                            : 'px-4 py-2 bg-white hover:bg-mono-200 text-mono-950 rounded-xl font-bold transition mx-1.5 shadow-md',
                        cancelButton: 'px-4 py-2 bg-mono-850 hover:bg-mono-800 text-mono-300 rounded-xl font-semibold transition mx-1.5 border border-mono-700'
                    }
                });
                return res.isConfirmed;
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            @if(session('success'))
                AppSwal.toastSuccess("{{ session('success') }}");
            @endif

            @if(session('error'))
                AppSwal.error("Gagal", "{{ session('error') }}");
            @endif

            @if(session('warning'))
                AppSwal.warning("Peringatan", "{{ session('warning') }}");
            @endif

            @if($errors->any())
                AppSwal.error("Kesalahan Input", "{!! implode('<br>', $errors->all()) !!}");
            @endif
        });
    </script>

    @stack('scripts')
</body>
</html>
