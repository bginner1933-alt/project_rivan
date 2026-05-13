@extends('layouts.app')

@section('title', 'Beranda - Koleksi Premium')

@section('content')
<div class="luxury-homepage">
    {{-- 1. HERO SECTION --}}
    <header class="hero-container">
        <div class="hero-card shadow-lg animate__animated animate__fadeIn">
            {{-- Background Video/Image --}}
            <video class="hero-bg" autoplay muted loop playsinline>
                <source src="{{ asset('videos/iklan.mp4') }}" type="video/mp4">
            </video>
            <div class="hero-overlay"></div>
            
            <div class="hero-content">
                <div class="reveal-text">
                    <span class="hero-subtitle">Wangi Shop</span>
                    <h1 class="serif hero-title">Kemewahan <br> <span class="text-azure">Eksklusif.</span></h1>
                    <p class="hero-text">
                        Temukan keindahan dalam setiap detail produk kami yang dirancang khusus untuk gaya hidup modern Anda.
                    </p>
                    <div class="hero-cta">
                        <a href="{{ route('catalog.index') }}" class="btn-luxury">
                            Jelajahi Koleksi <i class="bi bi-arrow-right ms-2"></i>
                        </a>
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
            <div>
                <div class="accent-line me-3"></div>
                    <h2 class="serif display-4 italic text-dark-blue">Kategori Populer</h2>
                <div class="flex-grow-1 ms-4 border-bottom opacity-25"></div>
            </div>

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
                                            <span class="text-lowercase fw-normal fs-7">/{{ $product->rental_unit ?? 'hari' }}</span>
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

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&family=Playfair+Display:ital,wght@0,700;1,700&display=swap');

    :root {
        --deep-blue: #0f172a;
        --royal-blue: #1e40af;
        --azure-blue: #3b82f6;
        --soft-blue: #eff6ff;
    }

    .luxury-homepage {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #fcfdfe;
        margin-top: -24px;
        overflow-x: hidden;
    }

    .serif { font-family: 'Playfair Display', serif !important; }
    .text-azure { color: var(--azure-blue); }
    .text-dark-blue { color: var(--deep-blue); }

    /* Hero Section */
    .hero-container { padding: 20px; }
    .hero-card {
        position: relative;
        width: 100%;
        height: 70vh;
        min-height: 550px;
        border-radius: 40px;
        overflow: hidden;
        background-color: var(--deep-blue);
    }
    .hero-bg {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.6;
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(75deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.1) 100%);
    }
    .hero-content {
        position: relative;
        z-index: 2;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding-left: 8%;
        color: white;
    }
    .hero-title { font-size: clamp(2.5rem, 6vw, 4.5rem); font-weight: 800; line-height: 1; }
    .hero-subtitle { color: var(--azure-blue); letter-spacing: 0.5em; font-size: 0.8rem; font-weight: 800; text-transform: uppercase; margin-bottom: 1rem; display: block; }
    .hero-text { max-width: 500px; font-weight: 300; opacity: 0.8; margin: 1.5rem 0 2.5rem; font-size: 1.1rem; }

    .btn-luxury {
        background: var(--azure-blue);
        color: white;
        padding: 18px 40px;
        border-radius: 100px;
        text-decoration: none;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 2px;
        transition: 0.4s;
        display: inline-block;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
    }
    .btn-luxury:hover { background: white; color: var(--deep-blue); transform: translateY(-5px); }

    /* Categories */
    .category-scroll { display: flex; gap: 3rem; overflow-x: auto; padding: 1rem 0.5rem; scrollbar-width: none; }
    .category-scroll::-webkit-scrollbar { display: none; }
    /* .category-circle-wrapper {
        padding: 6px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--azure-blue) 0%, transparent 100%);
        transition: 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        margin-bottom: 1rem;
    }
    .category-circle {
        width: 120px; height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        overflow: hidden;
        background: white;
    } */
    .category-circle img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
    .category-item:hover .category-circle img { transform: scale(1.1); }
    .category-item:hover .category-circle-wrapper { transform: rotate(10deg) scale(1.05); }
    .category-name { font-weight: 800; font-size: 0.7rem; text-transform: uppercase; color: var(--deep-blue); margin-bottom: 0.2rem; }
    .category-count { font-size: 0.65rem; color: #94a3b8; font-weight: 600; }

    /* Featured Section */
    .featured-section { background-color: #fff; border-radius: 60px 60px 0 0; }
    .product-img-wrapper {
        position: relative;
        aspect-ratio: 4/5;
        border-radius: 30px;
        overflow: hidden;
        background: var(--soft-blue);
    }
    .product-img-wrapper img { width: 100%; height: 100%; object-fit: cover; transition: 0.8s cubic-bezier(0.2, 0, 0, 1); }
    .product-card-luxury:hover img { transform: scale(1.08); }

    .badge-new {
        position: absolute; top: 20px; left: 20px;
        background: var(--deep-blue); color: white;
        padding: 6px 14px; border-radius: 50px;
        font-size: 0.6rem; font-weight: 800; text-transform: uppercase;
    }

    .badge-terjual {
        position: absolute; 
        bottom: 20px; 
        left: 20px;

        background: var(--royal-blue); 
        color: white;
        
        padding: 6px 14px; 
        border-radius: 50px;
        
        font-size: 0.6rem; 
        font-weight: 800; 
        text-transform: uppercase;
    }

    .wishlist-btn-luxury {
        position: absolute; top: 20px; right: 20px;
        width: 40px; height: 40px; border-radius: 50%;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border: none; display: flex; align-items: center; justify-content: center;
        transition: 0.3s;
    }
    .wishlist-btn-luxury:hover { background: white; transform: scale(1.15); }

    .product-name-luxury { font-weight: 800; text-transform: uppercase; font-size: 0.75rem; color: var(--deep-blue); margin-bottom: 0.8rem; }
    
    .original-price { color: #ef4444; font-weight: 900; font-size: 1.2rem; }
    .sale-price { color: #94a3b8; font-size: 0.8rem; text-decoration: line-through; font-weight: 600; }

    .product-scroll {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    padding-bottom: 10px;
    scroll-snap-type: x mandatory;
    }

    .product-scroll::-webkit-scrollbar {
        display: none;
    }

    .product-card-luxury {
        min-width: 200px;
        flex: 0 0 auto;
        scroll-snap-align: start;
    }

    .wishlist-btn-luxury i {
        transition: 0.2s ease;
    }

    .wishlist-btn-luxury:hover i {
        transform: scale(1.2);
    }

    .category-circle-wrapper {
        margin-bottom: 1rem;
    }

    .category-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;

        position: relative;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #f3f4f6;
    }

    .category-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .category-text-fallback {
        width: 100%;
        height: 100%;

        display: flex;
        align-items: center;
        justify-content: center;

        text-align: center;
        padding: 10px;

        font-size: 15px;
        font-weight: 700;
        color: #111827;

        background: #f3f4f6;
    }
</style>
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
    });
}
</script>
@endsection