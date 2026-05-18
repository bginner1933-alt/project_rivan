@extends('layouts.app')

@section('title', 'Katalog Produk')

@section('content')
<style>
    :root {
        --azure-main: #3b82f6;
        --azure-soft: #eff6ff;
        --dark-slate: #0f172a;
        --radius-extra: 35px; /* 🟢 Variabel baru untuk sudut bulat */
    }

    /* 🌌 LATAR BELAKANG GRADASI DINAMIS */
    body {
        background: linear-gradient(135deg, #47a0ff 0%, #1e3a8a 50%, #1e40af 100%);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ✨ GAYA HEADER */
    .catalog-header {
        padding: 60px 0 40px;
    }

    .display-magazine {
        font-size: 4rem;
        font-weight: 900;
        color: white;
        letter-spacing: -3px;
        line-height: 0.85;
        text-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .text-header-small {
        color: rgba(255, 255, 255, 0.7);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 5px;
        font-size: 0.75rem;
    }

    /* 🏷️ BILAH SAMPING FILTER */
    .filter-sidebar {
        background: #ffffff;
        border-radius: var(--radius-extra);
        padding: 35px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* 🃏 KARTU PRODUK BULAT */
    .col .card, 
    .col x-product-card,
    .product-card-class { 
        border-radius: var(--radius-extra) !important;
        overflow: hidden;
        /* overflow: auto;
        overflow: scroll; */
        border: none !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .col .card img, 
    .col x-product-card img {
        border-top-left-radius: var(--radius-extra) !important;
        border-top-right-radius: var(--radius-extra) !important;
    }

    .col .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important;
    }

    /* 🔘 ITEM KATEGORI KUSTOM */
    .form-check-custom {
        padding: 12px 15px;
        border-radius: 14px;
        transition: 0.3s;
        cursor: pointer;
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }

    .form-check-custom:hover {
        background-color: var(--azure-soft);
    }

    /* 💰 INPUT HARGA */
    .price-input-group {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 15px;
        padding: 5px 15px;
        transition: 0.3s;
    }

    .btn-apply {
        background: var(--dark-slate);
        color: white;
        border-radius: 20px;
        padding: 15px;
        font-weight: 700;
        border: none;
        transition: 0.4s;
        width: 100%;
        margin-top: 10px;
    }

    .btn-apply:hover {
        background: #000;
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        color: white;
    }

    .empty-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: var(--radius-extra);
        padding: 80px 40px;
        text-align: center;
    }

    /* 🔗 PAGINASI KUSTOM */
    .pagination .page-link {
        border: none;
        background: white;
        color: var(--azure-main);
        margin: 0 5px;
        border-radius: 12px !important;
        font-weight: 700;
    }

    .filter-sidebar {
        position: sticky;
        top: 30px;
    }
</style>

<div class="container pb-5">

    {{-- BAGIAN HEADER --}}
    <div class="catalog-header animate__animated animate__fadeIn">
        <div class="row align-items-end">
            <div class="col-md-7">
                <span class="text-header-small">selamat datang di</span>
                <h1 class="display-magazine mt-3">
                    wangi<br>
                    <span class="text-white-50">Projek</span>
                </h1>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="d-flex flex-column align-items-md-end gap-3">
                    <a href="{{ route('home') }}" class="btn btn-light rounded-pill px-4 fw-800 shadow-lg py-3">
                        <i class="bi bi-house-door-fill me-2"></i> Beranda
                    </a>
                    <div class="badge bg-white text-dark rounded-pill px-3 py-2 shadow-sm small fw-bold">
                        TOTAL {{ $products->total() }} PRODUK DITEMUKAN
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 g-lg-5">
        {{-- BILAH SAMPING KIRI: FILTER --}}
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <div class="filter-title" style="font-weight: 800; color: var(--dark-slate); font-size: 1.25rem; margin-bottom: 25px; display: flex; align-items: center;">
                    <i class="bi bi-sliders2-vertical me-3 text-primary"></i> Filter Produk
                </div>

                <form action="{{ route('catalog.index') }}" method="GET">
                    
                   {{-- BAGIAN KATEGORI --}}
                <div class="mb-5">

                    <div class="mb-4">
                            <h6 class="fw-bold mb-2">Kategori</h6>
                            @foreach($categories as $cat)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category" value="{{ $cat->slug }}"
                                        {{ request('category') == $cat->slug ? 'checked' : '' }}
                                        onchange="this.form.submit()">
                                    <label class="form-check-label">{{ $cat->name }} <small class="text-muted">({{ $cat->products_count }})</small></label>
                                </div>
                            @endforeach
                        </div>
                </div>

                    {{-- BAGIAN RENTANG HARGA --}}
                    <div class="mb-5">
                        <p class="text-uppercase small fw-900 text-muted mb-3" style="letter-spacing: 1px;">Rentang Harga</p>
                        
                        <div class="price-input-group d-flex align-items-center mb-3">
                            <span class="text-muted small fw-bold">MIN</span>
                            <input type="number" name="min_price" class="form-control border-0 bg-transparent" 
                                   placeholder="0" value="{{ request('min_price') }}">
                        </div>

                        <div class="price-input-group d-flex align-items-center mb-4">
                            <span class="text-muted small fw-bold">MAX</span>
                            <input type="number" name="max_price" class="form-control border-0 bg-transparent" 
                                   placeholder="Tanpa Batas" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    {{-- GRUP FILTRASI TIPE TRANSAKSI --}}
                    <div class="mb-5">
                        <p class="text-uppercase small fw-900 text-muted mb-3" style="letter-spacing: 1px;">Tipe Transaksi</p>
                        
                        <div class="transaction-group" role="group" aria-label="Tipe Transaksi">
                            <div class="d-flex flex-column gap-2">
                                {{-- Opsi: Semua --}}
                                <input type="radio" class="btn-check" name="category" id="cat-all" value="all" 
                                    {{ request('category') == 'all' || !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="btn btn-outline-custom" for="cat-all">
                                    <i class="bi bi-grid-fill me-2"></i>Semua Produk
                                </label>

                                {{-- Opsi: Beli --}}
                                <input type="radio" class="btn-check" name="category" id="cat-beli" value="beli" 
                                    {{ request('category') == 'beli' ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="btn btn-outline-custom" for="cat-beli">
                                    <i class="bi bi-bag-check-fill me-2"></i>Hanya Beli
                                </label>

                                {{-- Opsi: Sewa --}}
                                <input type="radio" class="btn-check" name="category" id="cat-sewa" value="sewa" 
                                    {{ request('category') == 'sewa' ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="btn btn-outline-custom" for="cat-sewa">
                                    <i class="bi bi-calendar-event-fill me-2"></i>Hanya Sewa
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-apply">
                       Terapkan Filter<i class="bi bi-arrow-right-short ms-1"></i>
                    </button>

                    <a href="{{ route('catalog.index') }}" class="btn btn-link w-100 text-muted mt-3 text-decoration-none small fw-bold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> ATUR ULANG FILTER
                    </a>
                </form>
            </div>
        </div>

        {{-- KONTEN KANAN: GRID PRODUK --}}
        <div class="col-lg-9">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-4 animate__animated animate__fadeInUp">
                @forelse($products as $product)
                    <div class="col">
                        <x-product-card :product="$product" />
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-container">
                            <div class="mb-4">
                                <i class="bi bi-search-heart text-primary opacity-25" style="font-size: 5rem;"></i>
                            </div>
                            <h3 class="fw-900 text-dark">Produk Tidak Ditemukan</h3>
                            <p class="text-muted mx-auto mb-4" style="max-width: 400px;">
                                Kami tidak dapat menemukan produk yang sesuai dengan kriteria filter Anda saat ini.
                            </p>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">
                                LIHAT SEMUA PRODUK
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- PAGINASI --}}
            @if($products->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    <div class="p-2 bg-white rounded-pill shadow-sm">
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection