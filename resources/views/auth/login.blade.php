<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CV. Syavir Jaya Utama</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #6c7bff, #8a4de8);
            color: #fff;
        }

        .navbar {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 1200px;
            padding: 14px 24px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .logo-text {
            font-size: 22px;
            font-weight: 700;
            color: white;
        }

        .nav-right a {
            text-decoration: none;
            color: white;
            font-weight: 600;
            padding: 10px 18px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 10px;
            transition: 0.3s;
        }

        .nav-right a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 120px 20px 40px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: rgba(255, 255, 255, 0.14);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .auth-card h2 {
            text-align: center;
            margin-bottom: 10px;
            font-size: 30px;
        }

        .auth-card p.subtitle {
            text-align: center;
            margin-bottom: 30px;
            color: #e8e8ff;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: none;
            border-radius: 12px;
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 14px;
        }

        .form-control::placeholder {
            color: #e5e5ff;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(90deg, #5e7cff, #3fd0ff);
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            opacity: 0.95;
        }

        .text-link {
            text-align: center;
            margin-top: 22px;
            font-size: 14px;
            color: #f3f3ff;
        }

        .text-link a {
            color: #9ffcff;
            text-decoration: none;
            font-weight: 700;
        }

        .alert-error {
            background: rgba(255, 77, 77, 0.15);
            border: 1px solid rgba(255, 77, 77, 0.4);
            color: #fff;
            padding: 12px 14px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .error-text {
            color: #ffe3e3;
            font-size: 13px;
            margin-top: 6px;
        }

        @media (max-width: 768px) {
            .navbar {
                width: 94%;
                padding: 12px 16px;
            }

            .logo-text {
                font-size: 17px;
            }

            .nav-right a {
                padding: 8px 14px;
                font-size: 14px;
            }

            .auth-card {
                padding: 30px 22px;
            }

            .auth-card h2 {
                font-size: 26px;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                top: 12px;
                border-radius: 14px;
            }

            .logo-text {
                font-size: 15px;
                max-width: 180px;
                line-height: 1.4;
            }

            .nav-right a {
                font-size: 13px;
                padding: 8px 12px;
            }

            .auth-card {
                border-radius: 20px;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo-text">CV. Syavir Jaya Utama</div>
        <div class="nav-right">
            <a href="{{ route('login') }}">Login</a>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="auth-card">
            <h2>Welcome Back</h2>
            <p class="subtitle">Silakan login ke akun Anda</p>

            @if ($errors->has('loginError'))
                <div class="alert-error">
                    {{ $errors->first('loginError') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" value="{{ old('username') }}">
                    @error('username')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password">
                    @error('password')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">Login</button>
            </form>

            <div class="text-link">
                Belum punya akun? <a href="{{ route('register') }}">Sign Up</a>
            </div>
        </div>
    </div>

</body>
</html>