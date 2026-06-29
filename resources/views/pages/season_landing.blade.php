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

        .landing-container {
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .brand-logo {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -1px;
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
    </style>
</head>
<body>

    <div class="landing-container">
        <!-- Logo -->
        <div class="brand-logo">
            <i class="bi bi-lightning-charge-fill"></i>
            <span>YOMUDA ADM</span>
        </div>

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
                <div class="menu-item" data-bs-toggle="modal" data-bs-target="#modalLiveChat">
                    <div class="menu-content">
                        <div class="menu-icon-wrapper">
                            <i class="bi bi-chat-left-text-fill"></i>
                        </div>
                        <div class="d-flex flex-column align-items-start gap-1">
                            <span>Chat Dengan Admin</span>
                            <span class="badge-coming-soon">COMING SOON</span>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right menu-arrow"></i>
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
    <!-- Modal Rules -->
    <!-- ---------------------------------------------------------------------- -->
    <div class="modal fade" id="modalRules" tabindex="-1" aria-labelledby="modalRulesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalRulesLabel"><i class="bi bi-file-earmark-ruled text-warning me-2"></i>Rules Turnamen</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <i class="bi bi-shield-lock-fill text-warning mb-3 d-inline-block" style="font-size: 3rem; filter: drop-shadow(0 0 15px rgba(255, 122, 0, 0.2));"></i>
                    <p class="small text-secondary mb-4 px-2">
                        Peraturan turnamen dirilis dalam bentuk dokumen resmi untuk menjamin transparansi dan kenyamanan bermain.
                    </p>
                    
                    @php
                        $rulesLink = \App\Models\Setting::getVal('global_rules_link') ?: $season->rules_link;
                    @endphp
                    @if($rulesLink)
                        <a href="{{ $rulesLink }}" target="_blank" class="btn btn-accent px-4 py-2 w-100 justify-content-center">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Buka Dokumen Rules Turnamen
                        </a>
                    @else
                        <div class="bg-dark p-3 rounded-4 text-secondary small border border-secondary border-opacity-10 mb-2">
                            <i class="bi bi-info-circle me-1"></i> File peraturan belum di-upload oleh admin untuk season ini.
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
    <!-- Modal Live Chat -->
    <!-- ---------------------------------------------------------------------- -->
    <div class="modal fade" id="modalLiveChat" tabindex="-1" aria-labelledby="modalLiveChatLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-bold" id="modalLiveChatLabel"><i class="bi bi-chat-dots-fill text-warning me-2"></i>Web Live Chat Admin</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <i class="bi bi-chat-left-heart text-warning" style="font-size: 3.5rem; filter: drop-shadow(0 0 15px rgba(255, 122, 0, 0.3));"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.5rem; padding: 4px 6px;">NEW</span>
                    </div>
                    <h6 class="fw-bold mb-2">Fitur Chat Langsung via Web</h6>
                    <p class="small text-secondary mb-4 px-2">
                        Kami sedang menyiapkan fitur <strong>Live Chat Web</strong> terintegrasi agar Anda dapat bertanya langsung kepada admin/panitia turnamen secara real-time tanpa perlu dialihkan ke WhatsApp, meminimalisir risiko banned nomor WhatsApp.
                    </p>
                    <div class="bg-dark p-3 rounded-4 text-warning small border border-warning border-opacity-10 mb-2">
                        <i class="bi bi-gear-fill spin me-1"></i> Sedang dikembangkan. Segera hadir untuk Anda!
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-modal-close w-100" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
