@extends('layouts.admin')

@section('content')
<style>
    /* ==============================
       ADMIN MANAGEMENT CONTENT (CUSTOM USER DESIGN)
    ============================== */

    .admin-page {
        padding: 28px 32px 40px;
        background: #f6f8fb;
        min-height: 100vh;
        font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #172033;
    }

    /* HEADER */
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .admin-header h1 {
        margin: 0 0 7px;
        font-size: 27px;
        font-weight: 800;
        letter-spacing: -0.8px;
        color: #151d2d;
        line-height: 1.2;
    }

    .admin-header p {
        margin: 0;
        color: #7d899b;
        font-size: 13px;
        line-height: 1.4;
    }

    .header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-shrink: 0;
    }

    .admin-btn {
        height: 42px;
        padding: 0 16px;
        border-radius: 10px;
        border: 1px solid #e5e9ef;
        background: #fff;
        color: #596579;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: .2s ease;
        text-decoration: none;
        white-space: nowrap;
    }

    .admin-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 7px 18px rgba(20, 30, 50, .07);
    }

    .admin-btn-danger {
        color: #ef4444;
        border-color: #ffcaca;
        background: #fff;
    }

    .admin-btn-primary {
        color: #182233;
        border: none;
        background: linear-gradient(135deg, #ffb21a, #f39400);
        box-shadow: 0 7px 17px rgba(255, 159, 0, .18);
    }

    /* ALERT */
    .admin-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 16px;
        margin-bottom: 20px;
        border-radius: 12px;
        border: 1px solid #ccebdc;
        background: #e8f7ef;
        color: #176b43;
        font-size: 12px;
        font-weight: 600;
    }

    .admin-alert-danger {
        border-color: #fecaca;
        background: #fef2f2;
        color: #991b1b;
    }

    .admin-alert .alert-icon {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #159765;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        flex-shrink: 0;
    }

    .admin-alert-danger .alert-icon {
        background: #ef4444;
    }

    /* GOOGLE PENDING ACCESS BANNER */
    .pending-banner {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-left: 4px solid #f59e0b;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }

    /* STATISTICS */
    .admin-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .admin-stat {
        background: #fff;
        border: 1px solid #e7ebf0;
        border-radius: 15px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        transition: .2s ease;
    }

    .admin-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(20, 30, 50, .05);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        flex-shrink: 0;
    }

    .stat-icon.gray {
        background: #f2f4f7;
        color: #68758a;
    }

    .stat-icon.green {
        background: #e8f8f0;
        color: #16a36a;
    }

    .stat-icon.blue {
        background: #edf5ff;
        color: #3b82f6;
    }

    .stat-icon.orange {
        background: #fff5df;
        color: #e99a00;
    }

    .stat-info strong {
        display: flex;
        align-items: center;
        font-size: 21px;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 6px;
        color: #151d2d;
    }

    .stat-info span {
        color: #818c9d;
        font-size: 11px;
        font-weight: 500;
    }

    .live-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 7px;
        margin-left: 6px;
        border-radius: 20px;
        background: #e8f8f0;
        color: #159765 !important;
        font-size: 9px !important;
        font-weight: 700 !important;
    }

    .live-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #16a36a;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(22, 163, 106, 0.7);
        animation: livePulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
    }

    @keyframes livePulse {
        to {
            box-shadow: 0 0 0 5px rgba(22, 163, 106, 0);
        }
    }

    /* TABLE CONTAINER */
    .admin-table-card {
        background: #fff;
        border: 1px solid #e6eaf0;
        border-radius: 17px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(20, 30, 50, .025);
    }

    /* TOOLBAR */
    .admin-toolbar {
        min-height: 72px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        border-bottom: 1px solid #edf0f4;
    }

    .admin-search {
        width: 360px;
        height: 40px;
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 0 12px;
        border: 1px solid #e5e9ef;
        border-radius: 10px;
        background: #fafbfc;
        color: #8a95a6;
        transition: .2s ease;
    }

    .admin-search:focus-within {
        border-color: #ffb52b;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(255, 159, 0, .08);
    }

    .admin-search-icon {
        font-size: 15px;
        color: #8a95a6;
    }

    .admin-search input {
        width: 100%;
        border: none;
        outline: none;
        background: transparent;
        color: #273247;
        font-size: 12px;
    }

    .admin-search input::placeholder {
        color: #a0a8b5;
    }

    /* FILTER */
    .admin-filters {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .admin-filter {
        height: 34px;
        padding: 0 11px;
        border: 1px solid #e6eaf0;
        border-radius: 9px;
        background: #fff;
        color: #6c788b;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: .2s;
        display: inline-flex;
        align-items: center;
    }

    .admin-filter:hover {
        background: #f7f8fa;
    }

    .admin-filter.active {
        background: #111b30;
        border-color: #111b30;
        color: #fff;
    }

    .filter-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 17px;
        height: 17px;
        padding: 0 4px;
        margin-left: 4px;
        border-radius: 20px;
        background: rgba(255,255,255,.18);
        font-size: 9px;
        font-weight: 700;
    }

    .admin-filter:not(.active) .filter-count {
        background: #f0f2f5;
        color: #7d899a;
    }

    /* TABLE */
    .admin-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .admin-table {
        width: 100%;
        min-width: 950px;
        border-collapse: collapse;
    }

    .admin-table th {
        padding: 14px 18px;
        text-align: left;
        background: #fbfcfd;
        border-bottom: 1px solid #edf0f4;
        color: #68758a;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .35px;
    }

    .admin-table td {
        padding: 15px 18px;
        border-bottom: 1px solid #f0f2f5;
        font-size: 12px;
        vertical-align: middle;
    }

    .admin-table tbody tr {
        transition: .18s ease;
    }

    .admin-table tbody tr:hover {
        background: #fafbfd;
    }

    .admin-table tbody tr.is-frozen {
        background: #fff9f9;
    }

    .admin-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* USER */
    .admin-user {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .admin-avatar {
        width: 40px;
        height: 40px;
        border-radius: 11px;
        background: #111b30;
        color: #ffae13;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
        flex-shrink: 0;
        object-fit: cover;
        border: 1.5px solid transparent;
    }

    .admin-avatar.super {
        background: linear-gradient(135deg, #ffe8af, #ffb72d);
        color: #674400;
    }

    .admin-avatar.online {
        border-color: #22c55e;
    }

    .admin-avatar.frozen {
        border-color: #ef4444;
        opacity: 0.75;
    }

    .admin-name {
        font-size: 12px;
        font-weight: 700;
        color: #202a3b;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .admin-email {
        color: #9099a8;
        font-size: 10px;
    }

    /* STATUS */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
    }

    .status-offline {
        background: #f1f3f5;
        color: #7d8796;
    }

    .status-online {
        background: #e8f8f0;
        color: #168052;
    }

    .status-online::before {
        content: "";
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #16a36a;
    }

    .status-frozen {
        background: #fee2e2;
        color: #dc2626;
    }

    /* ACCESS */
    .access-wrapper {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .access-progress {
        width: 75px;
        height: 5px;
        overflow: hidden;
        border-radius: 10px;
        background: #edf0f4;
    }

    .access-progress span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #f5a000;
    }

    .access-text {
        color: #596579;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .super-admin-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 9px;
        border-radius: 7px;
        background: #fff4d9;
        color: #956100;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    /* ACTIVITY */
    .activity strong {
        display: block;
        margin-bottom: 3px;
        color: #263144;
        font-size: 11px;
    }

    .activity span {
        color: #929baa;
        font-size: 10px;
        display: block;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ACTION */
    .admin-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 6px;
    }

    .permission-btn {
        height: 34px;
        padding: 0 11px;
        border: 1px solid #ffd37e;
        border-radius: 9px;
        background: #fff;
        color: #986700;
        font-size: 10px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .permission-btn:hover {
        background: #fff6e4;
    }

    .more-btn {
        width: 34px;
        height: 34px;
        border: 1px solid #e6eaf0;
        border-radius: 9px;
        background: #fff;
        color: #778397;
        cursor: pointer;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: .2s;
        padding: 0;
    }

    .more-btn:hover {
        background: #f5f7fa;
        color: #172033;
    }

    .dropdown-menu-admin {
        border: 1px solid #e6eaf0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(20, 30, 50, .1);
        padding: 6px;
        font-size: 0.8rem;
        min-width: 190px;
    }

    .dropdown-menu-admin .dropdown-item {
        padding: 7px 12px;
        border-radius: 8px;
        font-weight: 500;
        color: #334155;
        transition: all 0.12s ease;
    }

    .dropdown-menu-admin .dropdown-item:hover {
        background-color: #f8fafc;
        color: #0f172a;
    }

    .dropdown-menu-admin .dropdown-item.text-danger:hover {
        background-color: #fef2f2;
        color: #dc2626 !important;
    }

    /* Modal Permissions */
    .btn-preset {
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 12px;
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
        padding: 4px 12px;
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
        border-radius: 10px;
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

    /* RESPONSIVE */
    @media (max-width: 1100px) {
        .admin-stats {
            grid-template-columns: repeat(2, 1fr);
        }
        .admin-header {
            flex-direction: column;
        }
        .header-actions {
            width: 100%;
        }
    }

    @media (max-width: 700px) {
        .admin-page {
            padding: 20px 15px;
        }
        .admin-stats {
            grid-template-columns: 1fr;
        }
        .header-actions {
            flex-direction: column;
        }
        .admin-btn {
            justify-content: center;
            width: 100%;
        }
        .admin-toolbar {
            flex-direction: column;
            align-items: stretch;
        }
        .admin-search {
            width: 100%;
        }
        .admin-filters {
            overflow-x: auto;
            padding-bottom: 2px;
        }
        .admin-filter {
            white-space: nowrap;
        }
    }
</style>

<div class="admin-page">

    {{-- HEADER --}}
    <div class="admin-header">
        <div>
            <h1>Kelola Akun Admin</h1>
            <p>
                Kelola otorisasi modul, status sesi aktif, dan hak akses staf administrator turnamen.
            </p>
        </div>

        <div class="header-actions">
            <form action="{{ route('admin.manage.force-logout-all') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('PERINGATAN: Logout semua user & perangkat sekarang?\n\nSemua admin (termasuk Anda) akan langsung dikeluarkan.');">
                @csrf
                <button type="submit" class="admin-btn admin-btn-danger">
                    <span>⏻</span> Logout Semua Sesi
                </button>
            </form>

            <button type="button" class="admin-btn admin-btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <span>＋</span> Tambah Staf Admin
            </button>
        </div>
    </div>

    {{-- ALERTS --}}
    @if ($errors->any())
        <div class="admin-alert admin-alert-danger">
            <div class="alert-icon">✕</div>
            <div>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="admin-alert">
            <div class="alert-icon">✓</div>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Pending Google Access Notification (if any) --}}
    @if(isset($pendingAdmins) && count($pendingAdmins) > 0)
        <div class="pending-banner">
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
    @endif

    {{-- STATISTICS --}}
    @php
        $totalAdmins = count($admins);
        $onlineAdminsCount = $admins->filter(fn($a) => method_exists($a, 'isOnline') ? $a->isOnline() : false)->count();
        $frozenAdminsCount = $admins->filter(fn($a) => isset($a->is_active) && !$a->is_active)->count();
        $superAdminsCount = $admins->filter(function($a) {
            $p = is_array($a->permissions) ? $a->permissions : (json_decode($a->permissions, true) ?: []);
            return count($p) >= 15;
        })->count();
    @endphp
    <div class="admin-stats">
        {{-- Stat 1: Total Akun --}}
        <div class="admin-stat">
            <div class="stat-icon gray">
                👥
            </div>
            <div class="stat-info">
                <strong>{{ $totalAdmins }}</strong>
                <span>Total Akun</span>
            </div>
        </div>

        {{-- Stat 2: Online Aktif --}}
        <div class="admin-stat">
            <div class="stat-icon green">
                ◉
            </div>
            <div class="stat-info">
                <strong>
                    {{ $onlineAdminsCount }}
                    @if($onlineAdminsCount > 0)
                        <span class="live-status">
                            <span class="live-dot"></span>
                            Live
                        </span>
                    @endif
                </strong>
                <span>Online Aktif</span>
            </div>
        </div>

        {{-- Stat 3: Akun Dibekukan --}}
        <div class="admin-stat">
            <div class="stat-icon blue">
                ❄
            </div>
            <div class="stat-info">
                <strong style="{{ $frozenAdminsCount > 0 ? 'color:#ef4444;' : '' }}">{{ $frozenAdminsCount }}</strong>
                <span>Akun Dibekukan</span>
            </div>
        </div>

        {{-- Stat 4: Super Admin --}}
        <div class="admin-stat">
            <div class="stat-icon orange">
                ♛
            </div>
            <div class="stat-info">
                <strong>{{ $superAdminsCount }}</strong>
                <span>Super Admin (15/15)</span>
            </div>
        </div>
    </div>

    {{-- TABLE CONTAINER --}}
    <div class="admin-table-card">

        {{-- TOOLBAR --}}
        <div class="admin-toolbar">
            <div class="admin-search">
                <span class="admin-search-icon">⌕</span>
                <input type="text" id="adminSearchInput" placeholder="Cari admin...">
                <button type="button" id="clearSearchBtn" class="btn btn-sm text-muted d-none border-0 p-0" style="font-size: 13px;" title="Hapus">✕</button>
            </div>

            <div class="admin-filters">
                <button type="button" class="admin-filter active" data-filter="all">
                    Semua
                    <span class="filter-count">{{ $totalAdmins }}</span>
                </button>

                <button type="button" class="admin-filter" data-filter="online">
                    <span style="color:#16a36a; margin-right: 4px;">●</span>
                    Online
                    <span class="filter-count">{{ $onlineAdminsCount }}</span>
                </button>

                @if($frozenAdminsCount > 0)
                    <button type="button" class="admin-filter" data-filter="frozen">
                        <span style="color:#ef4444; margin-right: 4px;">❄</span>
                        Dibekukan
                        <span class="filter-count">{{ $frozenAdminsCount }}</span>
                    </button>
                @endif

                <button type="button" class="admin-filter" data-filter="superadmin">
                    <span style="color:#f59e0b; margin-right: 4px;">♛</span>
                    Super Admin
                    <span class="filter-count">{{ $superAdminsCount }}</span>
                </button>
            </div>
        </div>

        {{-- TABLE WRAPPER --}}
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Admin / Pengguna</th>
                        <th>Status</th>
                        <th>Hak Akses Modul</th>
                        <th>Aktivitas Terakhir</th>
                        <th style="text-align:right;">Aksi</th>
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
                            
                            {{-- Admin / User --}}
                            <td>
                                <div class="admin-user">
                                    @if($admin->avatar)
                                        <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}" 
                                             class="admin-avatar {{ $isSuperAdmin ? 'super' : '' }} {{ !$isActive ? 'frozen' : ($isOnline ? 'online' : '') }}">
                                    @else
                                        <div class="admin-avatar {{ $isSuperAdmin ? 'super' : '' }} {{ !$isActive ? 'frozen' : ($isOnline ? 'online' : '') }}">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="admin-name">
                                            {{ $admin->name }}
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
                                        <div class="admin-email">
                                            <span>{{ '@' . $admin->username }}</span>
                                            <span>·</span>
                                            <span>{{ $admin->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if(!$isActive)
                                    <span class="status-badge status-frozen">
                                        ❄ Dibekukan
                                    </span>
                                @elseif($isOnline)
                                    <span class="status-badge status-online">
                                        Online
                                    </span>
                                @else
                                    <span class="status-badge status-offline">
                                        Offline
                                    </span>
                                @endif
                            </td>

                            {{-- Access Modul --}}
                            <td class="cell-access">
                                @if($isSuperAdmin)
                                    <span class="super-admin-badge">
                                        ♛ Super Admin · 15/15
                                    </span>
                                @else
                                    <div class="access-wrapper">
                                        <div class="access-progress">
                                            <span style="width: {{ $pct }}%;"></span>
                                        </div>
                                        <span class="access-text">
                                            {{ $activeCount }}/15 Modul
                                        </span>
                                    </div>
                                @endif
                            </td>

                            {{-- Activity --}}
                            <td>
                                <div class="activity">
                                    <strong>
                                        {{ $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Belum pernah' }}
                                    </strong>
                                    <span title="{{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada aktivitas' }}">
                                        {{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada catatan aktivitas' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="admin-actions">
                                    <button type="button" class="permission-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#permissionsModal{{ $admin->id }}">
                                        <span>⚙</span> Atur Izin
                                    </button>

                                    <div class="dropdown d-inline-block">
                                        <button class="more-btn shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                            ⋯
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-admin shadow-sm">
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
                            <td colspan="5" style="padding: 40px; text-align: center; color: #8a95a6;">
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
                        <div class="admin-avatar me-3" style="width: 42px; height: 42px; font-size: 1.1rem; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffb72d;">
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
    const filterChips = document.querySelectorAll('.admin-filter');
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
                        <span class="super-admin-badge">
                            ♛ Super Admin · 15/15
                        </span>
                    `;
                } else {
                    accessCell.innerHTML = `
                        <div class="access-wrapper">
                            <div class="access-progress">
                                <span style="width: ${pct}%;"></span>
                            </div>
                            <span class="access-text">${count}/15 Modul</span>
                        </div>
                    `;
                }
            }

            const avatarEl = adminItem.querySelector('.admin-avatar');
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
