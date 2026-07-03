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
            @if($qrisTx->status === 'CLAIMED')
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-500/10 text-blue-400 border border-blue-500/20 mb-2 animate-pulse">
                    Sedang Diverifikasi
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 mb-2">
                    Menunggu Pembayaran
                </span>
            @endif
            <h1 class="text-xl font-extrabold text-white">Pembayaran QRIS</h1>
            <p class="text-xs text-gray-400 mt-1">Scan kode QRIS di bawah ini dengan aplikasi perbankan atau e-wallet Anda</p>
        </div>

        @if(session('success'))
            <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs text-emerald-400 text-center font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-xs text-red-400 text-center font-medium">
                {{ session('error') }}
            </div>
        @endif

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
                    <span class="text-gray-500 font-medium">Nominal Transfer Wajib</span>
                    <p class="text-[10px] text-gray-600 mt-0.5">Harus sama persis (termasuk kode unik +Rp {{ $qrisTx->unique_code }})</p>
                </div>
                <div class="text-right">
                    <span class="text-lg font-black text-yellow-500 font-mono">
                        Rp {{ number_format($qrisTx->amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        @if($qrisTx->status === 'CLAIMED')
            <!-- Waiting Approval Screen -->
            <div class="bg-gray-950/50 border border-gray-850 p-6 rounded-3xl text-center space-y-4">
                <div class="w-12 h-12 bg-blue-500/15 rounded-full flex items-center justify-center mx-auto text-blue-400">
                    <svg class="animate-spin h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Bukti Transfer Sedang Diverifikasi</h3>
                    <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                        Sistem sedang memproses verifikasi manual bukti transfer Anda. Tim Anda akan otomatis terdaftar sebagai <b>LUNAS (PAID)</b> begitu admin menyetujui. Halaman ini akan dialihkan secara otomatis.
                    </p>
                </div>
            </div>
        @else
            <!-- QR Code -->
            <div class="flex flex-col items-center space-y-3 bg-white p-6 rounded-3xl border border-gray-800 shadow-inner">
                <!-- QRIS Brand Logo -->
                <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-6 object-contain mb-2">
                
                <!-- Generated QR Code Image (Static QRIS) -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($qrisTx->qris_string) }}" 
                     alt="QR Code QRIS" 
                     class="w-52 h-52 object-contain">

                <div class="text-[10px] text-red-650 font-extrabold text-center mt-2 leading-normal">
                    PENTING: MASUKKAN NOMINAL SECARA MANUAL SEBESAR<br>
                    <span class="text-xs font-black text-slate-900 font-mono">Rp {{ number_format($qrisTx->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Petunjuk Singkat -->
            <div class="text-[11px] text-gray-500 space-y-1 sm:px-2">
                <p class="font-bold text-gray-400 mb-1">Petunjuk Pembayaran:</p>
                <p>1. Buka aplikasi perbankan (BNI, BCA) atau e-wallet (GoPay, OVO, ShopeePay, Dana).</p>
                <p>2. Arahkan scanner ke QR Code di atas.</p>
                <p>3. <b>Ketik nominal transfer secara manual</b> sebesar <strong class="text-yellow-500 font-mono">Rp {{ number_format($qrisTx->amount, 0, ',', '.') }}</strong>.</p>
                <p>4. Setelah transfer sukses, halaman ini akan otomatis mendeteksi status pembayaran dalam 1-10 detik.</p>
            </div>

            <!-- Upload Bukti Transfer Form -->
            <div class="bg-gray-950/40 border border-gray-800/80 rounded-2xl p-4 space-y-3">
                <h4 class="text-xs font-bold text-white flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Pembayaran Bermasalah / Salah Nominal?
                </h4>
                <p class="text-[10px] text-gray-400 leading-normal">Jika pembayaran Anda tidak terdeteksi otomatis atau Anda tidak sengaja mentransfer nominal yang salah, silakan upload bukti transfer Anda di bawah ini untuk klaim manual oleh Admin.</p>
                
                <form action="{{ route('qris.pay.proof', $team->trx_id) }}" method="POST" enctype="multipart/form-data" class="space-y-2 mt-2">
                    @csrf
                    <input type="file" name="proof_file" required accept="image/*"
                           class="block w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer">
                    <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white text-xs font-bold py-2 rounded-xl transition-all">
                        Unggah Bukti Transfer
                    </button>
                </form>
            </div>
        @endif

        <!-- Tombol Cek Pembayaran Manual -->
        <div class="px-2">
            <button id="btnCheckNow" class="w-full bg-blue-600 hover:bg-blue-500 text-white text-sm font-black py-3.5 rounded-2xl shadow-[0_0_15px_rgba(59,130,246,0.5)] transition-all flex justify-center items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                SAYA SUDAH BAYAR
            </button>
        </div>

        <!-- Loading Polling Status -->
        <div class="flex items-center justify-center gap-2 text-[11px] text-gray-500 pt-1 pb-2">
            <svg class="animate-spin h-3.5 w-3.5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Mendeteksi pembayaran Anda otomatis (estimasi 1 menit)...</span>
        </div>

    </div>

    <!-- Script Polling & Countdown -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const expireTime = new Date("{{ $qrisTx->expires_at->toIso8601String() }}").getTime();
            const checkStatusUrl = "{{ route('qris.check', $team->trx_id) }}";
            const forceCheckUrl = "{{ route('qris.check.force', $team->trx_id) }}";
            
            const btnCheckNow = document.getElementById('btnCheckNow');
            
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

            // Manual Force Check
            if (btnCheckNow) {
                btnCheckNow.addEventListener('click', () => {
                    btnCheckNow.disabled = true;
                    const originalText = btnCheckNow.innerHTML;
                    btnCheckNow.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> MEMERIKSA...`;
                    
                    fetch(forceCheckUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'PAID') {
                            clearInterval(timer);
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Pembayaran belum terdeteksi. Pastikan Anda sudah mentransfer sesuai nominal yang diminta. Silakan coba lagi beberapa saat.');
                            btnCheckNow.disabled = false;
                            btnCheckNow.innerHTML = originalText;
                        }
                    })
                    .catch(err => {
                        console.error("Error force checking:", err);
                        alert('Terjadi kesalahan koneksi.');
                        btnCheckNow.disabled = false;
                        btnCheckNow.innerHTML = originalText;
                    });
                });
            }

            // AJAX Polling Status Pembayaran (Background)
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
