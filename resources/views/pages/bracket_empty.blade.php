<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bagan Turnamen - {{ $season->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #141416;
            color: #f4f4f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card {
            background-color: #1e1e24;
            border: 1px solid #3f3f46;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }
        .icon-box {
            color: #ff7a00;
            font-size: 3rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-box">
            <i class="bi bi-diagram-3"></i>
        </div>
        <h4 class="fw-bold mb-2">Bagan Belum Dirilis</h4>
        <p class="text-secondary mb-0">
            Bagan turnamen untuk <strong>{{ $season->name }}</strong> belum dibuat atau belum dirilis oleh admin Yomuda. Silakan kembali lagi nanti saat turnamen akan segera dimulai.
        </p>
    </div>
</body>
</html>
