<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yomuda Champs - Speed Diagnostics</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;950&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0c0a0f;
            color: #f8fafc;
        }
        .glow {
            text-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-900/50 border border-slate-800 rounded-3xl p-8 shadow-2xl backdrop-blur-md relative overflow-hidden">
        <!-- Decoration -->
        <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -left-10 -top-10 w-36 h-36 bg-violet-600/10 rounded-full blur-3xl"></div>

        <div class="text-center relative z-10">
            <div class="w-16 h-16 bg-blue-600/10 border border-blue-500/20 text-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="bi bi-speedometer2 text-3xl"></i>
            </div>
            
            <h2 class="text-xl font-extrabold tracking-tight text-white mb-2">Test Kecepatan Server</h2>
            <p class="text-xs text-slate-400 mb-8">Hasil analisis waktu respon rendering halaman di server Yomuda Champs.</p>

            <!-- Speed Gauge -->
            <div class="inline-flex items-center justify-center relative w-48 h-48 border-4 border-slate-800 rounded-full mb-8">
                <div class="absolute inset-2 border-2 border-dashed border-slate-700/50 rounded-full"></div>
                <div class="text-center">
                    <span id="speed-value" class="block text-4xl font-black text-blue-500 font-mono tracking-tight glow">
                        {{ $executionTimeMs }}
                    </span>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest block mt-1">Milidetik (ms)</span>
                </div>
            </div>

            <!-- Diagnostics Details -->
            <div class="space-y-4 text-left border-t border-slate-800/80 pt-6">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Response Server (Laravel)</span>
                    <span class="font-mono font-bold text-white">{{ $executionTimeMs }} ms</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Pemuatan Browser (DOM)</span>
                    <span id="dom-load-time" class="font-mono font-bold text-blue-400">Loading...</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Koneksi Database (MySQL)</span>
                    <span class="font-bold {{ $dbStatus === 'Connected' ? 'text-emerald-400' : 'text-rose-400' }}">{{ $dbStatus }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Penggunaan Memori RAM</span>
                    <span class="font-mono text-slate-300">{{ round(memory_get_usage(true) / 1024 / 1024, 2) }} MB</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400">Versi PHP</span>
                    <span class="font-mono text-slate-300">{{ PHP_VERSION }}</span>
                </div>
            </div>

            <div class="mt-8">
                <button onclick="window.location.reload()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3 px-6 rounded-2xl text-xs tracking-wide uppercase transition-all shadow-lg active:scale-98">
                    <i class="bi bi-arrow-clockwise me-1"></i> Uji Ulang Kecepatan
                </button>
            </div>
        </div>
    </div>

    <script>
        // Hitung waktu pemuatan client-side setelah halaman selesai dimuat sepenuhnya
        window.addEventListener('load', () => {
            setTimeout(() => {
                const perfData = window.performance.timing;
                const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
                document.getElementById('dom-load-time').innerText = pageLoadTime + ' ms';
            }, 100);
        });
    </script>
</body>
</html>
