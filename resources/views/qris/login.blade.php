<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Panel Admin QRIS</title>
    <!-- Tailwind CSS (CDN untuk panel mandiri terisolasi agar desain premium instan) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #111827, #030712);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-gray-900/60 backdrop-blur-xl border border-gray-800 p-8 rounded-3xl shadow-2xl">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-500/10 text-blue-400 rounded-2xl mb-4 border border-blue-500/20">
                <!-- Icon Key -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-white">QRIS Admin Gateway</h1>
            <p class="text-sm text-gray-400 mt-1">Masukkan kredensial khusus gateway Anda</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl">
                <p class="text-sm text-red-400 font-semibold">{{ $errors->first() }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 p-4 bg-green-500/10 border border-green-500/20 rounded-2xl">
                <p class="text-sm text-green-400 font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('qris.login.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="username" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Username</label>
                <input type="text" name="username" id="username" required
                    class="w-full bg-gray-950/80 border border-gray-800 text-white rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500/50 transition-colors placeholder-gray-600"
                    placeholder="Masukkan username">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full bg-gray-950/80 border border-gray-800 text-white rounded-2xl px-4 py-3.5 text-sm focus:outline-none focus:border-blue-500/50 transition-colors placeholder-gray-600"
                    placeholder="••••••••••••">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold py-3.5 px-4 rounded-2xl shadow-lg shadow-blue-500/20 active:scale-[0.98] transition-all text-sm">
                Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="text-xs text-gray-500 hover:text-gray-400 transition-colors">
                &larr; Kembali ke Yomuda Champs
            </a>
        </div>
    </div>
</body>
</html>
