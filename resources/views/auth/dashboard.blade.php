<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV. Syavir Jaya Utama</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #eef7ff 0%, #f8fbff 45%, #ffffff 100%);
            color: #1e293b;
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            box-shadow: 0 6px 24px rgba(15, 23, 42, 0.08);
        }

        .nav-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
        }

        .brand {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.3px;
        }

        .brand span {
            color: #2563eb;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 26px;
        }

        .nav-links a {
            color: #334155;
            font-weight: 600;
            font-size: 0.97rem;
            transition: 0.3s ease;
        }

        .nav-links a:hover {
            color: #2563eb;
        }

        .login-btn {
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: #ffffff !important;
            padding: 11px 22px;
            border-radius: 999px;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.22);
            transition: 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.28);
            color: #ffffff !important;
        }

        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }

        .menu-toggle span {
            width: 26px;
            height: 3px;
            background: #0f172a;
            border-radius: 4px;
        }

        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background:
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.23), transparent 28%),
                radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.16), transparent 28%),
                linear-gradient(135deg, #eef7ff 0%, #f8fbff 55%, #ffffff 100%);
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(to right, rgba(37, 99, 235, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, 0.04) 1px, transparent 1px);
            background-size: 38px 38px;
        }

        .hero-wrapper {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 40px;
            align-items: center;
            padding-top: 80px;
        }

        .hero-content {
            animation: fadeUp 1s ease;
        }

        .hero-badge {
            display: inline-block;
            padding: 9px 16px;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.10);
            color: #2563eb;
            font-size: 0.92rem;
            font-weight: 700;
            margin-bottom: 18px;
            border: 1px solid rgba(37, 99, 235, 0.12);
        }

        .hero h1 {
            font-size: 3.35rem;
            line-height: 1.15;
            color: #0f172a;
            margin-bottom: 18px;
            font-weight: 800;
        }

        .hero h1 span {
            color: #2563eb;
        }

        .hero p {
            font-size: 1.07rem;
            line-height: 1.8;
            color: #475569;
            max-width: 660px;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 28px;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            transition: 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: #ffffff;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.20);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.26);
        }

        .btn-outline {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
        }

        .btn-outline:hover {
            border-color: #2563eb;
            color: #2563eb;
            transform: translateY(-3px);
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.10);
            animation: floatCard 4s ease-in-out infinite;
        }

        .hero-card h3 {
            font-size: 1.45rem;
            color: #0f172a;
            margin-bottom: 18px;
            font-weight: 800;
        }

        .hero-info {
            display: grid;
            gap: 14px;
        }

        .hero-info-item {
            background: #f8fbff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 16px 18px;
        }

        .hero-info-item strong {
            display: block;
            color: #0f172a;
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .hero-info-item span {
            color: #475569;
            font-size: 0.95rem;
        }

        .section {
            padding: 90px 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.2rem;
            color: #0f172a;
            margin-bottom: 14px;
            font-weight: 800;
        }

        .section-subtitle {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 48px;
            color: #64748b;
            line-height: 1.8;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
            transition: 0.35s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.10);
            border-color: #bfdbfe;
        }

        .card-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #dbeafe, #e0f2fe);
            color: #2563eb;
            font-weight: 800;
            font-size: 1.2rem;
            margin-bottom: 18px;
        }

        .card h3 {
            font-size: 1.3rem;
            color: #0f172a;
            margin-bottom: 12px;
            font-weight: 800;
        }

        .card p {
            color: #475569;
            margin-bottom: 16px;
            line-height: 1.8;
        }

        .card ul {
            list-style: none;
        }

        .card ul li {
            position: relative;
            padding-left: 18px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .card ul li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 10px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2563eb;
        }

        .about-preview {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 26px;
            align-items: stretch;
        }

        .about-box,
        .contact-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 30px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
        }

        .about-box h3,
        .contact-box h3 {
            font-size: 1.5rem;
            color: #0f172a;
            margin-bottom: 14px;
            font-weight: 800;
        }

        .about-box p,
        .contact-box p {
            color: #475569;
            line-height: 1.8;
            margin-bottom: 14px;
        }

        .feature-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
            margin-top: 20px;
        }

        .feature-item {
            background: #f8fbff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 16px;
        }

        .feature-item h4 {
            color: #0f172a;
            font-size: 1rem;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .feature-item p {
            color: #64748b;
            font-size: 0.94rem;
            margin-bottom: 0;
        }

        .contact-list {
            list-style: none;
            margin-top: 12px;
        }

        .contact-list li {
            margin-bottom: 12px;
            color: #475569;
        }

        .contact-list strong {
            color: #0f172a;
        }

        .mini-cta {
            margin-top: 20px;
            padding: 20px;
            border-radius: 18px;
            background: linear-gradient(135deg, #eff6ff, #f0f9ff);
            border: 1px solid #dbeafe;
        }

        .mini-cta p {
            margin-bottom: 14px;
        }

        .footer {
            background: #eaf4ff;
            padding: 28px 0;
            border-top: 1px solid #dbeafe;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            color: #475569;
            font-size: 0.95rem;
        }

        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(35px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes floatCard {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-8px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        @media (max-width: 992px) {
            .hero-wrapper {
                grid-template-columns: 1fr;
            }

            .hero h1 {
                font-size: 2.75rem;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .about-preview {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: flex;
            }

            .nav-links {
                position: absolute;
                top: 78px;
                right: 5%;
                width: 240px;
                background: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 18px;
                box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10);
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
                display: none;
            }

            .nav-links.show {
                display: flex;
            }

            .hero {
                text-align: center;
            }

            .hero p {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero h1 {
                font-size: 2.2rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }

            .feature-list {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.9rem;
            }

            .hero p {
                font-size: 0.97rem;
            }

            .btn {
                width: 100%;
            }

            .about-box,
            .contact-box,
            .card,
            .hero-card {
                padding: 22px;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container nav-wrapper">
            <a href="{{ route('dashboard.awal') }}" class="brand">CV. <span>Syavir Jaya Utama</span></a>

            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="nav-links" id="navLinks">
                <a href="{{ route('lihatbarang') }}">Lihat Barang</a>
                <a href="{{ route('tentangkami') }}">Tentang Kami</a>
                <a href="{{ route('login') }}" class="login-btn">Login</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container hero-wrapper">
            <div class="hero-content">
                <div class="hero-badge">Distributor IBC, Jurigen, dan Pallet</div>
                <h1>Solusi Penjualan <span>IBC, Jurigen, dan Pallet</span> yang Praktis dan Profesional</h1>
                <p>
                    CV. Syavir Jaya Utama hadir untuk memenuhi kebutuhan usaha Anda dalam penyediaan IBC, jurigen, dan pallet dengan kualitas yang baik, layanan yang responsif, serta proses pemesanan yang efisien. Kami berfokus pada kebutuhan distribusi, pergudangan, dan penyimpanan barang agar operasional bisnis berjalan lebih optimal.
                </p>

                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn btn-primary">Pesan Sekarang</a>
                    <a href="{{ route('lihatbarang') }}" class="btn btn-outline">Lihat Barang</a>
                </div>
            </div>

            <div class="hero-card">
                <h3>Keunggulan Layanan Kami</h3>
                <div class="hero-info">
                    <div class="hero-info-item">
                        <strong>Produk Berkualitas</strong>
                        <span>IBC, jurigen, dan pallet tersedia untuk kebutuhan distribusi dan penyimpanan.</span>
                    </div>
                    <div class="hero-info-item">
                        <strong>Harga Kompetitif</strong>
                        <span>Penawaran harga yang disesuaikan untuk mendukung efisiensi usaha pelanggan.</span>
                    </div>
                    <div class="hero-info-item">
                        <strong>Pelayanan Cepat</strong>
                        <span>Respons yang cepat untuk konsultasi produk, informasi stok, dan pemesanan.</span>
                    </div>
                    <div class="hero-info-item">
                        <strong>Mitra Usaha Andal</strong>
                        <span>Kami siap menjadi rekan penyedia kebutuhan logistik dan distribusi Anda.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section reveal">
        <div class="container">
            <h2 class="section-title">Produk Utama Kami</h2>
            <p class="section-subtitle">
                Produk yang kami sediakan dirancang untuk menunjang kebutuhan industri, perdagangan, serta sistem distribusi dan penyimpanan barang secara lebih rapi, aman, dan efisien.
            </p>

            <div class="product-grid">
                <div class="card">
                    <div class="card-icon">IBC</div>
                    <h3>IBC Tank</h3>
                    <p>
                        IBC digunakan untuk kebutuhan penyimpanan dan distribusi cairan dalam kapasitas besar. Produk ini sesuai untuk kebutuhan industri yang memerlukan wadah yang kuat, efisien, dan mudah dipindahkan.
                    </p>
                    <ul>
                        <li>Kapasitas besar dan efisien</li>
                        <li>Konstruksi kuat dan stabil</li>
                        <li>Cocok untuk distribusi industri</li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-icon">JG</div>
                    <h3>Jurigen</h3>
                    <p>
                        Jurigen tersedia untuk berbagai kebutuhan penyimpanan cairan dengan bentuk yang praktis, mudah digunakan, serta mendukung proses distribusi dalam skala kecil hingga menengah.
                    </p>
                    <ul>
                        <li>Praktis untuk penyimpanan cairan</li>
                        <li>Tersedia berbagai ukuran</li>
                        <li>Mudah digunakan dan dipindahkan</li>
                    </ul>
                </div>

                <div class="card">
                    <div class="card-icon">PL</div>
                    <h3>Pallet</h3>
                    <p>
                        Pallet membantu proses pergudangan dan distribusi barang agar lebih tertata dan mudah dipindahkan. Produk ini sangat penting dalam sistem logistik modern.
                    </p>
                    <ul>
                        <li>Mendukung aktivitas gudang</li>
                        <li>Mempermudah angkut barang</li>
                        <li>Meningkatkan efisiensi distribusi</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="section reveal">
        <div class="container">
            <div class="about-preview">
                <div class="about-box">
                    <h3>Tentang Perusahaan</h3>
                    <p>
                        CV. Syavir Jaya Utama merupakan perusahaan yang bergerak di bidang penjualan IBC, jurigen, dan pallet untuk mendukung kebutuhan distribusi, penyimpanan, serta logistik berbagai sektor usaha. Kami berupaya menghadirkan produk yang sesuai kebutuhan pelanggan dengan kualitas yang terjaga dan layanan yang profesional.
                    </p>
                    <p>
                        Dalam menjalankan usaha, kami menempatkan kepuasan pelanggan sebagai prioritas. Oleh karena itu, kami mengedepankan kejelasan informasi produk, kecepatan respons, serta kemudahan proses pemesanan agar kerja sama yang terjalin dapat berlangsung secara optimal dan berkelanjutan.
                    </p>

                    <div class="feature-list">
                        <div class="feature-item">
                            <h4>Kualitas Produk</h4>
                            <p>Produk dipilih untuk menunjang kebutuhan usaha dengan standar yang baik.</p>
                        </div>
                        <div class="feature-item">
                            <h4>Pelayanan Profesional</h4>
                            <p>Tim siap membantu informasi dan kebutuhan pemesanan dengan responsif.</p>
                        </div>
                        <div class="feature-item">
                            <h4>Harga Efisien</h4>
                            <p>Penawaran harga dirancang agar kompetitif dan sesuai kebutuhan pelanggan.</p>
                        </div>
                        <div class="feature-item">
                            <h4>Komitmen Jangka Panjang</h4>
                            <p>Kami berorientasi pada kerja sama usaha yang profesional dan terpercaya.</p>
                        </div>
                    </div>
                </div>

                <div class="contact-box">
                    <h3>Hubungi Kami</h3>
                    <p>
                        Untuk informasi stok, konsultasi kebutuhan barang, dan pemesanan, Anda dapat menghubungi CV. Syavir Jaya Utama melalui kontak berikut.
                    </p>

                    <ul class="contact-list">
                        <li><strong>Perusahaan:</strong> CV. Syavir Jaya Utama</li>
                        <li><strong>Alamat:</strong> Silakan isi alamat perusahaan</li>
                        <li><strong>Telepon:</strong> Silakan isi nomor telepon</li>
                        <li><strong>Email:</strong> Silakan isi email perusahaan</li>
                        <li><strong>Jam Operasional:</strong> Senin - Sabtu, 08.00 - 17.00</li>
                    </ul>

                    <div class="mini-cta">
                        <p>
                            Untuk melihat penjelasan perusahaan secara lebih lengkap, silakan buka halaman tentang kami.
                        </p>
                        <a href="{{ route('tentangkami') }}" class="btn btn-primary">Buka Tentang Kami</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer-content">
            <div>© 2025 CV. Syavir Jaya Utama. Seluruh hak cipta dilindungi.</div>
            <div>Penyedia IBC, Jurigen, dan Pallet untuk kebutuhan distribusi dan logistik.</div>
        </div>
    </footer>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');

        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('show');
        });

        const reveals = document.querySelectorAll('.reveal');

        function revealOnScroll() {
            reveals.forEach((el) => {
                const windowHeight = window.innerHeight;
                const elementTop = el.getBoundingClientRect().top;
                const elementVisible = 100;

                if (elementTop < windowHeight - elementVisible) {
                    el.classList.add('active');
                }
            });
        }

        window.addEventListener('scroll', revealOnScroll);
        revealOnScroll();
    </script>

</body>
</html>