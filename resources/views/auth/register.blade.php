<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CV. Syavir Jaya Utama</title>
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
            min-height: 100vh;
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

        .nav-links a:hover {
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

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 0 50px;
        }

        .register-layout {
            display: grid;
            grid-template-columns: 1.02fr 0.98fr;
            gap: 34px;
            align-items: center;
            width: 100%;
        }

        .register-info {
            animation: fadeUp 0.9s ease;
        }

        .register-badge {
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

        .register-info h1 {
            font-size: 3rem;
            line-height: 1.15;
            color: #0f172a;
            margin-bottom: 18px;
            font-weight: 800;
        }

        .register-info h1 span {
            color: #2563eb;
        }

        .register-info p {
            color: #475569;
            font-size: 1.03rem;
            line-height: 1.85;
            margin-bottom: 26px;
            max-width: 620px;
        }

        .feature-points {
            display: grid;
            gap: 14px;
            max-width: 560px;
        }

        .feature-item {
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 16px 18px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        }

        .feature-item strong {
            display: block;
            color: #0f172a;
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .feature-item span {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.86);
            border: 1px solid rgba(148, 163, 184, 0.22);
            border-radius: 28px;
            padding: 34px 30px;
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.10);
            backdrop-filter: blur(14px);
            animation: fadeUp 1s ease;
        }

        .auth-card h2 {
            text-align: center;
            color: #0f172a;
            font-size: 2rem;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .auth-card .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 26px;
            line-height: 1.7;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #0f172a;
            font-size: 0.94rem;
            font-weight: 700;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #dbeafe;
            background: #f8fbff;
            color: #0f172a;
            font-size: 0.95rem;
            outline: none;
            transition: 0.3s ease;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            border-color: #60a5fa;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.12);
        }

        .error-text {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 7px;
        }

        .btn-submit {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 14px 18px;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s ease;
            margin-top: 8px;
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.20);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 30px rgba(37, 99, 235, 0.26);
        }

        .text-link {
            text-align: center;
            margin-top: 22px;
            color: #64748b;
            font-size: 0.94rem;
        }

        .text-link a {
            color: #2563eb;
            font-weight: 700;
        }

        .text-link a:hover {
            text-decoration: underline;
        }

        .footer-note {
            text-align: center;
            margin-top: 18px;
            color: #94a3b8;
            font-size: 0.85rem;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 992px) {
            .register-layout {
                grid-template-columns: 1fr;
            }

            .register-info {
                text-align: center;
            }

            .register-info p {
                margin-left: auto;
                margin-right: auto;
            }

            .feature-points {
                margin: 0 auto;
            }

            .register-info h1 {
                font-size: 2.4rem;
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

            .page-wrapper {
                padding-top: 150px;
            }

            .auth-card {
                padding: 28px 22px;
            }

            .auth-card h2 {
                font-size: 1.7rem;
            }

            .register-info h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .brand {
                font-size: 1.1rem;
            }

            .nav-links {
                gap: 10px;
            }

            .nav-links a {
                font-size: 0.88rem;
            }

            .nav-btn {
                padding: 9px 14px;
            }

            .register-info h1 {
                font-size: 1.75rem;
            }

            .register-info p {
                font-size: 0.95rem;
            }

            .auth-card {
                border-radius: 22px;
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
                <a href="{{ route('login') }}" class="nav-btn">Login</a>
            </div>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="container">
            <div class="register-layout">
                <div class="register-info">
                    <div class="register-badge">Pendaftaran Akun Baru</div>
                    <h1>Buat akun untuk mulai <span>mengakses sistem</span></h1>
                    <p>
                        Silakan daftar untuk menggunakan sistem CV. Syavir Jaya Utama. Setelah akun dibuat, Anda dapat melanjutkan proses login dan menggunakan layanan pemesanan produk seperti IBC, jurigen, dan pallet dengan tampilan yang konsisten dan profesional.
                    </p>

                    <div class="feature-points">
                        <div class="feature-item">
                            <strong>Pendaftaran mudah</strong>
                            <span>Form dibuat sederhana agar proses pembuatan akun berjalan cepat dan jelas.</span>
                        </div>
                        <div class="feature-item">
                            <strong>Tampilan selaras</strong>
                            <span>Desain register disesuaikan dengan dashboard dan halaman login agar tetap konsisten.</span>
                        </div>
                        <div class="feature-item">
                            <strong>Siap digunakan</strong>
                            <span>Setelah registrasi berhasil, akun dapat digunakan untuk masuk ke sistem dan melanjutkan aktivitas pengguna.</span>
                        </div>
                    </div>
                </div>

                <div class="auth-card">
                    <h2>Register</h2>
                    <p class="subtitle">Lengkapi data berikut untuk membuat akun baru</p>

                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input
                                type="text"
                                id="username"
                                name="username"
                                class="form-control"
                                placeholder="Masukkan username"
                                value="{{ old('username') }}"
                            >
                            @error('username')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="Masukkan email"
                                value="{{ old('email') }}"
                            >
                            @error('email')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Masukkan password"
                            >
                            @error('password')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Ulangi password"
                            >
                        </div>

                        <button type="submit" class="btn-submit">Daftar Sekarang</button>
                    </form>

                    <div class="text-link">
                        Sudah punya akun? <a href="{{ route('login') }}">Login di sini</a>
                    </div>

                    <div class="footer-note">
                        © 2025 CV. Syavir Jaya Utama
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>