<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yomuda Championship - {{ $season->name }}</title>
    
    <!-- Google Fonts & Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-dark: #09090b;
            --bg-card: #18181b;
            --border-color: rgba(255, 255, 255, 0.08);
            --accent-orange: #ff7a00;
            --accent-glow: rgba(255, 122, 0, 0.15);
            --text-light: #f4f4f5;
            --text-dim: #a1a1aa;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 122, 0, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 122, 0, 0.05) 0%, transparent 40%);
            padding: 24px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .landing-container {
            max-width: 500px;
            width: 100%;
            text-align: center;
            animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.25rem;
            letter-spacing: -0.5px;
            color: #ffffff;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .brand-logo i {
            color: var(--accent-orange);
            filter: drop-shadow(0 0 8px var(--accent-orange));
        }

        .season-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 30px 24px;
            margin-bottom: 24px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
        }

        .season-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 122, 0, 0.3), transparent);
        }

        .season-poster {
            width: 80px;
            height: 80px;
            border-radius: 18px;
            object-fit: cover;
            border: 2px solid var(--border-color);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            margin-bottom: 16px;
        }

        .season-title {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            color: #ffffff;
        }

        .season-badge {
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 50px;
            background-color: rgba(255, 122, 0, 0.1);
            color: var(--accent-orange);
            border: 1px solid rgba(255, 122, 0, 0.2);
            display: inline-block;
            margin-bottom: 20px;
        }

        .menu-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .menu-item {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-align: left;
        }

        .menu-item:hover {
            background-color: rgba(255, 122, 0, 0.04);
            border-color: rgba(255, 122, 0, 0.3);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 122, 0, 0.05);
        }

        .menu-icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: var(--accent-orange);
            transition: background-color 0.2s ease;
        }

        .menu-item:hover .menu-icon-wrapper {
            background-color: var(--accent-orange);
            color: #000000;
        }

        .menu-content {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .menu-arrow {
            color: var(--text-dim);
            font-size: 0.9rem;
            transition: transform 0.2s ease;
        }

        .menu-item:hover .menu-arrow {
            transform: translateX(4px);
            color: #ffffff;
        }

        .badge-coming-soon {
            font-size: 0.58rem;
            font-weight: 800;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-dim);
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            letter-spacing: 0.3px;
        }

        .footer-text {
            font-size: 0.72rem;
            color: var(--text-dim);
            margin-top: 32px;
        }

        /* Modal Styles */
        .modal-content {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            color: var(--text-light);
        }

        .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        .timeline-table {
            width: 100%;
            border-collapse: collapse;
        }

        .timeline-table th {
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-dim);
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .timeline-table td {
            padding: 14px 12px;
            font-size: 0.82rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
        }

        .timeline-time {
            font-weight: 700;
            color: var(--accent-orange);
        }

        .btn-modal-close {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            font-weight: 600;
            font-size: 0.8rem;
            padding: 8px 20px;
            border-radius: 50px;
            transition: all 0.2s ease;
        }

        .btn-modal-close:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        .btn-accent {
            background-color: var(--accent-orange);
            color: #000000;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 8px 20px;
            border-radius: 50px;
            border: none;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-accent:hover {
            background-color: #ff912a;
            color: #000000;
            transform: translateY(-1px);
        }
        /* Floating Live Chat Widget CSS */
        .chat-widget-wrapper {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 1050;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .chat-toggle-btn {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background-color: var(--accent-orange);
            color: #000000;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            box-shadow: 0 10px 30px rgba(255, 122, 0, 0.3);
        }

        .chat-toggle-btn:hover {
            transform: scale(1.08) rotate(5deg);
            background-color: #ff912a;
        }

        .chat-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: #ef4444;
            color: #ffffff;
            font-size: 0.65rem;
            font-weight: 800;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #18181b;
        }

        .chat-box-container {
            position: absolute;
            bottom: 68px;
            right: 0;
            width: 320px;
            height: 400px;
            background-color: rgba(24, 24, 27, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform: scale(0.9) translateY(20px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            transform-origin: bottom right;
        }

        .chat-box-container.active {
            transform: scale(1) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        .chat-box-header {
            padding: 14px 16px;
            background-color: rgba(39, 39, 42, 0.6);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        .chat-header-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: #ffffff;
            line-height: 1.2;
        }

        .chat-header-subtitle {
            font-size: 0.68rem;
            color: var(--text-dim);
        }

        .chat-close-btn {
            background: none;
            border: none;
            color: var(--text-dim);
            font-size: 0.9rem;
            cursor: pointer;
            transition: color 0.15s ease;
        }

        .chat-close-btn:hover {
            color: #ffffff;
        }

        .chat-messages-body {
            flex-grow: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .chat-system-message {
            background-color: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 0.72rem;
            color: var(--text-dim);
            line-height: 1.4;
            text-align: center;
        }

        .chat-msg-bubble {
            max-width: 85%;
            padding: 8px 12px;
            border-radius: 14px;
            font-size: 0.78rem;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .chat-msg-bubble.user {
            background-color: rgba(255, 122, 0, 0.12);
            border: 1px solid rgba(255, 122, 0, 0.2);
            color: #ffffff;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .chat-msg-bubble.admin {
            background-color: #27272a;
            border: 1px solid var(--border-color);
            color: var(--text-light);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
        }

        .chat-input-wrapper {
            padding: 10px 12px;
            border-top: 1px solid var(--border-color);
            background-color: rgba(24, 24, 27, 0.8);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .chat-input-wrapper input {
            flex-grow: 1;
            background-color: rgba(255,255,255,0.03);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 8px 16px;
            color: #ffffff;
            font-size: 0.8rem;
            outline: none;
            transition: border-color 0.15s ease;
        }

        .chat-input-wrapper input:focus {
            border-color: rgba(255, 122, 0, 0.4);
        }

        .chat-input-wrapper button {
            background: none;
            border: none;
            color: var(--accent-orange);
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease;
        }

        .chat-input-wrapper button:hover {
            transform: scale(1.1);
        }

        @media (max-width: 576px) {
            .chat-widget-wrapper {
                bottom: 16px;
                right: 16px;
                left: 16px;
            }
            .chat-box-container {
                width: 100% !important;
                height: 380px;
                bottom: 60px;
            }
            .chat-input-wrapper input {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>

    <div class="landing-container">

        <!-- Season Card info -->
        <div class="season-card">
            <div class="season-poster d-flex align-items-center justify-content-center bg-dark text-warning mx-auto" style="font-size: 2.2rem; width: 80px; height: 80px; border-radius: 18px; border: 2px solid var(--border-color); box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); margin-bottom: 16px;">
                <i class="bi bi-trophy-fill"></i>
            </div>

            <h1 class="season-title">{{ $season->name }}</h1>
            <span class="season-badge">{{ $season->status ?? 'ACTIVE' }}</span>

            <!-- Menu list -->
            <div class="menu-list">
                <!-- 1. Lihat Bagan Bermain -->
                <a href="{{ route('public.season.bracket', $slug) }}" class="menu-item">
                    <div class="menu-content">
                        <div class="menu-icon-wrapper">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                        <span>Lihat Bagan Bermain</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </a>

                <!-- 2. Lihat Jadwal -->
                <div class="menu-item" data-bs-toggle="modal" data-bs-target="#modalJadwal">
                    <div class="menu-content">
                        <div class="menu-icon-wrapper">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <span>Lihat Jadwal Tanding</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </div>

                <!-- 3. Lihat Rules -->
                <div class="menu-item" data-bs-toggle="modal" data-bs-target="#modalRules">
                    <div class="menu-content">
                        <div class="menu-icon-wrapper">
                            <i class="bi bi-file-earmark-ruled-fill"></i>
                        </div>
                        <span>Lihat Rules Turnamen</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </div>

                <!-- 4. Chat Dengan Admin (Live Chat) -->
                <div class="menu-item" id="btnLandingChatOpen">
                    <div class="menu-content">
                        <div class="menu-icon-wrapper">
                            <i class="bi bi-chat-left-text-fill"></i>
                        </div>
                        <div class="d-flex flex-column align-items-start gap-1">
                            <span>Chat Dengan Admin</span>
                            <span class="badge bg-success rounded-pill px-2 py-0.5" style="font-size: 0.52rem; letter-spacing: 0.3px;">LIVE NOW</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
                </div>

                <!-- 5. Laporkan Hasil Laga (Upload Bukti) -->
                <div class="menu-item" data-bs-toggle="modal" data-bs-target="#modalReportScore" style="border-color: rgba(255, 193, 7, 0.25); background-color: rgba(255, 193, 7, 0.02);">
                    <div class="menu-content">
                        <div class="menu-icon-wrapper" style="background-color: rgba(255, 193, 7, 0.1); color: #ffc107;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <span>Laporkan Hasil Laga</span>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow" style="color: #ffc107;"></i>
                </div>
            </div>
        </div>

        <p class="footer-text">© {{ date('Y') }} Yomuda Championship. All Rights Reserved.</p>
    </div>

    <!-- ---------------------------------------------------------------------- -->
    <!-- Modal Jadwal -->
    <!-- ---------------------------------------------------------------------- -->
    <div class="modal fade" id="modalJadwal" tabindex="-1" aria-labelledby="modalJadwalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalJadwalLabel"><i class="bi bi-calendar3 text-warning me-2"></i>Jadwal Tanding</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if($season->schedule_info)
                        <div class="bg-dark p-3 rounded-4 border border-secondary border-opacity-25 text-start" style="white-space: pre-line; font-size: 0.85rem; line-height: 1.6;">
                            {{ $season->schedule_info }}
                        </div>
                    @else
                        <!-- Default WhatsApp template schedule formatting -->
                        <div class="bg-dark p-3 rounded-4 border border-secondary border-opacity-25 text-start" style="white-space: pre-line; font-size: 0.82rem; line-height: 1.5;">
                            📢 <strong>JADWAL TURNAMEN ML</strong>

                            🗓️ <strong>Mulai:</strong> 20.00 WIB

                            ⏰ <strong>Waktu Main:</strong>
                            Babak 1 → 20.00 – 20.40
                            Babak 2 → 20.40 – 21.15
                            Babak 3 → 21.15 – 21.50
                            Babak 4 → 21.50 – 22.20
                            Babak 5 → 22.20 – 22.50
                            Semifinal → 22.50 – 23.20
                            Bronze (BO1) & Final (BO3) → 23.20 – Selesai

                            🗡️ <strong>Format Match:</strong>
                            Babak 1 – Bronze: Draft Pick | BO1
                            Final: Draft Pick | BO3

                            ⚠️ <strong>Aturan Waktu:</strong>
                            Toleransi telat 10 menit tiap babak
                            Babak 1 toleransi 15 menit
                            Lawan telat → SS lobby (JAM HARUS TERLIHAT)
                            Lewat toleransi → WO / Diskualifikasi

                            📺 Babak 4 - Final LIVE Streaming
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-modal-close w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------------------------------------------------------------------- -->
    <!-- Modal Rules (Embedded Reader) -->
    <!-- ---------------------------------------------------------------------- -->
    <style>
        .rules-pdf-container {
            width: 100%;
            height: 65vh;
            min-height: 450px;
            max-height: 580px;
            background-color: #ffffff;
        }
        @media (max-width: 768px) {
            .rules-pdf-container {
                height: 50vh;
                min-height: 300px;
            }
        }
    </style>

    <div class="modal fade" id="modalRules" tabindex="-1" aria-labelledby="modalRulesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="background-color: #1e1e24; border: 1px solid rgba(255,122,0,0.25); border-radius: 20px; color: #fff;">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalRulesLabel"><i class="bi bi-file-earmark-ruled text-warning me-2"></i>Rules Turnamen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-secondary mb-3">
                        Peraturan turnamen dirilis dalam bentuk dokumen resmi untuk menjamin transparansi dan kenyamanan bermain.
                    </p>
                    
                    @php
                        $rulesLink = \App\Models\Setting::getVal('global_rules_link') ?: $season->rules_link;
                        $embedLink = $rulesLink;
                        if ($rulesLink && str_contains($rulesLink, 'drive.google.com')) {
                            // Convert standard drive view link to /preview so Google Drive's native controls load inside the iframe
                            $embedLink = str_replace(['/view', '/edit'], '/preview', $rulesLink);
                        }
                    @endphp
                    @if($rulesLink)
                        @if(str_contains($rulesLink, 'drive.google.com'))
                            <!-- Google Drive preview reader with full page controls -->
                            <div class="rules-pdf-container rounded-4 overflow-hidden shadow-lg border border-secondary border-opacity-20 position-relative">
                                <iframe src="{{ $embedLink }}" style="width: 100%; height: 100%; border: none;" scrolling="yes"></iframe>
                            </div>
                        @else
                            <!-- PDF Zoom Control Bar -->
                            <div class="d-flex align-items-center justify-content-center gap-3 mb-3 p-2 rounded-4" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);">
                                <button type="button" class="btn btn-outline-warning btn-sm rounded-circle d-flex align-items-center justify-content-center" id="pdf-zoom-out" style="width: 32px; height: 32px; padding: 0;">
                                    <i class="bi bi-dash-lg"></i>
                                </button>
                                <span class="small fw-bold text-warning" id="pdf-zoom-level" style="min-width: 55px; text-align: center;">100%</span>
                                <button type="button" class="btn btn-outline-warning btn-sm rounded-circle d-flex align-items-center justify-content-center" id="pdf-zoom-in" style="width: 32px; height: 32px; padding: 0;">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>
                            
                            <!-- PDF.js Canvas Render for direct PDF files (gives perfect scrolling & page controls inside modal on both desktop and mobile) -->
                            <div id="pdf-viewer-container" class="rounded-4 overflow-auto shadow-lg border border-secondary border-opacity-20 p-2" style="height: 60vh; min-height: 400px; max-height: 580px; background-color: #2a2a30;">
                                <div class="text-center py-5 text-secondary" id="pdf-loader">
                                    <div class="spinner-border text-warning mb-2" role="status"></div>
                                    <p class="small text-secondary m-0">Memuat halaman peraturan...</p>
                                </div>
                            </div>
                        @endif
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ $rulesLink }}" target="_blank" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5" style="font-size: 0.78rem;">
                                <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru / Download
                            </a>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-info-circle text-secondary fs-1 mb-3"></i>
                            <div class="bg-dark p-3 rounded-4 text-secondary small border border-secondary border-opacity-10 max-w-50 mx-auto">
                                File peraturan belum di-upload oleh admin untuk season ini.
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-modal-close w-100" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 10px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------------------------------------------------------------------- -->
    <!-- Modal Report Score -->
    <!-- ---------------------------------------------------------------------- -->
    <div class="modal fade" id="modalReportScore" tabindex="-1" aria-labelledby="modalReportScoreLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: #1e1e24; border: 1px solid rgba(255,193,7,0.25); border-radius: 20px;">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold text-white" id="modalReportScoreLabel">
                        <i class="bi bi-trophy-fill text-warning me-2"></i> Laporkan Hasil Laga
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <!-- Step 1: Verification Form -->
                <div id="reportStepVerification" class="modal-body p-4 text-start">
                    <p class="small text-secondary mb-4">
                        Masukkan nomor WhatsApp Kapten/Perwakilan tim yang terdaftar saat registrasi untuk mencocokkan pertandingan aktif tim Anda.
                    </p>
                    <div class="mb-3 text-start">
                        <label class="small fw-bold text-warning text-uppercase mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                            Nomor WhatsApp Kapten
                        </label>
                        <input type="tel" id="reportWaInput" class="form-control bg-dark border-secondary text-white rounded-4 p-3 shadow-none" placeholder="Contoh: 08123456789" required autocomplete="off" style="border: 1px solid rgba(255,255,255,0.1);">
                    </div>
                    <button type="button" id="btnVerifyReportWa" class="btn btn-warning w-100 py-3 fw-bold rounded-4 text-dark">
                        CARI PERTANDINGAN SAYA <i class="bi bi-arrow-right-short ms-1 fs-5"></i>
                    </button>
                </div>

                <!-- Step 2: Score & Screenshot Form (Initially Hidden) -->
                <div id="reportStepSubmit" class="modal-body p-4" style="display: none;">
                    <div class="bg-dark p-3 rounded-4 border border-secondary border-opacity-10 mb-4">
                        <div class="small text-secondary fw-bold text-center text-uppercase mb-1" style="font-size: 0.6rem; letter-spacing: 0.8px;">Pertandingan Aktif Anda</div>
                        <h6 class="fw-bold text-white text-center mb-0" id="reportMatchTitle">TIM A vs TIM B</h6>
                        <div class="small text-warning text-center mt-1" style="font-size: 0.68rem;" id="reportMatchRound">Round 1</div>
                    </div>

                    <form id="formSubmitReport" enctype="multipart/form-data">
                        <input type="hidden" id="reportMatchId" name="match_id">
                        <input type="hidden" id="reportReporterTeamId" name="reporter_team_id">

                        <!-- Scores inputs -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="small fw-bold text-secondary text-uppercase mb-2 d-block text-start" style="font-size: 0.6rem;" id="labelScoreTeam1">Skor Tim 1</label>
                                <select id="scoreTeam1Input" name="score_team1" class="form-select bg-dark border-secondary text-white rounded-4 p-2.5 shadow-none" required style="border: 1px solid rgba(255,255,255,0.1);">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold text-secondary text-uppercase mb-2 d-block text-start" style="font-size: 0.6rem;" id="labelScoreTeam2">Skor Tim 2</label>
                                <select id="scoreTeam2Input" name="score_team2" class="form-select bg-dark border-secondary text-white rounded-4 p-2.5 shadow-none" required style="border: 1px solid rgba(255,255,255,0.1);">
                                    <option value="0">0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                        </div>

                        <!-- Screenshot Uploader -->
                        <div class="mb-4 text-start">
                            <label class="small fw-bold text-warning text-uppercase mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                                Upload Screenshot Hasil Game
                            </label>
                            <input type="file" id="reportImageInput" name="image" accept="image/*" class="form-control bg-dark border-secondary text-white rounded-4 p-2 shadow-none" required style="border: 1px solid rgba(255,255,255,0.1);">
                            <small class="text-secondary d-block mt-1 text-start" style="font-size: 0.68rem;">Format JPG/PNG/WebP, maksimal 5MB</small>
                        </div>

                        <button type="submit" id="btnSubmitReportScore" class="btn btn-warning w-100 py-3 fw-bold rounded-4 text-dark">
                            KIRIM LAPORAN SEKARANG
                        </button>
                    </form>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-modal-close w-100" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 10px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ---------------------------------------------------------------------- -->
    <!-- Floating Live Chat UI & Scripts -->
    <!-- ---------------------------------------------------------------------- -->
    <div class="chat-widget-wrapper" id="chatWidget">
        <button class="chat-toggle-btn shadow-lg" id="btnChatToggle">
            <i class="bi bi-chat-left-text-fill"></i>
            <span class="chat-badge" id="chatUnreadCount" style="display: none;">0</span>
        </button>
        
        <div class="chat-box-container" id="chatBoxContainer">
            <div class="chat-box-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="chat-status-dot"></div>
                    <div>
                        <div class="chat-header-title">Live Chat Admin</div>
                        <div class="chat-header-subtitle">Yomuda Panitia</div>
                    </div>
                </div>
                <button class="chat-close-btn" id="btnChatClose"><i class="bi bi-x-lg"></i></button>
            </div>
            
            <div class="chat-messages-body" id="chatMessagesBody">
                <div class="chat-system-message">
                    Halo! Ada yang bisa kami bantu mengenai bracket turnamen? Tulis pertanyaanmu di bawah.
                </div>
            </div>
            
            <div class="chat-input-wrapper">
                <button id="btnChatAttach" title="Kirim Screenshot" style="background:none; border:none; color:var(--text-dim); padding:0 4px;"><i class="bi bi-camera-fill"></i></button>
                <input type="file" id="chatFileInput" accept="image/*" style="display: none;">
                <input type="text" id="chatInputText" placeholder="Ketik pesan..." autocomplete="off">
                <button id="btnChatSend"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Session Token Setup
            let sessionToken = localStorage.getItem('yomuda_chat_session_token');
            if (!sessionToken) {
                sessionToken = 'token_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
                localStorage.setItem('yomuda_chat_session_token', sessionToken);
            }

            const btnChatToggle = document.getElementById('btnChatToggle');
            const btnChatClose = document.getElementById('btnChatClose');
            const btnLandingChatOpen = document.getElementById('btnLandingChatOpen');
            const chatBoxContainer = document.getElementById('chatBoxContainer');
            const chatMessagesBody = document.getElementById('chatMessagesBody');
            const chatInputText = document.getElementById('chatInputText');
            const btnChatSend = document.getElementById('btnChatSend');
            const chatUnreadCount = document.getElementById('chatUnreadCount');

            let isChatOpen = false;
            let lastMessageId = 0;
            let chatPollingInterval = null;

            function toggleChat() {
                isChatOpen = !isChatOpen;
                if (isChatOpen) {
                    chatBoxContainer.classList.add('active');
                    chatUnreadCount.style.display = 'none';
                    chatUnreadCount.textContent = '0';
                    scrollChatToBottom();
                    fetchChatMessages();
                    if (!chatPollingInterval) {
                        chatPollingInterval = setInterval(fetchChatMessages, 3000);
                    }
                } else {
                    chatBoxContainer.classList.remove('active');
                    if (chatPollingInterval) {
                        clearInterval(chatPollingInterval);
                        chatPollingInterval = null;
                    }
                }
            }

            btnChatToggle.addEventListener('click', toggleChat);
            btnChatClose.addEventListener('click', toggleChat);
            if (btnLandingChatOpen) {
                btnLandingChatOpen.addEventListener('click', toggleChat);
            }

            function scrollChatToBottom() {
                setTimeout(() => {
                    chatMessagesBody.scrollTop = chatMessagesBody.scrollHeight;
                }, 80);
            }

            let isInitialLoad = true;

            function playNotificationSound() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc1 = audioCtx.createOscillator();
                    const osc2 = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(880, audioCtx.currentTime);
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(1200, audioCtx.currentTime);
                    
                    gainNode.gain.setValueAtTime(0.12, audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.5);
                    
                    osc1.connect(gainNode);
                    osc2.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    
                    osc1.start();
                    osc2.start();
                    osc1.stop(audioCtx.currentTime + 0.5);
                    osc2.stop(audioCtx.currentTime + 0.5);
                } catch(e) {
                    console.log("Audio play blocked:", e);
                }
            }

            function fetchChatMessages() {
                fetch("{{ route('public.season.chat.messages', $slug) }}?session_token=" + sessionToken)
                    .then(r => r.json())
                    .then(res => {
                        if (res.success && res.messages) {
                            let newMessagesFound = false;
                            let unread = 0;
                            let shouldPlaySound = false;

                            res.messages.forEach(msg => {
                                if (msg.id > lastMessageId) {
                                    renderMessage(msg);
                                    lastMessageId = msg.id;
                                    newMessagesFound = true;
                                    if (msg.is_admin) {
                                        unread++;
                                        if (!isInitialLoad) {
                                            shouldPlaySound = true;
                                        }
                                    }
                                }
                            });

                            isInitialLoad = false;

                            if (newMessagesFound) {
                                scrollChatToBottom();
                                if (!isChatOpen && unread > 0) {
                                    chatUnreadCount.style.display = 'flex';
                                    chatUnreadCount.textContent = unread;
                                }
                                if (shouldPlaySound) {
                                    playNotificationSound();
                                }
                            }
                        }
                    })
                    .catch(err => console.log("Chat fetch issue:", err));
            }

            function renderMessage(msg) {
                const bubble = document.createElement('div');
                bubble.className = `chat-msg-bubble ${msg.is_admin ? 'admin' : 'user'}`;
                if (typeof msg.id === 'string' && msg.id.startsWith('temp-')) {
                    bubble.id = msg.id;
                    bubble.style.opacity = '0.75';
                }
                if (msg.message.startsWith('[IMAGE]:')) {
                    const imgUrl = msg.message.substring(8);
                    bubble.innerHTML = `<img src="${imgUrl}" class="img-fluid rounded-3 my-1" style="max-height: 120px; cursor: pointer; display: block;" onclick="window.open('${imgUrl}', '_blank')" onload="scrollChatToBottom()">`;
                } else {
                    bubble.textContent = msg.message;
                }
                chatMessagesBody.appendChild(bubble);
            }

            function sendPublicMessage() {
                const text = chatInputText.value.trim();
                if (!text) return;

                chatInputText.value = '';
                
                // Optimistic render
                const tempMsg = {
                    id: 99999999 + Math.random(),
                    message: text,
                    is_admin: false
                };
                renderMessage(tempMsg);
                scrollChatToBottom();

                fetch("{{ route('public.season.chat.send', $slug) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        session_token: sessionToken,
                        message: text
                    })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        lastMessageId = Math.max(lastMessageId, res.chat.id);
                    }
                })
                .catch(err => console.log("Chat send issue:", err));
            }

            btnChatSend.addEventListener('click', sendPublicMessage);
            chatInputText.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendPublicMessage();
                }
            });

            // Chat image upload handling
            const btnChatAttach = document.getElementById('btnChatAttach');
            const chatFileInput = document.getElementById('chatFileInput');

            if (btnChatAttach && chatFileInput) {
                btnChatAttach.addEventListener('click', () => chatFileInput.click());
                chatFileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        if (file.size > 5 * 1024 * 1024) {
                            alert("Ukuran file maksimal 5MB!");
                            return;
                        }
                        
                        // 1. Generate local URL for instant preview (Optimistic UI)
                        const localImgUrl = URL.createObjectURL(file);
                        const tempId = 'temp-' + Date.now();
                        
                        const tempMsg = {
                            id: tempId,
                            message: "[IMAGE]:" + localImgUrl,
                            is_admin: false
                        };
                        renderMessage(tempMsg);
                        scrollChatToBottom();
                        
                        // 2. Compress the image before uploading (waswuss)
                        compressImage(file, 1200, 0.75)
                        .then(compressedFile => {
                            const formData = new FormData();
                            formData.append('image', compressedFile);
                            formData.append('session_token', sessionToken);
                            
                            return fetch("{{ route('public.season.chat.upload', $slug) }}", {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            });
                        })
                        .then(r => r.json())
                        .then(res => {
                            // Clean up local blob memory
                            URL.revokeObjectURL(localImgUrl);
                            
                            // Remove optimistic preview element before rendering the real one
                            const tempEl = document.getElementById(tempId);
                            if (tempEl) tempEl.remove();
                            
                            if (res.success) {
                                fetchChatMessages();
                            } else {
                                alert("Gagal mengunggah: " + res.message);
                            }
                        })
                        .catch(err => {
                            console.log("Upload err:", err);
                            URL.revokeObjectURL(localImgUrl);
                            const tempEl = document.getElementById(tempId);
                            if (tempEl) tempEl.remove();
                            alert("Gagal mengunggah gambar.");
                        });
                    }
                });
            }

            // ----------------------------------------------------
            // Match Report Score JavaScript Logic
            // ----------------------------------------------------
            const btnVerifyReportWa = document.getElementById('btnVerifyReportWa');
            const reportWaInput = document.getElementById('reportWaInput');
            const reportStepVerification = document.getElementById('reportStepVerification');
            const reportStepSubmit = document.getElementById('reportStepSubmit');
            const reportMatchTitle = document.getElementById('reportMatchTitle');
            const reportMatchRound = document.getElementById('reportMatchRound');
            const reportMatchId = document.getElementById('reportMatchId');
            const reportReporterTeamId = document.getElementById('reportReporterTeamId');
            const labelScoreTeam1 = document.getElementById('labelScoreTeam1');
            const labelScoreTeam2 = document.getElementById('labelScoreTeam2');
            const formSubmitReport = document.getElementById('formSubmitReport');
            const btnSubmitReportScore = document.getElementById('btnSubmitReportScore');

            if (btnVerifyReportWa) {
                btnVerifyReportWa.addEventListener('click', function() {
                    const wa = reportWaInput.value.trim();
                    if (!wa) {
                        alert('Silakan masukkan nomor WhatsApp Anda.');
                        return;
                    }

                    btnVerifyReportWa.disabled = true;
                    btnVerifyReportWa.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mencari...';

                    fetch("{{ route('public.match-report.find', $slug) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ wa_number: wa })
                    })
                    .then(r => r.json())
                    .then(res => {
                        btnVerifyReportWa.disabled = false;
                        btnVerifyReportWa.innerHTML = 'CARI PERTANDINGAN SAYA <i class="bi bi-arrow-right-short ms-1 fs-5"></i>';

                        if (res.success && res.match) {
                            const match = res.match;
                            reportMatchId.value = match.id;
                            reportReporterTeamId.value = res.reporter_team_id;
                            reportMatchTitle.textContent = `${match.team1_name} vs ${match.team2_name}`;
                            reportMatchRound.textContent = `Round ${match.round_number} (Bracket Match ${match.match_number})`;
                            labelScoreTeam1.textContent = `Skor ${match.team1_name}`;
                            labelScoreTeam2.textContent = `Skor ${match.team2_name}`;
                            
                            reportStepVerification.style.display = 'none';
                            reportStepSubmit.style.display = 'block';
                        } else {
                            alert(res.message);
                        }
                    })
                    .catch(err => {
                        btnVerifyReportWa.disabled = false;
                        btnVerifyReportWa.innerHTML = 'CARI PERTANDINGAN SAYA <i class="bi bi-arrow-right-short ms-1 fs-5"></i>';
                        console.error('Error finding match:', err);
                        alert('Terjadi kesalahan saat mencari pertandingan.');
                    });
                });
            }

            // Client-side image compressor using Canvas to achieve instant ("waswuss") uploads
            function compressImage(file, maxWidth, quality) {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.readAsDataURL(file);
                    reader.onload = function(event) {
                        const img = new Image();
                        img.src = event.target.result;
                        img.onload = function() {
                            const canvas = document.createElement('canvas');
                            let width = img.width;
                            let height = img.height;

                            if (width > maxWidth) {
                                height = Math.round((height * maxWidth) / width);
                                width = maxWidth;
                            }

                            canvas.width = width;
                            canvas.height = height;

                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                if (blob) {
                                    resolve(new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                        type: 'image/jpeg',
                                        lastModified: Date.now()
                                    }));
                                } else {
                                    reject(new Error("Canvas toBlob failed"));
                                }
                            }, 'image/jpeg', quality);
                        };
                        img.onerror = (err) => reject(err);
                    };
                    reader.onerror = (err) => reject(err);
                });
            }

            if (formSubmitReport) {
                formSubmitReport.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const fileInput = document.getElementById('reportImageInput');
                    if (!fileInput.files || fileInput.files.length === 0) {
                        alert('Silakan pilih berkas bukti screenshot.');
                        return;
                    }

                    btnSubmitReportScore.disabled = true;
                    btnSubmitReportScore.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengompres Gambar...';

                    const file = fileInput.files[0];
                    compressImage(file, 1200, 0.7)
                    .then(compressedFile => {
                        btnSubmitReportScore.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim Laporan... 🚀';

                        const formData = new FormData();
                        formData.append('match_id', formSubmitReport.querySelector('[name="match_id"]').value);
                        formData.append('reporter_team_id', formSubmitReport.querySelector('[name="reporter_team_id"]').value);
                        formData.append('score_team1', formSubmitReport.querySelector('[name="score_team1"]').value);
                        formData.append('score_team2', formSubmitReport.querySelector('[name="score_team2"]').value);
                        formData.append('image', compressedFile);

                        return fetch("{{ route('public.match-report.submit', $slug) }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });
                    })
                    .then(r => r.json())
                    .then(res => {
                        btnSubmitReportScore.disabled = false;
                        btnSubmitReportScore.innerHTML = 'KIRIM LAPORAN SEKARANG';

                        if (res.success) {
                            alert(res.message);
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('modalReportScore'));
                            if (modal) modal.hide();
                            
                            // Reset state
                            formSubmitReport.reset();
                            reportWaInput.value = '';
                            reportStepVerification.style.display = 'block';
                            reportStepSubmit.style.display = 'none';
                        } else {
                            alert(res.message);
                        }
                    })
                    .catch(err => {
                        btnSubmitReportScore.disabled = false;
                        btnSubmitReportScore.textContent = 'KIRIM LAPORAN SEKARANG';
                        console.error('Error submitting report:', err);
                        alert('Terjadi kesalahan saat mengirimkan laporan.');
                    });
                });
            }

            // Reset modal on close
            const modalEl = document.getElementById('modalReportScore');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    if (formSubmitReport) formSubmitReport.reset();
                    if (reportWaInput) reportWaInput.value = '';
                    if (reportStepVerification) reportStepVerification.style.display = 'block';
                    if (reportStepSubmit) reportStepSubmit.style.display = 'none';
                });
            }

            // Auto open report modal if URL parameter report=1 is set
            if (new URLSearchParams(window.location.search).get('report') === '1') {
                const reportModal = new bootstrap.Modal(document.getElementById('modalReportScore'));
                if (reportModal) reportModal.show();
            }

            fetchChatMessages();
            setInterval(() => {
                if (!isChatOpen) {
                    fetchChatMessages();
                }
            }, 10000);
        });
    </script>

    @if($rulesLink && !str_contains($rulesLink, 'drive.google.com'))
    <!-- Load PDF.js from CDN to render local PDF files directly as Canvas in the modal -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pdfjsLib = window['pdfjs-dist/build/pdf'];
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

            const modalRulesEl = document.getElementById('modalRules');
            let pdfLoaded = false;
            let currentScale = window.innerWidth < 768 ? 1.0 : 1.3;
            let pagesObjects = [];

            function renderPages() {
                const container = document.getElementById('pdf-viewer-container');
                if (!container) return;

                // Clear previous canvases
                container.querySelectorAll('canvas').forEach(c => c.remove());

                // Update zoom label
                const zoomLabel = document.getElementById('pdf-zoom-level');
                if (zoomLabel) {
                    zoomLabel.textContent = Math.round(currentScale * 100) + '%';
                }

                pagesObjects.forEach((page, index) => {
                    const canvas = document.createElement('canvas');
                    canvas.className = 'mb-3 rounded shadow-sm d-block mx-auto';
                    canvas.style.maxWidth = '100%';
                    canvas.style.height = 'auto';
                    container.appendChild(canvas);

                    const viewport = page.getViewport({ scale: currentScale });
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext);
                });
            }

            if (modalRulesEl) {
                modalRulesEl.addEventListener('shown.bs.modal', function () {
                    if (pdfLoaded) return; // Prevent double rendering
                    
                    const url = "{{ $rulesLink }}";
                    const container = document.getElementById('pdf-viewer-container');
                    const loader = document.getElementById('pdf-loader');

                    if (!container) return;

                    pdfjsLib.getDocument(url).promise.then(pdf => {
                        pdfLoaded = true;
                        
                        let pagesFetched = 0;
                        pagesObjects = [];

                        // Fetch pages sequentially to maintain order, then render
                        for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                            pdf.getPage(pageNum).then(page => {
                                pagesObjects[pageNum - 1] = page;
                                pagesFetched++;

                                if (pagesFetched === pdf.numPages) {
                                    if (loader) loader.remove();
                                    renderPages();
                                }
                            });
                        }
                    }).catch(err => {
                        console.error("PDF.js render error:", err);
                        if (loader) {
                            loader.innerHTML = `<i class="bi bi-exclamation-triangle-fill text-danger fs-2 d-block mb-2"></i><p class="small text-danger m-0">Gagal memuat preview dokumen.</p>`;
                        }
                    });
                });

                // Attach zoom button event listeners
                const btnZoomIn = document.getElementById('pdf-zoom-in');
                const btnZoomOut = document.getElementById('pdf-zoom-out');

                if (btnZoomIn && btnZoomOut) {
                    btnZoomIn.addEventListener('click', function () {
                        if (currentScale < 2.5) {
                            currentScale += 0.2;
                            renderPages();
                        }
                    });

                    btnZoomOut.addEventListener('click', function () {
                        if (currentScale > 0.6) {
                            currentScale -= 0.2;
                            renderPages();
                        }
                    });
                }
            }
        });
    </script>
    @endif
</body>
</html>
