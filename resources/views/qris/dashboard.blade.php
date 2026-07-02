<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GoPay Merchant - Scalify Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f4f6;
        }
        .sidebar-gradient {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        }
        .active-nav {
            background-color: rgba(255, 255, 255, 0.15);
            border-left: 4px solid #fff;
        }
        /* Custom scrollbar for drawers */
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.15);
            border-radius: 10px;
        }
    </style>
</head>
<body class="min-h-screen flex text-gray-800">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col shrink-0 border-r border-gray-800">
        <!-- Sidebar Branding -->
        <div class="h-20 sidebar-gradient flex items-center px-6 gap-3">
            <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center border border-white/20">
                <i class="bi bi-credit-card-2-front text-xl"></i>
            </div>
            <div>
                <h1 class="text-sm font-black tracking-tight leading-none">GoPay Merchant</h1>
                <span class="text-[10px] text-sky-200 font-medium">Scalify Panel</span>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-1">
            <button onclick="switchTab('dashboard')" id="nav-dashboard" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-gray-300 hover:text-white hover:bg-white/5 active-nav transition-all text-left">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </button>
            <button onclick="switchTab('payments')" id="nav-payments" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all text-left">
                <i class="bi bi-wallet2"></i> Payments
            </button>
            <button onclick="switchTab('config')" id="nav-config" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-gray-300 hover:text-white hover:bg-white/5 transition-all text-left">
                <i class="bi bi-sliders"></i> Configuration
            </button>
        </nav>

        <!-- Sidebar Footer (Log Out) -->
        <div class="p-4 border-t border-gray-850">
            <form action="{{ route('qris.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-all text-left">
                    <i class="bi bi-box-arrow-left"></i> Log out
                </button>
            </form>
        </div>
    </aside>

    <!-- MAIN BODY -->
    <div class="flex-1 flex flex-col overflow-hidden">
        
        <!-- HEADER -->
        <header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-8 shrink-0">
            <div class="flex items-center gap-4">
                <h2 id="page-title" class="text-xl font-bold text-gray-900">Dashboard</h2>
            </div>

            <!-- Top Header Stats / Pills -->
            <div class="flex items-center gap-3">
                <div class="bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1">
                    <i class="bi bi-clock-history"></i> Poll Active (Dynamic)
                </div>
                <div class="bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded-full flex items-center gap-1">
                    <i class="bi bi-geo-alt"></i> Asia/Jakarta
                </div>
                <div class="bg-sky-50 text-sky-700 text-xs font-bold px-4 py-1.5 rounded-full border border-sky-100 flex items-center gap-1.5">
                    <i class="bi bi-shop text-sm"></i> ID: <span class="font-mono font-bold">{{ $config->merchant_id ?: 'UNCONFIGURED' }}</span>
                </div>
            </div>
        </header>

        <!-- CONTENT SCROLLER -->
        <div class="flex-1 overflow-y-auto p-8 custom-scroll relative">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-700">
                    <i class="bi bi-check-circle-fill text-lg"></i>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <!-- ================= TAB: DASHBOARD ================= -->
            <div id="tab-dashboard" class="space-y-8 tab-content">
                <!-- Stats Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Volume Paid</div>
                        <div class="text-2xl font-black text-gray-900">
                            Rp {{ number_format($transactions->where('status', 'PAID')->sum('amount'), 0, ',', '.') }}
                        </div>
                        <p class="text-[10px] text-emerald-600 mt-1 font-semibold"><i class="bi bi-arrow-up-short"></i> 100% Realtime settle</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Paid Transactions</div>
                        <div class="text-2xl font-black text-gray-900">
                            {{ $transactions->where('status', 'PAID')->count() }}
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">Total transaksi berstatus lunas</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pending Transactions</div>
                        <div class="text-2xl font-black text-gray-900 text-yellow-600">
                            {{ $transactions->where('status', 'PENDING')->count() }}
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">Sedang menunggu pembayaran</p>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Expired Transactions</div>
                        <div class="text-2xl font-black text-gray-900 text-gray-400">
                            {{ $transactions->where('status', 'EXPIRED')->count() }}
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1">Melewati batas waktu transfer</p>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
                    <h3 class="text-md font-bold mb-4 flex items-center gap-2 text-gray-900">
                        <i class="bi bi-clock-history text-gray-400"></i> Aktivitas Pembayaran Terbaru
                    </h3>
                    <div class="divide-y divide-gray-100">
                        @forelse($transactions->take(5) as $tx)
                            <div class="py-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $tx->status === 'PAID' ? 'bg-emerald-50 text-emerald-600' : ($tx->status === 'PENDING' ? 'bg-yellow-50 text-yellow-600' : 'bg-gray-50 text-gray-400') }}">
                                        <i class="bi {{ $tx->status === 'PAID' ? 'bi-check-circle' : ($tx->status === 'PENDING' ? 'bi-hourglass-split' : 'bi-slash-circle') }} text-lg"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 text-sm">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                                        <span class="text-[10px] font-mono text-gray-400">{{ $tx->trx_id }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-black text-sm text-gray-900">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                                    <span class="text-[10px] text-gray-400">{{ $tx->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-400 py-6 text-sm">Belum ada riwayat transaksi.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- ================= TAB: PAYMENTS ================= -->
            <div id="tab-payments" class="space-y-6 tab-content hidden">
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold">
                                <th class="py-4 px-6">ID / Referensi</th>
                                <th class="py-4 px-6">Nama Tim</th>
                                <th class="py-4 px-6">Nominal</th>
                                <th class="py-4 px-6">Status</th>
                                <th class="py-4 px-6">Tanggal Dibuat</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($transactions as $tx)
                                <tr class="hover:bg-gray-50/50 transition-all cursor-pointer" onclick="openDetailDrawer({{ json_encode($tx) }}, '{{ $tx->team->name ?? 'Tim Terhapus' }}', '{{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}', '{{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}', '{{ $tx->paid_at ? $tx->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }}')">
                                    <td class="py-4 px-6 font-mono text-xs text-sky-600 font-semibold">{{ $tx->trx_id }}</td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-900">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                                        <span class="text-[10px] text-gray-400">Season: {{ $tx->team->season->name ?? '-' }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="font-bold text-gray-900">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                                        <span class="text-[10px] text-gray-500">Kode Unik: +{{ $tx->unique_code }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if($tx->status === 'PAID')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> PAID
                                            </span>
                                        @elseif($tx->status === 'PENDING')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">
                                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> PENDING
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> EXPIRED
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-xs text-gray-500">
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
                                            <span class="text-xs text-gray-400 font-medium">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-400 text-sm">
                                        Belum ada transaksi QRIS yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $transactions->links() }}
                </div>
            </div>

            <!-- ================= TAB: CONFIGURATION ================= -->
            <div id="tab-config" class="space-y-6 tab-content hidden">
                <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-8 max-w-3xl mx-auto">
                    <h3 class="text-lg font-bold mb-6 flex items-center gap-2 text-gray-900">
                        <i class="bi bi-sliders text-sky-600"></i> Pengaturan GoPay Merchant & API
                    </h3>

                    <form action="{{ route('qris.config.update') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Merchant ID</label>
                                <input type="text" name="merchant_id" value="{{ $config->merchant_id }}" required
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-sky-500/50 transition-all font-mono"
                                    placeholder="G572567010">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">GoPay API URL</label>
                                <input type="url" name="api_url" value="{{ $config->api_url }}" required
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-sky-500/50 transition-all font-mono"
                                    placeholder="https://api.gojekapi.com/v2/transactions">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Token Otorisasi (GoBiz Bearer Token)</label>
                            <input type="password" name="token" 
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-sky-500/50 transition-all font-mono placeholder-gray-400"
                                placeholder="{{ $config->has_token ? '•••••••••••••••••••••••••••••••• (Sudah Tersimpan)' : 'Masukkan token baru' }}">
                            <p class="text-[10px] text-gray-400 mt-1">Kosongkan kolom ini jika Anda tidak ingin memperbarui token yang sudah ada di database.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">String QRIS Statis</label>
                            <textarea name="static_qris" rows="5" required
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl px-4 py-3 text-[11px] font-mono focus:outline-none focus:border-sky-500/50 transition-all"
                                placeholder="00020101021126610014COM.GO-JEK.WWW01189..."></textarea>
                            <p class="text-[10px] text-gray-400 mt-1">Salin mentah string QRIS statis dari outlet GoPay Merchant Anda. String ini akan diparse menggunakan library EMVCo buatan kita untuk nominal dinamis.</p>
                        </div>

                        <button type="submit"
                            class="w-full bg-sky-600 hover:bg-sky-500 text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] mt-4">
                            Simpan Konfigurasi
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- PAYMENT DETAIL DRAWER (SIDE SLIDE-OVER) -->
    <div id="detail-drawer" class="fixed inset-y-0 right-0 w-[450px] bg-white border-l border-gray-200 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Drawer Header -->
        <div class="h-20 border-b border-gray-200 flex items-center justify-between px-6 shrink-0">
            <h3 class="text-md font-extrabold text-gray-900">Payment Detail</h3>
            <button onclick="closeDetailDrawer()" class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-500">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <!-- Drawer Content -->
        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scroll">
            
            <!-- Amount & Status Badge -->
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-400 font-semibold uppercase">Amount</span>
                    <div id="drawer-amount" class="text-2xl font-black text-gray-900 font-mono">Rp 0</div>
                </div>
                <div id="drawer-status">
                    <!-- Status Badge -->
                </div>
            </div>

            <!-- Details List -->
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-semibold">Payment ID</span>
                    <span id="drawer-id" class="font-mono text-gray-800 font-bold">-</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-semibold">Tim</span>
                    <span id="drawer-team" class="font-bold text-gray-900">-</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-semibold">Created At</span>
                    <span id="drawer-created" class="text-gray-800 font-semibold">-</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-semibold">Expires At</span>
                    <span id="drawer-expires" class="text-gray-800 font-semibold">-</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-semibold">Paid At</span>
                    <span id="drawer-paid" class="text-emerald-600 font-bold">-</span>
                </div>
                <hr class="border-gray-100">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-semibold">GoPay Reference</span>
                    <span id="drawer-gopay-ref" class="font-mono font-bold text-gray-800">-</span>
                </div>
            </div>

            <!-- QRIS Dynamic QR Code Display -->
            <div class="border border-gray-200 rounded-2xl p-5 flex flex-col items-center justify-center bg-gray-50/50">
                <span class="text-xs text-gray-400 font-bold uppercase mb-3">Dynamic QR Code</span>
                <img id="drawer-qr-img" src="" alt="QR Code QRIS" class="w-40 h-40 object-contain border border-gray-200 rounded-xl bg-white p-2 mb-3">
                <div class="w-full">
                    <span class="text-[10px] text-gray-400 font-bold block mb-1">RAW QRIS STRING</span>
                    <textarea id="drawer-qris-string" readonly rows="3" class="w-full bg-gray-100 border border-gray-200 rounded-lg p-2 text-[9px] font-mono text-gray-500 focus:outline-none"></textarea>
                </div>
            </div>

            <!-- Manual Settle button in drawer -->
            <div id="drawer-actions-container" class="pt-2">
                <!-- Action Form Button -->
            </div>
        </div>
    </div>

    <!-- Background Backdrop for Drawer -->
    <div id="drawer-backdrop" onclick="closeDetailDrawer()" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-40 hidden"></div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // Tab switching logic
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Remove active style from all nav buttons
            document.querySelectorAll('nav button').forEach(el => el.classList.remove('active-nav'));

            // Show active content
            document.getElementById('tab-' + tabId).classList.remove('hidden');
            // Set active style to nav button
            document.getElementById('nav-' + tabId).classList.add('active-nav');

            // Update page title
            const titles = {
                'dashboard': 'Dashboard',
                'payments': 'Payments',
                'config': 'Configuration'
            };
            document.getElementById('page-title').innerText = titles[tabId];
        }

        // Set static QRIS value from config
        document.addEventListener('DOMContentLoaded', () => {
            const staticQris = @json($config->static_qris);
            if (staticQris) {
                document.querySelector('textarea[name="static_qris"]').value = staticQris;
            }
        });

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
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">PAID</span>`;
            } else if (tx.status === 'PENDING') {
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">PENDING</span>`;
            } else {
                statusContainer.innerHTML = `<span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">EXPIRED</span>`;
            }

            // Set Action Form (Settle Button)
            const actionsContainer = document.getElementById('drawer-actions-container');
            if (tx.status === 'PENDING') {
                actionsContainer.innerHTML = `
                    <form action="/qris-gateway/settle/${tx.trx_id}" method="POST" onsubmit="return confirm('Selesaikan transaksi ini secara manual?');">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] flex items-center justify-center gap-2">
                            <i class="bi bi-patch-check"></i> Settle Transaksi Secara Manual
                        </button>
                    </form>
                `;
            } else {
                actionsContainer.innerHTML = '';
            }

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
