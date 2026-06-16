<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Yomuda Championship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">
    <style>
        :root {
            --bg-body: #f8fafc;
            --color-body: #1e293b;
            --bg-card: #ffffff;
            --border-color: #f1f5f9;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --bg-light: #f8fafc;
            --bg-input: #ffffff;
            --border-input: #dee2e6;
            --text-input: #212529;
            --bg-modal: #ffffff;
        }

        body.dark-mode {
            --bg-body: #090d16;
            --color-body: #cbd5e1;
            --bg-card: #0f172a;
            --border-color: rgba(255, 255, 255, 0.08);
            --text-dark: #f8fafc;
            --text-muted: #94a3b8;
            --bg-light: #161f30;
            --bg-input: #161f30;
            --border-input: rgba(255, 255, 255, 0.1);
            --text-input: #f8fafc;
            --bg-modal: #0f172a;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body) !important;
            color: var(--color-body);
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Card custom overrides */
        .card,
        [class*="card"] {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--color-body) !important;
        }
        
        .card h1, .card h2, .card h3, .card h4, .card h5, .card h6,
        [class*="card"] h1, [class*="card"] h2, [class*="card"] h3, [class*="card"] h4, [class*="card"] h5, [class*="card"] h6,
        .text-dark, .text-slate-800, .text-slate-700, .fw-bold {
            color: var(--text-dark) !important;
        }
        
        .text-secondary, .text-muted {
            color: var(--text-muted) !important;
        }

        /* Dark mode generic selector overrides for inline helper classes and third-party classes */
        body.dark-mode .bg-white,
        body.dark-mode .card-stats,
        body.dark-mode .card-settings,
        body.dark-mode .season-card,
        body.dark-mode .list-group-item,
        body.dark-mode .dropdown-menu {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--color-body) !important;
        }

        body.dark-mode .dropdown-item {
            color: var(--color-body) !important;
        }
        body.dark-mode .dropdown-item:hover {
            background-color: var(--bg-light) !important;
            color: var(--text-dark) !important;
        }
        
        body.dark-mode .container-fluid,
        body.dark-mode .main-content div[style*="background-color: #f8fafc"],
        body.dark-mode .main-content div[style*="background-color:#f8fafc"],
        body.dark-mode div[style*="background-color: #f8fafc"],
        body.dark-mode div[style*="background-color:#f8fafc"] {
            background-color: var(--bg-body) !important;
        }

        body.dark-mode [style*="background-color: #ffffff"],
        body.dark-mode [style*="background-color:#ffffff"] {
            background-color: var(--bg-card) !important;
        }

        body.dark-mode .text-dark,
        body.dark-mode .text-slate-800,
        body.dark-mode .text-slate-700,
        body.dark-mode .text-slate-900,
        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3, 
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6,
        body.dark-mode .h1, body.dark-mode .h2, body.dark-mode .h3,
        body.dark-mode .h4, body.dark-mode .h5, body.dark-mode .h6 {
            color: var(--text-dark) !important;
        }

        body.dark-mode .text-secondary,
        body.dark-mode .text-muted,
        body.dark-mode small.text-muted,
        body.dark-mode .text-slate-500,
        body.dark-mode .text-slate-600 {
            color: var(--text-muted) !important;
        }

        /* Match specific hardcoded dark text inline colors in dark mode */
        body.dark-mode [style*="color: #1e293b"],
        body.dark-mode [style*="color:#1e293b"],
        body.dark-mode [style*="color: #0f172a"],
        body.dark-mode [style*="color:#0f172a"],
        body.dark-mode [style*="color: #334155"],
        body.dark-mode [style*="color:#334155"],
        body.dark-mode [style*="color: #212529"],
        body.dark-mode [style*="color:#212529"],
        body.dark-mode [style*="color: #475569"],
        body.dark-mode [style*="color:#475569"] {
            color: var(--text-dark) !important;
        }

        body.dark-mode [style*="color: #64748b"],
        body.dark-mode [style*="color:#64748b"] {
            color: var(--text-muted) !important;
        }

        /* Scrollbar styling in Dark Mode */
        body.dark-mode::-webkit-scrollbar {
            width: 10px;
        }
        body.dark-mode::-webkit-scrollbar-track {
            background: var(--bg-body);
        }
        body.dark-mode::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 5px;
        }
        body.dark-mode::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }

        /* Input fields and selects */
        .form-control, .form-select, .input-group-text {
            background-color: var(--bg-input) !important;
            border-color: var(--border-input) !important;
            color: var(--text-input) !important;
        }
        
        /* Ensure dropdown option text is white on dark background in chrome/safari */
        body.dark-mode select option {
            background-color: var(--bg-card) !important;
            color: var(--text-dark) !important;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--bg-input) !important;
            border-color: #f59e0b !important; /* Warning highlight */
            color: var(--text-input) !important;
            box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25) !important;
        }
        body.dark-mode .form-control::placeholder {
            color: var(--text-muted) !important;
            opacity: 0.6;
        }
        
        /* Modal backgrounds */
        .modal-content {
            background-color: var(--bg-modal) !important;
            color: var(--color-body) !important;
            border: 1px solid var(--border-color) !important;
        }
        .modal-header, .modal-footer {
            border-color: var(--border-color) !important;
        }

        /* Tables & rows override */
        .table {
            color: var(--color-body) !important;
        }
        .table th {
            background-color: var(--bg-light) !important;
            color: var(--text-dark) !important;
            border-color: var(--border-color) !important;
        }
        .table td {
            background-color: transparent !important;
            border-color: var(--border-color) !important;
            color: var(--color-body) !important;
        }
        .table-hover tbody tr:hover td {
            background-color: var(--bg-light) !important;
        }
        .table-responsive {
            border-color: var(--border-color) !important;
        }
        body.dark-mode tr, 
        body.dark-mode td,
        body.dark-mode th {
            border-bottom-color: var(--border-color) !important;
        }

        /* List groups */
        .list-group-item {
            background-color: transparent !important;
            border-color: var(--border-color) !important;
            color: var(--color-body) !important;
        }
        
        /* Breadcrumbs */
        .breadcrumb-item.active {
            color: var(--text-muted) !important;
        }
        body.dark-mode .breadcrumb-item a {
            color: #fbbf24 !important; /* Warning gold link */
        }

        /* Miscellaneous utilities & Buttons */
        .bg-light {
            background-color: var(--bg-light) !important;
        }
        .border-light, .border-light-subtle {
            border-color: var(--border-color) !important;
        }

        body.dark-mode .btn-light {
            background-color: var(--bg-light) !important;
            color: var(--color-body) !important;
            border-color: var(--border-color) !important;
        }
        body.dark-mode .btn-light:hover {
            background-color: var(--border-color) !important;
            color: var(--text-dark) !important;
        }

        /* Force warning button text to stay dark for visibility */
        body.dark-mode .btn-warning,
        body.dark-mode .btn-warning * {
            color: #0f172a !important;
        }
        
        /* Convert btn-dark to bright warning button in dark mode for contrast */
        body.dark-mode .btn-dark {
            background-color: #f59e0b !important;
            border-color: #f59e0b !important;
            color: #0f172a !important;
            font-weight: 700 !important;
        }
        body.dark-mode .btn-dark:hover {
            background-color: #d97706 !important;
            border-color: #d97706 !important;
            color: #ffffff !important;
        }
        
        /* Badges design system adaptation in dark mode */
        body.dark-mode .bg-success-subtle {
            background-color: rgba(16, 185, 129, 0.15) !important;
            color: #34d399 !important;
        }
        body.dark-mode .bg-secondary-subtle {
            background-color: rgba(148, 163, 184, 0.15) !important;
            color: #cbd5e1 !important;
        }
        body.dark-mode .bg-warning-subtle {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #fbbf24 !important;
        }
        body.dark-mode .bg-danger-subtle {
            background-color: rgba(239, 68, 68, 0.15) !important;
            color: #fca5a5 !important;
        }
        body.dark-mode .bg-primary-subtle {
            background-color: rgba(59, 130, 246, 0.15) !important;
            color: #93c5fd !important;
        }

        /* Info alerts overrides */
        body.dark-mode .alert-info,
        body.dark-mode [style*="background-color: #f0f9ff"],
        body.dark-mode [style*="background-color:#f0f9ff"] {
            background-color: rgba(14, 165, 233, 0.15) !important;
            border-color: rgba(14, 165, 233, 0.3) !important;
            color: #38bdf8 !important;
        }
        
        /* Whatsapp link action icon in dark mode */
        body.dark-mode .btn-light-success {
            background-color: rgba(16, 185, 129, 0.15) !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
            color: #34d399 !important;
        }
        body.dark-mode .btn-light-success:hover {
            background-color: rgba(16, 185, 129, 0.25) !important;
        }
        
        /* SweetAlert popup overrides in dark mode */
        body.dark-mode .swal2-popup {
            background-color: var(--bg-card) !important;
            color: var(--color-body) !important;
        }
        body.dark-mode .swal2-title,
        body.dark-mode .swal2-content,
        body.dark-mode .swal2-html-container {
            color: var(--text-dark) !important;
        }
        
        /* ApexCharts labels adjustments dynamically in dark mode */
        body.dark-mode .apexcharts-canvas text {
            fill: #94a3b8 !important;
        }
        body.dark-mode .apexcharts-tooltip {
            background: #0f172a !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #cbd5e1 !important;
        }
        body.dark-mode .apexcharts-tooltip-title {
            background: #1e293b !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }
        
        /* Sidebar Backup item special override */
        .sidebar-nav .nav-link.backup-link {
            color: #f87171 !important;
        }
        .sidebar-nav .nav-link.backup-link:hover {
            background-color: rgba(239, 68, 68, 0.1) !important;
            color: #fca5a5 !important;
            transform: translateX(4px);
        }

        /* Desktop Sidebar Stylings */
        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            background: linear-gradient(180deg, #0f172a 0%, #020617 100%);
            color: #ffffff;
            z-index: 1000;
            padding: 24px 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 10px 8px 30px 8px;
            text-align: center;
            transition: all 0.3s ease;
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
            height: calc(100% - 70px);
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

        .nav-link.text-danger:hover {
            color: #ef4444 !important;
            background: rgba(239, 68, 68, 0.1) !important;
        }

        .main-content {
            margin-left: 280px;
            padding: 40px;
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
                width: 80px;
                padding: 24px 12px;
            }
            body.sidebar-collapsed .sidebar-header {
                padding-bottom: 20px;
            }
            body.sidebar-collapsed .sidebar-brand span:not(.brand-icon) {
                display: none !important;
            }
            body.sidebar-collapsed .sidebar .nav-link {
                padding: 12px;
                justify-content: center;
                margin-bottom: 8px;
            }
            body.sidebar-collapsed .sidebar .nav-link:hover {
                transform: scale(1.05);
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
            body.sidebar-collapsed .main-content {
                margin-left: 80px;
            }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 25px 15px;
            }
        }
    </style>
</head>

<body>
    <script>
        // Check sidebar state immediately to prevent layout shift/flash
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
        // Check dark mode state immediately to prevent theme flash
        if (localStorage.getItem('dark-mode') === 'true') {
            document.body.classList.add('dark-mode');
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
        <button class="btn btn-warning btn-sm position-absolute rounded-circle shadow-sm border border-light-subtle d-flex align-items-center justify-content-center" 
                id="toggleSidebar" style="right: -12px; top: 32px; width: 24px; height: 24px; z-index: 1100; padding: 0; transition: transform 0.2s ease;">
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

            <a href="{{ route('admin.seasons') }}" class="nav-link {{ request()->routeIs('admin.seasons') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-trophy"></i> <span>Daftar Season</span>
            </a>

            <a href="{{ route('admin.teams') }}" class="nav-link {{ request()->routeIs('admin.teams') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> <span>Daftar Team</span>
            </a>
            
            <a href="{{ route('admin.payments') }}" class="nav-link {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> <span>Riwayat Pembayaran</span>
            </a>

            <a href="{{ route('admin.notes.index') }}" class="nav-link {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
                <i class="bi bi-sticky"></i> <span>Catatan Admin</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                <i class="bi bi-gear"></i> <span>Pengaturan</span>
            </a>

            <a href="{{ route('admin.backup') }}" class="nav-link backup-link {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                <i class="bi bi-database-down"></i> <span>Backup Database</span>
            </a>

            <div class="mt-auto pt-2 w-100">
                <a href="javascript:void(0)" class="nav-link text-warning w-100" id="btnToggleTheme">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i> <span id="themeText">Mode Gelap</span>
                </a>
                <hr class="border-secondary opacity-25 mx-3 mb-3">
                <a href="{{ route('admin.logout') }}" class="nav-link text-danger w-100">
                    <i class="bi bi-box-arrow-right"></i> <span>Keluar</span>
                </a>
            </div>
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
            <div class="nav nav-pills flex-column h-100">
                <small class="text-uppercase text-secondary fw-bold mb-3" style="font-size: 0.65rem; letter-spacing: 1.2px; padding-left: 16px;">Menu Utama</small>
                
                <a href="{{ route('admin.dashboard.home') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.dashboard.home') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2 me-2"></i> <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.seasons') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.seasons') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-trophy me-2"></i> <span>Daftar Season</span>
                </a>

                <a href="{{ route('admin.teams') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.teams') ? 'active' : '' }}">
                    <i class="bi bi-people-fill me-2"></i> <span>Daftar Team</span>
                </a>

                <a href="{{ route('admin.payments') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack me-2"></i> <span>Riwayat Pembayaran</span>
                </a>

                <a href="{{ route('admin.notes.index') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.notes.*') ? 'active' : '' }}">
                    <i class="bi bi-sticky me-2"></i> <span>Catatan Admin</span>
                </a>

                <a href="{{ route('admin.settings') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i> <span>Pengaturan</span>
                </a>

                <a href="{{ route('admin.backup') }}" class="nav-link backup-link text-white mb-2 {{ request()->routeIs('admin.backup') ? 'active' : '' }}">
                    <i class="bi bi-database-down me-2"></i> <span>Backup Database</span>
                </a>

                <div class="mt-auto pt-4 w-100">
                    <a href="javascript:void(0)" class="nav-link text-warning w-100 mb-2" id="btnToggleThemeMobile">
                        <i class="bi bi-moon-stars-fill me-2" id="themeIconMobile"></i> <span id="themeTextMobile">Mode Gelap</span>
                    </a>
                    <hr class="border-secondary opacity-25 mb-3">
                    <a href="{{ route('admin.logout') }}" class="nav-link text-danger w-100">
                        <i class="bi bi-box-arrow-right me-2"></i> <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Page Content --}}
    <main class="main-content">
        @yield('content')
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- Sidebar Toggle & Dark Mode JS Logic --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const toggleBtn = document.getElementById('toggleSidebar');
            const toggleIcon = document.getElementById('toggleIcon');
            
            // Set correct icon if sidebar is already collapsed
            if (document.body.classList.contains('sidebar-collapsed')) {
                if (toggleIcon) {
                    toggleIcon.classList.replace('bi-chevron-left', 'bi-chevron-right');
                }
            }
            
            if (toggleBtn) {
                // Hover Scale Effect
                toggleBtn.addEventListener('mouseenter', () => {
                    toggleBtn.style.transform = 'scale(1.15)';
                });
                toggleBtn.addEventListener('mouseleave', () => {
                    toggleBtn.style.transform = 'scale(1)';
                });

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

            // Theme toggle elements
            const btnTheme = document.getElementById('btnToggleTheme');
            const iconTheme = document.getElementById('themeIcon');
            const textTheme = document.getElementById('themeText');

            const btnThemeMobile = document.getElementById('btnToggleThemeMobile');
            const iconThemeMobile = document.getElementById('themeIconMobile');
            const textThemeMobile = document.getElementById('themeTextMobile');

            function updateThemeUI(isDark) {
                if (isDark) {
                    document.body.classList.add('dark-mode');
                    if (iconTheme) iconTheme.className = 'bi bi-sun-fill';
                    if (textTheme) textTheme.innerText = 'Mode Terang';
                    if (iconThemeMobile) iconThemeMobile.className = 'bi bi-sun-fill';
                    if (textThemeMobile) textThemeMobile.innerText = 'Mode Terang';
                } else {
                    document.body.classList.remove('dark-mode');
                    if (iconTheme) iconTheme.className = 'bi bi-moon-stars-fill';
                    if (textTheme) textTheme.innerText = 'Mode Gelap';
                    if (iconThemeMobile) iconThemeMobile.className = 'bi bi-moon-stars-fill';
                    if (textThemeMobile) textThemeMobile.innerText = 'Mode Gelap';
                }
            }

            // Sync initial state UI
            const isDarkInit = localStorage.getItem('dark-mode') === 'true';
            updateThemeUI(isDarkInit);

            function toggleTheme() {
                const isDark = document.body.classList.contains('dark-mode');
                updateThemeUI(!isDark);
                localStorage.setItem('dark-mode', !isDark);
            }

            if (btnTheme) btnTheme.addEventListener('click', toggleTheme);
            if (btnThemeMobile) btnThemeMobile.addEventListener('click', toggleTheme);
        });
    </script>
    @stack('scripts')
</body>

</html>