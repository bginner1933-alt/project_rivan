@extends('layouts.app')

@section('title', 'Beranda - Koleksi Premium')

@section('content')
<div class="luxury-homepage">
    {{-- 1. HERO SECTION --}}
    <header class="hero-container">
        <div class="luxury-homepage">
            <div class="container-luxury">
                
                {{-- LAYOUT GRID: CAROUSEL (KIRI) & SIDE BANNERS (KANAN) --}}
                <div class="hero-grid-wrapper">
                    
                    {{-- BAGIAN KIRI: MAIN CAROUSEL --}}
                    <div class="main-carousel-area">
                        <div id="luxuryHeroCarousel" class="carousel slide carousel-fade shadow-lg" data-bs-ride="carousel" data-bs-interval="5000">
                            
                            <div class="carousel-indicators">
                                <button type="button" data-bs-target="#luxuryHeroCarousel" data-bs-slide-to="0" class="active"></button>
                                <button type="button" data-bs-target="#luxuryHeroCarousel" data-bs-slide-to="1"></button>
                                <button type="button" data-bs-target="#luxuryHeroCarousel" data-bs-slide-to="2"></button>
                            </div>

                            <div class="carousel-inner">
                                {{-- SLIDE 1: VIDEO --}}
                                <div class="carousel-item active">
                                    <div class="hero-card">
                                        <video class="hero-bg" autoplay muted loop playsinline>
                                            <source src="{{ asset('videos/iklan.mp4') }}" type="video/mp4">
                                        </video>
                                        <div class="hero-overlay"></div>
                                        <div class="hero-content">
                                            <span class="hero-subtitle">Wangi Shop Exclusive</span>
                                            <h1 class="serif hero-title">Kemewahan <br> <span class="text-azure">Eksklusif.</span></h1>
                                            <a href="{{ route('catalog.index') }}" class="btn-luxury">Jelajahi Koleksi <i class="bi bi-arrow-right"></i></a>
                                        </div>
                                    </div>
                                </div>

                                {{-- SLIDE 2: IMAGE --}}
                                <div class="carousel-item">
                                    <div class="hero-card">
                                        <img src="{{ asset('images/banner2.jpg') }}" class="hero-bg-img" alt="Banner 2">
                                        <div class="hero-overlay"></div>
                                        <div class="hero-content">
                                            <span class="hero-subtitle">New Collection</span>
                                            <h1 class="serif hero-title">Style Modern <br> <span class="text-gold">Elegan.</span></h1>
                                            <a href="{{ route('catalog.index') }}" class="btn-luxury">Belanja Sekarang <i class="bi bi-bag"></i></a>
                                        </div>
                                    </div>
                                </div>

                                {{-- SLIDE 3: IMAGE --}}
                                <div class="carousel-item">
                                    <div class="hero-card">
                                        <img src="{{ asset('images/banner3.jpg') }}" class="hero-bg-img" alt="Banner 3">
                                        <div class="hero-overlay"></div>
                                        <div class="hero-content">
                                            <span class="hero-subtitle">Limited Offer</span>
                                            <h1 class="serif hero-title">Diskon <br> <span class="text-danger">Special.</span></h1>
                                            <a href="{{ route('catalog.index') }}" class="btn-luxury">Lihat Promo <i class="bi bi-stars"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#luxuryHeroCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#luxuryHeroCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>

                    {{-- BAGIAN KANAN: SIDE BANNERS (ALA TOKOPEDIA) --}}
                    <div class="side-banners-area">
                        <div class="side-banner-item shadow-sm mb-3">
                            <img src="{{ asset('storage/avatars/rr.jpg') }}" alt="Promo 1">
                            <div class="side-banner-overlay">
                                <h6>Best Seller</h6>
                            </div>
                        </div>
                        <div class="side-banner-item shadow-sm">
                            <img src="{{ asset('storage/avatars/rr.jpg') }}" alt="Promo 2">
                            <div class="side-banner-overlay">
                                <h6>Voucher Member</h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>

    {{-- 2. KATEGORI POPULER --}}
    <section class="container py-5 mt-4">
        <div class="d-flex align-items-center mb-5">
            <div class="accent-line me-3"></div>
            <h2 class="section-label">Kategori Pilihan</h2>
            <div class="flex-grow-1 ms-4 border-bottom opacity-25"></div>
        </div>

        <div class="category-scroll">
            @foreach($categories as $category)
            <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="category-item text-decoration-none">
                <div class="category-circle">
                    @if($category->image_url)
                        <img 
                            src="{{ $category->image_url }}" 
                            alt="{{ $category->name }}"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >

                        <div class="category-text-fallback" style="display:none;">
                            {{ $category->name }}
                        </div>
                    @else
                        <div class="category-text-fallback">
                            {{ $category->name }}
                        </div>
                    @endif
                </div>
                <div class="text-center">
                    <p class="category-name">{{ $category->name }}</p>
                    <span class="category-count">{{ $category->products_count }} Koleksi</span>
                </div>
            </a>
            @endforeach
        </div>
    </section>

    {{-- 3. PRODUK UNGGULAN --}}
    <section class="featured-section py-5">
        <div class="container py-4">
            {{-- <div>
                <div class="accent-line me-3"></div>
                    <h2 class="serif display-4 italic text-dark-blue">Kategori Populer</h2>
                <div class="flex-grow-1 ms-4 border-bottom opacity-25"></div>
            </div> --}}

            <div class="row align-items-end mb-5">
                <div class="col-md-8">
                    <h2 class="serif display-4 italic text-dark-blue">For You</h2>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="{{ route('catalog.index') }}" class="view-all-link">
                        Lihat Semua Koleksi 
                        <span class="arrow-icon">→</span>
                    </a>
                </div>
            </div>

            <div class="product-scroll">
                @foreach($featuredProducts as $product)
                <div class="col-6 col-md-3">
                    <div class="product-card-luxury">
                        <a href="{{ route('catalog.show', $product->slug) }}" class="text-decoration-none">
                            <div class="product-img-wrapper shadow-sm">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                
                                {{-- Badge --}}
                                <span class="badge-new">New Arrival</span>

                                {{-- Wishlist --}}
                                <button onclick="event.preventDefault(); toggleWishlist({{ $product->id }})" 
                                        class="wishlist-btn-luxury wishlist-btn-{{ $product->id }}">
                                    <i class="bi {{ $product->is_wishlisted ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                                </button>

                                <span class="badge-terjual">
                                    {{ $product->total_sold ?? 0 }} Terjual
                                </span>
                            </div>

                            <div class="price-container d-flex flex-column align-items-center py-2">  
                                <h6 class="product-name-luxury mb-1">{{ $product->name }}</h6>                        
                                <!-- Bagian Harga Jual -->
                                @if($product->display_price > 0)
                                    <div class="main-price-wrapper text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <!-- Harga Asli (Harga Jual) -->
                                            <span class="fw-bold text-danger fs-5">
                                                <small class="fs-6">Rp</small>{{ number_format($product->display_price, 0, ',', '.') }}
                                            </span>

                                            <!-- Harga Coret (Hanya muncul jika ada diskon) -->
                                            @if($product->display_price < $product->price)
                                                <span class="text-muted text-decoration-line-through small">
                                                    Rp{{ number_format($product->price, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-secondary d-block" style="font-size: 0.7rem; margin-top: -5px;">Harga Jual</small>
                                    </div>
                                @endif

                                <!-- Divider tipis jika ada kedua harga -->
                                @if($product->display_price > 0 && $product->rental_price > 0)
                                    <div class="w-50 border-top my-2 opacity-25"></div>
                                @endif

                                <!-- Bagian Harga Sewa -->
                                @if($product->rental_price > 0)
                                    <div class="rental-price-wrapper text-center">
                                        <span class="fw-semibold text-primary">
                                            <small>Rp</small>{{ number_format($product->rental_price, 0, ',', '.') }}
                                            <span class="text-lowercase fw-normal fs-7">/{{ $product->rental_duration }} hari </span>
                                        </span>
                                        <small class="text-secondary d-block" style="font-size: 0.7rem; margin-top: -3px;">Harga Sewa</small>
                                    </div>
                                @endif
                            </div>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
</div>

{{-- CSS STYLE LENGKAP --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap');

    :root {
        --deep-blue: #0f172a;
        --royal-blue: #1e40af;
        --azure-blue: #3b82f6;
        --soft-blue: #eff6ff;
        --gold: #ffd369;
    }

    .luxury-homepage {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #fcfdfe;
        margin-top: -24px;
        overflow-x: hidden;
        padding-top: 20px;
    }

    .container-luxury {
        max-width: 1320px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .serif { font-family: 'Playfair Display', serif !important; }
    .text-azure { color: var(--azure-blue); }
    .text-dark-blue { color: var(--deep-blue); }
    .text-gold { color: var(--gold); }

    /* HERO GRID LOGIC */
    .hero-grid-wrapper {
        display: grid;
        grid-template-columns: 2.2fr 1fr; /* 8:4 Ratio */
        gap: 15px;
    }

    /* MAIN CAROUSEL */
    #luxuryHeroCarousel {
        border-radius: 30px;
        overflow: hidden;
        height: 520px;
    }
    .hero-card {
        position: relative;
        height: 520px;
        background-color: var(--deep-blue);
    }
    .hero-bg, .hero-bg-img {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.7;
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.2) 60%, transparent 100%);
        z-index: 1;
    }
    .hero-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding-left: 50px;
        color: white;
    }
    .hero-title { font-size: 3.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 10px; }
    .hero-subtitle { 
        color: var(--azure-blue); 
        letter-spacing: 0.2em; 
        font-size: 0.75rem; 
        font-weight: 800; 
        text-transform: uppercase; 
        margin-bottom: 1rem; 
        display: inline-block;
        padding: 5px 15px;
        background: rgba(255,255,255,0.1);
        border-radius: 50px;
        width: fit-content;
    }
    .hero-text { max-width: 450px; font-weight: 300; opacity: 0.8; margin-bottom: 2rem; font-size: 1rem; }

    .btn-luxury {
        background: var(--azure-blue);
        color: white;
        padding: 15px 35px;
        border-radius: 100px;
        text-decoration: none;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        transition: 0.4s;
        display: inline-block;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }
    .btn-luxury:hover { background: white; color: var(--deep-blue); transform: translateY(-3px); }

    /* SIDE BANNERS */
    .side-banners-area { display: flex; flex-direction: column; }
    .side-banner-item {
        position: relative;
        flex: 1;
        border-radius: 20px;
        overflow: hidden;
        cursor: pointer;
    }
    .side-banner-item img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
    .side-banner-item:hover img { transform: scale(1.05); }
    .side-banner-overlay {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        padding: 20px;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white;
    }

    /* CATEGORIES */
    .category-scroll { display: flex; gap: 2.5rem; overflow-x: auto; padding: 1rem 0; scrollbar-width: none; }
    .category-scroll::-webkit-scrollbar { display: none; }
    .category-circle-wrapper { margin-bottom: 1rem; transition: 0.3s; }
    .category-circle {
        width: 100px; height: 100px;
        border-radius: 50%;
        overflow: hidden;
        background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
    }
    .category-circle img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
    .category-item:hover .category-circle-wrapper { transform: translateY(-5px); }
    .category-name { font-weight: 800; font-size: 0.7rem; text-transform: uppercase; color: var(--deep-blue); }
    .category-count { font-size: 0.65rem; color: #94a3b8; }

    /* PRODUCTS */
    .product-scroll { display: flex; gap: 1.5rem; overflow-x: auto; padding-bottom: 20px; scrollbar-width: none; }
    .product-scroll::-webkit-scrollbar { display: none; }
    .product-card-luxury { min-width: 240px; flex: 0 0 auto; }
    .product-img-wrapper {
        position: relative;
        aspect-ratio: 4/5;
        border-radius: 25px;
        overflow: hidden;
        background: var(--soft-blue);
    }
    .product-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: 0.8s cubic-bezier(0.2, 0, 0, 1); }
    .product-card-luxury:hover img { transform: scale(1.08); }

    .badge-new, .badge-terjual {
        position: absolute; background: var(--deep-blue); color: white;
        padding: 5px 12px; border-radius: 50px; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; z-index: 2;
    }
    .badge-new { top: 15px; left: 15px; }
    .badge-terjual { bottom: 15px; left: 15px; background: var(--royal-blue); }

    .wishlist-btn-luxury {
        position: absolute; top: 15px; right: 15px;
        width: 38px; height: 38px; border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px); border: none; z-index: 2; transition: 0.3s;
    }
    .product-name-luxury { font-weight: 800; text-transform: uppercase; font-size: 0.8rem; color: var(--deep-blue); margin-bottom: 5px; }
    .original-price { font-weight: 900; font-size: 1.1rem; }
    .sale-price { color: #94a3b8; font-size: 0.8rem; text-decoration: line-through; }

    /* RESPONSIVE */
    @media (max-width: 992px) {
        .hero-grid-wrapper { grid-template-columns: 1fr; }
        .side-banners-area { display: none; }
        #luxuryHeroCarousel, .hero-card { height: 400px; }
        .hero-title { font-size: 2.2rem; }
        .hero-content { padding-left: 30px; }
    }
</style>

{{-- SCRIPT LENGKAP --}}
<script>
    function toggleWishlist(productId) {
        fetch(`/wishlist/toggle/${productId}`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Content-Type": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            const icon = document.querySelector(`.wishlist-btn-${productId} i`);
            if (data.status === 'added') {
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill', 'text-danger');
            } else {
                icon.classList.remove('bi-heart-fill', 'text-danger');
                icon.classList.add('bi-heart');
            }
        })
        .catch(err => console.error("Wishlist error:", err));
    }
</script>
@endsection