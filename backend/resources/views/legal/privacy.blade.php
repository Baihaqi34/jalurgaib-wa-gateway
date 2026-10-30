<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kebijakan Privasi (Privacy Policy) — JalurGaib WA Gateway</title>
  <meta name="description" content="Kebijakan Privasi resmi platform JalurGaib WA Gateway. Penjelasan transparan mengenai pengumpulan, perlindungan, dan pengelolaan data pengguna termasuk Google Firebase Authentication.">
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

  <!-- Google Fonts & Font Awesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <style>
    :root {
      --bg-base: #09090b;
      --bg-surface: #121215;
      --bg-elevated: #18181b;
      --border-subtle: rgba(255, 255, 255, 0.08);
      --border-medium: rgba(255, 255, 255, 0.16);
      --text-primary: #fafafa;
      --text-secondary: #a1a1aa;
      --text-muted: #71717a;
      --accent-mono: #ffffff;
      --radius-sm: 8px;
      --radius-md: 14px;
      --radius-lg: 20px;
    }

    [data-theme="light"] {
      --bg-base: #f8fafc;
      --bg-surface: #ffffff;
      --bg-elevated: #f1f5f9;
      --border-subtle: rgba(0, 0, 0, 0.08);
      --border-medium: rgba(0, 0, 0, 0.16);
      --text-primary: #0f172a;
      --text-secondary: #475569;
      --text-muted: #64748b;
      --accent-mono: #000000;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--bg-base);
      color: var(--text-primary);
      line-height: 1.7;
      transition: background-color 0.25s ease, color 0.25s ease;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Top Navbar */
    .legal-nav {
      position: sticky;
      top: 0;
      z-index: 50;
      background: var(--bg-surface);
      border-bottom: 1px solid var(--border-subtle);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      padding: 14px 0;
    }

    .container {
      width: 100%;
      max-width: 960px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .nav-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
    }

    .brand-link {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: var(--text-primary);
      font-weight: 800;
      font-size: 1.05rem;
      letter-spacing: -0.02em;
    }

    .brand-logo-img {
      width: 28px;
      height: 28px;
      border-radius: var(--radius-sm);
      object-fit: cover;
    }

    .nav-actions {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .theme-btn, .nav-btn {
      background: var(--bg-elevated);
      border: 1px solid var(--border-subtle);
      color: var(--text-primary);
      padding: 7px 14px;
      border-radius: var(--radius-sm);
      font-size: 0.825rem;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
    }

    .theme-btn:hover, .nav-btn:hover {
      border-color: var(--border-medium);
      background: var(--bg-surface);
    }

    /* Hero Banner */
    .legal-hero {
      padding: 48px 0 32px 0;
      border-bottom: 1px solid var(--border-subtle);
      background: linear-gradient(180deg, var(--bg-surface) 0%, var(--bg-base) 100%);
    }

    .badge-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 4px 12px;
      background: var(--bg-elevated);
      border: 1px solid var(--border-subtle);
      border-radius: 9999px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--text-secondary);
      margin-bottom: 16px;
    }

    .hero-title {
      font-size: 2.25rem;
      font-weight: 800;
      letter-spacing: -0.03em;
      line-height: 1.25;
      margin-bottom: 12px;
      color: var(--text-primary);
    }

    .hero-meta {
      font-size: 0.85rem;
      color: var(--text-muted);
      display: flex;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    /* Main Content */
    .legal-content {
      padding: 40px 0 60px 0;
      flex: 1;
    }

    .legal-card {
      background: var(--bg-surface);
      border: 1px solid var(--border-subtle);
      border-radius: var(--radius-lg);
      padding: 36px 40px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    @media (max-width: 640px) {
      .legal-card {
        padding: 24px 20px;
        border-radius: var(--radius-md);
      }
      .hero-title {
        font-size: 1.75rem;
      }
    }

    .legal-section {
      margin-bottom: 32px;
    }

    .legal-section:last-child {
      margin-bottom: 0;
    }

    .section-heading {
      font-size: 1.25rem;
      font-weight: 700;
      letter-spacing: -0.015em;
      margin-bottom: 12px;
      color: var(--text-primary);
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .legal-section p {
      font-size: 0.925rem;
      color: var(--text-secondary);
      margin-bottom: 14px;
    }

    .legal-section ul {
      list-style: none;
      padding-left: 0;
      margin-bottom: 14px;
    }

    .legal-section ul li {
      position: relative;
      padding-left: 24px;
      font-size: 0.925rem;
      color: var(--text-secondary);
      margin-bottom: 8px;
    }

    .legal-section ul li::before {
      content: "•";
      position: absolute;
      left: 8px;
      top: 0;
      color: var(--text-muted);
      font-size: 1.2rem;
      line-height: 1;
    }

    .highlight-box {
      background: var(--bg-elevated);
      border: 1px solid var(--border-subtle);
      border-left: 4px solid var(--accent-mono);
      border-radius: var(--radius-sm);
      padding: 16px 20px;
      margin: 16px 0;
    }

    .highlight-box p {
      margin-bottom: 0;
      color: var(--text-primary);
      font-size: 0.875rem;
    }

    /* Footer */
    .legal-footer {
      border-top: 1px solid var(--border-subtle);
      padding: 28px 0;
      background: var(--bg-surface);
      font-size: 0.8rem;
      color: var(--text-muted);
    }

    .footer-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }

    .footer-links {
      display: flex;
      gap: 16px;
    }

    .footer-links a {
      color: var(--text-secondary);
      text-decoration: none;
      transition: color 0.2s;
    }

    .footer-links a:hover {
      color: var(--text-primary);
    }
  </style>
</head>
<body>

  <!-- Navigation -->
  <header class="legal-nav">
    <div class="container">
      <div class="nav-inner">
        <a href="{{ url('/') }}" class="brand-link">
          <img src="{{ asset('images/logo.png') }}" alt="JalurGaib Logo" class="brand-logo-img">
          <span>JalurGaib WA Gateway</span>
        </a>

        <div class="nav-actions">
          <a href="{{ route('terms') }}" class="nav-btn">
            <i class="fa-solid fa-file-contract"></i> Terms of Service
          </a>
          <button type="button" class="theme-btn" onclick="toggleTheme()" title="Ubah Tema Gelap / Terang">
            <i class="fa-solid fa-circle-half-stroke"></i>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero Header -->
  <section class="legal-hero">
    <div class="container">
      <div class="badge-pill">
        <i class="fa-solid fa-shield-halved"></i> Kebijakan Privasi & Perlindungan Data
      </div>
      <h1 class="hero-title">Kebijakan Privasi (Privacy Policy)</h1>
      <div class="hero-meta">
        <span><i class="fa-regular fa-clock"></i> Terakhir Diperbarui: 30 Oktober 2026</span>
        <span><i class="fa-solid fa-building"></i> JalurGaib Cloud Systems</span>
        <span><i class="fa-solid fa-lock"></i> Kepatuhan Google OAuth & Firebase</span>
      </div>
    </div>
  </section>

  <!-- Content Section -->
  <main class="legal-content">
    <div class="container">
      <div class="legal-card">
        
        <!-- Pendahuluan -->
        <section class="legal-section">
          <h2 class="section-heading">1. Pendahuluan</h2>
          <p>
            Selamat datang di <strong>JalurGaib WA Gateway</strong> ("kami", "layanan kami", atau "JalurGaib"). Kami sangat menghargai privasi dan keamanan data pribadi Anda. Kebijakan Privasi ini menerangkan bagaimana kami mengumpulkan, menggunakan, menyimpan, memproses, dan melindungi informasi pribadi pengguna saat Anda mengakses situs web, mendaftar akun, menggunakan integrasi <strong>Google Sign-In / Firebase Authentication</strong>, atau memanfaatkan REST API kami.
          </p>
          <p>
            Dengan mengakses atau menggunakan platform JalurGaib, Anda menyetujui praktik pengelolaan data sebagaimana dijelaskan dalam dokumen Kebijakan Privasi ini.
          </p>
        </section>

        <!-- Data yang Kami Kumpulkan -->
        <section class="legal-section">
          <h2 class="section-heading">2. Informasi yang Kami Kumpulkan</h2>
          <p>
            Kami mengumpulkan data yang diperlukan semata-mata untuk mengoperasikan layanan gateway secara aman dan memberikan fungsionalitas terbaik bagi Anda:
          </p>
          
          <div class="highlight-box">
            <p><strong>A. Informasi Akun Google / Firebase Authentication:</strong></p>
            <p style="margin-top: 6px; font-size: 0.85rem; color: var(--text-secondary);">
              Saat Anda memilih untuk masuk menggunakan <em>Google Sign-In</em> melalui Google Firebase, kami meminta izin akses terbatas sesuai cakupan profil dasar Google OAuth 2.0 (<em>email, profile, openid</em>):
            </p>
            <ul style="margin-top: 8px; margin-bottom: 0;">
              <li><strong>Nama Lengkap (Display Name):</strong> Digunakan untuk menampilkan identitas akun Anda di dashboard.</li>
              <li><strong>Alamat Email:</strong> Digunakan sebagai pengenal unik akun, pengiriman faktur/invoice langganan, dan komunikasi keamanan resmi.</li>
              <li><strong>Foto Profil (Avatar URL):</strong> Ditampilkan sebagai avatar akun pada navigasi dashboard.</li>
              <li><strong>Google User ID (UID):</strong> Identifier acak untuk memvalidasi otentikasi login secara kriptografis tanpa menyimpan kata sandi Google Anda.</li>
            </ul>
          </div>

          <ul>
            <li><strong>Data Kredensial Langsung:</strong> Jika Anda mendaftar secara manual (bukan via Google), kami menyimpan nama, email, dan hash kata sandi yang dienkripsi menggunakan algoritma Bcrypt/Argon2id.</li>
            <li><strong>Data WhatsApp Device & API Key:</strong> Informasi sesi token otentikasi perangkat WhatsApp yang Anda hubungkan (melalui mekanisme QR code resmi Multi-Device) serta API Key unik yang digenerate untuk akses REST API Anda.</li>
            <li><strong>Data Log Transaksi Pesan:</strong> Nomor tujuan pengiriman, timestamp waktu kirim, ID pesan unik, serta status pengiriman pesan (Pending, Terkirim, Gagal).</li>
          </ul>
        </section>

        <!-- Cara Kami Menggunakan Data -->
        <section class="legal-section">
          <h2 class="section-heading">3. Penggunaan Informasi</h2>
          <p>
            Informasi yang kami kumpulkan dipergunakan untuk tujuan berikut:
          </p>
          <ul>
            <li>Mengotentikasi identitas akun Anda secara aman melalui Firebase / Google Identity Toolkit.</li>
            <li>Menyediakan infrastruktur pengiriman pesan WhatsApp via REST API atas instruksi eksplisit dari pemilik akun atau API Key yang bersangkutan.</li>
            <li>Menghitung kuota harian atau bulanan sesuai paket langganan aktif yang Anda miliki.</li>
            <li>Mendeteksi, mencegah, dan menindaklanjuti potensi penyalahgunaan layanan (seperti spamming, penipuan, atau peretasan).</li>
            <li>Mengirimkan pemberitahuan penting terkait perubahan status layanan, keamanan akun, atau bukti pembayaran langganan.</li>
          </ul>
        </section>

        <!-- Kepatuhan Google API Services User Data Policy -->
        <section class="legal-section">
          <h2 class="section-heading">4. Kepatuhan Kebijakan Data Pengguna Google</h2>
          <p>
            Penggunaan dan transfer data yang diterima dari Google API oleh JalurGaib ke aplikasi lain sepenuhnya mematuhi 
            <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener noreferrer" style="color: var(--text-primary); font-weight: 600; text-decoration: underline;">Kebijakan Data Pengguna Layanan Google API</a>, termasuk persyaratan <em>Limited Use</em>:
          </p>
          <ul>
            <li>Kami <strong>TIDAK PERNAH</strong> menjual, menyewakan, atau memperdagangkan data pribadi pengguna yang diperoleh dari Google Sign-In kepada pihak ketiga mana pun.</li>
            <li>Kami <strong>TIDAK</strong> menggunakan data Google Anda untuk keperluan periklanan bertarget (targeted ads), survei komersial eksternal, atau penilaian kelayakan kredit.</li>
            <li>Staf atau personel internal kami tidak membaca konten data pribadi Anda kecuali atas persetujuan tertulis eksplisit Anda untuk tujuan investigasi teknis / debugging dukungan pelanggan.</li>
          </ul>
        </section>

        <!-- Keamanan & Penyimpanan Data -->
        <section class="legal-section">
          <h2 class="section-heading">5. Keamanan dan Penyimpanan Data</h2>
          <p>
            Kami menerapkan langkah-langkah keamanan teknis dan organisasional berstandar industri:
          </p>
          <ul>
            <li>Seluruh pertukaran data antara browser/klien API Anda dan server kami dienkripsi menggunakan protokol <strong>Transport Layer Security (HTTPS/TLS 1.3)</strong>.</li>
            <li>Kredensial sensitif seperti kata sandi dan API Key dilindungi dengan enkripsi satu arah yang kuat.</li>
            <li>Folder unggahan media dilindungi oleh aturan keamanan server ketat yang menolak eksekusi berkas skrip executable.</li>
            <li>Server database kami beroperasi dalam jaringan internal yang terisolasi dengan akses terbatas.</li>
          </ul>
        </section>

        <!-- Hak Pengguna & Penghapusan Akun -->
        <section class="legal-section">
          <h2 class="section-heading">6. Hak Pengguna & Penghapusan Data (Data Deletion)</h2>
          <p>
            Sesuai regulasi perlindungan data yang berlaku (termasuk UU Perlindungan Data Pribadi No. 27 Tahun 2022 dan GDPR):
          </p>
          <ul>
            <li><strong>Hak Akses & Pembaruan:</strong> Anda dapat melihat dan memperbarui profil nama serta informasi akun Anda kapan saja melalui menu Pengaturan Akun di dashboard pengguna.</li>
            <li><strong>Hak Pencabutan Akses Google:</strong> Anda dapat mencabut izin akses Google Sign-In JalurGaib kapan saja langsung melalui halaman resmi <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener noreferrer" style="color: var(--text-primary); text-decoration: underline;">Pengaturan Keamanan Akun Google Anda</a>.</li>
            <li><strong>Hak Penghapusan Akun (Right to Erasure):</strong> Anda berhak meminta penghapusan permanen seluruh data akun, sesi WhatsApp, riwayat log pesan, dan API Key Anda dari database kami dengan menghubungi kami melalui saluran kontak resmi yang tercantum di bawah.</li>
          </ul>
        </section>

        <!-- Cookie & Penyimpanan Lokal -->
        <section class="legal-section">
          <h2 class="section-heading">7. Cookie dan Penyimpanan Lokal</h2>
          <p>
            Kami menggunakan cookie sesi dan LocalStorage pada browser Anda hanya untuk keperluan fungsional:
          </p>
          <ul>
            <li>Menjaga status autentikasi login Anda agar tetap aktif selama Anda menggunakan dashboard.</li>
            <li>Menyimpan preferensi tampilan tema (Dark Mode / Light Mode).</li>
            <li>Mencegah serangan pemalsuan permintaan antar-situs melalui token CSRF (Cross-Site Request Forgery).</li>
          </ul>
        </section>

        <!-- Kontak Kami -->
        <section class="legal-section">
          <h2 class="section-heading">8. Kontak Kami</h2>
          <p>
            Jika Anda memiliki pertanyaan, masukan, keluhan, atau ingin mengajukan permohonan penghapusan data seputar Kebijakan Privasi ini, silakan hubungi tim kami:
          </p>
          <div class="highlight-box">
            <p><strong>JalurGaib Support & Data Protection Officer</strong></p>
            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px;">
              <i class="fa-solid fa-envelope"></i> Email: <a href="mailto:support@jalurgaib.com" style="color: var(--text-primary); font-weight: 600;">support@jalurgaib.com</a><br>
              <i class="fa-brands fa-whatsapp"></i> WhatsApp Helpdesk: <a href="https://wa.me/6283141847231" target="_blank" rel="noopener noreferrer" style="color: var(--text-primary); font-weight: 600;">+62 831-4184-7231</a><br>
              <i class="fa-solid fa-globe"></i> Website Resmi: <a href="{{ url('/') }}" style="color: var(--text-primary); font-weight: 600;">{{ url('/') }}</a>
            </p>
          </div>
        </section>

      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="legal-footer">
    <div class="container">
      <div class="footer-inner">
        <div>© 2026 JalurGaib WA Gateway. Seluruh hak cipta dilindungi undang-undang.</div>
        <div class="footer-links">
          <a href="{{ route('privacy') }}">Privacy Policy</a>
          <a href="{{ route('terms') }}">Terms of Service</a>
          <a href="{{ route('docs') }}">API Docs</a>
          <a href="{{ url('/') }}">Beranda</a>
        </div>
      </div>
    </div>
  </footer>

  <script>
    function toggleTheme() {
      const html = document.documentElement;
      const current = html.getAttribute('data-theme') || 'dark';
      const next = current === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-theme', next);
      localStorage.setItem('jg_theme', next);
    }

    (function() {
      const saved = localStorage.getItem('jg_theme');
      if (saved) {
        document.documentElement.setAttribute('data-theme', saved);
      }
    })();
  </script>
</body>
</html>
