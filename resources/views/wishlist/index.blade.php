{{-- resources/views/wishlist/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Wishlist Saya')

@section('content')
<style>
    :root {
        --azure-main: #3b82f6;
        --azure-dark: #1e3a8a;
        --dark-slate: #0f172a;
        --glass-white: rgba(255, 255, 255, 0.95);
    }

    /* 🌌 BACKGROUND & OVERLAY */
    body {
        background: linear-gradient(180deg, #47a0ff 0%, #3b82f6 100%);
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* HEADER STYLING */
    .wishlist-title {
        font-weight: 800;
        font-size: clamp(2.5rem, 5vw, 3.8rem);
        letter-spacing: -2px;
        color: white;
        line-height: 1;
    }

    .wishlist-title span {
        color: var(--dark-slate); /* Kontras menarik pada kata 'Favorit' */
    }

    .text-header-small {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 4px;
        display: block;
    }

    /* BACK BUTTON */
    .btn-back-home {
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: white;
        color: var(--azure-main);
        padding: 14px 28px;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .btn-back-home:hover {
        background-color: var(--dark-slate);
        color: white;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    /* PRODUCT WRAPPER */
    .wishlist-item-container {
        transition: all 0.4s ease;
        animation: fadeInUp 0.6s ease forwards;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* EMPTY STATE GLASSMORPHISM */
    .empty-container {
        background: var(--glass-white);
        backdrop-filter: blur(10px);
        border-radius: 40px;
        padding: 100px 40px;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .btn-explore {
        background-color: var(--azure-main);
        color: white;
        border-radius: 50px;
        padding: 15px 35px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 1px;
        transition: 0.3s;
        border: none;
    }

    .btn-explore:hover {
        background-color: var(--dark-slate);
        color: white;
        transform: scale(1.05);
    }

    /* CUSTOM PAGINATION FOR DARK BG */
    .pagination .page-link {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        border-radius: 12px !important;
        margin: 0 4px;
        padding: 10px 18px;
    }

    .pagination .page-item.active .page-link {
        background: white !important;
        color: var(--azure-main) !important;
        border-color: white !important;
    }
</style>

<div class="container py-5">
    {{-- HEADER SECTION --}}
    <div class="row mb-5 align-items-end">
        <div class="col-md-8">
            <span class="text-header-small mb-2 animate__animated animate__fadeIn">Koleksi Pribadi</span>
            <h1 class="wishlist-title animate__animated animate__fadeInLeft">Wishlist <br><span>Favorit Anda.</span></h1>
        </div>
        <div class="col-md-4 text-md-end mt-4 mt-md-0">
            <a href="{{ route('home') }}" class="btn-back-home shadow-sm animate__animated animate__fadeInRight">
                <i class="bi bi-house-door-fill me-2"></i>Beranda Utama
            </a>
        </div>
    </div>

    @if($products->count())
        {{-- GRID PRODUK --}}
        <div class="row row-cols-2 row-cols-md-4 g-4 g-lg-5">
            @foreach($products as $index => $product)
                <div class="col wishlist-item-container" style="animation-delay: {{ $index * 0.1 }}s">
                    <x-product-card :product="$product" />
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div class="mt-5 d-flex justify-content-center">
            {{ $products->links() }}
        </div>

    @else
        {{-- TAMPILAN KOSONG --}}
        <div class="empty-container text-center shadow-lg animate__animated animate__zoomIn">
            <div class="mb-4">
                <div class="icon-circle mb-4 mx-auto d-flex align-items-center justify-content-center" 
                     style="width: 120px; height: 120px; background: rgba(59, 130, 246, 0.1); border-radius: 50%;">
                    <i class="bi bi-heart-break text-azure-main" style="font-size: 4rem; color: var(--azure-main);"></i>
                </div>
            </div>
            <h2 class="fw-800 text-dark mb-2">Belum ada item impian.</h2>
            <p class="text-muted mb-5 mx-auto" style="max-width: 400px;">
                Sepertinya kamu belum menandai produk apapun. Mulai jelajahi katalog kami dan temukan kemewahan yang pas untukmu.
            </p>
            
            <div class="d-flex justify-content-center gap-3 flex-column flex-md-row">
                <a href="{{ route('catalog.index') }}" class="btn-explore shadow-sm">
                    Mulai Belanja <i class="bi bi-bag-plus ms-2"></i>
                </a>
                <a href="{{ route('home') }}" class="btn btn-outline-dark px-4 fw-bold d-flex align-items-center justify-content-center" style="border-radius: 50px;">
                    Kembali Ke Beranda
                </a>
            </div>
        </div>
    @endif
</div>
@endsection     