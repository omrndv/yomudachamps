<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Yomuda Championship</title>
    
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
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .sidebar-nav::-webkit-scrollbar {
            display: none;
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
        @media (min-width: 1200px) {
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

        @media (max-width: 1199.98px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 25px 15px;
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
    <nav class="navbar navbar-mobile d-xl-none shadow-sm sticky-top">
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
    <aside class="sidebar d-none d-xl-block">
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
            <a href="{{ route('admin.payments') }}" class="nav-link {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> <span>Riwayat Pembayaran</span>
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
            <a href="{{ route('admin.system-logs') }}" class="nav-link {{ request()->routeIs('admin.system-logs') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> <span>Log Laravel</span>
            </a>
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

        <div class="sidebar-footer mt-auto pt-2 w-100 bg-transparent">
            <hr class="border-secondary opacity-25 mx-3 mb-3">
            <a href="{{ route('admin.logout') }}" class="nav-link text-danger w-100 d-flex align-items-center gap-2" style="padding: 12px 16px;">
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
                <a href="{{ route('admin.payments') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.payments') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack me-2"></i> <span>Riwayat Pembayaran</span>
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
                <a href="{{ route('admin.logout') }}" class="nav-link text-danger w-100 d-flex align-items-center gap-2">
                    <i class="bi bi-box-arrow-right"></i> <span>Keluar</span>
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

            function checkForNewPayments() {
                fetch("{{ route('admin.payments.check-new') }}")
                    .then(r => r.json())
                    .then(res => {
                        if (res.success && res.latest_paid) {
                            const team = res.latest_paid;
                            
                            // Initialize lastPaidTeamId if not set, so it won't trigger sound on page load
                            if (!lastPaidTeamId) {
                                localStorage.setItem('last_paid_team_id', team.id);
                                lastPaidTeamId = team.id;
                                return;
                            }

                            if (team.id > lastPaidTeamId) {
                                localStorage.setItem('last_paid_team_id', team.id);
                                lastPaidTeamId = team.id;

                                // Play money success sound
                                playSuccessPaymentSound();

                                // Show Toast Notification using SweetAlert
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
                    })
                    .catch(err => console.log("New payment check issue:", err));
            }

            setInterval(checkForNewPayments, 15000);
            checkForNewPayments();
        });
    </script>

    @stack('scripts')
</body>

</html>