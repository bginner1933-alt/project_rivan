<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Wangi Project</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }

        /* Styling Sidebar agar tetap rapi */
        .sidebar-wrapper {
            min-width: 250px;
            max-width: 250px;
            min-height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
        }

        .main-wrapper {
            width: 100%;
            min-height: 100vh;
        }

        /* Dropdown Profile Styling */
        .dropdown-menu.show {
            display: block;
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="d-flex">

    {{-- SIDEBAR --}}
    {{-- Pastikan file sidebar.blade.php ada di folder resources/views/layouts/ --}}
    <aside class="sidebar-wrapper d-none d-lg-block">
        @include('partials.admin.sidebar')
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="main-wrapper flex-grow-1">

        {{-- NAVBAR --}}
        {{-- Pastikan file navbar.blade.php ada di folder resources/views/layouts/ --}}
        <header>
            @include('partials.admin.navbar')
        </header>

        {{-- ALERTS / FLASH MESSAGES --}}
        <div class="px-4 pt-3">
            @if(view()->exists('layouts.flash-messages'))
                @include('layouts.flash-messages')
            @endif
        </div>

        {{-- CONTENT AREA --}}
        <main class="p-4">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>

    </div>
</div>

{{-- JAVASCRIPT --}}
<!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Fungsi Toggle Dropdown Profile Manual (Jika tidak pakai data-bs-toggle)
    function toggleMenu(event) {
        if(event) event.stopPropagation();
        const dropdown = document.getElementById('profileDropdown');
        if(dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    // Klik di luar untuk menutup dropdown
    window.onclick = function(event) {
        const dropdown = document.getElementById('profileDropdown');
        if (dropdown && !event.target.closest('.dropdown')) {
            dropdown.classList.remove('show');
        }
    }
</script>

@stack('scripts') {{-- Tempat script tambahan dari file dashboard --}}

</body>
</html>