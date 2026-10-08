<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <title>Login Admin - Yomuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            margin: 0;
            padding: 20px 15px;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #ffffff;
            border-radius: 12px;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
        }

        .btn-login {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            border: none;
            color: #ffffff;
            font-weight: 700;
            padding: 14px;
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3), 0 4px 6px -2px rgba(245, 158, 11, 0.1);
        }

        .form-control {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #f59e0b;
            box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.15);
        }

        .btn-google-login {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #1e293b;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .btn-google-login:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .btn-google-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon mb-3">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <h4 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.5px;">YOMUDA <span class="text-warning">ADMIN</span></h4>
            <p class="text-secondary small">Silakan masuk untuk mengelola portal turnamen</p>
        </div>

        @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0 mb-3 rounded-3 py-2.5 d-flex align-items-center" style="font-size: 0.8rem;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
            <div>{{ session('error') }}</div>
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning shadow-sm border-0 mb-3 rounded-3 py-2.5 d-flex align-items-center text-dark" style="font-size: 0.8rem; background-color: #fef3c7;">
            <i class="bi bi-shield-exclamation me-2 fs-6 text-warning-emphasis"></i>
            <div>{{ session('warning') }}</div>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info shadow-sm border-0 mb-3 rounded-3 py-2.5 d-flex align-items-center text-dark" style="font-size: 0.8rem; background-color: #e0f2fe;">
            <i class="bi bi-info-circle-fill me-2 fs-6 text-primary"></i>
            <div>{{ session('info') }}</div>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success shadow-sm border-0 mb-3 rounded-3 py-2.5 d-flex align-items-center text-dark" style="font-size: 0.8rem; background-color: #dcfce7;">
            <i class="bi bi-check-circle-fill me-2 fs-6 text-success"></i>
            <div>{{ session('success') }}</div>
        </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Username</label>
                <input type="text" name="username" class="form-control shadow-none" required autocomplete="username" placeholder="Masukkan username">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Password</label>
                <div class="position-relative">
                    <input type="password" name="password" id="passwordInput" class="form-control shadow-none pe-5" required autocomplete="current-password" placeholder="Masukkan password">
                    <button type="button" id="togglePasswordBtn" class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-secondary text-decoration-none border-0 pe-3" style="cursor: pointer; z-index: 5;" onclick="togglePasswordVisibility()" aria-label="Lihat Password">
                        <i class="bi bi-eye" id="togglePasswordIcon" style="font-size: 1.15rem;"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <span>MASUK SEKARANG</span>
                <i class="bi bi-arrow-right-short fs-5"></i>
            </button>
        </form>

        <div class="d-flex align-items-center my-4">
            <hr class="flex-grow-1 my-0 text-muted opacity-25">
            <span class="px-3 text-muted small user-select-none fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">ATAU</span>
            <hr class="flex-grow-1 my-0 text-muted opacity-25">
        </div>

        {{-- Tombol Masuk dengan Google --}}
        <a href="{{ route('admin.login.google') }}" class="btn-google-login w-100">
            <svg width="20" height="20" viewBox="0 0 24 24" style="flex-shrink: 0;">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Masuk dengan Google</span>
        </a>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('togglePasswordIcon');
            if (!passwordInput || !toggleIcon) return;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'bi bi-eye-slash text-warning';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'bi bi-eye text-secondary';
            }
        }
    </script>
</body>

</html>