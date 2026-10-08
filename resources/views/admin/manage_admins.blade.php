@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
<style>
    /* Metric stat cards */
    .metric-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 16px;
        padding: 1.15rem 1.25rem;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05);
        border-color: #cbd5e1;
    }
    .metric-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* Admin Table Styling */
    .admin-table-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.03);
    }
    .admin-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 14px 18px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .admin-table tbody td {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    .admin-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .admin-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .admin-table tbody tr.is-frozen {
        background-color: #fffbfb;
    }
    .admin-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Admin Card (Grid View) */
    .admin-grid-card {
        border-radius: 16px;
        border: 1px solid rgba(226, 232, 240, 0.85);
        background: #ffffff;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        position: relative;
    }
    .admin-grid-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.08);
        transform: translateY(-3px);
    }
    .admin-grid-card.is-frozen {
        background: linear-gradient(180deg, #ffffff 0%, #fff5f5 100%);
        border-color: #fecaca;
    }

    /* Avatar styling */
    .avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }
    .avatar-img-circle {
        border-radius: 14px;
        object-fit: cover;
    }
    .avatar-circle {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #f59e0b;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        letter-spacing: -0.5px;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .avatar-circle.online-ring, .avatar-img-circle.online-ring {
        border-color: #22c55e;
        box-shadow: 0 0 0 2.5px rgba(34, 197, 94, 0.25);
    }
    .avatar-circle.frozen-ring, .avatar-img-circle.frozen-ring {
        border-color: #ef4444;
        box-shadow: 0 0 0 2.5px rgba(239, 68, 68, 0.2);
    }

    /* Live pulse animation */
    .live-pulse-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #22c55e;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: livePulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes livePulse {
        to {
            box-shadow: 0 0 0 7px rgba(34, 197, 94, 0);
        }
    }

    /* Action buttons toolbar */
    .admin-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: #ffffff;
        color: #64748b;
        transition: all 0.15s ease;
        font-size: 0.82rem;
        cursor: pointer;
        padding: 0;
        text-decoration: none;
    }
    .admin-action-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }
    .admin-action-btn.btn-action-danger:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #dc2626;
    }
    .admin-action-btn.btn-action-freeze:hover {
        background: #f0f9ff;
        border-color: #bae6fd;
        color: #0284c7;
    }

    /* Filter pills */
    .filter-pill {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.8rem;
        transition: all 0.15s ease;
        padding: 0.4rem 0.9rem;
    }
    .filter-pill:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }
    .filter-pill.active {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }
    .filter-pill.active .count-badge {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
    }

    /* View Switcher */
    .view-switcher-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        font-size: 0.95rem;
        transition: all 0.15s ease;
    }
    .view-switcher-btn:hover {
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .view-switcher-btn.active {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
    }

    /* Progress bar */
    .perm-progress {
        height: 5px;
        border-radius: 999px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .perm-progress-bar {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #f59e0b, #d97706);
        transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .perm-progress-bar.is-full {
        background: linear-gradient(90deg, #22c55e, #16a34a);
    }

    /* Modal Permissions */
    .icon-square {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
    }
    .permission-card {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .permission-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        transform: translateY(-1px);
    }
    .form-check-input:checked {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
    }
    .hover-gold:hover {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
        color: #ffffff !important;
    }

    /* Preset buttons */
    .btn-preset {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
        font-size: 0.76rem;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .btn-preset:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }
    .btn-preset-danger {
        border: 1px solid #fee2e2;
        background: #fff5f5;
        color: #dc2626;
        font-size: 0.76rem;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .btn-preset-danger:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #991b1b;
    }
</style>

    {{-- Top Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">
                    <i class="bi bi-shield-lock-fill me-1"></i> ACCESS CONTROL &amp; SECURITY
                </span>
            </div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.65rem; letter-spacing: -0.5px;">
                Kelola Akun Admin
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.86rem;">
                Monitor status sesi realtime, pembagian hak akses 15 modul, bekukan akun, atau putus sesi login instan.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.manage.force-logout-all') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MELOGOUT SEMUA USER & PERANGKAT sekarang?\n\nSemua admin (termasuk Anda) yang sedang aktif akan langsung dikeluarkan dari sistem.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger fw-semibold px-3 py-2 rounded-pill shadow-sm d-inline-flex align-items-center gap-1.5" style="font-size: 0.84rem;">
                    <i class="bi bi-power"></i>
                    <span>Logout Semua Perangkat</span>
                </button>
            </form>
            <button class="btn btn-warning fw-bold px-3.5 py-2 rounded-pill shadow-sm text-dark hover-gold flex-shrink-0 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addAdminModal" style="font-size: 0.84rem;">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Staf Admin</span>
            </button>
        </div>
    </div>

    {{-- Validation and Feedback Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 py-3 small">
            @foreach ($errors->all() as $error)
                <div class="d-flex align-items-center mb-1"><i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center py-3">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div class="fw-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Permintaan Akses Google Menunggu Persetujuan --}}
    @if(isset($pendingAdmins) && count($pendingAdmins) > 0)
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="border-left: 4px solid #f59e0b !important; background: #fffbeb;">
            <div class="p-3 p-md-3.5">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2.5">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="rounded-circle bg-warning text-dark d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 1.1rem;">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <span>Permintaan Akses Google Menunggu Persetujuan</span>
                                <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ count($pendingAdmins) }} Menunggu</span>
                            </h6>
                            <p class="text-secondary small mb-0" style="font-size: 0.78rem;">
                                Akun berikut baru mencoba login via Google dan memerlukan verifikasi Superadmin.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row g-2.5">
                    @foreach($pendingAdmins as $pAdmin)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="bg-white p-2.5 rounded-3 border d-flex flex-column justify-content-between shadow-xs h-100">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    @if($pAdmin->avatar)
                                        <img src="{{ $pAdmin->avatar }}" alt="{{ $pAdmin->name }}" class="rounded-3 border" style="width: 38px; height: 38px; object-fit: cover;">
                                    @else
                                        <div class="rounded-3 bg-light text-dark fw-bold border d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 1rem;">
                                            {{ strtoupper(substr($pAdmin->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;" title="{{ $pAdmin->name }}">{{ $pAdmin->name }}</div>
                                        <div class="text-secondary text-truncate" style="font-size: 0.72rem;">
                                            <i class="bi bi-google text-danger me-1"></i>{{ $pAdmin->email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 pt-1.5 border-top">
                                    <form action="{{ route('admin.manage.approve', $pAdmin->id) }}" method="POST" class="flex-grow-1 m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success w-100 rounded-pill fw-bold py-1 shadow-none" style="font-size: 0.72rem;">
                                            <i class="bi bi-check-lg me-1"></i> Setujui
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.manage.delete', $pAdmin->id) }}" onclick="return confirm('Tolak dan hapus permohonan akun {{ $pAdmin->name }}?')" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-x-lg"></i> Tolak
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Executive Metric Stat Cards --}}
    @php
        $totalAdmins = count($admins);
        $onlineAdminsCount = $admins->filter(fn($a) => method_exists($a, 'isOnline') ? $a->isOnline() : false)->count();
        $frozenAdminsCount = $admins->filter(fn($a) => isset($a->is_active) && !$a->is_active)->count();
        $superAdminsCount = $admins->filter(function($a) {
            $p = is_array($a->permissions) ? $a->permissions : (json_decode($a->permissions, true) ?: []);
            return count($p) >= 15;
        })->count();
    @endphp
    <div class="row g-3 mb-4">
        {{-- Stat 1: Total Admins --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Admin</span>
                    <div class="metric-icon-box" style="background: #f1f5f9; color: #0f172a;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold text-dark mb-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">{{ $totalAdmins }}</h3>
                    <span class="text-secondary small">Akun</span>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.72rem;">
                    Terdaftar di sistem
                </div>
            </div>
        </div>

        {{-- Stat 2: Online Standby --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.5px;">Online Sekarang</span>
                    <div class="metric-icon-box" style="background: #f0fdf4; color: #16a34a;">
                        <i class="bi bi-broadcast"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="fw-bold text-dark mb-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">{{ $onlineAdminsCount }}</h3>
                    @if($onlineAdminsCount > 0)
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                            <span class="live-pulse-dot me-1"></span> Live
                        </span>
                    @else
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                            Idle
                        </span>
                    @endif
                </div>
                <div class="text-muted mt-1" style="font-size: 0.72rem;">
                    Aktif 5 menit terakhir
                </div>
            </div>
        </div>

        {{-- Stat 3: Frozen Admins --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100 {{ $frozenAdminsCount > 0 ? 'border-danger-subtle' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.5px;">Dibekukan</span>
                    <div class="metric-icon-box" style="background: {{ $frozenAdminsCount > 0 ? '#fef2f2' : '#f8fafc' }}; color: {{ $frozenAdminsCount > 0 ? '#dc2626' : '#64748b' }};">
                        <i class="bi bi-snow"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold {{ $frozenAdminsCount > 0 ? 'text-danger' : 'text-dark' }} mb-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">{{ $frozenAdminsCount }}</h3>
                    <span class="text-secondary small">Akun</span>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.72rem;">
                    {{ $frozenAdminsCount > 0 ? 'Akses disuspend' : 'Tidak ada suspend' }}
                </div>
            </div>
        </div>

        {{-- Stat 4: Super Admin / Access Level --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 0.74rem; text-transform: uppercase; letter-spacing: 0.5px;">Super Admin</span>
                    <div class="metric-icon-box" style="background: #fef3c7; color: #d97706;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold text-dark mb-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">{{ $superAdminsCount }}</h3>
                    <span class="text-secondary small">Akun</span>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.72rem;">
                    15/15 modul izin penuh
                </div>
            </div>
        </div>
    </div>

    {{-- Search, Filter, & View Mode Controls Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="row g-2.5 align-items-center justify-content-between">
                {{-- Search Box --}}
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 14px; transform: translateY(-50%); font-size: 0.85rem;"></i>
                        <input type="text" id="adminSearchInput" class="form-control rounded-pill ps-5 pe-4 py-2 border-light-subtle shadow-none" placeholder="Cari nama, username, email..." style="font-size: 0.82rem;">
                        <button type="button" id="clearSearchBtn" class="btn btn-sm position-absolute text-muted d-none border-0 p-0" style="top: 50%; right: 14px; transform: translateY(-50%);" title="Hapus pencarian">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                {{-- Filter Chips & View Mode Switcher --}}
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-md-end">
                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                            <button type="button" class="btn btn-sm filter-pill active rounded-pill" data-filter="all">
                                Semua <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1 count-badge">{{ $totalAdmins }}</span>
                            </button>
                            <button type="button" class="btn btn-sm filter-pill rounded-pill" data-filter="online">
                                <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.45rem;"></i>
                                Online <span class="badge bg-success-subtle text-success rounded-pill ms-1 count-badge">{{ $onlineAdminsCount }}</span>
                            </button>
                            @if($frozenAdminsCount > 0)
                            <button type="button" class="btn btn-sm filter-pill rounded-pill" data-filter="frozen">
                                <i class="bi bi-snow text-danger me-1"></i>
                                Dibekukan <span class="badge bg-danger-subtle text-danger rounded-pill ms-1 count-badge">{{ $frozenAdminsCount }}</span>
                            </button>
                            @endif
                            <button type="button" class="btn btn-sm filter-pill rounded-pill" data-filter="superadmin">
                                <i class="bi bi-award text-warning me-1"></i>
                                Super Admin <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill ms-1 count-badge">{{ $superAdminsCount }}</span>
                            </button>
                        </div>

                        {{-- View Toggle (Table / Grid) --}}
                        <div class="d-none d-sm-flex align-items-center border rounded-pill p-0.5 bg-light ms-md-2">
                            <button type="button" id="btnViewTable" class="btn btn-sm view-switcher-btn rounded-pill active" title="Tampilan Tabel">
                                <i class="bi bi-list-ul"></i>
                            </button>
                            <button type="button" id="btnViewGrid" class="btn btn-sm view-switcher-btn rounded-pill" title="Tampilan Kartu (Grid)">
                                <i class="bi bi-grid"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- VIEW 1: MODERN ADMIN TABLE VIEW (Default) --}}
    <div id="adminTableViewContainer" class="admin-table-card mb-4">
        <div class="table-responsive">
            <table class="table admin-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 260px;">Admin / Pengguna</th>
                        <th style="min-width: 140px;">Status Sesi</th>
                        <th style="min-width: 220px;">Hak Akses &amp; Otorisasi</th>
                        <th style="min-width: 170px;">Aktivitas Terakhir</th>
                        <th class="text-end" style="min-width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                    @forelse($admins as $index => $admin)
                        @php
                            $userPerms = $admin->permissions;
                            if (!is_array($userPerms)) {
                                $userPerms = json_decode($userPerms, true) ?: [];
                            }
                            $activeCount = count($userPerms);
                            $permPercent = round(($activeCount / 15) * 100);
                            $isOnline = method_exists($admin, 'isOnline') ? $admin->isOnline() : false;
                            $isActive = isset($admin->is_active) ? (bool)$admin->is_active : true;
                            $isCurrentUser = ($admin->id === Auth::id());
                            $isSuperAdmin = ($activeCount >= 15);
                        @endphp
                        <tr class="admin-item {{ !$isActive ? 'is-frozen' : '' }}"
                            data-name="{{ strtolower($admin->name) }}"
                            data-username="{{ strtolower($admin->username) }}"
                            data-email="{{ strtolower($admin->email) }}"
                            data-online="{{ $isOnline && $isActive ? '1' : '0' }}"
                            data-frozen="{{ !$isActive ? '1' : '0' }}"
                            data-superadmin="{{ $isSuperAdmin ? '1' : '0' }}">
                            
                            {{-- Col 1: Avatar & User Details --}}
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-wrapper">
                                        @if($admin->avatar)
                                            <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}" class="avatar-img-circle border {{ !$isActive ? 'frozen-ring opacity-75' : ($isOnline ? 'online-ring' : '') }}" style="width: 42px; height: 42px;">
                                        @else
                                            <div class="avatar-circle {{ !$isActive ? 'frozen-ring opacity-75' : ($isOnline ? 'online-ring' : '') }}" style="width: 42px; height: 42px; font-size: 1.05rem;">
                                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <span class="fw-bold text-dark text-truncate" style="font-size: 0.9rem;" title="{{ $admin->name }}">
                                                {{ $admin->name }}
                                            </span>
                                            @if($isCurrentUser)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.62rem;">
                                                    Anda
                                                </span>
                                            @endif
                                            @if($admin->google_id)
                                                <span class="badge bg-light text-dark border rounded-pill px-1.5 py-0.5" style="font-size: 0.6rem;" title="Terhubung Google: {{ $admin->email }}">
                                                    <i class="bi bi-google text-danger"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-secondary small d-flex align-items-center gap-2 mt-0.5" style="font-size: 0.76rem;">
                                            <span>{{ '@' . $admin->username }}</span>
                                            <span>&bull;</span>
                                            <span class="text-truncate">{{ $admin->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Col 2: Status Sesi --}}
                            <td>
                                @if(!$isActive)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-snow me-1"></i> Dibekukan
                                    </span>
                                @elseif($isOnline)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <span class="live-pulse-dot me-1.5"></span> Online (Live)
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-circle-fill me-1 text-muted opacity-50" style="font-size: 0.45rem;"></i> Offline
                                    </span>
                                @endif
                            </td>

                            {{-- Col 3: Hak Akses / Otorisasi --}}
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($isSuperAdmin)
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.74rem;">
                                            <i class="bi bi-award-fill me-1"></i> Super Admin (15/15)
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-semibold perm-count-badge" style="font-size: 0.74rem;">
                                            <i class="bi bi-shield-check text-warning me-1"></i> {{ $activeCount }}/15 Modul
                                        </span>
                                    @endif
                                    <button class="btn btn-sm btn-outline-warning border-0 p-1 text-dark hover-gold rounded-circle d-inline-flex align-items-center justify-content-center" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#permissionsModal{{ $admin->id }}" 
                                            title="Ubah Hak Akses Modul" 
                                            style="width: 26px; height: 26px; font-size: 0.8rem;">
                                        <i class="bi bi-sliders2-vertical"></i>
                                    </button>
                                </div>
                            </td>

                            {{-- Col 4: Aktivitas Terakhir --}}
                            <td>
                                <div class="text-dark fw-semibold small" style="font-size: 0.78rem;">
                                    <i class="bi bi-clock-history text-warning me-1"></i>
                                    {{ $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Belum pernah online' }}
                                </div>
                                <div class="text-secondary text-truncate small mt-0.5" style="max-width: 220px; font-size: 0.72rem;" title="{{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada aktivitas' }}">
                                    {{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada aktivitas' }}
                                </div>
                            </td>

                            {{-- Col 5: Aksi Toolbar --}}
                            <td class="text-end">
                                <div class="d-inline-flex gap-1 align-items-center">
                                    {{-- Atur Izin --}}
                                    <button type="button" class="admin-action-btn" title="Atur Izin Modul" data-bs-toggle="modal" data-bs-target="#permissionsModal{{ $admin->id }}">
                                        <i class="bi bi-sliders2-vertical text-warning"></i>
                                    </button>

                                    @if(!$isCurrentUser)
                                        {{-- Force Logout --}}
                                        <button type="button" class="admin-action-btn" title="Putus Sesi Login (Force Logout)" onclick="forceLogoutAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                                            <i class="bi bi-box-arrow-right text-secondary"></i>
                                        </button>

                                        {{-- Bekukan / Aktifkan --}}
                                        <button type="button" class="admin-action-btn {{ !$isActive ? 'btn-action-danger bg-danger-subtle text-danger' : 'btn-action-freeze' }}" 
                                                title="{{ $isActive ? 'Bekukan Akun Sementara' : 'Aktifkan Kembali Akun' }}" 
                                                onclick="toggleAdminStatus({{ $admin->id }}, '{{ addslashes($admin->name) }}', {{ $isActive ? 'true' : 'false' }})">
                                            <i class="bi {{ $isActive ? 'bi-snow text-info' : 'bi-sun-fill text-warning' }}"></i>
                                        </button>
                                    @endif

                                    {{-- Edit Data --}}
                                    <button type="button" class="admin-action-btn" title="Edit Akun" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Hapus (Bukan Diri Sendiri) --}}
                                    @if(!$isCurrentUser)
                                        <a href="{{ route('admin.manage.delete', $admin->id) }}" 
                                           class="admin-action-btn btn-action-danger" title="Hapus Akun Permanen"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $admin->username }}? Hapus akun tidak dapat dibatalkan.');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-secondary">
                                <i class="bi bi-people fs-2 text-muted mb-2 d-block"></i>
                                Belum ada staf admin yang ditambahkan ke sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- VIEW 2: REDESIGNED CARD GRID (Switchable) --}}
    <div id="adminGridViewContainer" class="row g-3 d-none mb-4">
        @forelse($admins as $index => $admin)
            @php
                $userPerms = $admin->permissions;
                if (!is_array($userPerms)) {
                    $userPerms = json_decode($userPerms, true) ?: [];
                }
                $activeCount = count($userPerms);
                $permPercent = round(($activeCount / 15) * 100);
                $isOnline = method_exists($admin, 'isOnline') ? $admin->isOnline() : false;
                $isActive = isset($admin->is_active) ? (bool)$admin->is_active : true;
                $isCurrentUser = ($admin->id === Auth::id());
                $isSuperAdmin = ($activeCount >= 15);
            @endphp
            <div class="col-12 col-md-6 col-xl-4 admin-item" 
                 data-name="{{ strtolower($admin->name) }}"
                 data-username="{{ strtolower($admin->username) }}"
                 data-email="{{ strtolower($admin->email) }}"
                 data-online="{{ $isOnline && $isActive ? '1' : '0' }}"
                 data-frozen="{{ !$isActive ? '1' : '0' }}"
                 data-superadmin="{{ $isSuperAdmin ? '1' : '0' }}">
                <div class="admin-grid-card h-100 p-3.5 p-md-4 {{ !$isActive ? 'is-frozen' : '' }}">
                    {{-- Card Header: Avatar, Name, and Quick Toolbar --}}
                    <div class="d-flex align-items-start justify-content-between mb-3 gap-2">
                        <div class="d-flex align-items-center gap-2.5 min-w-0">
                            <div class="avatar-wrapper">
                                @if($admin->avatar)
                                    <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}" class="avatar-img-circle border {{ !$isActive ? 'frozen-ring opacity-75' : ($isOnline ? 'online-ring' : '') }}" style="width: 46px; height: 46px;">
                                @else
                                    <div class="avatar-circle {{ !$isActive ? 'frozen-ring opacity-75' : ($isOnline ? 'online-ring' : '') }}" style="width: 46px; height: 46px; font-size: 1.15rem;">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.92rem;" title="{{ $admin->name }}">
                                        {{ $admin->name }}
                                    </h6>
                                    @if($isCurrentUser)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.62rem;">
                                            Anda
                                        </span>
                                    @endif
                                </div>
                                <div class="text-secondary small text-truncate" style="font-size: 0.75rem;">
                                    {{ '@' . $admin->username }}
                                </div>
                            </div>
                        </div>

                        {{-- Quick Actions Toolbar --}}
                        <div class="d-flex gap-1 align-items-center flex-shrink-0">
                            @if(!$isCurrentUser)
                                <button type="button" class="admin-action-btn" title="Force Logout" onclick="forceLogoutAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                                    <i class="bi bi-box-arrow-right text-secondary"></i>
                                </button>
                                <button type="button" class="admin-action-btn {{ !$isActive ? 'btn-action-danger bg-danger-subtle text-danger' : 'btn-action-freeze' }}" 
                                        title="{{ $isActive ? 'Bekukan Akun' : 'Aktifkan Akun' }}" 
                                        onclick="toggleAdminStatus({{ $admin->id }}, '{{ addslashes($admin->name) }}', {{ $isActive ? 'true' : 'false' }})">
                                    <i class="bi {{ $isActive ? 'bi-snow text-info' : 'bi-sun-fill text-warning' }}"></i>
                                </button>
                            @endif
                            <button type="button" class="admin-action-btn" title="Edit Akun" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            @if(!$isCurrentUser)
                                <a href="{{ route('admin.manage.delete', $admin->id) }}" 
                                   class="admin-action-btn btn-action-danger" title="Hapus Akun"
                                   onclick="return confirm('Hapus akun admin {{ $admin->username }}?');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Details Info: Email & Status --}}
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3 pt-2 border-top text-secondary small" style="font-size: 0.78rem;">
                        <span class="text-truncate"><i class="bi bi-envelope me-1 text-muted"></i>{{ $admin->email }}</span>
                        @if(!$isActive)
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                Dibekukan
                            </span>
                        @elseif($isOnline)
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                <span class="live-pulse-dot me-1"></span> Live
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                Offline
                            </span>
                        @endif
                    </div>

                    {{-- Activity Snippet --}}
                    <div class="bg-light rounded-3 p-2 mb-3 border border-light-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-0.5">
                            <span class="text-muted" style="font-size: 0.7rem;">Terakhir Aktif:</span>
                            <span class="text-dark fw-bold" style="font-size: 0.72rem;">{{ $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Belum pernah' }}</span>
                        </div>
                        <div class="text-secondary text-truncate" style="font-size: 0.7rem;" title="{{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada aktivitas' }}">
                            {{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada catatan aktivitas' }}
                        </div>
                    </div>

                    {{-- Otorisasi Progress & Trigger --}}
                    <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                        <div>
                            <div class="d-flex align-items-center gap-1.5 mb-1">
                                <span class="text-secondary fw-semibold" style="font-size: 0.72rem; text-transform: uppercase;">Otorisasi:</span>
                                <span class="perm-count fw-bold" style="font-size: 0.75rem; color: {{ $isSuperAdmin ? '#16a34a' : ($activeCount >= 8 ? '#d97706' : '#64748b') }};">
                                    {{ $activeCount }}/15 Modul
                                </span>
                            </div>
                            <div class="perm-progress" style="width: 110px;">
                                <div class="perm-progress-bar {{ $isSuperAdmin ? 'is-full' : '' }}" style="width: {{ $permPercent }}%;"></div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3 py-1 text-dark d-inline-flex align-items-center gap-1" 
                                data-bs-toggle="modal" 
                                data-bs-target="#permissionsModal{{ $admin->id }}"
                                style="font-size: 0.74rem;">
                            <i class="bi bi-sliders2-vertical"></i>
                            <span>Atur Izin</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white py-5 text-center text-secondary">
                    <i class="bi bi-people fs-2 text-muted mb-2 d-block"></i>
                    Belum ada staf admin yang ditambahkan ke sistem.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Empty Search Results Feedback --}}
    <div id="noAdminResults" class="card border-0 shadow-sm rounded-4 bg-white my-4 d-none">
        <div class="card-body py-5 text-center text-secondary">
            <i class="bi bi-search fs-2 text-muted mb-3 d-block opacity-50"></i>
            <h5 class="fw-bold text-dark mb-1">Tidak Ada Admin Ditemukan</h5>
            <p class="text-secondary small mb-3">Tidak ada akun admin yang cocok dengan kata kunci atau filter yang dipilih.</p>
            <button type="button" id="resetFiltersBtn" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-semibold">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Pencarian &amp; Filter
            </button>
        </div>
    </div>
</div>

{{-- ALL MODALS (Permissions Modal + Edit Admin Modal) --}}
@foreach($admins as $admin)
    @php
        $userPerms = $admin->permissions;
        if (!is_array($userPerms)) {
            $userPerms = json_decode($userPerms, true) ?: [];
        }
        $activeCount = count($userPerms);
    @endphp

    {{-- 1. Permissions Management Modal --}}
    <div class="modal fade" id="permissionsModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                {{-- Modal Header with Dark Gradient --}}
                <div class="modal-header border-0 text-white px-4 py-3 py-md-3.5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3" style="width: 46px; height: 46px; font-size: 1.15rem; background: rgba(255, 255, 255, 0.12); border-color: rgba(255, 255, 255, 0.2);">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div class="text-start">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-white mb-0" style="font-size: 1.1rem;">{{ $admin->name }}</h5>
                                <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-0.5 modal-perm-badge" style="font-size: 0.68rem;">
                                    {{ $activeCount }}/15 Modul Aktif
                                </span>
                            </div>
                            <p class="text-white-50 mb-0 small" style="font-size: 0.78rem;">{{ '@' . $admin->username }} &bull; {{ $admin->email }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-3 p-md-4 bg-light" style="max-height: 70vh; overflow-y: auto;">
                    {{-- Quick Role Preset Bar --}}
                    <div class="bg-white rounded-3 border border-light-subtle p-3 mb-3 shadow-xs">
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="bi bi-lightning-charge-fill text-warning"></i>
                                <span class="fw-bold text-dark" style="font-size: 0.8rem;">Preset Peran Cepat:</span>
                                <span class="text-muted" style="font-size: 0.72rem;">Terapkan paket otorisasi modul dalam satu klik</span>
                            </div>
                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                Auto-Sync Instan
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1" 
                                    onclick="applyPreset({{ $admin->id }}, 'wasit', 'Wasit Turnamen')">
                                <i class="bi bi-trophy text-warning me-1"></i> Wasit Turnamen
                            </button>
                            <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1" 
                                    onclick="applyPreset({{ $admin->id }}, 'bendahara', 'Bendahara / Keuangan')">
                                <i class="bi bi-cash-stack text-success me-1"></i> Bendahara
                            </button>
                            <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1" 
                                    onclick="applyPreset({{ $admin->id }}, 'cs', 'Customer Care / LO')">
                                <i class="bi bi-headset text-info me-1"></i> Customer Care
                            </button>
                            <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1" 
                                    onclick="applyPreset({{ $admin->id }}, 'super', 'Super Admin')">
                                <i class="bi bi-award-fill text-primary me-1"></i> Pilih Semua (Super Admin)
                            </button>
                            <button type="button" class="btn btn-sm btn-preset-danger rounded-pill px-3 py-1" 
                                    onclick="applyPreset({{ $admin->id }}, 'none', 'Kosongkan')">
                                <i class="bi bi-x-circle text-danger me-1"></i> Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-1.5 text-secondary small mb-3" style="font-size: 0.76rem;">
                        <i class="bi bi-info-circle text-primary"></i> 
                        <span>Setiap switch yang diubah langsung disimpan realtime ke server.</span>
                    </div>

                    {{-- 15 Permissions Grid --}}
                    <div class="row g-2.5">
                        @php
                            $availablePerms = [
                                'dashboard' => [
                                    'label' => 'Dashboard Utama',
                                    'desc' => 'Mengakses beranda dashboard admin, metrik ringkasan sistem, grafik pendaftaran cepat, dan rangkuman data kas.',
                                    'icon' => 'bi-grid-1x2',
                                    'color' => '#0f172a',
                                    'bg' => '#e2e8f0'
                                ],
                                'seasons' => [
                                    'label' => 'Daftar Season', 
                                    'desc' => 'Mengakses menu season, membuat season baru, mengubah detail turnamen, dan verifikasi tim pendaftar.',
                                    'icon' => 'bi-trophy', 
                                    'color' => '#d97706',
                                    'bg' => '#fef3c7'
                                ],
                                'teams' => [
                                    'label' => 'Daftar Team (Global)',
                                    'desc' => 'Melihat list global seluruh tim, memfilter status pembayaran, dan melacak roster pemain.',
                                    'icon' => 'bi-people-fill',
                                    'color' => '#06b6d4',
                                    'bg' => '#ecfeff'
                                ],
                                'payments' => [
                                    'label' => 'Riwayat Pembayaran (Gateway & QRIS)',
                                    'desc' => 'Mengakses mutasi log transaksi gateway, sinkronisasi status, antrean klaim QRIS manual & approval bukti transfer.',
                                    'icon' => 'bi-cash-stack',
                                    'color' => '#10b981',
                                    'bg' => '#d1fae5'
                                ],
                                'notes' => [
                                    'label' => 'Catatan Staf', 
                                    'desc' => 'Melihat, membuat, mengubah, dan menghapus catatan koordinasi internal antar administrator.',
                                    'icon' => 'bi-sticky', 
                                    'color' => '#8b5cf6',
                                    'bg' => '#ede9fe'
                                ],
                                'settings' => [
                                    'label' => 'Pengaturan Sistem (Super)',
                                    'desc' => 'Mengonfigurasi token API WhatsApp Fonnte, kredensial Tripay/iPaymu, template notifikasi pesan, dan setting global.',
                                    'icon' => 'bi-gear',
                                    'color' => '#ea580c',
                                    'bg' => '#ffedd5'
                                ],
                                'gateway_notifications' => [
                                    'label' => 'Notifikasi Gateway',
                                    'desc' => 'Mengakses log rekam jejak notifikasi Webhook/Callback dari payment gateway secara realtime.',
                                    'icon' => 'bi-bell',
                                    'color' => '#f59e0b',
                                    'bg' => '#fef3c7'
                                ],
                                'faqs' => [
                                    'label' => 'Kelola FAQ', 
                                    'desc' => 'Menambah, mengubah susunan urutan, dan menghapus daftar tanya jawab publik di halaman utama portal.',
                                    'icon' => 'bi-question-circle', 
                                    'color' => '#6b7280',
                                    'bg' => '#f3f4f6'
                                ],
                                'activity_log' => [
                                    'label' => 'Log Aktivitas Staf', 
                                    'desc' => 'Melihat audit log rekaman jejak tindakan administratif seluruh administrator platform.',
                                    'icon' => 'bi-clock-history', 
                                    'color' => '#ec4899',
                                    'bg' => '#fce7f3'
                                ],
                                'manage' => [
                                    'label' => 'Kelola Admin (Super)',
                                    'desc' => 'Menambah, mengedit, membekukan akun admin, serta mengatur pembagian hak akses perizinan modul.',
                                    'icon' => 'bi-person-gear',
                                    'color' => '#3b82f6',
                                    'bg' => '#dbeafe'
                                ],
                                'laravel_logs' => [
                                    'label' => 'Log Laravel',
                                    'desc' => 'Mengakses log error aplikasi Laravel (laravel.log) secara realtime untuk kebutuhan diagnostik sistem.',
                                    'icon' => 'bi-file-earmark-text',
                                    'color' => '#64748b',
                                    'bg' => '#f1f5f9'
                                ],
                                'storage' => [
                                    'label' => 'Kelola Penyimpanan File',
                                    'desc' => 'Memantau kapasitas disk space server, membersihkan file sampah massal, serta arsip bukti transfer lama.',
                                    'icon' => 'bi-hdd-network',
                                    'color' => '#14b8a6',
                                    'bg' => '#ccfbf1'
                                ],
                                'backup' => [
                                    'label' => 'Backup Database (Super)',
                                    'desc' => 'Mengunduh salinan cadangan struktur dan data SQL database platform langsung ke storage komputer.',
                                    'icon' => 'bi-database-down',
                                    'color' => '#ef4444',
                                    'bg' => '#fee2e2'
                                ],
                                'finance' => [
                                    'label' => 'Keuangan Ledger (Modul Season)', 
                                    'desc' => 'Mengakses data finansial season, mencatat pemasukan/pengeluaran kas, serta rekapitulasi cashflow laba/rugi.',
                                    'icon' => 'bi-currency-exchange', 
                                    'color' => '#22c55e',
                                    'bg' => '#f0fdf4'
                                ],
                                'solo_matchmaker' => [
                                    'label' => 'Solo Matchmaker (Modul Season)', 
                                    'desc' => 'Menggunakan modul matchmaking otomatis untuk menyusun peserta solo ke dalam komposisi tim yang seimbang.',
                                    'icon' => 'bi-people', 
                                    'color' => '#6366f1',
                                    'bg' => '#e0e7ff'
                                ],
                            ];
                        @endphp

                        @foreach($availablePerms as $key => $pInfo)
                            <div class="col-md-6">
                                <div class="permission-card rounded-3 p-2.5 h-100 d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-start me-2.5">
                                        <div class="icon-square me-2.5" style="background-color: {{ $pInfo['bg'] }}; color: {{ $pInfo['color'] }};">
                                            <i class="bi {{ $pInfo['icon'] }}"></i>
                                        </div>
                                        <div class="text-start">
                                            <h6 class="fw-bold text-dark mb-0.5" style="font-size: 0.84rem;">{{ $pInfo['label'] }}</h6>
                                            <p class="text-secondary mb-0" style="font-size: 0.72rem; line-height: 1.35;">{{ $pInfo['desc'] }}</p>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch p-0 m-0 pt-1">
                                        <input class="form-check-input permission-switch shadow-none" 
                                               type="checkbox" 
                                               role="switch" 
                                               id="perm_modal_{{ $admin->id }}_{{ $key }}"
                                               data-admin-id="{{ $admin->id }}"
                                               data-permission="{{ $key }}"
                                               {{ in_array($key, $userPerms) ? 'checked' : '' }}
                                               style="width: 2.2em; height: 1.1em; cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer border-0 bg-white px-4 py-2.5 justify-content-between">
                    <span class="text-muted small" style="font-size: 0.75rem;">
                        <i class="bi bi-shield-lock me-1 text-warning"></i> Simpan otomatis setiap perubahan switch
                    </span>
                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-4 fw-semibold shadow-sm text-white" data-bs-dismiss="modal">
                        Selesai &amp; Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Edit Admin Modal --}}
    <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <form action="{{ route('admin.manage.update', $admin->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom border-light px-4 py-3">
                        <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-pencil-square text-warning me-1.5"></i> Edit Akun Admin</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                   value="{{ $admin->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Username</label>
                            <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                   value="{{ $admin->username }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Email</label>
                            <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                   value="{{ $admin->email }}" required>
                        </div>
                        <div class="mb-0">
                            <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Password Baru (Kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                   placeholder="Minimal 6 karakter...">
                        </div>
                    </div>
                    <div class="modal-footer border-top border-light px-4 py-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark hover-gold btn-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

{{-- Global Add Admin Modal --}}
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <form action="{{ route('admin.manage.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark fs-6"><i class="bi bi-person-plus-fill text-warning me-1.5"></i> Tambah Akun Admin Baru</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Nama lengkap admin..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Username</label>
                        <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Username untuk login..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Alamat email aktif..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Minimal 6 karakter..." required>
                    </div>
                </div>
                <div class="modal-footer border-top border-light px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark hover-gold btn-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast Notification Container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1300;">
    <div id="permissionToast" class="toast align-items-center border-0 text-white rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center py-2.5 px-3">
                <i class="bi me-2 fs-5" id="toastIcon"></i>
                <span id="toastMessage" class="fw-semibold" style="font-size: 0.84rem;"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-3 m-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Live switch permission listener
    document.querySelectorAll('.permission-switch').forEach(switchEl => {
        switchEl.addEventListener('change', function() {
            const adminId = this.dataset.adminId;
            const permission = this.dataset.permission;
            const status = this.checked ? 1 : 0;
            
            this.disabled = true;
            
            fetch("{{ route('admin.manage.toggle-permission') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    admin_id: adminId,
                    permission: permission,
                    status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    showToast('Berhasil', data.message || 'Izin berhasil diperbarui', 'success');
                    updateCardPermissionsVisual(adminId, data.permissions);
                } else {
                    this.checked = !this.checked;
                    showToast('Gagal', data.message || 'Gagal memperbarui izin', 'danger');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.checked = !this.checked;
                showToast('Gagal', 'Terjadi kesalahan jaringan', 'danger');
            });
        });
    });

    // 2. Real-time Search and Filter Tabs Logic
    const searchInput = document.getElementById('adminSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const filterPills = document.querySelectorAll('.filter-pill');
    const adminItems = document.querySelectorAll('.admin-item');
    const noResults = document.getElementById('noAdminResults');
    const resetFiltersBtn = document.getElementById('resetFiltersBtn');

    let currentFilter = 'all';

    function applyFilters() {
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        let visibleCount = 0;

        if (clearSearchBtn) {
            if (query.length > 0) {
                clearSearchBtn.classList.remove('d-none');
            } else {
                clearSearchBtn.classList.add('d-none');
            }
        }

        adminItems.forEach(item => {
            const name = item.dataset.name || '';
            const username = item.dataset.username || '';
            const email = item.dataset.email || '';
            const isOnline = item.dataset.online === '1';
            const isFrozen = item.dataset.frozen === '1';
            const isSuper = item.dataset.superadmin === '1';

            // Match query
            const matchesQuery = (query === '') || 
                                 name.includes(query) || 
                                 username.includes(query) || 
                                 email.includes(query);

            // Match tab filter
            let matchesFilter = true;
            if (currentFilter === 'online') {
                matchesFilter = isOnline;
            } else if (currentFilter === 'frozen') {
                matchesFilter = isFrozen;
            } else if (currentFilter === 'superadmin') {
                matchesFilter = isSuper;
            }

            if (matchesQuery && matchesFilter) {
                item.classList.remove('d-none');
                visibleCount++;
            } else {
                item.classList.add('d-none');
            }
        });

        if (noResults) {
            if (visibleCount === 0 && adminItems.length > 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
                applyFilters();
            }
        });
    }

    filterPills.forEach(pill => {
        pill.addEventListener('click', function() {
            filterPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter || 'all';
            applyFilters();
        });
    });

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            currentFilter = 'all';
            filterPills.forEach(p => {
                if (p.dataset.filter === 'all') p.classList.add('active');
                else p.classList.remove('active');
            });
            applyFilters();
        });
    }

    // 3. View Switcher Toggle (Table vs Grid)
    const btnViewTable = document.getElementById('btnViewTable');
    const btnViewGrid = document.getElementById('btnViewGrid');
    const tableViewContainer = document.getElementById('adminTableViewContainer');
    const gridViewContainer = document.getElementById('adminGridViewContainer');

    function setViewMode(mode) {
        if (mode === 'grid') {
            if (tableViewContainer) tableViewContainer.classList.add('d-none');
            if (gridViewContainer) gridViewContainer.classList.remove('d-none');
            if (btnViewTable) btnViewTable.classList.remove('active');
            if (btnViewGrid) btnViewGrid.classList.add('active');
            localStorage.setItem('admin_manage_view_mode', 'grid');
        } else {
            if (tableViewContainer) tableViewContainer.classList.remove('d-none');
            if (gridViewContainer) gridViewContainer.classList.add('d-none');
            if (btnViewTable) btnViewTable.classList.add('active');
            if (btnViewGrid) btnViewGrid.classList.remove('active');
            localStorage.setItem('admin_manage_view_mode', 'table');
        }
    }

    const savedViewMode = localStorage.getItem('admin_manage_view_mode') || 'table';
    setViewMode(savedViewMode);

    if (btnViewTable) {
        btnViewTable.addEventListener('click', function() {
            setViewMode('table');
        });
    }
    if (btnViewGrid) {
        btnViewGrid.addEventListener('click', function() {
            setViewMode('grid');
        });
    }
});

// Helper to update card & table visual after permission changes
function updateCardPermissionsVisual(adminId, permissions) {
    const modalTriggers = document.querySelectorAll(`[data-bs-target="#permissionsModal${adminId}"]`);
    if (!permissions) return;

    const count = permissions.length;
    const percent = Math.round((count / 15) * 100);

    modalTriggers.forEach(modalTrigger => {
        const adminItem = modalTrigger.closest('.admin-item');
        if (!adminItem) return;

        // Update progress bar
        const barEl = adminItem.querySelector('.perm-progress-bar');
        if (barEl) {
            barEl.style.width = `${percent}%`;
            if (count >= 15) {
                barEl.classList.add('is-full');
            } else {
                barEl.classList.remove('is-full');
            }
        }

        // Update count label / badge
        const countEl = adminItem.querySelector('.perm-count');
        if (countEl) {
            countEl.textContent = `${count}/15 Modul`;
            countEl.style.color = count >= 15 ? '#16a34a' : (count >= 8 ? '#d97706' : '#64748b');
        }

        const countBadge = adminItem.querySelector('.perm-count-badge');
        if (countBadge) {
            countBadge.innerHTML = `<i class="bi bi-shield-check text-warning me-1"></i> ${count}/15 Modul`;
        }

        // Update data-superadmin attribute
        adminItem.dataset.superadmin = count >= 15 ? '1' : '0';
    });

    // Update modal badge
    const modal = document.getElementById(`permissionsModal${adminId}`);
    if (modal) {
        const modalBadge = modal.querySelector('.modal-perm-badge');
        if (modalBadge) {
            modalBadge.textContent = `${count}/15 Modul Aktif`;
        }
    }
}

// Preset definition mappings
const ROLE_PRESETS = {
    wasit: ['dashboard', 'seasons', 'teams', 'activity_log', 'notes'],
    bendahara: ['dashboard', 'seasons', 'payments', 'gateway_notifications', 'finance', 'notes'],
    cs: ['dashboard', 'seasons', 'teams', 'faqs', 'notes'],
    super: [
        'dashboard', 'seasons', 'teams', 'payments', 'notes', 
        'settings', 'gateway_notifications', 'faqs', 'activity_log', 
        'manage', 'laravel_logs', 'storage', 'backup', 'finance', 'solo_matchmaker'
    ],
    none: []
};

// Apply preset to admin permissions via AJAX sync
function applyPreset(adminId, presetKey, presetName) {
    const targetPerms = ROLE_PRESETS[presetKey] !== undefined ? ROLE_PRESETS[presetKey] : [];
    const modalEl = document.getElementById(`permissionsModal${adminId}`);
    if (!modalEl) return;

    const switches = modalEl.querySelectorAll('.permission-switch');
    switches.forEach(sw => sw.disabled = true);

    fetch("{{ route('admin.manage.sync-permissions') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            admin_id: adminId,
            permissions: targetPerms,
            preset_name: presetName
        })
    })
    .then(res => res.json())
    .then(data => {
        switches.forEach(sw => sw.disabled = false);
        if (data.success) {
            // Update switch states visually
            switches.forEach(sw => {
                const perm = sw.dataset.permission;
                sw.checked = data.permissions.includes(perm);
            });

            updateCardPermissionsVisual(adminId, data.permissions);
            showToast('Preset Diterapkan', data.message || `Izin berhasil diperbarui ke preset ${presetName}`, 'success');
        } else {
            showToast('Gagal', data.message || 'Gagal menerapkan preset', 'danger');
        }
    })
    .catch(err => {
        switches.forEach(sw => sw.disabled = false);
        showToast('Gagal', 'Terjadi kesalahan jaringan saat menerapkan preset', 'danger');
    });
}

function showToast(title, message, type) {
    const toastEl = document.getElementById('permissionToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-dark');
    toastIcon.classList.remove('bi-check-circle-fill', 'bi-x-circle-fill');
    
    if (type === 'success') {
        toastEl.classList.add('bg-success');
        toastIcon.classList.add('bi-check-circle-fill');
    } else {
        toastEl.classList.add('bg-danger');
        toastIcon.classList.add('bi-x-circle-fill');
    }
    
    toastMessage.textContent = message;
    
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
}

function toggleAdminStatus(adminId, adminName, isCurrentlyActive) {
    const actionText = isCurrentlyActive ? 'Bekukan' : 'Aktifkan';
    const alertTitle = isCurrentlyActive ? `Bekukan Akun ${adminName}?` : `Aktifkan Kembali Akun ${adminName}?`;
    const alertDesc = isCurrentlyActive 
        ? `Akun ${adminName} akan dinonaktifkan sementara dan sesinya langsung diputus saat ini juga.` 
        : `Akun ${adminName} akan diizinkan kembali untuk login dan mengelola sistem turnamen.`;
    const btnColor = isCurrentlyActive ? '#dc3545' : '#16a34a';

    Swal.fire({
        title: alertTitle,
        text: alertDesc,
        icon: isCurrentlyActive ? 'warning' : 'question',
        showCancelButton: true,
        confirmButtonColor: btnColor,
        cancelButtonColor: '#64748b',
        confirmButtonText: `Ya, ${actionText}!`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            fetch(`/admin/manage-admins/toggle-status/${adminId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghubungi server'
                });
            });
        }
    });
}

function forceLogoutAdmin(adminId, adminName) {
    Swal.fire({
        title: `Putus Sesi ${adminName}?`,
        text: `Sesi login ${adminName} akan dihentikan seketika di seluruh perangkat (komputer/HP). Admin tersebut harus login kembali jika ingin masuk.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Putus Sesi!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.showLoading();
            fetch(`/admin/manage-admins/force-logout/${adminId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sesi Diputus!',
                        text: data.message,
                        timer: 1800,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal menghubungi server'
                });
            });
        }
    });
}
</script>
@endsection
