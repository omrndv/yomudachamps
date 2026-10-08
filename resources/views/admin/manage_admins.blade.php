@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4 px-md-4">
<style>
    /* Compact Metric Bar */
    .metric-bar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.02);
    }
    .metric-tile {
        padding: 8px 16px;
    }
    .metric-tile:not(:last-child) {
        border-right: 1px solid #f1f5f9;
    }
    @media (max-width: 767.98px) {
        .metric-tile {
            padding: 8px 12px;
            border-right: none !important;
            border-bottom: 1px solid #f1f5f9;
        }
        .metric-tile:last-child {
            border-bottom: none;
        }
    }

    /* Modern Table Container */
    .table-container-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.04);
    }
    .table-toolbar {
        padding: 14px 18px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
    }
    .admin-main-table thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 12px 18px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .admin-main-table tbody td {
        padding: 13px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    .admin-main-table tbody tr {
        transition: background-color 0.15s ease;
    }
    .admin-main-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .admin-main-table tbody tr.is-frozen {
        background-color: #fff8f8;
    }
    .admin-main-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Avatars */
    .avatar-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
        flex-shrink: 0;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #f59e0b;
        border: 1.5px solid transparent;
        transition: all 0.2s ease;
    }
    .avatar-box.online-ring {
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }
    .avatar-box.frozen-ring {
        border-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }
    .avatar-img {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1.5px solid transparent;
    }
    .avatar-img.online-ring {
        border-color: #22c55e;
        box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
    }
    .avatar-img.frozen-ring {
        border-color: #ef4444;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
    }

    /* Live Pulse */
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
            box-shadow: 0 0 0 6px rgba(34, 197, 94, 0);
        }
    }

    /* Filter Chips */
    .filter-chip {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        font-weight: 600;
        font-size: 0.78rem;
        transition: all 0.15s ease;
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
    }
    .filter-chip:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .filter-chip.active {
        background: #0f172a;
        border-color: #0f172a;
        color: #ffffff;
    }
    .filter-chip.active .count-badge {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
    }

    /* Action Dropdown Menu Button */
    .btn-action-more {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #64748b;
        font-size: 0.85rem;
        transition: all 0.15s ease;
        padding: 0;
    }
    .btn-action-more:hover, .btn-action-more:focus {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .dropdown-menu-admin {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.1);
        padding: 6px;
        font-size: 0.82rem;
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

    /* Preset buttons */
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

    /* Modal Permissions */
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
</style>

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3 pb-1">
        <div>
            <h2 class="fw-bold text-dark mb-0.5" style="font-size: 1.5rem; letter-spacing: -0.4px;">
                Kelola Akun Admin
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.84rem;">
                Kelola otorisasi 15 modul, status sesi aktif, dan hak akses staf administrator turnamen.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form action="{{ route('admin.manage.force-logout-all') }}" method="POST" class="d-inline m-0" onsubmit="return confirm('PERINGATAN: Logout semua user & perangkat sekarang?\n\nSemua admin (termasuk Anda) akan langsung dikeluarkan.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger fw-semibold px-3 py-1.5 rounded-3 d-inline-flex align-items-center gap-1.5 shadow-none" style="font-size: 0.82rem;">
                    <i class="bi bi-power"></i>
                    <span>Logout Semua Sesi</span>
                </button>
            </form>
            <button class="btn btn-warning fw-bold px-3.5 py-1.5 rounded-3 shadow-sm text-dark d-inline-flex align-items-center gap-1.5" data-bs-toggle="modal" data-bs-target="#addAdminModal" style="font-size: 0.82rem;">
                <i class="bi bi-person-plus-fill"></i>
                <span>Tambah Staf Admin</span>
            </button>
        </div>
    </div>

    {{-- Feedback Alerts --}}
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
        <div class="card border-0 shadow-sm rounded-3 mb-3 overflow-hidden" style="border-left: 4px solid #f59e0b !important; background: #fffbeb;">
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

    {{-- Slim Metric Bar --}}
    @php
        $totalAdmins = count($admins);
        $onlineAdminsCount = $admins->filter(fn($a) => method_exists($a, 'isOnline') ? $a->isOnline() : false)->count();
        $frozenAdminsCount = $admins->filter(fn($a) => isset($a->is_active) && !$a->is_active)->count();
        $superAdminsCount = $admins->filter(function($a) {
            $p = is_array($a->permissions) ? $a->permissions : (json_decode($a->permissions, true) ?: []);
            return count($p) >= 15;
        })->count();
    @endphp
    <div class="metric-bar-card mb-3">
        <div class="row g-0 align-items-center">
            {{-- Metric 1 --}}
            <div class="col-6 col-md-3 metric-tile">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-light text-secondary d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1.05rem;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 1.15rem; line-height: 1.1;">{{ $totalAdmins }}</div>
                        <div class="text-secondary small" style="font-size: 0.72rem;">Total Akun</div>
                    </div>
                </div>
            </div>

            {{-- Metric 2 --}}
            <div class="col-6 col-md-3 metric-tile">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1.05rem;">
                        <i class="bi bi-broadcast"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-1.5">
                            <span class="fw-bold text-dark" style="font-size: 1.15rem; line-height: 1.1;">{{ $onlineAdminsCount }}</span>
                            @if($onlineAdminsCount > 0)
                                <span class="badge bg-success-subtle text-success rounded-pill px-1.5 py-0.5" style="font-size: 0.62rem;">
                                    <span class="live-pulse-dot me-0.5"></span> Live
                                </span>
                            @endif
                        </div>
                        <div class="text-secondary small" style="font-size: 0.72rem;">Online Aktif</div>
                    </div>
                </div>
            </div>

            {{-- Metric 3 --}}
            <div class="col-6 col-md-3 metric-tile">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 {{ $frozenAdminsCount > 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-muted' }} d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1.05rem;">
                        <i class="bi bi-snow"></i>
                    </div>
                    <div>
                        <div class="fw-bold {{ $frozenAdminsCount > 0 ? 'text-danger' : 'text-dark' }}" style="font-size: 1.15rem; line-height: 1.1;">{{ $frozenAdminsCount }}</div>
                        <div class="text-secondary small" style="font-size: 0.72rem;">Akun Dibekukan</div>
                    </div>
                </div>
            </div>

            {{-- Metric 4 --}}
            <div class="col-6 col-md-3 metric-tile">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="rounded-3 bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 1.05rem;">
                        <i class="bi bi-award"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 1.15rem; line-height: 1.1;">{{ $superAdminsCount }}</div>
                        <div class="text-secondary small" style="font-size: 0.72rem;">Super Admin (15/15)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Card Container with Search & Filter Header --}}
    <div class="table-container-card">
        {{-- Toolbar inside Card --}}
        <div class="table-toolbar">
            <div class="row g-2.5 align-items-center justify-content-between">
                {{-- Search Box --}}
                <div class="col-12 col-md-4">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 12px; transform: translateY(-50%); font-size: 0.8rem;"></i>
                        <input type="text" id="adminSearchInput" class="form-control rounded-pill ps-5 pe-4 py-1.5 border-light-subtle shadow-none bg-light" placeholder="Cari admin..." style="font-size: 0.82rem;">
                        <button type="button" id="clearSearchBtn" class="btn btn-sm position-absolute text-muted d-none border-0 p-0" style="top: 50%; right: 12px; transform: translateY(-50%);" title="Hapus">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                {{-- Filter Chips --}}
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-center gap-1.5 flex-wrap justify-content-md-end">
                        <button type="button" class="btn btn-sm filter-chip active" data-filter="all">
                            Semua <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1 count-badge">{{ $totalAdmins }}</span>
                        </button>
                        <button type="button" class="btn btn-sm filter-chip" data-filter="online">
                            <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.45rem;"></i>
                            Online <span class="badge bg-success-subtle text-success rounded-pill ms-1 count-badge">{{ $onlineAdminsCount }}</span>
                        </button>
                        @if($frozenAdminsCount > 0)
                        <button type="button" class="btn btn-sm filter-chip" data-filter="frozen">
                            <i class="bi bi-snow text-danger me-1"></i>
                            Dibekukan <span class="badge bg-danger-subtle text-danger rounded-pill ms-1 count-badge">{{ $frozenAdminsCount }}</span>
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm filter-chip" data-filter="superadmin">
                            <i class="bi bi-award text-warning me-1"></i>
                            Super Admin <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill ms-1 count-badge">{{ $superAdminsCount }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table View --}}
        <div class="table-responsive">
            <table class="table admin-main-table mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 250px;">Admin / Pengguna</th>
                        <th style="min-width: 130px;">Status</th>
                        <th style="min-width: 210px;">Hak Akses Modul</th>
                        <th style="min-width: 170px;">Aktivitas Terakhir</th>
                        <th class="text-end" style="min-width: 140px;">Aksi</th>
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
                            
                            {{-- Col 1: Admin / Pengguna --}}
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($admin->avatar)
                                        <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}" class="avatar-img {{ !$isActive ? 'frozen-ring opacity-75' : ($isOnline ? 'online-ring' : '') }}">
                                    @else
                                        <div class="avatar-box {{ !$isActive ? 'frozen-ring opacity-75' : ($isOnline ? 'online-ring' : '') }}">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                            <span class="fw-semibold text-dark text-truncate" style="font-size: 0.88rem;" title="{{ $admin->name }}">
                                                {{ $admin->name }}
                                            </span>
                                            @if($isCurrentUser)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-1.5 py-0.5" style="font-size: 0.6rem;">
                                                    Anda
                                                </span>
                                            @endif
                                            @if($admin->google_id)
                                                <span class="badge bg-light text-dark border rounded-pill px-1.5 py-0.5" style="font-size: 0.58rem;" title="Google: {{ $admin->email }}">
                                                    <i class="bi bi-google text-danger"></i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-secondary small text-truncate" style="font-size: 0.74rem;">
                                            <span>{{ '@' . $admin->username }}</span>
                                            <span class="text-muted mx-1">&bull;</span>
                                            <span>{{ $admin->email }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Col 2: Status --}}
                            <td>
                                @if(!$isActive)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-snow me-1"></i> Dibekukan
                                    </span>
                                @elseif($isOnline)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        <span class="live-pulse-dot me-1"></span> Online
                                    </span>
                                @else
                                    <span class="badge bg-light text-secondary border rounded-pill px-2.5 py-1" style="font-size: 0.72rem;">
                                        Offline
                                    </span>
                                @endif
                            </td>

                            {{-- Col 3: Hak Akses --}}
                            <td>
                                @if($isSuperAdmin)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold" style="font-size: 0.73rem;">
                                        <i class="bi bi-award-fill me-1"></i> Super Admin (15/15)
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-medium perm-count-badge" style="font-size: 0.73rem;">
                                        <i class="bi bi-shield-check text-warning me-1"></i> {{ $activeCount }}/15 Modul
                                    </span>
                                @endif
                            </td>

                            {{-- Col 4: Aktivitas Terakhir --}}
                            <td>
                                <div class="text-dark fw-semibold" style="font-size: 0.76rem;">
                                    {{ $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Belum pernah' }}
                                </div>
                                <div class="text-secondary text-truncate" style="max-width: 200px; font-size: 0.7rem;" title="{{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada aktivitas' }}">
                                    {{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada catatan aktivitas' }}
                                </div>
                            </td>

                            {{-- Col 5: Aksi (Atur Izin + Dropdown Menu) --}}
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    {{-- Tombol Atur Izin --}}
                                    <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3 py-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#permissionsModal{{ $admin->id }}"
                                            style="font-size: 0.74rem;">
                                        <i class="bi bi-sliders2-vertical me-1"></i>
                                        <span>Atur Izin</span>
                                    </button>

                                    {{-- Dropdown Action Menu (···) --}}
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-action-more" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Aksi">
                                            <i class="bi bi-three-dots"></i>
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
                        <div class="avatar-box me-3" style="width: 42px; height: 42px; font-size: 1.1rem; background: rgba(255, 255, 255, 0.1); border-color: rgba(255, 255, 255, 0.2);">
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
                                    'icon' => 'bi-file-earmark-text',
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
    const filterChips = document.querySelectorAll('.filter-chip');
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

    if (modalTrigger) {
        const adminItem = modalTrigger.closest('.admin-item');
        if (adminItem) {
            const badgeEl = adminItem.querySelector('.perm-count-badge');
            if (badgeEl) {
                if (isSuper) {
                    badgeEl.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1 fw-bold';
                    badgeEl.innerHTML = '<i class="bi bi-award-fill me-1"></i> Super Admin (15/15)';
                } else {
                    badgeEl.className = 'badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-medium perm-count-badge';
                    badgeEl.innerHTML = `<i class="bi bi-shield-check text-warning me-1"></i> ${count}/15 Modul`;
                }
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
