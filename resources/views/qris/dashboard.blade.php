<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin QRIS Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #111827, #030712);
        }
    </style>
</head>
<body class="min-h-screen text-gray-100 flex flex-col">
    <!-- Navbar -->
    <header class="border-b border-gray-800 bg-gray-950/60 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center border border-blue-500/20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-md font-extrabold tracking-tight">QRIS Gateway</h1>
                    <p class="text-xs text-gray-500">GoPay Merchant Poller</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-400 hover:text-white transition-colors">
                    Lihat Website
                </a>
                <form action="{{ route('qris.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm bg-red-500/10 border border-red-500/20 hover:bg-red-500/20 text-red-400 px-4 py-2 rounded-xl transition-all">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        @if(session('success'))
            <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-2xl flex items-center gap-3">
                <div class="w-8 h-8 bg-green-500/20 text-green-400 rounded-lg flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-green-400">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Config Form -->
            <div class="bg-gray-900/40 border border-gray-800 rounded-3xl p-6 backdrop-blur-xl h-fit">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <!-- Icon cog -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Konfigurasi Gateway
                </h2>

                <form action="{{ route('qris.config.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">Merchant ID GoPay</label>
                        <input type="text" name="merchant_id" value="{{ $config->merchant_id }}" required
                            class="w-full bg-gray-950/80 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500/50"
                            placeholder="G123456789">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">GoPay API URL</label>
                        <input type="url" name="api_url" value="{{ $config->api_url }}" required
                            class="w-full bg-gray-950/80 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500/50"
                            placeholder="https://api.gobiz.co.id/v2/transactions">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">Token Otorisasi (GoBiz)</label>
                        <input type="password" name="token" 
                            class="w-full bg-gray-950/80 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-blue-500/50 placeholder-gray-600"
                            placeholder="{{ $config->has_token ? '•••••••••••••••• (Tersimpan)' : 'Masukkan token baru' }}">
                        <p class="text-[10px] text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah token yang tersimpan di DB.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase mb-2">String QRIS Statis</label>
                        <textarea name="static_qris" rows="6" required
                            class="w-full bg-gray-950/80 border border-gray-800 text-white rounded-xl px-4 py-2.5 text-[11px] font-mono focus:outline-none focus:border-blue-500/50"
                            placeholder="00020101021226610014COM.GO-JEK..."></textarea>
                        <p class="text-[10px] text-gray-500 mt-1">String QRIS statis dari GoPay Merchant Anda. String ini akan dimodifikasi di server menjadi dinamis.</p>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition-colors active:scale-[0.98]">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Right Side: Transactions History -->
            <div class="lg:col-span-2 bg-gray-900/40 border border-gray-800 rounded-3xl p-6 backdrop-blur-xl">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <!-- Icon list -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Riwayat Transaksi QRIS
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b border-gray-800 text-gray-400">
                                <th class="py-3 px-4 font-semibold">Tim</th>
                                <th class="py-3 px-4 font-semibold">Nominal</th>
                                <th class="py-3 px-4 font-semibold">Status</th>
                                <th class="py-3 px-4 font-semibold">Waktu Pembuatan</th>
                                <th class="py-3 px-4 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            @forelse($transactions as $tx)
                                <tr>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-white">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                                        <div class="text-xs text-gray-500">Trx ID: {{ $tx->trx_id }}</div>
                                    </td>
                                    <td class="py-4 px-4 font-mono">
                                        <div class="font-bold text-yellow-500">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                                        <div class="text-[10px] text-gray-500">Kode Unik: +{{ $tx->unique_code }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if($tx->status === 'PAID')
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500/10 text-green-400 border border-green-500/20">PAID</span>
                                        @elseif($tx->status === 'PENDING')
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">PENDING</span>
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20">EXPIRED</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-xs text-gray-400">
                                        {{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                    </td>
                                    <td class="py-4 px-4 text-right">
                                        @if($tx->status === 'PENDING')
                                            <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi ini secara manual?');">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-500 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors active:scale-[0.98]">
                                                    Settle Manual
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-600">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500 text-sm">
                                        Belum ada transaksi QRIS yang tercatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800 bg-gray-950/20 py-4 text-center text-xs text-gray-600">
        &copy; 2026 Yomuda Champs. Built with &hearts; directly integrated in Laravel.
    </footer>

    <script>
        // Set textarea default value dari backend config
        document.addEventListener('DOMContentLoaded', () => {
            const staticQris = @json($config->static_qris);
            if (staticQris) {
                document.querySelector('textarea[name="static_qris"]').value = staticQris;
            }
        });
    </script>
</body>
</html>
