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
            --border-light: rgba(255, 255, 255, 0.08);
            --card-bg: rgba(15, 23, 42, 0.65);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-primary);
            color: #ffffff;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 20% 30%, rgba(245, 158, 11, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(59, 130, 246, 0.05) 0%, transparent 40%);
        }

        /* Header Style */
        .bracket-header {
            padding: 40px 0 20px 0;
            border-bottom: 1px solid var(--border-light);
            background: linear-gradient(180deg, rgba(11, 19, 41, 0.8) 0%, transparent 100%);
        }

        .brand-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--accent-gold) 0%, #d97706 100%);
            color: #020617;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            font-size: 1.25rem;
            font-weight: 800;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
            margin-right: 12px;
        }

        /* Bracket View Wrapper */
        .bracket-container {
            padding: 50px 20px;
            overflow-x: auto;
            white-space: nowrap;
            cursor: grab;
            user-select: none;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-gold) var(--bg-secondary);
        }

        .bracket-container:active {
            cursor: grabbing;
        }

        .bracket-container::-webkit-scrollbar {
            height: 8px;
        }

        .bracket-container::-webkit-scrollbar-track {
            background: var(--bg-secondary);
            border-radius: 4px;
        }

        .bracket-container::-webkit-scrollbar-thumb {
            background: var(--accent-gold);
            border-radius: 4px;
        }

        /* Bracket Columns/Rounds */
        .bracket-round {
            display: inline-flex;
            flex-direction: column;
            justify-content: space-around;
            height: 640px; /* Base height for vertical alignment */
            vertical-align: middle;
            margin-right: 70px;
            position: relative;
        }

        .round-title {
            position: absolute;
            top: -35px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
            color: var(--accent-gold);
            opacity: 0.9;
        }

        /* Match Card Styles */
        .match-card {
            background: var(--card-bg);
            border: 1px solid var(--border-light);
            border-radius: 14px;
            width: 250px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            z-index: 2;
        }

        .match-card:hover {
            border-color: rgba(245, 158, 11, 0.3);
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.1);
            transform: translateY(-2px);
        }

        /* Match Schedule Info Row */
        .match-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-bottom: 1px solid var(--border-light);
            font-size: 0.72rem;
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
            padding: 10px 12px;
            position: relative;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .team-row:first-of-type {
            border-bottom: 1px solid var(--border-light);
        }

        .team-row.winner {
            background: rgba(245, 158, 11, 0.03);
        }

        .team-row.loser {
            opacity: 0.45;
        }

        .team-row:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        .team-info {
            display: flex;
            align-items: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .team-logo {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.72rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .team-name {
            font-size: 0.84rem;
            font-weight: 600;
            color: #ffffff;
            transition: color 0.2s ease;
        }

        .team-score {
            font-size: 0.95rem;
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
            font-size: 0.78rem;
            margin-left: 6px;
        }

        /* Interactive Path Glow Style */
        .team-highlighted .team-name {
            color: var(--accent-gold) !important;
            text-shadow: 0 0 8px rgba(245, 158, 11, 0.5);
        }
        
        .team-highlighted .team-logo {
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 8px rgba(245, 158, 11, 0.3);
        }

        /* Bracket connecting lines */
        .connector-svg {
            position: absolute;
            top: 0;
            left: 250px;
            width: 70px;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .connector-line {
            fill: none;
            stroke: rgba(255, 255, 255, 0.08);
            stroke-width: 2;
            transition: stroke 0.3s ease, stroke-width 0.3s ease;
        }

        /* Glow active connector line */
        .connector-line.highlighted {
            stroke: var(--accent-gold);
            stroke-width: 3;
            filter: drop-shadow(0 0 3px rgba(245, 158, 11, 0.5));
        }

        @keyframes pulse-live {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }

        /* Back to Admin Home Button */
        .btn-back-admin {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-back-admin:hover {
            color: var(--accent-gold);
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="bracket-header">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span class="brand-logo">Y</span>
                <div>
                    <h4 class="fw-bold m-0" style="letter-spacing: 0.5px;">YOMUDA <span class="fw-light text-warning">CHAMPIONSHIP</span></h4>
                    <p class="text-secondary m-0 small">Bagan Turnamen Native - Pratinjau Desain Front-End</p>
                </div>
            </div>
            <div>
                <a href="{{ route('admin.seasons') }}" class="btn-back-admin">
                    <i class="bi bi-arrow-left"></i> Kembali ke Panel Admin
                </a>
            </div>
        </div>
    </header>

    {{-- Interactive Info Box --}}
    <div class="container mt-4">
        <div class="alert border-0 rounded-4 p-3 d-flex align-items-center gap-3" style="background-color: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.15) !important;">
            <i class="bi bi-info-circle-fill text-warning fs-3"></i>
            <div>
                <h6 class="fw-bold text-warning mb-0.5">Informasi Demonstrasi Interaktif</h6>
                <p class="text-white-50 m-0 small" style="line-height: 1.4;">
                    Dekatkan kursor (*hover*) pada salah satu nama tim di bawah ini untuk menyalakan jalur histori tanding mereka! Anda juga dapat **klik dan geser (*drag*)** area bagan secara horizontal.
                </p>
            </div>
        </div>
    </div>

    {{-- Bracket Field Wrapper --}}
    <div class="container-fluid px-0">
        <div class="bracket-container" id="bracketContainer">
            
            {{-- ROUND 1: QUARTERFINALS --}}
            <div class="bracket-round">
                <span class="round-title">Perempat Final</span>
                
                {{-- Match 1 --}}
                <div class="match-card" data-match-id="1">
                    <div class="match-header">
                        <span>BRACKET 1</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 19:00 WIB</span>
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
                <div class="match-card" data-match-id="2">
                    <div class="match-header">
                        <span>BRACKET 2</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 20:00 WIB</span>
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
                <div class="match-card" data-match-id="3">
                    <div class="match-header">
                        <span>BRACKET 3</span>
                        <span class="match-time"><i class="bi bi-clock"></i> 21:00 WIB</span>
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
                <div class="match-card" data-match-id="4">
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
            <div class="bracket-round">
                <span class="round-title">Semifinal</span>
                
                {{-- Match 5 --}}
                <div class="match-card" data-match-id="5">
                    <div class="match-header">
                        <span>BRACKET 5</span>
                        <span class="match-time live"><i class="bi bi-broadcast"></i> LIVE NOW</span>
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
                <div class="match-card" data-match-id="6">
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
            <div class="bracket-round">
                <span class="round-title">Grand Final</span>
                
                {{-- Match 7 --}}
                <div class="match-card" data-match-id="7">
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
                            <span class="team-name text-muted">Pemenang Match 6</span>
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
        
        // Define paths connecting matches (which match outputs feed to which next lines)
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

                // 1. Highlight all rows with same team
                document.querySelectorAll(`.team-row[data-team-id="${teamId}"]`).forEach(el => {
                    el.classList.add('team-highlighted');
                });

                // 2. Glow matching connector lines
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

                // 1. Remove highlight
                document.querySelectorAll(`.team-row[data-team-id="${teamId}"]`).forEach(el => {
                    el.classList.remove('team-highlighted');
                });

                // 2. Remove glow from connector lines
                const lines = teamLines[teamId] || [];
                lines.forEach(lineId => {
                    const lineEl = document.getElementById(lineId);
                    if (lineEl) {
                        lineEl.classList.remove('highlighted');
                    }
                });
            });
        });
    });
    </script>
</body>
</html>
