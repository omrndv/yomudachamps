@extends('layouts.admin')

@section('content')
@php
    $startNumbers = [];
@endphp
<style>
    .search-box-season {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 2px 12px;
        transition: all 0.2s ease;
    }
    .search-box-season:focus-within {
        background: #ffffff;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    .search-box-season input {
        border: 0;
        background: transparent;
        font-size: 0.85rem;
        padding: 8px 6px;
        outline: none;
        width: 100%;
        color: #1e293b;
    }
    .search-box-season i {
        color: #94a3b8;
    }
</style>
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Breadcrumb & Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.seasons') }}" class="text-decoration-none text-warning fw-semibold">Daftar Season</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard', $season->id) }}" class="text-decoration-none text-warning fw-semibold">{{ $season->name }}</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">Kelola Bagan</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Kelola Bagan Turnamen <span class="text-warning">{{ $season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1 d-flex align-items-center flex-wrap gap-2">
                        <span>Atur jadwal serentak per babak, geser (drag & drop) posisi tim di Babak 1, dan edit skor.</span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 d-inline-flex align-items-center gap-1.5" style="font-size: 0.58rem; font-weight: 700;">
                            <span class="pulse-dot-admin"></span> LIVE SYNC ACTIVE
                        </span>
                    </p>
                    <div class="mt-2 d-flex align-items-center gap-2">
                        <div class="form-check form-switch m-0 p-0 d-flex align-items-center gap-2">
                            <input class="form-check-input m-0" type="checkbox" role="switch" id="toggleBracketVisibility" {{ $season->is_bracket_visible ? 'checked' : '' }} style="width: 2.8em; height: 1.4em; cursor: pointer;">
                            <label class="form-check-label fw-bold m-0" for="toggleBracketVisibility" style="font-size: 0.72rem; cursor: pointer;">
                                <span id="bracketVisibilityLabel" class="{{ $season->is_bracket_visible ? 'text-success' : 'text-danger' }}">
                                    {{ $season->is_bracket_visible ? '🟢 Bracket Terlihat oleh Peserta' : '🔴 Bracket Tersembunyi dari Peserta' }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 justify-content-md-start align-items-center">
                    <button type="button" class="btn btn-outline-info text-dark btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalAdminLiveChat">
                        <i class="bi bi-chat-left-dots-fill me-1"></i> Live Chat <span class="badge bg-danger ms-1" id="adminGlobalUnreadBadge" style="display: none; font-size: 0.55rem; padding: 3px 6px;">0</span>
                    </button>
                    <a href="{{ route('admin.season.match-reports', $season->id) }}" class="btn btn-outline-primary btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap">
                        <i class="bi bi-trophy-fill me-1"></i> Laporan Laga
                    </a>
                    <a href="{{ route('public.season.landing', \App\Http\Controllers\BracketController::encodeId($season->id)) }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap">
                        <i class="bi bi-eye me-1"></i> Lihat Halaman User
                    </a>
                    
                    @if($brackets->count() > 0)
                        <button type="button" class="btn btn-outline-danger text-dark btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalUnfinishedMatches">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Laga Belum Selesai ({{ $brackets->filter(fn($b) => $b->status !== 'finished' && $b->team1_id && $b->team2_id)->count() }})
                        </button>
                        <button type="button" class="btn btn-outline-warning text-dark btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalYmdSlots">
                            <i class="bi bi-tag-fill me-1"></i> Detail Slot YMD
                        </button>
                        <button type="button" class="btn btn-success text-white btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalShareTemplates">
                            <i class="bi bi-share-fill me-1"></i> Teks Share WA
                        </button>
                    @endif

                    <button type="button" class="btn btn-outline-success btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalCopyTeams">
                        <i class="bi bi-clipboard me-1"></i> Copy Daftar Tim (Backup)
                    </button>
                    
                    <button type="button" class="btn {{ $season->manual_juara1 ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }} btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#modalManualWinners">
                        <i class="bi bi-trophy-fill me-1"></i> {{ $season->manual_juara1 ? '🏆 Juara Manual (Aktif)' : 'Input Juara Manual' }}
                    </button>
                    
                    @if($brackets->count() > 0)
                        <form action="{{ route('admin.season.bracket.generate', $season->id) }}" method="POST" onsubmit="return confirm('PERINGATAN! Generate ulang bagan akan MENGHAPUS semua skor dan data tanding yang sudah ada. Lanjutkan?')" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm px-3 fw-bold rounded-pill shadow-sm text-nowrap">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset & Acak Ulang
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($brackets->count() === 0)
        {{-- Empty State - Generate Bracket --}}
        <div class="row">
            <div class="col-md-8 mx-auto text-center py-5">
                <div class="card border-0 shadow-sm rounded-4 p-5" style="background-color: #ffffff;">
                    <div class="mb-4">
                        <span class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle" style="width: 80px; height: 80px;">
                            <i class="bi bi-diagram-3-fill" style="font-size: 2.5rem;"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">Bagan Turnamen Belum Dibuat</h4>
                    <p class="text-secondary mb-4">
                        Ada <strong>{{ $teams->count() }} tim lunas (PAID)</strong> terdaftar untuk season ini. <br>
                        Sistem akan mengacak posisi tanding seluruh tim secara adil.
                    </p>
                    <form action="{{ route('admin.season.bracket.generate', $season->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Generate & Acak Bagan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- Controls Panel --}}
        <div class="card border-0 shadow-sm rounded-4 p-3 bg-white mb-4" style="border: 1px solid rgba(0, 0, 0, 0.06) !important;">
            <div class="row g-3 align-items-center">
                {{-- Search Box --}}
                <div class="col-md-4">
                    <div class="search-box-season" style="padding-right: 2px;">
                        <i class="bi bi-search"></i>
                        <input type="text" id="adminTeamSearch" placeholder="Cari nama tim...">
                        <button class="btn btn-warning text-dark btn-sm d-flex align-items-center gap-1 px-3 fw-bold rounded-3" type="button" id="toggleSearchModeBtn" style="font-size: 0.72rem; margin: 2px; height: 32px; white-space: nowrap;">
                            <i class="bi bi-person-fill"></i> Nama
                        </button>
                    </div>
                </div>
                {{-- Dark/Light Theme Switch --}}
                <div class="col-md-3 text-start">
                    <div class="form-check form-switch ps-5">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleBracketThemeSwitch" checked style="cursor: pointer;">
                        <label class="form-check-label small fw-bold text-dark" for="toggleBracketThemeSwitch" style="cursor: pointer;">Tema Bagan Gelap (Dark)</label>
                    </div>
                </div>
                {{-- Bronze Match Toggle Switch --}}
                <div class="col-md-3 text-start">
                    @php
                        $hasBronze = false;
                        $finalRoundKey = $brackets->max('round_number');
                        if ($finalRoundKey) {
                            $hasBronze = $brackets->where('round_number', $finalRoundKey)->where('match_number', 2)->isNotEmpty();
                        }
                    @endphp
                    <div class="form-check form-switch ps-5">
                        <input class="form-check-input" type="checkbox" role="switch" id="toggleBronzeMatchSwitch" {{ $hasBronze ? 'checked' : '' }} onchange="toggleBronzeMatchSetting(this)" style="cursor: pointer;">
                        <label class="form-check-label small fw-bold text-dark" for="toggleBronzeMatchSwitch" style="cursor: pointer;">Bronze Match (Juara 3/4)</label>
                    </div>
                </div>
                {{-- Info text & Mode Swap Toggle --}}
                <div class="col-md-2 text-end d-flex align-items-center justify-content-end gap-1.5">
                    <button type="button" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-2.5 fw-bold text-nowrap" id="toggleSwapModeBtn" style="font-size: 0.68rem;">
                        <i class="bi bi-arrow-down-up me-1"></i> <span id="swapModeText">Tukar Posisi</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 fw-bold text-nowrap" style="font-size: 0.68rem;" data-bs-toggle="modal" data-bs-target="#modalRoundTimes">
                        <i class="bi bi-clock-fill text-warning me-1"></i> Jam Babak
                    </button>
                </div>
            </div>

            {{-- Filter Babak / Round Tab Focus --}}
            <div class="row pt-3 border-top mt-3 align-items-center">
                <div class="col-12 d-flex align-items-center gap-2 flex-wrap">
                    <span class="small fw-bold text-secondary me-1" style="font-size: 0.75rem;"><i class="bi bi-funnel-fill text-warning me-1"></i>Fokus Babak:</span>
                    <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold round-filter-btn active" data-round="all" style="font-size: 0.72rem;">
                        Semua Babak
                    </button>
                    @php
                        $totalRoundsCount = count($rounds);
                    @endphp
                    @foreach($rounds as $rNum => $rMatches)
                        @php
                            if ($rNum == $totalRoundsCount) {
                                $rLabel = "Grand Final";
                            } elseif ($rNum == $totalRoundsCount - 1 && $totalRoundsCount > 1) {
                                $rLabel = "Semifinal";
                            } else {
                                $rLabel = "Babak " . $rNum;
                            }
                        @endphp
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold round-filter-btn" data-round="{{ $rNum }}" style="font-size: 0.72rem;">
                            {{ $rLabel }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bracket Tree Viewer --}}
        <div id="bracketCardContainer" class="card border-0 shadow-sm rounded-4 theme-dark" style="overflow: hidden; transition: all 0.3s ease;">
            <div class="round-headers-bar" id="adminRoundHeadersBar">
                @php
                    $totalRounds = count($rounds);
                @endphp
                @foreach($rounds as $roundNum => $matches)
                    <div class="round-header-item" data-round-header="{{ $roundNum }}">
                        @php
                            if ($roundNum == $totalRounds) {
                                $title = "Grand Final";
                            } elseif ($roundNum == $totalRounds - 1 && $totalRounds > 1) {
                                $title = "Semifinal";
                            } else {
                                $title = "Babak " . $roundNum;
                            }
                        @endphp
                        {{ $title }}
                    </div>
                @endforeach
            </div>

            @php
                $totalRounds = $brackets->max('round_number') ?? 1;
                $treeSlotsR1 = (int) pow(2, $totalRounds - 1); // e.g. 32 slots for 64-tree

                $r1MatchCounter = 0;
                $startNumbers = [];
                $currentStart = 1;
                foreach ($rounds as $rNum => $rMatches) {
                    $startNumbers[$rNum] = $currentStart;
                    if ($rNum === 1) {
                        $realR1Matches = $rMatches->filter(fn($m) => $m->team1_id && $m->team2_id);
                        $currentStart += $realR1Matches->count();
                    } else {
                        $currentStart += $rMatches->count();
                    }
                }
            @endphp

            <!-- Scrollable Bracket Canvas -->
            <div class="bracket-container" id="adminBracketContainer">
                @foreach($rounds as $roundNum => $matches)
                    @php
                        $isFinalRound = ($roundNum === $brackets->max('round_number'));
                        $columnMatches = $isFinalRound ? $matches->where('match_number', 1) : $matches;
                        $roundHeight = 4600;
                        $matchesCount = $columnMatches->count();
                        $bronzeMatch = $isFinalRound ? $brackets->where('round_number', $roundNum)->where('match_number', 2)->first() : null;
                    @endphp
                    <div class="bracket-round" data-round-col="{{ $roundNum }}">
                        @foreach($columnMatches as $match)
                            @php
                                // Skip render di Babak 1 jika match tidak memiliki 2 tim bertanding (BYE atau slot kosong)
                                $isByeMatch = ($roundNum === 1 && (!$match->team1_id || !$match->team2_id));
                                
                                $totalPosInCol = ($roundNum === 1) ? $treeSlotsR1 : $matchesCount;
                                $slotHeight = $roundHeight / $totalPosInCol;
                                $cardTop = (int)(($match->match_number - 0.5) * $slotHeight) - 32;

                                if ($roundNum === 1 && !$isByeMatch) {
                                    $r1MatchCounter++;
                                    $badgeNumber = $r1MatchCounter;
                                } else {
                                    $badgeNumber = $startNumbers[$roundNum] + ($match->match_number - 1);
                                }
                            @endphp

                            @if(!$isByeMatch)
                                <div class="match-card {{ $match->status === 'live' ? 'border-primary' : '' }}" 
                                     id="card_m_{{ $match->round_number }}_{{ $match->match_number }}"
                                     style="position: absolute; top: {{ $cardTop }}px;"
                                     onclick="openEditMatchModal({{ json_encode([
                                         'id' => $match->id,
                                         'team1_name' => $match->team1 ? $match->team1->name : 'TBD',
                                         'team2_name' => $match->team2 ? $match->team2->name : 'TBD',
                                         'team1_score' => $match->team1_score,
                                         'team2_score' => $match->team2_score,
                                         'match_time' => $match->match_time ?? '20:00 WIB',
                                         'status' => $match->status,
                                         'team1_exists' => (bool)$match->team1_id,
                                         'team2_exists' => (bool)$match->team2_id
                                     ]) }})">
                                    
                                    <div class="match-card-header">
                                        <span>BRACKET {{ $badgeNumber }}</span>
                                        <span class="match-card-time">
                                            @if($match->status === 'live')
                                                <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.5rem; animation: pulse 1s infinite alternate;">LIVE</span>
                                            @else
                                                <i class="bi bi-clock"></i> {{ $match->match_time ?? '20:00 WIB' }}
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Team 1 Row --}}
                                    <div class="team-row {{ $match->winner_id && $match->winner_id === $match->team1_id ? 'winner' : '' }} {{ $match->winner_id && $match->winner_id !== $match->team1_id ? 'loser' : '' }}"
                                         data-team-id="{{ $match->team1_id ?? '' }}"
                                         data-team-name="{{ $match->team1 ? strtolower($match->team1->name) : '' }}"
                                         data-team-wa="{{ $match->team1 ? strtolower($match->team1->wa_number) : '' }}"
                                         data-match-id="{{ $match->id }}"
                                         data-slot="1"
                                         data-round="{{ $match->round_number }}"
                                         @if($match->round_number === 1 && $match->status !== 'finished') draggable="true" @endif>
                                         <div class="team-info">
                                            @if($match->team1)
                                                <span class="team-name fw-semibold">{{ $match->team1->name }}</span>
                                            @else
                                                <span class="team-name text-muted italic">Belum Ada Tim</span>
                                            @endif
                                        </div>
                                        @if($match->team1_id && $match->status !== 'finished')
                                            <button type="button" class="btn-quick-win btn-quick-win-t1" title="Loloskan {{ $match->team1->name }}" onclick="event.stopPropagation(); quickWinMatch({{ $match->id }}, {{ $match->team1_id }}, '{{ addslashes($match->team1->name) }}')"><i class="bi bi-trophy-fill"></i></button>
                                        @endif
                                        <span class="team-score-box">{{ $match->team1_score }}</span>
                                    </div>

                                    {{-- Team 2 Row --}}
                                    <div class="team-row {{ $match->winner_id && $match->winner_id === $match->team2_id ? 'winner' : '' }} {{ $match->winner_id && $match->winner_id !== $match->team2_id ? 'loser' : '' }}"
                                         data-team-id="{{ $match->team2_id ?? '' }}"
                                         data-team-name="{{ $match->team2 ? strtolower($match->team2->name) : '' }}"
                                         data-team-wa="{{ $match->team2 ? strtolower($match->team2->wa_number) : '' }}"
                                         data-match-id="{{ $match->id }}"
                                         data-slot="2"
                                         data-round="{{ $match->round_number }}"
                                         @if($match->round_number === 1 && $match->status !== 'finished') draggable="true" @endif>
                                         <div class="team-info">
                                            @if($match->team2)
                                                <span class="team-name fw-semibold">{{ $match->team2->name }}</span>
                                            @else
                                                <span class="team-name text-muted italic">Belum Ada Tim</span>
                                            @endif
                                        </div>
                                        @if($match->team2_id && $match->status !== 'finished')
                                            <button type="button" class="btn-quick-win btn-quick-win-t2" title="Loloskan {{ $match->team2->name }}" onclick="event.stopPropagation(); quickWinMatch({{ $match->id }}, {{ $match->team2_id }}, '{{ addslashes($match->team2->name) }}')"><i class="bi bi-trophy-fill"></i></button>
                                        @endif
                                        <span class="team-score-box">{{ $match->team2_score }}</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        {{-- Draw dynamic SVG connector lines between columns --}}
                        @if($roundNum < count($rounds))
                            @php
                                $connSlotsCount = ($roundNum === 1) ? $treeSlotsR1 : $matchesCount;
                                $nextColSlotsCount = ($roundNum === 1) ? ($treeSlotsR1 / 2) : ($matchesCount / 2);
                            @endphp
                            <svg class="round-connectors" viewBox="0 0 80 {{ $roundHeight }}" preserveAspectRatio="none">
                                @for($m = 1; $m <= $connSlotsCount; $m++)
                                    @php
                                        // Skip connector untuk posisi BYE/kosong di Babak 1
                                        $matchAtConnPos = ($roundNum === 1) ? $columnMatches->firstWhere('match_number', $m) : null;
                                        $isByeConnPos = ($roundNum === 1 && (!$matchAtConnPos || !$matchAtConnPos->team1_id || !$matchAtConnPos->team2_id));
                                        $nextMatchIndex = ceil($m / 2);
                                        $startY = ($roundHeight / $connSlotsCount) * ($m - 0.5);
                                        $endY = ($roundHeight / $nextColSlotsCount) * ($nextMatchIndex - 0.5);
                                        $midX = 40;
                                    @endphp
                                    @if(!$isByeConnPos)
                                        <path class="connector-line" id="line_{{ $roundNum }}_{{ $m }}" d="M 0,{{ $startY }} L {{ $midX }},{{ $startY }} L {{ $midX }},{{ $endY }} L 80,{{ $endY }}"></path>
                                    @endif
                                @endfor
                            </svg>
                        @endif

                        {{-- Render Bronze Match inside the final column --}}
                        @if($isFinalRound && $bronzeMatch)
                            <div class="bronze-match-wrapper">
                                <div class="bronze-match-title">3rd Place Match</div>
                                <div class="match-card {{ $bronzeMatch->status === 'live' ? 'border-primary' : '' }}" 
                                     id="card_m_{{ $bronzeMatch->round_number }}_{{ $bronzeMatch->match_number }}"
                                     onclick="openEditMatchModal({{ json_encode([
                                         'id' => $bronzeMatch->id,
                                         'team1_name' => $bronzeMatch->team1 ? $bronzeMatch->team1->name : 'TBD',
                                         'team2_name' => $bronzeMatch->team2 ? $bronzeMatch->team2->name : 'TBD',
                                         'team1_score' => $bronzeMatch->team1_score,
                                         'team2_score' => $bronzeMatch->team2_score,
                                         'match_time' => $bronzeMatch->match_time ?? '',
                                         'status' => $bronzeMatch->status,
                                         'team1_exists' => (bool)$bronzeMatch->team1_id,
                                         'team2_exists' => (bool)$bronzeMatch->team2_id
                                     ]) }})">
                                    
                                    {{-- Team 1 Row --}}
                                    <div class="team-row {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id === $bronzeMatch->team1_id ? 'winner' : '' }} {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id !== $bronzeMatch->team1_id ? 'loser' : '' }}"
                                         data-team-id="{{ $bronzeMatch->team1_id ?? '' }}"
                                         data-team-name="{{ $bronzeMatch->team1 ? strtolower($bronzeMatch->team1->name) : '' }}"
                                         data-team-wa="{{ $bronzeMatch->team1 ? strtolower($bronzeMatch->team1->wa_number) : '' }}"
                                         data-match-id="{{ $bronzeMatch->id }}"
                                         data-slot="1"
                                         data-round="{{ $bronzeMatch->round_number }}">
                                         <div class="team-info">
                                            @if($bronzeMatch->team1)
                                                <span class="team-name fw-semibold">{{ $bronzeMatch->team1->name }}</span>
                                            @else
                                                <span class="team-name text-muted italic">Belum Ada Tim</span>
                                            @endif
                                        </div>
                                        <span class="team-score-box">{{ $bronzeMatch->team1_score }}</span>
                                    </div>

                                    {{-- Team 2 Row --}}
                                    <div class="team-row {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id === $bronzeMatch->team2_id ? 'winner' : '' }} {{ $bronzeMatch->winner_id && $bronzeMatch->winner_id !== $bronzeMatch->team2_id ? 'loser' : '' }}"
                                         data-team-id="{{ $bronzeMatch->team2_id ?? '' }}"
                                         data-team-name="{{ $bronzeMatch->team2 ? strtolower($bronzeMatch->team2->name) : '' }}"
                                         data-team-wa="{{ $bronzeMatch->team2 ? strtolower($bronzeMatch->team2->wa_number) : '' }}"
                                         data-match-id="{{ $bronzeMatch->id }}"
                                         data-slot="2"
                                         data-round="{{ $bronzeMatch->round_number }}">
                                         <div class="team-info">
                                            @if($bronzeMatch->team2)
                                                <span class="team-name fw-semibold">{{ $bronzeMatch->team2->name }}</span>
                                            @else
                                                <span class="team-name text-muted italic">Belum Ada Tim</span>
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
        </div>
    @endif
</div>

{{-- Modal Pertandingan Belum Selesai (Unfinished Matches Follow-up) --}}
@if($brackets->count() > 0)
    @php
        $unfinishedMatches = $brackets->filter(function($b) {
            return $b->status !== 'finished' && $b->team1_id !== null && $b->team2_id !== null;
        });
    @endphp
    <div class="modal fade" id="modalUnfinishedMatches" tabindex="-1" aria-labelledby="modalUnfinishedMatchesLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header bg-dark text-white rounded-top-4 border-0 py-3">
                    <h6 class="modal-title fw-bold" id="modalUnfinishedMatchesLabel">
                        <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Daftar Pertandingan Belum Selesai ({{ $unfinishedMatches->count() }})
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    <p class="text-secondary small mb-3">
                        Berikut adalah daftar pertandingan yang kedua timnya sudah siap tetapi hasil tanding/skor belum selesai diinput. Hubungi kapten tim via WhatsApp untuk koordinasi.
                    </p>
                    
                    @if($unfinishedMatches->count() > 0)
                        <div class="list-group gap-2.5">
                            @foreach($unfinishedMatches as $match)
                                <div class="list-group-item border rounded-3 p-3 bg-light d-flex justify-content-between align-items-center flex-wrap gap-3">
                                    <div>
                                        <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                            {{ $match->team1->name }} <span class="text-secondary fw-normal">vs</span> {{ $match->team2->name }}
                                        </div>
                                        <div class="small text-muted mb-2" style="font-size: 0.75rem;">
                                            WA Kapten 1: <code class="text-dark fw-bold">{{ $match->team1->wa_number ?? '-' }}</code> | 
                                            WA Kapten 2: <code class="text-dark fw-bold">{{ $match->team2->wa_number ?? '-' }}</code>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 small text-secondary">
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                                                @php
                                                    $totalRounds = count($rounds);
                                                    if ($match->round_number == $totalRounds) {
                                                        $rLabel = "Grand Final";
                                                    } elseif ($match->round_number == $totalRounds - 1 && $totalRounds > 1) {
                                                        $rLabel = "Semifinal";
                                                    } else {
                                                        $rLabel = "Babak " . $match->round_number;
                                                    }
                                                @endphp
                                                {{ $rLabel }} (Match {{ $match->match_number }})
                                            </span>
                                            @if($match->match_time)
                                                <span><i class="bi bi-clock me-1"></i>{{ $match->match_time }}</span>
                                            @endif
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">
                                                {{ strtoupper($match->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        {{-- Contact Team 1 Captain --}}
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $match->team1->wa_number) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-whatsapp me-1"></i> WA Kapten 1
                                        </a>
                                        {{-- Contact Team 2 Captain --}}
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $match->team2->wa_number) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" style="font-size: 0.75rem;">
                                            <i class="bi bi-whatsapp me-1"></i> WA Kapten 2
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-secondary">
                            <i class="bi bi-check-circle-fill text-success d-block mb-3" style="font-size: 3rem;"></i>
                            <h6 class="fw-bold mb-1">Semua Pertandingan Selesai!</h6>
                            <p class="small text-muted mb-0">Tidak ada pertandingan yang menunda laporan saat ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Modal Detail Slot YMD (Manage Placeholders) --}}
@if($brackets->count() > 0)
<div class="modal fade" id="modalYmdSlots" tabindex="-1" aria-labelledby="modalYmdSlotsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-dark text-white rounded-top-4 border-0 py-3">
                <h6 class="modal-title fw-bold" id="modalYmdSlotsLabel"><i class="bi bi-tag-fill text-warning me-2"></i>Kelola Slot / Tim Placeholder YMD</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                {{-- Panel Tambah Slot & Cari Slot --}}
                <div class="row g-3 mb-4">
                    {{-- Bulk Add --}}
                    <div class="col-md-7">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-plus-circle me-1 text-warning"></i>Tambah Banyak Slot YMD Baru</h6>
                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <span class="small text-secondary">Jumlah:</span>
                                </div>
                                <div class="col-4 col-sm-3">
                                    <input type="number" id="ymdAddCount" class="form-control form-control-sm" min="1" max="100" value="5">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-warning btn-sm fw-bold px-3 rounded" onclick="bulkAddYmdSlots()">
                                        <i class="bi bi-plus-lg"></i> Tambahkan
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-success btn-sm fw-bold px-3 rounded" onclick="winAllYmdSlots()">
                                        <i class="bi bi-trophy-fill"></i> Loloskan Semua Slot YMD
                                    </button>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-3 rounded" onclick="deleteAllYmdSlots()">
                                        <i class="bi bi-trash-fill"></i> Hapus Semua Slot
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Search box --}}
                    <div class="col-md-5">
                        <div class="p-3 border rounded-3 bg-light h-100">
                            <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-search me-1 text-warning"></i>Cari Slot YMD</h6>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white"><i class="bi bi-search text-secondary"></i></span>
                                <input type="text" id="modalYmdSearch" class="form-control" placeholder="Cari nama slot / tim...">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- List of current YMD Teams --}}
                <h6 class="fw-bold text-dark mb-2.5 small"><i class="bi bi-list-stars me-1 text-warning"></i>Daftar Slot YMD Terdaftar (Klik Simpan untuk Ganti Nama Tim)</h6>
                <div style="max-height: 280px; overflow-y: auto;" class="border rounded bg-white">
                    <table class="table table-sm align-middle m-0 small" id="modalYmdTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3 py-2">Nama Slot Asli</th>
                                <th class="py-2">Ganti Nama Tim Peserta</th>
                                <th class="py-2" style="width: 140px;">Harga Slot (Rp)</th>
                                <th class="text-end pe-3 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ymdTeams = $teams->filter(function($t) {
                                    return str_starts_with(strtolower($t->name), 'ymd');
                                })->sortBy(function($t) {
                                    $parts = explode('-', $t->name);
                                    return isset($parts[1]) ? intval($parts[1]) : 0;
                                });
                            @endphp
                            @forelse($ymdTeams as $t)
                                <tr>
                                    <td class="ps-3 fw-bold text-warning">{{ $t->name }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm w-90" id="ymdRenameInput_{{ $t->id }}" placeholder="Ketik nama tim peserta..." list="registeredTeamsList">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" id="ymdPriceInput_{{ $t->id }}" value="{{ $season->price }}" min="0">
                                    </td>
                                    <td class="text-end pe-3">
                                        <button type="button" class="btn btn-warning btn-sm py-0.5 px-2.5 fw-bold rounded-pill text-dark" style="font-size: 0.7rem;" onclick="renameYmdSlot({{ $t->id }}, '{{ $t->name }}')">
                                            Simpan
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-secondary italic">Tidak ada slot YMD yang terdaftar. Gunakan formulir di atas untuk menambahkannya secara bulk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-bold rounded-pill text-white" data-bs-dismiss="modal">Selesai</button>
            </div>
        </div>
    </div>
</div>

{{-- Autocomplete recommendations datalist --}}
<datalist id="registeredTeamsList">
    @foreach($teams->filter(function($t) { return !str_starts_with(strtolower($t->name), 'ymd'); }) as $rt)
        <option value="{{ $rt->name }}"></option>
    @endforeach
</datalist>
@endif

{{-- Modal Atur Jam Main per Babak --}}
@if($brackets->count() > 0)
<div class="modal fade" id="modalRoundTimes" tabindex="-1" aria-labelledby="modalRoundTimesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-dark text-white rounded-top-4 border-0 py-3">
                <h6 class="modal-title fw-bold" id="modalRoundTimesLabel"><i class="bi bi-clock-fill text-warning me-2"></i>Atur Jam Main Serentak per Babak</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Preset Buttons Bar --}}
                <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 mb-4 text-dark">
                    <label class="small fw-bold d-block mb-2"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Preset Otomatis Jam Fast Tour (1-Klik Isi Semua):</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-warning btn-sm fw-bold rounded-pill text-dark shadow-sm" onclick="applyOfficialYomudaPreset()">
                            <i class="bi bi-clock-fill me-1"></i> Preset Resmi Fast Tour Yomuda (20.00 – Selesai)
                        </button>
                    </div>
                </div>

                <p class="text-secondary small mb-3">Masukkan jadwal jam tanding untuk masing-masing babak di bawah. Anda bisa ubah per babak atau simpan sekaligus.</p>
                <div class="row g-3">
                    @php
                        $totalRounds = count($rounds);
                    @endphp
                    @foreach($rounds as $roundNum => $matches)
                        @php
                            if ($roundNum == $totalRounds) {
                                $title = "Grand Final";
                            } elseif ($roundNum == $totalRounds - 1 && $totalRounds > 1) {
                                $title = "Semifinal";
                            } else {
                                $title = "Babak " . $roundNum;
                            }
                            $firstMatch = $matches->first();
                        @endphp
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 border rounded bg-light">
                                <label class="d-block small fw-bold text-dark mb-1.5">{{ $title }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" id="roundTime_{{ $roundNum }}" value="{{ $firstMatch->match_time ?? '20:00 WIB' }}" placeholder="Contoh: 20:00 WIB">
                                    <button class="btn btn-warning" type="button" onclick="saveRoundTime({{ $roundNum }})">
                                        <i class="bi bi-check-lg"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm px-3 fw-bold rounded-pill text-white" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success btn-sm px-4 fw-bold rounded-pill text-white shadow" onclick="saveAllRoundTimes()">
                    <i class="bi bi-check2-all me-1"></i> Simpan Semua Jam Babak (1-Klik)
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Modal Copy Teams (Backup to Challonge) --}}
<div class="modal fade" id="modalCopyTeams" tabindex="-1" aria-labelledby="modalCopyTeamsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-dark text-white rounded-top-4 border-0 py-3">
                <h6 class="modal-title fw-bold" id="modalCopyTeamsLabel"><i class="bi bi-clipboard me-2"></i>Copy Nama Semua Tim</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">Salin daftar tim lunas di bawah untuk di-import langsung ke Challonge (satu tim per baris) sebagai cadangan.</p>
                <textarea class="form-control bg-light" id="teamsListArea" rows="10" readonly style="font-family: monospace; font-size: 0.85rem;">@php
                    $added = [];
                    // Scan seluruh match (semua babak) agar tim yang mendapat slot BYE (langsung ke Babak 2) tidak terlewat
                    foreach($brackets as $m) {
                        if ($m->team1 && !in_array($m->team1->name, $added)) {
                            echo $m->team1->name . "\n";
                            $added[] = $m->team1->name;
                        }
                        if ($m->team2 && !in_array($m->team2->name, $added)) {
                            echo $m->team2->name . "\n";
                            $added[] = $m->team2->name;
                        }
                    }
                @endphp</textarea>
            </div>
            <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning btn-sm px-4 fw-bold rounded-pill shadow-sm" onclick="copyTeamsList()">
                    <i class="bi bi-copy me-1"></i> Copy ke Clipboard
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Match Modal --}}
<div class="modal fade" id="editMatchModal" tabindex="-1" aria-labelledby="editMatchModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-dark text-white rounded-top-4 border-0 py-3">
                <h6 class="modal-title fw-bold" id="editMatchModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Skor & Jadwal Tanding</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMatchForm">
                @csrf
                <input type="hidden" name="match_id" id="modalMatchId">
                <div class="modal-body p-4">
                    
                    {{-- Alert Info if incomplete --}}
                    <div id="modalIncompleteAlert" class="alert alert-warning py-2 small border-0 mb-3 rounded-3 shadow-sm d-none">
                        <i class="bi bi-info-circle-fill me-2"></i> Tim belum lengkap. Silakan tunggu pemenang dari babak sebelumnya.
                    </div>

                    {{-- Matchup Header --}}
                    <div class="row g-2 mb-4 align-items-center text-center">
                        <div class="col-5">
                            <div class="p-2 border rounded bg-light">
                                <span class="d-block small text-muted">Tim 1</span>
                                <strong class="text-dark d-block text-truncate" id="modalT1Name" style="font-size: 0.85rem;">-</strong>
                            </div>
                            <input type="number" name="team1_score" id="modalT1Score" class="form-control text-center fw-bold mt-2 fs-5" min="0" value="0">
                        </div>
                        <div class="col-2">
                            <span class="badge bg-secondary">VS</span>
                        </div>
                        <div class="col-5">
                            <div class="p-2 border rounded bg-light">
                                <span class="d-block small text-muted">Tim 2</span>
                                <strong class="text-dark d-block text-truncate" id="modalT2Name" style="font-size: 0.85rem;">-</strong>
                            </div>
                            <input type="number" name="team2_score" id="modalT2Score" class="form-control text-center fw-bold mt-2 fs-5" min="0" value="0">
                        </div>
                    </div>

                    {{-- Schedule and Status --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Jadwal Tanding (Tanggal & Jam)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                            <input type="text" name="match_time" id="modalMatchTime" class="form-control" placeholder="Contoh: 29 Juni, 20:00 WIB">
                        </div>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">Format bebas, contoh: <strong>29 Juni, 20:00 WIB</strong></small>
                    </div>

                    <div class="mb-3" style="display: none;">
                        <input type="hidden" name="status" id="modalMatchStatus" value="upcoming">
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4 d-flex align-items-center">
                    <button type="button" class="btn btn-outline-danger btn-sm px-3 fw-bold rounded-pill me-auto" id="btnResetMatch">Reset Match</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold rounded-pill shadow-sm" id="btnSaveMatch">Simpan Hasil</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Admin Live Chat Dashboard Modal --}}
<div class="modal fade" id="modalAdminLiveChat" tabindex="-1" aria-labelledby="modalAdminLiveChatLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden bg-dark text-white">
            <div class="modal-header bg-black text-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="modal-title fw-bold" id="modalAdminLiveChatLabel">
                    <i class="bi bi-chat-left-heart-fill text-warning me-2"></i> Live Chat Konsol Admin
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 d-flex" style="height: 500px;">
                <!-- Left panel: Threads list -->
                <div class="border-end border-secondary border-opacity-25" style="width: 260px; flex-shrink: 0; background-color: rgba(0,0,0,0.15); display: flex; flex-direction: column;">
                    <div class="p-3 border-bottom border-secondary border-opacity-25 text-secondary small fw-bold text-uppercase d-flex justify-content-between align-items-center">
                        <span>Pesan Masuk</span>
                        <button id="adminBtnClearAllChats" class="btn btn-outline-danger btn-sm py-0.5 px-2 rounded-pill fw-bold" style="font-size: 0.6rem;">
                            Reset All
                        </button>
                    </div>
                    <!-- Tab Pills for Active vs Archived -->
                    <div class="p-2 border-bottom border-secondary border-opacity-10 d-flex gap-1.5" style="background-color: rgba(0,0,0,0.1);">
                        <button id="adminTabActive" class="btn btn-warning btn-sm py-0.5 px-2.5 rounded-pill fw-bold" style="font-size: 0.65rem;">
                            Aktif
                        </button>
                        <button id="adminTabArchived" class="btn btn-outline-secondary text-white btn-sm py-0.5 px-2.5 rounded-pill fw-bold" style="font-size: 0.65rem;">
                            Diarsipkan
                        </button>
                    </div>
                    <div id="adminChatThreadsList" class="flex-grow-1 overflow-y-auto" style="list-style: none; padding: 0; margin: 0;">
                        <div class="text-center text-secondary py-5 small">Belum ada chat masuk.</div>
                    </div>
                </div>
                <!-- Right panel: Active Chat Thread -->
                <div class="flex-grow-1 d-flex flex-column" style="background-color: rgba(255,255,255,0.01);">
                    <div class="p-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center justify-content-between" style="background-color: rgba(0,0,0,0.1);">
                        <div id="adminActiveThreadTitle" class="fw-bold text-warning small">Pilih percakapan untuk memulai</div>
                        <span id="adminThreadSessionToken" style="display:none;"></span>
                        <div class="d-flex gap-2 align-items-center">
                            <button id="adminBtnArchiveThread" class="btn btn-outline-warning btn-sm py-0.5 px-2 rounded-pill fw-bold" style="font-size: 0.68rem; display: none;">
                                <i class="bi bi-archive-fill me-1"></i> Arsipkan
                            </button>
                            <button id="adminBtnUnarchiveThread" class="btn btn-outline-success btn-sm py-0.5 px-2 rounded-pill fw-bold" style="font-size: 0.68rem; display: none;">
                                <i class="bi bi-arrow-up-right-square-fill me-1"></i> Buka Arsip
                            </button>
                            <button id="adminBtnDeleteThread" class="btn btn-outline-danger btn-sm py-0.5 px-2 rounded-pill fw-bold" style="font-size: 0.68rem; display: none;">
                                <i class="bi bi-trash3-fill me-1"></i> Hapus Chat
                            </button>
                        </div>
                    </div>
                    
                    <div id="adminChatMessagesBody" class="flex-grow-1 p-3 overflow-y-auto d-flex flex-column gap-2">
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-chat-dots" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Silakan pilih salah satu user anonim di samping untuk membalas pertanyaan.</p>
                        </div>
                    </div>
                    
                    <div class="p-3 border-top border-secondary border-opacity-25 d-flex gap-2 align-items-center" style="background-color: rgba(0,0,0,0.2);">
                        <button id="adminBtnAttach" class="btn btn-outline-secondary btn-sm p-1 rounded-circle" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;" disabled>
                            <i class="bi bi-camera-fill"></i>
                        </button>
                        <input type="file" id="adminFileInput" accept="image/*" style="display: none;">
                        <input type="text" id="adminReplyInput" class="form-control bg-dark border-secondary text-white rounded-pill shadow-none" placeholder="Ketik balasan admin..." autocomplete="off" disabled>
                        <button id="adminBtnReplySend" class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" disabled>
                            <i class="bi bi-send-fill text-dark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.bracket-css')

@include('admin.partials.bracket-js-main')

{{-- MODAL SHARE TEMPLATES WA --}}
<div class="modal fade" id="modalShareTemplates" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 text-dark">
            <div class="modal-header border-bottom border-light p-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-share-fill text-success me-2"></i>Salin Template Pengumuman & Share WA
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-4">Pilih dan salin template teks berikut untuk dibagikan ke WhatsApp peserta atau media sosial.</p>
                
                {{-- 1. Template Info Website Season --}}
                <div class="card border border-light-subtle rounded-3 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="fw-bold text-dark small"><i class="bi bi-globe2 text-success me-1"></i>Template Pengumuman Website Season (Share ke Grup WA)</span>
                        <button class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem;" onclick="copyText('textareaInfoWebsite')">
                            <i class="bi bi-copy me-1"></i>Salin Teks
                        </button>
                    </div>
                    <div class="card-body p-3">
                        @php
                            $seasonPublicUrl = route('public.season.landing', \App\Http\Controllers\BracketController::encodeId($season->id));
                        @endphp
                        <textarea id="textareaInfoWebsite" class="form-control bg-light border-0 small text-dark p-3 font-monospace" rows="14" readonly style="font-size: 0.78rem;">Halo, ges! 👋

Seluruh informasi mengenai {{ $season->name }} bisa kalian akses melalui website berikut:

🌐 {{ $seasonPublicUrl }}

Di website tersebut kalian bisa melihat:

🏆 Bagan/Bracket Turnamen (ada juga cek musuh dan nomer wa musuh)
📅 Jadwal Tanding
📖 Rules Turnamen
💬 Chat dengan Admin
📝 Form Laporan Hasil Pertandingan

Jadi sebelum bertanya di grup, pastikan cek website terlebih dahulu ya, karena seluruh informasi terbaru akan selalu diperbarui di sana. 🔥</textarea>
                    </div>
                </div>
                
                {{-- 1. Template Juara --}}
                <div class="card border border-light-subtle rounded-3 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="fw-bold text-dark small"><i class="bi bi-trophy-fill text-warning me-1"></i>Template Pengumuman Juara</span>
                        <button class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem;" onclick="copyText('textareaJuara')">
                            <i class="bi bi-copy me-1"></i>Salin Teks
                        </button>
                    </div>
                    <div class="card-body p-3">
                        @php
                            $juara1 = '[Belum Ditentukan]';
                            $juara2 = '[Belum Ditentukan]';
                            $juara3 = null;
                            $juara4 = null;

                            if (!empty($season->manual_juara1)) {
                                $juara1 = $season->manual_juara1;
                                $juara2 = $season->manual_juara2 ?? '[Belum Ditentukan]';
                                $juara3 = $season->manual_juara3 ?: null;
                                $juara4 = $season->manual_juara4 ?: null;
                            } else {
                                $finalRoundNumber = $brackets->max('round_number') ?? 0;
                                $finalMatch = $brackets->where('round_number', $finalRoundNumber)->where('match_number', 1)->first();
                                $bronzeMatchObj = $brackets->where('round_number', $finalRoundNumber)->where('match_number', 2)->first();

                                if ($finalMatch && $finalMatch->status === 'finished' && $finalMatch->winner) {
                                    $juara1 = $finalMatch->winner->name;
                                    $juara2 = ($finalMatch->winner_id == $finalMatch->team1_id) 
                                        ? ($finalMatch->team2->name ?? '[Belum Ditentukan]') 
                                        : ($finalMatch->team1->name ?? '[Belum Ditentukan]');
                                }

                                if ($bronzeMatchObj && $bronzeMatchObj->status === 'finished' && $bronzeMatchObj->winner) {
                                    $juara3 = $bronzeMatchObj->winner->name;
                                }
                            }
                        @endphp
                        <textarea id="textareaJuara" class="form-control bg-light border-0 small text-dark p-3 font-monospace" rows="8" readonly style="font-size: 0.78rem;">*🎉🏆 JUARA YOMUDA CHAMPIONSHIP {{ strtoupper($season->name) }} 🏆🎉*
Pengumuman Juara Yomuda Championship {{ $season->name }} resmi dirilis! 🎉

Berikut kami umumkan para juara turnamen kali ini:

🥇 *Juara 1: {{ $juara1 }}* 
🥈 *Juara 2: {{ $juara2 }}* @if($juara3)

🥉 *Juara 3: {{ $juara3 }}*@endif @if($juara4)

🏅 *Juara 4: {{ $juara4 }}*@endif

Selamat kepada para pemenang! Kalian udah menunjukkan permainan terbaik dan pantas jadi yang teratas! 🔥💯

Untuk seluruh peserta lainnya, terima kasih sudah berjuang dengan sportif dan all-out di setiap match. Tetap semangat, setiap turnamen adalah pengalaman buat jadi lebih kuat! 💪⚔️</textarea>
                    </div>
                </div>

                {{-- 2. Template Roomtour --}}
                <div class="card border border-light-subtle rounded-3 mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="fw-bold text-dark small"><i class="bi bi-camera-video-fill text-info me-1"></i>List Bracket Roomtour (Acak Adil)</span>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary py-1 px-2.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem;" onclick="generateRoomtour()">
                                <i class="bi bi-shuffle me-1"></i>Acak Ulang
                            </button>
                            <button class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem;" onclick="copyText('roomtourTextarea')">
                                <i class="bi bi-copy me-1"></i>Salin Teks
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <textarea id="roomtourTextarea" class="form-control bg-light border-0 small text-dark p-3 font-monospace" rows="8" readonly style="font-size: 0.78rem;"></textarea>
                    </div>
                </div>

                {{-- 3. Template Sertifikat --}}
                <div class="card border border-light-subtle rounded-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2.5 px-3">
                        <span class="fw-bold text-dark small"><i class="bi bi-file-earmark-text-fill text-primary me-1"></i>Template Pembagian Sertifikat</span>
                        <button class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-pill fw-bold text-uppercase" style="font-size: 0.72rem;" onclick="copyText('textareaSertifikat')">
                            <i class="bi bi-copy me-1"></i>Salin Teks
                        </button>
                    </div>
                    <div class="card-body p-3">
                        @php
                            $shortLink = url("/sertifikat/" . \Illuminate\Support\Str::slug($season->name));
                        @endphp
                        <textarea id="textareaSertifikat" class="form-control bg-light border-0 small text-dark p-3 font-monospace" rows="8" readonly style="font-size: 0.78rem;">Untuk seluruh peserta lainnya, terima kasih sudah berjuang dengan sportif dan all-out di setiap match. Tetap semangat, setiap turnamen adalah pengalaman buat jadi lebih kuat! 💪⚔️

📨 E-sertifikat dapat diunduh melalui link berikut:
👉 [{{ $shortLink }}]

Sampai ketemu di *Yomuda Championship/Fast Tour Season Berikutnya* !</textarea>
                    </div>
                </div>

            </div>
            <div class="modal-footer border-top border-light p-3">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold small" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.bracket-js-roomtour')

{{-- Modal Input Juara Manual --}}
<div class="modal fade" id="modalManualWinners" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 text-dark">
            <div class="modal-header border-bottom border-light p-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-trophy-fill text-warning me-2"></i>Input / Edit Juara Manual
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form id="formManualWinners">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 small mb-3">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Jika data juara manual diisi, maka rekap AI di <b>/dashboard</b> dan Teks Share WA akan menggunakan juara manual ini. Jika dikosongkan/direset, sistem akan otomatis mengambil juara dari hasil pertandingan bracket.
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">🥇 Juara 1</label>
                        <input type="text" list="teamsList" class="form-control form-control-sm rounded-3" id="inputManualJuara1" name="manual_juara1" value="{{ $season->manual_juara1 }}" placeholder="Contoh: TEAM OPM">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">🥈 Juara 2</label>
                        <input type="text" list="teamsList" class="form-control form-control-sm rounded-3" id="inputManualJuara2" name="manual_juara2" value="{{ $season->manual_juara2 }}" placeholder="Contoh: TEAM EVOS">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">🥉 Juara 3 (Opsional)</label>
                        <input type="text" list="teamsList" class="form-control form-control-sm rounded-3" id="inputManualJuara3" name="manual_juara3" value="{{ $season->manual_juara3 }}" placeholder="Contoh: TEAM RRQ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">🏅 Juara 4 (Opsional)</label>
                        <input type="text" list="teamsList" class="form-control form-control-sm rounded-3" id="inputManualJuara4" name="manual_juara4" value="{{ $season->manual_juara4 }}" placeholder="Contoh: TEAM ONIC">
                    </div>

                    <datalist id="teamsList">
                        @foreach($teams as $t)
                            <option value="{{ $t->name }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="modal-footer border-top border-light p-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" onclick="resetManualWinners()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset (Gunakan Bracket)
                    </button>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-3 fw-semibold small" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold small text-dark">
                            <i class="bi bi-check-lg me-1"></i>Simpan Juara
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('admin.partials.bracket-js-manual-winners')
@endsection
