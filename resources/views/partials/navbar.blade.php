<!-- WRAPPER HEADER UNTUK STICKY -->
<header class="sticky-top shadow-sm">
    
    {{-- 1. TOP BAR (Gaya Tokopedia dari image_1da23b.png) --}}
    <div class="top-bar d-none d-lg-block border-bottom py-1">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="top-bar-left">
                <a class="text-decoration-none text-muted small hover-primary">
                    <i class="bi bi-truck me-1"></i> 
                    <strong>Beli sekarang</strong> sebelum kehabisan
                    {{-- <i class="bi bi-chevron-right small"></i> --}}
                </a>
            </div>
            <div class="top-bar-right d-flex gap-3">
                <a href=" {{ route('tentang') }} " class="text-decoration-none text-muted small hover-primary">Tentang Wangi</a>
                <a href="#" class="text-decoration-none text-muted small hover-primary">Promo</a>
                <a href=" {{ route('bantuan') }} " class="text-decoration-none text-muted small hover-primary">Wangi Care</a>
            </div>
        </div>
    </div>

    {{-- 2. NAVBAR UTAMA --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
        <div class="container">
            {{-- Logo & Brand --}}
            <a class="navbar-brand fw-bold text-dark d-flex align-items-center animate__animated animate__fadeInLeft" href="{{ route('home') }}">
                {{-- Logo Image --}}
                <img src="{{ asset('storage/images/logo.jpeg') }}" 
                     alt="Wangi Project Logo" 
                     style="height: 45px;" 
                     class="me-2">

                {{-- Brand Text --}}
                <span class="ls-tight">
                    Wangi <span class="text-primary">Project</span>
                </span>
            </a>

            {{-- Mobile Toggle --}}
            <button class="navbar-toggler border-0 shadow-none" type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            {{-- Navbar Content --}}
            <div class="collapse navbar-collapse" id="navbarMain">
                {{-- Search Form --}}
                <form class="d-flex mx-lg-5 my-3 my-lg-0 flex-grow-1" style="max-width: 500px;" 
                      action="{{ route('catalog.index') }}" method="GET">
                    <div class="input-group bg-light rounded-pill p-1 overflow-hidden border">
                        <input type="text" name="q" 
                               class="form-control bg-transparent border-0 ps-3 shadow-none" 
                               placeholder="Cari produk impianmu..." 
                               value="{{ request('q') }}">
                        <button class="btn btn-primary rounded-pill px-3 shadow-sm" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                {{-- Right Menu --}}
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    <li class="nav-item">
                        <a class="nav-link fw-medium hover-text-primary" href="{{ route('catalog.index') }}">
                            <i class="bi bi-grid-fill me-1"></i> Katalog
                        </a>
                    </li>

                    @auth
                        {{-- Wishlist --}}
                        <li class="nav-item">
                            <a class="nav-link position-relative btn-icon-hover" href="{{ route('wishlist.index') }}">
                                <i class="bi bi-heart fs-5"></i>
                                @php $wishCount = auth()->user()->wishlists()->count(); @endphp
                                @if($wishCount > 0)
                                    <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem; padding: 0.35em 0.5em;">
                                        {{ $wishCount }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        {{-- Cart --}}
                        <li class="nav-item me-lg-2">
                            <a class="nav-link position-relative btn-icon-hover" href="{{ route('cart.index') }}">
                                <i class="bi bi-cart3 fs-5"></i>
                                @php $cartCount = auth()->user()->cart?->items()->count() ?? 0; @endphp
                                @if($cartCount > 0)
                                    <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-primary border border-white" style="font-size: 0.6rem; padding: 0.35em 0.5em;">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>
                        </li>

                        {{-- User Dropdown --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle user-card-pill p-1 pe-3 shadow-sm border" 
                               href="#" id="userDropdown" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?background=0D6EFD&color=fff&name='.urlencode(auth()->user()->name) }}" 
                                     class="rounded-circle shadow-sm" width="35" height="35" alt="{{ auth()->user()->name }}">
                                <span class="ms-2 fw-bold text-dark small d-none d-md-inline">{{ Str::before(auth()->user()->name, ' ') }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 animate__animated animate__fadeIn animate__faster mt-2 py-2 rounded-4">
                                <li class="px-3 py-2 mb-2 border-bottom">
                                    <p class="text-muted small mb-0">Selamat datang,</p>
                                    <p class="fw-bold text-dark mb-0 text-truncate" style="max-width: 150px;">{{ auth()->user()->name }}</p>
                                </li>
                                <li><a class="dropdown-item py-2 px-3 rounded-3 mx-2 w-auto mb-1 hover-lift-sm" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i> Profil Saya</a></li>
                                <li><a class="dropdown-item py-2 px-3 rounded-3 mx-2 w-auto mb-1 hover-lift-sm" href="{{ route('orders.index') }}"><i class="bi bi-bag-check me-2 text-success"></i> Pesanan Saya</a></li>
                                <li><a class="dropdown-item py-2 px-3 rounded-3 mx-2 w-auto mb-1 hover-lift-sm" href="{{ route('chat.index') }}"><i class="bi bi-chat me-2 text-info"></i>
                                @if(auth()->user()->isAdmin())
                                    Chat Customer
                                @else
                                    Chat Admin
                                @endif
                                </a></li>
                                @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider opacity-50"></li>
                                    <li><a class="dropdown-item py-2 px-3 rounded-3 mx-2 w-auto mb-1 bg-soft-primary text-primary fw-bold" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Admin Panel</a></li>
                                @endif
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                                    <button type="button" class="dropdown-item py-2 px-3 rounded-3 mx-2 w-auto text-danger hover-lift-sm" onclick="confirmLogout()">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link fw-medium px-3" href="{{ route('login') }}">Masuk</a></li>
                        <li class="nav-item"><a class="btn btn-primary rounded-pill px-4 shadow-sm hover-lift" href="{{ route('register') }}">Daftar</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>

{{-- CSS KHUSUS NAVBAR --}}
<style>
    /* Styling Top Bar sesuai image_1da23b.png */
    .top-bar {
        background-color: #f3f4f5; /* Abu-abu muda Tokopedia */
    }
    .top-bar a {
        font-size: 12px;
        transition: color 0.2s ease;
    }
    .hover-primary:hover {
        color: #0d6efd !important;
    }

    /* Font & Kerning */
    .ls-tight { letter-spacing: -0.5px; }
    
    /* Sticky Effect with Glassmorphism */
    header.sticky-top {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.9) !important;
        z-index: 1020;
    }

    /* Navbar Link Hover */
    .hover-lift { transition: all 0.2s ease; }
    .hover-lift:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(13,110,253,0.2) !important; }
    
    .hover-lift-sm { transition: all 0.2s ease; }
    .hover-lift-sm:hover { transform: translateX(5px); background-color: #f8f9fa; }

    .btn-icon-hover { transition: all 0.2s ease; color: #6c757d; }
    .btn-icon-hover:hover { color: #0d6efd; transform: scale(1.1); }

    .hover-text-primary:hover { color: #0d6efd !important; }

    /* User Dropdown Pill */
    .user-card-pill {
        border-radius: 50px;
        background: #fff;
        transition: all 0.3s ease;
    }
    .user-card-pill:hover {
        background: #f8f9fa;
        border-color: #0d6efd !important;
    }

    .bg-soft-primary { background-color: #eef4ff; }
    .dropdown-menu { min-width: 210px; }
    .swal-rounded { border-radius: 18px !important; box-shadow: 0 20px 60px rgba(0,0,0,0.15) !important; }
</style>

{{-- Script SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout() {
    Swal.fire({
        title: 'Keluar dari akun?',
        text: 'Kamu bisa login kembali kapan saja.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Logout',
        cancelButtonText: 'Tetap di sini',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#94a3b8',
        background: '#ffffff',
        color: '#0f172a',
        customClass: { popup: 'swal-rounded' },
        showClass: { popup: 'animate__animated animate__fadeInDown' },
        hideClass: { popup: 'animate__animated animate__fadeOutUp' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}
</script>