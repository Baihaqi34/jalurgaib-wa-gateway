<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>JalurGaib — Managed WhatsApp Gateway Cloud & Anti-Banned Engine</title>
  
  <!-- SEO Primary Meta Tags -->
  <meta name="title" content="JalurGaib — Managed WhatsApp Gateway Cloud & Anti-Banned Engine">
  <meta name="description" content="Platform Managed WhatsApp Gateway Cloud modern di Indonesia. Kirim notifikasi, OTP, tagihan, dan broadcast otomatis dengan proteksi antrean cerdas, webhook, dan multi-session stabil.">
  <meta name="keywords" content="whatsapp gateway, wa gateway indonesia, rest api whatsapp, whatsapp blast, otp whatsapp, webhook whatsapp, whatsmeow, baileys, api wa murah, anti ban wa, jalurgaib">
  <meta name="author" content="JalurGaib">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <meta name="googlebot" content="index, follow">
  <link rel="canonical" href="{{ url('/') }}">

  <!-- Favicon & Touch Icons -->
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/logo.png') }}">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
  <meta name="theme-color" content="#09090b">

  <!-- Open Graph / Facebook / WhatsApp Preview -->
  <meta property="og:type" content="website">
  <meta property="og:locale" content="id_ID">
  <meta property="og:url" content="{{ url('/') }}">
  <meta property="og:site_name" content="JalurGaib WA Gateway">
  <meta property="og:title" content="JalurGaib — Managed WhatsApp Gateway Cloud & Anti-Banned Engine">
  <meta property="og:description" content="Platform Managed WhatsApp Gateway Cloud modern di Indonesia. Kirim notifikasi, OTP, dan broadcast otomatis dengan proteksi antrean cerdas dan multi-session stabil.">
  <meta property="og:image" content="{{ asset('images/logo.png') }}">
  <meta property="og:image:secure_url" content="{{ asset('images/logo.png') }}">
  <meta property="og:image:width" content="512">
  <meta property="og:image:height" content="512">
  <meta property="og:image:alt" content="JalurGaib Logo">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:url" content="{{ url('/') }}">
  <meta name="twitter:title" content="JalurGaib — Managed WhatsApp Gateway Cloud & Anti-Banned Engine">
  <meta name="twitter:description" content="Kirim notifikasi, OTP, dan broadcast otomatis dengan proteksi ritme pengetikan manusiawi dan multi-session yang handal.">
  <meta name="twitter:image" content="{{ asset('images/logo.png') }}">

  <!-- Google Structured Data (Schema.org JSON-LD) -->
  <script type="application/ld+json">
  {!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
      [
        '@type' => 'Organization',
        '@id' => url('/') . '/#organization',
        'name' => 'JalurGaib WA Gateway',
        'url' => url('/'),
        'logo' => asset('images/logo.png'),
        'description' => 'Penyedia layanan Managed WhatsApp Gateway Cloud dan Anti-Banned Engine di Indonesia.'
      ],
      [
        '@type' => 'WebApplication',
        '@id' => url('/') . '/#webapp',
        'name' => 'JalurGaib WhatsApp Gateway',
        'url' => url('/'),
        'applicationCategory' => 'BusinessApplication',
        'operatingSystem' => 'All',
        'browserRequirements' => 'Requires JavaScript. Requires HTML5.',
        'description' => 'Kirimkan pesan WhatsApp notifikasi, OTP, dan broadcast dari aplikasi backend via REST API.',
        'publisher' => [
          '@id' => url('/') . '/#organization'
        ],
        'offers' => [
          '@type' => 'Offer',
          'price' => '0',
          'priceCurrency' => 'IDR'
        ]
      ]
    ]
  ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
  </script>

  <!-- Typography: Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Icons: Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    /* ==========================================================================
       MONOCHROMATIC & MODERN BENTO DESIGN SYSTEM (Noir Quiet Luxury)
       Strict Monochrome: Light (Paper Slate) | Dark (Deep Carbon Noir)
       ========================================================================== */
    :root {
      /* Light Mode: Crisp Monochromatic Paper & Slate */
      --bg-body: #f8fafc;
      --bg-surface: #ffffff;
      --bg-surface-elevated: #f1f5f9;
      --bg-surface-subtle: #f8fafc;
      --border-subtle: #e2e8f0;
      --border-medium: #cbd5e1;
      --border-focus: #0f172a;
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-muted: #64748b;
      --tint-badge: #f1f5f9;
      --btn-primary-bg: #0f172a;
      --btn-primary-text: #ffffff;
      --btn-primary-hover: #000000;

      /* Unified Terminal Surface (Consistent Dark Obsidian for Code Blocks) */
      --terminal-bg: #09090b;
      --terminal-surface: #121215;
      --terminal-border: #27272a;
      --terminal-text: #f4f4f5;
      --terminal-text-muted: #71717a;
      --terminal-tab-active-bg: #18181c;
      --terminal-tab-active-text: #ffffff;

      --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.04), 0 1px 2px rgba(15, 23, 42, 0.02);
      --shadow-md: 0 4px 16px rgba(15, 23, 42, 0.06), 0 2px 6px rgba(15, 23, 42, 0.03);
      --shadow-lg: 0 16px 36px rgba(15, 23, 42, 0.08), 0 4px 12px rgba(15, 23, 42, 0.04);

      --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      --font-mono: 'JetBrains Mono', monospace;

      --radius-sm: 8px;
      --radius-md: 12px;
      --radius-lg: 18px;
      --radius-xl: 24px;
    }

    /* Dark Mode (Carbon Noir) */
    [data-theme="dark"] {
      --bg-body: #09090b;
      --bg-surface: #121215;
      --bg-surface-elevated: #18181c;
      --bg-surface-subtle: #141418;
      --border-subtle: #27272a;
      --border-medium: #3f3f46;
      --border-focus: #f4f4f5;
      --text-primary: #f4f4f5;
      --text-secondary: #a1a1aa;
      --text-muted: #71717a;
      --tint-badge: #18181c;
      --btn-primary-bg: #fafafa;
      --btn-primary-text: #09090b;
      --btn-primary-hover: #ffffff;

      --terminal-bg: #09090b;
      --terminal-surface: #121215;
      --terminal-border: #27272a;
      --terminal-text: #f4f4f5;
      --terminal-text-muted: #71717a;
      --terminal-tab-active-bg: #18181c;
      --terminal-tab-active-text: #ffffff;

      --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.4);
      --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.5);
      --shadow-lg: 0 20px 48px rgba(0, 0, 0, 0.7);
    }

    /* Reset & Base - Strict Zero Horizontal Overflow Guarantee */
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    html {
      scroll-behavior: smooth;
      font-size: 16px;
      overflow-x: hidden;
      max-width: 100vw;
    }

    body {
      font-family: var(--font-sans);
      background-color: var(--bg-body);
      color: var(--text-primary);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
      width: 100%;
      max-width: 100vw;
      position: relative;
      transition: background-color 0.2s ease, color 0.2s ease;
    }

    img, svg, video, canvas, audio, iframe, embed, object {
      display: block;
      max-width: 100%;
      height: auto;
    }

    .bg-grid {
      background-image: radial-gradient(var(--border-subtle) 1px, transparent 1px);
      background-size: 28px 28px;
    }

    .container {
      width: 100%;
      max-width: 1220px;
      margin: 0 auto;
      padding: 0 24px;
    }

    /* Monochromatic Buttons */
    .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-family: var(--font-sans);
      font-weight: 700;
      font-size: 0.875rem;
      padding: 11px 22px;
      border-radius: var(--radius-md);
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      cursor: pointer;
      text-decoration: none;
      border: 1px solid transparent;
      outline: none;
    }

    .btn:active {
      transform: scale(0.97);
    }

    .btn-primary {
      background-color: var(--btn-primary-bg);
      color: var(--btn-primary-text);
      box-shadow: var(--shadow-sm);
    }
    .btn-primary:hover {
      background-color: var(--btn-primary-hover);
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }

    .btn-secondary {
      background-color: var(--bg-surface);
      color: var(--text-primary);
      border-color: var(--border-subtle);
      box-shadow: var(--shadow-sm);
    }
    .btn-secondary:hover {
      background-color: var(--bg-surface-elevated);
      border-color: var(--border-medium);
      transform: translateY(-2px);
    }

    .btn-ghost {
      background: transparent;
      color: var(--text-primary);
      border-color: var(--border-subtle);
    }
    .btn-ghost:hover {
      background-color: var(--bg-surface-elevated);
      border-color: var(--border-medium);
    }

    .btn-sm {
      padding: 7px 15px;
      font-size: 0.8125rem;
      border-radius: var(--radius-sm);
    }

    .btn-disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }

    /* Monochromatic Badges */
    .mono-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 5px 12px;
      background-color: var(--tint-badge);
      color: var(--text-primary);
      border: 1px solid var(--border-subtle);
      border-radius: 100px;
      font-size: 0.75rem;
      font-weight: 700;
      font-family: var(--font-mono);
      letter-spacing: 0.03em;
    }

    .mono-badge-dot {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background-color: var(--text-primary);
      animation: pulse-dot 2s infinite ease-in-out;
    }

    @keyframes pulse-dot {
      0%, 100% { opacity: 1; transform: scale(1); }
      50% { opacity: 0.35; transform: scale(0.85); }
    }

    /* ==========================================================================
       NAVBAR (Clean JalurGaib)
       ========================================================================== */
    .navbar {
      position: sticky;
      top: 0;
      z-index: 1000;
      background-color: var(--bg-body);
      border-bottom: 1px solid var(--border-subtle);
      padding: 14px 0;
      backdrop-filter: blur(14px);
      transition: all 0.2s ease;
    }

    .navbar-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .brand-logo {
      display: flex;
      align-items: center;
      gap: 12px;
      text-decoration: none;
      color: var(--text-primary);
    }

    .brand-icon {
      width: 38px;
      height: 38px;
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 4px;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }

    .jalurgaib-logo {
      width: 100%;
      height: 100%;
      object-fit: contain;
      transition: filter 0.2s ease;
      filter: brightness(0);
    }

    [data-theme="dark"] .jalurgaib-logo {
      filter: brightness(0) invert(1);
    }

    .brand-name {
      font-weight: 800;
      font-size: 1.25rem;
      letter-spacing: -0.035em;
      color: var(--text-primary);
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 28px;
      list-style: none;
    }

    .nav-links a {
      text-decoration: none;
      color: var(--text-secondary);
      font-weight: 600;
      font-size: 0.875rem;
      transition: color 0.15s ease;
    }

    .nav-links a:hover {
      color: var(--text-primary);
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .theme-toggle-btn {
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      color: var(--text-primary);
      width: 36px;
      height: 36px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 0.95rem;
      transition: all 0.2s;
    }
    .theme-toggle-btn:hover {
      border-color: var(--border-medium);
      transform: rotate(20deg);
    }

    /* ==========================================================================
       HERO SECTION (Quiet Luxury Bento)
       ========================================================================== */
    .hero {
      padding: 70px 0 85px;
      position: relative;
    }

    .hero-grid {
      display: grid;
      grid-template-columns: 1.15fr 0.85fr;
      gap: 44px;
      align-items: center;
    }

    .hero-title {
      font-size: clamp(2rem, 5vw + 0.8rem, 3.25rem);
      font-weight: 800;
      line-height: 1.18;
      letter-spacing: -0.04em;
      color: var(--text-primary);
      margin-top: 18px;
      margin-bottom: 20px;
      word-break: break-word;
    }

    .desktop-br {
      display: inline;
    }
    @media (max-width: 768px) {
      .desktop-br {
        display: none;
      }
    }

    .hero-desc {
      font-size: clamp(0.925rem, 2vw + 0.5rem, 1.05rem);
      color: var(--text-secondary);
      line-height: 1.68;
      margin-bottom: 30px;
      max-width: 580px;
    }

    .hero-buttons {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-bottom: 38px;
    }

    /* Bento Stats Strip */
    .hero-bento-strip {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 12px;
      padding-top: 24px;
      border-top: 1px solid var(--border-subtle);
    }

    .bento-stat-card {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      padding: 14px 16px;
      transition: all 0.2s ease;
    }
    .bento-stat-card:hover {
      border-color: var(--border-medium);
      transform: translateY(-2px);
    }

    .bento-stat-val {
      font-size: 1.35rem;
      font-weight: 800;
      font-family: var(--font-mono);
      color: var(--text-primary);
      display: block;
      line-height: 1.2;
      font-variant-numeric: tabular-nums;
    }

    .bento-stat-lbl {
      font-size: 0.725rem;
      color: var(--text-muted);
      font-weight: 600;
      margin-top: 4px;
      display: block;
    }

    /* ==========================================================================
       AUTHENTIC DEVELOPER TERMINAL (Obsidian Sandbox Container)
       ========================================================================== */
    .terminal-container {
      background-color: var(--terminal-bg);
      border: 1px solid var(--terminal-border);
      border-radius: var(--radius-xl);
      overflow: hidden;
      box-shadow: var(--shadow-lg);
    }

    .terminal-header {
      background-color: var(--terminal-surface);
      padding: 12px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--terminal-border);
    }

    .terminal-dots {
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .terminal-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background-color: #3f3f46;
    }

    .terminal-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-family: var(--font-mono);
      font-size: 0.7rem;
      font-weight: 700;
      color: var(--terminal-text-muted);
      background-color: var(--terminal-bg);
      border: 1px solid var(--terminal-border);
      padding: 3px 10px;
      border-radius: 6px;
    }

    .terminal-tabs {
      display: flex;
      background-color: var(--terminal-bg);
      border-bottom: 1px solid var(--terminal-border);
      padding: 6px 14px 0;
      gap: 6px;
    }

    .terminal-tab-btn {
      background: transparent;
      border: 1px solid transparent;
      border-bottom: none;
      padding: 8px 14px;
      font-family: var(--font-mono);
      font-size: 0.775rem;
      font-weight: 700;
      color: var(--terminal-text-muted);
      cursor: pointer;
      border-radius: var(--radius-sm) var(--radius-sm) 0 0;
      transition: all 0.15s ease;
    }

    .terminal-tab-btn:hover {
      color: var(--terminal-text);
    }

    .terminal-tab-btn.active {
      color: var(--terminal-tab-active-text);
      background-color: var(--terminal-tab-active-bg);
      border-color: var(--terminal-border);
    }

    .terminal-box {
      background-color: var(--terminal-bg);
      padding: 20px;
      font-family: var(--font-mono);
      font-size: 0.78rem;
      line-height: 1.65;
      color: var(--terminal-text);
      max-height: 290px;
      overflow-y: auto;
      overflow-x: auto;
      max-width: 100%;
      -webkit-overflow-scrolling: touch;
    }

    .terminal-box code {
      font-family: inherit;
      white-space: pre;
      display: block;
      min-width: min-content;
    }

    .terminal-notice {
      padding: 12px 18px;
      background-color: var(--terminal-surface);
      border-top: 1px solid var(--terminal-border);
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.75rem;
      color: var(--terminal-text-muted);
    }

    /* ==========================================================================
       SECTION HEADERS
       ========================================================================== */
    .section-padding {
      padding: 85px 0;
    }

    .section-header {
      text-align: center;
      max-width: 680px;
      margin: 0 auto 52px;
    }

    .section-tag {
      font-family: var(--font-mono);
      font-size: 0.75rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--text-muted);
      margin-bottom: 12px;
      display: inline-block;
      padding: 4px 10px;
      border-radius: 6px;
      background: var(--bg-surface-elevated);
      border: 1px solid var(--border-subtle);
    }

    .section-title {
      font-size: 2.25rem;
      font-weight: 800;
      letter-spacing: -0.035em;
      color: var(--text-primary);
      line-height: 1.25;
      margin-bottom: 14px;
    }

    .section-desc {
      font-size: 1rem;
      color: var(--text-secondary);
      font-weight: 400;
      line-height: 1.6;
    }

    /* ==========================================================================
       FEATURES BENTO GRID
       ========================================================================== */
    .features-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
    }

    .feature-card {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: 28px;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .feature-card:hover {
      border-color: var(--border-focus);
      transform: translateY(-3px);
      box-shadow: var(--shadow-md);
    }

    .feature-icon-badge {
      width: 44px;
      height: 44px;
      background-color: var(--bg-surface-elevated);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.15rem;
      margin-bottom: 18px;
      color: var(--text-primary);
      transition: all 0.25s ease;
    }

    .feature-card:hover .feature-icon-badge {
      background-color: var(--text-primary);
      color: var(--bg-surface);
      border-color: var(--text-primary);
      transform: scale(1.05);
    }

    .feature-title {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 8px;
    }

    .feature-desc {
      font-size: 0.875rem;
      color: var(--text-secondary);
      line-height: 1.6;
    }

    .feature-tech-tag {
      display: inline-block;
      margin-top: 14px;
      font-size: 0.725rem;
      font-family: var(--font-mono);
      color: var(--text-secondary);
      background-color: var(--bg-surface-elevated);
      padding: 3px 8px;
      border-radius: 4px;
      border: 1px solid var(--border-subtle);
    }

    /* ==========================================================================
       ARCHITECTURE FLOW (Bento Process Pipeline)
       ========================================================================== */
    .arch-wrapper {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-xl);
      padding: 36px 30px;
      box-shadow: var(--shadow-sm);
    }

    .arch-flow-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 14px;
      align-items: stretch;
    }

    .arch-node {
      background-color: var(--bg-surface-elevated);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      padding: 20px 16px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.2s;
    }
    .arch-node:hover {
      border-color: var(--border-medium);
      transform: translateY(-2px);
    }

    .arch-node-badge {
      font-family: var(--font-mono);
      font-size: 0.65rem;
      font-weight: 800;
      color: var(--text-muted);
      letter-spacing: 0.05em;
      margin-bottom: 8px;
      display: inline-block;
    }

    .arch-node-title {
      font-size: 0.95rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 6px;
    }

    .arch-node-desc {
      font-size: 0.775rem;
      color: var(--text-secondary);
      line-height: 1.45;
    }

    /* ==========================================================================
       EYE-CATCHING TESTIMONIALS BENTO SECTION (Monochromatic Quiet Luxury)
       ========================================================================== */
    .testimonials-bento {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
    }

    .testimonial-bento-hero {
      grid-column: span 3;
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: 28px 36px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 24px;
      box-shadow: var(--shadow-sm);
    }

    .t-hero-stats {
      display: flex;
      align-items: center;
      gap: 36px;
    }

    .t-stat-block {
      text-align: left;
    }

    .t-stat-num {
      font-size: 1.75rem;
      font-weight: 800;
      font-family: var(--font-mono);
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 6px;
      font-variant-numeric: tabular-nums;
    }

    .t-stat-label {
      font-size: 0.775rem;
      color: var(--text-muted);
      font-weight: 600;
      margin-top: 2px;
    }

    .testimonial-card {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: 26px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .testimonial-card:hover {
      border-color: var(--border-focus);
      transform: translateY(-3px);
      box-shadow: var(--shadow-md);
    }

    .testimonial-stars {
      display: flex;
      align-items: center;
      gap: 3px;
      color: var(--text-primary);
      font-size: 0.85rem;
      margin-bottom: 14px;
    }

    .testimonial-quote {
      font-size: 0.885rem;
      color: var(--text-secondary);
      line-height: 1.65;
      margin-bottom: 22px;
      font-style: italic;
    }

    .testimonial-author-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding-top: 16px;
      border-top: 1px solid var(--border-subtle);
    }

    .t-author-info {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .t-avatar {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-sm);
      object-fit: cover;
      border: 1px solid var(--border-subtle);
    }

    .t-avatar-fallback {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-sm);
      background-color: var(--bg-surface-elevated);
      border: 1px solid var(--border-subtle);
      color: var(--text-primary);
      font-family: var(--font-mono);
      font-weight: 800;
      font-size: 0.85rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .t-author-name {
      font-size: 0.875rem;
      font-weight: 700;
      color: var(--text-primary);
    }

    .t-author-role {
      font-size: 0.75rem;
      color: var(--text-muted);
      margin-top: 1px;
    }

    .t-verified-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-size: 0.7rem;
      font-family: var(--font-mono);
      color: var(--text-primary);
      font-weight: 700;
      background: var(--bg-surface-elevated);
      padding: 3px 8px;
      border-radius: 4px;
      border: 1px solid var(--border-subtle);
    }

    /* ==========================================================================
       PRICING TIERS BENTO
       ========================================================================== */
    .pricing-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 22px;
      align-items: stretch;
    }

    .price-card {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: 32px 28px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      transition: all 0.25s ease;
    }

    .price-card:hover {
      border-color: var(--border-medium);
      transform: translateY(-3px);
      box-shadow: var(--shadow-md);
    }

    .price-card.featured-plan {
      border-color: var(--border-focus);
      box-shadow: var(--shadow-md);
    }

    .price-header {
      margin-bottom: 20px;
    }

    .price-tier-name {
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 6px;
    }

    .price-tier-sub {
      font-size: 0.825rem;
      color: var(--text-secondary);
    }

    .price-amount-wrap {
      display: flex;
      align-items: baseline;
      gap: 6px;
      margin-bottom: 24px;
      padding-bottom: 20px;
      border-bottom: 1px solid var(--border-subtle);
    }

    .price-amount {
      font-size: 2.25rem;
      font-weight: 800;
      font-family: var(--font-mono);
      color: var(--text-primary);
      line-height: 1;
      font-variant-numeric: tabular-nums;
    }

    .price-period {
      font-size: 0.825rem;
      color: var(--text-muted);
      font-weight: 600;
    }

    .price-features {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 28px;
    }

    .price-features li {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 0.85rem;
      color: var(--text-secondary);
    }

    .price-features li::before {
      content: '✓';
      font-family: var(--font-mono);
      font-weight: 800;
      color: var(--text-primary);
      font-size: 0.85rem;
    }

    /* ==========================================================================
       CODE SDK SHOWCASE (Unified Obsidian Terminal)
       ========================================================================== */
    .sdk-container {
      background-color: var(--terminal-bg);
      border: 1px solid var(--terminal-border);
      border-radius: var(--radius-lg);
      overflow: hidden;
      box-shadow: var(--shadow-md);
    }

    .sdk-header {
      background-color: var(--terminal-surface);
      padding: 10px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid var(--terminal-border);
    }

    .sdk-tabs {
      display: flex;
      gap: 6px;
    }

    .sdk-tab {
      background: transparent;
      border: 1px solid transparent;
      color: var(--terminal-text-muted);
      padding: 6px 14px;
      border-radius: var(--radius-sm);
      font-family: var(--font-mono);
      font-size: 0.775rem;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .sdk-tab:hover {
      color: var(--terminal-text);
    }

    .sdk-tab.active {
      background-color: var(--terminal-tab-active-bg);
      color: var(--terminal-tab-active-text);
      border-color: var(--terminal-border);
    }

    .sdk-body {
      padding: 24px;
      background-color: var(--terminal-bg);
    }

    .code-block {
      font-family: var(--font-mono);
      font-size: 0.8rem;
      line-height: 1.65;
      color: var(--terminal-text);
      overflow-x: auto;
      white-space: pre;
      max-width: 100%;
      -webkit-overflow-scrolling: touch;
      display: block;
    }

    /* ==========================================================================
       FAQ ACCORDION
       ========================================================================== */
    .faq-container {
      max-width: 820px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .faq-item {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-md);
      overflow: hidden;
      transition: border-color 0.2s;
    }

    .faq-item.active {
      border-color: var(--border-focus);
    }

    .faq-header {
      padding: 18px 22px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      cursor: pointer;
      user-select: none;
      font-weight: 700;
      font-size: 0.95rem;
      color: var(--text-primary);
    }

    .faq-icon {
      font-family: var(--font-mono);
      font-size: 1.1rem;
      color: var(--text-muted);
      transition: transform 0.2s;
    }

    .faq-item.active .faq-icon {
      transform: rotate(45deg);
      color: var(--text-primary);
    }

    .faq-body {
      display: none;
      padding: 0 22px 18px;
      font-size: 0.875rem;
      color: var(--text-secondary);
      line-height: 1.65;
      border-top: 1px solid var(--border-subtle);
      padding-top: 14px;
    }

    .faq-item.active .faq-body {
      display: block;
    }

    /* ==========================================================================
       CTA BANNER
       ========================================================================== */
    .cta-banner {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-xl);
      padding: 55px 36px;
      text-align: center;
      box-shadow: var(--shadow-sm);
    }

    .cta-banner-title {
      font-size: 2.2rem;
      font-weight: 800;
      color: var(--text-primary);
      margin-bottom: 12px;
      letter-spacing: -0.03em;
    }

    .cta-banner-desc {
      font-size: 1rem;
      color: var(--text-secondary);
      max-width: 580px;
      margin: 0 auto 28px;
    }

    .cta-buttons {
      display: flex;
      justify-content: center;
      gap: 14px;
      flex-wrap: wrap;
    }
    @media (max-width: 480px) {
      .cta-buttons {
        flex-direction: column;
        width: 100%;
        gap: 10px;
      }
      .cta-buttons .btn {
        width: 100%;
        justify-content: center;
      }
    }

    /* ==========================================================================
       FOOTER
       ========================================================================== */
    .footer {
      background-color: var(--bg-surface);
      border-top: 1px solid var(--border-subtle);
      padding: 50px 0 26px;
      margin-top: 50px;
    }

    .footer-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr;
      gap: 36px;
      margin-bottom: 36px;
    }

    .footer-col-title {
      font-size: 0.8rem;
      font-family: var(--font-mono);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--text-primary);
      margin-bottom: 16px;
    }

    .footer-links {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 9px;
    }

    .footer-links a {
      text-decoration: none;
      color: var(--text-secondary);
      font-size: 0.85rem;
      transition: color 0.15s ease;
    }

    .footer-links a:hover {
      color: var(--text-primary);
    }

    .footer-bottom {
      padding-top: 22px;
      border-top: 1px solid var(--border-subtle);
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.775rem;
      color: var(--text-muted);
      font-family: var(--font-mono);
    }

    /* ==========================================================================
       FLOATING WHATSAPP CS BUTTON (Bottom-Right, Quiet Luxury)
       ========================================================================== */
    .wa-cs-floating {
      position: fixed;
      bottom: 24px;
      right: 24px;
      z-index: 990;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .wa-cs-tooltip {
      background-color: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      box-shadow: var(--shadow-md);
      padding: 7px 13px;
      border-radius: 100px;
      font-size: 0.75rem;
      font-weight: 700;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 7px;
      white-space: nowrap;
      opacity: 0;
      transform: translateX(10px);
      pointer-events: none;
      transition: all 0.2s ease;
    }

    .wa-cs-floating:hover .wa-cs-tooltip {
      opacity: 1;
      transform: translateX(0);
    }

    .wa-cs-btn {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      background: #25D366;
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      box-shadow: 0 4px 14px rgba(37, 211, 102, 0.35);
      text-decoration: none;
      transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
    }

    .wa-cs-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(37, 211, 102, 0.45);
    }

    /* ==========================================================================
       MOBILE MENU BUTTON & SLIDE-OUT DRAWER (Monochromatic Quiet Luxury)
       ========================================================================== */
    .mobile-menu-btn {
      display: none;
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      color: var(--text-primary);
      width: 38px;
      height: 38px;
      border-radius: var(--radius-sm);
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1.1rem;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .mobile-menu-btn:hover {
      background-color: var(--bg-surface-elevated);
      border-color: var(--border-medium);
    }

    .mobile-nav-overlay {
      position: fixed;
      inset: 0;
      background-color: rgba(9, 9, 11, 0.72);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      z-index: 1998;
      opacity: 0;
      visibility: hidden;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .mobile-nav-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .mobile-nav-drawer {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      width: 86%;
      max-width: 350px;
      background-color: var(--bg-surface);
      border-left: 1px solid var(--border-subtle);
      z-index: 1999;
      transform: translateX(100%);
      transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      flex-direction: column;
      box-shadow: var(--shadow-lg);
      overflow-y: auto;
    }
    .mobile-nav-drawer.active {
      transform: translateX(0);
    }

    .mobile-drawer-header {
      padding: 16px 20px;
      border-bottom: 1px solid var(--border-subtle);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .mobile-drawer-close {
      background: var(--bg-surface-elevated);
      border: 1px solid var(--border-subtle);
      color: var(--text-primary);
      width: 34px;
      height: 34px;
      border-radius: var(--radius-sm);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      font-size: 1rem;
      transition: all 0.15s ease;
    }
    .mobile-drawer-close:hover {
      background: var(--text-primary);
      color: var(--bg-surface);
    }

    .mobile-drawer-body {
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 20px;
      flex: 1;
      justify-content: space-between;
    }

    .mobile-nav-links {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .mobile-nav-links a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border-radius: var(--radius-md);
      color: var(--text-primary);
      text-decoration: none;
      font-weight: 700;
      font-size: 0.9rem;
      transition: all 0.15s ease;
      background: transparent;
      border: 1px solid transparent;
    }
    .mobile-nav-links a:hover, .mobile-nav-links a:active {
      background-color: var(--bg-surface-elevated);
      border-color: var(--border-subtle);
      transform: translateX(3px);
    }

    .mobile-drawer-actions {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding-top: 18px;
      border-top: 1px solid var(--border-subtle);
      flex-shrink: 0;
    }

    .btn-block {
      width: 100%;
      text-align: center;
      justify-content: center;
    }

    /* ==========================================================================
       RESPONSIVE MULTI-TIER BREAKPOINTS (Noir Quiet Luxury Standard)
       Zero Horizontal Overflow & Clean Fluid Scaling
       ========================================================================== */

    /* TABLET & LAPTOP LANDSCAPE (max-width: 1199px) */
    @media (max-width: 1199px) {
      .container {
        padding: 0 20px;
      }
      .hero-grid {
        gap: 32px;
      }
      .arch-flow-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    /* TABLET PORTRAIT (max-width: 992px) */
    @media (max-width: 992px) {
      .nav-links {
        display: none !important;
      }
      .desktop-only-btn {
        display: none !important;
      }
      .mobile-menu-btn {
        display: inline-flex !important;
      }
      
      .hero {
        padding: 45px 0 65px;
      }
      .hero-grid {
        grid-template-columns: 1fr;
        gap: 36px;
      }
      .hero-title {
        font-size: clamp(2.1rem, 5vw + 0.5rem, 2.85rem);
      }
      .hero-desc {
        max-width: 100%;
      }
      .hero-bento-strip {
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
      }

      .features-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
      }

      .arch-flow-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
      }

      .testimonials-bento {
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
      }
      .testimonial-bento-hero {
        grid-column: span 2;
        padding: 24px;
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
      }
      .t-hero-stats {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
      }

      .pricing-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 18px;
      }
      .pricing-grid .price-card:last-child {
        grid-column: span 2;
        max-width: 600px;
        margin: 0 auto;
        width: 100%;
      }

      .footer-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
      }

      .terminal-box {
        max-height: 270px;
      }
    }

    /* MOBILE STANDARD (max-width: 768px) */
    @media (max-width: 768px) {
      .section-padding {
        padding: 55px 0;
      }
      .section-header {
        margin-bottom: 34px;
      }
      .section-title {
        font-size: clamp(1.6rem, 4vw + 0.5rem, 2rem);
      }

      .hero-bento-strip {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
      }
      .bento-stat-val {
        font-size: 1.2rem;
      }

      .arch-flow-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .pricing-grid {
        grid-template-columns: 1fr;
      }
      .pricing-grid .price-card:last-child {
        grid-column: span 1;
        max-width: 100%;
      }

      .features-grid {
        grid-template-columns: 1fr;
      }

      .testimonials-bento {
        grid-template-columns: 1fr;
      }
      .testimonial-bento-hero {
        grid-column: span 1;
      }
      .t-hero-stats {
        grid-template-columns: 1fr;
        gap: 14px;
      }

      .arch-wrapper {
        padding: 22px 16px;
      }

      .cta-banner {
        padding: 38px 20px;
      }
      .cta-banner-title {
        font-size: 1.75rem;
      }

      .footer-grid {
        grid-template-columns: 1fr;
        gap: 28px;
      }
      .footer-bottom {
        flex-direction: column;
        gap: 12px;
        text-align: center;
      }

      /* Horizontal scrolling for tabs so they never push the viewport */
      .terminal-tabs, .sdk-tabs {
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
      }
      .terminal-tabs::-webkit-scrollbar, .sdk-tabs::-webkit-scrollbar {
        display: none;
      }
      .terminal-tab-btn, .sdk-tab {
        flex-shrink: 0;
      }
    }

    /* MOBILE COMPACT (max-width: 480px) */
    @media (max-width: 480px) {
      .container {
        padding: 0 16px;
      }
      .section-padding {
        padding: 42px 0;
      }
      .hero {
        padding: 30px 0 45px;
      }
      .hero-title {
        font-size: clamp(1.7rem, 7vw, 2.2rem);
        line-height: 1.2;
      }
      .hero-desc {
        font-size: 0.925rem;
        margin-bottom: 22px;
      }

      .hero-buttons {
        flex-direction: column;
        width: 100%;
        gap: 10px;
        margin-bottom: 26px;
      }
      .hero-buttons .btn {
        width: 100%;
        padding: 12px 18px;
        font-size: 0.85rem;
        justify-content: center;
      }

      .hero-bento-strip {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        padding-top: 16px;
      }
      .bento-stat-card {
        padding: 10px 12px;
      }
      .bento-stat-val {
        font-size: 1.05rem;
      }
      .bento-stat-lbl {
        font-size: 0.675rem;
      }

      .arch-flow-grid {
        grid-template-columns: 1fr;
        gap: 10px;
      }

      .terminal-box {
        padding: 14px;
        font-size: 0.72rem;
      }
      .terminal-header {
        padding: 10px 14px;
      }
      .terminal-badge {
        font-size: 0.65rem;
        padding: 2px 7px;
      }

      .sdk-body {
        padding: 16px 14px;
      }
      .code-block {
        font-size: 0.75rem;
      }

      .price-card {
        padding: 22px 16px;
      }
      .price-amount {
        font-size: 1.85rem;
      }

      .cta-banner {
        padding: 30px 16px;
      }
      .cta-banner-title {
        font-size: 1.45rem;
      }
      .cta-banner-desc {
        font-size: 0.875rem;
      }

      /* Floating WhatsApp Button on small mobile */
      .wa-cs-floating {
        bottom: 16px;
        right: 16px;
      }
      .wa-cs-tooltip {
        display: none !important;
      }
      .wa-cs-btn {
        width: 44px;
        height: 44px;
        font-size: 1.35rem;
      }
    }
  </style>
</head>
<body class="bg-grid">

  <!-- NAVBAR (Clean JalurGaib) -->
  <nav class="navbar">
    <div class="container navbar-inner">
      <a href="{{ route('landing') }}" class="brand-logo">
        <div class="brand-icon">
          <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo">
        </div>
        <span class="brand-name">JalurGaib</span>
      </a>

      <ul class="nav-links">
        <li><a href="#fitur">Fitur</a></li>
        <li><a href="#arsitektur">Arsitektur</a></li>
        <li><a href="#testimoni">Ulasan Klien</a></li>
        <li><a href="#harga">Paket Kuota</a></li>
        <li><a href="#sdk">Contoh API</a></li>
        <li><a href="#faq">FAQ</a></li>
      </ul>

      <div class="nav-actions">
        <button class="theme-toggle-btn" id="themeBtn" title="Ganti Mode Tampilan" onclick="toggleTheme()">
          ◐
        </button>

        @auth
          @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm desktop-only-btn">
              Admin Panel →
            </a>
          @else
            <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-sm desktop-only-btn">
              Dashboard ({{ Auth::user()->name }}) →
            </a>
          @endif
        @else
          <a href="{{ route('login') }}" class="btn btn-ghost btn-sm desktop-only-btn">
            Masuk
          </a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm desktop-only-btn">
            Daftar Gratis
          </a>
        @endauth

        <!-- Mobile Menu Hamburger Button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Buka Menu Navigasi" onclick="toggleMobileMenu()">
          <i class="fa-solid fa-bars"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- Mobile Navigation Overlay & Slide-Out Drawer -->
  <div id="mobileNavOverlay" class="mobile-nav-overlay" onclick="toggleMobileMenu()"></div>

  <div id="mobileNavDrawer" class="mobile-nav-drawer" aria-hidden="true">
    <div class="mobile-drawer-header">
      <a href="{{ route('landing') }}" class="brand-logo" onclick="toggleMobileMenu()">
        <div class="brand-icon">
          <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo">
        </div>
        <span class="brand-name">JalurGaib</span>
      </a>
      <button class="mobile-drawer-close" onclick="toggleMobileMenu()" aria-label="Tutup Menu">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <div class="mobile-drawer-body">
      <ul class="mobile-nav-links">
        <li><a href="#fitur" onclick="toggleMobileMenu()"><i class="fa-solid fa-bolt" style="width: 20px; color: var(--text-muted);"></i> <span>Fitur Cloud Engine</span></a></li>
        <li><a href="#arsitektur" onclick="toggleMobileMenu()"><i class="fa-solid fa-network-wired" style="width: 20px; color: var(--text-muted);"></i> <span>Arsitektur Pipeline</span></a></li>
        <li><a href="#testimoni" onclick="toggleMobileMenu()"><i class="fa-solid fa-star" style="width: 20px; color: var(--text-muted);"></i> <span>Ulasan Klien</span></a></li>
        <li><a href="#harga" onclick="toggleMobileMenu()"><i class="fa-solid fa-tags" style="width: 20px; color: var(--text-muted);"></i> <span>Paket Kuota</span></a></li>
        <li><a href="#sdk" onclick="toggleMobileMenu()"><i class="fa-solid fa-code" style="width: 20px; color: var(--text-muted);"></i> <span>Contoh API & SDK</span></a></li>
        <li><a href="#faq" onclick="toggleMobileMenu()"><i class="fa-solid fa-circle-question" style="width: 20px; color: var(--text-muted);"></i> <span>Tanya Jawab (FAQ)</span></a></li>
      </ul>

      <div class="mobile-drawer-actions">
        @auth
          @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-block">
              Admin Panel →
            </a>
          @else
            <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-block">
              Buka Dashboard ({{ Auth::user()->name }}) →
            </a>
          @endif
        @else
          <a href="{{ route('register') }}" class="btn btn-primary btn-block">
            Mulai Kuota Gratis 🚀
          </a>
          <a href="{{ route('login') }}" class="btn btn-secondary btn-block">
            Masuk ke Akun
          </a>
        @endauth

        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 12px; font-size: 0.8rem; color: var(--text-muted);">
          <span>Ganti Mode Gelap / Terang</span>
          <button class="theme-toggle-btn" onclick="toggleTheme()" title="Ganti Mode Tampilan">◐</button>
        </div>
      </div>
    </div>
  </div>

  <!-- HERO SECTION -->
  <section class="hero">
    <div class="container">
      <div class="hero-grid">
        <!-- Left: Headline & Copy -->
        <div>
          <div class="mono-badge">
            <span class="mono-badge-dot"></span>
            CLOUD ENGINE ONLINE & MANAGED
          </div>

          <h1 class="hero-title">
            Gateway WhatsApp <br class="desktop-br">
            JalurGaib. <br class="desktop-br">
            Kirim Notifikasi Tanpa Ribet Banned.
          </h1>

          <p class="hero-desc">
            Layanan <strong>Managed WhatsApp Gateway Cloud</strong> berkinerja tinggi. Dilengkapi perlindungan ritme pengetikan manusiawi (Adaptive Human Jitter) agar nomor Anda tetap sehat saat mengirim OTP, billing, maupun resi pesanan.
          </p>

          <div class="hero-buttons">
            @auth
              <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary">
                Buka Dashboard Saya ⚡
              </a>
            @else
              <a href="{{ route('register') }}" class="btn btn-primary">
                Mulai Kuota Gratis 🚀
              </a>
              <a href="{{ route('login') }}" class="btn btn-secondary">
                Masuk ke Akun
              </a>
            @endauth
            <a href="#preview" class="btn btn-ghost">
              Payload JSON ↓
            </a>
          </div>

          <!-- Bento Metric Telemetry Strip -->
          <div class="hero-bento-strip">
            <div class="bento-stat-card">
              <span class="bento-stat-val">99.8%</span>
              <span class="bento-stat-lbl">Uptime Engine</span>
            </div>
            <div class="bento-stat-card">
              <span class="bento-stat-val">&lt; 150ms</span>
              <span class="bento-stat-lbl">Webhook Latency</span>
            </div>
            <div class="bento-stat-card">
              <span class="bento-stat-val">1 - 4s</span>
              <span class="bento-stat-lbl">Human Jitter</span>
            </div>
            <div class="bento-stat-card">
              <span class="bento-stat-val">24 / 7</span>
              <span class="bento-stat-lbl">Persistent Session</span>
            </div>
          </div>
        </div>

        <!-- Right: Authentic Developer Terminal Sandbox -->
        <div>
          <div class="terminal-container" id="preview">
            <div class="terminal-header">
              <div class="terminal-dots">
                <span class="terminal-dot"></span>
                <span class="terminal-dot"></span>
                <span class="terminal-dot"></span>
              </div>
              <span class="terminal-badge">LIVE API SIMULATOR</span>
            </div>

            <div class="terminal-tabs">
              <button class="terminal-tab-btn active" onclick="switchPreviewTab('json', this)">Format Request</button>
              <button class="terminal-tab-btn" onclick="switchPreviewTab('response', this)">Response JSON</button>
              <button class="terminal-tab-btn" onclick="switchPreviewTab('auth', this)">Header & Auth</button>
            </div>

            <!-- Preview Tab 1: Request -->
            <div id="preview-tab-json" class="terminal-box"><code>POST /api/messages/send
Content-Type: application/json
X-API-Key: JG_GAIB_YOUR_TENANT_API_KEY

{
  "device_id": "dev_01HQ88",
  "to": "6281234567890",
  "message": "Kode OTP JalurGaib Anda: 914022. Berlaku 5 menit.",
  "delay": 2
}</code></div>

            <!-- Preview Tab 2: Response -->
            <div id="preview-tab-response" class="terminal-box" style="display: none;"><code>{
  "status": "success",
  "code": 200,
  "message": "Pesan telah berhasil masuk antrean cloud dispatch",
  "data": {
    "message_id": "JG_MSG_88491A",
    "recipient": "6281234567890",
    "delay_stagger": "2s applied (Human jitter)",
    "queue_status": "PROCESSED",
    "timestamp": "2026-10-18T16:40:00Z"
  }
}</code></div>

            <!-- Preview Tab 3: Auth -->
            <div id="preview-tab-auth" class="terminal-box" style="display: none;"><code>// Cara Cepat Autentikasi API:
// 1. Daftar akun di JalurGaib
// 2. Tautkan Device WhatsApp via scan QR di dashboard
// 3. Salin X-API-Key tenant dari menu Settings
// 4. Panggil endpoint resmi dari server backend Anda</code></div>

            <div class="terminal-notice">
              <i class="fa-solid fa-lock text-xs"></i>
              <span>Live dispatch terisolasi per tenant via API Key untuk proteksi anti-spam & rate-limit.</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FEATURES SECTION -->
  <section class="section-padding" style="background-color: var(--bg-surface-subtle);" id="fitur">
    <div class="container">
      <div class="section-header">
        <span class="section-tag"><i class="fa-solid fa-shield-halved"></i> ARSENAL ANTI-BANNED</span>
        <h2 class="section-title">Fitur Handal Menjaga Nomor Tetap Sehat</h2>
        <p class="section-desc">
          Seluruh infrastruktur di-hosting di server cloud kami. Anda cukup integrasikan REST API ke aplikasi bisnis Anda.
        </p>
      </div>

      <div class="features-grid">
        <!-- Feature 1 -->
        <div class="feature-card">
          <div class="feature-icon-badge">
            <i class="fa-solid fa-fingerprint"></i>
          </div>
          <h3 class="feature-title">Algoritma Ritme Manusia</h3>
          <p class="feature-desc">
            Adaptive Human Jitter memberikan jeda acak natural per pesan, mengeliminasi pola robotik serentak yang memicu deteksi sensor spam.
          </p>
          <span class="feature-tech-tag"><i class="fa-regular fa-clock"></i> Adaptive Delay</span>
        </div>

        <!-- Feature 2 -->
        <div class="feature-card">
          <div class="feature-icon-badge">
            <i class="fa-solid fa-bolt-lightning"></i>
          </div>
          <h3 class="feature-title">Direct Socket Engine</h3>
          <p class="feature-desc">
            Transmisi data instan melalui soket cloud terdedikasi tanpa membebani browser headless yang boros RAM dan rentan crash.
          </p>
          <span class="feature-tech-tag"><i class="fa-solid fa-microchip"></i> Zero Headless Overhead</span>
        </div>

        <!-- Feature 3 -->
        <div class="feature-card">
          <div class="feature-icon-badge">
            <i class="fa-solid fa-layer-group"></i>
          </div>
          <h3 class="feature-title">Traffic Shaper Otomatis</h3>
          <p class="feature-desc">
            Antrean dan buffer cerdas di server cloud yang meredam lonjakan kirim pesan massal agar reputasi nomor WhatsApp tetap aman.
          </p>
          <span class="feature-tech-tag"><i class="fa-solid fa-shield-halved"></i> Smart Rate Limiter</span>
        </div>

        <!-- Feature 4 -->
        <div class="feature-card">
          <div class="feature-icon-badge">
            <i class="fa-solid fa-cloud-arrow-up"></i>
          </div>
          <h3 class="feature-title">Persistent Cloud Session</h3>
          <p class="feature-desc">
            Tautkan nomor WhatsApp sekali dari dashboard. Sesi tetap tersimpan aman dan aktif 24 jam walau ponsel Anda dalam kondisi offline.
          </p>
          <span class="feature-tech-tag"><i class="fa-solid fa-server"></i> Always-On Cloud 24/7</span>
        </div>

        <!-- Feature 5 -->
        <div class="feature-card">
          <div class="feature-icon-badge">
            <i class="fa-solid fa-arrows-rotate"></i>
          </div>
          <h3 class="feature-title">Real-Time Auto-Healing</h3>
          <p class="feature-desc">
            Sistem monitoring koneksi aktif yang otomatis menyambungkan kembali socket WhatsApp jika terjadi fluktuasi jaringan secara instan.
          </p>
          <span class="feature-tech-tag"><i class="fa-solid fa-heart-pulse"></i> Auto-Reconnect Daemon</span>
        </div>

        <!-- Feature 6 -->
        <div class="feature-card">
          <div class="feature-icon-badge">
            <i class="fa-solid fa-lock"></i>
          </div>
          <h3 class="feature-title">Isolasi Token Multi-Tenant</h3>
          <p class="feature-desc">
            Setiap akun mendapatkan kuota, database sesi, dan API Key terisolasi penuh untuk memastikan integritas privasi setiap data pesan.
          </p>
          <span class="feature-tech-tag"><i class="fa-solid fa-user-shield"></i> Multi-Tenant Security</span>
        </div>
      </div>
    </div>
  </section>

  <!-- ARCHITECTURE FLOW -->
  <section class="section-padding" id="arsitektur">
    <div class="container">
      <div class="section-header">
        <span class="section-tag"><i class="fa-solid fa-diagram-project"></i> ALIRAN CLOUD PIPELINE</span>
        <h2 class="section-title">Bagaimana JalurGaib Memproses Pesan?</h2>
        <p class="section-desc">
          Server cloud kami mengurus seluruh kerumitan protokol soket, manajemen antrean, dan perlindungan anti-ban secara otomatis.
        </p>
      </div>

      <div class="arch-wrapper">
        <div class="arch-flow-grid">
          <div class="arch-node">
            <div>
              <span class="arch-node-badge"><i class="fa-solid fa-code"></i> 01. REQUEST</span>
              <h4 class="arch-node-title">Aplikasi Anda</h4>
              <p class="arch-node-desc">Kirim payload JSON ke REST API JalurGaib</p>
            </div>
            <code style="font-size: 0.65rem; color: var(--text-muted); margin-top: 8px;"><i class="fa-solid fa-lock"></i> HTTPS + API Key</code>
          </div>

          <div class="arch-node">
            <div>
              <span class="arch-node-badge"><i class="fa-solid fa-shield-halved"></i> 02. AUTH</span>
              <h4 class="arch-node-title">API Gateway</h4>
              <p class="arch-node-desc">Validasi API key & verifikasi alokasi kuota harian</p>
            </div>
            <code style="font-size: 0.65rem; color: var(--text-muted); margin-top: 8px;"><i class="fa-solid fa-sliders"></i> Rate Limiter</code>
          </div>

          <div class="arch-node">
            <div>
              <span class="arch-node-badge"><i class="fa-solid fa-layer-group"></i> 03. BUFFER</span>
              <h4 class="arch-node-title">Traffic Buffer</h4>
              <p class="arch-node-desc">Antrean pesan distabilkan dengan jeda dinamis</p>
            </div>
            <code style="font-size: 0.65rem; color: var(--text-muted); margin-top: 8px;"><i class="fa-solid fa-shuffle"></i> Dynamic Stagger</code>
          </div>

          <div class="arch-node">
            <div>
              <span class="arch-node-badge"><i class="fa-solid fa-network-wired"></i> 04. DISPATCH</span>
              <h4 class="arch-node-title">Cloud Worker</h4>
              <p class="arch-node-desc">Transmisi soket terenkripsi langsung ke server WA</p>
            </div>
            <code style="font-size: 0.65rem; color: var(--text-muted); margin-top: 8px;"><i class="fa-solid fa-paper-plane"></i> Direct Transmit</code>
          </div>

          <div class="arch-node">
            <div>
              <span class="arch-node-badge"><i class="fa-solid fa-circle-check"></i> 05. DELIVERED</span>
              <h4 class="arch-node-title">Penerima</h4>
              <p class="arch-node-desc">Pesan mendarat di WhatsApp target (centang dua)</p>
            </div>
            <code style="font-size: 0.65rem; color: var(--text-muted); margin-top: 8px;"><i class="fa-solid fa-check-double"></i> Success (200 OK)</code>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================================================
       EYE-CATCHING TESTIMONIALS SECTION (Monochromatic Quiet Luxury)
       ========================================================================== */
  -->
  <section class="section-padding" style="background-color: var(--bg-surface-subtle);" id="testimoni">
    <div class="container">
      <div class="section-header">
        <span class="section-tag"><i class="fa-solid fa-comments"></i> BUKTI KEPUASAN KLIEN</span>
        <h2 class="section-title">Dipercaya Ribuan Bisnis & Developer</h2>
        <p class="section-desc">
          Kredibilitas teruji dalam menangani jutaan notifikasi transaksi, OTP, dan update sistem otomatis setiap hari.
        </p>
      </div>

      <div class="testimonials-bento">
        <!-- Top Bento Highlight Tile -->
        <div class="testimonial-bento-hero">
          <div>
            <div class="mono-badge" style="margin-bottom: 8px;">
              <span class="mono-badge-dot"></span> 99.8% DELIVERY SUCCESS RATE
            </div>
            <h3 style="font-size: 1.45rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">
              Skalabilitas Tinggi Tanpa Khawatir Terblokir
            </h3>
            <p style="font-size: 0.85rem; color: var(--text-secondary); max-width: 520px; margin-top: 4px;">
              Dari toko online UMKM hingga aplikasi logistik dan fintech, arsitektur JalurGaib terbukti menjaga kelancaran pengiriman pesan pelanggan.
            </p>
          </div>

          <div class="t-hero-stats">
            <div class="t-stat-block">
              <div class="t-stat-num">
                5.0 <span style="font-size: 0.95rem; color: var(--text-primary); margin-left: 4px;">★★★★★</span>
              </div>
              <div class="t-stat-label">Rating Kepuasan Klien</div>
            </div>
            <div class="t-stat-block">
              <div class="t-stat-num">2.400+</div>
              <div class="t-stat-label">Akun Developer Aktif</div>
            </div>
          </div>
        </div>

        <!-- Testimonial Cards Loop -->
        @forelse($testimonials as $t)
          <div class="testimonial-card">
            <div>
              <div class="testimonial-stars">
                @for($s = 1; $s <= 5; $s++)
                  @if($s <= ($t->rating ?? 5))
                    <i class="fa-solid fa-star"></i>
                  @else
                    <i class="fa-regular fa-star" style="opacity: 0.3;"></i>
                  @endif
                @endfor
                <span style="font-size: 0.75rem; font-family: var(--font-mono); font-weight: 700; color: var(--text-primary); margin-left: 4px;">
                  {{ $t->rating ?? 5 }}.0
                </span>
              </div>

              <p class="testimonial-quote">
                “{{ $t->content }}”
              </p>
            </div>

            <div class="testimonial-author-row">
              <div class="t-author-info">
                @if(!empty($t->avatar))
                  <img src="{{ $t->avatar }}" alt="{{ $t->name }}" class="t-avatar">
                @else
                  <div class="t-avatar-fallback">
                    {{ $t->initials ?? substr($t->name, 0, 2) }}
                  </div>
                @endif
                <div>
                  <div class="t-author-name">{{ $t->name }}</div>
                  <div class="t-author-role">{{ $t->role_or_company ?? 'Klien Terverifikasi' }}</div>
                </div>
              </div>

              <span class="t-verified-badge" title="Pengguna Terverifikasi">
                <i class="fa-solid fa-check text-xs"></i> Verified
              </span>
            </div>
          </div>
        @empty
          <div class="testimonial-card" style="grid-column: span 3; text-align: center; padding: 40px;">
            <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada testimoni yang dipublikasikan.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- PRICING TIERS -->
  <section class="section-padding" id="harga">
    <div class="container">
      <div class="section-header">
        <span class="section-tag"><i class="fa-solid fa-cubes"></i> PAKET KUOTA & TIER</span>
        <h2 class="section-title">Pilihan Paket Sesuai Kebutuhan</h2>
        <p class="section-desc">
          Seluruh layanan berjalan di server cloud kami (SaaS managed). Mulai gratis sekarang dan upgrade kapan saja!
        </p>
      </div>

      <div class="pricing-grid">
        @php
          $landingPackages = $packages ?? \App\Models\Package::whereIn('status', ['active', 'coming_soon'])
            ->orderBy('sort_order')->orderBy('id')->get();
        @endphp

        @forelse($landingPackages as $pkg)
          <div class="price-card {{ $pkg->status === 'active' && $pkg->price > 0 ? 'featured-plan' : '' }}"
               style="{{ $pkg->status === 'coming_soon' ? 'opacity: 0.85;' : '' }}">
            <div>
              <div class="mono-badge" style="margin-bottom: 12px; {{ $pkg->status === 'coming_soon' ? 'opacity: 0.65;' : '' }}">
                {{ $pkg->status === 'coming_soon' ? 'COMING SOON' : ($pkg->badge ?? 'AKTIF') }}
              </div>
              <div class="price-header">
                <h3 class="price-tier-name">{{ $pkg->name }}</h3>
                <p class="price-tier-sub">{{ $pkg->description ?? 'Paket layanan WhatsApp Gateway' }}</p>
              </div>

              <div class="price-amount-wrap">
                @if($pkg->price == 0)
                  <span class="price-amount">Gratis</span>
                  <span class="price-period">/ selamanya</span>
                @elseif($pkg->status === 'coming_soon')
                  <span class="price-amount">Segera</span>
                  <span class="price-period">/ bulan</span>
                @else
                  <span class="price-amount">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                  <span class="price-period">/ bulan</span>
                @endif
              </div>

              <ul class="price-features">
                @if(!empty($pkg->benefits) && is_array($pkg->benefits))
                  @foreach($pkg->benefits as $benefit)
                    <li>{{ $benefit }}</li>
                  @endforeach
                @else
                  <li><strong>{{ $pkg->max_devices }} Device</strong> WhatsApp terhubung</li>
                  <li><strong>{{ number_format($pkg->daily_message_limit, 0, ',', '.') }} Pesan / Hari</strong></li>
                  <li>REST API Key Terisolasi</li>
                @endif
              </ul>
            </div>

            @if($pkg->status === 'coming_soon')
              <button class="btn btn-secondary btn-disabled" style="width: 100%;" disabled>
                ⏳ Segera Hadir
              </button>
            @elseif($pkg->price == 0)
              @auth
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary" style="width: 100%;">
                  Buka Dashboard
                </a>
              @else
                <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">
                  Daftar Gratis Sekarang
                </a>
              @endauth
            @else
              @auth
                <a href="{{ route('user.upgrade', ['package_id' => $pkg->id]) }}" class="btn btn-primary" style="width: 100%;">
                  Pilih Paket Ini →
                </a>
              @else
                <a href="{{ route('register', ['package_id' => $pkg->id]) }}" class="btn btn-primary" style="width: 100%;">
                  Pilih Paket Ini →
                </a>
              @endauth
            @endif
          </div>
        @empty
          <div class="price-card featured-plan">
            <div>
              <div class="mono-badge" style="margin-bottom: 12px;">AKTIF SEKARANG</div>
              <div class="price-header">
                <h3 class="price-tier-name">Free Tier</h3>
                <p class="price-tier-sub">Managed Cloud Server (Gratis)</p>
              </div>
              <div class="price-amount-wrap">
                <span class="price-amount">Gratis</span>
                <span class="price-period">/ selamanya</span>
              </div>
              <ul class="price-features">
                <li><strong>2 Device</strong> WhatsApp terhubung</li>
                <li><strong>50 Pesan / Hari</strong></li>
                <li>REST API Key Terisolasi</li>
              </ul>
            </div>
            @auth
              <a href="{{ route('user.dashboard') }}" class="btn btn-primary" style="width: 100%;">Buka Dashboard</a>
            @else
              <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">Daftar Gratis</a>
            @endauth
          </div>
        @endforelse
      </div>
    </div>
  </section>

  <!-- CODE SDK SHOWCASE (Unified Obsidian Terminal) -->
  <section class="section-padding" style="background-color: var(--bg-surface-subtle);" id="sdk">
    <div class="container">
      <div class="section-header">
        <span class="section-tag"><i class="fa-solid fa-code"></i> INTEGRASI DEVELOPER</span>
        <h2 class="section-title">Setup Cepat di Berbagai Bahasa</h2>
        <p class="section-desc">
          Gunakan API Key dari dashboard Anda untuk memanggil API JalurGaib dari Laravel, cURL, Node.js, atau Python.
        </p>
      </div>

      <div class="sdk-container">
        <div class="sdk-header">
          <div class="sdk-tabs">
            <button class="sdk-tab active" onclick="switchSdkTab('php', this)">PHP / Laravel</button>
            <button class="sdk-tab" onclick="switchSdkTab('curl', this)">cURL</button>
            <button class="sdk-tab" onclick="switchSdkTab('js', this)">Node.js / TS</button>
            <button class="sdk-tab" onclick="switchSdkTab('python', this)">Python</button>
          </div>
          <button class="btn btn-secondary btn-sm" id="copyBtn" onclick="copySdkCode()">
            Salin Kode 📋
          </button>
        </div>

        <div class="sdk-body">
          <pre class="code-block" id="sdk-code-display"><code>use Illuminate\Support\Facades\Http;

$response = Http::withHeaders([
    'X-API-Key' => 'JG_GAIB_YOUR_API_KEY',
    'Accept'    => 'application/json',
])->post('{{ url('/api/messages/send') }}', [
    'device_id' => 'dev_primary',
    'to'        => '6281234567890',
    'message'   => 'Halo! Pesanan #1042 Anda sedang dikirim via kurir 🚚',
    'delay'     => 2, // 2s human delay
]);

if ($response->successful()) {
    $data = $response->json();
}</code></pre>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ SECTION -->
  <section class="section-padding" id="faq">
    <div class="container">
      <div class="section-header">
        <span class="section-tag"><i class="fa-solid fa-circle-question"></i> PERTANYAAN UMUM</span>
        <h2 class="section-title">Pertanyaan yang Sering Diajukan</h2>
        <p class="section-desc">
          Hal penting seputar operasional layanan WhatsApp Gateway JalurGaib.
        </p>
      </div>

      <div class="faq-container">
        <div class="faq-item active" onclick="toggleFaq(this)">
          <div class="faq-header">
            <span>Apakah saya perlu install software atau server sendiri di komputer saya?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-body">
            Tidak perlu sama sekali! Layanan JalurGaib beroperasi 100% sebagai <strong>Managed Cloud SaaS</strong>. Seluruh infrastruktur server, antrean pesan, dan mesin koneksi sudah kami kelola sepenuhnya di cloud. Anda cukup mendaftar, scan QR untuk menautkan nomor, dan langsung mengirimkan pesan melalui REST API.
          </div>
        </div>

        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-header">
            <span>Berapa kuota yang saya dapatkan pada akun gratis?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-body">
            Setiap akun gratis dapat menautkan <strong>1 hingga 2 device WhatsApp</strong> dan mengirimkan kuota pesan harian gratis untuk keperluan testing integrasi sistem Anda.
          </div>
        </div>

        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-header">
            <span>Apakah HP saya harus selalu terhubung ke charger & internet?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-body">
            Tidak! Berkat fitur WhatsApp Multi-Device resmi, setelah scan QR sekali dari dashboard kami, sesi Anda tersimpan aman di server cloud. Ponsel Anda bisa dalam kondisi mati atau tanpa koneksi, server cloud JalurGaib akan tetap online dan siap memproses pengiriman pesan.
          </div>
        </div>

        <div class="faq-item" onclick="toggleFaq(this)">
          <div class="faq-header">
            <span>Bagaimana cara JalurGaib mencegah nomor terkena banned?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-body">
            Server kami menerapkan <em>Adaptive Human Jitter Delay</em> (jeda acak bertahap per pesan) serta buffer antrean cerdas. Pola pengiriman ini meniru ritme pengetikan manusia normal secara natural sehingga tidak memicu radar deteksi otomatis sistem WhatsApp.
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA BANNER -->
  <section class="section-padding" style="padding-top: 0;">
    <div class="container">
      <div class="cta-banner">
        <h2 class="cta-banner-title">Mulai Kirim Notifikasi WhatsApp Sekarang</h2>
        <p class="cta-banner-desc">
          Daftar akun gratis sekarang dan tautkan nomor WhatsApp bisnis Anda dalam waktu kurang dari 2 menit.
        </p>
        <div class="cta-buttons">
          @auth
            <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="btn btn-primary">
              Buka Dashboard Saya →
            </a>
          @else
            <a href="{{ route('register') }}" class="btn btn-primary">
              Daftar Akun Gratis Sekarang
            </a>
            <a href="{{ route('login') }}" class="btn btn-secondary">
              Masuk ke Akun
            </a>
          @endauth
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <a href="#" class="brand-logo" style="margin-bottom: 12px;">
            <div class="brand-icon">
              <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="jalurgaib-logo">
            </div>
            <span class="brand-name">JalurGaib</span>
          </a>
          <p style="font-size: 0.825rem; color: var(--text-secondary); max-width: 320px; line-height: 1.6;">
            Platform Managed WhatsApp Gateway Cloud handal dengan proteksi ritme pengetikan manusiawi dan multi-session 24/7.
          </p>
        </div>

        <div>
          <h4 class="footer-col-title">Produk</h4>
          <ul class="footer-links">
            <li><a href="#fitur">Fitur Utama</a></li>
            <li><a href="#arsitektur">Arsitektur Cloud</a></li>
            <li><a href="#testimoni">Ulasan Klien</a></li>
            <li><a href="#harga">Paket Kuota</a></li>
            <li><a href="#faq">FAQ</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-col-title">Akses</h4>
          <ul class="footer-links">
            @auth
              <li><a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}">Dashboard Saya</a></li>
            @else
              <li><a href="{{ route('login') }}">Masuk</a></li>
              <li><a href="{{ route('register') }}">Daftar Gratis</a></li>
            @endauth
            <li><a href="{{ route('docs') }}">Dokumentasi API</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-col-title">Disclaimer</h4>
          <p style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.5;">
            JalurGaib adalah platform cloud independen dan tidak berafiliasi secara resmi dengan WhatsApp atau Meta Platforms, Inc.
          </p>
        </div>
      </div>

      <div class="footer-bottom">
        <div>© 2026 JalurGaib — Managed WhatsApp Cloud Service.</div>
        <div>Monochromatic Bento Design System</div>
      </div>
    </div>
  </footer>

  <!-- FLOATING WHATSAPP CS BUTTON (Bottom-Right) -->
  <div class="wa-cs-floating">
    <div class="wa-cs-tooltip">
      <span>Chat CS JalurGaib</span>
    </div>
    <a href="https://wa.me/6283141847231?text=Halo%20CS%20JalurGaib,%20saya%20tertarik%20dengan%20layanan%20WhatsApp%20Gateway%20dan%20ingin%20tanya%20info%20lebih%20lanjut."
       target="_blank"
       rel="noopener noreferrer"
       class="wa-cs-btn"
       title="Chat Customer Service JalurGaib via WhatsApp">
      <i class="fa-brands fa-whatsapp"></i>
    </a>
  </div>

  <!-- JAVASCRIPT -->
  <script>
    // Theme Switcher (Instant silent toggle)
    function toggleTheme() {
      const html = document.documentElement;
      const current = html.getAttribute('data-theme') || 'light';
      const next = current === 'light' ? 'dark' : 'light';
      html.setAttribute('data-theme', next);
      localStorage.setItem('jg_theme', next);
    }

    (function() {
      const saved = localStorage.getItem('jg_theme');
      if (saved) {
        document.documentElement.setAttribute('data-theme', saved);
      }
    })();

    // Terminal Preview Tab Switcher
    function switchPreviewTab(tab, btn) {
      document.querySelectorAll('.terminal-tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      document.getElementById('preview-tab-json').style.display = 'none';
      document.getElementById('preview-tab-response').style.display = 'none';
      document.getElementById('preview-tab-auth').style.display = 'none';

      if (tab === 'json') {
        document.getElementById('preview-tab-json').style.display = 'block';
      } else if (tab === 'response') {
        document.getElementById('preview-tab-response').style.display = 'block';
      } else if (tab === 'auth') {
        document.getElementById('preview-tab-auth').style.display = 'block';
      }
    }

    // SDK Code Snippets
    const sdkSnippets = {
      php: `use Illuminate\\Support\\Facades\\Http;

$response = Http::withHeaders([
    'X-API-Key' => 'JG_GAIB_YOUR_API_KEY',
    'Accept'    => 'application/json',
])->post('{{ url('/api/messages/send') }}', [
    'device_id' => 'dev_primary',
    'to'        => '6281234567890',
    'message'   => 'Halo! Pesanan #1042 Anda sedang dikirim via kurir 🚚',
    'delay'     => 2, // 2s human delay
]);

if ($response->successful()) {
    $data = $response->json();
}`,
      curl: `curl -X POST {{ url('/api/messages/send') }} \\
  -H "X-API-Key: JG_GAIB_YOUR_API_KEY" \\
  -H "Content-Type: application/json" \\
  -d '{
    "device_id": "dev_primary",
    "to": "6281234567890",
    "message": "Halo! Pesanan #1042 Anda sedang dikirim via kurir 🚚",
    "delay": 2
  }'`,
      js: `import axios from 'axios';

const { data } = await axios.post('{{ url('/api/messages/send') }}', {
  device_id: 'dev_primary',
  to: '6281234567890',
  message: 'Halo! Pesanan #1042 Anda sedang dikirim via kurir 🚚',
  delay: 2
}, {
  headers: {
    'X-API-Key': 'JG_GAIB_YOUR_API_KEY',
    'Content-Type': 'application/json'
  }
});

console.log(data);`,
      python: `import requests

res = requests.post("{{ url('/api/messages/send') }}", json={
    "device_id": "dev_primary",
    "to": "6281234567890",
    "message": "Halo! Pesanan #1042 Anda sedang dikirim via kurir 🚚",
    "delay": 2
}, headers={
    "X-API-Key": "JG_GAIB_YOUR_API_KEY",
    "Content-Type": "application/json"
})

print(res.json())`
    };

    function switchSdkTab(lang, btn) {
      document.querySelectorAll('.sdk-tab').forEach(t => t.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('sdk-code-display').innerText = sdkSnippets[lang];
    }

    function copySdkCode() {
      const code = document.getElementById('sdk-code-display').innerText;
      navigator.clipboard.writeText(code);
      const btn = document.getElementById('copyBtn');
      if (btn) {
        const originalText = btn.innerHTML;
        btn.innerHTML = 'Tersalin! ✓';
        setTimeout(() => {
          btn.innerHTML = originalText;
        }, 2000);
      }
    }

    // FAQ Toggle
    function toggleFaq(el) {
      const isActive = el.classList.contains('active');
      document.querySelectorAll('.faq-item').forEach(item => item.classList.remove('active'));
      if (!isActive) {
        el.classList.add('active');
      }
    }

    // Mobile Navigation Drawer Toggle (Zero Overflow & Body Lock)
    function toggleMobileMenu() {
      const drawer = document.getElementById('mobileNavDrawer');
      const overlay = document.getElementById('mobileNavOverlay');
      if (!drawer || !overlay) return;

      const isOpen = drawer.classList.contains('active');
      if (isOpen) {
        drawer.classList.remove('active');
        overlay.classList.remove('active');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      } else {
        drawer.classList.add('active');
        overlay.classList.add('active');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
      }
    }
  </script>
</body>
</html>
