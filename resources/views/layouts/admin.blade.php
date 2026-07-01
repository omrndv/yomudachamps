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

            <div class="mt-auto pt-2 w-100">
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

                <div class="mt-auto pt-4 w-100">
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

        });
    </script>

    {{-- FLOATING ADMIN AI ASSISTANT CHATBOT --}}
    <div id="admin-ai-chat" class="position-fixed bottom-0 end-0 m-3 m-md-4" style="z-index: 2000; font-family: 'Plus Jakarta Sans', sans-serif;">
        <!-- Toggle Button -->
        <button id="admin-ai-chat-toggle" class="btn btn-dark rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border: 2px solid rgba(255, 255, 255, 0.1); background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important; transition: all 0.3s ease;">
            <i class="bi bi-robot fs-3 text-warning" id="admin-ai-icon"></i>
            <i class="bi bi-x-lg fs-4 text-white d-none" id="admin-ai-close-icon"></i>
        </button>

        <!-- Chat Window -->
        <div id="admin-ai-chat-window" class="card shadow-2xl border-0 rounded-4 d-none" style="width: 400px; height: 520px; position: absolute; bottom: 70px; right: 0; background: #ffffff; border: 1px solid rgba(226, 232, 240, 0.9) !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); transform: scale(0.95); opacity: 0;">
            <!-- Header -->
            <div class="card-header bg-dark text-white rounded-top-4 p-3.5 d-flex align-items-center justify-content-between border-0" style="background: linear-gradient(135deg, #0f172a 0%, #020617 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;">
                        <i class="bi bi-robot text-dark fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white" style="font-size: 0.92rem; letter-spacing: -0.3px;">Yomuda Admin AI</h6>
                        <span class="text-warning small fw-bold d-flex align-items-center gap-1" style="font-size: 0.68rem; opacity: 0.85;">
                            <span class="d-inline-block bg-warning rounded-circle" style="width: 6px; height: 6px; animation: pulse 1.5s infinite;"></span> Dashboard Analyst
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-link text-white-50 p-1.5 rounded-3 hover-bg-white-10" id="admin-ai-chat-clear-btn" title="Hapus riwayat chat">
                        <i class="bi bi-trash3" style="font-size: 0.9rem;"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white shadow-none" id="admin-ai-chat-close-btn" style="font-size: 0.8rem;"></button>
                </div>
            </div>
            <!-- Body / Message Area -->
            <div class="card-body p-3.5 overflow-y-auto d-flex flex-column gap-3" id="admin-ai-chat-messages" style="height: 380px; font-size: 0.85rem; background: #fafafb;">
                <!-- Welcome Message -->
                <div class="d-flex gap-2.5 ai-message">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;">
                        <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed">
                        Halo Administrator! Saya **Yomuda Admin AI** 📊🤖<br><br>
                        Saya terhubung langsung ke database real-time Anda hari ini. Anda bisa menanyakan sejarah tim, rekap finansial, performa turnamen, atau estimasi slot penuh.<br><br>
                        *Contoh pertanyaan:*
                        - *"tim griffin pernah daftar di season berapa saja?"*
                        - *"rekap finansial dan total transaksi saat ini"*
                        - *"bagaimana perkembangan slot season aktif?"*

                        <div class="d-flex flex-wrap gap-1.5 mt-3" id="admin-ai-quick-prompts">
                            <button type="button" class="btn btn-outline-dark btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(0,0,0,0.15);" data-prompt="rekap finansial dan total transaksi saat ini">💵 Rekap Uang</button>
                            <button type="button" class="btn btn-outline-dark btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(0,0,0,0.15);" data-prompt="bagaimana perkembangan slot season aktif?">📊 Cek Slot</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer / Input Form -->
            <div class="card-footer p-2.5 bg-white border-top border-light rounded-bottom-4">
                <form id="admin-ai-chat-form" class="input-group input-group-sm">
                    <input type="text" id="admin-ai-chat-input" class="form-control border-0 bg-light rounded-pill-start ps-3.5 shadow-none text-dark" placeholder="Tanya tentang tokomu / turnamen..." style="height: 42px; font-size: 14px !important; outline: none;">
                    <button type="submit" class="btn btn-dark rounded-pill-end px-3.5 shadow-none d-flex align-items-center justify-content-center" style="height: 42px; background: #0f172a; border-color: #0f172a;">
                        <i class="bi bi-send-fill text-warning fs-5" id="admin-ai-send-icon"></i>
                        <span class="spinner-border spinner-border-sm text-warning d-none" id="admin-ai-send-spinner" role="status"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        #admin-ai-chat-toggle {
            position: relative;
        }
        #admin-ai-chat-toggle:hover {
            transform: scale(1.1) rotate(5deg);
        }
        #admin-ai-chat-toggle::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid #f59e0b;
            animation: admin-ai-pulse 2s infinite;
            opacity: 0;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        @keyframes admin-ai-pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.8;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }
        #admin-ai-chat-messages::-webkit-scrollbar {
            width: 4px;
        }
        #admin-ai-chat-messages::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        #admin-ai-chat-input:focus {
            background: #fff !important;
            border: 1px solid rgba(15, 23, 42, 0.4) !important;
        }
        .hover-bg-white-10:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        @media (max-width: 575.98px) {
            #admin-ai-chat-window {
                width: calc(100vw - 32px) !important;
                right: 0 !important;
                left: auto !important;
                bottom: 70px !important;
                height: 480px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('admin-ai-chat-toggle');
            const closeBtn = document.getElementById('admin-ai-chat-close-btn');
            const clearBtn = document.getElementById('admin-ai-chat-clear-btn');
            const chatWindow = document.getElementById('admin-ai-chat-window');
            const chatMessages = document.getElementById('admin-ai-chat-messages');
            const chatForm = document.getElementById('admin-ai-chat-form');
            const chatInput = document.getElementById('admin-ai-chat-input');
            const aiIcon = document.getElementById('admin-ai-icon');
            const aiCloseIcon = document.getElementById('admin-ai-close-icon');
            const sendIcon = document.getElementById('admin-ai-send-icon');
            const sendSpinner = document.getElementById('admin-ai-send-spinner');

            function toggleChat() {
                if (chatWindow.classList.contains('d-none')) {
                    chatWindow.classList.remove('d-none');
                    setTimeout(() => {
                        chatWindow.style.opacity = '1';
                        chatWindow.style.transform = 'scale(1)';
                    }, 50);
                    aiIcon.classList.add('d-none');
                    aiCloseIcon.classList.remove('d-none');
                    chatInput.focus();
                } else {
                    chatWindow.style.opacity = '0';
                    chatWindow.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        chatWindow.classList.add('d-none');
                    }, 250);
                    aiIcon.classList.remove('d-none');
                    aiCloseIcon.classList.add('d-none');
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            if (toggleBtn && closeBtn && chatWindow) {
                toggleBtn.addEventListener('click', toggleChat);
                closeBtn.addEventListener('click', toggleChat);
            }

            function attachAdminPromptHandlers() {
                const promptBtns = document.querySelectorAll('#admin-ai-quick-prompts button');
                promptBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        chatInput.value = btn.getAttribute('data-prompt');
                        chatForm.dispatchEvent(new Event('submit'));
                    });
                });
            }

            function formatMessageText(text) {
                if (!text) return '';
                text = text.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
                text = text.replace(/^\s*\*\s+(.+)$/gm, '• $1');
                text = text.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" class="text-primary fw-semibold">$1</a>');
                text = text.replace(/(<a[^>]*>.*?<\/a>)|(https?:\/\/[^\s<]+)/g, (match, group1, group2) => {
                    if (group1) return group1;
                    return `<a href="${group2}" target="_blank" class="text-primary fw-semibold">${group2}</a>`;
                });
                return text;
            }

            const welcomeTextEl = document.querySelector('.ai-message .bg-white');
            if (welcomeTextEl) {
                welcomeTextEl.innerHTML = formatMessageText(welcomeTextEl.innerHTML);
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    if (confirm('Hapus seluruh riwayat chat dengan Yomuda Admin AI?')) {
                        chatMessages.innerHTML = `
                            <div class="d-flex gap-2.5 ai-message">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;">
                                    <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                                </div>
                                <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed">
                                    Riwayat obrolan admin di-reset. Ada analisa data lain yang bisa saya bantu?
                                    <div class="d-flex flex-wrap gap-1.5 mt-3" id="admin-ai-quick-prompts">
                                        <button type="button" class="btn btn-outline-dark btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(0,0,0,0.15);" data-prompt="rekap finansial dan total transaksi saat ini">💵 Rekap Uang</button>
                                        <button type="button" class="btn btn-outline-dark btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(0,0,0,0.15);" data-prompt="bagaimana perkembangan slot season aktif?">📊 Cek Slot</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        attachAdminPromptHandlers();
                    }
                });
            }

            attachAdminPromptHandlers();

            if (chatForm) {
                chatForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const text = chatInput.value.trim();
                    if (!text) return;

                    chatInput.value = '';
                    const qp = document.getElementById('admin-ai-quick-prompts');
                    if (qp) qp.classList.add('d-none');

                    if (sendIcon && sendSpinner) {
                        sendIcon.classList.add('d-none');
                        sendSpinner.classList.remove('d-none');
                    }

                    const userMsg = document.createElement('div');
                    userMsg.className = 'd-flex justify-content-end mb-1';
                    userMsg.innerHTML = `
                        <div class="bg-dark bg-opacity-10 border border-dark border-opacity-10 p-3 rounded-4 rounded-end-0 text-dark max-w-75 shadow-xs leading-relaxed">
                            ${text}
                        </div>
                    `;
                    chatMessages.appendChild(userMsg);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    const loader = document.createElement('div');
                    loader.className = 'd-flex gap-2 align-items-center text-muted mb-1';
                    loader.id = 'admin-ai-typing-indicator';
                    loader.innerHTML = `
                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;">
                            <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                        </div>
                        <div class="spinner-border spinner-border-sm text-dark" role="status"></div>
                        <span class="small italic" style="font-size: 0.75rem;">Yomuda Admin AI sedang menganalisis data...</span>
                    `;
                    chatMessages.appendChild(loader);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    fetch("{{ route('admin.ai.chat') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: text })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (sendIcon && sendSpinner) {
                            sendIcon.classList.remove('d-none');
                            sendSpinner.classList.add('d-none');
                        }

                        const ind = document.getElementById('admin-ai-typing-indicator');
                        if (ind) ind.remove();

                        const aiReply = document.createElement('div');
                        aiReply.className = 'd-flex gap-2.5 mb-1';
                        
                        let formattedText = formatMessageText(data.reply || 'Maaf, saya gagal menganalisis.');

                        aiReply.innerHTML = `
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;">
                                <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                            </div>
                            <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed" style="white-space: pre-line;">
                                ${formattedText}
                            </div>
                        `;
                        chatMessages.appendChild(aiReply);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    })
                    .catch(err => {
                        if (sendIcon && sendSpinner) {
                            sendIcon.classList.remove('d-none');
                            sendSpinner.classList.add('d-none');
                        }

                        const ind = document.getElementById('admin-ai-typing-indicator');
                        if (ind) ind.remove();

                        const aiReply = document.createElement('div');
                        aiReply.className = 'd-flex gap-2.5 mb-1';
                        aiReply.innerHTML = `
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;">
                                <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                            </div>
                            <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed">
                                Gagal menghubungkan ke asisten AI. Silakan periksa jaringan server Anda.
                            </div>
                        `;
                        chatMessages.appendChild(aiReply);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    });
                });
            }
        });
    </script>

    @stack('scripts')
</body>

</html>