<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Toko Online') - {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .serif { font-family: 'Playfair Display', serif; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @unless (request()->routeIs('welcome', 'login', 'register'))
        @include('partials.navbar')
    @endunless

    <main class="min-vh-100">
        @yield('content')
    </main>

    @unless (request()->routeIs('welcome', 'login', 'register'))
        @include('partials.footer')
    @endunless

    <script>
        // --- 1. Fungsi Toast Notifikasi (Gunakan ini untuk pesan sukses/gagal) ---
        function showToast(message, icon = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({ icon: icon, title: message });
        }

        // --- 2. Fungsi Konfirmasi Modern (Gunakan ini untuk tombol Hapus/COD) ---
        function showConfirm(title, text, confirmCallback) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#d1d5db',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) { confirmCallback(); }
            });
        }

        // --- 3. Script Wishlist & Qty (Original) ---
        async function toggleWishlist(productId) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(`/wishlist/toggle/${productId}`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": token },
                });
                if (response.status === 401) { window.location.href = "/login"; return; }
                const data = await response.json();
                if (data.status === "success") {
                    // Update UI...
                    showToast(data.message);
                }
            } catch (error) { console.error("Error:", error); }
        }

        function incrementQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) < parseInt(input.max)) input.value = parseInt(input.value) + 1;
        }

        function decrementQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }
    </script>

    {{-- Script untuk menangkap session flash message Laravel --}}
    @if(session('success'))
        <script>showToast("{{ session('success') }}", 'success');</script>
    @endif
    @if(session('error'))
        <script>showToast("{{ session('error') }}", 'error');</script>
    @endif

    @stack('scripts')
</body>
</html>