<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - CV. Syavir Jaya Utama</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(180deg, #eef7ff 0%, #ffffff 100%);
            color: #1e293b;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .navbar {
            background: rgba(255,255,255,0.90);
            backdrop-filter: blur(10px);
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 0;
        }

        .brand {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
        }

        .brand span {
            color: #2563eb;
        }

        .nav-links {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .nav-links a {
            color: #334155;
            font-weight: 600;
        }

        .login-btn {
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: #ffffff !important;
            padding: 10px 20px;
            border-radius: 999px;
        }

        .page-header {
            padding: 80px 0 40px;
            text-align: center;
        }

        .page-header h1 {
            font-size: 2.8rem;
            color: #0f172a;
            margin-bottom: 14px;
        }

        .page-header p {
            max-width: 760px;
            margin: 0 auto;
            color: #475569;
            line-height: 1.8;
        }

        .content-section {
            padding: 20px 0 80px;
        }

        .content-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 34px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
            margin-bottom: 24px;
        }

        .content-box h2 {
            font-size: 1.5rem;
            color: #0f172a;
            margin-bottom: 14px;
        }

        .content-box p {
            color: #475569;
            line-height: 1.9;
            margin-bottom: 14px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .mini-box {
            background: #f8fbff;
            border: 1px solid #dbeafe;
            border-radius: 20px;
            padding: 22px;
        }

        .mini-box h3 {
            color: #0f172a;
            margin-bottom: 10px;
        }

        .mini-box p {
            color: #64748b;
            line-height: 1.8;
        }

        .footer {
            background: #eaf4ff;
            padding: 26px 0;
            border-top: 1px solid #dbeafe;
        }

        .footer p {
            text-align: center;
            color: #475569;
        }

        @media (max-width: 768px) {
            .nav-wrapper {
                flex-direction: column;
                gap: 14px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .content-box {
                padding: 24px;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container nav-wrapper">
            <a href="{{ route('dashboard.awal') }}" class="brand">CV. <span>Syavir Jaya Utama</span></a>
            <div class="nav-links">
                <a href="{{ route('lihatbarang') }}">Lihat Barang</a>
                <a href="{{ route('tentangkami') }}">Tentang Kami</a>
                <a href="{{ route('login') }}" class="login-btn">Login</a>
            </div>
        </div>
    </nav>

    <section class="page-header">
        <div class="container">
            <h1>Tentang CV. Syavir Jaya Utama</h1>
            <p>
                CV. Syavir Jaya Utama merupakan perusahaan yang berfokus pada penjualan IBC, jurigen, dan pallet untuk mendukung berbagai kebutuhan distribusi, pergudangan, dan penyimpanan barang. Kami berkomitmen untuk memberikan produk yang layak, layanan yang profesional, dan proses pemesanan yang efisien bagi setiap pelanggan.
            </p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="content-box">
                <h2>Profil Perusahaan</h2>
                <p>
                    Dalam menjalankan kegiatan usaha, CV. Syavir Jaya Utama menempatkan kualitas layanan sebagai salah satu prioritas utama. Kami memahami bahwa kebutuhan setiap pelanggan dapat berbeda, sehingga kami berusaha memberikan informasi yang jelas, respons yang cepat, dan penawaran produk yang sesuai dengan kebutuhan operasional pelanggan.
                </p>
                <p>
                    Produk yang kami sediakan meliputi IBC untuk penyimpanan cairan dalam kapasitas besar, jurigen untuk kebutuhan distribusi dan penyimpanan yang lebih praktis, serta pallet untuk menunjang aktivitas logistik dan pergudangan. Dengan menyediakan produk-produk tersebut, kami berupaya membantu pelanggan dalam meningkatkan efisiensi dan keteraturan proses kerja mereka.
                </p>
                <p>
                    Ke depan, kami ingin terus berkembang sebagai mitra usaha yang dapat dipercaya, dengan menjaga kualitas produk, memperbaiki layanan, dan membangun hubungan kerja sama jangka panjang yang saling menguntungkan.
                </p>
            </div>

            <div class="grid">
                <div class="mini-box">
                    <h3>Visi</h3>
                    <p>
                        Menjadi perusahaan yang terpercaya dalam penyediaan IBC, jurigen, dan pallet untuk mendukung kebutuhan distribusi dan logistik pelanggan.
                    </p>
                </div>

                <div class="mini-box">
                    <h3>Misi</h3>
                    <p>
                        Menyediakan produk yang berkualitas, memberikan pelayanan yang profesional, serta membangun hubungan kerja sama yang berorientasi pada kepuasan pelanggan.
                    </p>
                </div>

                <div class="mini-box">
                    <h3>Komitmen</h3>
                    <p>
                        Kami berkomitmen untuk menjaga mutu layanan, memberikan respons yang cepat, dan menghadirkan solusi produk yang sesuai dengan kebutuhan usaha pelanggan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>© 2025 CV. Syavir Jaya Utama. Seluruh hak cipta dilindungi.</p>
        </div>
    </footer>

</body>
</html>