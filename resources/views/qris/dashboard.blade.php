<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dips Gateway - Dashboard</title>
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
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
    </style>
</head>
<body class="min-h-screen flex text-slate-800 dark:text-slate-100 overflow-hidden p-0">

    <!-- FULL SCREEN FRAME CONTAINER (No outer border/padding, fits perfectly like TriPay) -->
    <div class="flex-grow flex h-screen bg-[#f8fafc] dark:bg-[#0c0a0f] overflow-hidden">

        <!-- SIDEBAR (Clean White Aesthetic like TriPay, No fixed positioning to prevent margin gap) -->
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
                        <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left">
                            <i data-lucide="layout-grid" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Dashboard</span>
                        </button>
                        <button onclick="switchTab('payments')" id="nav-payments" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Daftar Transaksi</span>
                        </button>
                    </div>
                </div>

                <!-- Group 2 -->
                <div>
                    <span class="sidebar-brand-text block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-3">Lainnya</span>
                    <div class="space-y-1.5">
                        <button onclick="switchTab('config')" id="nav-config" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left">
                            <i data-lucide="sliders" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Pengaturan Sistem</span>
                        </button>
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
                    <button onclick="switchTab('dashboard'); closeMobileSidebar();" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-slate-500 hover:text-blue-600 text-left">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i> Dashboard
                    </button>
                    <button onclick="switchTab('payments'); closeMobileSidebar();" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-slate-500 hover:text-blue-600 text-left">
                        <i data-lucide="receipt" class="w-5 h-5"></i> Daftar Transaksi
                    </button>
                    <button onclick="switchTab('config'); closeMobileSidebar();" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-slate-500 hover:text-blue-600 text-left">
                        <i data-lucide="sliders" class="w-5 h-5"></i> Konfigurasi
                    </button>
                </nav>

                <div class="p-4 border-t border-slate-100 dark:border-slate-800 shrink-0">
                    <a href="/admin/dashboard" class="w-full flex items-center gap-3 px-4 py-3 text-xs font-bold rounded-2xl bg-slate-155 text-slate-700 hover:bg-blue-600 hover:text-white transition-all text-left mb-2">
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
                        <h2 class="text-xl font-black text-slate-900 dark:text-white">Selamat Datang, Admin!</h2>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Berikut adalah ringkasan mutasi transaksi gateway Anda hari ini.</p>
                    </div>
                </div>

                <!-- Controls -->
                <div class="flex items-center gap-4">
                    <!-- Search Bar (Functional) -->
                    <div class="relative hidden lg:block">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-slate-500">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="search-input" placeholder="Cari transaksi..." 
                            class="w-64 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white border border-slate-200 dark:border-slate-700/85 rounded-full pl-11 pr-4 py-2.5 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all placeholder-slate-450 dark:placeholder-slate-500">
                    </div>

                    <!-- Notification bell (Functional) -->
                    <div class="relative z-[999]">
                        <button id="notif-btn" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-400 active:scale-95 transition-all relative">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if($globalStats->pending_count > 0)
                                <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white rounded-full flex items-center justify-center text-[8px] font-black animate-bounce shadow-sm">
                                    {{ $globalStats->pending_count }}
                                </span>
                            @endif
                        </button>
                        <!-- Notif Dropdown (Fixed Z-Index & Overlay) -->
                        <div id="notif-dropdown" class="absolute right-0 mt-3 w-80 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl py-3 hidden z-[9999]">
                            <div class="px-4 pb-2 border-b border-slate-100 dark:border-slate-800">
                                <h4 class="text-xs font-black text-slate-950 dark:text-white">Pemberitahuan Transaksi Realtime</h4>
                            </div>
                            <div class="max-h-64 overflow-y-auto custom-scroll py-2 px-3 space-y-2">
                                @forelse($transactions->take(5) as $tx)
                                    <div class="p-2.5 rounded-xl border border-slate-100 dark:border-slate-800/80 flex items-start gap-2 bg-slate-50/50 dark:bg-slate-850/50 text-[10px]">
                                        @if($tx->status === 'PAID')
                                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-slate-200">Pembayaran Berhasil</p>
                                                <p class="text-slate-450 dark:text-slate-400 mt-0.5 leading-relaxed">Tim <b>{{ $tx->team->name ?? 'Tim' }}</b> baru saja melunasi pendaftaran Rp {{ number_format($tx->amount, 0, ',', '.') }}.</p>
                                            </div>
                                        @elseif($tx->status === 'PENDING')
                                            <i data-lucide="clock" class="w-4 h-4 text-blue-500 mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-bold text-slate-900 dark:text-slate-200">Tagihan Baru Dibuat</p>
                                                <p class="text-slate-450 dark:text-slate-400 mt-0.5 leading-relaxed">Tim <b>{{ $tx->team->name ?? 'Tim' }}</b> mendaftar dengan tagihan pending Rp {{ number_format($tx->amount, 0, ',', '.') }}.</p>
                                            </div>
                                        @else
                                            <i data-lucide="alert-triangle" class="w-4 h-4 text-slate-450 mt-0.5 shrink-0"></i>
                                            <div>
                                                <p class="font-bold text-slate-700 dark:text-slate-400">Transaksi Kedaluwarsa</p>
                                                <p class="text-slate-450 dark:text-slate-500 mt-0.5 leading-relaxed">Transaksi tim <b>{{ $tx->team->name ?? 'Tim' }}</b> telah melewati batas waktu pembayaran.</p>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="py-4 text-center text-slate-400 text-[10px] font-medium">
                                        Tidak ada pemberitahuan baru.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="w-10 h-10 rounded-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-750 hover:text-blue-600 flex items-center justify-center text-slate-500 dark:text-slate-400 active:scale-95 transition-all">
                        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                        <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
                    </button>

                    <!-- Profile Dropdown Container (Z-Index Fixed Overlay) -->
                    <div class="relative z-[9999]">
                        <button id="profile-btn" class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-750 overflow-hidden flex items-center justify-center font-bold text-sm text-slate-700 dark:text-slate-350 active:scale-95 transition-all">
                            AD
                        </button>
                        <!-- Profile Dropdown Menu -->
                        <div id="profile-dropdown" class="absolute right-0 mt-3 w-56 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl py-2 hidden z-[9999]">
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-black text-slate-950 dark:text-white">Admin Gateway</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">superadmin@gate.com</p>
                            </div>
                            <button onclick="switchTab('config')" class="w-full text-left px-4 py-2.5 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 flex items-center gap-2">
                                <i data-lucide="settings" class="w-3.5 h-3.5 text-slate-400"></i> Pengaturan
                            </button>
                            <hr class="border-slate-100 dark:border-slate-800">
                            <a href="{{ route('admin.logout') }}" class="w-full text-left px-4 py-2.5 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 flex items-center gap-2 block">
                                <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN SCROLLABLE CONTENT -->
            <div class="flex-1 overflow-y-auto p-6 sm:p-8 custom-scroll relative">
                
                <!-- ================= TAB: DASHBOARD ================= -->
                <div id="tab-dashboard" class="space-y-8 tab-content">
                    
                    <!-- Stats Summary Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- First Card: Dark Blue Theme Card -->
                        <div class="bg-blue-600 dark:bg-blue-700 text-white rounded-3xl p-6 shadow-md relative overflow-hidden group">
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-blue-100 uppercase tracking-wider">Total Volume Sukses</div>
                                <div class="w-7 h-7 bg-white/10 rounded-lg flex items-center justify-center text-white">
                                    <i data-lucide="wallet" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black font-mono">
                                Rp {{ number_format($globalStats->total_volume, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-blue-100 mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> 100% Settle rate
                            </div>
                        </div>

                        <!-- Card 2: White/Dark Spending Card -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-555 uppercase tracking-wider">Transaksi Sukses</div>
                                <div class="w-7 h-7 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-450">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">
                                {{ $globalStats->paid_count }} Tx
                            </div>
                            <div class="text-[10px] text-slate-450 dark:text-slate-500 mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Transaksi terbayar lunas
                            </div>
                        </div>

                        <!-- Card 3: Pending Card -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-555 uppercase tracking-wider">Transaksi Pending</div>
                                <div class="w-7 h-7 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg flex items-center justify-center text-yellow-600 dark:text-yellow-450">
                                    <i data-lucide="hourglass" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">
                                {{ $globalStats->pending_count }} Tx
                            </div>
                            <div class="text-[10px] text-yellow-600 dark:text-yellow-450 mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu pembayaran
                            </div>
                        </div>

                        <!-- Card 4: Expired Card -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-555 uppercase tracking-wider">Transaksi Kedaluwarsa</div>
                                <div class="w-7 h-7 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-450">
                                    <i data-lucide="slash" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">
                                {{ $globalStats->expired_count }} Tx
                            </div>
                            <div class="text-[10px] text-slate-450 dark:text-slate-500 mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Melewati batas waktu
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row with Chart.js -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Chart 1: Bar Chart (Monthly Overview) -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-6">Analisis Transaksi Bulanan</h3>
                            <div class="h-56 relative w-full">
                                <canvas id="monthlyChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart 2: Line Chart (Weekly Volume) -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-6">Performa Transaksi Mingguan</h3>
                            <div class="h-56 relative w-full">
                                <canvas id="weeklyChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities Table list -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6">
                        <h3 class="text-sm font-extrabold mb-5 flex items-center gap-2 text-slate-900 dark:text-white">
                            <i data-lucide="activity" class="w-4 h-4 text-blue-600"></i> Mutasi Transaksi Terbaru (Live Stream)
                        </h3>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
                            @forelse($transactions->take(5) as $tx)
                                <div class="py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold text-xs uppercase">
                                            {{ substr($tx->team->name ?? 'T', 0, 2) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white text-sm leading-none">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                                            <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500 mt-1.5 block">{{ $tx->trx_id }}</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-black text-sm text-slate-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 block">{{ $tx->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-slate-400 py-6 text-sm">Belum ada riwayat transaksi.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- ================= TAB: PAYMENTS ================= -->
                <div id="tab-payments" class="space-y-6 tab-content hidden">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800 text-slate-450 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                        <th class="py-4 px-6">ID / Referensi</th>
                                        <th class="py-4 px-6">Nama Tim</th>
                                        <th class="py-4 px-6">Nominal</th>
                                        <th class="py-4 px-6">Batas Pembayaran</th>
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($transactions as $tx)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-all cursor-pointer" onclick="openDetailDrawer({{ json_encode($tx) }}, '{{ $tx->team->name ?? 'Tim Terhapus' }}', '{{ $tx->team->email ?? '-' }}', '{{ $tx->team->phone ?? '-' }}', '{{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }}', '{{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }}', '{{ $tx->paid_at ? $tx->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') : '-' }}', '{{ $tx->team->season->name ?? '-' }}')">
                                            <td class="py-4 px-6 font-mono text-xs text-sky-600 dark:text-sky-400 font-bold">{{ $tx->trx_id }}</td>
                                            <td class="py-4 px-6">
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 block">Season: {{ $tx->team->season->name ?? '-' }}</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 block">Kode Unik: +{{ $tx->unique_code }}</span>
                                            </td>
                                            <td class="py-4 px-6 text-xs font-semibold">
                                                @if($tx->status === 'PENDING')
                                                    <!-- Countdown Timer -->
                                                    <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1 countdown-timer" data-expires="{{ $tx->expires_at->timestamp }}">
                                                        <i data-lucide="clock" class="w-3.5 h-3.5 shrink-0"></i> Menghitung...
                                                    </span>
                                                @else
                                                    <span class="text-slate-400 dark:text-slate-500">
                                                        {{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6">
                                                @if($tx->status === 'PAID')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> PAID
                                                    </span>
                                                @elseif($tx->status === 'PENDING')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-450 border border-yellow-100 dark:border-yellow-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> PENDING
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-450 border border-slate-200 dark:border-slate-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> EXPIRED
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-right space-x-2" onclick="event.stopPropagation()">
                                                @if($tx->status === 'PENDING')
                                                    <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi ini secara manual?');" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-sm active:scale-[0.98]">
                                                            Settle Manual
                                                        </button>
                                                    </form>
                                                @endif

                                                <!-- Delete Button -->
                                                <form action="{{ route('qris.delete', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini dari database?');" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-50 text-red-650 hover:bg-red-500 hover:text-white dark:bg-red-950/20 dark:text-red-400 dark:hover:bg-red-650 text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-sm active:scale-[0.98]">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                                                Belum ada transaksi QRIS yang tercatat.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                </div>

                <!-- ================= TAB: CONFIGURATION ================= -->
                <div id="tab-config" class="space-y-6 tab-content hidden">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6 sm:p-8 max-w-3xl mx-auto transition-colors duration-300">
                        <h3 class="text-md font-extrabold mb-6 flex items-center gap-2 text-slate-900 dark:text-white">
                            <i data-lucide="sliders" class="w-5 h-5 text-blue-500"></i> Pengaturan GoPay Merchant & API
                        </h3>

                        <form action="{{ route('qris.config.update') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Merchant ID</label>
                                    <input type="text" name="merchant_id" value="{{ $config->merchant_id }}" required
                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all font-mono"
                                        placeholder="G572567010">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">GoPay API URL</label>
                                    <input type="url" name="api_url" value="{{ $config->api_url }}" required
                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all font-mono"
                                        placeholder="https://api.gojekapi.com/v2/transactions">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Token Otorisasi (GoBiz Bearer Token)</label>
                                <input type="password" name="token" 
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all font-mono placeholder-slate-400 dark:placeholder-slate-650"
                                    placeholder="{{ $config->has_token ? '•••••••••••••••••••••••••••••••• (Sudah Tersimpan)' : 'Masukkan token baru' }}">
                                <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5 leading-normal">Kosongkan kolom ini jika Anda tidak ingin memperbarui token yang sudah ada di database.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">String QRIS Statis</label>
                                <textarea name="static_qris" rows="5" required
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-[11px] font-mono focus:outline-none focus:border-blue-500 transition-all"
                                    placeholder="00020101021126610014COM.GO-JEK.WWW01189..."></textarea>
                                <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5 leading-normal">Salin mentah string QRIS statis dari outlet GoPay Merchant Anda. String ini akan diparse menggunakan library EMVCo buatan kita untuk nominal dinamis.</p>
                            </div>

                            <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] mt-4 flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Konfigurasi
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- PAYMENT DETAIL DRAWER (SIDE SLIDE-OVER - CLEAN SAAS LOOK MATCHING REFERENCED SCREENSHOT) -->
        <div id="detail-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[600px] bg-[#f8fafc] dark:bg-slate-950 border-l border-slate-200 dark:border-slate-850 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <!-- Drawer Header -->
            <div class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 shrink-0">
                <div class="flex items-center gap-2 text-slate-800 dark:text-white">
                    <button onclick="closeDetailDrawer()" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </button>
                    <h3 class="text-md font-extrabold">Detail Transaksi</h3>
                </div>
                <div id="drawer-header-actions">
                    <!-- Action Form Button -->
                </div>
            </div>

            <!-- Drawer Content (Structured like TriPay details page) -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scroll">
                
                <!-- Summary Card -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-6">
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block">No Referensi</span>
                        <div id="drawer-summary-id" class="text-xs font-mono font-bold text-slate-800 dark:text-slate-200 mt-1.5 break-all">-</div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block">Jumlah Bayar</span>
                        <div id="drawer-summary-amount" class="text-sm font-black text-slate-900 dark:text-white mt-1.5">-</div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block">Status</span>
                        <div id="drawer-summary-status" class="mt-1.5">
                            <!-- Status Badge -->
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block">Channel</span>
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1.5">QRIS</div>
                    </div>
                </div>

                <!-- Detail Pembayaran Table -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                    <h4 class="text-xs font-black text-slate-950 dark:text-white uppercase tracking-wider mb-4">Detail Pembayaran</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Nama Merchant</span>
                            <span class="font-bold text-slate-850 dark:text-slate-200">Yomuda Championship</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Jumlah Dibayar</span>
                            <span id="drawer-det-amount" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">No. Ref. Merchant</span>
                            <span id="drawer-det-ref" class="font-bold text-slate-850 dark:text-slate-200 font-mono">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Biaya Merchant</span>
                            <span class="font-bold text-slate-850 dark:text-slate-200">Rp 0</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Tanggal Request</span>
                            <span id="drawer-det-created" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Biaya Pelanggan</span>
                            <span id="drawer-det-customer-fee" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Batas Pembayaran</span>
                            <span id="drawer-det-expires" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Total Biaya</span>
                            <span id="drawer-det-total" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Dibayar Pada</span>
                            <span id="drawer-det-paid" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold">Status Kliring</span>
                            <span id="drawer-det-clearing" class="font-bold">-</span>
                        </div>
                    </div>
                </div>

                <!-- Informasi Pelanggan -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                    <h4 class="text-xs font-black text-slate-950 dark:text-white uppercase tracking-wider mb-4">Informasi Pelanggan</h4>
                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-3 py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold col-span-1">Nama</span>
                            <span id="drawer-cust-name" class="font-bold text-slate-850 dark:text-slate-200 col-span-2">-</span>
                        </div>
                        <div class="grid grid-cols-3 py-2 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-400 font-semibold col-span-1">Email</span>
                            <span id="drawer-cust-email" class="font-bold text-slate-850 dark:text-slate-200 col-span-2">-</span>
                        </div>
                        <div class="grid grid-cols-3 py-2">
                            <span class="text-slate-400 font-semibold col-span-1">Telepon</span>
                            <span id="drawer-cust-phone" class="font-bold text-slate-850 dark:text-slate-200 col-span-2">-</span>
                        </div>
                    </div>
                </div>

                <!-- Detail Pesanan -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
                    <h4 class="text-xs font-black text-slate-950 dark:text-white uppercase tracking-wider mb-4">Detail Pesanan</h4>
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800 text-slate-450 dark:text-slate-400 font-bold">
                                <th class="py-2.5 px-4">Nama Produk</th>
                                <th class="py-2.5 px-4 text-center">Jumlah</th>
                                <th class="py-2.5 px-4 text-right">Harga</th>
                                <th class="py-2.5 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr>
                                <td id="drawer-order-product" class="py-3 px-4 font-bold text-slate-850 dark:text-slate-205">-</td>
                                <td class="py-3 px-4 text-center font-semibold text-slate-800 dark:text-slate-200">1</td>
                                <td id="drawer-order-price" class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">-</td>
                                <td id="drawer-order-subtotal" class="py-3 px-4 text-right font-bold text-slate-800 dark:text-slate-200">-</td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 font-bold text-slate-850 dark:text-slate-205">Biaya Transaksi Pelanggan (Kode Unik)</td>
                                <td class="py-3 px-4 text-center font-semibold text-slate-800 dark:text-slate-200">1</td>
                                <td id="drawer-order-fee-price" class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">-</td>
                                <td id="drawer-order-fee-subtotal" class="py-3 px-4 text-right font-bold text-slate-800 dark:text-slate-200">-</td>
                            </tr>
                            <tr class="font-extrabold text-slate-950 dark:text-white bg-slate-50/50 dark:bg-slate-800/20">
                                <td colspan="3" class="py-3 px-4 text-right text-xs">Total Pembayaran</td>
                                <td id="drawer-order-grandtotal" class="py-3 px-4 text-right text-xs font-black">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- QRIS Dynamic QR Code Display -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-3xl p-5 flex flex-col items-center justify-center bg-white dark:bg-slate-900 shadow-sm">
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase mb-3">Dynamic QR Code (Scan to Pay)</span>
                    <img id="drawer-qr-img" src="" alt="QR Code QRIS" class="w-44 h-44 object-contain border border-slate-200 dark:border-slate-850 rounded-xl bg-white p-2.5 mb-4">
                    <div class="w-full">
                        <span class="text-[9px] text-slate-400 dark:text-slate-550 font-bold block mb-1">RAW QRIS STRING</span>
                        <textarea id="drawer-qris-string" readonly rows="3" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-2 text-[9px] font-mono text-slate-500 dark:text-slate-400 focus:outline-none"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Background Backdrop for Drawer -->
        <div id="drawer-backdrop" onclick="closeDetailDrawer()" class="fixed inset-0 bg-black/45 backdrop-blur-sm z-40 hidden"></div>

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

            // Notification Dropdown Toggle Function & Red badge removal
            const notifBtn = document.getElementById('notif-btn');
            const notifDropdown = document.getElementById('notif-dropdown');
            if (notifBtn && notifDropdown) {
                notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                    // Hapus badge merah saat pemberitahuan dibaca
                    const badge = notifBtn.querySelector('span');
                    if (badge) {
                        badge.remove();
                    }
                });
                document.addEventListener('click', () => {
                    notifDropdown.classList.add('hidden');
                });
            }

            // Search Bar Filter Table
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    
                    // Automatically switch to Payments Tab if search is active
                    if (query.length > 0) {
                        switchTab('payments');
                    }
                    
                    const rows = document.querySelectorAll('#tab-payments tbody tr');
                    rows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        if (text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Theme Switcher Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            themeToggleBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('qris-theme', isDark ? 'dark' : 'light');
                // Reload page to apply new theme grid/text colors to Chart.js
                setTimeout(() => { location.reload(); }, 150);
            });

            // Desktop Sidebar Collapser (FLEXBOX layout: no margin bugs!)
            const toggleSidebarBtn = document.getElementById('toggle-sidebar');
            const desktopSidebar = document.getElementById('desktop-sidebar');
            const toggleIcon = document.getElementById('toggle-icon');

            function applySidebarState() {
                const isCollapsed = localStorage.getItem('qris-sidebar-collapsed') === 'true';
                if (isCollapsed) {
                    document.documentElement.classList.add('sidebar-collapsed');
                    if (desktopSidebar) desktopSidebar.style.width = '72px';
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
            switchTab('dashboard');

            // ----------------------------------------------------
            // INITIALIZE CHART.JS (With Beautiful Theme Integration)
            // ----------------------------------------------------
            const isDark = document.documentElement.classList.contains('dark');
            const labelColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? '#334155' : '#f1f5f9';

            // 1. Bar Chart (Monthly)
            const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
            new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'Transaksi',
                        data: @json($monthlyCounts),
                        backgroundColor: '#2563eb', // Blue Navy/Royal
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            grid: { color: gridColor },
                            ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                        }
                    }
                }
            });

            // 2. Line Chart (Weekly)
            const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
            new Chart(ctxWeekly, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                        label: 'Settle',
                        data: @json($weeklyCounts),
                        borderColor: '#2563eb',
                        borderWidth: 3,
                        pointBackgroundColor: '#2563eb',
                        pointHoverRadius: 6,
                        tension: 0.35,
                        fill: true,
                        backgroundColor: 'rgba(37, 99, 235, 0.08)'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutBack'
                    },
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            grid: { color: gridColor },
                            ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                        }
                    }
                }
            });

            // ----------------------------------------------------
            // COUNTDOWN TIMER LOGIC (Untuk Setiap Row Pending)
            // ----------------------------------------------------
            const timerElements = document.querySelectorAll('.countdown-timer');
            
            function updateCountdown() {
                const now = Math.floor(Date.now() / 1000);
                timerElements.forEach(el => {
                    const expiresTimestamp = parseInt(el.getAttribute('data-expires'));
                    const diff = expiresTimestamp - now;
                    
                    if (diff <= 0) {
                        el.innerHTML = `<span class="text-red-500 font-bold uppercase text-[9px]"><i data-lucide="alert-circle" class="w-3.5 h-3.5 inline"></i> Kedaluwarsa</span>`;
                        lucide.createIcons();
                    } else {
                        const mins = Math.floor(diff / 60);
                        const secs = diff % 60;
                        el.innerHTML = `<i data-lucide="clock" class="w-3.5 h-3.5 shrink-0 inline"></i> ${mins}m ${secs}s`;
                    }
                });
            }
            
            if (timerElements.length > 0) {
                setInterval(updateCountdown, 1000);
                updateCountdown();
            }
        });

        // Tab switching logic
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Remove active style from all nav buttons
            document.querySelectorAll('nav button').forEach(el => {
                el.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
                el.classList.add('text-slate-500', 'hover:text-blue-600', 'hover:bg-slate-50', 'dark:text-slate-400', 'dark:hover:text-white', 'dark:hover:bg-slate-800');
            });

            // Show active content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            // Set active style to nav button
            const activeNav = document.getElementById('nav-' + tabId);
            if (activeNav) {
                activeNav.classList.add('bg-blue-600', 'text-white', 'shadow-md');
                activeNav.classList.remove('text-slate-500', 'hover:text-blue-600', 'hover:bg-slate-50', 'dark:text-slate-400', 'dark:hover:text-white', 'dark:hover:bg-slate-800');
            }

            // Update page title
            const titles = {
                'dashboard': 'Dashboard',
                'payments': 'Daftar Transaksi',
                'config': 'Konfigurasi'
            };
            const pageTitle = document.getElementById('page-title');
            if (pageTitle) pageTitle.innerText = titles[tabId];
        }

        // Open Payment Detail Drawer (Structured like TriPay details)
        function openDetailDrawer(tx, custName, custEmail, custPhone, createdAt, expiresAt, paidAt, seasonName) {
            
            // Format amounts
            const amountFormatted = 'Rp ' + Number(tx.amount).toLocaleString('id-ID');
            const baseAmountFormatted = 'Rp ' + Number(tx.base_amount).toLocaleString('id-ID');
            const uniqueFeeFormatted = 'Rp ' + Number(tx.unique_code).toLocaleString('id-ID');

            // Set Header Settle Action Button
            const actionsContainer = document.getElementById('drawer-header-actions');
            if (tx.status === 'PENDING') {
                actionsContainer.innerHTML = `
                    <form action="/qris-gateway/settle/${tx.trx_id}" method="POST" onsubmit="return confirm('Selesaikan transaksi ini secara manual?');">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-xl text-xs transition-all shadow-md active:scale-[0.98] flex items-center gap-1.5">
                            <i data-lucide="check" class="w-4 h-4"></i> Settle Manual
                        </button>
                    </form>
                `;
            } else {
                actionsContainer.innerHTML = '';
            }

            // Fill Summary Card
            document.getElementById('drawer-summary-id').innerText = tx.trx_id;
            document.getElementById('drawer-summary-amount').innerText = amountFormatted;
            
            const summaryStatus = document.getElementById('drawer-summary-status');
            const clearingStatus = document.getElementById('drawer-det-clearing');
            if (tx.status === 'PAID') {
                summaryStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-100">Dibayar</span>`;
                clearingStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-100">Selesai (${paidAt})</span>`;
            } else if (tx.status === 'PENDING') {
                summaryStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-yellow-50 text-yellow-700 border border-yellow-100">Pending</span>`;
                clearingStatus.innerHTML = `<span class="text-yellow-600 dark:text-yellow-450 font-bold">Menunggu Pembayaran</span>`;
            } else {
                summaryStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">Kedaluwarsa</span>`;
                clearingStatus.innerHTML = `<span class="text-slate-400 font-bold">Gagal</span>`;
            }

            // Fill Detail Pembayaran
            document.getElementById('drawer-det-amount').innerText = amountFormatted;
            document.getElementById('drawer-det-ref').innerText = tx.trx_id;
            document.getElementById('drawer-det-created').innerText = createdAt + ' WIB';
            document.getElementById('drawer-det-customer-fee').innerText = uniqueFeeFormatted;
            document.getElementById('drawer-det-expires').innerText = expiresAt + ' WIB';
            document.getElementById('drawer-det-total').innerText = amountFormatted;
            document.getElementById('drawer-det-paid').innerText = paidAt !== '-' ? paidAt + ' WIB' : '-';

            // Fill Customer Info
            document.getElementById('drawer-cust-name').innerText = custName;
            document.getElementById('drawer-cust-email').innerText = custEmail;
            document.getElementById('drawer-cust-phone').innerText = custPhone;

            // Fill Order Details
            document.getElementById('drawer-order-product').innerText = `Registrasi ${seasonName}`;
            document.getElementById('drawer-order-price').innerText = baseAmountFormatted;
            document.getElementById('drawer-order-subtotal').innerText = baseAmountFormatted;
            document.getElementById('drawer-order-fee-price').innerText = uniqueFeeFormatted;
            document.getElementById('drawer-order-fee-subtotal').innerText = uniqueFeeFormatted;
            document.getElementById('drawer-order-grandtotal').innerText = amountFormatted;

            // Fill QR Code
            document.getElementById('drawer-qr-img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(tx.qris_string);
            document.getElementById('drawer-qris-string').value = tx.qris_string;

            // Re-render Lucide Icons in drawer
            lucide.createIcons();

            // Slide in Drawer & show Backdrop
            document.getElementById('detail-drawer').classList.remove('translate-x-full');
            document.getElementById('drawer-backdrop').classList.remove('hidden');
        }

        // Close Drawer
        function closeDetailDrawer() {
            document.getElementById('detail-drawer').classList.add('translate-x-full');
            document.getElementById('drawer-backdrop').classList.add('hidden');
        }
    </script>
</body>
</html>
