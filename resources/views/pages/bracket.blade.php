<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bagan Turnamen - {{ $season->name }}</title>
    <!-- DNS prefetch and preconnect for Google Fonts to maximize loading speed -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
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

        /* Allow default mobile pull-to-refresh and page scroll while maintaining clean structure */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            width: 100%;
            overscroll-behavior-y: auto;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-secondary);
            color: var(--text-light);
            display: flex;
            flex-direction: column;
            -webkit-font-smoothing: antialiased;
            -webkit-user-select: none;
            user-select: none;
        }

        /* Header Style - Fixed height */
        .bracket-header {
            padding: 10px 0;
            background-color: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        /* Search Area Container - Fixed height */
        .search-area-container {
            padding: 10px 15px;
            background-color: var(--bg-secondary);
            flex-shrink: 0;
            position: relative;
            z-index: 999;
        }

        .search-wrapper {
            max-width: 360px;
            margin: 0 auto;
            position: relative;
        }

        .search-input-group {
            background-color: #27272a;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 2px 4px;
            position: relative;
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
            padding: 5px 30px 5px 8px; /* Extra padding right for clear button */
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
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .search-icon-btn:hover {
            color: var(--accent-orange);
        }

        /* Clear button inside input */
        .search-clear-btn {
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: var(--text-dim);
            font-size: 0.8rem;
            display: none;
            cursor: pointer;
            padding: 0 4px;
        }

        .search-clear-btn:hover {
            color: #ffffff;
        }

        /* Search Results Panel with smooth fade-in */
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
            z-index: 99999;
            display: none;
            animation: fadeIn 0.2s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Sticky Round Titles Bar - Fixed height */
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
            flex-shrink: 0;
            position: relative;
            z-index: 5;
        }

        .round-header-item {
            width: 185px;
            margin-right: 80px;
            flex-shrink: 0;
            text-align: center;
        }

        /* Bracket container layout */
        .bracket-container {
            padding: 30px 30px 40px 30px;
            overflow: auto;
            white-space: nowrap;
            cursor: grab;
            scrollbar-width: thin;
            scrollbar-color: var(--accent-orange) var(--bg-secondary);
            scroll-behavior: smooth;
            flex-grow: 1; /* Takes exactly the remaining viewport height */
            transform: translate3d(0, 0, 0);
            will-change: scroll-position;
            position: relative;
            z-index: 1;
            -webkit-overflow-scrolling: touch; /* Smooth kinetic scroll for iOS Safari */
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
            height: 4600px;
            vertical-align: top;
            width: 185px;
            margin-right: 80px;
            position: relative;
        }

        /* Challonge Match Card */
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

        /* Beautiful glowing focus animation */
        .match-card.focus-glow {
            border-color: var(--accent-orange) !important;
            box-shadow: 0 0 20px rgba(255, 122, 0, 0.7) !important;
            transform: scale(1.04);
            animation: pulse-border 1.2s infinite alternate;
        }

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

        .team-row .text-muted {
            color: #3f3f46 !important;
            opacity: 0.3;
        }

        .italic {
            font-style: italic;
        }

        .team-highlighted {
            background-color: #373740 !important;
        }
        .team-highlighted .team-name {
            color: var(--accent-orange) !important;
        }

        .bronze-match-wrapper {
            position: absolute;
            top: calc(50% + 100px);
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 50;
        }

        .bronze-match-title {
            font-size: 0.6rem;
            font-weight: 800;
            color: var(--accent-orange);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            text-align: center;
        }

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

        /* Action Buttons on Result Card */
        .result-actions-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .btn-whatsapp-chat {
            background-color: #25d366;
            color: #ffffff;
            border: none;
            font-weight: bold;
            font-size: 0.72rem;
            padding: 6px 14px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.2s ease;
        }

        .btn-whatsapp-chat:hover {
            background-color: #1ebe5d;
            color: #ffffff;
        }

        /* ==========================================================================
           RESPONSIVE MOBILE STYLES (Screens <= 576px)
           ========================================================================== */
        @media (max-width: 576px) {
            .round-headers-bar {
                padding: 6px 15px;
            }
            
            .round-header-item {
                width: 155px;
                margin-right: 40px;
            }

            .bracket-container {
                padding: 20px 15px 30px 15px;
            }

            .bracket-round {
                width: 155px;
                margin-right: 40px;
            }

            .match-card {
                width: 155px;
            }

            .round-connectors {
                left: 155px;
                width: 40px;
            }

            .team-row {
                font-size: 0.63rem;
                height: 20px;
            }

            .team-score-box {
                width: 20px;
                height: 20px;
                font-size: 0.65rem;
            }

            .team-seed {
                font-size: 0.52rem;
                margin-right: 4px;
            }

            .match-card-header {
                font-size: 0.54rem;
                padding: 2px 4px;
            }
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
            }
            .chat-box-container {
                width: 280px;
                height: 360px;
                bottom: 60px;
            }
            .chat-input-wrapper input {
                font-size: 16px !important;
            }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="bracket-header">
        <div class="container text-center">
            <h5 class="fw-bold m-0" style="letter-spacing: 0.5px; font-size: 1.05rem;">{{ strtoupper($season->name) }}</h5>
            <p class="text-secondary m-0" style="font-size: 0.68rem;">Bagan Tournament Yomuda</p>
        </div>
    </header>

    {{-- Search Area Container --}}
    <div class="search-area-container">
        <div class="search-wrapper text-center">
            <div class="search-input-group d-flex align-items-center">
                <input type="text" id="teamSearchInput" autocomplete="off" placeholder="Cari nama tim Anda...">
                <button class="search-clear-btn" id="searchClearBtn"><i class="bi bi-x-circle-fill"></i></button>
                <button class="search-icon-btn" id="searchIconBtn"><i class="bi bi-search"></i></button>
            </div>

            {{-- Result Panel --}}
            <div id="searchResultCard" class="search-results-panel">
                <div class="d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25 pb-1.5 mb-2">
                    <strong class="text-warning" id="resTeamName">Nama Tim</strong>
                    <span class="badge bg-success rounded-pill px-2.5 py-0.5" id="resMatchStatus" style="font-size: 0.6rem;">Selesai</span>
                </div>
                <div class="row g-2 mb-2.5 text-white-50" style="font-size: 0.75rem;">
                    <div class="col-6">Team Musuh: <strong class="text-white" id="resOpponent">Tim Lawan</strong></div>
                    <div class="col-6">Nomer WA Musuh: <strong class="text-warning" id="resOpponentWA">-</strong></div>
                    <div class="col-6">Jam Main: <strong class="text-white" id="resSchedule">Jam Tanding</strong></div>
                    <div class="col-6">Babak: <strong class="text-white" id="resRoundLabel">Babak 1</strong></div>
                    <div class="col-6">Bracket: <strong class="text-white" id="resBracketLabel">Bracket 1</strong></div>
                </div>
                <div class="result-actions-wrapper border-top border-secondary border-opacity-25 pt-2">
                    <a href="#" target="_blank" class="btn-whatsapp-chat" id="btnChatWA">
                        <i class="bi bi-whatsapp"></i> Hubungi Musuh
                    </a>
                    <button class="btn btn-warning btn-sm fw-bold px-2.5 py-1 rounded-pill text-dark" id="btnFocusBracket" style="font-size: 0.72rem;">
                        Fokuskan ke Bagan
                    </button>
                    <button class="btn btn-outline-warning btn-sm fw-bold px-2.5 py-1 rounded-pill d-inline-flex align-items-center gap-1" id="btnDownloadMatchday" style="font-size: 0.72rem;">
                        <i class="bi bi-download"></i> Share Matchday
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Round Header Bar --}}
    <div class="round-headers-bar" id="roundHeadersBar">
        @foreach($rounds as $roundNum => $matches)
            <div class="round-header-item">
                @php
                    $names = [1 => "Babak 1", 2 => "Babak 2", 3 => "Babak 3", 4 => "Babak 4", 5 => "Perempat", 6 => "Semifinal", 7 => "Grand Final"];
                    $title = isset($names[$roundNum]) ? $names[$roundNum] : "Babak " . $roundNum;
                @endphp
                {{ $title }}
            </div>
        @endforeach
    </div>

    @php
        $startNumbers = [];
        $currentStart = 1;
        foreach ($rounds as $rNum => $rMatches) {
            $startNumbers[$rNum] = $currentStart;
            $currentStart += $rMatches->count();
        }
    @endphp

    {{-- Bracket Field Wrapper --}}
    <div class="bracket-container" id="bracketContainer">
        @foreach($rounds as $roundNum => $matches)
            @php
                $isFinalRound = ($roundNum === $brackets->max('round_number'));
                $columnMatches = $isFinalRound ? $matches->where('match_number', 1) : $matches;
                $roundHeight = 4600;
                $matchesCount = $columnMatches->count();
                $bronzeMatch = $isFinalRound ? $brackets->where('round_number', $roundNum)->where('match_number', 2)->first() : null;
            @endphp
            <div class="bracket-round">
                @foreach($columnMatches as $match)
                    <div class="match-card" id="card_m_{{ $match->round_number }}_{{ $match->match_number }}">
                        <div class="match-card-header">
                            <span>BRACKET {{ $startNumbers[$roundNum] + ($match->match_number - 1) }}</span>
                            <span class="match-card-time">
                                @if($match->status === 'live')
                                    <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.5rem;">LIVE</span>
                                @else
                                    @php
                                        $timeDisplay = $match->match_time ?? '20:00 WIB';
                                        if (strpos($timeDisplay, ',') !== false) {
                                            $parts = explode(',', $timeDisplay);
                                            $timeDisplay = trim(end($parts));
                                        }
                                    @endphp
                                    <i class="bi bi-clock"></i> {{ $timeDisplay }}
                                @endif
                            </span>
                        </div>
                        
                        {{-- Team 1 Row --}}
                        <div class="team-row {{ $match->winner_id && $match->winner_id === $match->team1_id ? 'winner' : '' }} {{ $match->winner_id && $match->winner_id !== $match->team1_id ? 'loser' : '' }}" data-team-id="{{ $match->team1_id ?? '' }}">
                            <div class="team-info">
                                @if($match->team1)
                                    <span class="team-name">{{ $match->team1->name }}</span>
                                @else
                                    <span class="team-name text-muted italic">TBD</span>
                                @endif
                            </div>
                            <span class="team-score-box">{{ $match->team1_score }}</span>
                        </div>

                        {{-- Team 2 Row --}}
                        <div class="team-row {{ $match->winner_id && $match->winner_id === $match->team2_id ? 'winner' : '' }} {{ $match->winner_id && $match->winner_id !== $match->team2_id ? 'loser' : '' }}" data-team-id="{{ $match->team2_id ?? '' }}">
                            <div class="team-info">
                                @if($match->team2)
                                    <span class="team-name">{{ $match->team2->name }}</span>
                                @else
                                    @if($match->round_number === 1)
                                        <span class="team-name text-success">BYE (Lolos)</span>
                                    @else
                                        <span class="team-name text-muted italic">TBD</span>
                                    @endif
                                @endif
                            </div>
                            <span class="team-score-box">{{ $match->team2_score }}</span>
                        </div>
                    </div>
                @endforeach

                {{-- Draw dynamic SVG connector lines --}}
                @if($roundNum < count($rounds))
                    <svg class="round-connectors" viewBox="0 0 80 {{ $roundHeight }}" preserveAspectRatio="none">
                        @for($m = 1; $m <= $matchesCount; $m++)
                            @php
                                $nextMatchIndex = ceil($m / 2);
                                $startY = ($roundHeight / $matchesCount) * ($m - 0.5);
                                $endY = ($roundHeight / ($matchesCount / 2)) * ($nextMatchIndex - 0.5);
                                $midX = 40;
                            @endphp
                            <path class="connector-line" id="line_{{ $roundNum }}_{{ $m }}" d="M 0,{{ $startY }} L {{ $midX }},{{ $startY }} L {{ $midX }},{{ $endY }} L 80,{{ $endY }}"></path>
                        @endfor
                    </svg>
                @endif

                {{-- Render Bronze Match inside the final column --}}
                @if($isFinalRound && $bronzeMatch)
                    <div class="bronze-match-wrapper">
                        <div class="bronze-match-title">3rd Place Match</div>
                        <div class="match-card" id="card_m_{{ $bronzeMatch->round_number }}_{{ $bronzeMatch->match_number }}">
                            {{-- Team 1 Row --}}
                            <div class="team-row {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id === $bronzeMatch->team1_id ? 'winner' : '' }} {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id !== $bronzeMatch->team1_id ? 'loser' : '' }}" data-team-id="{{ $bronzeMatch->team1_id ?? '' }}">
                                <div class="team-info">
                                    @if($bronzeMatch->team1)
                                        <span class="team-name">{{ $bronzeMatch->team1->name }}</span>
                                    @else
                                        <span class="team-name text-muted italic">TBD</span>
                                    @endif
                                </div>
                                <span class="team-score-box">{{ $bronzeMatch->team1_score }}</span>
                            </div>

                            {{-- Team 2 Row --}}
                            <div class="team-row {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id === $bronzeMatch->team2_id ? 'winner' : '' }} {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id !== $bronzeMatch->team2_id ? 'loser' : '' }}" data-team-id="{{ $bronzeMatch->team2_id ?? '' }}">
                                <div class="team-info">
                                    @if($bronzeMatch->team2)
                                        <span class="team-name">{{ $bronzeMatch->team2->name }}</span>
                                    @else
                                        <span class="team-name text-muted italic">TBD</span>
                                    @endif
                                </div>
                                <span class="team-score-box">{{ $bronzeMatch->team2_score }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const headerBar = document.getElementById('roundHeadersBar');
        const container = document.getElementById('bracketContainer');

        // Sync sticky header bar horizontal scrolling with bracket scroll
        if (container && headerBar) {
            container.addEventListener('scroll', function() {
                headerBar.scrollLeft = container.scrollLeft;
            });

            // Drag to scroll functionality
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
        }

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

        // Search engine database generated dynamically by Blade
        const matchesData = {
            @foreach($brackets as $b)
                @if($b->team1_id && $b->team2_id)
                    "{{ strtolower($b->team1->name) }}": {
                        name: "{{ $b->team1->name }}",
                        opponent: "{{ $b->team2->name }}",
                        opponentWA: "{{ $b->team2->wa_number ?? '-' }}",
                        schedule: "{{ $b->match_time ?? '20:00 WIB' }}",
                        bracket: "Bracket {{ $b->match_number }}",
                        round: "@php
                            $names = [1 => 'Babak 1', 2 => 'Babak 2', 3 => 'Babak 3', 4 => 'Babak 4', 5 => 'Perempat', 6 => 'Semifinal', 7 => 'Grand Final'];
                            echo isset($names[$b->round_number]) ? $names[$b->round_number] : 'Babak ' . $b->round_number;
                        @endphp",
                        status: "{{ $b->winner_id === $b->team1_id ? 'Lolos' : ($b->winner_id ? 'Kalah' : 'Belum Main') }}",
                        cardId: "card_m_{{ $b->round_number }}_{{ $b->match_number }}"
                    },
                    "{{ strtolower($b->team2->name) }}": {
                        name: "{{ $b->team2->name }}",
                        opponent: "{{ $b->team1->name }}",
                        opponentWA: "{{ $b->team1->wa_number ?? '-' }}",
                        schedule: "{{ $b->match_time ?? '20:00 WIB' }}",
                        bracket: "Bracket {{ $b->match_number }}",
                        round: "@php
                            $names = [1 => 'Babak 1', 2 => 'Babak 2', 3 => 'Babak 3', 4 => 'Babak 4', 5 => 'Perempat', 6 => 'Semifinal', 7 => 'Grand Final'];
                            echo isset($names[$b->round_number]) ? $names[$b->round_number] : 'Babak ' . $b->round_number;
                        @endphp",
                        status: "{{ $b->winner_id === $b->team2_id ? 'Lolos' : ($b->winner_id ? 'Kalah' : 'Belum Main') }}",
                        cardId: "card_m_{{ $b->round_number }}_{{ $b->match_number }}"
                    },
                @elseif($b->team1_id && !$b->team2_id && $b->round_number === 1)
                    "{{ strtolower($b->team1->name) }}": {
                        name: "{{ $b->team1->name }}",
                        opponent: "Lolos (BYE)",
                        opponentWA: "-",
                        schedule: "{{ $b->match_time ?? '20:00 WIB' }}",
                        bracket: "Bracket {{ $b->match_number }}",
                        round: "Babak 1",
                        status: "Lolos",
                        cardId: "card_m_{{ $b->round_number }}_{{ $b->match_number }}"
                    },
                @endif
            @endforeach
        };

        const searchInput = document.getElementById('teamSearchInput');
        const resultCard = document.getElementById('searchResultCard');
        const btnFocus = document.getElementById('btnFocusBracket');
        const searchIconBtn = document.getElementById('searchIconBtn');
        const searchClearBtn = document.getElementById('searchClearBtn');
        const btnChatWA = document.getElementById('btnChatWA');
        let activeFocusedCardId = null;

        function performFocusScroll() {
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
        }

        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.match-card').forEach(card => card.classList.remove('focus-glow'));

            if (!query) {
                resultCard.style.display = 'none';
                searchClearBtn.style.display = 'none';
                return;
            }

            searchClearBtn.style.display = 'block';

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
                
                if (matchData.status === 'Lolos') {
                    statusBadge.className = 'badge bg-success rounded-pill px-2 py-0.5';
                } else if (matchData.status === 'Kalah') {
                    statusBadge.className = 'badge bg-secondary rounded-pill px-2 py-0.5';
                } else {
                    statusBadge.className = 'badge bg-warning text-dark rounded-pill px-2 py-0.5';
                }

                document.getElementById('resOpponent').textContent = matchData.opponent;
                document.getElementById('resOpponentWA').textContent = matchData.opponentWA;
                
                let scheduleClean = matchData.schedule;
                if (scheduleClean.includes(',')) {
                    const parts = scheduleClean.split(',');
                    scheduleClean = parts[parts.length - 1].trim();
                }
                document.getElementById('resSchedule').textContent = scheduleClean;
                
                document.getElementById('resBracketLabel').textContent = matchData.bracket;
                document.getElementById('resRoundLabel').textContent = matchData.round;

                if (matchData.opponentWA && matchData.opponentWA !== '-') {
                    const numericWA = matchData.opponentWA.replace(/^0/, '62').replace(/[^\d]/g, '');
                    btnChatWA.href = `https://wa.me/${numericWA}`;
                    btnChatWA.style.display = 'inline-flex';
                } else {
                    btnChatWA.style.display = 'none';
                }

                activeFocusedCardId = matchData.cardId;
                resultCard.style.display = 'block';
            } else {
                document.getElementById('resTeamName').textContent = 'Tim tidak ditemukan';
                document.getElementById('resMatchStatus').textContent = '-';
                document.getElementById('resMatchStatus').className = 'badge bg-secondary rounded-pill px-2 py-0.5';
                document.getElementById('resOpponent').textContent = 'Tidak ada';
                document.getElementById('resOpponentWA').textContent = '-';
                document.getElementById('resSchedule').textContent = '-';
                document.getElementById('resBracketLabel').textContent = '-';
                document.getElementById('resRoundLabel').textContent = 'Periksa ejaan nama tim Anda';
                
                btnChatWA.style.display = 'none';
                activeFocusedCardId = null;
                resultCard.style.display = 'block';
            }
        });

        searchIconBtn.addEventListener('click', function() {
            performFocusScroll();
        });

        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performFocusScroll();
                searchInput.blur();
            }
        });

        searchClearBtn.addEventListener('click', function() {
            searchInput.value = '';
            resultCard.style.display = 'none';
            searchClearBtn.style.display = 'none';
            document.querySelectorAll('.match-card').forEach(card => card.classList.remove('focus-glow'));
        });

        btnFocus.addEventListener('click', function() {
            performFocusScroll();
        });

        // ----------------------------------------------------
        // ----------------------------------------------------
        // LIVE Real-Time Polling (Sync public bracket updates without refresh)
        // Optimized with Page Visibility API to save bandwidth
        // ----------------------------------------------------
        let pollingInterval = null;

        function fetchLatestBracketData() {
            fetch("{{ route('public.season.bracket.data', \App\Http\Controllers\BracketController::encodeId($season->id)) }}")
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.matches) {
                        res.matches.forEach(m => {
                            const card = document.getElementById(`card_m_${m.round_number}_${m.match_number}`);
                            if (card) {
                                // 1. Update time / live badge
                                const timeSpan = card.querySelector('.match-card-time');
                                if (timeSpan) {
                                    if (m.status === 'live') {
                                        timeSpan.innerHTML = '<span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.5rem;">LIVE</span>';
                                    } else {
                                        let timeDisplay = m.match_time || '20:00 WIB';
                                        if (timeDisplay.includes(',')) {
                                            const parts = timeDisplay.split(',');
                                            timeDisplay = parts[parts.length - 1].trim();
                                        }
                                        timeSpan.innerHTML = `<i class="bi bi-clock"></i> ${timeDisplay}`;
                                    }
                                }

                                // 2. Update border class
                                if (m.status === 'live') {
                                    card.classList.add('border-primary');
                                } else {
                                    card.classList.remove('border-primary');
                                }

                                // 3. Update Team Rows
                                const rows = card.querySelectorAll('.team-row');
                                if (rows[0]) {
                                    rows[0].dataset.teamId = m.team1_id || '';
                                    rows[0].className = `team-row ${m.winner_id && m.winner_id === m.team1_id ? 'winner' : ''} ${m.winner_id && m.winner_id !== m.team1_id ? 'loser' : ''}`;
                                    
                                    const nameSpan = rows[0].querySelector('.team-name');
                                    if (nameSpan) {
                                        nameSpan.className = m.team1_name ? 'team-name' : 'team-name text-muted italic';
                                        nameSpan.textContent = m.team1_name || 'TBD';
                                    }
                                    const scoreBox = rows[0].querySelector('.team-score-box');
                                    if (scoreBox) scoreBox.textContent = m.team1_score;
                                }
                                if (rows[1]) {
                                    rows[1].dataset.teamId = m.team2_id || '';
                                    rows[1].className = `team-row ${m.winner_id && m.winner_id === m.team2_id ? 'winner' : ''} ${m.winner_id && m.winner_id !== m.team2_id ? 'loser' : ''}`;
                                    
                                    const nameSpan = rows[1].querySelector('.team-name');
                                    if (nameSpan) {
                                        if (m.team2_name) {
                                            nameSpan.className = 'team-name';
                                            nameSpan.textContent = m.team2_name;
                                        } else {
                                            if (m.round_number === 1) {
                                                nameSpan.className = 'team-name text-success';
                                                nameSpan.textContent = 'BYE (Lolos)';
                                            } else {
                                                nameSpan.className = 'team-name text-muted italic';
                                                nameSpan.textContent = 'TBD';
                                            }
                                        }
                                    }
                                    const scoreBox = rows[1].querySelector('.team-score-box');
                                    if (scoreBox) scoreBox.textContent = m.team2_score;
                                }
                            }
                        });

                        // Rebuild search engine matchesData index in real-time
                        for (const key in matchesData) {
                            delete matchesData[key];
                        }
                        const roundNames = {1: 'Babak 1', 2: 'Babak 2', 3: 'Babak 3', 4: 'Babak 4', 5: 'Perempat', 6: 'Semifinal', 7: 'Grand Final'};
                        res.matches.forEach(b => {
                            const roundName = roundNames[b.round_number] || ('Babak ' + b.round_number);
                            if (b.team1_name && b.team2_name) {
                                matchesData[b.team1_name.toLowerCase()] = {
                                    name: b.team1_name,
                                    opponent: b.team2_name,
                                    opponentWA: b.team2_wa || '-',
                                    schedule: b.match_time || '20:00 WIB',
                                    bracket: "Bracket " + b.match_number,
                                    round: roundName,
                                    status: b.winner_id === b.team1_id ? 'Lolos' : (b.winner_id ? 'Kalah' : 'Belum Main'),
                                    cardId: "card_m_" + b.round_number + "_" + b.match_number
                                };
                                matchesData[b.team2_name.toLowerCase()] = {
                                    name: b.team2_name,
                                    opponent: b.team1_name,
                                    opponentWA: b.team1_wa || '-',
                                    schedule: b.match_time || '20:00 WIB',
                                    bracket: "Bracket " + b.match_number,
                                    round: roundName,
                                    status: b.winner_id === b.team2_id ? 'Lolos' : (b.winner_id ? 'Kalah' : 'Belum Main'),
                                    cardId: "card_m_" + b.round_number + "_" + b.match_number
                                };
                            } else if (b.team1_name && !b.team2_name && b.round_number === 1) {
                                matchesData[b.team1_name.toLowerCase()] = {
                                    name: b.team1_name,
                                    opponent: "Lolos (BYE)",
                                    opponentWA: "-",
                                    schedule: b.match_time || '20:00 WIB',
                                    bracket: "Bracket " + b.match_number,
                                    round: roundName,
                                    status: "Lolos",
                                    cardId: "card_m_" + b.round_number + "_" + b.match_number
                                };
                            }
                        });
                    }
                })
                .catch(err => console.log("Realtime sync issue:", err));
        }

        function startPolling() {
            if (!pollingInterval) {
                pollingInterval = setInterval(fetchLatestBracketData, 4000);
            }
        }

        function stopPolling() {
            if (pollingInterval) {
                clearInterval(pollingInterval);
                pollingInterval = null;
            }
        }

        // Detect Visibility change to stop requests when browser tab is inactive
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopPolling();
            } else {
                fetchLatestBracketData(); // immediate fetch when coming back active
                startPolling();
            }
        });

        // Start polling initially
        startPolling();

        // ----------------------------------------------------
        // Web Live Chat Widget Implementation
        // ----------------------------------------------------
        const chatWidgetHTML = `
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
        `;
        document.body.insertAdjacentHTML('beforeend', chatWidgetHTML);

        // Session Token Setup
        let sessionToken = localStorage.getItem('yomuda_chat_session_token');
        if (!sessionToken) {
            sessionToken = 'token_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            localStorage.setItem('yomuda_chat_session_token', sessionToken);
        }

        const btnChatToggle = document.getElementById('btnChatToggle');
        const btnChatClose = document.getElementById('btnChatClose');
        const chatBoxContainer = document.getElementById('chatBoxContainer');
        const chatMessagesBody = document.getElementById('chatMessagesBody');
        const chatInputText = document.getElementById('chatInputText');
        const btnChatSend = document.getElementById('btnChatSend');
        const chatUnreadCount = document.getElementById('chatUnreadCount');

        let isChatOpen = false;
        let lastMessageId = 0;
        let chatPollingInterval = null;

        btnChatToggle.addEventListener('click', () => {
            isChatOpen = !isChatOpen;
            if (isChatOpen) {
                chatBoxContainer.classList.add('active');
                chatUnreadCount.style.display = 'none';
                chatUnreadCount.textContent = '0';
                scrollChatToBottom();
                fetchChatMessages();
                // Start chat polling when open
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
        });

        btnChatClose.addEventListener('click', () => {
            isChatOpen = false;
            chatBoxContainer.classList.remove('active');
            if (chatPollingInterval) {
                clearInterval(chatPollingInterval);
                chatPollingInterval = null;
            }
        });

        function scrollChatToBottom() {
            chatMessagesBody.scrollTop = chatMessagesBody.scrollHeight;
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
            if (msg.message.startsWith('[IMAGE]:')) {
                const imgUrl = msg.message.substring(8);
                bubble.innerHTML = `<img src="${imgUrl}" class="img-fluid rounded-3 my-1" style="max-height: 120px; cursor: pointer; display: block;" onclick="window.open('${imgUrl}', '_blank')">`;
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
                    // Update last message ID to avoid duplication
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
                    if (file.size > 2 * 1024 * 1024) {
                        alert("Ukuran file maksimal 2MB!");
                        return;
                    }
                    
                    // Show uploading state
                    const tempMsg = {
                        id: 88888888 + Math.random(),
                        message: "Mengunggah gambar...",
                        is_admin: false
                    };
                    renderMessage(tempMsg);
                    scrollChatToBottom();
                    
                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('session_token', sessionToken);
                    
                    fetch("{{ route('public.season.chat.upload', $slug) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            fetchChatMessages();
                        } else {
                            alert("Gagal mengunggah: " + res.message);
                        }
                    })
                    .catch(err => {
                        console.log("Upload err:", err);
                        alert("Gagal mengunggah gambar.");
                    });
                }
            });
        }

        // ----------------------------------------------------
        // Canvas Matchday Generator
        // ----------------------------------------------------
        const btnDownloadMatchday = document.getElementById('btnDownloadMatchday');
        if (btnDownloadMatchday) {
            btnDownloadMatchday.addEventListener('click', function() {
                const teamName = document.getElementById('resTeamName').textContent;
                const opponentName = document.getElementById('resOpponent').textContent;
                const schedule = document.getElementById('resSchedule').textContent;
                const round = document.getElementById('resRoundLabel').textContent;
                const bracket = document.getElementById('resBracketLabel').textContent;
                
                if (!teamName || teamName === 'Tim tidak ditemukan') return;
                
                const canvas = document.createElement('canvas');
                canvas.width = 1080;
                canvas.height = 1080;
                const ctx = canvas.getContext('2d');
                
                const gradient = ctx.createRadialGradient(540, 540, 100, 540, 540, 800);
                gradient.addColorStop(0, '#1c1c1f');
                gradient.addColorStop(1, '#09090b');
                ctx.fillStyle = gradient;
                ctx.fillRect(0, 0, 1080, 1080);
                
                ctx.strokeStyle = 'rgba(255, 122, 0, 0.4)';
                ctx.lineWidth = 4;
                ctx.beginPath();
                ctx.moveTo(50, 200);
                ctx.lineTo(50, 50);
                ctx.lineTo(200, 50);
                ctx.stroke();
                
                ctx.beginPath();
                ctx.moveTo(1030, 200);
                ctx.lineTo(1030, 50);
                ctx.lineTo(880, 50);
                ctx.stroke();
                
                ctx.beginPath();
                ctx.moveTo(50, 880);
                ctx.lineTo(50, 1030);
                ctx.lineTo(200, 1030);
                ctx.stroke();
                
                ctx.beginPath();
                ctx.moveTo(1030, 880);
                ctx.lineTo(1030, 1030);
                ctx.lineTo(880, 1030);
                ctx.stroke();
                
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.02)';
                ctx.lineWidth = 1;
                for (let i = 0; i < 1080; i += 60) {
                    ctx.beginPath();
                    ctx.moveTo(i, 0);
                    ctx.lineTo(i, 1080);
                    ctx.stroke();
                    
                    ctx.beginPath();
                    ctx.moveTo(0, i);
                    ctx.lineTo(1080, i);
                    ctx.stroke();
                }
                
                ctx.fillStyle = '#ff7a00';
                ctx.font = '800 38px "Plus Jakarta Sans", sans-serif';
                ctx.textAlign = 'center';
                ctx.shadowColor = '#ff7a00';
                ctx.shadowBlur = 15;
                ctx.fillText('YOMUDA CHAMPIONSHIP', 540, 140);
                
                ctx.shadowBlur = 0;
                ctx.fillStyle = '#a1a1aa';
                ctx.font = '700 24px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('MATCHDAY INFORMATION', 540, 190);
                
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.1)';
                ctx.lineWidth = 2;
                ctx.beginPath();
                ctx.moveTo(340, 230);
                ctx.lineTo(740, 230);
                ctx.stroke();
                
                ctx.fillStyle = '#ffffff';
                ctx.font = '800 52px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(teamName.toUpperCase(), 540, 390);
                
                ctx.fillStyle = '#ff7a00';
                ctx.font = '800 38px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('VS', 540, 470);
                
                ctx.fillStyle = '#ffffff';
                ctx.font = '800 52px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(opponentName.toUpperCase(), 540, 560);
                
                ctx.fillStyle = 'rgba(255, 255, 255, 0.03)';
                ctx.strokeStyle = 'rgba(255, 255, 255, 0.05)';
                ctx.lineWidth = 1;
                ctx.beginPath();
                ctx.roundRect(140, 680, 800, 220, 24);
                ctx.fill();
                ctx.stroke();
                
                ctx.fillStyle = '#a1a1aa';
                ctx.font = '600 24px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('JADWAL PERTANDINGAN', 540, 735);
                ctx.fillStyle = '#ffffff';
                ctx.font = '800 32px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(schedule, 540, 785);
                
                ctx.fillStyle = '#a1a1aa';
                ctx.font = '600 24px "Plus Jakarta Sans", sans-serif';
                ctx.fillText(`${round.toUpperCase()}  |  ${bracket.toUpperCase()}`, 540, 850);
                
                ctx.fillStyle = 'rgba(255, 255, 255, 0.2)';
                ctx.font = '600 20px "Plus Jakarta Sans", sans-serif';
                ctx.fillText('yomudachamps.com', 540, 990);
                
                const dataUrl = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = `Matchday_${teamName}_vs_${opponentName}.png`.replace(/\s+/g, '_');
                link.href = dataUrl;
                link.click();
            });
        }

        // Check messages initially
        fetchChatMessages();
        setInterval(() => {
            if (!isChatOpen) {
                fetchChatMessages();
            }
        }, 10000);
    });
    </script>
</body>
</html>
