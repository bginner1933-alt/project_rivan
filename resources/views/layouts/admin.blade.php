{{-- ================================================
     ADMIN LAYOUT - BLUE WEDDING ELEGANCE VERSION (IMPROVED UI)
     ================================================ --}}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Admin Panel</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
        }

        .sidebar {
            min-height: 100vh;
            width: 260px;
            background: linear-gradient(180deg, #0d6efd, #1d4ed8);
            animation: slideInLeft 0.6s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 10px 0 30px rgba(0,0,0,0.08);
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.78);
            padding: 11px 16px;
            border-radius: 12px;
            margin: 6px 12px;
            transition: 0.25s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.12);
            transform: translateX(6px);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
            color: white;
            box-shadow: 0 4px 15px rgba(59,130,246,0.4);
        }

        .sidebar .nav-link i {
            width: 18px;
            text-align: center;
        }

        header {
            background: linear-gradient(270deg, #1e3a8a, #3b82f6, #60a5fa);
            background-size: 600% 600%;
            animation: gradientMove 8s ease infinite;
            color: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .btn-light {
            font-weight: 600;
            border-radius: 10px;
            transition: 0.3s;
        }

        .btn-light:hover {
            transform: translateY(-2px);
        }

        .sidebar .user-box {
            padding: 14px;
            border-radius: 12px;
            background: rgba(255,255,255,0.08);
            margin: 10px;
        }

        @keyframes slideInLeft {
            from {transform: translateX(-60px); opacity: 0;}
            to {transform: translateX(0); opacity: 1;}
        }

        @keyframes gradientMove {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }
    </style>
</head>

<body>
<div class="d-flex">

    {{-- SIDEBAR --}}
    <div class="sidebar">

        <div>

            {{-- BRAND --}}
            <div class="p-3 border-bottom border-secondary">
                <a href="{{ route('admin.dashboard') }}"
                   class="text-white text-decoration-none fw-bold d-flex align-items-center">
                    <i class="fas fa-store me-2"></i>
                    <span>Pkl Admin</span>
                </a>
            </div>

            {{-- MENU --}}
            <nav class="py-3">
                <ul class="nav flex-column">

                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">
                            <i class="fas fa-gauge"></i> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="nav-link">
                            <i class="fas fa-folder"></i> Kategori
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.products.index') }}" class="nav-link">
                            <i class="fas fa-box"></i> Produk
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.orders.index') }}" class="nav-link">
                            <i class="fas fa-receipt"></i> Pesanan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.users.index') }}" class="nav-link">
                            <i class="fas fa-users"></i> Pengguna
                        </a>
                    </li>

                    {{-- ✅ TAMBAHAN: LAPORAN --}}
                    <li>
                        <a href="{{ route('admin.reports.sales') }}" class="nav-link">
                            <i class="fas fa-chart-line"></i> Laporan Penjualan
                        </a>
                    </li>

                    <hr>

                    <li>
                        <a href="{{ route('wishlist.index') }}" class="nav-link">
                            <i class="fas fa-heart"></i> Daftar Keinginan
                        </a>
                    </li>

                     <li>
                        <a href="{{ route('cart.index') }}" class="nav-link">
                            <i class="fas fa-cart-shopping"></i> Keranjang
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('catalog.index') }}" class="nav-link">
                            <i class="fas fa-store"></i> Katalog
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.laporanpengaduan.index') }}" class="nav-link">
                            <i class="fas fa-store"></i> Laporan Pengaduan
                        </a>
                    </li>

                </ul>
            </nav>
        </div>

        {{-- USER --}}
        <div class="p-3 border-top border-secondary text-white">

            <div class="user-box">

                <div class="d-flex align-items-center mb-3">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://via.placeholder.com/36' }}"
                         class="rounded-circle me-2" width="38" height="38">

                    <div>
                        <div class="small fw-bold">{{ auth()->user()->name }}</div>
                        <div class="small text-white-50">Account</div>
                    </div>
                </div>

               

            </div>

        </div>

    </div>

    {{-- MAIN --}}
    <div class="flex-grow-1">

        <header class="py-3 px-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-semibold">@yield('page-title', 'Dashboard')</h4>

            <a href="{{ route('home') }}" class="btn btn-light btn-sm px-3">
                Lihat Toko 🛒
            </a>
        </header>

        <div class="px-4 pt-3">
            @include('partials.flash-messages')
        </div>

        <main class="p-4">
            @yield('content')
        </main>

    </div>

</div>
</body>
</html>