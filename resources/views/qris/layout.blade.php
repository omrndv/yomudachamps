<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dips Gateway - @yield('title', 'Dashboard')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js (Untuk Grafik Animatif Sangat Smooth & Interaktif) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;950&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            blue: '#2563eb',       // Biru utama TriPay
                            blueLight: '#eff6ff',  // Latar aktif menu
                            grayBg: '#f8fafc',     // Latar abu-abu TriPay
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        };

        // Theme Checker
        if (localStorage.getItem('qris-theme') === 'dark' || (!('qris-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Sidebar state checker
        if (localStorage.getItem('qris-sidebar-collapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc; 
            transition: background-color 0.3s, color 0.3s;
        }
        .dark body {
            background-color: #0c0a0f;
        }
        /* Collapsed transitions */
        .sidebar-transition {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        /* Custom scrollbar */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.2);
            border-radius: 99px;
        }

        /* Sidebar Collapsed Styles (Fixing squished layout on close) */
        .sidebar-collapsed #desktop-sidebar {
            width: 80px !important;
        }
        .sidebar-collapsed .sidebar-brand-text {
            display: none !important;
        }
        .sidebar-collapsed #desktop-sidebar nav a {
            justify-content: center !important;
            padding: 12px !important;
        }
        .sidebar-collapsed #desktop-sidebar nav a svg {
            width: 22px !important;
            height: 22px !important;
        }
        .sidebar-collapsed #desktop-sidebar .p-4 a, 
        .sidebar-collapsed #desktop-sidebar .p-4 button {
            justify-content: center !important;
            padding: 12px !important;
        }
        .sidebar-collapsed #desktop-sidebar .p-4 a span, 
        .sidebar-collapsed #desktop-sidebar .p-4 button span {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen flex text-slate-800 dark:text-slate-100 overflow-hidden p-0">

    <!-- FULL SCREEN FRAME CONTAINER -->
    <div class="flex-grow flex h-screen bg-[#f8fafc] dark:bg-[#0c0a0f] overflow-hidden">

        <!-- SIDEBAR (Clean White Aesthetic like TriPay, Flexbox-managed) -->
        <aside id="desktop-sidebar" class="sidebar-transition w-64 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-300 flex flex-col shrink-0 border-r border-slate-200/80 dark:border-slate-800/80 h-full hidden xl:flex relative">
            <!-- Sidebar Branding / Logo -->
            <div class="h-24 flex items-center px-8 gap-3 shrink-0 border-b border-slate-100 dark:border-slate-800/60">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-md shrink-0">
                    <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                </div>
                <div class="sidebar-brand-text">
                    <h1 class="text-md font-extrabold tracking-tight leading-none text-slate-900 dark:text-white">Dips Gateway</h1>
                    <span class="text-[9px] text-blue-600 dark:text-blue-450 font-bold uppercase tracking-wider block mt-1">Dashboard</span>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto custom-scroll">
                <!-- Group 1 -->
                <div>
                    <span class="sidebar-brand-text block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-3">Utama</span>
                    <div class="space-y-1.5">
                        <a href="{{ route('qris.dashboard') }}" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left {{ request()->routeIs('qris.dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <i data-lucide="layout-grid" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Dashboard</span>
                        </a>
                        <a href="{{ route('qris.transactions') }}" class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left {{ request()->routeIs('qris.transactions') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="flex items-center gap-3.5">
                                <i data-lucide="receipt" class="w-5 h-5"></i>
                                <span class="sidebar-brand-text">Daftar Transaksi</span>
                            </div>
                            @if(isset($qrisAnomalyCount) && $qrisAnomalyCount > 0)
                                <span class="sidebar-brand-text bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">{{ $qrisAnomalyCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('qris.rekonsiliasi') }}" class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left {{ request()->routeIs('qris.rekonsiliasi') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <div class="flex items-center gap-3.5">
                                <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                                <span class="sidebar-brand-text">Rekonsiliasi GoPay</span>
                            </div>
                            @if(isset($qrisAnomalyCount) && $qrisAnomalyCount > 0)
                                <span class="sidebar-brand-text bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">{{ $qrisAnomalyCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('qris.laporan') }}" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left {{ request()->routeIs('qris.laporan') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Laporan & Export</span>
                        </a>
                    </div>
                </div>

                <!-- Group 2 -->
                <div>
                    <span class="sidebar-brand-text block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-3">Lainnya</span>
                    <div class="space-y-1.5">
                        <a href="{{ route('qris.settings') }}" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left {{ request()->routeIs('qris.settings') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                            <i data-lucide="sliders" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Pengaturan Sistem</span>
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Sidebar Collapse toggle -->
            <button id="toggle-sidebar" class="absolute -right-3 top-10 w-6 h-6 rounded-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-sm active:scale-95 transition-all">
                <i data-lucide="chevron-left" id="toggle-icon" class="w-4 h-4 transition-transform duration-300"></i>
            </button>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-100 dark:border-slate-800 shrink-0">
                <!-- Button to main Yomuda ADM panel -->
                <a href="/admin/dashboard" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-blue-600 hover:text-white dark:hover:bg-blue-600 dark:hover:text-white transition-all text-left mb-2">
                    <i data-lucide="arrow-left-right" class="w-4 h-4 shrink-0"></i>
                    <span class="sidebar-brand-text">Kembali ke Yomuda ADM</span>
                </a>
                <a href="{{ route('admin.logout') }}" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-2xl text-red-500 hover:text-red-650 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-left">
                    <i data-lucide="log-out" class="w-5 h-5 shrink-0"></i>
                    <span class="sidebar-brand-text">Keluar</span>
                </a>
            </div>
        </aside>

        <!-- MOBILE DRAWER SIDEBAR -->
        <div id="mobile-sidebar" class="fixed inset-0 z-50 overflow-hidden hidden" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div id="mobile-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0 duration-300"></div>

            <div class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-slate-900 text-slate-750 dark:text-white flex flex-col border-r border-slate-200 dark:border-slate-800 transform -translate-x-full transition-transform duration-300 ease-in-out">
                <div class="h-24 bg-gradient-to-r from-blue-600 to-blue-700 flex items-center justify-between px-6 shrink-0 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/25">
                            <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-sm font-black tracking-wider leading-none uppercase">Dips Gateway</h1>
                            <span class="text-[9px] text-blue-200 font-bold tracking-widest uppercase">Dashboard</span>
                        </div>
                    </div>
                    <button id="close-mobile-sidebar" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                        <i data-lucide="x" class="w-5 h-5 text-white"></i>
                    </button>
                </div>

                <!-- Drawer Links -->
                <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto custom-scroll">
                    <a href="{{ route('qris.dashboard') }}" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-left {{ request()->routeIs('qris.dashboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i> Dashboard
                    </a>
                    <a href="{{ route('qris.transactions') }}" class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-2xl text-left {{ request()->routeIs('qris.transactions') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3.5">
                            <i data-lucide="receipt" class="w-5 h-5"></i> Daftar Transaksi
                        </div>
                        @if(isset($qrisAnomalyCount) && $qrisAnomalyCount > 0)
                            <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">{{ $qrisAnomalyCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('qris.rekonsiliasi') }}" class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold rounded-2xl text-left {{ request()->routeIs('qris.rekonsiliasi') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                        <div class="flex items-center gap-3.5">
                            <i data-lucide="arrow-left-right" class="w-5 h-5"></i> Rekonsiliasi GoPay
                        </div>
                        @if(isset($qrisAnomalyCount) && $qrisAnomalyCount > 0)
                            <span class="bg-red-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">{{ $qrisAnomalyCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('qris.laporan') }}" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-left {{ request()->routeIs('qris.laporan') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i> Laporan & Export
                    </a>
                    <a href="{{ route('qris.settings') }}" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-left {{ request()->routeIs('qris.settings') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-500 hover:text-blue-600 hover:bg-slate-50 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800' }}">
                        <i data-lucide="sliders" class="w-5 h-5"></i> Konfigurasi
                    </a>
                </nav>

                <div class="p-4 border-t border-slate-100 dark:border-slate-800 shrink-0">
                    <a href="/admin/dashboard" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-2xl bg-slate-150 text-slate-700 hover:bg-blue-600 hover:text-white transition-all text-left mb-2">
                        <i data-lucide="arrow-left-right" class="w-4 h-4 shrink-0"></i> Kembali ke Yomuda ADM
                    </a>
                    <a href="{{ route('admin.logout') }}" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-2xl text-red-500 hover:text-red-650 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-left">
                        <i data-lucide="log-out" class="w-5 h-5 shrink-0"></i> Keluar
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN CONTAINER -->
        <div id="main-container" class="flex-grow flex flex-col overflow-hidden">
            
            <!-- HEADER / NAVBAR -->
            <header class="h-24 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 flex items-center justify-between px-8 shrink-0 relative z-30 shadow-sm transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <!-- Mobile Trigger -->
                    <button id="open-mobile-sidebar" class="w-10 h-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-850 flex items-center justify-center text-slate-500 xl:hidden active:scale-95 transition-all">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 dark:text-white">Selamat Datang, {{ Auth::user()->name }}!</h2>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Berikut adalah ringkasan mutasi transaksi gateway Anda hari ini.</p>
                    </div>
                </div>

                <!-- Controls -->
                <div class="flex items-center gap-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-750 hover:text-blue-600 flex items-center justify-center text-slate-500 dark:text-slate-400 active:scale-95 transition-all">
                        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                        <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
                    </button>

                    <!-- Profile Dropdown Container -->
                    <div class="relative z-[9999]">
                        <button id="profile-btn" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-750 overflow-hidden flex items-center justify-center font-bold text-sm text-slate-700 dark:text-slate-300 active:scale-95 transition-all font-mono">
                            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                        </button>
                        <!-- Profile Dropdown Menu -->
                        <div id="profile-dropdown" class="absolute right-0 mt-3 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl py-2 hidden z-[9999]">
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-black text-slate-950 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ Auth::user()->email ?? 'admin@yomudachamps.com' }}</p>
                            </div>
                            <a href="{{ route('qris.settings') }}" class="w-full text-left px-4 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center gap-2 block">
                                <i data-lucide="settings" class="w-3.5 h-3.5 text-slate-400"></i> Pengaturan
                            </a>
                            <hr class="border-slate-100 dark:border-slate-800">
                            <a href="{{ route('admin.logout') }}" class="w-full text-left px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all flex items-center gap-2 block font-semibold">
                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN SCROLLABLE CONTENT -->
            <div class="flex-1 overflow-y-auto p-6 sm:p-8 custom-scroll relative">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize Icons
            lucide.createIcons();

            // Profile Dropdown Toggle Function
            const profileBtn = document.getElementById('profile-btn');
            const profileDropdown = document.getElementById('profile-dropdown');
            if (profileBtn && profileDropdown) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    profileDropdown.classList.toggle('hidden');
                });
                document.addEventListener('click', () => {
                    profileDropdown.classList.add('hidden');
                });
            }

            // Theme Switcher Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            if(themeToggleBtn){
                themeToggleBtn.addEventListener('click', () => {
                    const isDark = document.documentElement.classList.toggle('dark');
                    localStorage.setItem('qris-theme', isDark ? 'dark' : 'light');
                    setTimeout(() => { location.reload(); }, 150);
                });
            }

            // Desktop Sidebar Collapser (FLEXBOX layout: no margin bugs!)
            const toggleSidebarBtn = document.getElementById('toggle-sidebar');
            const desktopSidebar = document.getElementById('desktop-sidebar');
            const toggleIcon = document.getElementById('toggle-icon');

            function applySidebarState() {
                const isCollapsed = localStorage.getItem('qris-sidebar-collapsed') === 'true';
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-collapsed');
                    if (desktopSidebar) desktopSidebar.style.width = '80px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                    document.querySelectorAll('.sidebar-brand-text').forEach(el => el.classList.add('hidden'));
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                    if (desktopSidebar) desktopSidebar.style.width = '256px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(0deg)';
                    document.querySelectorAll('.sidebar-brand-text').forEach(el => el.classList.remove('hidden'));
                }
            }

            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', () => {
                    const currentState = localStorage.getItem('qris-sidebar-collapsed') === 'true';
                    localStorage.setItem('qris-sidebar-collapsed', !currentState);
                    applySidebarState();
                });
            }

            // Mobile Drawer toggle logic
            const openMobileSidebarBtn = document.getElementById('open-mobile-sidebar');
            const closeMobileSidebarBtn = document.getElementById('close-mobile-sidebar');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileBackdrop = document.getElementById('mobile-backdrop');
            const mobileContent = mobileSidebar ? mobileSidebar.querySelector('.transform') : null;

            if (openMobileSidebarBtn && mobileSidebar && mobileBackdrop && mobileContent) {
                openMobileSidebarBtn.addEventListener('click', () => {
                    mobileSidebar.classList.remove('hidden');
                    setTimeout(() => {
                        mobileBackdrop.classList.remove('opacity-0');
                        mobileBackdrop.classList.add('opacity-100');
                        mobileContent.classList.remove('-translate-x-full');
                        mobileContent.classList.add('translate-x-0');
                    }, 50);
                });

                window.closeMobileSidebar = () => {
                    mobileBackdrop.classList.remove('opacity-100');
                    mobileBackdrop.classList.add('opacity-0');
                    mobileContent.classList.remove('translate-x-0');
                    mobileContent.classList.add('-translate-x-full');
                    setTimeout(() => {
                        mobileSidebar.classList.add('hidden');
                    }, 300);
                };

                closeMobileSidebarBtn.addEventListener('click', closeMobileSidebar);
                mobileBackdrop.addEventListener('click', closeMobileSidebar);
            }

            applySidebarState();
        });
    </script>
    @stack('scripts')
</body>
</html>
