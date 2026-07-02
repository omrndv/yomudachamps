<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Yomuda Championship</title>
    
    <!-- Speed Optimization: DNS Prefetch & Preconnect -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Tailwind CSS (BFF / SaaS Modern Dashboard Styling) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons for Premium UI look -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">
    
    <script>
        // Tailwind Config & Dark Mode
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        };

        // Theme Checker
        if (localStorage.getItem('admin-theme') === 'dark' || (!('admin-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Sidebar Collapse Checker
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        /* ---------------------------------------------------- */
        /* HYBRID BOOTSTRAP OVERRIDES TO MATCH TAILWIND DESIGN */
        /* ---------------------------------------------------- */
        
        /* Layout Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.25);
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.15);
        }

        /* Cards Override */
        .card, .card-custom {
            background-color: #ffffff !important;
            border: 1px solid #f1f5f9 !important;
            border-radius: 20px !important;
            padding: 24px !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05) !important;
            color: #1e293b !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .dark .card, .dark .card-custom {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }

        /* Table Override */
        .table-responsive {
            border-radius: 16px !important;
            border: 1px solid #f1f5f9 !important;
            overflow: hidden;
        }
        .dark .table-responsive {
            border-color: #334155 !important;
        }
        .table {
            margin-bottom: 0 !important;
            background-color: transparent !important;
        }
        .table th {
            background-color: #f8fafc !important;
            color: #64748b !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.7rem !important;
            letter-spacing: 0.8px !important;
            padding: 16px 20px !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }
        .dark .table th {
            background-color: #0f172a !important;
            color: #94a3b8 !important;
            border-color: #334155 !important;
        }
        .table td {
            padding: 16px 20px !important;
            vertical-align: middle !important;
            color: #334155 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 0.85rem !important;
            background-color: transparent !important;
        }
        .dark .table td {
            color: #cbd5e1 !important;
            border-color: #334155 !important;
        }
        .table tr:last-child td {
            border-bottom: none !important;
        }
        .table-hover tbody tr:hover td {
            background-color: #f8fafc !important;
        }
        .dark .table-hover tbody tr:hover td {
            background-color: #1e293b !important;
        }

        /* Form Inputs Override */
        .form-control, .form-select {
            background-color: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px !important;
            padding: 10px 16px !important;
            font-size: 0.875rem !important;
            color: #1e293b !important;
            transition: all 0.2s !important;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff !important;
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12) !important;
        }
        .dark .form-control, .dark .form-select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .dark .form-control:focus, .dark .form-select:focus {
            background-color: #0f172a !important;
            border-color: #818cf8 !important;
        }

        /* Buttons Override */
        .btn {
            border-radius: 12px !important;
            padding: 10px 18px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            border: none !important;
            transition: all 0.2s active:scale-[0.98] !important;
        }
        .btn-primary {
            background-color: #4f46e5 !important;
            color: #ffffff !important;
        }
        .btn-primary:hover {
            background-color: #4338ca !important;
        }
        .btn-success {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }
        .btn-success:hover {
            background-color: #059669 !important;
        }
        .btn-danger {
            background-color: #ef4444 !important;
            color: #ffffff !important;
        }
        .btn-danger:hover {
            background-color: #dc2626 !important;
        }
        .btn-warning {
            background-color: #f59e0b !important;
            color: #ffffff !important;
        }
        .btn-warning:hover {
            background-color: #d97706 !important;
        }

        /* Soft Badges Override */
        .badge {
            padding: 6px 12px !important;
            font-weight: 700 !important;
            font-size: 0.75rem !important;
            border-radius: 9999px !important;
            text-transform: uppercase !important;
        }
        .badge.bg-success {
            background-color: rgba(16, 185, 129, 0.1) !important;
            color: #10b981 !important;
        }
        .badge.bg-warning {
            background-color: rgba(245, 158, 11, 0.1) !important;
            color: #d97706 !important;
        }
        .badge.bg-danger {
            background-color: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
        }
        .badge.bg-secondary {
            background-color: rgba(100, 116, 139, 0.1) !important;
            color: #64748b !important;
        }

        /* Sidebar Collapse Transitions */
        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .content-transition {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col">

    <!-- DESKTOP SIDEBAR -->
    <aside id="desktop-sidebar" class="fixed inset-y-0 left-0 z-40 sidebar-transition w-64 bg-slate-900 dark:bg-slate-950 text-white flex flex-col border-r border-slate-800/60 hidden xl:flex">
        <!-- Sidebar Branding -->
        <div class="h-20 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center px-6 gap-3 shrink-0 relative">
            <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shadow-sm shrink-0">
                <i data-lucide="zap" class="w-5 h-5 text-indigo-200"></i>
            </div>
            <div class="sidebar-brand-text">
                <h1 class="text-sm font-black tracking-wider leading-none uppercase">Yomuda</h1>
                <span class="text-[10px] text-indigo-200 font-bold tracking-widest uppercase">Championship</span>
            </div>
        </div>

        <!-- Sidebar Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scroll">
            <span class="sidebar-brand-text block text-[10px] font-black text-slate-500 uppercase tracking-widest px-4 mb-3">Menu Utama</span>

            <a href="{{ route('admin.dashboard.home') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.dashboard.home') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Dashboard</span>
            </a>

            @if(Auth::check() && Auth::user()->hasPermission('seasons'))
            <a href="{{ route('admin.seasons') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.seasons') || request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="trophy" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Daftar Season</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('teams'))
            <a href="{{ route('admin.teams') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.teams') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Daftar Team</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('payments'))
            <a href="{{ route('admin.payments') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.payments') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="wallet-2" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Riwayat Pembayaran</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('notes'))
            <a href="{{ route('admin.notes.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.notes.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="sticky-note" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Catatan Admin</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('settings'))
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.settings') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="sliders" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Pengaturan</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('faqs'))
            <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.faqs.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="help-circle" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Kelola FAQ</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('activity_log'))
            <a href="{{ route('admin.activity-log') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.activity-log') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="history" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Log Aktivitas</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('manage'))
            <span class="sidebar-brand-text block text-[10px] font-black text-slate-500 uppercase tracking-widest px-4 pt-4 mb-3">System & Dev</span>
            <a href="{{ route('admin.manage') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.manage') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Kelola Admin</span>
            </a>
            <a href="{{ route('admin.system-logs') }}" class="nav-link flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.system-logs') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="file-text" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Log Laravel</span>
            </a>
            <a href="{{ route('admin.storage') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.storage') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                <i data-lucide="database" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Kelola Penyimpanan</span>
            </a>
            @endif

            @if(Auth::check() && Auth::user()->hasPermission('backup'))
            <a href="{{ route('admin.backup') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all {{ request()->routeIs('admin.backup') ? 'bg-red-500/20' : '' }}">
                <i data-lucide="download" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Backup Database</span>
            </a>
            @endif
        </nav>

        <!-- Sidebar Collapse Toggle Button (Floating) -->
        <button id="toggle-sidebar" class="absolute -right-3 top-8 w-6 h-6 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-500 hover:text-indigo-600 shadow-sm active:scale-95 transition-all">
            <i data-lucide="chevron-left" id="toggle-icon" class="w-4 h-4 transition-transform duration-300"></i>
        </button>
    </aside>

    <!-- MOBILE DRAWER SIDEBAR -->
    <div id="mobile-sidebar" class="fixed inset-0 z-50 overflow-hidden hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div id="mobile-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0 duration-300"></div>

        <div class="fixed inset-y-0 left-0 w-64 bg-slate-900 dark:bg-slate-950 text-white flex flex-col border-r border-slate-800/60 transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div class="h-20 bg-gradient-to-r from-indigo-600 to-indigo-700 flex items-center justify-between px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shadow-sm">
                        <i data-lucide="zap" class="w-5 h-5 text-indigo-200"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-black tracking-wider leading-none uppercase">Yomuda</h1>
                        <span class="text-[10px] text-indigo-200 font-bold tracking-widest uppercase">Championship</span>
                    </div>
                </div>
                <button id="close-mobile-sidebar" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5 text-white"></i>
                </button>
            </div>

            <!-- Drawer Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scroll">
                <a href="{{ route('admin.dashboard.home') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.dashboard.home') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
                </a>
                @if(Auth::check() && Auth::user()->hasPermission('seasons'))
                <a href="{{ route('admin.seasons') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.seasons') || request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="trophy" class="w-5 h-5"></i> Daftar Season
                </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('teams'))
                <a href="{{ route('admin.teams') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.teams') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="users" class="w-5 h-5"></i> Daftar Team
                </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('payments'))
                <a href="{{ route('admin.payments') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.payments') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="wallet-2" class="w-5 h-5"></i> Riwayat Pembayaran
                </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('notes'))
                <a href="{{ route('admin.notes.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.notes.*') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="sticky-note" class="w-5 h-5"></i> Catatan Admin
                </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('settings'))
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.settings') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="sliders" class="w-5 h-5"></i> Pengaturan
                </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('faqs'))
                <a href="{{ route('admin.faqs.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.faqs.*') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="help-circle" class="w-5 h-5"></i> Kelola FAQ
                </a>
                @endif
                @if(Auth::check() && Auth::user()->hasPermission('activity_log'))
                <a href="{{ route('admin.activity-log') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all {{ request()->routeIs('admin.activity-log') ? 'bg-indigo-600 text-white' : 'text-slate-400' }}">
                    <i data-lucide="history" class="w-5 h-5"></i> Log Aktivitas
                </a>
                @endif
            </nav>
        </div>
    </div>

    <!-- MAIN APP CONTAINER -->
    <div id="main-container" class="flex-1 flex flex-col min-h-screen content-transition xl:ml-64">
        
        <!-- HEADER / NAVBAR -->
        <header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 sm:px-8 sticky top-0 z-30 shrink-0 shadow-sm">
            <div class="flex items-center gap-4">
                <!-- Mobile Sidebar Toggle -->
                <button id="open-mobile-sidebar" class="w-10 h-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 xl:hidden active:scale-95 transition-all">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 class="text-md sm:text-lg font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    Panel Admin
                </h2>
            </div>

            <!-- Header Controls -->
            <div class="flex items-center gap-4">
                <!-- Status Badge -->
                <div class="hidden sm:flex bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 text-[10px] font-extrabold px-3 py-1.5 rounded-full items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Monitoring
                </div>

                <!-- Theme Toggle Button -->
                <button id="theme-toggle" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:text-indigo-600 flex items-center justify-center text-slate-500 active:scale-95 transition-all">
                    <i data-lucide="sun" id="sun-icon" class="w-5 h-5 hidden dark:block"></i>
                    <i data-lucide="moon" id="moon-icon" class="w-5 h-5 dark:hidden"></i>
                </button>

                <!-- Admin Profile Info -->
                <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-800">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="hidden md:block">
                        <div class="text-xs font-extrabold text-slate-800 dark:text-slate-100 leading-none">{{ Auth::user()->name ?? 'Administrator' }}</div>
                        <span class="text-[9px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-0.5 block">Admin</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT AREA -->
        <main class="flex-1 p-6 sm:p-8">
            @yield('content')
        </main>

    </div>

    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initiate Lucide Icons
            lucide.createIcons();

            // ----------------------------------------------------
            // Theme Toggle Logic
            // ----------------------------------------------------
            const themeToggleBtn = document.getElementById('theme-toggle');
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('admin-theme', isDark ? 'dark' : 'light');
                });
            }

            // ----------------------------------------------------
            // Desktop Sidebar Collapse Logic
            // ----------------------------------------------------
            const toggleSidebarBtn = document.getElementById('toggle-sidebar');
            const desktopSidebar = document.getElementById('desktop-sidebar');
            const mainContainer = document.getElementById('main-container');
            const toggleIcon = document.getElementById('toggle-icon');

            function applySidebarState() {
                const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-collapsed');
                    if (desktopSidebar) desktopSidebar.style.width = '72px';
                    if (mainContainer) mainContainer.style.marginLeft = '72px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                    
                    // Hide branding text
                    document.querySelectorAll('.sidebar-brand-text').forEach(el => el.classList.add('hidden'));
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                    if (desktopSidebar) desktopSidebar.style.width = '256px';
                    if (mainContainer) mainContainer.style.marginLeft = '256px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                    
                    // Show branding text
                    document.querySelectorAll('.sidebar-brand-text').forEach(el => el.classList.remove('hidden'));
                }
            }

            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function() {
                    const currentState = localStorage.getItem('sidebar-collapsed') === 'true';
                    localStorage.setItem('sidebar-collapsed', !currentState);
                    applySidebarState();
                });
            }
            
            // Apply immediately on load
            applySidebarState();

            // ----------------------------------------------------
            // Mobile Sidebar Drawer Logic
            // ----------------------------------------------------
            const openMobileSidebarBtn = document.getElementById('open-mobile-sidebar');
            const closeMobileSidebarBtn = document.getElementById('close-mobile-sidebar');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileBackdrop = document.getElementById('mobile-backdrop');
            const mobileMenuContent = mobileSidebar ? mobileSidebar.querySelector('.transform') : null;

            if (openMobileSidebarBtn && mobileSidebar && mobileBackdrop && mobileMenuContent) {
                openMobileSidebarBtn.addEventListener('click', function() {
                    mobileSidebar.classList.remove('hidden');
                    setTimeout(() => {
                        mobileBackdrop.classList.remove('opacity-0');
                        mobileBackdrop.classList.add('opacity-100');
                        mobileMenuContent.classList.remove('-translate-x-full');
                        mobileMenuContent.classList.add('translate-x-0');
                    }, 50);
                });

                const closeMenu = function() {
                    mobileBackdrop.classList.remove('opacity-100');
                    mobileBackdrop.classList.add('opacity-0');
                    mobileMenuContent.classList.remove('translate-x-0');
                    mobileMenuContent.classList.add('-translate-x-full');
                    setTimeout(() => {
                        mobileSidebar.classList.add('hidden');
                    }, 300);
                };

                closeMobileSidebarBtn.addEventListener('click', closeMenu);
                mobileBackdrop.addEventListener('click', closeMenu);
            }

            // ----------------------------------------------------
            // Global Live Payment Notifications
            // ----------------------------------------------------
            let lastPaidTeamId = localStorage.getItem('last_paid_team_id');

            function playSuccessPaymentSound() {
                try {
                    const context = new (window.AudioContext || window.webkitAudioContext)();
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
                            
                            if (!lastPaidTeamId) {
                                localStorage.setItem('last_paid_team_id', team.id);
                                lastPaidTeamId = team.id;
                                return;
                            }

                            if (team.id > lastPaidTeamId) {
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