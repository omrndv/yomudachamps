<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'Yomuda Championship - Platform Pendaftaran Turnamen E-sports')</title>
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="description" content="@yield('meta_description', 'Platform pendaftaran turnamen E-sports dengan sistem pembayaran otomatis dan verifikasi real-time. Kelola pendaftaran timmu dengan mudah.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Yomuda Championship, pendaftaran turnamen e-sports, turnamen mobile legends, turnamen online indonesia')">
    <meta name="author" content="Yomuda Championship">

    <meta property="og:title" content="@yield('og_title', 'Yomuda Championship - Turnamen E-sports')">
    <meta property="og:description" content="@yield('meta_description', 'Sistem pendaftaran turnamen e-sports otomatis.')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-yomuda.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Yomuda Championship')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Daftar turnamen e-sports jadi lebih mudah dengan sistem otomatis.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/logo-yomuda.png'))">

    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">

    <!-- Performance Optimizations: DNS Prefetch & Preconnect -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="preload" as="image" href="/images/bg-mobile.jpg" media="(max-width: 768px)">
    <link rel="preload" as="image" href="/images/bg-yomuda.jpg" media="(min-width: 769px)">
    <link rel="preload" as="image" href="/images/logo-yomuda.png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Defer non-critical scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    <meta name="google-site-verification" content="ErJnugnESZ9vYec5vVLW1evAKh6SQ5xJTG_jlY2WzOg">

    <style>
        html,
        body {
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #121417;
            color: #ffffff;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        button,
        input,
        textarea,
        select {
            font-family: inherit;
        }

        a {
            text-decoration: none;
        }

        main {
            flex: 1;
            width: 100%;
            padding: 100px 20px 50px;
            background-image: url('/images/bg-yomuda.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .content-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .floating-check-team {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1050;
            border-radius: 50px;
            font-size: 0.7rem;
            padding: 7px 15px;
            font-weight: 800;
            letter-spacing: 0.2px;
            color: #000;
            background: #ffc107;
            border: none;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.18);
            transition: 0.25s ease;
        }

        .floating-check-team:hover {
            background: #ffffff;
            color: #000;
            transform: translateY(-2px);
        }

        footer {
            margin-top: 80px;
            border-top: 1px solid rgba(255,255,255,.05);
        }

        @media (max-width: 768px) {
            main {
                padding: 80px 10px 50px;
                background-image: url('/images/bg-mobile.jpg');
                background-attachment: scroll;
            }

            .floating-check-team {
                top: 12px;
                right: 12px;
                font-size: 0.62rem;
                padding: 6px 12px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <a href="{{ route('check.team') }}" class="floating-check-team d-flex align-items-center justify-content-center">
        <i class="bi bi-search me-1"></i>
        CEK TIM KAMU
    </a>

    <main>
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    @include('components.footer')

    @if(session('success') || session('error') || session('info'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Waduh!',
                    text: @json(session('error')),
                    confirmButtonColor: '#ffc107'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Pendaftaran Ditemukan',
                    text: @json(session('info')),
                    confirmButtonColor: '#ffc107'
                });
            @endif
        });
    </script>
    @endif

    @stack('scripts')
    <!-- Instant.page: Preloads pages on hover before user clicks for instant (waswuss) navigation -->
    <script src="https://cdn.jsdelivr.net/npm/instant.page@5.2.0/instantpage.us.min.js" type="module" defer></script>
</body>
</html>