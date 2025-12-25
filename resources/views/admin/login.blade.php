<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Yomuda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('/images/bg-yomuda.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            align-items: center;
            flex-direction: column;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 40px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .btn-login {
            background: #ffc107;
            border: none;
            color: #000;
            font-weight: 700;
            padding: 12px;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #e5ac00;
            transform: translateY(-2px);
        }

        .form-control {
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            background-color: #fcfcfc;
        }

        .form-control:focus {
            border-color: #ffc107;
            box-shadow: 0 0 0 0.25rem rgba(255, 193, 7, 0.1);
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1">YOMUDA <span style="color: #ffc107;">ADMIN</span></h4>
            <p class="text-muted small">Silakan masuk untuk mengelola turnamen</p>
        </div>

        @if(session('error'))
        <div class="alert alert-danger shadow-sm border-0 mb-3" style="font-size: 0.8rem;">
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-login w-100 shadow-sm">MASUK SEKARANG</button>
        </form>

        <div class="text-center mt-4">
            <a href="/" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left"></i> Kembali ke Beranda</a>
        </div>
    </div>

</body>

</html>