<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-key" content="{{ auth()->check() ? auth()->user()->getOrCreateApiKey() : '' }}">
    <title>@yield('title', 'JalurGaib WA Gateway')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
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
            outline: none !important;
        }

        /* Theme Icon Visibility */
        html.dark .theme-icon-sun { display: inline-block !important; }
        html.dark .theme-icon-moon { display: none !important; }
        html.light .theme-icon-sun { display: none !important; }
        html.light .theme-icon-moon { display: inline-block !important; }

        /* ─── MODERN BENTO & QUIET LUXURY: LIGHT MODE ARCHITECTURE ─── */
        html.light {
            color-scheme: light;
        }

        html.light body {
            background-color: #f8fafc !important; /* Slate 50 canvas */
            color: #0f172a !important;            /* Slate 900 text */
            background-image: 
                radial-gradient(at 0% 0%, rgba(15, 23, 42, 0.03) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.02) 0px, transparent 50%) !important;
        }

        /* Bento Surfaces & Cards */
        html.light .mono-card,
        html.light .gradient-border-card {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 6px 20px -4px rgba(15, 23, 42, 0.04) !important;
        }
        html.light .mono-card:hover,
        html.light .gradient-border-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.07) !important;
        }

        /* Universal Surface Classes with Opacity Catchers */
        html.light [class*="bg-mono-950"] {
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

        /* Hairline Crisp 1px Borders */
        html.light [class*="border-mono-800"],
        html.light [class*="border-mono-700"],
        html.light [class*="border-mono-600"] {
            border-color: #e2e8f0 !important;
        }
        html.light [class*="border-mono-500"] {
            border-color: #cbd5e1 !important;
        }
        html.light hr {
            border-color: #e2e8f0 !important;
        }

        /* Typography Hierarchy (Quiet Luxury Tabular) */
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

        /* Badges & Micro-tags */
        html.light span[class*="bg-mono-800"],
        html.light span[class*="bg-mono-850"] {
            background-color: #f1f5f9 !important;
            color: #334155 !important;
            border-color: #e2e8f0 !important;
        }
        html.light span[class*="bg-mono-950"] {
            background-color: #f8fafc !important;
            color: #334155 !important;
            border-color: #e2e8f0 !important;
        }
        html.light .mono-badge {
            background-color: #f1f5f9 !important;
            border-color: #e2e8f0 !important;
            color: #1e293b !important;
        }

        /* ─── SIDEBAR ARCHITECTURE (MODERN BENTO) ─── */
        html.light aside {
            background-color: #ffffff !important;
            border-right: 1px solid #e2e8f0 !important;
            box-shadow: 1px 0 10px 0 rgba(15, 23, 42, 0.02) !important;
        }
        html.light aside [class*="border-b"],
        html.light aside [class*="border-t"] {
            border-color: #e2e8f0 !important;
        }
        html.light aside div.text-mono-500,
        html.light aside div.font-mono.uppercase {
            color: #64748b !important;
            letter-spacing: 0.08em !important;
            font-weight: 700 !important;
        }

        /* Active Navigation Item in Light Mode: Quiet Luxury Slate-900 Pill */
        html.light .sidebar-nav-item.active,
        html.light aside a.active,
        html.light aside a.bg-mono-50 {
            background-color: #0f172a !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15) !important;
        }
        html.light .sidebar-nav-item.active i,
        html.light aside a.active i,
        html.light aside a.bg-mono-50 i {
            color: #ffffff !important;
        }

        /* Inactive Navigation Item in Light Mode */
        html.light .sidebar-nav-item:not(.active),
        html.light aside a:not(.active):not(.bg-mono-50) {
            color: #475569 !important;
            font-weight: 600 !important;
        }
        html.light .sidebar-nav-item:not(.active) i,
        html.light aside a:not(.active):not(.bg-mono-50) i {
            color: #64748b !important;
        }
        html.light .sidebar-nav-item:not(.active):hover,
        html.light aside a:not(.active):not(.bg-mono-50):hover {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
        }
        html.light .sidebar-nav-item:not(.active):hover i,
        html.light aside a:not(.active):not(.bg-mono-50):hover i {
            color: #0f172a !important;
        }

        /* Sidebar Footer / User Profile */
        html.light aside > div:last-child {
            background-color: #f8fafc !important;
            border-top: 1px solid #e2e8f0 !important;
        }
        html.light aside > div:last-child .w-9.h-9 {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* ─── TOP NAVBAR ARCHITECTURE ─── */
        html.light header {
            background-color: rgba(255, 255, 255, 0.88) !important;
            backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        html.light header h2,
        html.light header h2 * {
            color: #0f172a !important;
        }
        html.light header [class*="text-mono-400"] {
            color: #64748b !important;
        }
        html.light header [class*="text-mono-600"] {
            color: #cbd5e1 !important;
        }
        html.light header [class*="border-mono-700"] {
            border-color: #e2e8f0 !important;
        }

        /* Telemetry Status Widget & Controls in Header */
        html.light header div[class*="bg-mono-850"],
        html.light header button[class*="bg-mono-850"] {
            background-color: #f1f5f9 !important;
            border: 1px solid #e2e8f0 !important;
            color: #334155 !important;
        }
        html.light header button[class*="bg-mono-850"]:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        html.light header button[class*="bg-mono-850"] .w-7.h-7 {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border: none !important;
        }

        /* Profile Dropdown Card */
        html.light div[class*="mono-card"][class*="z-50"] {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.1) !important;
        }
        html.light div[class*="mono-card"][class*="z-50"] > div.bg-mono-950 {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }

        /* ─── CURRENT PLAN & BENTO TELEMETRY WIDGET ─── */
        html.light .plan-star-box {
            background-color: #fffbeb !important;
            border: 1px solid #fde68a !important;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.12) !important;
        }
        html.light .quota-summary-box,
        html.light div.flex.items-center.gap-3[class*="bg-mono-950"] {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        }
        html.light .quota-summary-box .text-mono-400,
        html.light div.flex.items-center.gap-3[class*="bg-mono-950"] .text-mono-400 {
            color: #64748b !important;
            font-weight: 700 !important;
        }
        html.light .quota-summary-box .text-mono-50,
        html.light div.flex.items-center.gap-3[class*="bg-mono-950"] .text-mono-50 {
            color: #0f172a !important;
            font-weight: 800 !important;
            font-family: 'JetBrains Mono', monospace !important;
        }
        html.light .quota-summary-box .bg-mono-700,
        html.light div.flex.items-center.gap-3[class*="bg-mono-950"] .bg-mono-700 {
            background-color: #e2e8f0 !important;
        }

        /* ─── PRICING & TIER BENTO GRID ─── */
        html.light .price-amount,
        html.light span.text-3xl.font-extrabold.font-mono,
        html.light span.text-2xl.font-extrabold.font-mono {
            color: #0f172a !important;
            font-variant-numeric: tabular-nums;
        }
        html.light .price-period,
        html.light span.text-xs[class*="text-mono-500"].font-mono,
        html.light span.text-xs[class*="text-mono-400"].font-mono {
            color: #64748b !important;
        }

        /* Highlight Selected Card */
        html.light div[class*="ring-mono-300"],
        html.light div[class*="border-mono-200"] {
            border-color: #0f172a !important;
            box-shadow: 0 0 0 2px #0f172a, 0 12px 30px -8px rgba(15, 23, 42, 0.12) !important;
        }

        /* Capacity Metric Micro-boxes */
        html.light .grid.grid-cols-2 .p-3[class*="bg-mono-950"] {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
        }
        html.light .grid.grid-cols-2 .p-3[class*="bg-mono-950"] .text-mono-50 {
            color: #0f172a !important;
            font-weight: 800 !important;
        }
        html.light .grid.grid-cols-2 .p-3[class*="bg-mono-950"] .text-mono-500 {
            color: #64748b !important;
            font-weight: 700 !important;
        }

        /* Feature Bullet Checks */
        html.light ul li span.text-mono-100 {
            color: #059669 !important; /* Emerald for clean positive ticks */
        }
        html.light ul li span.text-mono-300 {
            color: #334155 !important;
        }

        /* ─── CONTROLS, INPUTS & BUTTONS ─── */
        html.light .mono-input {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
        }
        html.light .mono-input:focus {
            border-color: #0284c7 !important;
            box-shadow: 0 0 0 1px #0284c7 !important;
        }
        html.light .mono-btn-primary {
            background-color: #0f172a !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.15) !important;
        }
        html.light .mono-btn-primary:hover {
            background-color: #1e293b !important;
            transform: translateY(-1px);
        }
        html.light .mono-btn-secondary {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }
        html.light .mono-btn-secondary:hover {
            background-color: #e2e8f0 !important;
        }

        /* ─── DATA TABLES (MODERN BENTO TABULAR) ─── */
        html.light table thead tr {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        html.light table thead th {
            color: #64748b !important;
            font-family: var(--font-mono);
            font-size: 11px !important;
            font-weight: 700 !important;
        }
        html.light table tbody tr {
            border-bottom-color: #f1f5f9 !important;
        }
        html.light table tbody tr:hover {
            background-color: #f8fafc !important;
        }
        html.light table td {
            font-variant-numeric: tabular-nums;
        }

        /* Status Pills in Light Mode */
        html.light .bg-emerald-500\/20 {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            border-color: #a7f3d0 !important;
        }
        html.light .bg-amber-500\/20 {
            background-color: #fffbeb !important;
            color: #92400e !important;
            border-color: #fde68a !important;
        }
        html.light .bg-zinc-500\/20,
        html.light .bg-zinc-600\/20 {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border-color: #cbd5e1 !important;
        }
        html.light .bg-red-500\/20 {
            background-color: #fef2f2 !important;
            color: #991b1b !important;
            border-color: #fecaca !important;
        }

        /* SweetAlert Light Mode */
        html.light .swal2-popup.swal2-dark-custom {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.15) !important;
            color: #0f172a !important;
        }
        html.light .swal2-title {
            color: #0f172a !important;
        }
        html.light .swal2-html-container {
            color: #475569 !important;
        }
        html.light .swal2-cancel {
            background: #f1f5f9 !important;
            color: #334155 !important;
            border-color: #cbd5e1 !important;
        }
        html.light .swal2-input, html.light .swal2-textarea {
            background: #ffffff !important;
            border-color: #cbd5e1 !important;
            color: #0f172a !important;
        }

        html.light footer {
            border-top-color: #e2e8f0 !important;
            color: #94a3b8 !important;
        }
    </style>
    <!-- Theme Switcher Logic -->
    <script>
        if (localStorage.theme === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark');
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.add('dark');
            document.documentElement.classList.remove('light');
        }
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
        <main class="flex-1 w-full px-4 py-5 sm:px-6 sm:py-6">
            @yield('content')
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-mono-700/60 py-4 px-4 sm:px-6 text-center text-xs text-mono-500 font-medium">
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
