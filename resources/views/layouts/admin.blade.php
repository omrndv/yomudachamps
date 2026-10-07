<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Yomuda Championship</title>
    
    <!-- PWA Support -->
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="YMD Admin">
    <link rel="apple-touch-icon" href="/images/logo-yomuda.png">
    
    <!-- Speed Optimization: DNS Prefetch & Preconnect for Admin Assets -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Desktop Sidebar Stylings */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
            color: #ffffff;
            z-index: 1000;
            padding: 24px 14px 16px 14px;
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            flex-direction: column;
            overflow: visible; /* Penting: visible agar tombol toggle bulat tidak terpotong separuh */
        }

        .sidebar-header {
            padding: 10px 8px 30px 8px;
            text-align: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-brand {
            font-size: 1.25rem;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .brand-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0f172a;
            border-radius: 8px;
            font-size: 1.1rem;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
            transition: all 0.3s ease;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
            padding-bottom: 12px;
            scrollbar-width: thin;
            scrollbar-color: rgba(245, 158, 11, 0.4) transparent;
        }

        .sidebar-footer {
            flex-shrink: 0;
            margin-top: auto;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.85) 0%, #020617 100%);
            padding: 12px 14px 14px 14px;
            z-index: 10;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: padding 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.4);
            border-radius: 10px;
        }
        .sidebar-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.7);
        }

        .nav-pills .nav-link {
            color: #94a3b8;
            padding: 12px 16px;
            margin-bottom: 6px;
            font-weight: 500;
            border-radius: 12px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            border: 1px solid transparent;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
        }

        .nav-pills .nav-link i {
            font-size: 1.15rem;
            margin-right: 12px;
            transition: margin 0.2s ease, font-size 0.2s ease;
        }

        .nav-pills .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.04);
            transform: translateX(4px);
        }

        .nav-pills .nav-link.active {
            color: #ffffff !important;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3) !important;
            font-weight: 600;
        }

        /* Logout Pill Button Styling */
        .sidebar-footer .nav-link-logout {
            color: #f87171 !important;
            background: rgba(239, 68, 68, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.15);
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.88rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            text-decoration: none;
            width: 100%;
        }

        .sidebar-footer .nav-link-logout i {
            font-size: 1.15rem;
            margin-right: 12px;
            transition: transform 0.2s ease;
        }

        .sidebar-footer .nav-link-logout:hover {
            color: #ffffff !important;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            border-color: transparent !important;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important;
            transform: translateY(-1px);
        }

        .sidebar-footer .nav-link-logout:hover i {
            transform: translateX(3px);
        }

        .main-content {
            margin-left: 260px;
            padding: 32px;
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-custom {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        /* Mobile Header and Drawer */
        .navbar-mobile {
            background: #0f172a;
            padding: 14px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
        }

        .offcanvas {
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.06) !important;
        }

        /* Collapsed Sidebar State Rules (Desktop Only) */
        @media (min-width: 992px) {
            body.sidebar-collapsed .sidebar {
                width: 72px;
                padding: 24px 8px 16px 8px;
            }
            body.sidebar-collapsed .sidebar-header {
                padding-bottom: 20px;
            }
            body.sidebar-collapsed .sidebar-brand span:not(.brand-icon) {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar .nav-link {
                padding: 12px 0 !important;
                justify-content: center !important;
                margin-bottom: 8px;
            }
            body.sidebar-collapsed .sidebar .nav-link:hover {
                transform: none !important;
                background: rgba(255, 255, 255, 0.08) !important;
            }
            body.sidebar-collapsed .sidebar .nav-link i {
                margin-right: 0 !important;
                font-size: 1.3rem;
            }
            body.sidebar-collapsed .sidebar .nav-link span {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar small.text-uppercase {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar-footer {
                padding: 12px 8px 14px 8px !important;
            }
            body.sidebar-collapsed .sidebar-user-card {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar-footer .nav-link-logout {
                padding: 12px 0 !important;
                justify-content: center !important;
            }
            body.sidebar-collapsed .sidebar-footer .nav-link-logout i {
                margin-right: 0 !important;
                font-size: 1.3rem;
            }
            body.sidebar-collapsed .sidebar-footer .nav-link-logout span {
                display: none !important;
            }
            body.sidebar-collapsed .main-content {
                margin-left: 72px;
            }
        }

        /* Tablet landscape (992-1199px): sidebar visible, tighter padding */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .sidebar {
                width: 240px;
                padding: 20px 12px;
            }
            .main-content {
                margin-left: 240px;
                padding: 24px 20px;
            }
            .nav-pills .nav-link {
                padding: 10px 14px;
                font-size: 0.85rem;
            }
            body.sidebar-collapsed .sidebar {
                width: 72px;
            }
            body.sidebar-collapsed .main-content {
                margin-left: 72px;
            }
        }

        /* Mobile & small tablets: hide sidebar, show offcanvas */
        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 20px 16px;
            }
        }

        /* Extra small phones */
        @media (max-width: 575.98px) {
            .main-content {
                padding: 16px 12px;
            }
        }

        /* Sidebar Backup item special override */
        .sidebar-nav .nav-link.backup-link {
            color: #ef4444 !important;
        }
        .sidebar-nav .nav-link.backup-link:hover {
            background-color: rgba(239, 68, 68, 0.08) !important;
            color: #dc2626 !important;
            transform: translateX(4px);
        }

        /* Floating Toggle Button */
        #toggleSidebar {
            position: absolute;
            right: -13px;
            top: 28px;
            width: 26px;
            height: 26px;
            z-index: 1050;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0f172a;
            border: 2px solid #0f172a !important;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease;
        }
        #toggleSidebar:hover {
            color: #ffffff;
            transform: scale(1.15);
            box-shadow: 0 4px 14px rgba(245, 158, 11, 0.5);
        }
        #toggleSidebar:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body>
    <script>
        // Check sidebar state immediately to prevent layout shift/flash
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>
    {{-- Mobile Header --}}
    <nav class="navbar navbar-mobile d-lg-none shadow-sm sticky-top">
        <div class="container-fluid p-0">
            <div class="d-flex align-items-center gap-2">
                <span class="brand-icon">
                    <i class="bi bi-lightning-charge-fill"></i>
                </span>
                <span class="navbar-brand fw-bold text-white m-0" style="font-size: 1.15rem; letter-spacing: 0.5px;">
                    YOMUDA <span class="fw-light text-white-50">ADM</span>
                </span>
            </div>
            <button class="btn btn-outline-warning border-0 p-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
                <i class="bi bi-list fs-2 text-white"></i>
            </button>
        </div>
    </nav>

    {{-- Desktop Sidebar --}}
    <aside class="sidebar d-none d-lg-block">
        {{-- Floating Toggle Button --}}
        <button type="button" id="toggleSidebar" title="Ciutkan / Lebarkan Sidebar" aria-label="Toggle Sidebar">
            <i class="bi bi-chevron-left" id="toggleIcon" style="font-size: 0.75rem;"></i>
        </button>

        <div class="sidebar-header">
            <div class="sidebar-brand fw-bold d-flex align-items-center justify-content-center gap-2">
                <span class="brand-icon">
                    <i class="bi bi-lightning-charge-fill"></i>
                </span>
                <span>YOMUDA <span class="fw-light text-white-50">ADM</span></span>
            </div>
        </div>
        
        <div class="sidebar-nav nav nav-pills">
            <small class="text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.65rem; letter-spacing: 1.2px; padding-left: 16px;">Menu Utama</small>
            
            <a href="{{ route('admin.dashboard.home') }}" class="nav-link {{ request()->routeIs('admin.dashboard.home') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> <span>Dashboard</span>
            </a>

            @if(Auth::check() && Auth::user()->hasPermission('seasons'))
            <a href="{{ route('admin.seasons') }}" class="nav-link {{ request()->routeIs('admin.seasons') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-trophy"></i> <span>Daftar Season</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('teams'))
            <a href="{{ route('admin.teams') }}" class="nav-link {{ request()->routeIs('admin.teams') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> <span>Daftar Team</span>
            </a>
            @endif
            
            @if(Auth::check() && Auth::user()->hasPermission('payments'))
            <a href="{{ route('admin.manual-payment') }}" class="nav-link {{ request()->routeIs('admin.manual-payment') ? 'active' : '' }}">
                <i class="bi bi-qr-code-scan"></i> <span>Pembayaran Manual</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('notes'))
            <a href="{{ route('admin.notes.index') }}" class="nav-link {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
                <i class="bi bi-sticky"></i> <span>Catatan Admin</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('settings'))
            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> <span>Pengaturan</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('gateway_notifications'))
            <a href="{{ route('admin.settings.gateway_notifications') }}" class="nav-link {{ request()->routeIs('admin.settings.gateway_notifications') ? 'active' : '' }}">
                <i class="bi bi-bell-fill text-warning"></i> <span>Notifikasi Gateway</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('faqs'))
            <a href="{{ route('admin.faqs.index') }}" class="nav-link {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                <i class="bi bi-question-circle"></i> <span>Kelola FAQ</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('activity_log'))
            <a href="{{ route('admin.activity-log') }}" class="nav-link {{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> <span>Log Aktivitas</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('manage'))
            <a href="{{ route('admin.manage') }}" class="nav-link {{ request()->routeIs('admin.manage') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> <span>Kelola Admin</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('laravel_logs'))
            <a href="{{ route('admin.system-logs') }}" class="nav-link {{ request()->routeIs('admin.system-logs') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> <span>Log Laravel</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('storage'))
            <a href="{{ route('admin.storage') }}" class="nav-link {{ request()->routeIs('admin.storage') ? 'active' : '' }}">
                <i class="bi bi-hdd-network"></i> <span>Kelola Penyimpanan</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('backup'))
            <a href="{{ route('admin.backup') }}" class="nav-link backup-link {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                <i class="bi bi-database-down"></i> <span>Backup Database</span>
            </a>
            @endif
        </div>

        <div class="sidebar-footer">
            @if(Auth::check())
            <div class="sidebar-user-card d-flex align-items-center gap-2 mb-2 px-1 py-1 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.06);">
                <div class="position-relative flex-shrink-0">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle border border-warning" style="width: 34px; height: 34px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.85rem;">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                    @if(Auth::user()->google_id)
                        <span class="position-absolute bottom-0 end-0 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 14px; height: 14px; transform: translate(2px, 2px);" title="Terhubung dengan Google">
                            <i class="bi bi-google text-danger" style="font-size: 9px;"></i>
                        </span>
                    @endif
                </div>
                <div class="min-w-0 flex-grow-1 text-truncate">
                    <div class="fw-bold text-white small text-truncate d-flex align-items-center gap-1" style="font-size: 0.8rem;" title="{{ Auth::user()->name }}">
                        <span class="text-truncate">{{ Auth::user()->name }}</span>
                        @if(Auth::user()->role === 'superadmin')
                            <span class="badge bg-warning text-dark px-1 py-0" style="font-size: 0.6rem;">SA</span>
                        @endif
                    </div>
                    <div class="text-secondary text-truncate" style="font-size: 0.68rem;" title="{{ Auth::user()->email ?? ('@' . Auth::user()->username) }}">
                        {{ Auth::user()->email ?? ('@' . Auth::user()->username) }}
                    </div>
                </div>
            </div>
            @endif

            <a href="{{ route('admin.logout') }}" class="nav-link-logout">
                <i class="bi bi-box-arrow-right"></i> <span>Keluar</span>
            </a>
        </div>
    </aside>

    {{-- Mobile Sidebar Drawer --}}
    <div class="offcanvas offcanvas-start text-white" tabindex="-1" id="sidebarMobile" style="width: 280px;">
        <div class="offcanvas-header border-bottom border-secondary border-opacity-35 p-4">
            <div class="d-flex align-items-center gap-2">
                <span class="brand-icon">
                    <i class="bi bi-lightning-charge-fill"></i>
                </span>
                <h5 class="offcanvas-title fw-bold text-white m-0" style="letter-spacing: 0.5px;">YOMUDA ADM</h5>
            </div>
            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4 d-flex flex-column h-100">
            <div class="nav nav-pills flex-column" style="flex: 1 1 auto; overflow-y: auto; scrollbar-width: none;">
                <small class="text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.65rem; letter-spacing: 1.2px; padding-left: 16px;">Menu Utama</small>
                
                <a href="{{ route('admin.dashboard.home') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.dashboard.home') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2 me-2"></i> <span>Dashboard</span>
                </a>

                @if(Auth::check() && Auth::user()->hasPermission('seasons'))
                <a href="{{ route('admin.seasons') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.seasons') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-trophy me-2"></i> <span>Daftar Season</span>
                </a>
                @endif

                @if(Auth::check() && Auth::user()->hasPermission('teams'))
                <a href="{{ route('admin.teams') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.teams') ? 'active' : '' }}">
                    <i class="bi bi-people-fill me-2"></i> <span>Daftar Team</span>
                </a>
                @endif

                @if(Auth::check() && Auth::user()->hasPermission('payments'))
                <a href="{{ route('admin.manual-payment') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.manual-payment') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan me-2"></i> <span>Pembayaran Manual</span>
                </a>
                @endif

                @if(Auth::check() && Auth::user()->hasPermission('notes'))
                <a href="{{ route('admin.notes.index') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
                    <i class="bi bi-sticky me-2"></i> <span>Catatan Admin</span>
                </a>
                @endif

                 @if(Auth::check() && Auth::user()->hasPermission('settings'))
                 <a href="{{ route('admin.settings') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                     <i class="bi bi-gear me-2"></i> <span>Pengaturan</span>
                 </a>
                 @endif

                 @if(Auth::check() && Auth::user()->hasPermission('gateway_notifications'))
                 <a href="{{ route('admin.settings.gateway_notifications') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.settings.gateway_notifications') ? 'active' : '' }}">
                     <i class="bi bi-bell-fill text-warning me-2"></i> <span>Notifikasi Gateway</span>
                 </a>
                 @endif

                 @if(Auth::check() && Auth::user()->hasPermission('faqs'))
                 <a href="{{ route('admin.faqs.index') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.faqs.*') ? 'active' : '' }}">
                     <i class="bi bi-question-circle me-2"></i> <span>Kelola FAQ</span>
                 </a>
                 @endif

                 @if(Auth::check() && Auth::user()->hasPermission('activity_log'))
                 <a href="{{ route('admin.activity-log') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
                     <i class="bi bi-clock-history me-2"></i> <span>Log Aktivitas</span>
                 </a>
                 @endif

                 @if(Auth::check() && Auth::user()->hasPermission('manage'))
                 <a href="{{ route('admin.manage') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.manage') ? 'active' : '' }}">
                     <i class="bi bi-person-gear me-2"></i> <span>Kelola Admin</span>
                 </a>
                 @endif

                 @if(Auth::check() && Auth::user()->hasPermission('laravel_logs'))
                 <a href="{{ route('admin.system-logs') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.system-logs') ? 'active' : '' }}">
                     <i class="bi bi-file-earmark-text me-2"></i> <span>Log Laravel</span>
                 </a>
                 @endif

                 @if(Auth::check() && Auth::user()->hasPermission('storage'))
                 <a href="{{ route('admin.storage') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.storage') ? 'active' : '' }}">
                     <i class="bi bi-hdd-network me-2"></i> <span>Kelola Penyimpanan</span>
                 </a>
                 @endif

                @if(Auth::check() && Auth::user()->hasPermission('backup'))
                <a href="{{ route('admin.backup') }}" class="nav-link backup-link mb-2 {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                    <i class="bi bi-database-down me-2"></i> <span>Backup Database</span>
                </a>
                @endif
            </div>

            <div class="mt-auto pt-4 w-100 bg-transparent shrink-0">
                <hr class="border-secondary opacity-25 mb-3">
                @if(Auth::check())
                <div class="d-flex align-items-center gap-2 mb-3 px-2 py-2 rounded-3" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.06);">
                    <div class="position-relative flex-shrink-0">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="rounded-circle border border-warning" style="width: 38px; height: 38px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-warning text-dark fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                        @if(Auth::user()->google_id)
                            <span class="position-absolute bottom-0 end-0 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 15px; height: 15px; transform: translate(2px, 2px);" title="Terhubung dengan Google">
                                <i class="bi bi-google text-danger" style="font-size: 10px;"></i>
                            </span>
                        @endif
                    </div>
                    <div class="min-w-0 flex-grow-1 text-truncate">
                        <div class="fw-bold text-white small text-truncate d-flex align-items-center gap-1" title="{{ Auth::user()->name }}">
                            <span class="text-truncate">{{ Auth::user()->name }}</span>
                            @if(Auth::user()->role === 'superadmin')
                                <span class="badge bg-warning text-dark px-1 py-0" style="font-size: 0.6rem;">SA</span>
                            @endif
                        </div>
                        <div class="text-secondary text-truncate" style="font-size: 0.68rem;" title="{{ Auth::user()->email ?? ('@' . Auth::user()->username) }}">
                            {{ Auth::user()->email ?? ('@' . Auth::user()->username) }}
                        </div>
                    </div>
                </div>
                @endif
                <a href="{{ route('admin.logout') }}" class="nav-link-logout" style="color: #f87171 !important; background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.18); padding: 12px 16px; border-radius: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px; text-decoration: none;">
                    <i class="bi bi-box-arrow-right fs-5"></i> <span>Keluar</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Main Page Content --}}
    <main class="main-content">
        @yield('content')
    </main>
    
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    {{-- Sidebar Toggle JS Logic --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // ----------------------------------------------------
            // Global Session Notifications (SweetAlert2)
            // ----------------------------------------------------
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "{{ session('success') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4500,
                    timerProgressBar: true,
                    background: '#18181b',
                    color: '#ffffff',
                    iconColor: '#22c55e'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Perhatian!',
                    text: "{{ session('error') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5500,
                    timerProgressBar: true,
                    background: '#18181b',
                    color: '#ffffff',
                    iconColor: '#ef4444'
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan!',
                    text: "{{ session('warning') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    background: '#18181b',
                    color: '#ffffff',
                    iconColor: '#f59e0b'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Informasi',
                    text: "{{ session('info') }}",
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    background: '#18181b',
                    color: '#ffffff',
                    iconColor: '#3b82f6'
                });
            @endif

            const toggleBtn = document.getElementById('toggleSidebar');
            const toggleIcon = document.getElementById('toggleIcon');
            
            // Set correct icon if sidebar is already collapsed
            if (document.body.classList.contains('sidebar-collapsed')) {
                if (toggleIcon) {
                    toggleIcon.classList.replace('bi-chevron-left', 'bi-chevron-right');
                }
            }
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                    
                    if (toggleIcon) {
                        if (isCollapsed) {
                            toggleIcon.classList.replace('bi-chevron-left', 'bi-chevron-right');
                        } else {
                            toggleIcon.classList.replace('bi-chevron-right', 'bi-chevron-left');
                        }
                    }
                });
            }

            // ----------------------------------------------------
            // Global Live Payment Notifications for Admin
            // ----------------------------------------------------
            let lastPaidTeamId = localStorage.getItem('last_paid_team_id');

            function playSuccessPaymentSound() {
                try {
                    const context = new (window.AudioContext || window.webkitAudioContext)();
                    
                    // Cash register style arpeggio chime (C6 -> E6 -> G6 -> C7)
                    const osc1 = context.createOscillator();
                    const gain1 = context.createGain();
                    
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(1046.50, context.currentTime); // C6
                    osc1.frequency.setValueAtTime(1318.51, context.currentTime + 0.08); // E6
                    osc1.frequency.setValueAtTime(1567.98, context.currentTime + 0.16); // G6
                    osc1.frequency.setValueAtTime(2093.00, context.currentTime + 0.24); // C7
                    
                    gain1.gain.setValueAtTime(0.12, context.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.6);
                    
                    osc1.connect(gain1);
                    gain1.connect(context.destination);
                    
                    osc1.start();
                    osc1.stop(context.currentTime + 0.6);
                } catch (e) {
                    console.log("AudioContext failed:", e);
                }
            }

            function playMatchReportSound() {
                try {
                    const context = new (window.AudioContext || window.webkitAudioContext)();
                    const osc1 = context.createOscillator();
                    const gain1 = context.createGain();
                    osc1.type = 'sawtooth';
                    osc1.frequency.setValueAtTime(880.00, context.currentTime); // A5
                    osc1.frequency.setValueAtTime(659.25, context.currentTime + 0.15); // E5
                    osc1.frequency.setValueAtTime(880.00, context.currentTime + 0.3); // A5
                    gain1.gain.setValueAtTime(0.15, context.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.5);
                    osc1.connect(gain1);
                    gain1.connect(context.destination);
                    osc1.start();
                    osc1.stop(context.currentTime + 0.5);
                } catch (e) {}
            }

            function playLiveChatSound() {
                try {
                    const context = new (window.AudioContext || window.webkitAudioContext)();
                    const osc1 = context.createOscillator();
                    const gain1 = context.createGain();
                    osc1.type = 'triangle';
                    osc1.frequency.setValueAtTime(587.33, context.currentTime); // D5
                    osc1.frequency.setValueAtTime(1174.66, context.currentTime + 0.08); // D6
                    gain1.gain.setValueAtTime(0.15, context.currentTime);
                    gain1.gain.exponentialRampToValueAtTime(0.001, context.currentTime + 0.3);
                    osc1.connect(gain1);
                    gain1.connect(context.destination);
                    osc1.start();
                    osc1.stop(context.currentTime + 0.3);
                } catch (e) {}
            }

            let lastReportId = localStorage.getItem('last_report_id');
            let lastChatId = localStorage.getItem('last_chat_id');

            let isCheckingPayments = false;

            function checkForNewPayments() {
                if (isCheckingPayments) return; // Cegah request bertumpuk jika request sebelumnya masih berjalan
                isCheckingPayments = true;

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 6000); // Batasi timeout 6 detik

                fetch("{{ route('admin.payments.check-new') }}", { signal: controller.signal })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            // 1. Cek pembayaran sukses
                            if (res.latest_paid) {
                                const team = res.latest_paid;
                                if (!lastPaidTeamId) {
                                    localStorage.setItem('last_paid_team_id', team.id);
                                    lastPaidTeamId = team.id;
                                } else if (team.id > lastPaidTeamId) {
                                    localStorage.setItem('last_paid_team_id', team.id);
                                    lastPaidTeamId = team.id;
                                    playSuccessPaymentSound();
                                    Swal.fire({
                                        title: '💸 Pembayaran Sukses!',
                                        html: `Tim <b>${team.name}</b> baru saja melunasi pendaftaran.<br><small class="text-secondary">Trx ID: ${team.trx_id}</small>`,
                                        icon: 'success',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 5000,
                                        timerProgressBar: true,
                                        background: '#0f172a',
                                        color: '#ffffff'
                                    });
                                }
                            }

                            // 2. Cek laporan laga masuk (Lapor Win)
                            if (res.latest_pending_report) {
                                const rpt = res.latest_pending_report;
                                if (!lastReportId) {
                                    localStorage.setItem('last_report_id', rpt.id);
                                    lastReportId = rpt.id;
                                } else if (rpt.id > lastReportId) {
                                    localStorage.setItem('last_report_id', rpt.id);
                                    lastReportId = rpt.id;
                                    playMatchReportSound();
                                    
                                    // Peringatan keras agar admin langsung sadar
                                    Swal.fire({
                                        title: '🚨 LAPORAN LAGA MASUK!',
                                        html: `Tim <b>${rpt.team_name}</b> melaporkan skor kemenangan <b>${rpt.scores}</b> di turnamen <b>${rpt.season_name}</b>.<br><br><span class="text-warning fw-bold">Segera cek bukti screenshot & setujui pemenang!</span>`,
                                        icon: 'warning',
                                        showCancelButton: false,
                                        confirmButtonText: 'Cek Laporan Laga',
                                        confirmButtonColor: '#f59e0b',
                                        background: '#1e1e24',
                                        color: '#ffffff'
                                    });
                                }
                            }

                            // 3. Cek chat masuk
                            if (res.latest_unread_chat) {
                                const chat = res.latest_unread_chat;
                                if (!lastChatId) {
                                    localStorage.setItem('last_chat_id', chat.id);
                                    lastChatId = chat.id;
                                } else if (chat.id > lastChatId) {
                                    localStorage.setItem('last_chat_id', chat.id);
                                    lastChatId = chat.id;
                                    playLiveChatSound();
                                    Swal.fire({
                                        title: '💬 Live Chat Baru!',
                                        html: `Dari <b>${chat.sender}</b>:<br>"${chat.message}"`,
                                        icon: 'info',
                                        toast: true,
                                        position: 'top-end',
                                        showConfirmButton: false,
                                        timer: 6000,
                                        timerProgressBar: true,
                                        background: '#18181b',
                                        color: '#ffffff'
                                    });
                                }
                            }
                        }
                    })
                    .catch(err => {
                        if (err.name !== 'AbortError') {
                            console.log("New activities check issue:", err);
                        }
                    })
                    .finally(() => {
                        clearTimeout(timeoutId);
                        isCheckingPayments = false;
                    });
            }

            // Delay background checking agar tidak menahan indikator loading awal browser
            window.addEventListener('load', function() {
                setTimeout(checkForNewPayments, 2000);
                setInterval(checkForNewPayments, 15000);
            });
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/pwa-sw.js?v=2')
                .then(reg => {
                    reg.update();
                    console.log('PWA Service Worker Registered (v2)');
                })
                .catch(err => console.log('PWA Service Worker Failed', err));
        }
    </script>

    @stack('scripts')
</body>

</html>