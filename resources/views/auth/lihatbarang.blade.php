<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lihat Barang - CV. Syavir Jaya Utama</title>
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
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at top right, rgba(56, 189, 248, 0.18), transparent 24%),
                radial-gradient(circle at bottom left, rgba(37, 99, 235, 0.14), transparent 26%),
                linear-gradient(to right, rgba(37, 99, 235, 0.04) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(37, 99, 235, 0.04) 1px, transparent 1px);
            background-size: auto, auto, 38px 38px, 38px 38px;
            z-index: -1;
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
            gap: 20px;
        }

        .brand {
            font-size: 1.4rem;
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
            gap: 16px;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: #334155;
            font-weight: 600;
            font-size: 0.95rem;
            transition: 0.3s ease;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: #2563eb;
        }

        .nav-btn {
            padding: 10px 18px;
            border-radius: 999px;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: #ffffff !important;
            box-shadow: 0 10px 24px rgba(37, 99, 235, 0.20);
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.26);
        }

        .hero {
            min-height: 78vh;
            display: flex;
            align-items: center;
            padding-top: 110px;
            padding-bottom: 60px;
        }

        .hero-wrapper {
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 34px;
            align-items: center;
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
            font-size: 3.15rem;
            line-height: 1.15;
            color: #0f172a;
            margin-bottom: 18px;
            font-weight: 800;
        }

        .hero h1 span {
            color: #2563eb;
        }

        .hero p {
            color: #475569;
            font-size: 1.03rem;
            line-height: 1.85;
            margin-bottom: 28px;
            max-width: 650px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            border-radius: 14px;
            font-size: 0.98rem;
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
            background: #ffffff;
            border: 1px solid #dbeafe;
            color: #0f172a;
        }

        .btn-outline:hover {
            border-color: #60a5fa;
            color: #2563eb;
            transform: translateY(-3px);
        }

        .hero-visual {
            position: relative;
            min-height: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .visual-card {
            width: 100%;
            max-width: 430px;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 30px;
            padding: 24px;
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.10);
            backdrop-filter: blur(14px);
            animation: floatCard 4s ease-in-out infinite;
        }

        .main-square {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #dbeafe, #eff6ff 50%, #e0f2fe);
            border: 1px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pallet-illustration {
            position: relative;
            width: 76%;
            height: 54%;
        }

        .pallet-top {
            position: absolute;
            top: 0;
            left: 8%;
            width: 84%;
            height: 26%;
            background: linear-gradient(180deg, #b45309, #92400e);
            border-radius: 8px;
            box-shadow: 0 10px 16px rgba(120, 53, 15, 0.18);
        }

        .pallet-strip {
            position: absolute;
            top: 4%;
            width: 21%;
            height: 18%;
            background: rgba(255,255,255,0.16);
            border-radius: 6px;
        }

        .strip-1 { left: 12%; }
        .strip-2 { left: 39%; }
        .strip-3 { left: 66%; }

        .pallet-leg {
            position: absolute;
            bottom: 0;
            width: 16%;
            height: 48%;
            background: linear-gradient(180deg, #92400e, #78350f);
            border-radius: 8px;
        }

        .leg-1 { left: 14%; }
        .leg-2 { left: 42%; }
        .leg-3 { left: 70%; }

        .visual-caption {
            margin-top: 18px;
            text-align: center;
        }

        .visual-caption h3 {
            font-size: 1.25rem;
            color: #0f172a;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .visual-caption p {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .section {
            padding: 80px 0;
        }

        .section-title {
            text-align: center;
            font-size: 2.15rem;
            color: #0f172a;
            margin-bottom: 14px;
            font-weight: 800;
        }

        .section-subtitle {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 48px;
            color: #64748b;
            line-height: 1.85;
        }

        .showcase {
            display: grid;
            gap: 28px;
        }

        .showcase-item {
            display: grid;
            grid-template-columns: 0.95fr 1.05fr;
            gap: 30px;
            align-items: center;
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            padding: 28px;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
            transition: 0.35s ease;
        }

        .showcase-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 22px 46px rgba(15, 23, 42, 0.10);
        }

        .showcase-item.reverse {
            grid-template-columns: 1.05fr 0.95fr;
        }

        .showcase-item.reverse .showcase-media {
            order: 2;
        }

        .showcase-item.reverse .showcase-text {
            order: 1;
        }

        .showcase-media {
            width: 100%;
        }

        .product-square {
            width: 100%;
            aspect-ratio: 1 / 1;
            border-radius: 24px;
            border: 1px solid #bfdbfe;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            position: relative;
            overflow: hidden;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-square::before {
            content: "";
            position: absolute;
            inset: 18px;
            border-radius: 18px;
            border: 1px dashed rgba(37, 99, 235, 0.18);
        }

        .badge-label {
            position: absolute;
            top: 18px;
            left: 18px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.82);
            color: #2563eb;
            font-size: 0.82rem;
            font-weight: 700;
            border: 1px solid #dbeafe;
            z-index: 2;
        }

        .pallet-box {
            position: relative;
            width: 74%;
            height: 52%;
            animation: floatMini 3.5s ease-in-out infinite;
        }

        .pallet-box .top {
            position: absolute;
            top: 0;
            left: 9%;
            width: 82%;
            height: 28%;
            border-radius: 8px;
            background: linear-gradient(180deg, #b45309, #92400e);
            box-shadow: 0 12px 20px rgba(120, 53, 15, 0.16);
        }

        .pallet-box .slat {
            position: absolute;
            top: 4%;
            width: 20%;
            height: 18%;
            background: rgba(255,255,255,0.18);
            border-radius: 6px;
        }

        .pallet-box .slat.s1 { left: 14%; }
        .pallet-box .slat.s2 { left: 40%; }
        .pallet-box .slat.s3 { left: 66%; }

        .pallet-box .leg {
            position: absolute;
            bottom: 0;
            width: 16%;
            height: 48%;
            border-radius: 8px;
            background: linear-gradient(180deg, #92400e, #78350f);
        }

        .pallet-box .l1 { left: 14%; }
        .pallet-box .l2 { left: 42%; }
        .pallet-box .l3 { left: 70%; }

        .showcase-text h3 {
            font-size: 1.8rem;
            color: #0f172a;
            margin-bottom: 12px;
            font-weight: 800;
        }

        .showcase-text p {
            color: #475569;
            line-height: 1.9;
            margin-bottom: 16px;
        }

        .info-list {
            list-style: none;
            display: grid;
            gap: 10px;
            margin-bottom: 18px;
        }

        .info-list li {
            position: relative;
            padding-left: 18px;
            color: #64748b;
            line-height: 1.8;
        }

        .info-list li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 11px;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #2563eb;
        }

        .small-tag {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 999px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            font-size: 0.87rem;
            font-weight: 700;
        }

        .cta-section {
            padding: 90px 0;
        }

        .cta-box {
            background: linear-gradient(135deg, #eff6ff, #f0f9ff);
            border: 1px solid #dbeafe;
            border-radius: 30px;
            padding: 38px 32px;
            text-align: center;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .cta-box h2 {
            font-size: 2rem;
            color: #0f172a;
            margin-bottom: 14px;
            font-weight: 800;
        }

        .cta-box p {
            max-width: 760px;
            margin: 0 auto 24px;
            color: #475569;
            line-height: 1.9;
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

        @keyframes floatCard {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
            100% { transform: translateY(0px); }
        }

        @keyframes floatMini {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
            100% { transform: translateY(0px); }
        }

        @media (max-width: 992px) {
            .hero-wrapper,
            .showcase-item,
            .showcase-item.reverse {
                grid-template-columns: 1fr;
            }

            .showcase-item.reverse .showcase-media,
            .showcase-item.reverse .showcase-text {
                order: unset;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero {
                min-height: auto;
            }
        }

        @media (max-width: 768px) {
            .nav-wrapper {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
                justify-content: flex-start;
            }

            .hero {
                padding-top: 150px;
            }

            .hero h1 {
                font-size: 2.05rem;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .showcase-item {
                padding: 22px;
            }

            .cta-box {
                padding: 28px 22px;
            }
        }

        @media (max-width: 480px) {
            .brand {
                font-size: 1.08rem;
            }

            .nav-links a {
                font-size: 0.88rem;
            }

            .nav-btn {
                padding: 9px 14px;
            }

            .hero h1 {
                font-size: 1.8rem;
            }

            .btn {
                width: 100%;
            }

            .hero-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container nav-wrapper">
            <a href="{{ route('dashboard.awal') }}" class="brand">CV. <span>Syavir Jaya Utama</span></a>
            <div class="nav-links">
                <a href="{{ route('lihatbarang') }}" class="active">Lihat Barang</a>
                <a href="{{ route('tentangkami') }}">Tentang Kami</a>
                <a href="{{ route('login') }}" class="nav-btn">Login</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container hero-wrapper">
            <div class="hero-content reveal active">
                <div class="hero-badge">Katalog Produk Pallet</div>
                <h1>Pilih pallet yang tepat untuk <span>kebutuhan distribusi dan gudang Anda</span></h1>
                <p>
                    Halaman ini dirancang agar pelanggan dapat melihat gambaran produk secara cepat namun tetap menarik. Kami menampilkan tiga contoh pallet dengan visual berbentuk persegi 1:1, deskripsi yang jelas, serta tata letak yang nyaman dilihat agar calon pembeli lebih mudah memahami manfaat setiap produk.
                </p>
                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn btn-primary">Pesan Sekarang</a>
                    <a href="#produk" class="btn btn-outline">Lihat Detail Produk</a>
                </div>
            </div>

            <div class="hero-visual reveal active">
                <div class="visual-card">
                    <div class="main-square">
                        <div class="pallet-illustration">
                            <div class="pallet-top"></div>
                            <div class="pallet-strip strip-1"></div>
                            <div class="pallet-strip strip-2"></div>
                            <div class="pallet-strip strip-3"></div>
                            <div class="pallet-leg leg-1"></div>
                            <div class="pallet-leg leg-2"></div>
                            <div class="pallet-leg leg-3"></div>
                        </div>
                    </div>
                    <div class="visual-caption">
                        <h3>Contoh Tampilan Produk</h3>
                        <p>Visual ini dapat diganti dengan foto pallet asli kapan saja tanpa mengubah struktur desain halaman.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="produk">
        <div class="container">
            <h2 class="section-title reveal">Pilihan Produk Pallet</h2>
            <p class="section-subtitle reveal">
                Tiga contoh berikut disusun untuk memberi kesan visual yang kuat sekaligus membantu pelanggan memahami karakter produk. Anda dapat mengganti nama, spesifikasi, dan gambar nanti sesuai stok yang sebenarnya.
            </p>

            <div class="showcase">
                <div class="showcase-item reveal">
                    <div class="showcase-media">
                        <div class="product-square">
                            <div class="badge-label">Pallet 01</div>
                            <div class="pallet-box">
                                <div class="top"></div>
                                <div class="slat s1"></div>
                                <div class="slat s2"></div>
                                <div class="slat s3"></div>
                                <div class="leg l1"></div>
                                <div class="leg l2"></div>
                                <div class="leg l3"></div>
                            </div>
                        </div>
                    </div>
                    <div class="showcase-text">
                        <h3>Pallet Standar Distribusi</h3>
                        <p>
                            Produk ini cocok untuk kebutuhan distribusi umum dan aktivitas pergudangan harian. Desainnya dibuat sederhana namun tetap memberikan kesan kuat, rapi, dan fungsional sehingga sangat sesuai untuk pelanggan yang membutuhkan solusi praktis dengan tampilan profesional.
                        </p>
                        <ul class="info-list">
                            <li>Cocok untuk pengangkutan dan penyimpanan barang secara rutin.</li>
                            <li>Membantu penataan stok agar lebih rapi dan mudah dipindahkan.</li>
                            <li>Ideal untuk kebutuhan usaha skala kecil hingga menengah.</li>
                        </ul>
                        <span class="small-tag">Praktis dan Efisien</span>
                    </div>
                </div>

                <div class="showcase-item reverse reveal">
                    <div class="showcase-media">
                        <div class="product-square">
                            <div class="badge-label">Pallet 02</div>
                            <div class="pallet-box">
                                <div class="top"></div>
                                <div class="slat s1"></div>
                                <div class="slat s2"></div>
                                <div class="slat s3"></div>
                                <div class="leg l1"></div>
                                <div class="leg l2"></div>
                                <div class="leg l3"></div>
                            </div>
                        </div>
                    </div>
                    <div class="showcase-text">
                        <h3>Pallet Gudang Serbaguna</h3>
                        <p>
                            Model ini ditujukan bagi pelanggan yang mengutamakan fleksibilitas penggunaan. Dengan tampilan yang bersih dan susunan layout yang lebih modern, produk terlihat lebih meyakinkan dan memberi kesan bahwa barang tersimpan secara aman dalam sistem gudang yang tertata.
                        </p>
                        <ul class="info-list">
                            <li>Sesuai untuk penyimpanan barang dalam area gudang dan logistik.</li>
                            <li>Menunjang alur kerja yang lebih tertib dan efisien.</li>
                            <li>Memberikan nilai visual yang baik saat ditampilkan dalam katalog.</li>
                        </ul>
                        <span class="small-tag">Rapi dan Fleksibel</span>
                    </div>
                </div>

                <div class="showcase-item reveal">
                    <div class="showcase-media">
                        <div class="product-square">
                            <div class="badge-label">Pallet 03</div>
                            <div class="pallet-box">
                                <div class="top"></div>
                                <div class="slat s1"></div>
                                <div class="slat s2"></div>
                                <div class="slat s3"></div>
                                <div class="leg l1"></div>
                                <div class="leg l2"></div>
                                <div class="leg l3"></div>
                            </div>
                        </div>
                    </div>
                    <div class="showcase-text">
                        <h3>Pallet Kebutuhan Industri</h3>
                        <p>
                            Produk ketiga ditampilkan dengan pendekatan yang lebih kuat agar menarik perhatian pelanggan yang membutuhkan pallet untuk kegiatan industri dan distribusi yang lebih intensif. Susunan visual dan teks dibuat agar produk terlihat terpercaya, siap pakai, dan layak dipertimbangkan untuk pemesanan.
                        </p>
                        <ul class="info-list">
                            <li>Memberikan kesan kokoh untuk kebutuhan distribusi yang lebih serius.</li>
                            <li>Baik digunakan sebagai pilihan utama pada penawaran produk.</li>
                            <li>Memperkuat citra profesional perusahaan di mata pelanggan.</li>
                        </ul>
                        <span class="small-tag">Kokoh dan Profesional</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section reveal">
        <div class="container">
            <div class="cta-box">
                <h2>Tertarik dengan produk kami?</h2>
                <p>
                    Jika Anda ingin melanjutkan proses pemesanan atau memperoleh informasi lebih lanjut mengenai produk pallet yang tersedia, silakan masuk ke sistem terlebih dahulu. Halaman ini dibuat sebagai pengantar visual, sedangkan proses berikutnya dapat diarahkan langsung ke halaman login agar pelanggan segera bertransaksi.
                </p>
                <a href="{{ route('login') }}" class="btn btn-primary">Login untuk Memesan</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container footer-content">
            <div>© 2025 CV. Syavir Jaya Utama. Seluruh hak cipta dilindungi.</div>
            <div>Katalog produk pallet untuk kebutuhan distribusi, gudang, dan logistik.</div>
        </div>
    </footer>

    <script>
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