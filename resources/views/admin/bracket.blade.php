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
                {{-- Info text --}}
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2.5 fw-bold" style="font-size: 0.68rem;" data-bs-toggle="modal" data-bs-target="#modalRoundTimes">
                        <i class="bi bi-clock-fill text-warning me-1"></i> Jam Babak
                    </button>
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
                    <div class="round-header-item">
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
                $startNumbers = [];
                $currentStart = 1;
                foreach ($rounds as $rNum => $rMatches) {
                    $startNumbers[$rNum] = $currentStart;
                    $currentStart += $rMatches->count();
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
                    <div class="bracket-round">
                        @foreach($columnMatches as $match)
                            <div class="match-card {{ $match->status === 'live' ? 'border-primary' : '' }}" 
                                 id="card_m_{{ $match->round_number }}_{{ $match->match_number }}"
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
                                    <span>BRACKET {{ $startNumbers[$roundNum] + ($match->match_number - 1) }}</span>
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
                                            @if($match->round_number === 1)
                                                <span class="team-name text-success fw-bold">BYE (Lolos)</span>
                                            @else
                                                <span class="team-name text-muted italic">Belum Ada Tim</span>
                                            @endif
                                        @endif
                                    </div>
                                    <span class="team-score-box">{{ $match->team2_score }}</span>
                                </div>
                            </div>
                        @endforeach

                        {{-- Draw dynamic SVG connector lines between columns --}}
                        @if($roundNum < count($rounds))
                            <svg class="round-connectors" viewBox="0 0 80 {{ $roundHeight }}" preserveAspectRatio="none">
                                @for($m = 1; $m <= $matchesCount; $m++)
                                    @php
                                        $nextMatchIndex = ceil($m / 2);
                                        $startY = ($roundHeight / $matchesCount) * ($m - 0.5);
                                        $endY = ($roundHeight / ($matchesCount / 2)) * ($nextMatchIndex - 0.5);
                                        $midX = 40;
                                    @endphp
                                    <path class="connector-line" d="M 0,{{ $startY }} L {{ $midX }},{{ $startY }} L {{ $midX }},{{ $endY }} L 80,{{ $endY }}"></path>
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
                                    <input type="number" id="ymdAddCount" class="form-control form-control-sm" min="1" max="32" value="5">
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-warning btn-sm fw-bold px-3 rounded" onclick="bulkAddYmdSlots()">
                                        <i class="bi bi-plus-lg"></i> Tambahkan
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
                <p class="text-secondary small mb-3">Masukkan jadwal (Tanggal & Jam) tanding untuk masing-masing babak di bawah. Jadwal akan langsung terupdate serentak ke semua pertandingan pada babak terkait.</p>
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
                                    <input type="text" class="form-control" id="roundTime_{{ $roundNum }}" value="{{ $firstMatch->match_time ?? '20:00 WIB' }}" placeholder="Contoh: 29 Juni, 20:00 WIB">
                                    <button class="btn btn-warning" type="button" onclick="saveRoundTime({{ $roundNum }})">
                                        <i class="bi bi-check-lg"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                <button type="button" class="btn btn-secondary btn-sm px-4 fw-bold rounded-pill text-white" data-bs-dismiss="modal">Selesai</button>
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
                    $babak1Matches = $brackets->where('round_number', 1)->sortBy('match_number');
                    foreach($babak1Matches as $m) {
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

<style>
    /* Styling for Admin Bracket Board Canvas */
    .round-headers-bar {
        display: flex;
        background-color: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        padding: 8px 30px;
        white-space: nowrap;
        overflow-x: hidden;
        font-size: 0.72rem;
        font-weight: 800;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .round-header-item {
        width: 185px;
        margin-right: 80px;
        flex-shrink: 0;
        text-align: center;
    }

    .bracket-container {
        padding: 30px 30px 40px 30px;
        overflow: auto;
        white-space: nowrap;
        scrollbar-width: thin;
        scrollbar-color: #e2e8f0 #ffffff;
        scroll-behavior: smooth;
        height: 620px;
        position: relative;
    }

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

    .match-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        width: 185px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
        z-index: 10;
        position: relative;
        cursor: pointer;
    }

    .match-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .match-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f8fafc;
        border-bottom: 1px solid #f1f5f9;
        padding: 3px 6px;
        font-size: 0.6rem;
        font-weight: 800;
        color: #94a3b8;
    }

    .match-card-time {
        color: #f97316;
    }

    .team-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 24px;
        font-size: 0.72rem;
        padding-left: 6px;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
        color: #334155;
        transition: background 0.2s ease;
    }

    .team-row:last-of-type {
        border-bottom: none;
    }

    /* Styling for Drag & Drop drag states */
    .team-row[draggable="true"] {
        cursor: grab;
    }

    .team-row[draggable="true"]:active {
        cursor: grabbing;
    }

    .team-row.drag-over {
        background-color: rgba(255, 122, 0, 0.2) !important;
        outline: 1.5px dashed var(--accent-orange);
    }

    .team-info {
        display: flex;
        align-items: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex-grow: 1;
    }

    .team-name {
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .team-score-box {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.72rem;
        background-color: #f8fafc;
        color: #94a3b8;
        border-left: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .team-row.winner {
        background-color: #f0fdf4;
    }

    .team-row.winner .team-name {
        color: #166534;
        font-weight: 700;
    }

    .team-row.winner .team-score-box {
        background-color: #22c55e;
        color: #ffffff;
        border-left-color: #22c55e;
    }

    .team-row.loser {
        opacity: 0.5;
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
        stroke: #cbd5e1;
        stroke-width: 1.5;
    }

    .italic {
        font-style: italic;
    }

    /* Highlight matching cards on search */
    .match-card.search-focus-glow {
        border-color: #ff7a00 !important;
        box-shadow: 0 0 15px rgba(255, 122, 0, 0.6) !important;
        transform: scale(1.04);
        z-index: 100;
    }

    @keyframes pulse {
        from { opacity: 0.6; }
        to { opacity: 1; }
    }

    .pulse-dot-admin {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 6px #10b981;
        animation: pulse-green 1.5s infinite alternate;
        display: inline-block;
    }

    @keyframes pulse-green {
        from { opacity: 0.4; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1.1); }
    }

    .bronze-match-wrapper {
        position: absolute;
        bottom: 30px;
        right: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 50;
    }

    .bronze-match-title {
        font-size: 0.65rem;
        font-weight: 800;
        color: #f97316;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
        text-align: center;
    }

    /* ---------------------------------------------------- */
    /* Theme Dark Styles for Bracket Container */
    /* ---------------------------------------------------- */
    #bracketCardContainer.theme-dark {
        background-color: #141416 !important;
        border-color: #3f3f46 !important;
    }
    #bracketCardContainer.theme-dark .round-headers-bar {
        background-color: #1e1e24 !important;
        border-bottom-color: #3f3f46 !important;
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .bracket-container {
        scrollbar-color: #ff7a00 #141416;
    }
    #bracketCardContainer.theme-dark .bracket-container::-webkit-scrollbar-track {
        background: #141416;
    }
    #bracketCardContainer.theme-dark .bracket-container::-webkit-scrollbar-thumb {
        background: #3f3f46;
        border-radius: 3px;
    }
    #bracketCardContainer.theme-dark .bracket-container::-webkit-scrollbar-thumb:hover {
        background: #ff7a00;
    }
    #bracketCardContainer.theme-dark .match-card {
        background-color: #2d2d35 !important;
        border-color: #3f3f46 !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.25);
    }
    #bracketCardContainer.theme-dark .match-card:hover {
        border-color: #52525b !important;
    }
    #bracketCardContainer.theme-dark .match-card-header {
        background-color: #202024 !important;
        border-bottom-color: #3f3f46 !important;
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .match-card-header .match-card-time {
        color: #ff7a00 !important;
    }
    #bracketCardContainer.theme-dark .team-row {
        background-color: #2d2d35 !important;
        border-bottom-color: rgba(255, 255, 255, 0.03) !important;
        color: #f4f4f5 !important;
    }
    #bracketCardContainer.theme-dark .team-row:hover {
        background-color: #373740 !important;
    }
    #bracketCardContainer.theme-dark .team-seed {
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .team-name {
        color: #f4f4f5 !important;
    }
    #bracketCardContainer.theme-dark .team-name.text-muted {
        color: #3f3f46 !important;
        opacity: 0.3;
    }
    #bracketCardContainer.theme-dark .team-score-box {
        background-color: #202024 !important;
        border-left-color: #3f3f46 !important;
        color: #a1a1aa !important;
    }
    #bracketCardContainer.theme-dark .team-row.winner {
        background-color: rgba(255, 122, 0, 0.02) !important;
    }
    #bracketCardContainer.theme-dark .team-row.winner .team-name {
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #bracketCardContainer.theme-dark .team-row.winner .team-score-box {
        background-color: #ff7a00 !important;
        color: #000000 !important;
    }
    #bracketCardContainer.theme-dark .team-row.loser {
        opacity: 0.45;
        background-color: transparent !important;
    }
    #bracketCardContainer.theme-dark .team-row.loser .team-name {
        color: #f4f4f5 !important;
    }
    #bracketCardContainer.theme-dark .round-connectors path {
        stroke: #44444f !important;
        stroke-width: 1.5;
    }
    #bracketCardContainer.theme-dark .bronze-match-title {
        color: #ff7a00 !important;
    }
</style>

<script>
let container = null;
document.addEventListener('DOMContentLoaded', function() {
    container = document.getElementById('adminBracketContainer');
    const headerBar = document.getElementById('adminRoundHeadersBar');

    // ----------------------------------------------------
    // Restore Scroll Position seamlessly (NO MORE RESETTING SCROLL ON ACTION!)
    // ----------------------------------------------------
    if (container) {
        const savedLeft = sessionStorage.getItem('admin_bracket_scroll_left');
        const savedTop = sessionStorage.getItem('admin_bracket_scroll_top');
        if (savedLeft !== null && savedTop !== null) {
            container.scrollLeft = parseFloat(savedLeft);
            container.scrollTop = parseFloat(savedTop);
            sessionStorage.removeItem('admin_bracket_scroll_left');
            sessionStorage.removeItem('admin_bracket_scroll_top');
        }
    }

    // Helper function to save scroll state and reload page
    function saveScrollAndReload() {
        if (container) {
            sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
            sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
        }
        window.location.reload();
    }

    if (container && headerBar) {
        // Sync header horizontal scrolling
        container.addEventListener('scroll', function() {
            headerBar.scrollLeft = container.scrollLeft;
        });

        // Drag to scroll
        let isDown = false;
        let startX, startY;
        let scrollLeft, scrollTop;

        container.addEventListener('mousedown', (e) => {
            if (e.target.closest('.match-card')) return;
            isDown = true;
            container.style.cursor = 'grabbing';
            startX = e.pageX - container.offsetLeft;
            startY = e.pageY - container.offsetTop;
            scrollLeft = container.scrollLeft;
            scrollTop = container.scrollTop;
        });
        
        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.style.cursor = 'default';
        });
        
        container.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'default';
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

    // Modal Form AJAX submit handler
    const form = document.getElementById('editMatchForm');
    const modalEl = document.getElementById('editMatchModal');
    const modal = new bootstrap.Modal(modalEl);

    // Reset Match Button Handler
    const btnResetMatch = document.getElementById('btnResetMatch');
    if (btnResetMatch) {
        btnResetMatch.addEventListener('click', function() {
            const matchId = document.getElementById('modalMatchId').value;
            if (!matchId) return;

            Swal.fire({
                title: 'Reset Pertandingan?',
                text: 'Semua skor akan di-nol-kan dan status dikembalikan ke awal. Bagan di babak selanjutnya yang terpengaruh juga akan dibersihkan secara otomatis.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('match_id', matchId);
                    formData.append('team1_score', '0');
                    formData.append('team2_score', '0');
                    formData.append('status', 'upcoming');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch("{{ route('admin.season.bracket.update-match', $season->id) }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            modal.hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pertandingan berhasil direset.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                saveScrollAndReload();
                            });
                        } else {
                            Swal.fire('Gagal', res.message || 'Gagal meriset pertandingan.', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Terjadi kesalahan koneksi saat meriset pertandingan.', 'error');
                    });
                }
            });
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = new FormData(form);
        const submitBtn = document.getElementById('btnSaveMatch');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

        fetch("{{ route('admin.season.bracket.update-match', $season->id) }}", {
            method: 'POST',
            body: data,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(res => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Hasil';
            
            if (res.success) {
                modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: res.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    saveScrollAndReload(); // Use smooth scroll reload
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: res.message || 'Gagal menyimpan perubahan tanding.'
                });
            }
        })
        .catch(err => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan Hasil';
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Error',
                text: 'Terjadi kegagalan koneksi atau error di sisi server.'
            });
        });
    });

    // ----------------------------------------------------
    // Drag and Drop Auto-Scroll boundaries logic
    // ----------------------------------------------------
    let autoScrollInterval = null;
    let scrollXSpeed = 0;
    let scrollYSpeed = 0;

    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        if (!draggedElement) return;

        const rect = container.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        
        const edgeSize = 65; // Distance in pixels from edge to trigger scroll
        const maxSpeed = 22; // Maximum scroll speed
        
        scrollXSpeed = 0;
        scrollYSpeed = 0;
        
        // Vertical Scroll boundaries
        if (mouseY < edgeSize) {
            scrollYSpeed = -Math.max(3, maxSpeed * (1 - mouseY / edgeSize));
        } else if (mouseY > rect.height - edgeSize) {
            const dist = rect.height - mouseY;
            scrollYSpeed = Math.max(3, maxSpeed * (1 - dist / edgeSize));
        }
        
        // Horizontal Scroll boundaries
        if (mouseX < edgeSize) {
            scrollXSpeed = -Math.max(3, maxSpeed * (1 - mouseX / edgeSize));
        } else if (mouseX > rect.width - edgeSize) {
            const dist = rect.width - mouseX;
            scrollXSpeed = Math.max(3, maxSpeed * (1 - dist / edgeSize));
        }
        
        // Handle trigger interval
        if (scrollXSpeed !== 0 || scrollYSpeed !== 0) {
            if (!autoScrollInterval) {
                autoScrollInterval = setInterval(() => {
                    container.scrollLeft += scrollXSpeed;
                    container.scrollTop += scrollYSpeed;
                }, 16);
            }
        } else {
            clearInterval(autoScrollInterval);
            autoScrollInterval = null;
        }
    });

    const stopDragScroll = () => {
        if (autoScrollInterval) {
            clearInterval(autoScrollInterval);
            autoScrollInterval = null;
        }
    };

    container.addEventListener('dragend', stopDragScroll);
    container.addEventListener('drop', stopDragScroll);

    // ----------------------------------------------------
    // Drag and Drop (Rearrange Seeding inside Round 1)
    // ----------------------------------------------------
    let draggedElement = null;

    const draggableRows = document.querySelectorAll('.team-row[draggable="true"]');
    
    draggableRows.forEach(row => {
        row.addEventListener('dragstart', function(e) {
            draggedElement = this;
            e.dataTransfer.effectAllowed = 'move';
            this.style.opacity = '0.5';
        });

        row.addEventListener('dragend', function() {
            draggedElement = null;
            this.style.opacity = '1';
            draggableRows.forEach(r => r.classList.remove('drag-over'));
        });

        row.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (draggedElement && draggedElement !== this) {
                this.classList.add('drag-over');
            }
        });

        row.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });

        row.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (!draggedElement || draggedElement === this) return;

            const m1_id = draggedElement.dataset.matchId;
            const slot1 = draggedElement.dataset.slot;
            const m2_id = this.dataset.matchId;
            const slot2 = this.dataset.slot;

            Swal.fire({
                title: 'Tukar Posisi Tim?',
                text: "Anda akan menukar posisi tim ini di Babak 1.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Tukar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    
                    fetch("{{ route('admin.season.bracket.swap-teams', $season->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            match1_id: m1_id,
                            slot1: slot1,
                            match2_id: m2_id,
                            slot2: slot2
                        })
                    })
                    .then(response => response.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: res.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                saveScrollAndReload(); // Use smooth scroll reload
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: res.message
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Gagal menukar posisi tim karena masalah koneksi.'
                        });
                    });
                }
            });
        });
    });

    // ----------------------------------------------------
    // Search Engine & Theme Switcher inside Admin View
    // ----------------------------------------------------
    const adminSearchInput = document.getElementById('adminTeamSearch');
    const toggleSearchModeBtn = document.getElementById('toggleSearchModeBtn');
    let searchMode = 'name'; // 'name' or 'wa'

    if (toggleSearchModeBtn) {
        toggleSearchModeBtn.addEventListener('click', function() {
            if (searchMode === 'name') {
                searchMode = 'wa';
                toggleSearchModeBtn.innerHTML = '<i class="bi bi-whatsapp"></i> No. WA';
                adminSearchInput.placeholder = 'Cari nomor WA kapten...';
            } else {
                searchMode = 'name';
                toggleSearchModeBtn.innerHTML = '<i class="bi bi-person-fill"></i> Nama';
                adminSearchInput.placeholder = 'Cari nama tim...';
            }
            // Trigger input event to re-evaluate search with new mode
            adminSearchInput.dispatchEvent(new Event('input'));
        });
    }

    adminSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.match-card').forEach(card => card.classList.remove('search-focus-glow'));

        if (!query) return;

        let selector = `.team-row[data-team-name*="${query}"]`;
        if (searchMode === 'wa') {
            selector = `.team-row[data-team-wa*="${query}"]`;
        }

        const matchRows = document.querySelectorAll(selector);
        let firstCard = null;
        matchRows.forEach(matchRow => {
            const targetCard = matchRow.closest('.match-card');
            if (targetCard) {
                targetCard.classList.add('search-focus-glow');
                if (!firstCard) {
                    firstCard = targetCard;
                }
            }
        });

        if (firstCard) {
            const containerRect = container.getBoundingClientRect();
            const cardRect = firstCard.getBoundingClientRect();
            
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
    });

    // Theme Switcher Logic
    const toggleBracketThemeSwitch = document.getElementById('toggleBracketThemeSwitch');
    const bracketCardContainer = document.getElementById('bracketCardContainer');

    if (toggleBracketThemeSwitch && bracketCardContainer) {
        // Default theme is dark
        const savedTheme = localStorage.getItem('admin_bracket_theme') || 'dark';
        
        if (savedTheme === 'dark') {
            toggleBracketThemeSwitch.checked = true;
            bracketCardContainer.classList.add('theme-dark');
            bracketCardContainer.classList.remove('theme-light');
        } else {
            toggleBracketThemeSwitch.checked = false;
            bracketCardContainer.classList.add('theme-light');
            bracketCardContainer.classList.remove('theme-dark');
        }

        toggleBracketThemeSwitch.addEventListener('change', function() {
            if (this.checked) {
                bracketCardContainer.classList.add('theme-dark');
                bracketCardContainer.classList.remove('theme-light');
                localStorage.setItem('admin_bracket_theme', 'dark');
            } else {
                bracketCardContainer.classList.add('theme-light');
                bracketCardContainer.classList.remove('theme-dark');
                localStorage.setItem('admin_bracket_theme', 'light');
            }
        });
    }

    // ----------------------------------------------------
    // Search & Filter inside YMD Slots Modal
    // ----------------------------------------------------
    const modalYmdSearch = document.getElementById('modalYmdSearch');
    if (modalYmdSearch) {
        modalYmdSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('#modalYmdTable tbody tr').forEach(row => {
                const firstCell = row.querySelector('td:first-child');
                const secondCellInput = row.querySelector('input[type="text"]');
                
                const slotName = firstCell ? firstCell.textContent.toLowerCase() : '';
                const renameVal = secondCellInput ? secondCellInput.value.toLowerCase() : '';

                if (slotName.includes(query) || renameVal.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // ----------------------------------------------------
    // LIVE Real-Time Polling (Sync database updates without refresh)
    // Optimized with Page Visibility API to save bandwidth
    // ----------------------------------------------------
    let pollingInterval = null;

    function fetchLatestBracketData() {
        const isModalOpen = document.querySelectorAll('.modal.show').length > 0;
        const isDragging = draggedElement !== null;

        // Only poll if no modal is active and admin is not currently dragging a row
        if (!isModalOpen && !isDragging) {
            fetch("{{ route('public.season.bracket.data', \App\Http\Controllers\BracketController::encodeId($season->id)) }}")
                .then(r => r.json())
                .then(res => {
                    if (res.success && res.matches) {
                        res.matches.forEach(m => {
                            const card = document.getElementById(`card_m_${m.round_number}_${m.match_number}`);
                            if (card) {
                                // 1. Update time / live status badge
                                const timeSpan = card.querySelector('.match-card-time');
                                if (timeSpan) {
                                    if (m.status === 'live') {
                                        timeSpan.innerHTML = '<span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.5rem; animation: pulse 1s infinite alternate;">LIVE</span>';
                                    } else {
                                        timeSpan.innerHTML = `<i class="bi bi-clock"></i> ${m.match_time || '20:00 WIB'}`;
                                    }
                                }

                                // 2. Update border class
                                if (m.status === 'live') {
                                    card.classList.add('border-primary');
                                } else {
                                    card.classList.remove('border-primary');
                                }

                                // 3. Update Team 1 row details
                                const row1 = card.querySelector('.team-row[data-slot="1"]');
                                if (row1) {
                                    row1.dataset.teamId = m.team1_id || '';
                                    row1.dataset.teamName = m.team1_name ? m.team1_name.toLowerCase() : '';
                                    
                                    row1.className = `team-row ${m.winner_id && m.winner_id === m.team1_id ? 'winner' : ''} ${m.winner_id && m.winner_id !== m.team1_id ? 'loser' : ''}`;
                                    
                                    const nameSpan = row1.querySelector('.team-name');
                                    if (nameSpan) {
                                        nameSpan.className = m.team1_name ? 'team-name text-dark fw-semibold' : 'team-name text-muted italic';
                                        nameSpan.textContent = m.team1_name || 'Belum Ada Tim';
                                    }
                                    const scoreBox = row1.querySelector('.team-score-box');
                                    if (scoreBox) scoreBox.textContent = m.team1_score;
                                }

                                // 4. Update Team 2 row details
                                const row2 = card.querySelector('.team-row[data-slot="2"]');
                                if (row2) {
                                    row2.dataset.teamId = m.team2_id || '';
                                    row2.dataset.teamName = m.team2_name ? m.team2_name.toLowerCase() : '';
                                    
                                    row2.className = `team-row ${m.winner_id && m.winner_id === m.team2_id ? 'winner' : ''} ${m.winner_id && m.winner_id !== m.team2_id ? 'loser' : ''}`;
                                    
                                    const nameSpan = row2.querySelector('.team-name');
                                    if (nameSpan) {
                                        if (m.team2_name) {
                                            nameSpan.className = 'team-name text-dark fw-semibold';
                                            nameSpan.textContent = m.team2_name;
                                        } else {
                                            if (m.round_number === 1) {
                                                nameSpan.className = 'team-name text-success fw-bold';
                                                nameSpan.textContent = 'BYE (Lolos)';
                                            } else {
                                                nameSpan.className = 'team-name text-muted italic';
                                                nameSpan.textContent = 'Belum Ada Tim';
                                            }
                                        }
                                    }
                                    const scoreBox = row2.querySelector('.team-score-box');
                                    if (scoreBox) scoreBox.textContent = m.team2_score;
                                }

                                // 5. Update open modal click handler payload
                                card.setAttribute('onclick', `openEditMatchModal(${JSON.stringify({
                                    id: m.id,
                                    team1_name: m.team1_name || 'TBD',
                                    team2_name: m.team2_name || 'TBD',
                                    team1_score: m.team1_score,
                                    team2_score: m.team2_score,
                                    match_time: m.match_time || '20:00 WIB',
                                    status: m.status,
                                    team1_exists: !!m.team1_id,
                                    team2_exists: !!m.team2_id
                                })})`);
                            }
                        });
                    }
                })
                .catch(err => console.log("Realtime sync issue:", err));
        }
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

    // Stop polling when tab is inactive
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopPolling();
        } else {
            fetchLatestBracketData();
            startPolling();
        }
    });

    startPolling();
});

// ----------------------------------------------------
// Save Jam Main per Babak
// ----------------------------------------------------
function saveRoundTime(roundNum) {
    const inputVal = document.getElementById(`roundTime_${roundNum}`).value;
    const totalRounds = {{ count($rounds) }};
    let roundLabel = `Babak ${roundNum}`;
    if (roundNum === totalRounds) {
        roundLabel = "Grand Final";
    } else if (roundNum === totalRounds - 1 && totalRounds > 1) {
        roundLabel = "Semifinal";
    }

    Swal.fire({
        title: 'Ubah Jadwal Babak?',
        text: `Semua pertandingan di ${roundLabel} akan diubah jadwalnya menjadi: "${inputVal}". Lanjutkan?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Perbarui!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.update-round-times', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    round_number: roundNum,
                    match_time: inputVal
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (typeof saveScrollAndReload === 'function') {
                            saveScrollAndReload();
                        } else {
                            const container = document.getElementById('adminBracketContainer');
                            if (container) {
                                sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
                                sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
                            }
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal mengubah jadwal karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Save / Rename YMD Slot Team
// ----------------------------------------------------
function renameYmdSlot(teamId, oldName) {
    const inputVal = document.getElementById(`ymdRenameInput_${teamId}`).value.trim();
    const priceVal = document.getElementById(`ymdPriceInput_${teamId}`).value.trim();
    const parsedPrice = parseInt(priceVal) || 0;

    if (!inputVal) {
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Nama tim baru tidak boleh kosong!'
        });
        return;
    }

    Swal.fire({
        title: 'Ganti Nama Slot?',
        text: `Ubah slot "${oldName}" menjadi "${inputVal}" dengan harga Rp ${parsedPrice.toLocaleString('id-ID')}? Perubahan akan langsung terlihat di bagan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.rename-ymd-slot', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    team_id: teamId,
                    new_name: inputVal,
                    price: parsedPrice
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        const container = document.getElementById('adminBracketContainer');
                        if (container) {
                            sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
                            sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal mengubah nama slot karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Delete All YMD Slots
// ----------------------------------------------------
function deleteAllYmdSlots() {
    Swal.fire({
        title: 'Hapus Semua Slot YMD?',
        text: "Semua tim placeholder berawalan YMD- di season ini akan dihapus dari database dan bagan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.delete-all-ymd-slots', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghapus slot karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Toggle Bronze Match (Juara 3 & 4)
// ----------------------------------------------------
function toggleBronzeMatchSetting(switchEl) {
    const active = switchEl.checked;
    Swal.showLoading();

    fetch("{{ route('admin.season.bracket.toggle-bronze-match', $season->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            active: active
        })
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: res.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            switchEl.checked = !active; // revert
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: res.message
            });
        }
    })
    .catch(err => {
        switchEl.checked = !active; // revert
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal mengubah pengaturan Bronze Match karena masalah koneksi.'
        });
    });
}

// ----------------------------------------------------
// Bulk Add YMD Slots
// ----------------------------------------------------
function bulkAddYmdSlots() {
    const count = parseInt(document.getElementById('ymdAddCount').value);
    
    if (isNaN(count) || count < 1 || count > 50) {
        Swal.fire({
            icon: 'warning',
            title: 'Jumlah Invalid',
            text: 'Silakan masukkan jumlah slot YMD antara 1 s/d 50.'
        });
        return;
    }

    Swal.fire({
        title: 'Tambah Slot YMD?',
        text: `Anda akan membuat ${count} slot placeholder YMD baru ke database untuk season ini.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f97316',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Tambahkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            
            fetch("{{ route('admin.season.bracket.add-ymd-slots', $season->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    count: count
                })
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        const container = document.getElementById('adminBracketContainer');
                        if (container) {
                            sessionStorage.setItem('admin_bracket_scroll_left', container.scrollLeft);
                            sessionStorage.setItem('admin_bracket_scroll_top', container.scrollTop);
                        }
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: res.message
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menambahkan slot YMD karena masalah koneksi.'
                });
            });
        }
    });
}

// ----------------------------------------------------
// Copy Teams list to Clipboard
// ----------------------------------------------------
function copyTeamsList() {
    const area = document.getElementById('teamsListArea');
    area.select();
    area.setSelectionRange(0, 99999);
    document.execCommand('copy');

    Swal.fire({
        icon: 'success',
        title: 'Disalin!',
        text: 'Daftar nama tim berhasil disalin ke clipboard.',
        timer: 1500,
        showConfirmButton: false
    });
}

// Function to populate and open Edit Modal
function openEditMatchModal(match) {
    document.getElementById('modalMatchId').value = match.id;
    document.getElementById('modalT1Name').textContent = match.team1_name;
    document.getElementById('modalT2Name').textContent = match.team2_name;
    document.getElementById('modalT1Score').value = match.team1_score;
    document.getElementById('modalT2Score').value = match.team2_score;
    document.getElementById('modalMatchTime').value = match.match_time;

    const alertEl = document.getElementById('modalIncompleteAlert');
    const input1 = document.getElementById('modalT1Score');
    const input2 = document.getElementById('modalT2Score');
    const btnSave = document.getElementById('btnSaveMatch');

    const btnReset = document.getElementById('btnResetMatch');

    if (!match.team1_exists && !match.team2_exists) {
        alertEl.classList.remove('d-none');
        alertEl.textContent = 'Pertandingan kosong (kedua tim belum ditentukan) tidak dapat diubah skornya.';
        input1.disabled = true;
        input2.disabled = true;
        btnSave.disabled = true;
        if (btnReset) btnReset.disabled = true;
    } else {
        alertEl.classList.add('d-none');
        input1.disabled = false;
        input2.disabled = false;
        
        input1.readOnly = !match.team1_exists;
        input2.readOnly = !match.team2_exists;
        
        // Styling abu-abu agar terlihat pasif
        if (!match.team1_exists) {
            input1.style.opacity = '0.6';
            input1.style.backgroundColor = '#e9ecef';
        } else {
            input1.style.opacity = '1';
            input1.style.backgroundColor = '#ffffff';
        }
        
        if (!match.team2_exists) {
            input2.style.opacity = '0.6';
            input2.style.backgroundColor = '#e9ecef';
        } else {
            input2.style.opacity = '1';
            input2.style.backgroundColor = '#ffffff';
        }
        
        btnSave.disabled = false;
        if (btnReset) btnReset.disabled = false;
    }

    const modal = new bootstrap.Modal(document.getElementById('editMatchModal'));
    modal.show();
}

// ----------------------------------------------------
// ----------------------------------------------------
// Bracket Visibility Toggle
// ----------------------------------------------------
document.getElementById('toggleBracketVisibility')?.addEventListener('change', function() {
    const label = document.getElementById('bracketVisibilityLabel');
    const isChecked = this.checked;
    
    fetch(`/admin/dashboard/{{ $season->id }}/bracket/toggle-visibility`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (res.is_bracket_visible) {
                label.className = 'text-success';
                label.textContent = '🟢 Bracket Terlihat oleh Peserta';
            } else {
                label.className = 'text-danger';
                label.textContent = '🔴 Bracket Tersembunyi dari Peserta';
            }
        }
    })
    .catch(err => {
        // Revert on error
        this.checked = !isChecked;
        console.error('Toggle visibility error:', err);
    });
});

// Admin Live Chat Dashboard Scripting
// ----------------------------------------------------
const threadsList = document.getElementById('adminChatThreadsList');
const activeThreadTitle = document.getElementById('adminActiveThreadTitle');
const threadSessionTokenInput = document.getElementById('adminThreadSessionToken');
const adminChatMessagesBody = document.getElementById('adminChatMessagesBody');
const adminReplyInput = document.getElementById('adminReplyInput');
const adminBtnReplySend = document.getElementById('adminBtnReplySend');
const adminGlobalUnreadBadge = document.getElementById('adminGlobalUnreadBadge');

let activeThreadToken = null;
let activeThreadName = null;
let adminLastMessageId = 0;
let adminThreadsInterval = null;
let adminMessagesInterval = null;
let adminChatTab = 'active';

// Thread List Styling helpers
function renderThreadListHTML(threads) {
    if (!threads || threads.length === 0) {
        threadsList.innerHTML = `<div class="text-center text-secondary py-5 small">Tidak ada percakapan ${adminChatTab === 'archived' ? 'diarsip' : 'aktif'}.</div>`;
        return;
    }

    let listHTML = '';
    threads.forEach(t => {
        const isSelected = activeThreadToken === t.sender_session_token;
        const activeClass = isSelected ? 'bg-secondary bg-opacity-25 border-start border-3 border-warning' : '';
        const unreadBadge = t.unread_count > 0 ? `<span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.55rem;">${t.unread_count}</span>` : '';
        
        // Truncate message
        let textTruncated = t.last_message || '';
        if (textTruncated.length > 22) {
            textTruncated = textTruncated.substring(0, 20) + '...';
        }
        
        listHTML += `
            <div class="p-3 border-bottom border-secondary border-opacity-10 cursor-pointer ${activeClass}" style="cursor: pointer;" onclick="selectChatThread('${t.sender_session_token}', '${t.sender_name}')">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold small text-white">${t.sender_name}</span>
                    ${unreadBadge}
                </div>
                <div class="small text-secondary mt-1 text-truncate">${t.last_message_is_admin ? 'Anda: ' : ''}${textTruncated}</div>
            </div>
        `;
    });
    threadsList.innerHTML = listHTML;
}

window.selectChatThread = function(token, name) {
    activeThreadToken = token;
    activeThreadName = name;
    activeThreadTitle.textContent = `Percakapan dengan ${name}`;
    threadSessionTokenInput.textContent = token;
    adminReplyInput.disabled = false;
    adminBtnReplySend.disabled = false;
    
    // Enable attachment buttons
    const adminBtnAttach = document.getElementById('adminBtnAttach');
    if (adminBtnAttach) adminBtnAttach.disabled = false;

    // Show delete button
    const adminBtnDeleteThread = document.getElementById('adminBtnDeleteThread');
    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'inline-block';
    
    // Show archive or unarchive button depending on tab
    const adminBtnArchiveThread = document.getElementById('adminBtnArchiveThread');
    const adminBtnUnarchiveThread = document.getElementById('adminBtnUnarchiveThread');
    
    if (adminChatTab === 'active') {
        if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'inline-block';
        if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'none';
    } else {
        if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'none';
        if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'inline-block';
    }

    adminReplyInput.focus();

    // Reset last message id to reload messages correctly
    adminLastMessageId = 0;
    adminChatMessagesBody.innerHTML = '<div class="text-center text-secondary py-5 small"><i class="bi bi-arrow-repeat spin"></i> Memuat pesan...</div>';

    // Mark as read immediately
    fetch(`/admin/dashboard/{{ $season->id }}/chat/read/${token}`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    fetchThreadMessages();
    fetchAdminChatThreads(); // refresh list to clear badge count
};

// Bind actions on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    const adminBtnDeleteThread = document.getElementById('adminBtnDeleteThread');
    const adminBtnArchiveThread = document.getElementById('adminBtnArchiveThread');
    const adminBtnUnarchiveThread = document.getElementById('adminBtnUnarchiveThread');
    const adminBtnClearAllChats = document.getElementById('adminBtnClearAllChats');
    const adminBtnAttach = document.getElementById('adminBtnAttach');
    const adminFileInput = document.getElementById('adminFileInput');
    const adminTabActive = document.getElementById('adminTabActive');
    const adminTabArchived = document.getElementById('adminTabArchived');

    // Tab Switching Bindings
    if (adminTabActive && adminTabArchived) {
        adminTabActive.addEventListener('click', () => {
            adminChatTab = 'active';
            adminTabActive.className = 'btn btn-warning btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            adminTabArchived.className = 'btn btn-outline-secondary text-white btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            fetchAdminChatThreads();
        });

        adminTabArchived.addEventListener('click', () => {
            adminChatTab = 'archived';
            adminTabActive.className = 'btn btn-outline-secondary text-white btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            adminTabArchived.className = 'btn btn-warning btn-sm py-0.5 px-2.5 rounded-pill fw-bold';
            fetchAdminChatThreads();
        });
    }

    // 1. Delete thread
    if (adminBtnDeleteThread) {
        adminBtnDeleteThread.addEventListener('click', () => {
            if (!activeThreadToken) return;
            if (!confirm(`Apakah Anda yakin ingin menghapus seluruh riwayat chat dengan ${activeThreadName} beserta berkas gambar yang dikirim?`)) return;
            
            fetch(`/admin/dashboard/{{ $season->id }}/chat/delete/${activeThreadToken}`, {
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    adminBtnDeleteThread.style.display = 'none';
                    if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'none';
                    if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-chat-dots" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Percakapan berhasil dihapus.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 2. Archive thread
    if (adminBtnArchiveThread) {
        adminBtnArchiveThread.addEventListener('click', () => {
            if (!activeThreadToken) return;
            if (!confirm(`Apakah Anda yakin ingin mengarsipkan percakapan dengan ${activeThreadName} untuk meredam spam?`)) return;

            fetch(`/admin/dashboard/{{ $season->id }}/chat/archive/${activeThreadToken}`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'none';
                    adminBtnArchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-archive" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Percakapan diarsipkan.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 3. Unarchive thread
    if (adminBtnUnarchiveThread) {
        adminBtnUnarchiveThread.addEventListener('click', () => {
            if (!activeThreadToken) return;
            
            fetch(`/admin/dashboard/{{ $season->id }}/chat/unarchive/${activeThreadToken}`, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'none';
                    adminBtnUnarchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Percakapan dikembalikan ke pesan aktif.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 4. Clear all chats (entire season)
    if (adminBtnClearAllChats) {
        adminBtnClearAllChats.addEventListener('click', () => {
            if (!confirm("PERINGATAN! Anda yakin ingin menghapus SELURUH riwayat obrolan dan gambar media di season ini? Tindakan ini tidak dapat dibatalkan.")) return;

            fetch(`/admin/dashboard/{{ $season->id }}/chat/clear-all`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    activeThreadToken = null;
                    activeThreadName = null;
                    activeThreadTitle.textContent = 'Pilih percakapan untuk memulai';
                    if (adminBtnDeleteThread) adminBtnDeleteThread.style.display = 'none';
                    if (adminBtnArchiveThread) adminBtnArchiveThread.style.display = 'none';
                    if (adminBtnUnarchiveThread) adminBtnUnarchiveThread.style.display = 'none';
                    if (adminBtnAttach) adminBtnAttach.disabled = true;
                    adminReplyInput.disabled = true;
                    adminBtnReplySend.disabled = true;
                    adminChatMessagesBody.innerHTML = `
                        <div class="text-center text-secondary my-auto py-5 small">
                            <i class="bi bi-trash" style="font-size: 2.5rem;"></i>
                            <p class="mt-2">Seluruh chat berhasil dibersihkan.</p>
                        </div>
                    `;
                    fetchAdminChatThreads();
                }
            });
        });
    }

    // 5. Admin upload file attachments
    if (adminBtnAttach && adminFileInput) {
        adminBtnAttach.addEventListener('click', () => adminFileInput.click());
        adminFileInput.addEventListener('change', function() {
            if (this.files && this.files[0] && activeThreadToken) {
                const file = this.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert("Ukuran berkas maksimal 2MB!");
                    return;
                }

                const formData = new FormData();
                formData.append('image', file);

                fetch(`/admin/dashboard/{{ $season->id }}/chat/upload/${activeThreadToken}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                        fetchThreadMessages();
                        fetchAdminChatThreads();
                    } else {
                        alert("Gagal mengunggah: " + res.message);
                    }
                })
                .catch(err => console.log("Upload error:", err));
            }
        });
    }
});

let previousGlobalUnread = -1;
let lastSoundPlayedTime = 0;

function playNotificationSound() {
    const now = Date.now();
    if (now - lastSoundPlayedTime < 3000) return; // Prevent spamming sound within 3 seconds
    lastSoundPlayedTime = now;

    try {
        const context = new (window.AudioContext || window.webkitAudioContext)();
        
        // Synth Chime (Ting sound)
        const osc = context.createOscillator();
        const gain = context.createGain();
        
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, context.currentTime); // A5 note
        osc.frequency.exponentialRampToValueAtTime(1320, context.currentTime + 0.1); // Sweep up to E6 note
        
        gain.gain.setValueAtTime(0.12, context.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.6);
        
        osc.connect(gain);
        gain.connect(context.destination);
        
        osc.start();
        osc.stop(context.currentTime + 0.6);
    } catch (e) {
        console.log("AudioContext blocked or not supported yet:", e);
    }
}

// Polling Laporan Hasil Laga (Match Reports)
let previousPendingReportsCount = -1;

function pollAdminMatchReports() {
    fetch("{{ route('admin.season.match-reports.poll', $season->id) }}")
        .then(r => r.json())
        .then(res => {
            if (res.reports) {
                const pendingCount = res.reports.filter(r => r.status === 'PENDING').length;

                if (previousPendingReportsCount !== -1 && pendingCount > previousPendingReportsCount) {
                    playNotificationSound();
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'warning',
                        title: 'Laporan Hasil Laga',
                        text: 'Ada laporan hasil tanding baru yang butuh verifikasi!'
                    });
                }
                previousPendingReportsCount = pendingCount;
            }
        })
        .catch(err => console.log("Report polling error:", err));
}

// Start report polling every 10 seconds
setInterval(pollAdminMatchReports, 10000);
pollAdminMatchReports();

function fetchAdminChatThreads() {
    fetch("{{ route('admin.season.chat.threads', $season->id) }}?status=" + adminChatTab)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.threads) {
                renderThreadListHTML(res.threads);
                
                // Calculate global unread count
                let globalUnread = 0;
                res.threads.forEach(t => {
                    globalUnread += parseInt(t.unread_count || 0);
                });

                // Play sound if unread count increases (new thread or new message in closed thread)
                if (previousGlobalUnread !== -1 && globalUnread > previousGlobalUnread) {
                    playNotificationSound();

                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        showCloseButton: true,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'info',
                        title: 'Pesan Live Chat Baru',
                        text: 'Ada pesan live chat baru dari peserta turnamen.'
                    });
                }
                previousGlobalUnread = globalUnread;

                if (globalUnread > 0) {
                    adminGlobalUnreadBadge.textContent = globalUnread;
                    adminGlobalUnreadBadge.style.display = 'inline-block';
                } else {
                    adminGlobalUnreadBadge.style.display = 'none';
                }
            }
        })
        .catch(err => console.log("Threads load issue:", err));
}

function fetchThreadMessages() {
    if (!activeThreadToken) return;

    fetch(`/admin/dashboard/{{ $season->id }}/chat/messages/${activeThreadToken}`)
        .then(r => r.json())
        .then(res => {
            if (res.success && res.messages) {
                let renderList = false;
                if (adminLastMessageId === 0) {
                    adminChatMessagesBody.innerHTML = '';
                    renderList = true;
                }

                res.messages.forEach(msg => {
                    if (msg.id > adminLastMessageId) {
                        // Play sound on new incoming user message in active open thread
                        if (adminLastMessageId > 0 && !msg.is_admin) {
                            playNotificationSound();
                        }

                        const bubble = document.createElement('div');
                        bubble.className = `p-2 rounded-3 text-white small ${msg.is_admin ? 'bg-secondary bg-opacity-50 align-self-end text-end' : 'bg-dark border border-secondary border-opacity-25 align-self-start'}`;
                        bubble.style.maxWidth = '80%';
                        
                        let displayContent = msg.message;
                        if (msg.message.startsWith('[IMAGE]:')) {
                            const imgUrl = msg.message.substring(8);
                            displayContent = `<img src="${imgUrl}" class="img-fluid rounded-3 my-1" style="max-height: 150px; cursor: pointer; display: block;" onclick="window.open('${imgUrl}', '_blank')" onload="this.closest('.modal-body').querySelector('.d-flex.flex-column').scrollTop = this.closest('.modal-body').querySelector('.d-flex.flex-column').scrollHeight">`;
                        }

                        bubble.innerHTML = `
                            <div class="fw-bold" style="font-size: 0.65rem; color: ${msg.is_admin ? '#cbd5e1' : '#f59e0b'};">${msg.is_admin ? 'Anda (Admin)' : msg.sender_name}</div>
                            <div class="mt-1">${displayContent}</div>
                        `;
                        adminChatMessagesBody.appendChild(bubble);
                        adminLastMessageId = msg.id;
                        renderList = true;
                    }
                });

                if (renderList) {
                    setTimeout(() => {
                        adminChatMessagesBody.scrollTop = adminChatMessagesBody.scrollHeight;
                    }, 80);
                }
            }
        })
        .catch(err => console.log("Messages load issue:", err));
}

function sendAdminReply() {
    const text = adminReplyInput.value.trim();
    if (!text || !activeThreadToken) return;

    adminReplyInput.value = '';

    fetch("{{ route('admin.season.chat.reply', $season->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            sender_session_token: activeThreadToken,
            message: text
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            fetchThreadMessages();
            fetchAdminChatThreads();
        }
    })
    .catch(err => console.log("Reply issue:", err));
}

adminBtnReplySend.addEventListener('click', sendAdminReply);
adminReplyInput.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendAdminReply();
    }
});

// Setup admin listeners
document.getElementById('modalAdminLiveChat').addEventListener('show.bs.modal', () => {
    fetchAdminChatThreads();
    adminThreadsInterval = setInterval(fetchAdminChatThreads, 4000);
    adminMessagesInterval = setInterval(fetchThreadMessages, 3000);
});

document.getElementById('modalAdminLiveChat').addEventListener('hide.bs.modal', () => {
    if (adminThreadsInterval) clearInterval(adminThreadsInterval);
    if (adminMessagesInterval) clearInterval(adminMessagesInterval);
});

// Initialize polling for thread badge counts (global badge)
setInterval(fetchAdminChatThreads, 15000);
fetchAdminChatThreads();
</script>

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
                            $juara3 = '[Belum Ditentukan]';

                            if (!empty($season->manual_juara1)) {
                                $juara1 = $season->manual_juara1;
                                $juara2 = $season->manual_juara2 ?? '[Belum Ditentukan]';
                                $juara3 = $season->manual_juara3 ?? '[Tidak Ada / Belum Ditentukan]';
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
🥈 *Juara 2: {{ $juara2 }}* 
🥉 *Juara 3: {{ $juara3 }}* 

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

<script>
    // Copy to clipboard helper
    function copyText(textareaId) {
        const textarea = document.getElementById(textareaId);
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(textarea.value).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Teks Berhasil Disalin!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }).catch(err => alert("Gagal menyalin teks: " + err));
    }

    // Roomtour bracket random generator logic
    function generateRoomtour() {
        const roundsData = @json($startNumbers);
        const roundKeys = Object.keys(roundsData).map(Number).sort((a,b) => a-b);
        const roundsCount = roundKeys.length;
        
        let text = "*List Bracket yang masuk Live 📺*\n\n";
        
        // Randomly pick for each round starting from round 3 up to semifinals
        for (let i = 0; i < roundKeys.length; i++) {
            const rNum = roundKeys[i];
            
            // Skip Babak 1 and Babak 2
            if (rNum < 3) continue;
            
            // Final is skip since it's "Final" (handled automatically at bottom)
            if (rNum === roundsCount) continue;
            
            const start = roundsData[rNum];
            let nextStart = roundsData[rNum + 1];
            if (!nextStart) {
                nextStart = start + 1;
            }
            
            const matchCount = nextStart - start;
            
            // Choose one random bracket number between start and nextStart - 1
            const randomBracket = Math.floor(Math.random() * matchCount) + start;
            
            let roundLabel = `Babak ${rNum}`;
            if (rNum === roundsCount - 1) {
                roundLabel = "Semifinal";
            } else if (rNum === roundsCount - 2) {
                roundLabel = "Babak 5";
            }
            
            text += `${roundLabel} : Bracket ${randomBracket}\n`;
        }
        
        text += "Final\n\n";
        text += "*Note: yang masuk ke dalam bracket dengan no diatas, mimin yang invite*";
        
        document.getElementById('roomtourTextarea').value = text;
    }

    // Run generateRoomtour once on modal show to prefill
    document.getElementById('modalShareTemplates').addEventListener('show.bs.modal', generateRoomtour);
</script>

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
                        <label class="form-label small fw-bold text-dark">🥉 Juara 3</label>
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

<script>
    document.getElementById('formManualWinners').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch("{{ route('admin.season.bracket.update-manual-winners', $season->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            } else {
                Swal.fire('Error', data.message || 'Gagal menyimpan data.', 'error');
            }
        })
        .catch(err => Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error'));
    });

    function resetManualWinners() {
        if (!confirm('Hapus inputan juara manual dan kembali gunakan hasil otomatis bracket?')) return;
        document.getElementById('inputManualJuara1').value = '';
        document.getElementById('inputManualJuara2').value = '';
        document.getElementById('inputManualJuara3').value = '';
        document.getElementById('inputManualJuara4').value = '';
        document.getElementById('formManualWinners').dispatchEvent(new Event('submit'));
    }
</script>
@endsection
