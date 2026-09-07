@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
<style>
    .admin-card {
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.8);
        background: #ffffff;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        overflow: hidden;
        position: relative;
    }
    .admin-card:hover {
        border-color: rgba(203, 213, 225, 1);
        box-shadow: 0 12px 30px -6px rgba(15, 23, 42, 0.08), 0 4px 12px -2px rgba(15, 23, 42, 0.04);
        transform: translateY(-3px);
    }
    .admin-card.is-frozen {
        background: linear-gradient(180deg, #ffffff 0%, #fef2f2 100%);
        border-color: #fecaca;
    }

    /* Metric stat cards */
    .metric-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 18px;
        padding: 1.25rem 1.35rem;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.06);
    }
    .metric-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    /* Avatar styling with status rings */
    .avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }
    .avatar-circle {
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #f59e0b;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
        letter-spacing: -0.5px;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .avatar-circle.online-ring {
        border-color: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.2);
    }
    .avatar-circle.frozen-ring {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
    }
    .avatar-circle-modal {
        width: 54px;
        height: 54px;
        background: rgba(255, 255, 255, 0.15);
        color: #f59e0b;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.3rem;
        border: 2px solid rgba(255, 255, 255, 0.25);
    }

    /* Live pulse animation */
    .live-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #22c55e;
        display: inline-block;
        box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        animation: livePulse 2s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes livePulse {
        to {
            box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
        }
    }

    /* Action buttons toolbar */
    .admin-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(226, 232, 240, 0.9);
        background: #f8fafc;
        color: #64748b;
        transition: all 0.18s ease;
        font-size: 0.88rem;
    }
    .admin-action-btn:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transform: translateY(-1px);
    }
    .admin-action-btn.btn-danger-action:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #dc2626;
    }
    .admin-action-btn.btn-freeze:hover {
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
        font-size: 0.82rem;
        transition: all 0.15s ease;
        padding: 0.45rem 1rem;
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

    /* Progress bar */
    .perm-progress {
        height: 6px;
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

    /* Permission Card inside Modal */
    .icon-square {
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .permission-card {
        transition: all 0.2s ease-in-out;
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    .permission-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        transform: translateY(-1.5px);
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
        font-size: 0.78rem;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .btn-preset:hover {
        background: #ffffff;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }
    .btn-preset-danger {
        border: 1px solid #fee2e2;
        background: #fff5f5;
        color: #dc2626;
        font-size: 0.78rem;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .btn-preset-danger:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #991b1b;
    }

    /* Activity snippet box */
    .activity-snippet-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.65rem 0.85rem;
    }

    @media (max-width: 575.98px) {
        .admin-card-body {
            padding: 1.15rem !important;
        }
        .avatar-circle {
            width: 44px;
            height: 44px;
            font-size: 1.1rem;
            border-radius: 13px;
        }
    }
</style>

    {{-- Top Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.72rem; font-weight: 700;">
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
        <button class="btn btn-warning fw-bold px-4 py-2.5 rounded-pill shadow-sm text-dark hover-gold flex-shrink-0 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addAdminModal" style="font-size: 0.88rem;">
            <i class="bi bi-person-plus-fill fs-6"></i>
            <span>Tambah Staf Admin</span>
        </button>
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
                    <span class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Admin</span>
                    <div class="metric-icon-box" style="background: #f1f5f9; color: #0f172a;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-extrabold text-dark mb-0" style="font-size: 1.85rem; letter-spacing: -0.5px;">{{ $totalAdmins }}</h3>
                    <span class="text-secondary small">Akun</span>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.74rem;">
                    Terdaftar di sistem turnamen
                </div>
            </div>
        </div>

        {{-- Stat 2: Online Standby --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Online Sekarang</span>
                    <div class="metric-icon-box" style="background: #f0fdf4; color: #16a34a;">
                        <i class="bi bi-broadcast"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <h3 class="fw-extrabold text-dark mb-0" style="font-size: 1.85rem; letter-spacing: -0.5px;">{{ $onlineAdminsCount }}</h3>
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
                <div class="text-muted mt-1" style="font-size: 0.74rem;">
                    Aktif 5 menit terakhir
                </div>
            </div>
        </div>

        {{-- Stat 3: Frozen Admins --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100 {{ $frozenAdminsCount > 0 ? 'border-danger-subtle' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Dibekukan</span>
                    <div class="metric-icon-box" style="background: {{ $frozenAdminsCount > 0 ? '#fef2f2' : '#f8fafc' }}; color: {{ $frozenAdminsCount > 0 ? '#dc2626' : '#64748b' }};">
                        <i class="bi bi-snow"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-extrabold {{ $frozenAdminsCount > 0 ? 'text-danger' : 'text-dark' }} mb-0" style="font-size: 1.85rem; letter-spacing: -0.5px;">{{ $frozenAdminsCount }}</h3>
                    <span class="text-secondary small">Akun</span>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.74rem;">
                    {{ $frozenAdminsCount > 0 ? 'Akses masuk disuspend' : 'Tidak ada akun disuspend' }}
                </div>
            </div>
        </div>

        {{-- Stat 4: Super Admin / Access Level --}}
        <div class="col-6 col-lg-3">
            <div class="metric-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold" style="font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.5px;">Super Admin</span>
                    <div class="metric-icon-box" style="background: #fef3c7; color: #d97706;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-extrabold text-dark mb-0" style="font-size: 1.85rem; letter-spacing: -0.5px;">{{ $superAdminsCount }}</h3>
                    <span class="text-secondary small">Akun</span>
                </div>
                <div class="text-muted mt-1" style="font-size: 0.74rem;">
                    Memiliki 15/15 modul izin penuh
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filter Controls Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3 p-md-3">
            <div class="row g-2 align-items-center">
                {{-- Search Box --}}
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute text-muted" style="top: 50%; left: 14px; transform: translateY(-50%); font-size: 0.88rem;"></i>
                        <input type="text" id="adminSearchInput" class="form-control rounded-pill ps-5 pe-4 py-2 border-light-subtle shadow-none" placeholder="Cari nama, username, email..." style="font-size: 0.84rem;">
                        <button type="button" id="clearSearchBtn" class="btn btn-sm position-absolute text-muted d-none border-0 p-0" style="top: 50%; right: 14px; transform: translateY(-50%);" title="Hapus pencarian">
                            <i class="bi bi-x-circle-fill"></i>
                        </button>
                    </div>
                </div>

                {{-- Filter Chips --}}
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="d-flex align-items-center gap-1.5 flex-wrap justify-content-md-end">
                        <button type="button" class="btn btn-sm filter-pill active rounded-pill px-3 py-1.5" data-filter="all">
                            Semua <span class="badge bg-secondary-subtle text-secondary rounded-pill ms-1 count-badge">{{ $totalAdmins }}</span>
                        </button>
                        <button type="button" class="btn btn-sm filter-pill rounded-pill px-3 py-1.5" data-filter="online">
                            <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.45rem;"></i>
                            Online <span class="badge bg-success-subtle text-success rounded-pill ms-1 count-badge">{{ $onlineAdminsCount }}</span>
                        </button>
                        @if($frozenAdminsCount > 0)
                        <button type="button" class="btn btn-sm filter-pill rounded-pill px-3 py-1.5" data-filter="frozen">
                            <i class="bi bi-snow text-danger me-1"></i>
                            Dibekukan <span class="badge bg-danger-subtle text-danger rounded-pill ms-1 count-badge">{{ $frozenAdminsCount }}</span>
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm filter-pill rounded-pill px-3 py-1.5" data-filter="superadmin">
                            <i class="bi bi-award text-warning me-1"></i>
                            Super Admin <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill ms-1 count-badge">{{ $superAdminsCount }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin Cards Grid --}}
    <div class="row g-3" id="adminGridContainer">
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
                <div class="admin-card h-100 {{ !$isActive ? 'is-frozen' : '' }}">
                    <div class="admin-card-body p-4">
                        {{-- Top Header: Avatar + Info + Quick Actions --}}
                        <div class="d-flex align-items-start justify-content-between mb-3 gap-2">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <div class="avatar-wrapper">
                                    <div class="avatar-circle {{ !$isActive ? 'frozen-ring opacity-75' : ($isOnline ? 'online-ring' : '') }}">
                                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                        <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 0.95rem;" title="{{ $admin->name }}">
                                            {{ $admin->name }}
                                        </h6>
                                        @if($isCurrentUser)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                <i class="bi bi-person-check-fill me-0.5"></i> Anda
                                            </span>
                                        @endif
                                    </div>

                                    <div class="d-flex align-items-center gap-2 mt-0.5 flex-wrap">
                                        <span class="text-secondary text-truncate" style="font-size: 0.78rem;">
                                            {{ '@' . $admin->username }}
                                        </span>
                                        @if(!$isActive)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                <i class="bi bi-snow me-0.5"></i> Dibekukan
                                            </span>
                                        @elseif($isOnline)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                <span class="live-pulse-dot me-1"></span> Online
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">
                                                Offline
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons Toolbar --}}
                            <div class="d-flex gap-1 align-items-center flex-shrink-0">
                                @if(!$isCurrentUser)
                                    {{-- Force Logout Button --}}
                                    <button type="button" class="admin-action-btn" title="Putus Sesi Login (Force Logout)" onclick="forceLogoutAdmin({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                                        <i class="bi bi-box-arrow-right text-warning"></i>
                                    </button>

                                    {{-- Freeze / Unfreeze Toggle Button --}}
                                    <button type="button" class="admin-action-btn {{ !$isActive ? 'btn-danger-action bg-danger-subtle text-danger' : 'btn-freeze' }}" 
                                            title="{{ $isActive ? 'Bekukan Akun Sementara' : 'Aktifkan Kembali Akun' }}" 
                                            onclick="toggleAdminStatus({{ $admin->id }}, '{{ addslashes($admin->name) }}', {{ $isActive ? 'true' : 'false' }})">
                                        <i class="bi {{ $isActive ? 'bi-snow text-info' : 'bi-sun-fill text-warning' }}"></i>
                                    </button>
                                @endif

                                {{-- Edit Button --}}
                                <button type="button" class="admin-action-btn" title="Edit Data Akun" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                {{-- Delete Button (Not for self) --}}
                                @if(!$isCurrentUser)
                                    <a href="{{ route('admin.manage.delete', $admin->id) }}" 
                                       class="admin-action-btn btn-danger-action" title="Hapus Akun Permanen"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $admin->username }}? Hapus akun tidak dapat dibatalkan.');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Email Row --}}
                        <div class="d-flex align-items-center gap-2 mb-3 text-secondary" style="font-size: 0.8rem;">
                            <i class="bi bi-envelope text-muted"></i>
                            <span class="text-truncate">{{ $admin->email }}</span>
                        </div>

                        {{-- Last Seen & Latest Activity Snippet Box --}}
                        <div class="activity-snippet-box mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-secondary fw-semibold" style="font-size: 0.72rem;">
                                    <i class="bi bi-clock-history me-1 text-warning"></i>Terakhir Aktif:
                                </span>
                                <span class="text-dark fw-bold" style="font-size: 0.72rem;">
                                    {{ $admin->last_seen_at ? $admin->last_seen_at->diffForHumans() : 'Belum pernah online' }}
                                </span>
                            </div>
                            <div class="text-secondary text-truncate" style="font-size: 0.72rem;" title="{{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada catatan aktivitas' }}">
                                <i class="bi bi-activity me-1 text-primary"></i>
                                <span class="fw-semibold text-dark">Aksi:</span> {{ $admin->latestActivity ? $admin->latestActivity->activity : 'Belum ada catatan aktivitas' }}
                            </div>
                        </div>

                        {{-- Permission Progress Section --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1.5">
                                <div class="d-flex align-items-center gap-1.5">
                                    <i class="bi bi-shield-check text-muted" style="font-size: 0.75rem;"></i>
                                    <span class="text-secondary fw-semibold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Otorisasi Modul</span>
                                </div>
                                <span class="perm-count fw-bold" style="font-size: 0.78rem; color: {{ $isSuperAdmin ? '#16a34a' : ($activeCount >= 8 ? '#d97706' : '#64748b') }};">
                                    {{ $activeCount }}/15 Modul
                                </span>
                            </div>
                            <div class="perm-progress">
                                <div class="perm-progress-bar {{ $isSuperAdmin ? 'is-full' : '' }}" style="width: {{ $permPercent }}%;"></div>
                            </div>
                        </div>

                        {{-- Footer: Registration Date + Atur Izin Trigger --}}
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid #f1f5f9;">
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $admin->created_at->format('d M Y') }}
                            </span>
                            <button class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3 py-1 text-dark d-inline-flex align-items-center gap-1.5" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#permissionsModal{{ $admin->id }}"
                                    style="font-size: 0.76rem; border-width: 1.5px;">
                                <i class="bi bi-sliders2-vertical"></i>
                                <span>Atur Izin</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permissions Management Modal --}}
            <div class="modal fade" id="permissionsModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                        {{-- Modal Header with Dark Gradient --}}
                        <div class="modal-header border-0 text-white px-4 py-3 py-md-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle-modal me-3">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                                <div class="text-start">
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="modal-title fw-bold text-white mb-0">{{ $admin->name }}</h5>
                                        <span class="badge bg-warning text-dark fw-bold rounded-pill px-2.5 py-0.5 modal-perm-badge" style="font-size: 0.7rem;">
                                            {{ $activeCount }}/15 Modul Aktif
                                        </span>
                                    </div>
                                    <p class="text-white-50 mb-0 small">{{ '@' . $admin->username }} &bull; {{ $admin->email }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="modal-body p-3 p-md-4 bg-light" style="max-height: 70vh; overflow-y: auto;">
                            {{-- Quick Role Preset Bar --}}
                            <div class="bg-white rounded-3 border border-light-subtle p-3 mb-3 shadow-sm">
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
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1.5" 
                                            onclick="applyPreset({{ $admin->id }}, 'wasit', 'Wasit Turnamen')">
                                        <i class="bi bi-trophy text-warning me-1"></i> Wasit Turnamen
                                    </button>
                                    <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1.5" 
                                            onclick="applyPreset({{ $admin->id }}, 'bendahara', 'Bendahara / Keuangan')">
                                        <i class="bi bi-cash-stack text-success me-1"></i> Bendahara
                                    </button>
                                    <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1.5" 
                                            onclick="applyPreset({{ $admin->id }}, 'cs', 'Customer Care / LO')">
                                        <i class="bi bi-headset text-info me-1"></i> Customer Care
                                    </button>
                                    <button type="button" class="btn btn-sm btn-preset rounded-pill px-3 py-1.5" 
                                            onclick="applyPreset({{ $admin->id }}, 'super', 'Super Admin')">
                                        <i class="bi bi-award-fill text-primary me-1"></i> Pilih Semua (Super Admin)
                                    </button>
                                    <button type="button" class="btn btn-sm btn-preset-danger rounded-pill px-3 py-1.5" 
                                            onclick="applyPreset({{ $admin->id }}, 'none', 'Kosongkan')">
                                        <i class="bi bi-x-circle text-danger me-1"></i> Kosongkan
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1.5 text-secondary small mb-3">
                                <i class="bi bi-info-circle text-primary"></i> 
                                <span>Setiap switch yang diubah langsung disimpan realtime ke server.</span>
                            </div>

                            {{-- 15 Permissions Grid --}}
                            <div class="row g-3">
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
                                        <div class="permission-card rounded-3 p-3 h-100 d-flex justify-content-between align-items-start">
                                            <div class="d-flex align-items-start me-3">
                                                <div class="icon-square me-3" style="background-color: {{ $pInfo['bg'] }}; color: {{ $pInfo['color'] }}; flex-shrink: 0;">
                                                    <i class="bi {{ $pInfo['icon'] }}"></i>
                                                </div>
                                                <div class="text-start">
                                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">{{ $pInfo['label'] }}</h6>
                                                    <p class="text-secondary mb-0" style="font-size: 0.74rem; line-height: 1.4;">{{ $pInfo['desc'] }}</p>
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
                                                       style="width: 2.3em; height: 1.15em; cursor: pointer;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="modal-footer border-0 bg-white px-4 py-3 justify-content-between">
                            <span class="text-muted small">
                                <i class="bi bi-shield-lock me-1 text-warning"></i> Simpan otomatis setiap perubahan switch
                            </span>
                            <button type="button" class="btn btn-dark rounded-pill px-4 fw-semibold shadow-sm text-white" data-bs-dismiss="modal">
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
                                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-warning me-1"></i> Edit Akun Admin</h5>
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
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark hover-gold">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body py-5 text-center text-secondary">
                        <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                        Belum ada staf admin yang ditambahkan ke sistem.
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Empty Search Results Feedback --}}
    <div id="noAdminResults" class="card border-0 shadow-sm rounded-4 bg-white my-4 d-none">
        <div class="card-body py-5 text-center text-secondary">
            <i class="bi bi-search fs-1 text-muted mb-3 d-block opacity-50"></i>
            <h5 class="fw-bold text-dark mb-1">Tidak Ada Admin Ditemukan</h5>
            <p class="text-secondary small mb-3">Tidak ada akun admin yang cocok dengan kata kunci atau filter yang dipilih.</p>
            <button type="button" id="resetFiltersBtn" class="btn btn-outline-dark rounded-pill px-4 btn-sm fw-semibold">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Pencarian &amp; Filter
            </button>
        </div>
    </div>
</div>

{{-- Add Admin Modal --}}
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <form action="{{ route('admin.manage.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill text-warning me-1"></i> Tambah Akun Admin Baru</h5>
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
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark hover-gold">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast Notification Container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1300;">
    <div id="permissionToast" class="toast align-items-center border-0 text-white rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center py-3 px-3">
                <i class="bi me-2 fs-5" id="toastIcon"></i>
                <span id="toastMessage" class="fw-semibold" style="font-size: 0.86rem;"></span>
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
});

// Helper to update card visual after permission changes
function updateCardPermissionsVisual(adminId, permissions) {
    const modalTrigger = document.querySelector(`[data-bs-target="#permissionsModal${adminId}"]`);
    if (!modalTrigger || !permissions) return;

    const adminCard = modalTrigger.closest('.admin-card');
    const adminItem = modalTrigger.closest('.admin-item');
    if (!adminCard) return;

    const count = permissions.length;
    const percent = Math.round((count / 15) * 100);

    // Update progress bar
    const barEl = adminCard.querySelector('.perm-progress-bar');
    if (barEl) {
        barEl.style.width = `${percent}%`;
        if (count >= 15) {
            barEl.classList.add('is-full');
        } else {
            barEl.classList.remove('is-full');
        }
    }

    // Update count label
    const countEl = adminCard.querySelector('.perm-count');
    if (countEl) {
        countEl.textContent = `${count}/15 Modul`;
        countEl.style.color = count >= 15 ? '#16a34a' : (count >= 8 ? '#d97706' : '#64748b');
    }

    // Update modal badge
    const modal = document.getElementById(`permissionsModal${adminId}`);
    if (modal) {
        const modalBadge = modal.querySelector('.modal-perm-badge');
        if (modalBadge) {
            modalBadge.textContent = `${count}/15 Modul Aktif`;
        }
    }

    // Update data-superadmin attribute
    if (adminItem) {
        adminItem.dataset.superadmin = count >= 15 ? '1' : '0';
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
