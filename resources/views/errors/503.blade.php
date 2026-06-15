<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lobby Sedang Maintenance | Yomuda Championship</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800;900&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0d0f12;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 193, 7, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 193, 7, 0.02) 0%, transparent 40%);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Background grid effects */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: linear-gradient(rgba(255, 255, 255, 0.005) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(255, 255, 255, 0.005) 1px, transparent 1px);
            background-size: 40px 40px;
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            max-width: 600px;
            padding: 30px;
            text-align: center;
        }

        .glow-icon {
            font-size: 6rem;
            color: #ffc107;
            display: inline-block;
            animation: pulse-glow 2.5s infinite ease-in-out;
            margin-bottom: 25px;
            filter: drop-shadow(0 0 20px rgba(255, 193, 7, 0.4));
        }

        @keyframes pulse-glow {
            0%, 100% {
                transform: scale(1);
                filter: drop-shadow(0 0 15px rgba(255, 193, 7, 0.3));
                opacity: 0.9;
            }
            50% {
                transform: scale(1.05);
                filter: drop-shadow(0 0 30px rgba(255, 193, 7, 0.6));
                opacity: 1;
            }
        }

        .badge-maintenance {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #ffc107;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 20px;
        }

        h1 {
            font-size: 2.8rem;
            font-weight: 900;
            letter-spacing: -1px;
            line-height: 1.1;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        h1 span {
            color: #ffc107;
            text-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
        }

        p {
            font-size: 1.1rem;
            color: #a0aec0;
            line-height: 1.6;
            margin-bottom: 40px;
            font-weight: 400;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 16px;
            margin-bottom: 35px;
            backdrop-filter: blur(10px);
        }

        .info-item {
            font-size: 0.95rem;
            color: #cbd5e0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .info-item i {
            color: #ffc107;
            font-size: 1.1rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            color: #ffffff;
            font-size: 1.2rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .social-btn:hover {
            background: #ffc107;
            color: #0d0f12;
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 193, 7, 0.2);
            border-color: #ffc107;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Maintenance Icon -->
        <div class="glow-icon">
            <i class="bi bi-tools"></i>
        </div>

        <!-- Badge -->
        <div class="badge-maintenance">
            System Update
        </div>

        <!-- Heading -->
        <h1>Lobby Sedang <span>Maintenance</span></h1>
        
        <!-- Description -->
        <p>Kami sedang mempersiapkan turnamen yang lebih seru & stabil untuk kamu! Lobby pendaftaran sedang ditingkatkan sementara waktu. Tunggu aba-aba admin ya, Squad!</p>

        <!-- Info Card -->
        <div class="info-card">
            <div class="info-item">
                <i class="bi bi-clock-history"></i>
                <span>Estimasi selesai: Segera hadir kembali hari ini.</span>
            </div>
        </div>

        <!-- Social Media / Contact -->
        <div class="social-links">
            <a href="https://www.instagram.com/yomuda.championship/" target="_blank" class="social-btn" title="Instagram">
                <i class="bi bi-instagram"></i>
            </a>
            <a href="https://www.tiktok.com/@yomudachampionship" target="_blank" class="social-btn" title="TikTok">
                <i class="bi bi-tiktok"></i>
            </a>
            <a href="https://www.youtube.com/@ymdchamps/streams" target="_blank" class="social-btn" title="YouTube">
                <i class="bi bi-youtube"></i>
            </a>
        </div>
    </div>

</body>
</html>
