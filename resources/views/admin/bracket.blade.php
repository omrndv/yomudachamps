@extends('layouts.admin')

@section('content')
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
                    <p class="text-secondary small mb-0 mt-1">Ubah skor, tentukan pemenang, atur jam main, dan pantau kelolosan tim otomatis.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('public.season.bracket', $season->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-eye me-1"></i> Lihat Halaman User
                    </a>
                    
                    @if($brackets->count() > 0)
                        <form action="{{ route('admin.season.bracket.generate', $season->id) }}" method="POST" onsubmit="return confirm('PERINGATAN! Generate ulang bagan akan MENGHAPUS semua skor dan data tanding yang sudah ada. Lanjutkan?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm px-3 fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset & Acak Ulang Bagan
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
                        Anda dapat men-generate bagan secara otomatis dengan menekan tombol di bawah. Sistem akan mengacak posisi tanding seluruh tim secara adil.
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
        {{-- Bracket Tree Viewer (Admin Mode) --}}
        <div class="card border-0 shadow-sm rounded-4" style="background-color: #ffffff; overflow: hidden;">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold text-dark m-0"><i class="bi bi-grid-3x3-gap me-2 text-warning"></i>Visualisasi Bagan</h5>
                <span class="badge bg-light text-secondary rounded-pill px-3 py-1.5 border" style="font-size: 0.75rem;">
                    Petunjuk: Klik kotak pertandingan untuk mengubah skor/jam main
                </span>
            </div>

            <!-- Sticky Round Titles Bar -->
            <div class="round-headers-bar" id="adminRoundHeadersBar">
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

            <!-- Scrollable Bracket Canvas -->
            <div class="bracket-container" id="adminBracketContainer">
                @foreach($rounds as $roundNum => $matches)
                    <div class="bracket-round">
                        @php
                            $roundHeight = 4600;
                            $matchesCount = $matches->count();
                        @endphp
                        @foreach($matches as $match)
                            <div class="match-card {{ $match->status === 'live' ? 'border-primary' : '' }}" 
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
                                    <span>BRACKET {{ $match->match_number }}</span>
                                    <span class="match-card-time">
                                        @if($match->status === 'live')
                                            <span class="badge bg-danger rounded-pill px-1.5 py-0.5" style="font-size: 0.5rem; animation: pulse 1s infinite alternate;">LIVE</span>
                                        @else
                                            <i class="bi bi-clock"></i> {{ $match->match_time ?? '20:00' }}
                                        @endif
                                    </span>
                                </div>

                                {{-- Team 1 Row --}}
                                <div class="team-row {{ $match->winner_id && $match->winner_id === $match->team1_id ? 'winner' : '' }} {{ $match->winner_id && $match->winner_id !== $match->team1_id ? 'loser' : '' }}">
                                    <div class="team-info">
                                        @if($match->team1)
                                            <span class="team-name text-dark fw-semibold">{{ $match->team1->name }}</span>
                                        @else
                                            <span class="team-name text-muted italic">Belum Ada Tim</span>
                                        @endif
                                    </div>
                                    <span class="team-score-box">{{ $match->team1_score }}</span>
                                </div>

                                {{-- Team 2 Row --}}
                                <div class="team-row {{ $match->winner_id && $match->winner_id === $match->team2_id ? 'winner' : '' }} {{ $match->winner_id && $match->winner_id !== $match->team2_id ? 'loser' : '' }}">
                                    <div class="team-info">
                                        @if($match->team2)
                                            <span class="team-name text-dark fw-semibold">{{ $match->team2->name }}</span>
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
                        @if($roundNum < $rounds->count())
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
                    </div>
                @endforeach
            </div>
        </div>
    @endif
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
                        <label class="form-label small fw-bold text-secondary">Jadwal Jam Main</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                            <input type="text" name="match_time" id="modalMatchTime" class="form-control" placeholder="Contoh: 20:00 WIB">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Status Pertandingan</label>
                        <select name="status" id="modalMatchStatus" class="form-select">
                            <option value="upcoming">Belum Main (Upcoming)</option>
                            <option value="live">Sedang Berlangsung (Live)</option>
                            <option value="finished">Selesai (Finished)</option>
                        </select>
                        <small class="text-muted mt-1 d-block" style="font-size: 0.72rem;">
                            Catatan: Memilih status <strong>Selesai</strong> otomatis meloloskan pemenang ke babak berikutnya.
                        </small>
                    </div>

                </div>
                <div class="modal-footer bg-light border-0 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold rounded-pill shadow-sm" id="btnSaveMatch">Simpan Hasil</button>
                </div>
            </form>
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
        cursor: pointer;
        transition: all 0.2s ease;
        z-index: 10;
        position: relative;
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
    }

    .team-row:last-of-type {
        border-bottom: none;
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

    @keyframes pulse {
        from { opacity: 0.6; }
        to { opacity: 1; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('adminBracketContainer');
    const headerBar = document.getElementById('adminRoundHeadersBar');

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

    // Modal Form AJAX submit handler
    const form = document.getElementById('editMatchForm');
    const modalEl = document.getElementById('editMatchModal');
    const modal = new bootstrap.Modal(modalEl);

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
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.reload();
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
});

// Function to populate and open Edit Modal
function openEditMatchModal(match) {
    document.getElementById('modalMatchId').value = match.id;
    document.getElementById('modalT1Name').textContent = match.team1_name;
    document.getElementById('modalT2Name').textContent = match.team2_name;
    document.getElementById('modalT1Score').value = match.team1_score;
    document.getElementById('modalT2Score').value = match.team2_score;
    document.getElementById('modalMatchTime').value = match.match_time;
    document.getElementById('modalMatchStatus').value = match.status;

    const alertEl = document.getElementById('modalIncompleteAlert');
    const input1 = document.getElementById('modalT1Score');
    const input2 = document.getElementById('modalT2Score');
    const statusSelect = document.getElementById('modalMatchStatus');
    const btnSave = document.getElementById('btnSaveMatch');

    // Disable editing if team matchups are not fully set (TBD)
    if (!match.team1_exists || !match.team2_exists) {
        alertEl.classList.remove('d-none');
        input1.disabled = true;
        input2.disabled = true;
        statusSelect.disabled = true;
        btnSave.disabled = true;
    } else {
        alertEl.classList.add('d-none');
        input1.disabled = false;
        input2.disabled = false;
        statusSelect.disabled = false;
        btnSave.disabled = false;
    }

    const modal = new bootstrap.Modal(document.getElementById('editMatchModal'));
    modal.show();
}
</script>
@endsection
