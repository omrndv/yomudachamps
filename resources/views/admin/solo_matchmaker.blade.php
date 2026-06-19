@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2 small border-0 mb-3 rounded-3 shadow-sm">
            @foreach ($errors->all() as $error)
                <li class="list-unstyled"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success py-2 small border-0 mb-3 rounded-3 shadow-sm">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger py-2 small border-0 mb-3 rounded-3 shadow-sm">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        </div>
    @endif

    {{-- Breadcrumb & Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.seasons') }}" class="text-decoration-none text-warning fw-semibold">Daftar Season</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard', $current_season->id) }}" class="text-decoration-none text-warning fw-semibold">{{ $current_season->name }}</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">Solo Matchmaker</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Interactive Matchmaker <span class="text-warning">{{ $current_season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1">Kelompokkan player secara visual. Duo/Trio terikat bersama secara otomatis berdasarkan Nomor WhatsApp.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-success btn-sm px-3 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreateEmptyTeam">
                        <i class="bi bi-folder-plus me-1"></i> Buat Tim Kosong
                    </button>
                    <button class="btn btn-warning btn-sm px-3 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddSolo">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Solo
                    </button>
                    <button class="btn btn-dark btn-sm px-3 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBulkSolo">
                        <i class="bi bi-stack me-1"></i> Bulk Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Drag & Drop Workspace --}}
    <div class="row g-4">
        
        {{-- Left: Unmatched Player Pool divided into 5 Role Sections --}}
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="fw-bold text-dark mb-0">Pool Unmatched (Belum Tergabung)</h5>
                    <span class="badge bg-secondary text-white">{{ $unmatched_players->count() }} Player</span>
                </div>

                {{-- Grouping matching WA numbers to indicate duo/trio --}}
                @php
                    $duoTrioCounts = $unmatched_players->groupBy('wa_number')->map->count();
                    $rolesList = ['Jungler', 'Mid Lane', 'Gold Lane', 'Exp Lane', 'Roamer'];
                @endphp

                <div class="role-sections-container">
                    @foreach($rolesList as $roleName)
                        @php
                            $rolePlayers = $unmatched_players->filter(function($p) use ($roleName) {
                                return strtolower(trim($p->role)) === strtolower(trim($roleName)) || 
                                       (strtolower(trim($roleName)) === 'jungler' && strtolower(trim($p->role)) === 'jungler') ||
                                       (strtolower(trim($roleName)) === 'mid lane' && strtolower(trim($p->role)) === 'mid lane') ||
                                       (strtolower(trim($roleName)) === 'gold lane' && strtolower(trim($p->role)) === 'gold lane') ||
                                       (strtolower(trim($roleName)) === 'exp lane' && strtolower(trim($p->role)) === 'exp lane') ||
                                       (strtolower(trim($roleName)) === 'roamer' && strtolower(trim($p->role)) === 'roamer');
                            });
                        @endphp
                        
                        <div class="role-section mb-3 p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold text-uppercase text-secondary" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    <i class="bi bi-shield-fill text-warning me-1"></i>{{ $roleName }}
                                </span>
                                <span class="badge bg-dark-subtle text-dark" style="font-size: 0.7rem;">{{ $rolePlayers->count() }}</span>
                            </div>

                            <div class="player-drop-zone d-flex flex-wrap gap-2 min-vh-5" id="pool-{{ Str::slug($roleName) }}" data-team-id="" style="min-height: 50px;">
                                @forelse($rolePlayers as $player)
                                    @php
                                        $isDuoTrio = $duoTrioCounts[$player->wa_number] > 1;
                                        $teamSize = $duoTrioCounts[$player->wa_number];
                                    @endphp
                                    <div class="player-card drag-item p-2 border bg-white rounded shadow-sm d-flex flex-column justify-content-between cursor-grab" 
                                         draggable="true" 
                                         data-id="{{ $player->id }}"
                                         data-wa="{{ $player->wa_number }}"
                                         style="width: 100%; font-size: 0.8rem; border-left: 4px solid {{ $isDuoTrio ? '#8b5cf6' : '#f59e0b' }} !important;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark text-truncate" style="max-width: 120px;">{{ $player->wa_number }}</span>
                                            <span class="badge bg-info text-dark" style="font-size: 0.65rem;">{{ $player->rank }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-success small fw-semibold">Rp {{ number_format($player->amount_paid, 0, ',', '.') }}</span>
                                            @if($isDuoTrio)
                                                <span class="badge bg-purple text-white px-2" style="font-size: 0.65rem; background-color: #8b5cf6;" title="Duo/Trio terikat bersama">
                                                    <i class="bi bi-link-45deg me-0.5"></i>{{ $teamSize == 2 ? 'Duo' : 'Trio' }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="d-flex justify-content-end gap-2 border-top pt-1 mt-1">
                                            <button type="button" class="btn btn-link text-warning p-0 m-0" style="font-size: 0.75rem;" 
                                                    onclick="openEditPlayerModal({{ json_encode($player) }})">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <a href="{{ route('admin.solo.delete', $player->id) }}" class="btn btn-link text-danger p-0 m-0" style="font-size: 0.75rem;" 
                                               onclick="return confirm('Hapus player ini?')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center w-100 py-2 text-muted small border-dashed rounded">Kosong</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right: Created Teams & Interactive Drop Zones --}}
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h5 class="fw-bold text-dark mb-3">Daftar Tim Hasil Pengelompokan ({{ $solo_teams->count() }})</h5>
                
                <div class="row g-3">
                    @forelse($solo_teams as $team)
                        <div class="col-md-6">
                            <div class="card border border-warning shadow-sm rounded-3 overflow-hidden bg-white h-100">
                                <div class="card-header bg-warning-subtle py-2.5 px-3 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;">
                                        <i class="bi bi-shield-shaded me-1"></i>{{ $team->name }}
                                    </h6>
                                    <span class="badge bg-dark rounded-pill" id="team-badge-{{ $team->id }}">{{ $team->players->count() }}/5 Player</span>
                                </div>
                                <div class="card-body p-3 team-drop-zone" id="team-zone-{{ $team->id }}" data-team-id="{{ $team->id }}" style="min-height: 180px; background-color: #fafafa;">
                                    
                                    {{-- Role structure overlay for clean view inside target teams --}}
                                    <div class="assigned-players-list d-flex flex-column gap-2">
                                        @forelse($team->players as $player)
                                            @php
                                                $isDuoTrio = $team->players->where('wa_number', $player->wa_number)->count() > 1;
                                            @endphp
                                            <div class="player-card drag-item p-2 border bg-white rounded shadow-sm d-flex flex-column cursor-grab mb-2"
                                                 draggable="true"
                                                 data-id="{{ $player->id }}"
                                                 data-wa="{{ $player->wa_number }}"
                                                 style="font-size: 0.8rem; border-left: 4px solid {{ $isDuoTrio ? '#8b5cf6' : '#10b981' }} !important;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <div class="d-flex flex-column">
                                                        <span class="fw-bold text-dark">{{ $player->wa_number }}</span>
                                                        <span class="text-muted small text-uppercase" style="font-size: 0.65rem;">{{ $player->role }} - {{ $player->rank }}</span>
                                                    </div>
                                                    <button type="button" class="btn btn-link text-danger p-0 m-0" onclick="movePlayerToPool({{ $player->id }})">
                                                        <i class="bi bi-x-circle-fill" style="font-size: 1.1rem;"></i>
                                                    </button>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center border-top pt-1 mt-1">
                                                    <span class="text-success small fw-semibold">Rp {{ number_format($player->amount_paid, 0, ',', '.') }}</span>
                                                    <div class="d-flex gap-2">
                                                        <button type="button" class="btn btn-link text-warning p-0 m-0" style="font-size: 0.7rem;" 
                                                                onclick="openEditPlayerModal({{ json_encode($player) }})">
                                                            <i class="bi bi-pencil-square"></i> Edit
                                                        </button>
                                                        <a href="{{ route('admin.solo.delete', $player->id) }}" class="btn btn-link text-danger p-0 m-0" style="font-size: 0.7rem;" 
                                                           onclick="return confirm('Hapus player ini dari tim & database?')">
                                                            <i class="bi bi-trash"></i> Hapus
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-4 text-secondary small border-dashed rounded bg-light empty-placeholder-text">
                                                Tarik player dari kiri ke mari.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                                @if($team->players->count() > 0)
                                    <div class="card-footer py-2 px-3 border-top bg-light text-center small text-secondary">
                                        WhatsApp Pendaftar: <strong>{{ $team->wa_number }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5 text-muted">
                            <i class="bi bi-shield-exclamation fs-1 mb-2 d-block text-secondary"></i>
                            Belum ada tim solo terbentuk. Klik tombol <strong>Buat Tim Kosong</strong> di atas untuk memulai drag & drop.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Buat Tim Kosong --}}
<div class="modal fade" id="modalCreateEmptyTeam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.solo.createEmptyTeam', $current_season->id) }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark">Buat Tim Solo Kosong</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Tim</label>
                    <input type="text" name="team_name" class="form-control" placeholder="Contoh: Solo Rangers A" required>
                </div>
                <p class="text-secondary small mb-0">Tim ini akan dibuat kosong dengan status <strong>PAID</strong>, siap untuk diisi player secara drag & drop.</p>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-toggle="modal">Batal</button>
                <button type="submit" class="btn btn-success btn-sm px-4 fw-bold rounded-pill text-white">Buat Tim</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Tambah Solo --}}
<div class="modal fade" id="modalAddSolo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.solo.store', $current_season->id) }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark">Tambah Solo Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">No. WhatsApp</label>
                    <input type="text" name="wa_number" class="form-control" placeholder="Contoh: 081234567890" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Role Utama</label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Rank</label>
                        <select name="rank" class="form-select" required>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank }}">{{ $rank }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Nominal Pembayaran</label>
                        <input type="number" name="amount_paid" class="form-control" value="8000" placeholder="Default 8000" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="PAID">PAID (Lunas)</option>
                            <option value="PENDING">PENDING</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold text-dark rounded-pill">Simpan Player</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Bulk Add Solo --}}
<div class="modal fade" id="modalBulkSolo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.solo.bulkStore', $current_season->id) }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark">Bulk Add Solo Players</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">Format input per baris:<br><code class="bg-light px-2 py-1 rounded">No. WA | Role | Rank</code></p>
                <div class="mb-3">
                    <textarea name="bulk_data" class="form-control text-monospace" rows="6" placeholder="08123456789 | Roamer | Legend&#10;08122233344 | Gold Lane | Mythic" required></textarea>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Nominal per Player</label>
                        <input type="number" name="default_amount" class="form-control" value="8000" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Status Default</label>
                        <select name="default_status" class="form-select" required>
                            <option value="PAID">PAID (Lunas)</option>
                            <option value="PENDING">PENDING</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-dark btn-sm px-4 fw-bold rounded-pill">Import Players</button>
            </div>
        </form>
    </div>
</div>

{{-- Inline JS for drag & drop handling and Duo/Trio binding --}}
<style>
    .cursor-grab { cursor: grab; }
    .cursor-grab:active { cursor: grabbing; }
    .drag-over { background-color: #e2e8f0 !important; border: 2px dashed #e2e8f0 !important; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const draggables = document.querySelectorAll('.drag-item');
        const dropZones = document.querySelectorAll('.team-drop-zone');

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', (e) => {
                // Find all players sharing the same WA (duo/trio binding)
                const wa = draggable.getAttribute('data-wa');
                const mates = document.querySelectorAll(`.drag-item[data-wa="${wa}"]`);
                const ids = [];
                mates.forEach(mate => ids.push(mate.getAttribute('data-id')));

                e.dataTransfer.setData('text/plain', JSON.stringify({
                    player_id: draggable.getAttribute('data-id'),
                    wa_number: wa,
                    mate_ids: ids
                }));
            });
        });

        dropZones.forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('drag-over');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('drag-over');
            });

            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('drag-over');
                
                try {
                    const data = JSON.parse(e.dataTransfer.getData('text/plain'));
                    const teamId = zone.getAttribute('data-team-id');
                    
                    // Call API to save status
                    updatePlayerTeamAPI(data.player_id, teamId);
                } catch (err) {
                    console.error("Drop parsing error", err);
                }
            });
        });
    });

    function movePlayerToPool(playerId) {
        // null target moves player back to pool
        updatePlayerTeamAPI(playerId, null);
    }

    function updatePlayerTeamAPI(playerId, teamId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch("{{ route('admin.solo.updatePlayerTeam', $current_season->id) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                player_id: playerId,
                team_id: teamId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Instantly reload to reflect updated state correctly
                window.location.reload();
            } else {
                alert(data.message || 'Gagal memindahkan player.');
            }
        })
        .catch(err => {
            console.error('API Error', err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function openEditPlayerModal(player) {
        const form = document.getElementById('formEditSolo');
        
        // Update form action dynamically
        form.action = `/admin/solo-matchmaker/update/${player.id}`;
        
        // Populate inputs
        document.getElementById('edit_wa_number').value = player.wa_number;
        document.getElementById('edit_role').value = player.role;
        document.getElementById('edit_rank').value = player.rank;
        document.getElementById('edit_amount_paid').value = player.amount_paid;
        document.getElementById('edit_status').value = player.status;

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('modalEditSoloPlayer'));
        modal.show();
    }
</script>

{{-- Modal: Edit Solo Player --}}
<div class="modal fade" id="modalEditSoloPlayer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formEditSolo" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark">Edit Solo Player</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">No. WhatsApp</label>
                    <input type="text" name="wa_number" id="edit_wa_number" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Role Utama</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Rank</label>
                        <select name="rank" id="edit_rank" class="form-select" required>
                            @foreach($ranks as $rank)
                                <option value="{{ $rank }}">{{ $rank }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">Nominal Pembayaran</label>
                        <input type="number" name="amount_paid" id="edit_amount_paid" class="form-control" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="PAID">PAID (Lunas)</option>
                            <option value="PENDING">PENDING</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-warning btn-sm px-4 fw-bold text-dark rounded-pill">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
