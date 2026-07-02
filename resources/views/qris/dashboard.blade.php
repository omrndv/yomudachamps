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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
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
            background-color: #020617;
        }
        .sidebar-gradient {
            background: linear-gradient(135deg, #0f172a 0%, #020617 100%);
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
<body class="min-h-screen flex text-slate-800 dark:text-slate-100 overflow-hidden">

    <!-- DESKTOP SIDEBAR -->
    <aside id="desktop-sidebar" class="fixed inset-y-0 left-0 z-40 sidebar-transition w-64 bg-slate-900 dark:bg-slate-950 text-white flex flex-col border-r border-slate-800/60 hidden xl:flex">
        <!-- Sidebar Branding -->
        <div class="h-20 bg-gradient-to-r from-sky-600 to-sky-700 flex items-center px-6 gap-3 shrink-0 relative">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shadow-sm shrink-0">
                <i data-lucide="qr-code" class="w-6 h-6 text-sky-100"></i>
            </div>
            <div class="sidebar-brand-text">
                <h1 class="text-sm font-black tracking-wider leading-none uppercase">GoPay Gateway</h1>
                <span class="text-[9px] text-sky-200 font-bold tracking-widest uppercase">Scalify Panel</span>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scroll">
            <span class="sidebar-brand-text block text-[10px] font-black text-slate-500 uppercase tracking-widest px-4 mb-3">Gateway Menu</span>

            <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all text-left text-slate-400 hover:text-white hover:bg-white/5">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Dashboard</span>
            </button>
            
            <button onclick="switchTab('payments')" id="nav-payments" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all text-left text-slate-400 hover:text-white hover:bg-white/5">
                <i data-lucide="receipt" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Payments</span>
            </button>

            <button onclick="switchTab('config')" id="nav-config" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition-all text-left text-slate-400 hover:text-white hover:bg-white/5">
                <i data-lucide="sliders" class="w-5 h-5"></i>
                <span class="sidebar-brand-text">Configuration</span>
            </button>
        </nav>

        <!-- Sidebar Collapse toggle -->
        <button id="toggle-sidebar" class="absolute -right-3 top-8 w-6 h-6 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-500 hover:text-indigo-600 shadow-sm active:scale-95 transition-all">
            <i data-lucide="chevron-left" id="toggle-icon" class="w-4 h-4 transition-transform duration-300"></i>
        </button>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800/60 shrink-0">
            <form action="{{ route('qris.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all text-left">
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

        <div class="fixed inset-y-0 left-0 w-64 bg-slate-900 dark:bg-slate-950 text-white flex flex-col border-r border-slate-800/60 transform -translate-x-full transition-transform duration-300 ease-in-out">
            <div class="h-20 bg-gradient-to-r from-sky-600 to-sky-700 flex items-center justify-between px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20 shadow-sm">
                        <i data-lucide="qr-code" class="w-6 h-6 text-sky-100"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-black tracking-wider leading-none uppercase">GoPay Gateway</h1>
                        <span class="text-[9px] text-sky-200 font-bold tracking-widest uppercase">Scalify Panel</span>
                    </div>
                </div>
                <button id="close-mobile-sidebar" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center">
                    <i data-lucide="x" class="w-5 h-5 text-white"></i>
                </button>
            </div>

            <!-- Drawer Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto custom-scroll">
                <button onclick="switchTab('dashboard'); closeMobileSidebar();" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-white/5 text-left">
                    <i data-lucide="layout-grid" class="w-5 h-5"></i> Dashboard
                </button>
                <button onclick="switchTab('payments'); closeMobileSidebar();" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-white/5 text-left">
                    <i data-lucide="receipt" class="w-5 h-5"></i> Payments
                </button>
                <button onclick="switchTab('config'); closeMobileSidebar();" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-slate-400 hover:text-white hover:bg-white/5 text-left">
                    <i data-lucide="sliders" class="w-5 h-5"></i> Configuration
                </button>
            </nav>

            <div class="p-4 border-t border-slate-800/60 shrink-0">
                <form action="{{ route('qris.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all text-left">
                        <i data-lucide="log-out" class="w-5 h-5 shrink-0"></i> Log out
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MAIN CONTAINER -->
    <div id="main-container" class="flex-1 flex flex-col overflow-hidden content-transition xl:ml-64">
        
        <!-- NAVBAR / HEADER -->
        <header class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 sm:px-8 shrink-0 sticky top-0 z-30 shadow-sm transition-colors duration-350">
            <div class="flex items-center gap-4">
                <!-- Mobile Trigger -->
                <button id="open-mobile-sidebar" class="w-10 h-10 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500 xl:hidden active:scale-95 transition-all">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                <h2 id="page-title" class="text-lg font-extrabold text-slate-850 dark:text-slate-100">Dashboard</h2>
            </div>

            <!-- Controls -->
            <div class="flex items-center gap-3">
                <div class="hidden md:flex bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-extrabold px-3 py-1.5 rounded-full items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Poll Active (Dynamic)
                </div>
                <div class="hidden sm:flex bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-extrabold px-3 py-1.5 rounded-full items-center gap-1">
                    <i data-lucide="globe" class="w-3.5 h-3.5"></i> Asia/Jakarta
                </div>
                
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:text-indigo-600 flex items-center justify-center text-slate-500 dark:text-slate-400 active:scale-95 transition-all">
                    <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
                    <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
                </button>

                <div class="bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 text-xs font-bold px-4 py-2 rounded-xl border border-sky-100 dark:border-sky-500/20 flex items-center gap-1.5">
                    <i data-lucide="store" class="w-4 h-4"></i> ID: <span class="font-mono font-extrabold">{{ $config->merchant_id ?: 'UNCONFIGURED' }}</span>
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
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-2">Total Volume Paid</div>
                        <div class="text-2xl font-black text-slate-900 dark:text-white">
                            Rp {{ number_format($transactions->where('status', 'PAID')->sum('amount'), 0, ',', '.') }}
                        </div>
                        <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 font-semibold flex items-center gap-0.5">
                            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> 100% Realtime settle
                        </p>
                    </div>

                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-2">Paid Transactions</div>
                        <div class="text-2xl font-black text-slate-900 dark:text-white">
                            {{ $transactions->where('status', 'PAID')->count() }}
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Total transaksi berstatus lunas</p>
                    </div>

                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-2">Pending Transactions</div>
                        <div class="text-2xl font-black text-yellow-600 dark:text-yellow-500">
                            {{ $transactions->where('status', 'PENDING')->count() }}
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Sedang menunggu pembayaran</p>
                    </div>

                    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-slate-450 dark:text-slate-500 uppercase tracking-wider mb-2">Expired Transactions</div>
                        <div class="text-2xl font-black text-slate-400 dark:text-slate-500">
                            {{ $transactions->where('status', 'EXPIRED')->count() }}
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">Melewati batas waktu transfer</p>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6">
                    <h3 class="text-sm font-extrabold mb-5 flex items-center gap-2 text-slate-900 dark:text-white">
                        <i data-lucide="history" class="w-4 h-4 text-slate-400 dark:text-slate-500"></i> Aktivitas Pembayaran Terbaru
                    </h3>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($transactions->take(5) as $tx)
                            <div class="py-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $tx->status === 'PAID' ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : ($tx->status === 'PENDING' ? 'bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-400' : 'bg-slate-50 dark:bg-slate-800 text-slate-400') }}">
                                        <i data-lucide="{{ $tx->status === 'PAID' ? 'check-circle' : ($tx->status === 'PENDING' ? 'hourglass' : 'slash') }}" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white text-sm leading-none">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                                        <span class="text-[10px] font-mono text-slate-400 mt-1.5 block">{{ $tx->trx_id }}</span>
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
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 block">Season: {{ $tx->team->season->name ?? '-' }}</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 block">Kode Unik: +{{ $tx->unique_code }}</span>
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
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
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
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 leading-normal">Kosongkan kolom ini jika Anda tidak ingin memperbarui token yang sudah ada di database.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">String QRIS Statis</label>
                            <textarea name="static_qris" rows="5" required
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-[11px] font-mono focus:outline-none focus:border-sky-500 transition-all"
                                placeholder="00020101021126610014COM.GO-JEK.WWW01189..."></textarea>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1.5 leading-normal">Salin mentah string QRIS statis dari outlet GoPay Merchant Anda. String ini akan diparse menggunakan library EMVCo buatan kita untuk nominal dinamis.</p>
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
            <button onclick="closeDetailDrawer()" class="w-8 h-8 rounded-full hover:bg-slate-105 dark:hover:bg-slate-800 flex items-center justify-center text-slate-500">
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
                    <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Tim</span>
                    <span id="drawer-team" class="font-bold text-slate-900 dark:text-white">-</span>
                </div>
                <hr class="border-slate-100 dark:border-slate-800">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Created At</span>
                    <span id="drawer-created" class="text-slate-800 dark:text-slate-200 font-bold">-</span>
                </div>
                <hr class="border-slate-100 dark:border-slate-800">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Expires At</span>
                    <span id="drawer-expires" class="text-slate-800 dark:text-slate-200 font-bold">-</span>
                </div>
                <hr class="border-slate-100 dark:border-slate-800">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 dark:text-slate-500 font-bold uppercase">Paid At</span>
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
    <div id="drawer-backdrop" onclick="closeDetailDrawer()" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden"></div>

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
                    if (mainContainer) mainContainer.style.marginLeft = '72px';
                    if (toggleIcon) toggleIcon.style.transform = 'rotate(180deg)';
                    document.querySelectorAll('.sidebar-brand-text').forEach(el => el.classList.add('hidden'));
                } else {
                    document.documentElement.classList.remove('sidebar-collapsed');
                    if (desktopSidebar) desktopSidebar.style.width = '256px';
                    if (mainContainer) mainContainer.style.marginLeft = '256px';
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
                el.classList.remove('bg-white/10', 'text-white', 'border-l-4', 'border-sky-500');
                el.classList.add('text-slate-400');
            });

            // Show active content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            // Set active style to nav button
            const activeNav = document.getElementById('nav-' + tabId);
            if (activeNav) {
                activeNav.classList.add('bg-white/10', 'text-white', 'border-l-4', 'border-sky-500');
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
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border border-yellow-100 dark:border-yellow-500/20">PENDING</span>`;
            } else {
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-350 border border-slate-200 dark:border-slate-750">EXPIRED</span>`;
            }

            // Set Action Form (Settle Button)
            const actionsContainer = document.getElementById('drawer-actions-container');
            if (tx.status === 'PENDING') {
                actionsContainer.innerHTML = `
                    <form action="/qris-gateway/settle/${tx.trx_id}" method="POST" onsubmit="return confirm('Selesaikan transaksi ini secara manual?');">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
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
