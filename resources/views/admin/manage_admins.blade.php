@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
<style>
    /* ===================================================
       KELOLA AKUN ADMIN - UNIFIED SYSTEM STYLING
    =================================================== */

    /* Stat Cards */
    .admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .admin-stat-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .admin-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
    }

    .stat-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .stat-icon-wrapper.gray {
        background: #f1f5f9;
        color: #475569;
    }

    .stat-icon-wrapper.green {
        background: #ecfdf5;
        color: #10b981;
    }

    .stat-icon-wrapper.blue {
        background: #eff6ff;
        color: #3b82f6;
    }

    .stat-icon-wrapper.orange {
        background: #fffbeb;
        color: #f59e0b;
    }

    .stat-details strong {
        display: flex;
        align-items: center;
        font-size: 1.45rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 4px;
        color: #0f172a;
        letter-spacing: -0.5px;
    }

    .stat-details span {
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
    }

    .live-status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 8px;
        margin-left: 8px;
        border-radius: 999px;
        background: #ecfdf5;
        color: #10b981 !important;
        font-size: 0.68rem !important;
        font-weight: 700 !important;
        border: 1px solid #d1fae5;
    }

    .live-dot-pulse {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: livePulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
    }

    @keyframes livePulse {
        to {
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
        }
    }

    /* Main Table Card */
    .admin-main-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    /* Toolbar Header */
    .admin-toolbar {
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid #edf2f7;
        background: #ffffff;
    }

    .admin-search-box {
        width: 340px;
        position: relative;
    }

    .admin-search-box input {
        width: 100%;
        height: 38px;
        padding: 0 36px 0 38px;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #f8fafc;
        color: #1e293b;
        font-size: 0.82rem;
        outline: none;
        transition: all 0.2s ease;
    }

    .admin-search-box input:focus {
        border-color: #f59e0b;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12);
    }

    .admin-search-box i.search-icon {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.88rem;
    }

    .admin-search-box button.clear-search {
        position: absolute;
        top: 50%;
        right: 12px;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #94a3b8;
        padding: 0;
        font-size: 0.85rem;
        cursor: pointer;
    }

    /* Filter Chips (Unified with Settings/Logs tabs) */
    .admin-filter-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .admin-filter-chip {
        height: 34px;
        padding: 0 14px;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #ffffff;
        color: #64748b;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .admin-filter-chip:hover {
        background: #f8fafc;
        color: #1e293b;
        border-color: #cbd5e1;
    }

    .admin-filter-chip.active {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }

    .chip-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 0.68rem;
        font-weight: 700;
    }

    .admin-filter-chip.active .chip-count {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }

    /* Table Styling matching log-table */
    .admin-table-container {
        width: 100%;
        overflow-x: auto;
    }

    .admin-data-table {
        width: 100%;
        min-width: 960px;
        border-collapse: collapse;
    }

    .admin-data-table th {
        font-size: 0.72rem;
        letter-spacing: 0.8px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        padding: 14px 20px;
        text-align: left;
    }

    .admin-data-table td {
        padding: 14px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        vertical-align: middle;
        color: #1e293b;
    }

    .admin-data-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .admin-data-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .admin-data-table tbody tr.is-frozen {
        background-color: #fff9f9;
    }

    .admin-data-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* User Profile in Table */
    .admin-profile-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .admin-avatar-box {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #0f172a;
        color: #f59e0b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        font-weight: 800;
        flex-shrink: 0;
        object-fit: cover;
        border: 1.5px solid transparent;
    }

    .admin-avatar-box.super {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-color: #fde68a;
    }

    .admin-avatar-box.online {
        border-color: #10b981;
    }

    .admin-avatar-box.frozen {
        border-color: #ef4444;
        opacity: 0.7;
    }

    .admin-name-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .admin-subtext {
        color: #64748b;
        font-size: 0.74rem;
    }

    /* Status Badges */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .status-pill.offline {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }

    .status-pill.online {
        background: #ecfdf5;
        color: #10b981;
        border: 1px solid #d1fae5;
    }

    .status-pill.online::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
    }

    .status-pill.frozen {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fee2e2;
    }

    /* Module Access Progress */
    .access-display-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .access-meter {
        width: 76px;
        height: 6px;
        overflow: hidden;
        border-radius: 999px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }

    .access-meter span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .access-count-label {
        color: #334155;
        font-size: 0.76rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .superadmin-label-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 8px;
        background: #fffbeb;
        color: #b45309;
        font-size: 0.74rem;
        font-weight: 700;
        white-space: nowrap;
        border: 1px solid #fef3c7;
    }

    /* Activity Column */
    .activity-cell strong {
        display: block;
        margin-bottom: 2px;
        color: #0f172a;
        font-size: 0.78rem;
    }

    .activity-cell span {
        color: #64748b;
        font-size: 0.72rem;
        display: block;
        max-width: 240px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Action Buttons */
    .admin-action-tools {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }

    .btn-manage-perms {
        height: 32px;
        padding: 0 14px;
        border: 1px solid #fde68a;
        border-radius: 999px;
        background: #fffbeb;
        color: #92400e;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-manage-perms:hover {
        background: #fef3c7;
        border-color: #fcd34d;
        color: #78350f;
    }

    .btn-table-dots {
        width: 32px;
        height: 32px;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        background: #ffffff;
        color: #64748b;
        cursor: pointer;
        font-size: 0.95rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s ease;
        padding: 0;
    }

    .btn-table-dots:hover {
        background: #f8fafc;
        color: #0f172a;
        border-color: #cbd5e1;
    }

    .dropdown-menu-unified {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
        padding: 6px;
        font-size: 0.8rem;
        min-width: 195px;
    }

    .dropdown-menu-unified .dropdown-item {
        padding: 8px 12px;
        border-radius: 8px;
        font-weight: 500;
        color: #334155;
        transition: all 0.12s ease;
    }

    .dropdown-menu-unified .dropdown-item:hover {
        background-color: #f8fafc;
        color: #0f172a;
    }

    .dropdown-menu-unified .dropdown-item.text-danger:hover {
        background-color: #fef2f2;
        color: #dc2626 !important;
    }

    /* Modal Permissions Styling */
    .btn-preset {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 999px;
        transition: all 0.15s ease;
    }
    .btn-preset:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    }
    .btn-preset-danger {
        border: 1px solid #fee2e2;
        background: #fff5f5;
        color: #dc2626;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 999px;
        transition: all 0.15s ease;
    }
    .btn-preset-danger:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #991b1b;
    }
    .permission-card {
        transition: all 0.15s ease;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        border-radius: 12px;
    }
    .permission-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }
    .form-check-input:checked {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
    }
    .icon-square {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    /* Responsiveness */
    @media (max-width: 1100px) {
        .admin-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767.98px) {
        .admin-stats-grid {
            grid-template-columns: 1fr;
        }
        .admin-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .admin-search-box {
            width: 100%;
        }
        .admin-filter-group {
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .admin-filter-chip {
            white-space: nowrap;
        }
    }
</style>

    {{-- Header (Standardized with Log Aktivitas & other admin pages) --}}
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-7 mb-3 mb-md-0">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                Kelola Akun Admin
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                Kelola otorisasi modul, status sesi aktif, dan hak akses staf administrator turnamen.
            </p>
        </div>
        <div class="col-12 col-md-5 text-md-end d-flex align-items-center justify-content-md-end gap-2 flex-wrap">
            <form action="{{ route('admin.manage.force-logout-all') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('PERINGATAN: Logout semua user & perangkat sekarang?\n\nSemua admin (termasuk Anda) akan langsung dikeluarkan.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger fw-semibold px-3 py-2 rounded-pill d-inline-flex align-items-center gap-1.5 shadow-none" style="font-size: 0.82rem; border-width: 1.5px;">
                    <i class="bi bi-power"></i> <span>Logout Semua Sesi</span>
                </button>
            </form>

            <button type="button" class="btn btn-warning fw-bold px-3.5 py-2 rounded-pill shadow-sm text-dark d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#addAdminModal" style="font-size: 0.82rem;">
                <i class="bi bi-person-plus-fill"></i> <span>Tambah Staf Admin</span>
            </button>
        </div>
    </div>

    {{-- System Alerts --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3 py-2.5 small">
            @foreach ($errors->all() as $error)
                <div class="d-flex align-items-center mb-0.5"><i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-3 d-flex align-items-center py-2.5 small">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div class="fw-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Pending Google Access Notification (if any) --}}
    @if(isset($pendingAdmins) && count($pendingAdmins) > 0)
        <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden" style="border-left: 4px solid #f59e0b !important; background: #fffbeb;">
            <div class="p-3">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-person-check-fill text-warning fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 0.86rem;">Permintaan Akses Google Menunggu Persetujuan</span>
                        <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">{{ count($pendingAdmins) }} Menunggu</span>
                    </div>
                </div>
                <div class="row g-2">
                    @foreach($pendingAdmins as $pAdmin)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="bg-white p-2.5 rounded-3 border d-flex align-items-center justify-content-between gap-2 shadow-xs">
                                <div class="d-flex align-items-center gap-2 min-w-0">
                                    @if($pAdmin->avatar)
                                        <img src="{{ $pAdmin->avatar }}" alt="{{ $pAdmin->name }}" class="rounded-2 border" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="rounded-2 bg-light text-dark fw-bold border d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                            {{ strtoupper(substr($pAdmin->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.82rem;">{{ $pAdmin->name }}</div>
                                        <div class="text-secondary text-truncate" style="font-size: 0.7rem;">{{ $pAdmin->email }}</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                                    <form action="{{ route('admin.manage.approve', $pAdmin->id) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill fw-bold py-1 px-2.5 shadow-none" style="font-size: 0.7rem;">
                                            Setujui
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.manage.delete', $pAdmin->id) }}" onclick="return confirm('Tolak permohonan {{ $pAdmin->name }}?')" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1" style="font-size: 0.7rem;">
                                        Tolak
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- STATISTICS METRIC CARDS --}}
    @php
        $totalAdmins = count($admins);
        $onlineAdminsCount = $admins->filter(fn($a) => method_exists($a, 'isOnline') ? $a->isOnline() : false)->count();
        $frozenAdminsCount = $admins->filter(fn($a) => isset($a->is_active) && !$a->is_active)->count();
        $superAdminsCount = $admins->filter(function($a) {
            $p = is_array($a->permissions) ? $a->permissions : (json_decode($a->permissions, true) ?: []);
            return count($p) >= 15;
        })->count();
    @endphp
    <div class="admin-stats-grid">
        {{-- Total Akun --}}
        <div class="admin-stat-card">
            <div class="stat-icon-wrapper gray">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-details">
                <strong>{{ $totalAdmins }}</strong>
                <span>Total Akun</span>
            </div>
        </div>

        {{-- Online Aktif --}}
        <div class="admin-stat-card">
            <div class="stat-icon-wrapper green">
                <i class="bi bi-broadcast"></i>
            </div>
            <div class="stat-details">
                <strong>
                    {{ $onlineAdminsCount }}
                    @if($onlineAdminsCount > 0)
                        <span class="live-status-pill">
                            <span class="live-dot-pulse"></span> Live
                        </span>
                    @endif
                </strong>
                <span>Online Aktif</span>
            </div>
        </div>

        {{-- Akun Dibekukan --}}
        <div class="admin-stat-card">
            <div class="stat-icon-wrapper blue">
                <i class="bi bi-snow"></i>
            </div>
            <div class="stat-details">
                <strong style="{{ $frozenAdminsCount > 0 ? 'color:#ef4444;' : '' }}">{{ $frozenAdminsCount }}</strong>
                <span>Akun Dibekukan</span>
            </div>
        </div>

        {{-- Super Admin --}}
        <div class="admin-stat-card">
            <div class="stat-icon-wrapper orange">
                <i class="bi bi-award-fill"></i>
            </div>
            <div class="stat-details">
                <strong>{{ $superAdminsCount }}</strong>
                <span>Super Admin (15/15)</span>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE CARD CONTAINER --}}
    <div class="admin-main-card">
        {{-- TOOLBAR --}}
        <div class="admin-toolbar">
            <div class="admin-search-box">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="adminSearchInput" placeholder="Cari nama, username, atau email...">
                <button type="button" id="clearSearchBtn" class="clear-search d-none" title="Hapus"><i class="bi bi-x-circle-fill"></i></button>
            </div>

            <div class="admin-filter-group">
                <button type="button" class="admin-filter-chip active" data-filter="all">
                    Semua <span class="chip-count">{{ $totalAdmins }}</span>
                </button>

                <button type="button" class="admin-filter-chip" data-filter="online">
                    <i class="bi bi-circle-fill text-success" style="font-size: 0.45rem;"></i>
                    Online <span class="chip-count">{{ $onlineAdminsCount }}</span>
                </button>

                @if($frozenAdminsCount > 0)
                    <button type="button" class="admin-filter-chip" data-filter="frozen">
                        <i class="bi bi-snow text-danger"></i>
                        Dibekukan <span class="chip-count">{{ $frozenAdminsCount }}</span>
                    </button>
                @endif

                <button type="button" class="admin-filter-chip" data-filter="superadmin">
                    <i class="bi bi-award-fill text-warning"></i>
                    Super Admin <span class="chip-count">{{ $superAdminsCount }}</span>
                </button>
            </div>
        </div>

        {{-- TABLE WRAPPER --}}
        <div class="admin-table-container">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th style="min-width: 250px;">Admin / Pengguna</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 210px;">Hak Akses Modul</th>
                        <th style="min-width: 180px;">Aktivitas Terakhir</th>
                        <th style="text-align: right; min-width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                    @forelse($admins as $admin)
                        @php
                            $userPerms = $admin->permissions;
                            if (!is_array($userPerms)) {
                                $userPerms = json_decode($userPerms, true) ?: [];
                            }
                            $activeCount = count($userPerms);
                            $isOnline = method_exists($admin, 'isOnline') ? $admin->isOnline() : false;
                            $isActive = isset($admin->is_active) ? (bool)$admin->is_active : true;
                            $isCurrentUser = ($admin->id === Auth::id());
                            $isSuperAdmin = ($activeCount >= 15);
                            $pct = round(($activeCount / 15) * 100);
                        @endphp
                        <tr class="admin-item {{ !$isActive ? 'is-frozen' : '' }}"
                            data-name="{{ strtolower($admin->name) }}"
                            data-username="{{ strtolower($admin->username) }}"
                            data-email="{{ strtolower($admin->email) }}"
                            data-online="{{ $isOnline && $isActive ? '1' : '0' }}"
                            data-frozen="{{ !$isActive ? '1' : '0' }}"
                            data-superadmin="{{ $isSuperAdmin ? '1' : '0' }}">
                            
                            {{-- Admin / User Profile --}}
                            <td>
                                <div class="admin-profile-cell">
                                    @if($admin->avatar)
                                        <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}" 
                                             class="admin-avatar-box {{ $isSuperAdmin ? 'super' : '' }} {{ !$isActive ? 'frozen' : ($isOnline ? 'online' : '') }}">
                                    @else
                                        <div class="admin-avatar-box {{ $isSuperAdmin ? 'super' : '' }} {{ !$isActive ? 'frozen' : ($isOnline ? 'online' : '') }}">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="admin-name-title">
                                            <span>{{ $admin->name }}</span>
                                            @if($isCurrentUser)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.6rem; font-weight: 700;">
                                                    Anda
                                                </span>
                                            @endif
                                            @if($admin->google_id)
                                                <span class="badge bg-light text-dark border rounded-pill px-1.5 py-0.5" style="font-size: 0.58rem;" title="Google: {{ $admin->email }}">
                                                    <i class="bi bi-google text-danger"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="admin-subtext">
                                            <span>{{ '@' . $admin->username }}</span>
                                            <span class="mx-1">&bull;</span>
                                            <span>{{ $admin->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td>
                                @if(!$isActive)
                                    <span class="status-pill frozen">
                                        <i class="bi bi-snow me-1"></i> Dibekukan
                                    </span>
                                @elseif($isOnline)
                                    <span class="status-pill online">
                                        Online
                                    </span>
                                @else
                                    <span class="status-pill offline">
                                        Offline
                                    </span>
                                @endif
                            </td>

                            {{-- Module Access Count / Bar --}}
                            <td class="cell-access">
                                @if($isSuperAdmin)
                                    <span class="superadmin-label-badge">
                                        <i class="bi bi-award-fill text-warning me-1"></i> Super Admin (15/15)
                                    </span>
                                @else
                                    <div class="access-display-wrap">
                                        <div class="access-meter">
                                            <span style="width: {{ $pct }}%;"></span>
                                        </div>
                                        <span class="access-count-label">
                                            {{ $activeCount }}/15 Modul
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Activity Column --}}
                            <td>
                                <div class="activity-cell">
                                    <strong>
                                        <i class="bi bi-clock me-1 text-muted" style="font-size: 0.72rem;"></i>
                                        {{ $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Belum pernah login' }}
                                    </strong>
                                    <span title="{{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada catatan aktivitas' }}">
                                        {{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada catatan aktivitas' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="admin-action-tools">
                                    <button type="button" class="btn-manage-perms" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#permissionsModal{{ $admin->id }}">
                                        <i class="bi bi-sliders2-vertical"></i> <span>Atur Izin</span>
                                    </button>

                                    <div class="dropdown d-inline-block">
                                        <button class="btn-table-dots shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                            <i class="bi bi-three-dots"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-unified shadow-sm">
                                            <li>
                                                <button class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                                    <i class="bi bi-pencil text-muted"></i>
                                                    <span>Edit Data Akun</span>
                                                </button>
                                            </li>

                                            @if(!$isCurrentUser)
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2" onclick="forceLogoutAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                                                        <i class="bi bi-box-arrow-right text-warning"></i>
                                                        <span>Putus Sesi (Logout)</span>
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item d-flex align-items-center gap-2 {{ $isActive ? 'text-danger' : 'text-success' }}" 
                                                            onclick="toggleAdminStatus({{ $admin->id }}, '{{ addslashes($admin->name) }}', {{ $isActive ? 'true' : 'false' }})">
                                                        <i class="bi {{ $isActive ? 'bi-snow text-info' : 'bi-sun-fill text-warning' }}"></i>
                                                        <span>{{ $isActive ? 'Bekukan Akun' : 'Aktifkan Akun' }}</span>
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger d-flex align-items-center gap-2" 
                                                       href="{{ route('admin.manage.delete', $admin->id) }}" 
                                                       onclick="return confirm('Hapus akun admin {{ $admin->username }} permanen?');">
                                                        <i class="bi bi-trash"></i>
                                                        <span>Hapus Akun</span>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
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

        {{-- Empty Search Results --}}
        <div id="noAdminResults" class="p-5 text-center text-secondary d-none border-top">
            <i class="bi bi-search fs-2 text-muted mb-2 d-block opacity-50"></i>
            <h6 class="fw-bold text-dark mb-1">Tidak Ada Admin Ditemukan</h6>
            <p class="text-secondary small mb-3">Tidak ada akun admin yang cocok dengan pencarian atau filter.</p>
            <button type="button" id="resetFiltersBtn" class="btn btn-outline-dark rounded-pill px-3 py-1 btn-sm fw-semibold">
                Reset Filter
            </button>
        </div>
    </div>
</div>

{{-- ALL MODALS (Permissions Modal + Edit Admin Modal + Add Admin Modal) --}}
@foreach($admins as $admin)
    @php
        $userPerms = $admin->permissions;
        if (!is_array($userPerms)) {
            $userPerms = json_decode($userPerms, true) ?: [];
        }
        $activeCount = count($userPerms);
    @endphp

    {{-- Permissions Management Modal --}}
    <div class="modal fade" id="permissionsModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                {{-- Modal Header with Dark Gradient --}}
                <div class="modal-header border-0 text-white px-4 py-3" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                    <div class="d-flex align-items-center">
                        <div class="admin-avatar-box me-3" style="width: 42px; height: 42px; font-size: 1.1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffb72d;">
                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                        </div>
                        <div class="text-start">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="modal-title fw-bold text-white mb-0" style="font-size: 1.05rem;">{{ $admin->name }}</h5>
                                <span class="badge bg-warning text-dark fw-bold rounded-pill px-2 py-0.5 modal-perm-badge" style="font-size: 0.68rem;">
                                    {{ $activeCount }}/15 Modul Aktif
                                </span>
                            </div>
                            <p class="text-white-50 mb-0 small" style="font-size: 0.76rem;">{{ '@' . $admin->username }} &bull; {{ $admin->email }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-3 p-md-3.5 bg-light" style="max-height: 70vh; overflow-y: auto;">
                    {{-- Quick Role Preset Bar --}}
                    <div class="bg-white rounded-3 border border-light-subtle p-2.5 mb-3 shadow-xs">
                        <div class="d-flex align-items-center justify-content-between mb-1.5 flex-wrap gap-2">
                            <span class="fw-bold text-dark small" style="font-size: 0.78rem;">
                                <i class="bi bi-lightning-charge-fill text-warning me-1"></i> Preset Peran Cepat (1 Klik):
                            </span>
                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-0.5" style="font-size: 0.62rem;">
                                Auto-Sync
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-1.5">
                            <button type="button" class="btn btn-sm btn-preset" 
                                    onclick="applyPreset({{ $admin->id }}, 'wasit', 'Wasit Turnamen')">
                                <i class="bi bi-trophy text-warning me-1"></i> Wasit
                            </button>
                            <button type="button" class="btn btn-sm btn-preset" 
                                    onclick="applyPreset({{ $admin->id }}, 'bendahara', 'Bendahara / Keuangan')">
                                <i class="bi bi-cash-stack text-success me-1"></i> Bendahara
                            </button>
                            <button type="button" class="btn btn-sm btn-preset" 
                                    onclick="applyPreset({{ $admin->id }}, 'cs', 'Customer Care / LO')">
                                <i class="bi bi-headset text-info me-1"></i> Customer Care
                            </button>
                            <button type="button" class="btn btn-sm btn-preset" 
                                    onclick="applyPreset({{ $admin->id }}, 'super', 'Super Admin')">
                                <i class="bi bi-award-fill text-primary me-1"></i> Super Admin (Semua)
                            </button>
                            <button type="button" class="btn btn-sm btn-preset-danger" 
                                    onclick="applyPreset({{ $admin->id }}, 'none', 'Kosongkan')">
                                <i class="bi bi-x-circle text-danger me-1"></i> Kosongkan
                            </button>
                        </div>
                    </div>

                    {{-- 15 Permissions Grid --}}
                    <div class="row g-2">
                        @php
                            $availablePerms = [
                                'dashboard' => [
                                    'label' => 'Dashboard Utama',
                                    'desc' => 'Mengakses beranda dashboard admin, metrik ringkasan sistem, dan grafik registrasi.',
                                    'icon' => 'bi-grid-1x2',
                                    'color' => '#0f172a',
                                    'bg' => '#e2e8f0'
                                ],
                                'seasons' => [
                                    'label' => 'Daftar Season', 
                                    'desc' => 'Mengakses menu season, membuat season baru, mengubah detail turnamen & verifikasi tim.',
                                    'icon' => 'bi-trophy', 
                                    'color' => '#d97706',
                                    'bg' => '#fef3c7'
                                ],
                                'teams' => [
                                    'label' => 'Daftar Team (Global)',
                                    'desc' => 'Melihat list global seluruh tim, filter status pembayaran, dan melacak roster pemain.',
                                    'icon' => 'bi-people-fill',
                                    'color' => '#06b6d4',
                                    'bg' => '#ecfeff'
                                ],
                                'payments' => [
                                    'label' => 'Riwayat Pembayaran (Gateway & QRIS)',
                                    'desc' => 'Mengakses mutasi log transaksi gateway, sinkronisasi status, klaim QRIS & approval bukti transfer.',
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
                                    'desc' => 'Mengonfigurasi token API WhatsApp Fonnte, kredensial Tripay/iPaymu, dan setting global.',
                                    'icon' => 'bi-gear',
                                    'color' => '#ea580c',
                                    'bg' => '#ffedd5'
                                ],
                                'gateway_notifications' => [
                                    'label' => 'Notifikasi Gateway',
                                    'desc' => 'Mengakses log rekam jejak notifikasi Webhook/Callback dari payment gateway realtime.',
                                    'icon' => 'bi-bell',
                                    'color' => '#f59e0b',
                                    'bg' => '#fef3c7'
                                ],
                                'faqs' => [
                                    'label' => 'Kelola FAQ', 
                                    'desc' => 'Menambah, menyusun urutan, dan menghapus daftar tanya jawab publik di portal.',
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
                                    'desc' => 'Menambah, mengedit, membekukan akun admin, serta mengatur pembagian hak akses modul.',
                                    'icon' => 'bi-person-gear',
                                    'color' => '#3b82f6',
                                    'bg' => '#dbeafe'
                                ],
                                'laravel_logs' => [
                                    'label' => 'Log Laravel',
                                    'desc' => 'Mengakses log error aplikasi Laravel (laravel.log) secara realtime untuk diagnostik.',
                                    'icon' => 'bi-journal-code',
                                    'color' => '#64748b',
                                    'bg' => '#f1f5f9'
                                ],
                                'storage' => [
                                    'label' => 'Kelola Penyimpanan File',
                                    'desc' => 'Memantau kapasitas disk space server dan membersihkan file sampah massal.',
                                    'icon' => 'bi-hdd-network',
                                    'color' => '#14b8a6',
                                    'bg' => '#ccfbf1'
                                ],
                                'backup' => [
                                    'label' => 'Backup Database (Super)',
                                    'desc' => 'Mengunduh salinan cadangan struktur dan data SQL database platform langsung.',
                                    'icon' => 'bi-database-down',
                                    'color' => '#ef4444',
                                    'bg' => '#fee2e2'
                                ],
                                'finance' => [
                                    'label' => 'Keuangan Ledger (Modul Season)', 
                                    'desc' => 'Mengakses finansial season, mencatat pemasukan/pengeluaran kas, serta rekapitulasi cashflow.',
                                    'icon' => 'bi-currency-exchange', 
                                    'color' => '#22c55e',
                                    'bg' => '#f0fdf4'
                                ],
                                'solo_matchmaker' => [
                                    'label' => 'Solo Matchmaker (Modul Season)', 
                                    'desc' => 'Menggunakan modul matchmaking otomatis untuk menyusun peserta solo ke dalam komposisi tim.',
                                    'icon' => 'bi-people', 
                                    'color' => '#6366f1',
                                    'bg' => '#e0e7ff'
                                ],
                            ];
                        @endphp

                        @foreach($availablePerms as $key => $pInfo)
                            <div class="col-md-6">
                                <div class="permission-card p-2.5 h-100 d-flex justify-content-between align-items-start">
                                    <div class="d-flex align-items-start me-2">
                                        <div class="icon-square me-2" style="background-color: {{ $pInfo['bg'] }}; color: {{ $pInfo['color'] }};">
                                            <i class="bi {{ $pInfo['icon'] }}"></i>
                                        </div>
                                        <div class="text-start">
                                            <div class="fw-bold text-dark mb-0.5" style="font-size: 0.82rem;">{{ $pInfo['label'] }}</div>
                                            <div class="text-secondary" style="font-size: 0.7rem; line-height: 1.3;">{{ $pInfo['desc'] }}</div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch p-0 m-0 pt-0.5">
                                        <input class="form-check-input permission-switch shadow-none" 
                                               type="checkbox" 
                                               role="switch" 
                                               id="perm_modal_{{ $admin->id }}_{{ $key }}"
                                               data-admin-id="{{ $admin->id }}"
                                               data-permission="{{ $key }}"
                                               {{ in_array($key, $userPerms) ? 'checked' : '' }}
                                               style="width: 2.1em; height: 1.05em; cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer border-0 bg-white px-4 py-2.5 justify-content-between">
                    <span class="text-muted small" style="font-size: 0.74rem;">
                        <i class="bi bi-shield-lock me-1 text-warning"></i> Simpan otomatis setiap perubahan switch
                    </span>
                    <button type="button" class="btn btn-sm btn-dark rounded-pill px-4 fw-semibold shadow-sm text-white" data-bs-dismiss="modal">
                        Selesai &amp; Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Admin Modal --}}
    <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                <form action="{{ route('admin.manage.update', $admin->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom border-light px-4 py-3">
                        <h6 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-warning me-1.5"></i> Edit Akun Admin</h6>
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
                    <div class="modal-footer border-top border-light px-4 py-2.5">
                        <button type="button" class="btn btn-light rounded-pill px-3 py-1 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning rounded-pill px-3.5 py-1 fw-bold text-dark btn-sm">Simpan</button>
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
                    <h6 class="modal-title fw-bold text-dark mb-0"><i class="bi bi-person-plus-fill text-warning me-1.5"></i> Tambah Akun Admin Baru</h6>
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
                <div class="modal-footer border-top border-light px-4 py-2.5">
                    <button type="button" class="btn btn-light rounded-pill px-3 py-1 fw-semibold btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-3.5 py-1 fw-bold text-dark btn-sm">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast Notification Container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1300;">
    <div id="permissionToast" class="toast align-items-center border-0 text-white rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center py-2 px-3">
                <i class="bi me-2 fs-5" id="toastIcon"></i>
                <span id="toastMessage" class="fw-semibold" style="font-size: 0.82rem;"></span>
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
            
            fetch('/admin/manage-admins/toggle-permission', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    admin_id: adminId,
                    permission: permission,
                    status: status
                })
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().catch(() => ({ message: 'Gagal memperbarui izin (Status ' + res.status + ')' })).then(d => Promise.reject(d));
                }
                return res.json();
            })
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
                const msg = (err && err.message) ? err.message : 'Terjadi kesalahan jaringan saat mengubah izin';
                showToast('Gagal', msg, 'danger');
            });
        });
    });

    // 2. Real-time Search and Filter Tabs Logic
    const searchInput = document.getElementById('adminSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const filterChips = document.querySelectorAll('.admin-filter-chip');
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

    filterChips.forEach(chip => {
        chip.addEventListener('click', function() {
            filterChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter || 'all';
            applyFilters();
        });
    });

    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            currentFilter = 'all';
            filterChips.forEach(c => {
                if (c.dataset.filter === 'all') c.classList.add('active');
                else c.classList.remove('active');
            });
            applyFilters();
        });
    }
});

// Helper to update table visuals after permission changes
function updateCardPermissionsVisual(adminId, permissions) {
    const modalTrigger = document.querySelector(`[data-bs-target="#permissionsModal${adminId}"]`);
    if (!permissions) return;

    const count = permissions.length;
    const isSuper = count >= 15;
    const pct = Math.round((count / 15) * 100);

    if (modalTrigger) {
        const adminItem = modalTrigger.closest('.admin-item');
        if (adminItem) {
            const accessCell = adminItem.querySelector('.cell-access');
            if (accessCell) {
                if (isSuper) {
                    accessCell.innerHTML = `
                        <span class="superadmin-label-badge">
                            <i class="bi bi-award-fill text-warning me-1"></i> Super Admin (15/15)
                        </span>
                    `;
                } else {
                    accessCell.innerHTML = `
                        <div class="access-display-wrap">
                            <div class="access-meter">
                                <span style="width: ${pct}%;"></span>
                            </div>
                            <span class="access-count-label">${count}/15 Modul</span>
                        </div>
                    `;
                }
            }

            const avatarEl = adminItem.querySelector('.admin-avatar-box');
            if (avatarEl) {
                if (isSuper) avatarEl.classList.add('super');
                else avatarEl.classList.remove('super');
            }

            adminItem.dataset.superadmin = isSuper ? '1' : '0';
        }
    }

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

    fetch('/admin/manage-admins/sync-permissions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            admin_id: adminId,
            permissions: targetPerms,
            preset_name: presetName
        })
    })
    .then(res => {
        if (!res.ok) {
            return res.json().catch(() => ({ message: 'Gagal menerapkan preset (Status ' + res.status + ')' })).then(d => Promise.reject(d));
        }
        return res.json();
    })
    .then(data => {
        switches.forEach(sw => sw.disabled = false);
        if (data.success) {
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
        const msg = (err && err.message) ? err.message : 'Terjadi kesalahan jaringan saat menerapkan preset';
        showToast('Gagal', msg, 'danger');
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
                    'Accept': 'application/json',
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
                    'Accept': 'application/json',
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
