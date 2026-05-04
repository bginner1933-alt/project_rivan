<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Toko Online') - {{ config('app.name') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .serif { font-family: 'Playfair Display', serif; }
        .tailwind-enabled h1, .tailwind-enabled h2, .tailwind-enabled p { margin-bottom: 0; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
</head>
<body>
    {{-- Navigasi disembunyikan di halaman tertentu --}}
    @unless (request()->routeIs('welcome', 'login', 'register'))
        @include('partials.navbar')
    @endunless

    {{-- Flash messages --}}
    <div class="container mt-3">
        @include('partials.flash-messages')
    </div>

    {{-- Main content --}}
    <main class="min-vh-100">
        @yield('content')
    </main>

    {{-- Footer disembunyikan di halaman tertentu --}}
    @unless (request()->routeIs('welcome', 'login', 'register'))
        @include('partials.footer')
    @endunless

    @stack('scripts')
    <script>
        // SCRIPT WISHLIST & QTY ASLI KAMU
        async function toggleWishlist(productId) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(`/wishlist/toggle/${productId}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": token,
                    },
                });
                if (response.status === 401) { window.location.href = "/login"; return; }
                const data = await response.json();
                if (data.status === "success") {
                    updateWishlistUI(productId, data.added);
                    updateWishlistCounter(data.count);
                    showToast(data.message);
                }
            } catch (error) { console.error("Error:", error); }
        }

        function updateWishlistUI(productId, isAdded) {
            const buttons = document.querySelectorAll(`.wishlist-btn-${productId}`);
            buttons.forEach((btn) => {
                const icon = btn.querySelector("i");
                if (isAdded) {
                    icon.classList.replace("bi-heart", "bi-heart-fill");
                    icon.classList.add("text-danger");
                } else {
                    icon.classList.replace("bi-heart-fill", "bi-heart");
                    icon.classList.remove("text-danger");
                }
            });
        }

        function updateWishlistCounter(count) {
            const badge = document.getElementById("wishlist-count");
            if (badge) {
                badge.innerText = count;
                badge.style.display = count > 0 ? "inline-block" : "none";
            }
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
</body>
</html>