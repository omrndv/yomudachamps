<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS - Yomuda Champs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #111827, #030712);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-gray-100">
    <div class="w-full max-w-md bg-gray-900/60 backdrop-blur-xl border border-gray-800 p-6 sm:p-8 rounded-3xl shadow-2xl flex flex-col space-y-6">
        
        <!-- Header -->
        <div class="text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 mb-2">
                Menunggu Pembayaran
            </span>
            <h1 class="text-xl font-extrabold text-white">Pembayaran QRIS</h1>
            <p class="text-xs text-gray-400 mt-1">Scan kode QRIS di bawah ini dengan aplikasi pembayaran</p>
        </div>

        <!-- Timer -->
        <div class="bg-gray-950/60 border border-gray-800 rounded-2xl p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Batas Waktu Pembayaran</p>
            <div id="countdown" class="text-2xl font-black text-red-500 tracking-wider mt-1">00:00</div>
        </div>

        <!-- Info Billing -->
        <div class="bg-gray-950/40 border border-gray-800/60 rounded-2xl p-4 space-y-3">
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-500 font-medium">Nama Tim</span>
                <span class="font-bold text-white">{{ $team->name }}</span>
            </div>
            <hr class="border-gray-800/60">
            <div class="flex justify-between items-start text-xs">
                <div>
                    <span class="text-gray-500 font-medium">Jumlah Tagihan</span>
                    <p class="text-[10px] text-gray-600 mt-0.5">Termasuk kode unik (+Rp {{ $qrisTx->unique_code }})</p>
                </div>
                <div class="text-right">
                    <span class="text-lg font-extrabold text-yellow-500 font-mono">
                        Rp {{ number_format($qrisTx->amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- QR Code -->
        <div class="flex flex-col items-center space-y-3 bg-white p-6 rounded-3xl border border-gray-800 shadow-inner">
            <!-- QRIS Brand Logo -->
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-6 object-contain mb-2">
            
            <!-- Generated QR Code Image -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrisTx->qris_string) }}" 
                 alt="QR Code QRIS" 
                 class="w-52 h-52 object-contain">

            <p class="text-[10px] text-gray-500 font-semibold text-center mt-2">
                Nominal otomatis terdeteksi saat di-scan.<br>
                Tidak perlu memasukkan nominal manual.
            </p>
        </div>

        <!-- Petunjuk Singkat -->
        <div class="text-[11px] text-gray-500 space-y-1 sm:px-2">
            <p class="font-bold text-gray-400 mb-1">Petunjuk Pembayaran:</p>
            <p>1. Buka aplikasi e-wallet Anda (GoPay, OVO, Dana, ShopeePay) atau mobile banking.</p>
            <p>2. Pilih menu scan / bayar lalu arahkan kamera ke QR Code di atas.</p>
            <p>3. Nominal akan otomatis terisi sebesar <strong class="text-yellow-500 font-mono">Rp {{ number_format($qrisTx->amount, 0, ',', '.') }}</strong>.</p>
            <p>4. Setelah pembayaran sukses di aplikasi Anda, halaman ini akan otomatis dialihkan.</p>
        </div>

        <!-- Loading Polling Status -->
        <div class="flex items-center justify-center gap-2 text-xs text-gray-500 pt-2 border-t border-gray-850">
            <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Mendeteksi pembayaran Anda otomatis...</span>
        </div>

    </div>

    <!-- Script Polling & Countdown -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const expireTime = new Date("{{ $qrisTx->expires_at->toIso8601String() }}").getTime();
            const checkStatusUrl = "{{ route('qris.check', $team->trx_id) }}";
            
            // Countdown Timer
            const countdownEl = document.getElementById('countdown');
            const timer = setInterval(() => {
                const now = new Date().getTime();
                const distance = expireTime - now;

                if (distance < 0) {
                    clearInterval(timer);
                    countdownEl.innerHTML = "EXPIRED";
                    countdownEl.classList.remove('text-red-500');
                    countdownEl.classList.add('text-gray-600');
                    alert('Batas waktu pembayaran telah habis. Silakan buat transaksi baru.');
                    window.location.reload();
                    return;
                }

                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                countdownEl.innerHTML = 
                    (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                    (seconds < 10 ? "0" + seconds : seconds);
            }, 1000);

            // AJAX Polling Status Pembayaran
            const pollInterval = setInterval(() => {
                fetch(checkStatusUrl)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'PAID') {
                            clearInterval(pollInterval);
                            clearInterval(timer);
                            window.location.href = data.redirect_url;
                        } else if (data.status === 'EXPIRED') {
                            clearInterval(pollInterval);
                            clearInterval(timer);
                            countdownEl.innerHTML = "EXPIRED";
                            alert('Transaksi telah kedaluwarsa.');
                            window.location.reload();
                        }
                    })
                    .catch(err => console.error("Error polling status:", err));
            }, 4000); // Polling setiap 4 detik
        });
    </script>
</body>
</html>
