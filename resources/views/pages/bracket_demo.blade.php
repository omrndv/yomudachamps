<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bagan Turnamen - Yomuda Championship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-primary: #020617;
            --bg-secondary: #0b1329;
            --accent-gold: #f59e0b;
            --accent-gold-hover: #d97706;
            --text-muted: #94a3b8;
            --border-light: rgba(255, 255, 255, 0.06);
            --card-bg: rgba(15, 23, 42, 0.75);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-primary);
            color: #ffffff;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(245, 158, 11, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(59, 130, 246, 0.03) 0%, transparent 40%);
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        /* Header Style */
        .bracket-header {
            padding: 24px 0;
            border-bottom: 1px solid var(--border-light);
            background: linear-gradient(180deg, rgba(11, 19, 41, 0.9) 0%, transparent 100%);
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-gold) 0%, #d97706 100%);
            color: #020617;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            font-size: 1.15rem;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
            margin-right: 12px;
        }

        /* Bracket View Wrapper with hardware acceleration optimization */
        .bracket-container {
            padding: 40px 20px;
            overflow-x: auto;
            white-space: nowrap;
            cursor: grab;
            user-select: none;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-gold) var(--bg-secondary);
            scroll-behavior: smooth;
            transform: translate3d(0, 0, 0); /* Hardware acceleration */
            will-change: scroll-position;
        }

        .bracket-container:active {
            cursor: grabbing;
        }

        .bracket-container::-webkit-scrollbar {
            height: 6px;
        }

        .bracket-container::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 4px;
        }

        .bracket-container::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 4px;
        }

        /* Bracket Columns/Rounds - Sizing is fluid to support high team counts (up to 128) */
        .bracket-round {
            display: inline-flex;
            flex-direction: column;
            justify-content: space-around;
            min-height: 580px; /* Minimal height */
            height: auto;
            vertical-align: middle;
            margin-right: 80px;
            position: relative;
            padding: 10px 0;
        }

        .round-title {
            position: absolute;
            top: -25px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            color: var(--accent-gold);
            opacity: 0.85;
        }

        /* Match Card Styles */
        .match-card {
            background: var(--card-bg);
            border: 1px solid var(--border-light);
            border-radius: 12px;
            width: 240px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
            position: relative;
            z-index: 2;
            margin: 15px 0; /* Margin-based flexible spacing to support scaling height */
        }

        .match-card:hover {
            border-color: rgba(245, 158, 11, 0.35);
            box-shadow: 0 10px 24px rgba(245, 158, 11, 0.12);
            transform: translateY(-2px);
        }

        .match-card.focus-glow {
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 25px rgba(245, 158, 11, 0.45) !important;
            animation: pulse-border 1.5s infinite alternate;
        }

        /* Match Schedule Info Row */
        .match-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .match-time {
            color: var(--accent-gold);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .match-time.live {
            color: #ef4444;
            animation: pulse-live 1.5s infinite;
        }

        /* Team Row Styles */
        .team-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 12px;
            position: relative;
            transition: background 0.2s ease;
            cursor: pointer;
        }

        .team-row:first-of-type {
            border-bottom: 1px solid var(--border-light);
        }

        .team-row.winner {
            background: rgba(245, 158, 11, 0.02);
        }

        .team-row.loser {
            opacity: 0.45;
        }

        .team-row:hover {
            background: rgba(255, 255, 255, 0.025);
        }

        .team-info {
            display: flex;
            align-items: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .team-logo {
            width: 22px;
            height: 22px;
            border-radius: 5px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.68rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .team-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: #ffffff;
            transition: color 0.2s ease;
        }

        .team-score {
            font-size: 0.92rem;
            font-weight: 800;
            color: #ffffff;
            padding-left: 10px;
        }

        .team-row.winner .team-name {
            color: #ffffff;
        }

        .team-row.winner .team-score {
            color: var(--accent-gold);
        }

        /* Winner crown/medal icon badge */
        .winner-badge {
            color: var(--accent-gold);
            font-size: 0.75rem;
            margin-left: 6px;
        }

        /* Interactive Path Glow Style */
        .team-highlighted .team-name {
            color: var(--accent-gold) !important;
            text-shadow: 0 0 6px rgba(245, 158, 11, 0.4);
        }
        
        .team-highlighted .team-logo {
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 6px rgba(245, 158, 11, 0.25);
        }

        /* Bracket connecting lines */
        .connector-svg {
            position: absolute;
            top: 0;
            left: 240px;
            width: 80px;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .connector-line {
            fill: none;
            stroke: rgba(255, 255, 255, 0.06);
            stroke-width: 1.8;
            transition: stroke 0.25s ease, stroke-width 0.25s ease;
        }

        /* Glow active connector line */
        .connector-line.highlighted {
            stroke: var(--accent-gold);
            stroke-width: 2.5;
            filter: drop-shadow(0 0 2px rgba(245, 158, 11, 0.4));
        }

        @keyframes pulse-live {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        @keyframes pulse-border {
            from { border-color: rgba(245, 158, 11, 0.35); }
            to { border-color: var(--accent-gold); }
        }

        /* Search Section Custom Style */
        .search-wrapper {
            max-width: 440px;
            margin: 0 auto;
        }

        .search-input-group {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-light);
            border-radius: 50px;
            padding: 3px 5px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        .search-input-group:focus-within {
            border-color: var(--accent-gold);
            box-shadow: 0 0 12px rgba(245, 158, 11, 0.15);
        }

        .search-input-group input {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 0.85rem;
            outline: none;
            padding: 6px 12px;
            width: 100%;
        }

        .search-input-group input::placeholder {
            color: var(--text-muted);
        }

        .search-icon-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 0 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Search Results Panel */
        .search-results-panel {
            background-color: #0b1329;
            border: 1px solid var(--border-light);
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
            display: none;
            margin-top: 10px;
            text-align: left;
            will-change: transform, opacity;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="bracket-header">
        <div class="container text-center">
            <div class="d-inline-flex align-items-center justify-content-center">
                <span class="brand-logo">Y</span>
                <div class="text-start">
                    <h4 class="fw-bold m-0" style="letter-spacing: 0.5px; font-size: 1.35rem;">YOMUDA <span class="text-warning text-uppercase">Season 33</span></h4>
                    <p class="text-secondary m-0 small" style="font-size: 0.78rem; letter-spacing: 0.3px;">Bagan Tournament Yomuda</p>
                </div>
            </div>
        </div>
    </header>

    {{-- Search Container --}}
    <div class="container mt-4">
        <div class="search-wrapper text-center mb-4">
            <div class="search-input-group d-flex align-items-center">
                <input type="text" id="teamSearchInput" autocomplete="off" placeholder="Cari nama tim Anda di bracket...">
                <button class="search-icon-btn"><i class="bi bi-search"></i></button>
            </div>

            {{-- Live Search Result Card --}}
            <div id="searchResultCard" class="search-results-panel">
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25 pb-2 mb-2.5">
                    <h6 class="fw-bold text-warning mb-0" id="resTeamName" style="font-size: 0.9rem;">Nama Tim</h6>
                    <span class="badge bg-danger rounded-pill px-2.5 py-1" id="resMatchStatus" style="font-size: 0.65rem;">Selesai</span>
                </div>
                <div class="row g-2 mb-2.5 text-white-50" style="font-size: 0.78rem;">
                    <div class="col-6">
                        <span class="d-block small text-muted text-uppercase fw-semibold mb-0.5" style="letter-spacing: 0.3px; font-size: 0.65rem;">Lawan Bertanding</span>
                        <strong class="text-white" id="resOpponent">Tim Lawan</strong>
                    </div>
                    <div class="col-6">
                        <span class="d-block small text-muted text-uppercase fw-semibold mb-0.5" style="letter-spacing: 0.3px; font-size: 0.65rem;">Jadwal Tanding</span>
                        <strong class="text-warning" id="resSchedule">Jam Tanding</strong>
                    </div>
                    <div class="col-6 mt-2">
                        <span class="d-block small text-muted text-uppercase fw-semibold mb-0.5" style="letter-spacing: 0.3px; font-size: 0.65rem;">Nomor Bracket</span>
                        <strong class="text-white" id="resBracketLabel">Bracket 1</strong>
                    </div>
                    <div class="col-6 mt-2">
                        <span class="d-block small text-muted text-uppercase fw-semibold mb-0.5" style="letter-spacing: 0.3px; font-size: 0.65rem;">Status Babak</span>
                        <strong class="text-white" id="resRoundLabel">Babak 1 (Perempat Final)</strong>
                    </div>
                </div>
                <div class="text-end">
                    <button class="btn btn-warning btn-sm fw-bold px-3 py-1.5 rounded-pill text-dark" id="btnFocusBracket" style="font-size: 0.78rem;">
                        <i class="bi bi-crosshair me-1"></i> Fokuskan ke Bagan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bracket Field Wrapper --}}
    <div class="container-fluid px-0">
        <div class="bracket-container" id="bracketContainer">
            
            {{-- ROUND 1: QUARTERFINALS --}}
            <div class="bracket-round" id="round_quarter">
                <span class="round-title">Perempat Final</span>
                
                {{-- Match 1 --}}
                <div class="match-card" data-match-id="1" id="card_match_1">
                    <div class="match-header">
                        <span>BRACKET 1</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 20:00 WIB</span>
                    </div>
                    <div class="team-row winner" data-team-id="yomuda_star">
                        <div class="team-info">
                            <div class="team-logo">YS</div>
                            <span class="team-name">Yomuda Star</span>
                            <i class="bi bi-patch-check-fill winner-badge"></i>
                        </div>
                        <span class="team-score">2</span>
                    </div>
                    <div class="team-row loser" data-team-id="sans_esport">
                        <div class="team-info">
                            <div class="team-logo">SE</div>
                            <span class="team-name">Sans Esports</span>
                        </div>
                        <span class="team-score">0</span>
                    </div>
                </div>

                {{-- Match 2 --}}
                <div class="match-card" data-match-id="2" id="card_match_2">
                    <div class="match-header">
                        <span>BRACKET 2</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 20:40 WIB</span>
                    </div>
                    <div class="team-row winner" data-team-id="rrq_yomu">
                        <div class="team-info">
                            <div class="team-logo">RY</div>
                            <span class="team-name">RRQ Yomu</span>
                            <i class="bi bi-patch-check-fill winner-badge"></i>
                        </div>
                        <span class="team-score">2</span>
                    </div>
                    <div class="team-row loser" data-team-id="evos_wann">
                        <div class="team-info">
                            <div class="team-logo">EW</div>
                            <span class="team-name">Evos Wann</span>
                        </div>
                        <span class="team-score">1</span>
                    </div>
                </div>

                {{-- Match 3 --}}
                <div class="match-card" data-match-id="3" id="card_match_3">
                    <div class="match-header">
                        <span>BRACKET 3</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 21:20 WIB</span>
                    </div>
                    <div class="team-row loser" data-team-id="alter_ego">
                        <div class="team-info">
                            <div class="team-logo">AE</div>
                            <span class="team-name">Alter Ego Y</span>
                        </div>
                        <span class="team-score">0</span>
                    </div>
                    <div class="team-row winner" data-team-id="onic_pro">
                        <div class="team-info">
                            <div class="team-logo">OP</div>
                            <span class="team-name">Onic Pro</span>
                            <i class="bi bi-patch-check-fill winner-badge"></i>
                        </div>
                        <span class="team-score">2</span>
                    </div>
                </div>

                {{-- Match 4 --}}
                <div class="match-card" data-match-id="4" id="card_match_4">
                    <div class="match-header">
                        <span>BRACKET 4</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 22:00 WIB</span>
                    </div>
                    <div class="team-row winner" data-team-id="aura_fire">
                        <div class="team-info">
                            <div class="team-logo">AF</div>
                            <span class="team-name">Aura Fire Junior</span>
                            <i class="bi bi-patch-check-fill winner-badge"></i>
                        </div>
                        <span class="team-score">2</span>
                    </div>
                    <div class="team-row loser" data-team-id="geek_fam">
                        <div class="team-info">
                            <div class="team-logo">GF</div>
                            <span class="team-name">Geek Fam X</span>
                        </div>
                        <span class="team-score">1</span>
                    </div>
                </div>

                {{-- SVG Connectors to Semifinals --}}
                <svg class="connector-svg">
                    <path class="connector-line" id="line_1" d="M 0 100 L 35 100 L 35 180 L 70 180" />
                    <path class="connector-line" id="line_2" d="M 0 260 L 35 260 L 35 180 L 70 180" />
                    <path class="connector-line" id="line_3" d="M 0 420 L 35 420 L 35 500 L 70 500" />
                    <path class="connector-line" id="line_4" d="M 0 580 L 35 580 L 35 500 L 70 500" />
                </svg>
            </div>

            {{-- ROUND 2: SEMIFINALS --}}
            <div class="bracket-round" id="round_semi">
                <span class="round-title">Semifinal</span>
                
                {{-- Match 5 --}}
                <div class="match-card" data-match-id="5" id="card_match_5">
                    <div class="match-header">
                        <span>BRACKET 5</span>
                        <span class="match-time live"><i class="bi bi-broadcast"></i> LIVE 22:40</span>
                    </div>
                    <div class="team-row winner" data-team-id="yomuda_star">
                        <div class="team-info">
                            <div class="team-logo">YS</div>
                            <span class="team-name">Yomuda Star</span>
                            <i class="bi bi-patch-check-fill winner-badge"></i>
                        </div>
                        <span class="team-score">2</span>
                    </div>
                    <div class="team-row loser" data-team-id="rrq_yomu">
                        <div class="team-info">
                            <div class="team-logo">RY</div>
                            <span class="team-name">RRQ Yomu</span>
                        </div>
                        <span class="team-score">0</span>
                    </div>
                </div>

                {{-- Match 6 --}}
                <div class="match-card" data-match-id="6" id="card_match_6">
                    <div class="match-header">
                        <span>BRACKET 6</span>
                        <span class="match-time"><i class="bi bi-clock"></i> Besok, 20:00</span>
                    </div>
                    <div class="team-row" data-team-id="onic_pro">
                        <div class="team-info">
                            <div class="team-logo">OP</div>
                            <span class="team-name">Onic Pro</span>
                        </div>
                        <span class="team-score">-</span>
                    </div>
                    <div class="team-row" data-team-id="aura_fire">
                        <div class="team-info">
                            <div class="team-logo">AF</div>
                            <span class="team-name">Aura Fire Junior</span>
                        </div>
                        <span class="team-score">-</span>
                    </div>
                </div>

                {{-- SVG Connectors to Grand Final --}}
                <svg class="connector-svg">
                    <path class="connector-line" id="line_5" d="M 0 180 L 35 180 L 35 340 L 70 340" />
                    <path class="connector-line" id="line_6" d="M 0 500 L 35 500 L 35 340 L 70 340" />
                </svg>
            </div>

            {{-- ROUND 3: GRAND FINAL --}}
            <div class="bracket-round" id="round_final">
                <span class="round-title">Grand Final</span>
                
                {{-- Match 7 --}}
                <div class="match-card" data-match-id="7" id="card_match_7">
                    <div class="match-header">
                        <span>BRACKET 7</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 2 Juli, 20:00</span>
                    </div>
                    <div class="team-row" data-team-id="yomuda_star">
                        <div class="team-info">
                            <div class="team-logo">YS</div>
                            <span class="team-name">Yomuda Star</span>
                        </div>
                        <span class="team-score">-</span>
                    </div>
                    <div class="team-row">
                        <div class="team-info">
                            <div class="team-logo">?</div>
                            <span class="team-name text-muted">Pemenang Bracket 6</span>
                        </div>
                        <span class="team-score">-</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Drag to scroll functionality
        const slider = document.getElementById('bracketContainer');
        let isDown = false;
        let startX;
        let scrollLeft;

        slider.addEventListener('mousedown', (e) => {
            isDown = true;
            slider.style.cursor = 'grabbing';
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        
        slider.addEventListener('mouseleave', () => {
            isDown = false;
            slider.style.cursor = 'grab';
        });
        
        slider.addEventListener('mouseup', () => {
            isDown = false;
            slider.style.cursor = 'grab';
        });
        
        slider.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 1.5; //scroll-fastness
            slider.scrollLeft = scrollLeft - walk;
        });

        // Hover Highlighting Logic
        const teamRows = document.querySelectorAll('.team-row[data-team-id]');
        
        // Define paths connecting matches
        const teamLines = {
            'yomuda_star': ['line_1', 'line_5'],
            'rrq_yomu': ['line_2'],
            'onic_pro': ['line_3'],
            'aura_fire': ['line_4'],
            'sans_esport': [],
            'evos_wann': [],
            'alter_ego': [],
            'geek_fam': []
        };

        teamRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                const teamId = this.dataset.teamId;
                if (!teamId) return;

                document.querySelectorAll(`.team-row[data-team-id="${teamId}"]`).forEach(el => {
                    el.classList.add('team-highlighted');
                });

                const lines = teamLines[teamId] || [];
                lines.forEach(lineId => {
                    const lineEl = document.getElementById(lineId);
                    if (lineEl) {
                        lineEl.classList.add('highlighted');
                    }
                });
            });

            row.addEventListener('mouseleave', function() {
                const teamId = this.dataset.teamId;
                if (!teamId) return;

                document.querySelectorAll(`.team-row[data-team-id="${teamId}"]`).forEach(el => {
                    el.classList.remove('team-highlighted');
                });

                const lines = teamLines[teamId] || [];
                lines.forEach(lineId => {
                    const lineEl = document.getElementById(lineId);
                    if (lineEl) {
                        lineEl.classList.remove('highlighted');
                    }
                });
            });
        });

        // Simulated Search Database
        const teamDatabase = {
            'yomuda star': {
                name: 'Yomuda Star',
                opponent: 'Onic Pro / Aura Fire',
                schedule: '2 Juli, 20:00 WIB',
                bracket: 'Bracket 7 (Grand Final)',
                round: 'Babak 3 (Grand Final) - Babak 2 Selesai',
                status: 'Mendatang',
                cardId: 'card_match_7'
            },
            'rrq yomu': {
                name: 'RRQ Yomu',
                opponent: 'Yomuda Star',
                schedule: 'LIVE 22:40 WIB',
                bracket: 'Bracket 5 (Semifinal)',
                round: 'Babak 2 (Semifinal) - Babak 1 Selesai',
                status: 'Kalah (Eliminasi)',
                cardId: 'card_match_5'
            },
            'onic pro': {
                name: 'Onic Pro',
                opponent: 'Aura Fire Junior',
                schedule: 'Besok, 20:00 WIB',
                bracket: 'Bracket 6 (Semifinal)',
                round: 'Babak 2 (Semifinal) - Babak 1 Selesai',
                status: 'Mendatang',
                cardId: 'card_match_6'
            },
            'aura fire junior': {
                name: 'Aura Fire Junior',
                opponent: 'Onic Pro',
                schedule: 'Besok, 20:00 WIB',
                bracket: 'Bracket 6 (Semifinal)',
                round: 'Babak 2 (Semifinal) - Babak 1 Selesai',
                status: 'Mendatang',
                cardId: 'card_match_6'
            },
            'sans esports': {
                name: 'Sans Esports',
                opponent: 'Yomuda Star',
                schedule: 'Selesai (20:00 WIB)',
                bracket: 'Bracket 1 (Perempat Final)',
                round: 'Babak 1 (Perempat Final) - Belum Selesai',
                status: 'Kalah (Eliminasi)',
                cardId: 'card_match_1'
            },
            'evos wann': {
                name: 'Evos Wann',
                opponent: 'RRQ Yomu',
                schedule: 'Selesai (20:40 WIB)',
                bracket: 'Bracket 2 (Perempat Final)',
                round: 'Babak 1 (Perempat Final) - Belum Selesai',
                status: 'Kalah (Eliminasi)',
                cardId: 'card_match_2'
            },
            'alter ego y': {
                name: 'Alter Ego Y',
                opponent: 'Onic Pro',
                schedule: 'Selesai (21:20 WIB)',
                bracket: 'Bracket 3 (Perempat Final)',
                round: 'Babak 1 (Perempat Final) - Belum Selesai',
                status: 'Kalah (Eliminasi)',
                cardId: 'card_match_3'
            },
            'geek fam x': {
                name: 'Geek Fam X',
                opponent: 'Aura Fire Junior',
                schedule: 'Selesai (22:00 WIB)',
                bracket: 'Bracket 4 (Perempat Final)',
                round: 'Babak 1 (Perempat Final) - Belum Selesai',
                status: 'Kalah (Eliminasi)',
                cardId: 'card_match_4'
            }
        };

        // Real-time Search Logic
        const searchInput = document.getElementById('teamSearchInput');
        const resultCard = document.getElementById('searchResultCard');
        const btnFocus = document.getElementById('btnFocusBracket');
        let activeFocusedCardId = null;

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            document.querySelectorAll('.match-card').forEach(card => card.classList.remove('focus-glow'));

            if (!query) {
                resultCard.style.display = 'none';
                return;
            }

            let foundKey = null;
            Object.keys(teamDatabase).forEach(key => {
                if (key.includes(query)) {
                    foundKey = key;
                }
            });

            if (foundKey) {
                const matchData = teamDatabase[foundKey];
                
                document.getElementById('resTeamName').textContent = matchData.name;
                
                const statusBadge = document.getElementById('resMatchStatus');
                statusBadge.textContent = matchData.status;
                if (matchData.status.includes('Kalah')) {
                    statusBadge.className = 'badge bg-secondary rounded-pill px-2.5 py-1';
                } else if (matchData.schedule.includes('LIVE')) {
                    statusBadge.className = 'badge bg-danger rounded-pill px-2.5 py-1';
                } else {
                    statusBadge.className = 'badge bg-success rounded-pill px-2.5 py-1';
                }

                document.getElementById('resOpponent').textContent = matchData.opponent;
                document.getElementById('resSchedule').textContent = matchData.schedule;
                document.getElementById('resBracketLabel').textContent = matchData.bracket;
                document.getElementById('resRoundLabel').textContent = matchData.round;

                activeFocusedCardId = matchData.cardId;
                resultCard.style.display = 'block';
            } else {
                document.getElementById('resTeamName').textContent = 'Tim tidak ditemukan';
                document.getElementById('resMatchStatus').textContent = '-';
                document.getElementById('resMatchStatus').className = 'badge bg-secondary rounded-pill px-2.5 py-1';
                document.getElementById('resOpponent').textContent = 'Tidak ada';
                document.getElementById('resSchedule').textContent = '-';
                document.getElementById('resBracketLabel').textContent = '-';
                document.getElementById('resRoundLabel').textContent = 'Periksa ejaan nama tim Anda';
                activeFocusedCardId = null;
                resultCard.style.display = 'block';
            }
        });

        // Bugfix: Focus Button Event Handler with correct relative offsets (independent of CSS absolute/relative parents)
        btnFocus.addEventListener('click', function() {
            if (!activeFocusedCardId) return;

            const cardElement = document.getElementById(activeFocusedCardId);
            if (!cardElement) return;

            // Highlight the card
            document.querySelectorAll('.match-card').forEach(card => card.classList.remove('focus-glow'));
            cardElement.classList.add('focus-glow');

            // Scroll container to the card's position (horizontally)
            const container = document.getElementById('bracketContainer');
            
            // Calculate position using getBoundingClientRect to bypass parent elements absolute/relative offsets
            const containerRect = container.getBoundingClientRect();
            const cardRect = cardElement.getBoundingClientRect();
            
            const relativeLeft = cardRect.left - containerRect.left + container.scrollLeft;
            const targetScrollLeft = relativeLeft - (containerRect.width / 2) + (cardRect.width / 2);

            container.scrollTo({
                left: targetScrollLeft,
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>
