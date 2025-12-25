<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Yomuda Championship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f4f7fa;
            color: #2d3436;
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            background: #1e2125;
            color: #ffffff;
            z-index: 1000;
            padding: 20px;
            transition: all 0.3s;
        }

        .sidebar-header {
            padding: 10px 15px 30px 15px;
            text-align: center;
        }

        .sidebar-brand {
            font-size: 1.25rem;
            letter-spacing: 1px;
            color: #ffc107;
            text-transform: uppercase;
        }

        .nav-pills .nav-link {
            color: #a4b0be;
            padding: 12px 18px;
            margin-bottom: 8px;
            font-weight: 500;
            border-radius: 12px;
            transition: 0.3s;
            display: flex;
            align-items: center;
        }

        .nav-pills .nav-link i {
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .nav-pills .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-pills .nav-link.active {
            color: #000;
            background: #ffc107;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .nav-link.text-danger:hover {
            background: rgba(231, 76, 60, 0.1) !important;
        }

        .main-content {
            margin-left: 280px;
            padding: 40px;
            min-height: 100vh;
        }

        .card-custom {
            background: #ffffff;
            border: none;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        }

        .navbar-mobile {
            background: #1e2125;
            padding: 15px 20px;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding: 25px 15px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-mobile d-lg-none border-bottom border-secondary shadow-sm sticky-top">
        <div class="container-fluid p-0">
            <span class="navbar-brand fw-bold text-warning m-0">YMD ADM</span>
            <button class="btn btn-outline-warning border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMobile">
                <i class="bi bi-grid-fill fs-3"></i>
            </button>
        </div>
    </nav>

    <aside class="sidebar d-none d-lg-block">
        <div class="sidebar-header">
            <div class="sidebar-brand fw-bold">
                <i class="bi bi-lightning-charge-fill me-1"></i> Yomuda <span class="text-white">ADM</span>
            </div>
        </div>
        
        <div class="nav nav-pills flex-column">
            <small class="text-uppercase text-muted fw-bold mb-3 px-3" style="font-size: 0.7rem; letter-spacing: 1px;">Menu Utama</small>
            
            <a href="{{ route('admin.seasons') }}" class="nav-link {{ request()->routeIs('admin.seasons') || request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-trophy"></i> Daftar Season
            </a>

            <div class="mt-auto pt-2">
                <hr class="border-secondary opacity-25 mx-3">
                <a href="{{ route('admin.logout') }}" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </a>
            </div>
        </div>
    </aside>

    <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMobile" style="width: 280px;">
        <div class="offcanvas-header border-bottom border-secondary p-4">
            <h5 class="offcanvas-title fw-bold text-warning">YOMUDA MENU</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-4">
            <div class="nav nav-pills flex-column">
                <a href="{{ route('admin.seasons') }}" class="nav-link text-white mb-2 {{ request()->routeIs('admin.seasons') ? 'active' : '' }}">
                    <i class="bi bi-trophy me-2"></i> Daftar Season
                </a>
                <a href="{{ route('admin.logout') }}" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <main class="main-content">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>