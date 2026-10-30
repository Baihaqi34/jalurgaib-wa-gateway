<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Syarat dan Ketentuan (Terms & Conditions) — JalurGaib WA Gateway</title>
  <meta name="description" content="Syarat dan Ketentuan Penggunaan Layanan resmi platform JalurGaib WA Gateway. Ketentuan penggunaan akun, batasan layanan, acceptable use policy, dan ketentuan langganan.">
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
          <a href="{{ route('privacy') }}" class="nav-btn">
            <i class="fa-solid fa-shield-halved"></i> Privacy Policy
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
        <i class="fa-solid fa-file-contract"></i> Ketentuan Hukum & Perjanjian Layanan
      </div>
      <h1 class="hero-title">Syarat dan Ketentuan (Terms of Service)</h1>
      <div class="hero-meta">
        <span><i class="fa-regular fa-clock"></i> Berlaku Sejak: 30 Oktober 2026</span>
        <span><i class="fa-solid fa-scale-balanced"></i> Hukum Republik Indonesia</span>
        <span><i class="fa-solid fa-code"></i> Developer & Enterprise API</span>
      </div>
    </div>
  </section>

  <!-- Content Section -->
  <main class="legal-content">
    <div class="container">
      <div class="legal-card">
        
        <!-- Penerimaan Ketentuan -->
        <section class="legal-section">
          <h2 class="section-heading">1. Penerimaan Ketentuan</h2>
          <p>
            Dengan mendaftar, mengakses, atau menggunakan layanan platform <strong>JalurGaib WA Gateway</strong> ("Layanan", "kami", atau "JalurGaib"), Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat secara hukum oleh Syarat dan Ketentuan ini ("Ketentuan").
          </p>
          <p>
            Jika Anda tidak menyetujui bagian mana pun dari Ketentuan ini, Anda tidak diperkenankan untuk mengakses atau menggunakan layanan kami.
          </p>
        </section>

        <!-- Deskripsi Layanan -->
        <section class="legal-section">
          <h2 class="section-heading">2. Deskripsi Layanan</h2>
          <p>
            JalurGaib adalah platform perantara berbasis cloud (<em>Managed Cloud Gateway</em>) yang menyediakan antarmuka Application Programming Interface (REST API) bagi para pengembang perangkat lunak, pemilik toko daring, dan pelaku usaha untuk menghubungkan sistem aplikasi mereka ke protokol pengiriman pesan WhatsApp secara mandiri.
          </p>
          <div class="highlight-box">
            <p><strong>Pernyataan Independensi (Disclaimer):</strong></p>
            <p style="margin-top: 6px; font-size: 0.85rem; color: var(--text-secondary);">
              JalurGaib adalah entitas pihak ketiga independen. Layanan ini <strong>TIDAK BERAFILIASI</strong>, tidak disponsori, tidak dikelola, dan tidak didukung secara resmi oleh Meta Platforms, Inc., WhatsApp LLC, atau anak perusahaannya. Merek dagang "WhatsApp" adalah milik sah dari WhatsApp LLC.
            </p>
          </div>
        </section>

        <!-- Akun & Otentikasi Pengguna -->
        <section class="legal-section">
          <h2 class="section-heading">3. Akun dan Otentikasi</h2>
          <ul>
            <li><strong>Kelayakan:</strong> Anda harus berusia minimal 18 tahun atau memiliki izin wali hukum yang sah untuk membuat akun di platform kami.</li>
            <li><strong>Google / Firebase Authentication:</strong> Anda dapat membuat akun menggunakan akun Google resmi Anda melalui integrasi Firebase Auth. Anda bertanggung jawab penuh untuk menjaga keamanan akun Google dan kredensial akses Anda.</li>
            <li><strong>Kerahasiaan API Key:</strong> Anda bertanggung jawab mutlak atas segala aktivitas pengiriman pesan yang dilakukan menggunakan API Key akun Anda. Segera lakukan rotasi / buat ulang API Key di dashboard jika Anda menduga kunci Anda telah bocor.</li>
          </ul>
        </section>

        <!-- Kebijakan Penggunaan yang Bertanggung Jawab -->
        <section class="legal-section">
          <h2 class="section-heading">4. Kebijakan Penggunaan yang Bertanggung Jawab (Acceptable Use Policy)</h2>
          <p>
            Platform JalurGaib dirancang untuk pengiriman notifikasi transaksi yang sah (seperti OTP, konfirmasi pesanan toko daring, invoice tagihan, dan pengumuman pelanggan yang telah memberikan persetujuan / opt-in).
          </p>
          <p><strong>Anda dilarang keras menggunakan layanan JalurGaib untuk:</strong></p>
          <ul>
            <li>Mengirimkan pesan massal tanpa persetujuan penerima (Spamming yang melanggar ketentuan hukum).</li>
            <li>Memfasilitasi kegiatan perjudian daring, judi online, kasino ilegal, atau sejenisnya.</li>
            <li>Melakukan upaya penipuan, pemerasan, rekayasa sosial, atau *phishing*.</li>
            <li>Menyebarkan materi pornografi, konten asusila, atau eksploitasi di bawah umur.</li>
            <li>Menyebarkan ujaran kebencian, ancaman kekerasan, fitnah, terorisme, atau konten diskriminatif.</li>
            <li>Menjual atau mempromosikan barang-barang ilegal atau zat terlarang (narkoba, senjata ilegal, dll).</li>
            <li>Menyerang, membebani (DDoS), atau menguji kerentanan infrastruktur server JalurGaib secara tidak sah.</li>
          </ul>

          <div class="highlight-box">
            <p><strong>Sanksi Pelanggaran:</strong></p>
            <p style="margin-top: 6px; font-size: 0.85rem; color: var(--text-secondary);">
              Kami menerapkan kebijakan <em>Zero-Tolerance</em>. Segala bentuk pelanggaran terhadap Acceptable Use Policy di atas akan mengakibatkan <strong>pemutusan sepihak dan penangguhan akun permanen (Suspend/Ban)</strong> tanpa pemberitahuan sebelumnya, serta tidak berhak atas pengembalian dana langganan (*non-refundable*).
            </p>
          </div>
        </section>

        <!-- Kuota & Pembayaran Langganan -->
        <section class="legal-section">
          <h2 class="section-heading">5. Kuota dan Pembayaran Langganan</h2>
          <ul>
            <li><strong>Paket Layanan:</strong> Layanan kami menawarkan kuota gratis (Starter Free) serta paket berlangganan berbayar (Starter Pro, Business, Enterprise) dengan kuota pengiriman pesan dan batas perangkat yang berbeda.</li>
            <li><strong>Siklus Reset Kuota:</strong> Kuota pesan dihitung dan di-reset sesuai siklus paket Anda (Harian / Bulanan). Kuota yang tidak terpakai dalam satu siklus tidak dapat diakumulasikan ke siklus berikutnya.</li>
            <li><strong>Pembayaran:</strong> Pembayaran langganan diproses melalui metode pembayaran QRIS / Payment Gateway resmi yang terintegrasi. Akses paket akan aktif secara otomatis setelah pembayaran terkonfirmasi lunas.</li>
          </ul>
        </section>

        <!-- Batasan Tanggung Jawab -->
        <section class="legal-section">
          <h2 class="section-heading">6. Batasan Tanggung Jawab (Limitation of Liability)</h2>
          <p>
            Layanan disediakan dengan dasar <em>"AS IS"</em> (sebagaimana adanya) dan <em>"AS AVAILABLE"</em> (sebagaimana tersedia).
          </p>
          <ul>
            <li>Meskipun mesin kami menerapkan algoritma <em>Adaptive Human Jitter Delay</em> untuk meminimalkan risiko, Anda memahami bahwa kebijakan pemblokiran nomor WhatsApp sepenuhnya berada di bawah kendali dan diskresi pihak WhatsApp / Meta Platforms, Inc. JalurGaib tidak bertanggung jawab atas pemblokiran nomor yang diakibatkan oleh pengaduan/report dari penerima pesan Anda.</li>
            <li>JalurGaib tidak bertanggung jawab atas kerugian langsung, tidak langsung, insidental, atau hilangnya keuntungan bisnis yang timbul dari kegagalan transmisi pesan akibat gangguan jaringan seluler pihak ketiga.</li>
          </ul>
        </section>

        <!-- Perubahan Ketentuan -->
        <section class="legal-section">
          <h2 class="section-heading">7. Perubahan Ketentuan</h2>
          <p>
            Kami berhak untuk mengubah, memperbarui, atau memodifikasi Syarat dan Ketentuan ini sewaktu-waktu. Pembaruan akan berlaku efektif setelah diunggah ke halaman ini. Kami menyarankan pengguna untuk memeriksa halaman ini secara berkala.
          </p>
        </section>

        <!-- Hukum yang Mengatur -->
        <section class="legal-section">
          <h2 class="section-heading">8. Hukum yang Mengatur</h2>
          <p>
            Syarat dan Ketentuan ini diatur oleh dan ditafsirkan sesuai dengan hukum yang berlaku di <strong>Negara Kesatuan Republik Indonesia</strong>. Segala perselisihan yang timbul akan diselesaikan melalui musyawarah untuk mufakat terlebih dahulu sebelum menempuh yurisdiksi pengadilan yang berwenang.
          </p>
        </section>

        <!-- Kontak Layanan -->
        <section class="legal-section">
          <h2 class="section-heading">9. Kontak dan Bantuan</h2>
          <p>
            Jika Anda memiliki pertanyaan seputar Syarat dan Ketentuan Layanan ini, silakan hubungi tim kami:
          </p>
          <div class="highlight-box">
            <p><strong>JalurGaib Legal & Customer Care</strong></p>
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
