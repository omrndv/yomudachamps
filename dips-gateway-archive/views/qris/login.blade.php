<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - QRIS Gateway Admin</title>
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

        // Initialize Theme
        if (localStorage.getItem('qris-theme') === 'dark' || (!('qris-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #f8fafc, #f1f5f9);
        }
        .dark body {
            background: radial-gradient(circle at top right, #0f172a, #020617);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 transition-all duration-300 relative">
    
    <!-- Theme Toggle at top right -->
    <button id="theme-toggle" class="absolute top-6 right-6 w-10 h-10 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:text-indigo-600 shadow-sm active:scale-95 transition-all">
        <i data-lucide="sun" class="w-5 h-5 hidden dark:block"></i>
        <i data-lucide="moon" class="w-5 h-5 dark:hidden"></i>
    </button>

    <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 p-8 rounded-3xl shadow-xl dark:shadow-2xl transition-all duration-300">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-2xl mb-4 border border-indigo-100 dark:border-indigo-500/20">
                <i data-lucide="key-round" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white leading-none">QRIS Admin Gateway</h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-2">Masukkan kredensial khusus gateway Anda</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl flex items-center gap-3 text-red-600 dark:text-red-400">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <p class="text-sm font-semibold">{{ $errors->first() }}</p>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 p-4 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 rounded-2xl flex items-center gap-3 text-emerald-600 dark:text-emerald-400">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                <p class="text-sm font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('qris.login.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="username" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-slate-500">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </span>
                    <input type="text" name="username" id="username" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-2xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10 dark:focus:ring-indigo-400/10 transition-all placeholder-slate-400 dark:placeholder-slate-600"
                        placeholder="Username">
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400 dark:text-slate-500">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-2xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/10 dark:focus:ring-indigo-400/10 transition-all placeholder-slate-400 dark:placeholder-slate-600"
                        placeholder="••••••••••••">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 px-4 rounded-2xl shadow-lg shadow-indigo-600/20 dark:shadow-indigo-600/10 active:scale-[0.98] transition-all text-sm flex items-center justify-center gap-2">
                <i data-lucide="log-in" class="w-4 h-4"></i> Masuk ke Dashboard
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-indigo-600 dark:text-slate-500 dark:hover:text-indigo-400 transition-colors font-semibold">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Yomuda Champs
            </a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            lucide.createIcons();

            // Theme Switcher
            const themeToggleBtn = document.getElementById('theme-toggle');
            themeToggleBtn.addEventListener('click', () => {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('qris-theme', isDark ? 'dark' : 'light');
            });
        });
    </script>
</body>
</html>
