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
            --border-light: rgba(255, 255, 255, 0.05);
            --card-bg: rgba(15, 23, 42, 0.8);
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

        /* Bracket View Wrapper */
        .bracket-container {
            padding: 50px 30px;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            cursor: grab;
            user-select: none;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-gold) var(--bg-secondary);
            scroll-behavior: smooth;
            transform: translate3d(0, 0, 0);
            will-change: scroll-position;
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

        /* Dynamic Heights according to rounds for 128 Teams */
        .bracket-round {
            display: inline-flex;
            flex-direction: column;
            justify-content: space-around;
            height: 4800px; /* High height to distribute 64 match cards in Round 1 */
            vertical-align: middle;
            margin-right: 90px;
            position: relative;
        }

        .round-title {
            position: absolute;
            top: -30px;
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
            width: 230px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
            position: relative;
            z-index: 2;
        }

        .match-card:hover {
            border-color: rgba(245, 158, 11, 0.35);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.15);
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
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .match-time {
            color: var(--accent-gold);
            display: flex;
            align-items: center;
            gap: 4px;
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

        /* Dynamic connector paths styling */
        .round-connectors {
            position: absolute;
            top: 0;
            left: 230px;
            width: 90px;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .connector-line {
            fill: none;
            stroke: rgba(255, 255, 255, 0.05);
            stroke-width: 1.5;
            transition: stroke 0.25s ease, stroke-width 0.25s ease;
        }

        .connector-line.highlighted {
            stroke: var(--accent-gold);
            stroke-width: 2.5;
            filter: drop-shadow(0 0 2px rgba(245, 158, 11, 0.4));
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
                <input type="text" id="teamSearchInput" autocomplete="off" placeholder="Cari nama tim Anda (cth: Tim 42)...">
                <button class="search-icon-btn"><i class="bi bi-search"></i></button>
            </div>

            {{-- Live Search Result Card --}}
            <div id="searchResultCard" class="search-results-panel">
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25 pb-2 mb-2.5">
                    <h6 class="fw-bold text-warning mb-0" id="resTeamName" style="font-size: 0.9rem;">Nama Tim</h6>
                    <span class="badge bg-success rounded-pill px-2.5 py-1" id="resMatchStatus" style="font-size: 0.65rem;">Selesai</span>
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
                        <strong class="text-white" id="resRoundLabel">Babak 1</strong>
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
            {{-- Rounds are generated dynamically here by JS to support 128 Teams smoothly & optimally --}}
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const teamCount = 128;
        const roundsCount = Math.log2(teamCount); // 7 Rounds for 128 teams
        
        // Staggered times per ROUND:
        const roundTimes = {
            1: "20:00 WIB",
            2: "20:40 WIB",
            3: "21:20 WIB",
            4: "22:00 WIB",
            5: "22:40 WIB",
            6: "23:20 WIB",
            7: "24:00 WIB"
        };

        const roundNames = {
            1: "Babak 128 Besar",
            2: "Babak 64 Besar",
            3: "Babak 32 Besar",
            4: "16 Besar",
            5: "Perempat Final",
            6: "Semifinal",
            7: "Grand Final"
        };

        // 1. Generate 128 Dummy Teams
        const teams = [];
        for (let i = 1; i <= teamCount; i++) {
            teams.push({
                id: `team_${i}`,
                name: `Tim ${i}`,
                logo: `T${i}`
            });
        }

        // 2. Generate Tournament Matches data structures
        const matchesData = {}; // Stores details for search index
        const container = document.getElementById('bracketContainer');

        let currentRoundTeams = [...teams];
        
        // Loop rounds from 1 to 7
        for (let round = 1; round <= roundsCount; round++) {
            const matchesInRound = currentRoundTeams.length / 2;
            const roundDiv = document.createElement('div');
            roundDiv.className = 'bracket-round';
            roundDiv.id = `round_${round}`;
            
            // Set Round Title
            const titleSpan = document.createElement('span');
            titleSpan.className = 'round-title';
            titleSpan.textContent = roundNames[round];
            roundDiv.appendChild(titleSpan);

            const nextRoundTeams = [];
            const svgConnectors = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svgConnectors.setAttribute("class", "round-connectors");

            // Calculate heights & gaps for connectors
            // Round height is 4800px.
            // Match card height with spacing is distributed vertically by justify-around.
            const cardHeight = 74;
            const roundHeight = 4800;

            for (let match = 1; match <= matchesInRound; match++) {
                const team1 = currentRoundTeams[(match - 1) * 2];
                const team2 = currentRoundTeams[(match - 1) * 2 + 1];
                
                // Simulate results: Tim with smaller ID wins
                const t1Score = Math.floor(Math.random() * 2) + 1; // 1 or 2
                const t2Score = t1Score === 2 ? Math.floor(Math.random() * 2) : 2; // Make sure one reaches 2
                
                const winner = t1Score > t2Score ? team1 : team2;
                const loser = winner === team1 ? team2 : team1;
                nextRoundTeams.push(winner);

                const matchId = `match_${round}_${match}`;
                
                // Create Match Card
                const cardDiv = document.createElement('div');
                cardDiv.className = 'match-card';
                cardDiv.id = `card_${matchId}`;
                cardDiv.setAttribute('data-match-id', matchId);

                // Staggered Time per Round
                const matchTime = roundTimes[round];

                cardDiv.innerHTML = `
                    <div class="match-header">
                        <span>BRACKET ${match}</span>
                        <span class="match-time"><i class="bi bi-clock"></i> ${matchTime}</span>
                    </div>
                    <div class="team-row ${winner === team1 ? 'winner' : 'loser'}" data-team-id="${team1.id}">
                        <div class="team-info">
                            <div class="team-logo">${team1.logo}</div>
                            <span class="team-name">${team1.name}</span>
                            ${winner === team1 ? '<i class="bi bi-patch-check-fill winner-badge"></i>' : ''}
                        </div>
                        <span class="team-score">${t1Score}</span>
                    </div>
                    <div class="team-row ${winner === team2 ? 'winner' : 'loser'}" data-team-id="${team2.id}">
                        <div class="team-info">
                            <div class="team-logo">${team2.logo}</div>
                            <span class="team-name">${team2.name}</span>
                            ${winner === team2 ? '<i class="bi bi-patch-check-fill winner-badge"></i>' : ''}
                        </div>
                        <span class="team-score">${t2Score}</span>
                    </div>
                `;

                roundDiv.appendChild(cardDiv);

                // Index search details for this team
                [team1, team2].forEach(t => {
                    const isWin = (t === winner);
                    const opp = (t === team1 ? team2 : team1);
                    
                    // If team won, check their next round status (simulation)
                    matchesData[t.name.toLowerCase()] = {
                        name: t.name,
                        opponent: opp.name,
                        schedule: matchTime,
                        bracket: `Bracket ${match} (${roundNames[round]})`,
                        round: `${roundNames[round]} - ${isWin ? 'Lolos ke Babak Berikutnya' : 'Kalah (Eliminasi)'}`,
                        status: isWin ? 'Lolos' : 'Kalah (Eliminasi)',
                        cardId: `card_${matchId}`
                    };
                });

                // Generate SVG path connectors dynamically if not the final round
                if (round < roundsCount) {
                    const nextMatchIndex = Math.ceil(match / 2);
                    const isTopBranch = (match % 2 !== 0);
                    
                    // Connectors are drawn dynamically in JS
                    const startY = (roundHeight / matchesInRound) * (match - 0.5);
                    const endY = (roundHeight / (matchesInRound / 2)) * (nextMatchIndex - 0.5);
                    const midX = 45;

                    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                    path.setAttribute("class", "connector-line");
                    path.setAttribute("id", `line_${round}_${match}`);
                    
                    // Draw cubic/orthogonal line path
                    const pathData = `M 0 ${startY} L ${midX} ${startY} L ${midX} ${endY} L 90 ${endY}`;
                    path.setAttribute("d", pathData);
                    svgConnectors.appendChild(path);
                }
            }

            if (round < roundsCount) {
                roundDiv.appendChild(svgConnectors);
            }

            container.appendChild(roundDiv);
            currentRoundTeams = nextRoundTeams;
        }

        // Drag to scroll functionality
        let isDown = false;
        let startX;
        let scrollLeft;

        slider = document.getElementById('bracketContainer');
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
            const walk = (x - startX) * 1.5;
            slider.scrollLeft = scrollLeft - walk;
        });

        // Hover Highlighting Logic
        const teamRows = document.querySelectorAll('.team-row[data-team-id]');
        teamRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                const teamId = this.dataset.teamId;
                if (!teamId) return;

                document.querySelectorAll(`.team-row[data-team-id="${teamId}"]`).forEach(el => {
                    el.classList.add('team-highlighted');
                });
            });

            row.addEventListener('mouseleave', function() {
                const teamId = this.dataset.teamId;
                if (!teamId) return;

                document.querySelectorAll(`.team-row[data-team-id="${teamId}"]`).forEach(el => {
                    el.classList.remove('team-highlighted');
                });
            });
        });

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

            // Find matching team in dynamic database
            let foundKey = null;
            Object.keys(matchesData).forEach(key => {
                if (key === query || key.includes(query)) {
                    foundKey = key;
                }
            });

            if (foundKey) {
                const matchData = matchesData[foundKey];
                
                document.getElementById('resTeamName').textContent = matchData.name;
                
                const statusBadge = document.getElementById('resMatchStatus');
                statusBadge.textContent = matchData.status;
                if (matchData.status.includes('Kalah')) {
                    statusBadge.className = 'badge bg-secondary rounded-pill px-2.5 py-1';
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
                document.getElementById('resRoundLabel').textContent = 'Contoh pencarian: Tim 42';
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

            // Scroll container to the card's position (both horizontally and vertically!)
            const container = document.getElementById('bracketContainer');
            
            const containerRect = container.getBoundingClientRect();
            const cardRect = cardElement.getBoundingClientRect();
            
            // Calculate relative scroll positions
            const relativeLeft = cardRect.left - containerRect.left + container.scrollLeft;
            const targetScrollLeft = relativeLeft - (containerRect.width / 2) + (cardRect.width / 2);

            container.scrollTo({
                left: targetScrollLeft,
                behavior: 'smooth'
            });

            // Smooth scroll vertically to center the card on screen as well!
            const relativeTop = cardRect.top - containerRect.top + window.scrollY;
            window.scrollTo({
                top: relativeTop - (window.innerHeight / 2) + (cardRect.height / 2),
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>
