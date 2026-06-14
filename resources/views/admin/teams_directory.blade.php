@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Direktori Tim Global
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Pencarian peserta terpadu lintas semua season, analisis loyalitas tim, dan filter data komprehensif.
            </p>
        </div>
    </div>

    {{-- Filter Panel --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <form action="{{ route('admin.teams') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Filter Season</label>
                    <select name="season_id" class="form-select border-0 bg-light rounded-3 shadow-none p-2.5" style="font-size: 0.85rem; cursor: pointer;">
                        <option value="">Semua Season</option>
                        @foreach($seasons as $s)
                            <option value="{{ $s->id }}" {{ isset($season_id) && $season_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Status Bayar</label>
                    <select name="status" class="form-select border-0 bg-light rounded-3 shadow-none p-2.5" style="font-size: 0.85rem; cursor: pointer;">
                        <option value="">Semua Status</option>
                        <option value="PAID" {{ isset($status) && $status == 'PAID' ? 'selected' : '' }}>PAID (Lunas)</option>
                        <option value="PENDING" {{ isset($status) && $status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Cari Tim / WA / TRX</label>
                    <input type="text" name="search" class="form-control border-0 bg-light rounded-3 shadow-none p-2.5" placeholder="Masukkan nama tim, nomor WhatsApp, atau ID TRX..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-dark w-100 fw-bold rounded-3 shadow-sm py-2.5" style="font-size: 0.85rem;">
                            <i class="bi bi-filter me-1"></i> CARI DATA
                        </button>
                        <a href="{{ route('admin.teams') }}" class="btn btn-light rounded-3 shadow-sm border border-light-subtle py-2.5 px-3 d-flex align-items-center justify-content-center text-muted" title="Reset Pencarian">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Pendaftar Terfilter --}}
        <div class="col-md-6">
            <div class="card card-stats border-0 p-4 bg-white shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Pendaftaran Ditemukan</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                            {{ $teams->total() }} <span class="fs-6 text-muted fw-normal">registrasi</span>
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                    Berdasarkan kriteria pencarian dan filter yang aktif.
                </div>
            </div>
        </div>

        {{-- Card 2: Jumlah Tim Loyal --}}
        <div class="col-md-6">
            <div class="card card-stats border-0 p-4 bg-white shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Loyalty Rate (Tim Setia)</p>
                        <h3 class="fw-bold text-success mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                            @php
                                $loyalCount = $teams->filter(function($t) { return $t->loyalty_count > 1; })->count();
                            @endphp
                            {{ $loyalCount }} <span class="fs-6 text-muted fw-normal">tim loyal terdaftar di halaman ini</span>
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="bi bi-star-fill text-white fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                    Tim terhitung loyal jika mendaftar di lebih dari satu season turnamen.
                </div>
            </div>
        </div>
    </div>

    {{-- View Switcher and Section Title --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.1rem; letter-spacing: -0.2px;">Daftar Pendaftar</h5>
        <div class="btn-group bg-light p-1 rounded-3" style="border: 1px solid rgba(226, 232, 240, 0.8);">
            <button type="button" class="btn btn-sm border-0 rounded-2 px-3 py-1.5 d-flex align-items-center gap-1.5" id="btnViewTable" onclick="switchView('table')" style="font-size: 0.8rem; font-weight: 600; transition: all 0.2s;">
                <i class="bi bi-table"></i> Tabel
            </button>
            <button type="button" class="btn btn-sm border-0 rounded-2 px-3 py-1.5 d-flex align-items-center gap-1.5" id="btnViewGrid" onclick="switchView('grid')" style="font-size: 0.8rem; font-weight: 600; transition: all 0.2s;">
                <i class="bi bi-grid-3x3-gap-fill"></i> Kartu
            </button>
        </div>
    </div>

    {{-- View Mode: Spacious Table View --}}
    <div id="tableViewContainer" class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-spacious align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light">
                        <tr class="fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; border-bottom: 2px solid #f1f5f9;">
                            <th class="ps-4 py-3 text-center border-0" width="60">#</th>
                            <th class="py-3 border-0">Tim & ID</th>
                            <th class="py-3 border-0">No WhatsApp</th>
                            <th class="py-3 border-0">Season Terdaftar</th>
                            <th class="py-3 border-0">Waktu Daftar</th>
                            <th class="py-3 text-center border-0">Status</th>
                            <th class="py-3 text-center border-0" width="160">Loyalitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $index => $team)
                        <tr>
                            <td class="ps-4 text-center text-muted fw-semibold">
                                {{ ($teams->currentPage() - 1) * $teams->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-uppercase searchable-text">{{ $team->name }}</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">TRX: <span class="fw-semibold searchable-text">{{ $team->trx_id }}</span></div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <code class="text-dark fw-bold searchable-text" style="font-size: 0.8rem;">{{ $team->wa_number }}</code>
                                    @php
                                        $wa = preg_replace('/[^0-9]/', '', $team->wa_number);
                                        $wa_link = str_starts_with($wa, '0') ? '62' . substr($wa, 1) : (str_starts_with($wa, '8') ? '62' . $wa : $wa);
                                    @endphp
                                    <a href="https://wa.me/{{ $wa_link }}" target="_blank" class="btn btn-sm btn-light-success text-success border border-success-subtle rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" title="Chat WhatsApp">
                                        <i class="bi bi-whatsapp" style="font-size: 0.75rem;"></i>
                                    </a>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2.5 py-1.5 rounded-3" style="font-size: 0.75rem;">
                                    {{ $team->season->name }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-slate-700">{{ $team->created_at->format('d M Y') }}</div>
                                <div class="text-muted small" style="font-size: 0.7rem;">{{ $team->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $team->status == 'PAID' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-3 py-1.5 rounded-pill" style="font-size: 0.65rem; font-weight: 600;">
                                    {{ $team->status }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($team->loyalty_count > 1)
                                    @php
                                        // Generate Popover Content listing historical seasons
                                        $popover_content = '<ul class="list-unstyled mb-0 text-start" style="padding-left: 0; min-width: 200px;">';
                                        foreach($team->history as $hist) {
                                            $badgeClass = $hist->status == 'PAID' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle';
                                            $popover_content .= '<li class="mb-2 pb-1 border-bottom border-light-subtle d-flex flex-column">';
                                            $popover_content .= '<span class="fw-bold text-dark" style="font-size: 0.8rem;">' . e($hist->season->name) . '</span>';
                                            $popover_content .= '<span class="text-muted" style="font-size: 0.7rem;">Tim: ' . e($hist->name) . '</span>';
                                            $popover_content .= '<span class="badge ' . $badgeClass . ' align-self-start mt-1" style="font-size: 0.55rem; padding: 2px 8px;">' . $hist->status . '</span>';
                                            $popover_content .= '</li>';
                                        }
                                        $popover_content .= '</ul>';
                                    @endphp
                                    <button type="button" class="btn btn-sm btn-outline-warning border-warning-subtle text-dark rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                            data-bs-toggle="popover" data-bs-title="Riwayat Turnamen" data-bs-content="{{ $popover_content }}" data-bs-trigger="focus" style="font-size: 0.75rem;">
                                        <i class="bi bi-star-fill text-warning"></i> LOYALTY ({{ $team->loyalty_count }})
                                    </button>
                                @else
                                    <span class="text-muted small" style="font-size: 0.75rem;">Baru Terdaftar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-bold">Tidak ada data pendaftaran tim ditemukan.</p>
                                    <small>Coba sesuaikan kata kunci pencarian atau ganti filter season/status.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($teams->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $teams->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    {{-- View Mode: Grid Card View (Spacious & Easy on the Eyes) --}}
    <div id="gridViewContainer" class="d-none mb-4">
        <div class="row g-3">
            @forelse($teams as $team)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 bg-white p-3 h-100 hover-card d-flex flex-column" style="transition: all 0.3s ease; border: 1px solid rgba(241, 245, 249, 0.8) !important;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 rounded-2" style="font-size: 0.7rem; font-weight: 600;">
                            {{ $team->season->name }}
                        </span>
                        <span class="badge {{ $team->status == 'PAID' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' }} px-2.5 py-1 rounded-pill" style="font-size: 0.65rem; font-weight: 600;">
                            {{ $team->status }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold text-dark text-uppercase mb-1 searchable-text" style="font-size: 0.95rem; letter-spacing: -0.2px;">{{ $team->name }}</h6>
                        <small class="text-muted" style="font-size: 0.72rem;">TRX ID: <span class="fw-semibold searchable-text">{{ $team->trx_id }}</span></small>
                    </div>

                    <div class="bg-light rounded-3 p-2.5 mb-3" style="font-size: 0.8rem; border: 1px solid rgba(241, 245, 249, 0.5);">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary" style="font-size: 0.75rem;">WhatsApp:</span>
                            <div class="d-flex align-items-center gap-1.5">
                                <code class="text-dark fw-bold searchable-text" style="font-size: 0.78rem;">{{ $team->wa_number }}</code>
                                @php
                                    $wa = preg_replace('/[^0-9]/', '', $team->wa_number);
                                    $wa_link = str_starts_with($wa, '0') ? '62' . substr($wa, 1) : (str_starts_with($wa, '8') ? '62' . $wa : $wa);
                                @endphp
                                <a href="https://wa.me/{{ $wa_link }}" target="_blank" class="btn btn-sm btn-light-success text-success border border-success-subtle rounded-circle p-1 d-inline-flex align-items-center justify-content-center" style="width: 20px; height: 20px;" title="Chat WhatsApp">
                                    <i class="bi bi-whatsapp" style="font-size: 0.7rem;"></i>
                                </a>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: 0.75rem;">Tanggal Daftar:</span>
                            <span class="text-slate-700 fw-semibold text-end" style="font-size: 0.75rem;">
                                {{ $team->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <div class="mt-auto d-flex justify-content-between align-items-center pt-2.5 border-top border-light">
                        <span class="text-secondary small" style="font-size: 0.75rem;">Loyalitas:</span>
                        @if($team->loyalty_count > 1)
                            @php
                                $popover_content = '<ul class="list-unstyled mb-0 text-start" style="padding-left: 0; min-width: 200px;">';
                                foreach($team->history as $hist) {
                                    $badgeClass = $hist->status == 'PAID' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle';
                                    $popover_content .= '<li class="mb-2 pb-1 border-bottom border-light-subtle d-flex flex-column">';
                                    $popover_content .= '<span class="fw-bold text-dark" style="font-size: 0.8rem;">' . e($hist->season->name) . '</span>';
                                    $popover_content .= '<span class="text-muted" style="font-size: 0.7rem;">Tim: ' . e($hist->name) . '</span>';
                                    $popover_content .= '<span class="badge ' . $badgeClass . ' align-self-start mt-1" style="font-size: 0.55rem; padding: 2px 8px;">' . $hist->status . '</span>';
                                    $popover_content .= '</li>';
                                }
                                $popover_content .= '</ul>';
                            @endphp
                            <button type="button" class="btn btn-sm btn-outline-warning border-warning-subtle text-dark rounded-pill px-2.5 py-1 fw-bold d-inline-flex align-items-center gap-1 shadow-sm"
                                    data-bs-toggle="popover" data-bs-title="Riwayat Turnamen" data-bs-content="{{ $popover_content }}" data-bs-trigger="focus" style="font-size: 0.72rem;">
                                <i class="bi bi-star-fill text-warning"></i> LOYALTY ({{ $team->loyalty_count }})
                            </button>
                        @else
                            <span class="text-muted small" style="font-size: 0.75rem;">Baru Terdaftar</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted bg-white rounded-4 shadow-sm border border-light-subtle">
                <div class="py-4">
                    <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                    <p class="mb-0 fw-bold">Tidak ada data pendaftaran tim ditemukan.</p>
                    <small>Coba sesuaikan kata kunci pencarian atau ganti filter season/status.</small>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Pagination for grid view --}}
        @if($teams->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $teams->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

{{-- Styling Khusus --}}
<style>
    .card-stats {
        background: #ffffff;
        border: 1px solid rgba(241, 245, 249, 0.8) !important;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-stats:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.04), 0 8px 8px -5px rgba(0, 0, 0, 0.02);
        border-color: rgba(226, 232, 240, 0.8) !important;
    }
    .icon-shape {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .btn-light-success {
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.15);
        color: #10b981;
        transition: all 0.2s ease;
    }
    .btn-light-success:hover {
        background-color: #10b981;
        color: #fff !important;
    }
    /* Spacious Table styling */
    .table-spacious tbody tr {
        transition: background-color 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-spacious tbody tr:hover {
        background-color: #f8fafc !important;
    }
    .table-spacious td {
        padding-top: 16px !important;
        padding-bottom: 16px !important;
    }
    /* Grid Hover Lift-up */
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08), 0 8px 8px -5px rgba(0, 0, 0, 0.03) !important;
        border-color: #f59e0b !important; /* Soft Amber highlight border on hover */
    }
    /* View Switcher Active/Inactive States */
    .btn-view-active {
        background-color: #0f172a !important;
        color: #ffffff !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    }
    .btn-view-inactive {
        background-color: transparent !important;
        color: #64748b !important;
    }
    /* Popover overrides */
    .popover {
        border: 0 !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
        border-radius: 16px !important;
        overflow: hidden;
    }
    .popover-header {
        background: #f8fafc !important;
        border-bottom: 1px solid #f1f5f9 !important;
        font-weight: 700;
        color: #1e293b;
        font-size: 0.85rem;
    }
    .popover-body {
        padding: 16px !important;
    }
</style>

{{-- Script Switch View (Tabel vs Kartu) --}}
<script>
    function switchView(mode) {
        const tableView = document.getElementById('tableViewContainer');
        const gridView = document.getElementById('gridViewContainer');
        const btnTable = document.getElementById('btnViewTable');
        const btnGrid = document.getElementById('btnViewGrid');
        
        if (mode === 'grid') {
            tableView.classList.add('d-none');
            gridView.classList.remove('d-none');
            
            btnTable.classList.remove('btn-view-active');
            btnTable.classList.add('btn-view-inactive');
            
            btnGrid.classList.remove('btn-view-inactive');
            btnGrid.classList.add('btn-view-active');
            
            localStorage.setItem('ymd_teams_view_mode', 'grid');
        } else {
            tableView.classList.remove('d-none');
            gridView.classList.add('d-none');
            
            btnTable.classList.remove('btn-view-inactive');
            btnTable.classList.add('btn-view-active');
            
            btnGrid.classList.remove('btn-view-active');
            btnGrid.classList.add('btn-view-inactive');
            
            localStorage.setItem('ymd_teams_view_mode', 'table');
        }
    }
</script>

{{-- Inline Initialization Script to Prevent Flicker --}}
<script>
    (function() {
        const mode = localStorage.getItem('ymd_teams_view_mode') || 'table';
        const tableView = document.getElementById('tableViewContainer');
        const gridView = document.getElementById('gridViewContainer');
        const btnTable = document.getElementById('btnViewTable');
        const btnGrid = document.getElementById('btnViewGrid');
        
        if (mode === 'grid') {
            tableView.classList.add('d-none');
            gridView.classList.remove('d-none');
            btnTable.classList.add('btn-view-inactive');
            btnGrid.classList.add('btn-view-active');
        } else {
            tableView.classList.remove('d-none');
            gridView.classList.add('d-none');
            btnTable.classList.add('btn-view-active');
            btnGrid.classList.add('btn-view-inactive');
        }
    })();
</script>

{{-- Script Popover bootstrap & Search Highlight --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Inisialisasi popover Bootstrap
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                html: true,
                container: 'body'
            })
        });

        // Highlighting search terms
        const searchParams = new URLSearchParams(window.location.search);
        const searchQuery = searchParams.get('search');
        
        if (searchQuery && searchQuery.trim() !== '') {
            const term = searchQuery.trim().replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'); // escape regex
            const regex = new RegExp(`(${term})`, 'gi');
            
            const targets = document.querySelectorAll('.searchable-text');
            targets.forEach(element => {
                const originalText = element.textContent;
                if (originalText.toLowerCase().includes(searchQuery.toLowerCase())) {
                    const newHTML = originalText.replace(regex, '<mark class="bg-warning-subtle text-dark p-0 px-1 rounded" style="font-weight: 700;">$1</mark>');
                    element.innerHTML = newHTML;
                }
            });
        }
    });
</script>
@endsection
