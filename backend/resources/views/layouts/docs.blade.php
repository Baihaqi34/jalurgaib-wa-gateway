<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dokumentasi REST API — JalurGaib WA Gateway')</title>

    <meta name="description" content="Dokumentasi resmi integrasi REST API WhatsApp Gateway JalurGaib. Panduan lengkap Laravel, cURL, Node.js, PHP, dan Python dengan webhook real-time.">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/docs') }}">

    <!-- Adaptive Favicon Engine -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo-white.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo-black.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo-white.png') }}" media="(prefers-color-scheme: dark)">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo-black.png') }}" media="(prefers-color-scheme: light)">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-white.png') }}" id="dynamic-favicon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <meta name="theme-color" content="#09090b">

    <!-- Immediate Theme Initialization (No FOUC) -->
    <script>
        (function() {
            const isLightPreferred = localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches);
            if (isLightPreferred) {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            } else {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            }
        })();
    </script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

        /* Dynamic Monochromatic Logo Filter */
        .jalurgaib-logo {
            object-fit: contain;
            transition: filter 0.2s ease, transform 0.2s ease;
            filter: brightness(0); /* Black in Light Mode */
        }
        html.dark .jalurgaib-logo,
        .dark .jalurgaib-logo {
            filter: brightness(0) invert(1); /* White in Dark Mode */
        }

        body {
            background-color: #09090b;
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 255, 255, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 255, 255, 0.02) 0px, transparent 50%);
            background-attachment: fixed;
            color: #fafafa;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        /* Monochromatic Cards */
        .mono-card {
            background-color: #121215;
            border: 1px solid #27272a;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.5);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        .mono-card:hover {
            border-color: #3f3f46;
        }

        /* Buttons */
        .mono-btn-primary {
            background-color: #fafafa;
            color: #09090b;
            border: 1px solid #fafafa;
            font-weight: 700;
            border-radius: 0.75rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .mono-btn-primary:hover {
            background-color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(255, 255, 255, 0.18);
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

        /* Code Terminal: Always crisp dark IDE aesthetic in both themes */
        .code-terminal {
            background-color: #090d16 !important;
            border: 1px solid #1e293b !important;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        .code-terminal pre {
            color: #e2e8f0 !important;
            font-family: 'JetBrains Mono', monospace !important;
            line-height: 1.6;
        }
        .code-terminal code {
            color: #e2e8f0 !important;
            font-family: 'JetBrains Mono', monospace !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
        }

        /* Inline code tags */
        code:not(pre code) {
            font-family: 'JetBrains Mono', monospace;
            padding: 0.15rem 0.45rem;
            border-radius: 0.375rem;
            font-size: 0.825em;
            font-weight: 600;
        }
        html.dark code:not(pre code) {
            background-color: #1e1e24;
            color: #f4f4f5;
            border: 1px solid #2e2e34;
        }
        html.light code:not(pre code) {
            background-color: #f1f5f9;
            color: #0f172a;
            border: 1px solid #e2e8f0;
        }

        /* HTTP Method Badges */
        .method-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            letter-spacing: 0.05em;
        }
        html.dark .method-post {
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        html.light .method-post {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        html.dark .method-get {
            background-color: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }
        html.light .method-get {
            background-color: #f0f9ff;
            color: #0284c7;
            border: 1px solid #bae6fd;
        }

        /* Language Tab Switcher */
        .docs-tab-nav {
            display: flex;
            gap: 0.375rem;
            padding: 0.25rem;
            border-radius: 0.875rem;
            overflow-x: auto;
        }
        html.dark .docs-tab-nav {
            background-color: #09090b;
            border: 1px solid #27272a;
        }
        html.light .docs-tab-nav {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
        }
        .docs-tab-btn {
            padding: 0.375rem 0.875rem;
            border-radius: 0.625rem;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        html.dark .docs-tab-btn.active {
            background-color: #fafafa;
            color: #09090b;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }
        html.dark .docs-tab-btn:not(.active) {
            color: #a1a1aa;
        }
        html.dark .docs-tab-btn:not(.active):hover {
            color: #ffffff;
            background-color: #1e1e24;
        }
        html.light .docs-tab-btn.active {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.15);
        }
        html.light .docs-tab-btn:not(.active) {
            color: #64748b;
        }
        html.light .docs-tab-btn:not(.active):hover {
            color: #0f172a;
            background-color: #e2e8f0;
        }

        /* Callout Tips Box */
        .docs-callout {
            border-radius: 0.875rem;
            padding: 1rem;
            font-size: 0.75rem;
            line-height: 1.5;
        }
        html.dark .docs-callout {
            background-color: rgba(32, 32, 36, 0.7);
            border: 1px solid #27272a;
            color: #a1a1aa;
        }
        html.light .docs-callout {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
        }

        /* ─── LIGHT MODE THEME OVERRIDES ─── */
        html.light {
            color-scheme: light;
        }
        html.light body {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            background-image: 
                radial-gradient(at 0% 0%, rgba(15, 23, 42, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.02) 0px, transparent 50%) !important;
        }

        /* Cards in Light Mode */
        html.light .mono-card {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #0f172a !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 6px 20px -4px rgba(15, 23, 42, 0.04) !important;
        }
        html.light .mono-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.07) !important;
        }

        /* Universal Surface Classes */
        html.light [class*="bg-mono-950"]:not(.code-terminal) {
            background-color: #f8fafc !important;
        }
        html.light [class*="bg-mono-900"] {
            background-color: #ffffff !important;
        }
        html.light [class*="bg-mono-850"] {
            background-color: #f1f5f9 !important;
        }
        html.light [class*="bg-mono-800"] {
            background-color: #e2e8f0 !important;
        }
        html.light [class*="bg-mono-700"] {
            background-color: #cbd5e1 !important;
        }

        /* Borders */
        html.light [class*="border-mono-800"],
        html.light [class*="border-mono-700"],
        html.light [class*="border-mono-600"] {
            border-color: #e2e8f0 !important;
        }
        html.light [class*="border-mono-500"] {
            border-color: #cbd5e1 !important;
        }

        /* Typography Hierarchy */
        html.light [class*="text-mono-50"],
        html.light [class*="text-mono-100"] {
            color: #0f172a !important;
        }
        html.light [class*="text-mono-200"],
        html.light [class*="text-mono-300"] {
            color: #334155 !important;
        }
        html.light [class*="text-mono-400"] {
            color: #64748b !important;
        }
        html.light [class*="text-mono-500"] {
            color: #94a3b8 !important;
        }
        html.light [class*="text-mono-950"] {
            color: #0f172a !important;
        }

        /* Badges & Tags */
        html.light span[class*="bg-mono-800"],
        html.light span[class*="bg-mono-850"] {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            border-color: #e2e8f0 !important;
        }

        /* Header in Light Mode */
        html.light header {
            background-color: rgba(255, 255, 255, 0.9) !important;
            border-color: #e2e8f0 !important;
            backdrop-filter: blur(12px) !important;
        }
        html.light header .w-9.h-9 {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        /* Footer in Light Mode */
        html.light footer {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
        }
        html.light footer div,
        html.light footer span,
        html.light footer a {
            color: #64748b !important;
        }
        html.light footer a:hover {
            color: #0f172a !important;
        }

        /* Buttons in Light Mode */
        html.light .mono-btn-primary {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border-color: #0f172a !important;
        }
        html.light .mono-btn-primary:hover {
            background-color: #1e293b !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15) !important;
        }
        html.light .mono-btn-secondary {
            background-color: #ffffff !important;
            color: #334155 !important;
            border-color: #cbd5e1 !important;
        }
        html.light .mono-btn-secondary:hover {
            background-color: #f8fafc !important;
            color: #0f172a !important;
            border-color: #94a3b8 !important;
        }

        /* Theme Icon Visibility */
        html.dark .theme-icon-sun { display: inline-block !important; }
        html.dark .theme-icon-moon { display: none !important; }
        html.light .theme-icon-sun { display: none !important; }
        html.light .theme-icon-moon { display: inline-block !important; }

        /* Scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        html.dark ::-webkit-scrollbar-thumb { background: #27272a; border-radius: 4px; }
        html.light ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>

    <script>
        window.toggleTheme = function() {
            const isDark = document.documentElement.classList.contains('dark');
            if (isDark) {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        };
    </script>
</head>
<body class="min-h-screen font-sans flex flex-col antialiased selection:bg-mono-100 selection:text-mono-950 relative overflow-x-hidden">

    <!-- Top Navigation Bar for Documentation -->
    <header class="h-16 px-4 sm:px-8 border-b border-mono-700/80 bg-mono-900/90 backdrop-blur-xl sticky top-0 z-40 flex items-center justify-between transition-colors">
        <!-- Left: Logo & Docs Title -->
        <div class="flex items-center gap-3.5">
            <a href="{{ route('docs') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-mono-950 border border-mono-700 flex items-center justify-center p-1.5 shadow-sm transition-colors">
                    <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo w-full h-full object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-extrabold text-mono-50 tracking-tight text-sm">JalurGaib</span>
                        <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-mono-800 text-mono-300 border border-mono-700 tracking-wider uppercase">REST API Docs</span>
                    </div>
                    <div class="text-[10px] font-mono text-mono-400">Developer Integration Portal</div>
                </div>
            </a>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-3">
            <!-- Theme Switcher Button -->
            <button type="button" 
                    onclick="toggleTheme()" 
                    class="w-9 h-9 rounded-xl bg-mono-850 hover:bg-mono-800 border border-mono-700/80 text-mono-300 hover:text-white flex items-center justify-center transition shadow-sm" 
                    title="Ganti Mode Gelap / Terang">
                <i class="fa-solid fa-sun text-amber-400 text-xs theme-icon-sun"></i>
                <i class="fa-solid fa-moon text-indigo-500 text-xs theme-icon-moon"></i>
            </button>

            <!-- Dashboard / Login CTA -->
            @if(auth()->check())
                <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" 
                   class="px-4 py-2 rounded-xl bg-mono-850 hover:bg-mono-800 border border-mono-700 text-mono-200 hover:text-white text-xs font-bold transition inline-flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="px-4 py-2 rounded-xl mono-btn-primary font-bold text-xs inline-flex items-center gap-1.5 shadow-sm">
                    <span>Masuk ke Dashboard</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            @endif
        </div>
    </header>

    <!-- Main Docs Content Area (Clean Centered Canvas) -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 sm:py-10">
        @yield('content')
    </main>

    <!-- Clean Docs Footer -->
    <footer class="border-t border-mono-700/60 py-6 px-4 sm:px-6 text-center text-xs font-medium transition-colors">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <div>
                JalurGaib WA Gateway &copy; {{ date('Y') }} &bull; High-Performance WhatsApp Architecture
            </div>
            <div class="flex items-center gap-4 text-[11px] font-mono">
                <span>REST API v1.0</span>
                <span>&bull;</span>
                <span>Anti-Ban Engine</span>
                <span>&bull;</span>
                <a href="#top" class="hover:underline transition">Ke Atas &uarr;</a>
            </div>
        </div>
    </footer>

    <!-- SweetAlert Toast Engine -->
    <script>
        const ToastSwal = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            customClass: {
                popup: '!p-3.5 !rounded-2xl shadow-2xl !bg-[#121215] !text-[#fafafa] !border !border-[#27272a]'
            }
        });

        window.AppSwal = {
            toastSuccess(message) {
                return ToastSwal.fire({ icon: 'success', title: message, iconColor: '#34d399' });
            }
        };
    </script>
    @stack('scripts')
</body>
</html>
