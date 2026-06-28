<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bagan Turnamen - Yomuda Championship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --bg-primary: #1e1e24;
            --bg-secondary: #141416;
            --bg-card: #2d2d35;
            --border-color: #3f3f46;
            --accent-orange: #ff7a00;
            --text-light: #f4f4f5;
            --text-dim: #a1a1aa;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-light);
            overflow: hidden; /* Lock the main window scroll to keep it neat like Challonge */
            -webkit-font-smoothing: antialiased;
        }

        /* Header Style */
        .bracket-header {
            padding: 12px 0;
            background-color: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
        }

        /* Search Box style */
        .search-wrapper {
            max-width: 360px;
            margin: 12px auto;
            position: relative;
            z-index: 999; /* Higher z-index stack for wrapper */
        }

        .search-input-group {
            background-color: #27272a;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 2px 4px;
        }

        .search-input-group:focus-within {
            border-color: var(--accent-orange);
            box-shadow: 0 0 10px rgba(255, 122, 0, 0.2);
        }

        .search-input-group input {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 0.78rem;
            outline: none;
            padding: 5px 8px;
            width: 100%;
        }

        .search-input-group input::placeholder {
            color: var(--text-dim);
        }

        .search-icon-btn {
            background: transparent;
            border: none;
            color: var(--text-dim);
            padding: 0 6px;
        }

        /* Search Results Panel with absolute overlay stack */
        .search-results-panel {
            background-color: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 12px;
            margin-top: 8px;
            text-align: left;
            font-size: 0.75rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.6);
            position: absolute;
            width: 100%;
            left: 0;
            z-index: 99999; /* Force overlay on top of sticky headers and SVGs */
        }

        /* Sticky Round Titles Bar */
        .round-headers-bar {
            display: flex;
            background-color: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            padding: 8px 30px;
            white-space: nowrap;
            overflow-x: hidden;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 5; /* Lower than search wrapper */
        }

        .round-header-item {
            width: 185px; /* Compact Column Width */
            margin-right: 80px; /* Matching column spacing */
            flex-shrink: 0;
            text-align: center;
        }

        /* Bracket container layout - BOTH horizontal and vertical scroll inside the container */
        .bracket-container {
            padding: 30px 30px 40px 30px;
            overflow: auto; /* Allow horizontal & vertical scrolling inside container */
            white-space: nowrap;
            cursor: grab;
            user-select: none;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-orange) var(--bg-secondary);
            scroll-behavior: smooth;
            height: calc(100vh - 145px); /* Responsive viewport fitting */
            transform: translate3d(0, 0, 0);
            will-change: scroll-position;
            position: relative;
            z-index: 1; /* Lowest layout stack */
        }

        .bracket-container:active {
            cursor: grabbing;
        }

        .bracket-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .bracket-container::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        .bracket-container::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        .bracket-container::-webkit-scrollbar-thumb:hover {
            background: var(--accent-orange);
        }

        /* Bracket Column per Round */
        .bracket-round {
            display: inline-flex;
            flex-direction: column;
            justify-content: space-around;
            height: 4600px; /* Precise height containing the tree */
            vertical-align: top;
            width: 185px;
            margin-right: 80px;
            position: relative;
        }

        /* Challonge Match Card (Ultra Compact & Sharp) */
        .match-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            width: 185px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
            transition: all 0.2s ease;
        }

        .match-card:hover {
            border-color: #52525b;
        }

        .match-card.focus-glow {
            border-color: var(--accent-orange) !important;
            box-shadow: 0 0 15px rgba(255, 122, 0, 0.6) !important;
            transform: scale(1.04);
        }

        /* Match Card Header Row for Label & Time */
        .match-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #202024;
            border-bottom: 1px solid var(--border-color);
            padding: 3px 6px;
            font-size: 0.58rem;
            font-weight: 700;
            color: var(--text-dim);
        }

        .match-card-time {
            color: var(--accent-orange);
        }

        /* Team Row - Compact Height 22px */
        .team-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 22px;
            font-size: 0.68rem;
            padding-left: 6px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03);
            background-color: var(--bg-card);
            color: var(--text-light);
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .team-row:last-of-type {
            border-bottom: none;
        }

        .team-row:hover {
            background-color: #373740;
        }

        .team-info {
            display: flex;
            align-items: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            flex-grow: 1;
        }

        /* Seed indicator */
        .team-seed {
            font-size: 0.58rem;
            color: var(--text-dim);
            margin-right: 5px;
            font-weight: 600;
        }

        .team-name {
            font-weight: 500;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Challonge Score Box - Compact Width/Height 22px */
        .team-score-box {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.7rem;
            background-color: #202024;
            color: var(--text-dim);
            border-left: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .team-row.winner {
            background-color: rgba(255, 122, 0, 0.02);
        }

        .team-row.winner .team-name {
            color: #ffffff;
            font-weight: 600;
        }

        .team-row.winner .team-score-box {
            background-color: var(--accent-orange);
            color: #000000;
        }

        .team-row.loser {
            opacity: 0.45;
        }

        /* Path Highlighting */
        .team-highlighted {
            background-color: #373740 !important;
        }
        .team-highlighted .team-name {
            color: var(--accent-orange) !important;
        }

        /* Orthogonal Connector Lines (Challonge-like SVG) */
        .round-connectors {
            position: absolute;
            top: 0;
            left: 185px;
            width: 80px;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .connector-line {
            fill: none;
            stroke: #44444f;
            stroke-width: 1.5;
            transition: stroke 0.2s ease, stroke-width 0.2s ease;
        }

        .connector-line.highlighted {
            stroke: var(--accent-orange);
            stroke-width: 2.2;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="bracket-header">
        <div class="container text-center">
            <h5 class="fw-bold m-0" style="letter-spacing: 0.5px; font-size: 1.1rem;">YOMUDA <span class="text-warning">SEASON 33</span></h5>
            <p class="text-secondary m-0" style="font-size: 0.72rem;">Bagan Tournament Yomuda</p>
        </div>
    </header>

    {{-- Search Area --}}
    <div class="container mt-2">
        <div class="search-wrapper text-center">
            <div class="search-input-group d-flex align-items-center">
                <input type="text" id="teamSearchInput" autocomplete="off" placeholder="Cari nama tim Anda (cth: Tim 42)...">
                <button class="search-icon-btn"><i class="bi bi-search"></i></button>
            </div>

            {{-- Result Panel --}}
            <div id="searchResultCard" class="search-results-panel">
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25 pb-1.5 mb-2">
                    <strong class="text-warning" id="resTeamName">Nama Tim</strong>
                    <span class="badge bg-success rounded-pill px-2.5 py-0.5" id="resMatchStatus" style="font-size: 0.6rem;">Selesai</span>
                </div>
                <div class="row g-2 mb-2 text-white-50" style="font-size: 0.75rem;">
                    <div class="col-6">Opponent: <strong class="text-white" id="resOpponent">Tim Lawan</strong></div>
                    <div class="col-6">Jadwal: <strong class="text-warning" id="resSchedule">Jam Tanding</strong></div>
                    <div class="col-6">Babak: <strong class="text-white" id="resRoundLabel">Babak 1</strong></div>
                    <div class="col-6">Bracket: <strong class="text-white" id="resBracketLabel">Bracket 1</strong></div>
                </div>
                <div class="text-end">
                    <button class="btn btn-warning btn-sm fw-bold px-2.5 py-1 rounded-pill text-dark" id="btnFocusBracket" style="font-size: 0.72rem;">
                        Fokuskan ke Bagan
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Round Header Bar --}}
    <div class="round-headers-bar" id="roundHeadersBar">
        <!-- Headers generated by JS -->
    </div>

    {{-- Bracket Field Wrapper --}}
    <div class="bracket-container" id="bracketContainer">
        <!-- Dynamic Columns generated by JS -->
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const teamCount = 128;
        const roundsCount = Math.log2(teamCount); // 7 Rounds
        
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
            1: "Babak 1",
            2: "Babak 2",
            3: "Babak 3",
            4: "Babak 4",
            5: "Perempat",
            6: "Semifinal",
            7: "Grand Final"
        };

        // Render Sticky Headers
        const headerBar = document.getElementById('roundHeadersBar');
        for (let i = 1; i <= roundsCount; i++) {
            const hItem = document.createElement('div');
            hItem.className = 'round-header-item';
            hItem.textContent = roundNames[i];
            headerBar.appendChild(hItem);
        }

        // Generate 128 Teams
        const teams = [];
        for (let i = 1; i <= teamCount; i++) {
            teams.push({
                id: `t_${i}`,
                name: `Tim ${i}`,
                seed: i
            });
        }

        const matchesData = {};
        const container = document.getElementById('bracketContainer');
        let currentRoundTeams = [...teams];
        
        // Height of the inner vertical canvas
        const roundHeight = 4600;

        // Draw rounds
        for (let round = 1; round <= roundsCount; round++) {
            const matchesInRound = currentRoundTeams.length / 2;
            const roundDiv = document.createElement('div');
            roundDiv.className = 'bracket-round';
            roundDiv.id = `round_${round}`;

            const nextRoundTeams = [];
            const svgConnectors = document.createElementNS("http://www.w3.org/2000/svg", "svg");
            svgConnectors.setAttribute("class", "round-connectors");
            svgConnectors.setAttribute("viewBox", `0 0 80 ${roundHeight}`);

            for (let match = 1; match <= matchesInRound; match++) {
                const team1 = currentRoundTeams[(match - 1) * 2];
                const team2 = currentRoundTeams[(match - 1) * 2 + 1];
                
                // Simulate score
                const t1Score = Math.floor(Math.random() * 2) + 1;
                const t2Score = t1Score === 2 ? Math.floor(Math.random() * 2) : 2;
                const winner = t1Score > t2Score ? team1 : team2;
                const loser = winner === team1 ? team2 : team1;
                nextRoundTeams.push(winner);

                const matchId = `m_${round}_${match}`;
                
                const cardDiv = document.createElement('div');
                cardDiv.className = 'match-card';
                cardDiv.id = `card_${matchId}`;

                const matchTime = roundTimes[round];

                cardDiv.innerHTML = `
                    <div class="match-card-header">
                        <span>BRACKET ${match}</span>
                        <span class="match-card-time"><i class="bi bi-clock"></i> ${matchTime}</span>
                    </div>
                    <div class="team-row ${winner === team1 ? 'winner' : 'loser'}" data-team-id="${team1.id}">
                        <div class="team-info">
                            <span class="team-seed">${team1.seed}</span>
                            <span class="team-name">${team1.name}</span>
                        </div>
                        <span class="team-score-box">${t1Score}</span>
                    </div>
                    <div class="team-row ${winner === team2 ? 'winner' : 'loser'}" data-team-id="${team2.id}">
                        <div class="team-info">
                            <span class="team-seed">${team2.seed}</span>
                            <span class="team-name">${team2.name}</span>
                        </div>
                        <span class="team-score-box">${t2Score}</span>
                    </div>
                `;
                
                roundDiv.appendChild(cardDiv);

                // Index details for search
                [team1, team2].forEach(t => {
                    const isWin = (t === winner);
                    matchesData[t.name.toLowerCase()] = {
                        name: t.name,
                        opponent: (t === team1 ? team2 : team1).name,
                        schedule: matchTime,
                        bracket: `Bracket ${match}`,
                        round: roundNames[round],
                        status: isWin ? 'Lolos' : 'Kalah',
                        cardId: `card_${matchId}`
                    };
                });

                // Draw orthogonal connector lines
                if (round < roundsCount) {
                    const nextMatchIndex = Math.ceil(match / 2);
                    
                    const startY = (roundHeight / matchesInRound) * (match - 0.5);
                    const endY = (roundHeight / (matchesInRound / 2)) * (nextMatchIndex - 0.5);
                    const midX = 40;

                    const path = document.createElementNS("http://www.w3.org/2000/svg", "path");
                    path.setAttribute("class", "connector-line");
                    path.setAttribute("id", `line_${round}_${match}`);
                    
                    const pathData = `M 0,${startY} L ${midX},${startY} L ${midX},${endY} L 80,${endY}`;
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

        // Sync sticky header bar horizontal scrolling with bracket scroll
        container.addEventListener('scroll', function() {
            headerBar.scrollLeft = container.scrollLeft;
        });

        // Drag to scroll functionality (Supports both vertical and horizontal scroll via mouse dragging)
        let isDown = false;
        let startX, startY;
        let scrollLeft, scrollTop;

        container.addEventListener('mousedown', (e) => {
            isDown = true;
            container.style.cursor = 'grabbing';
            startX = e.pageX - container.offsetLeft;
            startY = e.pageY - container.offsetTop;
            scrollLeft = container.scrollLeft;
            scrollTop = container.scrollTop;
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });
        
        container.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const y = e.pageY - container.offsetTop;
            const walkX = (x - startX) * 1.5;
            const walkY = (y - startY) * 1.5;
            container.scrollLeft = scrollLeft - walkX;
            container.scrollTop = scrollTop - walkY;
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

        // Search engine
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
                statusBadge.className = matchData.status.includes('Kalah') ? 'badge bg-secondary rounded-pill px-2 py-0.5' : 'badge bg-warning text-dark rounded-pill px-2 py-0.5';

                document.getElementById('resOpponent').textContent = matchData.opponent;
                document.getElementById('resSchedule').textContent = matchData.schedule;
                document.getElementById('resBracketLabel').textContent = matchData.bracket;
                document.getElementById('resRoundLabel').textContent = matchData.round;

                activeFocusedCardId = matchData.cardId;
                resultCard.style.display = 'block';
            } else {
                document.getElementById('resTeamName').textContent = 'Tim tidak ditemukan';
                document.getElementById('resMatchStatus').textContent = '-';
                document.getElementById('resMatchStatus').className = 'badge bg-secondary rounded-pill px-2 py-0.5';
                document.getElementById('resOpponent').textContent = 'Tidak ada';
                document.getElementById('resSchedule').textContent = '-';
                document.getElementById('resBracketLabel').textContent = '-';
                document.getElementById('resRoundLabel').textContent = 'Periksa ejaan nama tim Anda';
                activeFocusedCardId = null;
                resultCard.style.display = 'block';
            }
        });

        // Smooth Auto-scroll Focus (Handles scrolling BOTH vertically and horizontally inside the container rect)
        btnFocus.addEventListener('click', function() {
            if (!activeFocusedCardId) return;

            const cardElement = document.getElementById(activeFocusedCardId);
            if (!cardElement) return;

            document.querySelectorAll('.match-card').forEach(card => card.classList.remove('focus-glow'));
            cardElement.classList.add('focus-glow');

            const containerRect = container.getBoundingClientRect();
            const cardRect = cardElement.getBoundingClientRect();
            
            const relativeLeft = cardRect.left - containerRect.left + container.scrollLeft;
            const targetScrollLeft = relativeLeft - (containerRect.width / 2) + (cardRect.width / 2);

            const relativeTop = cardRect.top - containerRect.top + container.scrollTop;
            const targetScrollTop = relativeTop - (containerRect.height / 2) + (cardRect.height / 2);

            container.scrollTo({
                left: targetScrollLeft,
                top: targetScrollTop,
                behavior: 'smooth'
            });
        });
    });
    </script>
</body>
</html>
