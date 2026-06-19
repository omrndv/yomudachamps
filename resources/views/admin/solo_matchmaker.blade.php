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
                        Solo Matchmaker <span class="text-warning">{{ $current_season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1">Gabungkan player solo/duo/trio menjadi 1 tim lunas otomatis.</p>
                </div>
                <div class="d-flex gap-2">
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

    {{-- Tabs & Interactive Matchmaker --}}
    <div class="row g-4">
        {{-- Matchmaker Panel --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Player Solo Belum Tergabung ({{ $unmatched_players->count() }})</h5>
                    <button type="button" class="btn btn-success btn-sm fw-bold px-3 rounded-pill shadow-sm" id="btnGroupSelected" disabled data-bs-toggle="modal" data-bs-target="#modalCreateTeam">
                        <i class="bi bi-people-fill me-1"></i> Bentuk 1 Tim (<span id="selectedCount">0</span>/5)
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr class="text-secondary small fw-bold" style="border-bottom: 2px solid #f1f5f9;">
                                <th width="40" class="text-center">Pilih</th>
                                <th>No. WhatsApp</th>
                                <th>Role Utama</th>
                                <th>Rank Saat Ini</th>
                                <th>Nominal Bayar</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" width="80">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unmatched_players as $player)
                            <tr class="player-row" data-id="{{ $player->id }}">
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input player-checkbox" value="{{ $player->id }}" onchange="updateSelection()">
                                </td>
                                <td>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $player->wa_number) }}" target="_blank" class="text-decoration-none text-success">
                                        <i class="bi bi-whatsapp me-1"></i>{{ $player->wa_number }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-white">{{ $player->role }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">{{ $player->rank }}</span>
                                </td>
                                <td class="fw-semibold text-success">Rp {{ number_format($player->amount_paid, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $player->status === 'PAID' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $player->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.solo.delete', $player->id) }}" class="btn btn-link text-danger p-0" onclick="return confirm('Hapus player ini?')">
                                        <i class="bi bi-trash fs-5"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada player solo terdaftar yang belum tergabung.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Matched Teams Panel --}}
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white">
                <h5 class="fw-bold text-dark mb-3">Player Solo yang Sudah Tergabung ({{ $matched_players->count() }})</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr class="text-secondary small fw-bold" style="border-bottom: 2px solid #f1f5f9;">
                                <th>No. WhatsApp</th>
                                <th>Role</th>
                                <th>Rank</th>
                                <th>Nama Tim Hasil Matchmaker</th>
                                <th>Nominal Bayar</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($matched_players as $player)
                            <tr>
                                <td>{{ $player->wa_number }}</td>
                                <td><span class="badge bg-secondary">{{ $player->role }}</span></td>
                                <td><span class="badge bg-info text-dark">{{ $player->rank }}</span></td>
                                <td>
                                    <span class="badge bg-primary text-white">
                                        <i class="bi bi-shield me-1"></i>{{ $player->team->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="fw-semibold text-success">Rp {{ number_format($player->amount_paid, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.solo.delete', $player->id) }}" class="btn btn-link text-danger p-0" onclick="return confirm('Hapus player ini dari tim & database?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada player solo yang tergabung ke dalam tim.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Matchmaking Strategy Insights Sidebar --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle text-warning me-2"></i>Informasi Matchmaker</h6>
                <p class="text-secondary small mb-3">
                    Fitur ini membantu admin mengelompokkan pendaftar solo, duo, atau trio menjadi tim lengkap berisi <strong>5 orang</strong> secara otomatis.
                </p>
                <div class="border-start border-warning border-3 ps-3 py-1 mb-3">
                    <span class="small text-muted d-block">Tips Menyusun Tim:</span>
                    <span class="small fw-semibold text-dark">Usahakan komposisi role ideal (Roamer, Gold Lane, Mid Lane, Exp Lane, Jungler) dan rank yang seimbang agar permainan adil.</span>
                </div>
                <div class="alert bg-light border-0 py-2.5 small mb-0 rounded-3 text-secondary">
                    <i class="bi bi-check2-circle text-success me-2"></i>Tim hasil gabungan solo otomatis berstatus <strong>PAID</strong> dan ditandai sebagai tim solo pada Dashboard Pendapatan.
                </div>
            </div>
        </div>
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
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-toggle="modal">Batal</button>
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

{{-- Modal: Create Team --}}
<div class="modal fade" id="modalCreateTeam" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.solo.group', $current_season->id) }}" method="POST" class="modal-content border-0 shadow rounded-4">
            @csrf
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title fw-bold text-dark">Bentuk Tim Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="teamGroupingModalBody">
                <div class="mb-3">
                    <label class="form-label small fw-bold">Nama Tim Baru</label>
                    <input type="text" name="team_name" class="form-control" placeholder="Contoh: Solo Rangers A" required>
                </div>
                <input type="hidden" name="player_ids[]" class="hidden-player-id">
                <input type="hidden" name="player_ids[]" class="hidden-player-id">
                <input type="hidden" name="player_ids[]" class="hidden-player-id">
                <input type="hidden" name="player_ids[]" class="hidden-player-id">
                <input type="hidden" name="player_ids[]" class="hidden-player-id">
                <p class="text-secondary small mb-0">Kamu memilih 5 player untuk digabungkan menjadi 1 tim baru.</p>
            </div>
            <div class="modal-footer border-top border-light">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success btn-sm px-4 fw-bold rounded-pill">Gabungkan Tim</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateSelection() {
        const checkboxes = document.querySelectorAll('.player-checkbox:checked');
        const count = checkboxes.length;
        document.getElementById('selectedCount').innerText = count;

        const btn = document.getElementById('btnGroupSelected');
        if (count === 5) {
            btn.removeAttribute('disabled');
            // Populating hidden player inputs for modal
            const hiddenInputs = document.querySelectorAll('.hidden-player-id');
            checkboxes.forEach((cb, index) => {
                if (hiddenInputs[index]) {
                    hiddenInputs[index].value = cb.value;
                }
            });
        } else {
            btn.setAttribute('disabled', 'true');
        }
    }
</script>
@endsection
