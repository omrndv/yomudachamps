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
            height: 100vh;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            margin: 0;
            padding: 15px;
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
        <div class="alert alert-danger shadow-sm border-0 mb-3 rounded-3 py-2.5" style="font-size: 0.8rem;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Username</label>
                <input type="text" name="username" class="form-control shadow-none" required autocomplete="username">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Password</label>
                <input type="password" name="password" class="form-control shadow-none" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-login w-100 shadow-sm">MASUK SEKARANG</button>
        </form>

        <div class="text-center mt-4">
            <a href="/" class="text-decoration-none text-secondary small hover-text-warning" style="font-size: 0.8rem; transition: color 0.2s ease;">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

</body>

</html>