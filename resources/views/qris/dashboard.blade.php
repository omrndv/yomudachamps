<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoPay Merchant - Scalify Panel</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;855;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            lime: '#d9f99d',      // Lime accent
                            limeDark: '#bef264',  // Solid active lime
                            darkBg: '#181824',    // Dark Sidebar
                            cardDark: '#201f30',  // Dark stats card
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
            background-color: #f3f4f6; /* Matching the soft gray background in the screenshot */
            transition: background-color 0.3s, color 0.3s;
        }
        .dark body {
            background-color: #0c0a0f;
        }
        /* Collapsed transitions */
        .sidebar-transition {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .content-transition {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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
<body class="min-h-screen flex text-slate-800 dark:text-slate-100 overflow-hidden p-0 sm:p-4">

    <!-- MAIN FRAME CONTAINER (Gives the tablet/window framed look matching the screenshot) -->
    <div class="flex-1 flex bg-[#f3f4f6] dark:bg-[#0c0a0f] sm:rounded-3xl sm:shadow-2xl overflow-hidden border border-slate-200/50 dark:border-slate-800/50">

        <!-- SIDEBAR -->
        <aside id="desktop-sidebar" class="sidebar-transition w-64 bg-[#181824] text-white flex flex-col shrink-0 z-40 hidden xl:flex relative">
            <!-- Sidebar Branding / Logo -->
            <div class="h-24 flex items-center px-8 gap-3 shrink-0">
                <div class="w-10 h-10 bg-[#bef264] rounded-xl flex items-center justify-center shadow-md shrink-0">
                    <i data-lucide="qr-code" class="w-6 h-6 text-[#181824]"></i>
                </div>
                <div class="sidebar-brand-text">
                    <h1 class="text-md font-extrabold tracking-tight leading-none text-white">GoPay Gateway</h1>
                    <span class="text-[9px] text-[#bef264] font-black uppercase tracking-widest block mt-1">Scalify Panel</span>
                </div>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-4 space-y-6 overflow-y-auto custom-scroll">
                
                <!-- Group 1 -->
                <div>
                    <span class="sidebar-brand-text block text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-3">Overview</span>
                    <div class="space-y-1.5">
                        <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left">
                            <i data-lucide="layout-grid" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Dashboard</span>
                        </button>
                        <button onclick="switchTab('payments')" id="nav-payments" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">Payments</span>
                        </button>
                    </div>
                </div>

                <!-- Group 2 -->
                <div>
                    <span class="sidebar-brand-text block text-[10px] font-bold text-slate-500 uppercase tracking-widest px-4 mb-3">Other</span>
                    <div class="space-y-1.5">
                        <button onclick="switchTab('config')" id="nav-config" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl transition-all text-left">
                            <i data-lucide="sliders" class="w-5 h-5"></i>
                            <span class="sidebar-brand-text">System Settings</span>
                        </button>
                    </div>
                </div>
            </nav>

            <!-- Bottom Promo/Update Banner (Matching the rocket banner in the screenshot) -->
            <div class="sidebar-brand-text p-4 m-4 bg-[#bef264] rounded-2xl text-[#181824] flex flex-col items-center text-center relative overflow-hidden group shadow-lg shrink-0">
                <!-- Decorative background elements -->
                <div class="absolute -right-6 -bottom-6 w-20 h-20 bg-white/20 rounded-full blur-xl group-hover:scale-125 transition-all"></div>
                <div class="w-12 h-12 bg-[#181824] rounded-full flex items-center justify-center shadow-md mb-3 text-[#bef264]">
                    <i data-lucide="rocket" class="w-6 h-6 animate-bounce"></i>
                </div>
                <h4 class="text-xs font-black tracking-tight mb-1">New update available</h4>
                <p class="text-[9px] font-semibold text-[#181824]/75 mb-3">Click to upgrade your gateway panel</p>
                <a href="#" class="w-full bg-[#181824] hover:bg-[#201f30] text-white text-[10px] font-bold py-2 px-3 rounded-xl transition-all active:scale-[0.98]">
                    Update!
                </a>
            </div>

            <!-- Sidebar Collapse toggle -->
            <button id="toggle-sidebar" class="absolute -right-3 top-10 w-6 h-6 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-500 hover:text-[#bef264] shadow-sm active:scale-95 transition-all">
                <i data-lucide="chevron-left" id="toggle-icon" class="w-4 h-4 transition-transform duration-300"></i>
            </button>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-slate-800/60 shrink-0">
                <form action="{{ route('qris.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-2xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all text-left">
                        <i data-lucide="log-out" class="w-5 h-5 shrink-0"></i>
                        <span class="sidebar-brand-text">Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MOBILE DRAWER SIDEBAR -->
        <div id="mobile-sidebar" class="fixed inset-0 z-50 overflow-hidden hidden" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div id="mobile-backdrop" class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0 duration-300"></div>

            <div class="fixed inset-y-0 left-0 w-64 bg-[#181824] text-white flex flex-col border-r border-slate-800/60 transform -translate-x-full transition-transform duration-300 ease-in-out">
                <div class="h-24 bg-gradient-to-r from-sky-600 to-sky-700 flex items-center justify-between px-6 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#bef264] rounded-xl flex items-center justify-center shadow-md">
                            <i data-lucide="qr-code" class="w-6 h-6 text-[#181824]"></i>
                        </div>
                        <div>
                            <h1 class="text-sm font-black tracking-wider leading-none uppercase">GoPay Gateway</h1>
                            <span class="text-[9px] text-[#bef264] font-bold tracking-widest uppercase">Scalify Panel</span>
                        </div>
                    </div>
                    <button id="close-mobile-sidebar" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                        <i data-lucide="x" class="w-5 h-5 text-white"></i>
                    </button>
                </div>

                <!-- Drawer Links -->
                <nav class="flex-1 px-4 py-6 space-y-3 overflow-y-auto custom-scroll">
                    <button onclick="switchTab('dashboard'); closeMobileSidebar();" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-slate-400 hover:text-white text-left">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i> Dashboard
                    </button>
                    <button onclick="switchTab('payments'); closeMobileSidebar();" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-slate-400 hover:text-white text-left">
                        <i data-lucide="receipt" class="w-5 h-5"></i> Payments
                    </button>
                    <button onclick="switchTab('config'); closeMobileSidebar();" class="w-full flex items-center gap-3.5 px-4 py-3 text-sm font-semibold rounded-2xl text-slate-400 hover:text-white text-left">
                        <i data-lucide="sliders" class="w-5 h-5"></i> Configuration
                    </button>
                </nav>

                <div class="p-4 border-t border-slate-800/60 shrink-0">
                    <form action="{{ route('qris.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-2xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all text-left">
                            <i data-lucide="log-out" class="w-5 h-5 shrink-0"></i> Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MAIN CONTAINER -->
        <div id="main-container" class="flex-1 flex flex-col overflow-hidden content-transition">
            
            <!-- HEADER / NAVBAR -->
            <header class="h-24 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between px-8 shrink-0 transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <!-- Mobile Trigger -->
                    <button id="open-mobile-sidebar" class="w-10 h-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-850 flex items-center justify-center text-slate-500 xl:hidden active:scale-95 transition-all">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">Welcome back, Admin!</h2>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Here is your transaction summary for today.</p>
                    </div>
                </div>

                <!-- Controls -->
                <div class="flex items-center gap-4">
                    <!-- Search Bar (Matching screenshot) -->
                    <div class="relative hidden lg:block">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-slate-500">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input type="text" placeholder="Search..." readonly
                            class="w-64 bg-slate-100 dark:bg-slate-850 text-slate-900 dark:text-white rounded-full pl-11 pr-4 py-2.5 text-xs border border-transparent focus:outline-none placeholder-slate-450 dark:placeholder-slate-500">
                    </div>

                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="w-10 h-10 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:text-indigo-600 flex items-center justify-center text-slate-500 dark:text-slate-400 active:scale-95 transition-all">
                        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                        <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
                    </button>

                    <!-- Avatar profile display (Matching screenshot) -->
                    <div class="w-10 h-10 rounded-full bg-slate-200 border border-slate-300 dark:border-slate-800 overflow-hidden flex items-center justify-center font-bold text-sm text-slate-700">
                        AD
                    </div>
                </div>
            </header>

            <!-- MAIN SCROLLABLE CONTENT -->
            <div class="flex-1 overflow-y-auto p-6 sm:p-8 custom-scroll relative">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400">
                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                        <p class="text-sm font-semibold">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- ================= TAB: DASHBOARD ================= -->
                <div id="tab-dashboard" class="space-y-8 tab-content">
                    
                    <!-- Stats Summary Grid (Matching the exact look of screenshot) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- First Card: Dark Accent Card (Matching Total Income card in screenshot) -->
                        <div class="bg-[#201f30] text-white border border-[#201f30] rounded-3xl p-6 shadow-md relative overflow-hidden group">
                            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Volume Paid</div>
                                <div class="w-7 h-7 bg-white/10 rounded-lg flex items-center justify-center text-[#bef264]">
                                    <i data-lucide="wallet" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black font-mono">
                                Rp {{ number_format($transactions->where('status', 'PAID')->sum('amount'), 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-[#bef264] mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i> +35.7% settle rate
                            </div>
                        </div>

                        <!-- Card 2: White/Dark Spending Card -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Paid Transactions</div>
                                <div class="w-7 h-7 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-600 dark:text-slate-400">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">
                                {{ $transactions->where('status', 'PAID')->count() }} txs
                            </div>
                            <div class="text-[10px] text-slate-450 dark:text-slate-500 mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i> +45.2% last 7 days
                            </div>
                        </div>

                        <!-- Card 3: Pending Card -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Pending Transactions</div>
                                <div class="w-7 h-7 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg flex items-center justify-center text-yellow-600 dark:text-yellow-450">
                                    <i data-lucide="hourglass" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">
                                {{ $transactions->where('status', 'PENDING')->count() }} txs
                            </div>
                            <div class="text-[10px] text-yellow-600 dark:text-yellow-450 mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Waiting for scan
                            </div>
                        </div>

                        <!-- Card 4: Expired Card -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-start mb-4">
                                <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Expired Transactions</div>
                                <div class="w-7 h-7 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-400">
                                    <i data-lucide="slash" class="w-4 h-4"></i>
                                </div>
                            </div>
                            <div class="text-2xl font-black text-slate-900 dark:text-white">
                                {{ $transactions->where('status', 'EXPIRED')->count() }} txs
                            </div>
                            <div class="text-[10px] text-slate-450 dark:text-slate-550 mt-3 font-semibold flex items-center gap-1">
                                <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Auto clean database
                            </div>
                        </div>
                    </div>

                    <!-- Charts row (Matching the charts in screenshot) -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        <!-- Chart 1: Flex Bar Chart (Mock Transaction Overview) -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Transaction Overview</h3>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1.5 rounded-lg flex items-center gap-1">Monthly <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i></span>
                            </div>
                            
                            <!-- Graph Bars -->
                            <div class="h-48 flex items-end justify-between gap-2.5 pt-4">
                                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                    <div class="w-full bg-[#bef264] rounded-t-lg transition-all" style="height: 65%;"></div>
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550">Jan</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                    <div class="w-full bg-[#bef264]/40 rounded-t-lg transition-all" style="height: 45%;"></div>
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550">Feb</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                    <div class="w-full bg-[#bef264] rounded-t-lg transition-all" style="height: 80%;"></div>
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550">Mar</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                    <div class="w-full bg-[#bef264]/40 rounded-t-lg transition-all" style="height: 35%;"></div>
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550">Apr</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                    <div class="w-full bg-[#bef264] rounded-t-lg transition-all" style="height: 90%;"></div>
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550">May</span>
                                </div>
                                <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end">
                                    <div class="w-full bg-[#bef264]/40 rounded-t-lg transition-all" style="height: 70%;"></div>
                                    <span class="text-[9px] font-bold text-slate-400 dark:text-slate-550">Jun</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chart 2: SVG Area Line Chart -->
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Volume Performance</h3>
                                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1.5 rounded-lg flex items-center gap-1">Weekly <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i></span>
                            </div>
                            
                            <div class="h-48 relative pt-4">
                                <svg class="w-full h-full" viewBox="0 0 300 100" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="gradient-chart" x1="0" y1="0" x2="0" y2="1">
                                            <stop offset="0%" stop-color="#bef264" stop-opacity="0.4" />
                                            <stop offset="100%" stop-color="#bef264" stop-opacity="0" />
                                        </linearGradient>
                                    </defs>
                                    <!-- Area -->
                                    <path d="M 0,80 Q 50,20 100,60 T 200,30 T 300,10 L 300,100 L 0,100 Z" fill="url(#gradient-chart)" />
                                    <!-- Line -->
                                    <path d="M 0,80 Q 50,20 100,60 T 200,30 T 300,10" fill="none" stroke="#bef264" stroke-width="2.5" />
                                </svg>
                                
                                <!-- Days labels -->
                                <div class="flex justify-between text-[9px] font-bold text-slate-400 dark:text-slate-550 mt-2">
                                    <span>Mon</span>
                                    <span>Tue</span>
                                    <span>Wed</span>
                                    <span>Thu</span>
                                    <span>Fri</span>
                                    <span>Sat</span>
                                    <span>Sun</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities Table list -->
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6">
                        <h3 class="text-sm font-extrabold mb-5 flex items-center gap-2 text-slate-900 dark:text-white">
                            <i data-lucide="activity" class="w-4 h-4 text-[#bef264]"></i> Latest Transactions (Live Stream)
                        </h3>
                        <div class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse($transactions->take(5) as $tx)
                                <div class="py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-850 flex items-center justify-center text-slate-500 font-bold text-xs uppercase">
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
                                        <th class="py-4 px-6">Status</th>
                                        <th class="py-4 px-6">Tanggal Dibuat</th>
                                        <th class="py-4 px-6 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                    @forelse($transactions as $tx)
                                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-all cursor-pointer" onclick="openDetailDrawer({{ json_encode($tx) }}, '{{ $tx->team->name ?? 'Tim Terhapus' }}', '{{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}', '{{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}', '{{ $tx->paid_at ? $tx->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }}')">
                                            <td class="py-4 px-6 font-mono text-xs text-sky-600 dark:text-sky-400 font-bold">{{ $tx->trx_id }}</td>
                                            <td class="py-4 px-6">
                                                <div class="font-bold text-slate-900 dark:text-white">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-555 mt-1.5 block">Season: {{ $tx->team->season->name ?? '-' }}</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                                                <span class="text-[10px] text-slate-400 dark:text-slate-555 mt-1.5 block">Kode Unik: +{{ $tx->unique_code }}</span>
                                            </td>
                                            <td class="py-4 px-6">
                                                @if($tx->status === 'PAID')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> PAID
                                                    </span>
                                                @elseif($tx->status === 'PENDING')
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border border-yellow-100 dark:border-yellow-500/20">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> PENDING
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> EXPIRED
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6 text-xs text-slate-500">
                                                {{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                            </td>
                                            <td class="py-4 px-6 text-right" onclick="event.stopPropagation()">
                                                @if($tx->status === 'PENDING')
                                                    <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi ini secara manual?');" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-sm active:scale-[0.98]">
                                                            Settle Manual
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">-</span>
                                                @endif
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
                            <i data-lucide="sliders" class="w-5 h-5 text-sky-500"></i> Pengaturan GoPay Merchant & API
                        </h3>

                        <form action="{{ route('qris.config.update') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Merchant ID</label>
                                    <input type="text" name="merchant_id" value="{{ $config->merchant_id }}" required
                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-sky-500 transition-all font-mono"
                                        placeholder="G572567010">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">GoPay API URL</label>
                                    <input type="url" name="api_url" value="{{ $config->api_url }}" required
                                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-sky-500 transition-all font-mono"
                                        placeholder="https://api.gojekapi.com/v2/transactions">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Token Otorisasi (GoBiz Bearer Token)</label>
                                <input type="password" name="token" 
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-sky-500 transition-all font-mono placeholder-slate-400 dark:placeholder-slate-650"
                                    placeholder="{{ $config->has_token ? '•••••••••••••••••••••••••••••••• (Sudah Tersimpan)' : 'Masukkan token baru' }}">
                                <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5 leading-normal">Kosongkan kolom ini jika Anda tidak ingin memperbarui token yang sudah ada di database.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">String QRIS Statis</label>
                                <textarea name="static_qris" rows="5" required
                                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-[11px] font-mono focus:outline-none focus:border-sky-500 transition-all"
                                    placeholder="00020101021126610014COM.GO-JEK.WWW01189..."></textarea>
                                <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5 leading-normal">Salin mentah string QRIS statis dari outlet GoPay Merchant Anda. String ini akan diparse menggunakan library EMVCo buatan kita untuk nominal dinamis.</p>
                            </div>

                            <button type="submit"
                                class="w-full bg-sky-600 hover:bg-sky-500 text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] mt-4 flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Konfigurasi
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        <!-- PAYMENT DETAIL DRAWER (SIDE SLIDE-OVER) -->
        <div id="detail-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[450px] bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
            <!-- Drawer Header -->
            <div class="h-20 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 shrink-0">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Payment Detail</h3>
                <button onclick="closeDetailDrawer()" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-805 flex items-center justify-center text-slate-500">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Drawer Content -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scroll">
                
                <!-- Amount & Status Badge -->
                <div class="bg-slate-50 dark:bg-slate-950 rounded-2xl p-5 border border-slate-100 dark:border-slate-850 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block">Amount</span>
                        <div id="drawer-amount" class="text-2xl font-black text-slate-900 dark:text-white font-mono mt-1">Rp 0</div>
                    </div>
                    <div id="drawer-status">
                        <!-- Status Badge -->
                    </div>
                </div>

                <!-- Details List -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Payment ID</span>
                        <span id="drawer-id" class="font-mono text-slate-800 dark:text-slate-200 font-bold">-</span>
                    </div>
                    <hr class="border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 dark:text-slate-550 font-bold uppercase">Tim</span>
                        <span id="drawer-team" class="font-bold text-slate-900 dark:text-white">-</span>
                    </div>
                    <hr class="border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Created At</span>
                        <span id="drawer-created" class="text-slate-800 dark:text-slate-200 font-bold">-</span>
                    </div>
                    <hr class="border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 dark:text-slate-550 font-bold uppercase">Expires At</span>
                        <span id="drawer-expires" class="text-slate-800 dark:text-slate-200 font-bold">-</span>
                    </div>
                    <hr class="border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 dark:text-slate-550 font-bold uppercase">Paid At</span>
                        <span id="drawer-paid" class="text-emerald-600 dark:text-emerald-400 font-black">-</span>
                    </div>
                    <hr class="border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">GoPay Reference</span>
                        <span id="drawer-gopay-ref" class="font-mono font-bold text-slate-800 dark:text-slate-200">-</span>
                    </div>
                </div>

                <!-- QRIS Dynamic QR Code Display -->
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl p-5 flex flex-col items-center justify-center bg-slate-50/50 dark:bg-slate-950/50">
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase mb-3">Dynamic QR Code</span>
                    <img id="drawer-qr-img" src="" alt="QR Code QRIS" class="w-40 h-40 object-contain border border-slate-200 dark:border-slate-850 rounded-xl bg-white p-2 mb-3">
                    <div class="w-full">
                        <span class="text-[9px] text-slate-400 dark:text-slate-550 font-bold block mb-1">RAW QRIS STRING</span>
                        <textarea id="drawer-qris-string" readonly rows="3" class="w-full bg-slate-100 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-lg p-2 text-[9px] font-mono text-slate-500 dark:text-slate-400 focus:outline-none"></textarea>
                    </div>
                </div>

                <!-- Manual Settle button in drawer -->
                <div id="drawer-actions-container" class="pt-2">
                    <!-- Action Form Button -->
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

            // Set static QRIS value from config
            const staticQris = @json($config->static_qris);
            if (staticQris) {
                document.querySelector('textarea[name="static_qris"]').value = staticQris;
            }

            // Theme Switcher Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            themeToggleBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('qris-theme', isDark ? 'dark' : 'light');
            });

            // Desktop Sidebar Collapser
            const toggleSidebarBtn = document.getElementById('toggle-sidebar');
            const desktopSidebar = document.getElementById('desktop-sidebar');
            const mainContainer = document.getElementById('main-container');
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
        });

        // Tab switching logic
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Remove active style from all nav buttons
            document.querySelectorAll('nav button').forEach(el => {
                el.classList.remove('bg-[#bef264]', 'text-[#181824]', 'shadow-md');
                el.classList.add('text-slate-400');
            });

            // Show active content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            // Set active style to nav button
            const activeNav = document.getElementById('nav-' + tabId);
            if (activeNav) {
                activeNav.classList.add('bg-[#bef264]', 'text-[#181824]', 'shadow-md');
                activeNav.classList.remove('text-slate-400');
            }

            // Update page title
            const titles = {
                'dashboard': 'Dashboard',
                'payments': 'Payments',
                'config': 'Configuration'
            };
            const pageTitle = document.getElementById('page-title');
            if (pageTitle) pageTitle.innerText = titles[tabId];
        }

        // Open Payment Detail Drawer
        function openDetailDrawer(tx, teamName, createdAt, expiresAt, paidAt) {
            document.getElementById('drawer-id').innerText = tx.id;
            document.getElementById('drawer-team').innerText = teamName;
            document.getElementById('drawer-created').innerText = createdAt + ' WIB';
            document.getElementById('drawer-expires').innerText = expiresAt + ' WIB';
            document.getElementById('drawer-paid').innerText = paidAt !== '-' ? paidAt + ' WIB' : '-';
            document.getElementById('drawer-gopay-ref').innerText = tx.gopay_reference || '-';
            document.getElementById('drawer-amount').innerText = 'Rp ' + Number(tx.amount).toLocaleString('id-ID');
            document.getElementById('drawer-qris-string').value = tx.qris_string;
            
            // Set QR Code Image via API qrserver
            document.getElementById('drawer-qr-img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(tx.qris_string);

            // Set Status Badge
            const statusContainer = document.getElementById('drawer-status');
            if (tx.status === 'PAID') {
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">PAID</span>`;
            } else if (tx.status === 'PENDING') {
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-450 border border-yellow-100 dark:border-yellow-500/20">PENDING</span>`;
            } else {
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-350 border border-slate-200 dark:border-slate-750">EXPIRED</span>`;
            }

            // Set Action Form (Settle Button)
            const actionsContainer = document.getElementById('drawer-actions-container');
            if (tx.status === 'PENDING') {
                actionsContainer.innerHTML = `
                    <form action="/qris-gateway/settle/${tx.trx_id}" method="POST" onsubmit="return confirm('Selesaikan transaksi ini secara manual?');">
                        @csrf
                        <button type="submit" class="w-full bg-[#181824] hover:bg-[#201f30] text-white dark:bg-slate-800 dark:hover:bg-slate-700 font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
                            <i data-lucide="check-square" class="w-4 h-4"></i> Settle Transaksi Secara Manual
                        </button>
                    </form>
                `;
            } else {
                actionsContainer.innerHTML = '';
            }

            // Re-render new Lucide Icons in drawer
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
