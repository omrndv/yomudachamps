<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bagan Turnamen - {{ $season->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0c0c0e;
            color: #f4f4f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(255, 122, 0, 0.06) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 122, 0, 0.04) 0%, transparent 50%);
            padding: 24px;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .empty-card {
            background-color: rgba(30, 30, 36, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(63, 63, 70, 0.5);
            border-radius: 20px;
            max-width: 460px;
            width: 100%;
            padding: 44px 32px;
            text-align: center;
            box-shadow: 
                0 20px 50px rgba(0, 0, 0, 0.6),
                0 0 0 1px rgba(255, 122, 0, 0.05);
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .icon-box {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(255, 122, 0, 0.15), rgba(255, 122, 0, 0.04));
            border: 1px solid rgba(255, 122, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            font-size: 1.8rem;
            color: #ff7a00;
        }

        .empty-title {
            font-weight: 800;
            font-size: 1.2rem;
            color: #ffffff;
            margin-bottom: 10px;
            letter-spacing: -0.3px;
        }

        .empty-desc {
            color: #a1a1aa;
            font-size: 0.82rem;
            line-height: 1.7;
            margin-bottom: 24px;
        }

        .empty-desc strong {
            color: #ff7a00;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #ff7a00;
            border: 1px solid rgba(255, 122, 0, 0.3);
            background-color: rgba(255, 122, 0, 0.04);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background-color: rgba(255, 122, 0, 0.1);
            border-color: rgba(255, 122, 0, 0.5);
            color: #ff7a00;
            transform: translateY(-1px);
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 0.6rem;
            font-weight: 700;
            color: #fbbf24;
            background-color: rgba(251, 191, 36, 0.08);
            border: 1px solid rgba(251, 191, 36, 0.15);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }

        .status-chip .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #fbbf24;
            animation: pulse 2s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div class="empty-card">
        <div class="icon-box">
            <i class="bi bi-diagram-3"></i>
        </div>
        <div class="status-chip">
            <span class="dot"></span> Menunggu Rilis
        </div>
        <h4 class="empty-title">Bagan Belum Dirilis</h4>
        <p class="empty-desc">
            Bagan turnamen untuk <strong>{{ $season->name }}</strong> belum dibuat atau belum dirilis oleh admin Yomuda. Silakan kembali lagi nanti saat turnamen akan segera dimulai.
        </p>
        <a href="{{ url()->previous() }}" class="back-btn">
            <i class="bi bi-chevron-left"></i> Kembali
        </a>
    </div>
</body>
</html>
