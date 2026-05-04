@extends('layouts.app')

@section('title', 'Product Catalog')

@section('content')
<style>
    :root {
        --azure-main: #3b82f6;
        --azure-soft: #eff6ff;
        --dark-slate: #0f172a;
    }

    /* 🌌 DYNAMIC GRADIENT BACKGROUND */
    body {
        background: linear-gradient(135deg, #47a0ff 0%, #3b82f6 50%, #1e40af 100%);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ✨ HEADER STYLING */
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

    /* 🏷️ FILTER SIDEBAR (CLEAN WHITE) */
    .filter-sidebar {
        background: #ffffff;
        border-radius: 28px;
        padding: 35px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .filter-title {
        font-weight: 800;
        color: var(--dark-slate);
        font-size: 1.25rem;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }

    /* 🔘 CATEGORY ITEM CUSTOM */
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

    .form-check-input-custom {
        width: 1.2em;
        height: 1.2em;
        margin-right: 12px;
        cursor: pointer;
        border: 2px solid #cbd5e1;
    }

    .form-check-input-custom:checked {
        background-color: var(--azure-main);
        border-color: var(--azure-main);
    }

    /* 💰 PRICE INPUTS */
    .price-input-group {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        border-radius: 15px;
        padding: 5px 15px;
        transition: 0.3s;
    }

    .price-input-group:focus-within {
        border-color: var(--azure-main);
        background: white;
    }

    .price-input-group input {
        border: none;
        background: transparent;
        padding: 10px 5px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .price-input-group input:focus { outline: none; }

    /* 🚀 ACTION BUTTONS */
    .btn-apply {
        background: var(--dark-slate);
        color: white;
        border-radius: 16px;
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

    /* 📄 EMPTY STATE */
    .empty-container {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 30px;
        padding: 80px 40px;
        text-align: center;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    /* 🔗 PAGINATION CUSTOM */
    .pagination .page-link {
        border: none;
        background: white;
        color: var(--azure-main);
        margin: 0 5px;
        border-radius: 12px !important;
        font-weight: 700;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }

    .pagination .page-item.active .page-link {
        background: var(--azure-main);
        color: white;
    }
</style>

<div class="container pb-5">

    {{-- HEADER SECTION --}}
    <div class="catalog-header animate__animated animate__fadeIn">
        <div class="row align-items-end">
            <div class="col-md-7">
                <span class="text-header-small">Premium Quality</span>
                <h1 class="display-magazine mt-3">
                    Our Best<br>
                    <span class="text-white-50">Catalog.</span>
                </h1>
            </div>
            <div class="col-md-5 text-md-end mt-4 mt-md-0">
                <div class="d-flex flex-column align-items-md-end gap-3">
                    <a href="{{ route('home') }}" class="btn btn-light rounded-pill px-4 fw-800 shadow-lg py-3">
                        <i class="bi bi-house-door-fill me-2"></i> BACK TO HOME
                    </a>
                    <div class="badge bg-white text-dark rounded-pill px-3 py-2 shadow-sm small fw-bold">
                        TOTAL {{ $products->total() }} PRODUCTS FOUND
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 g-lg-5">

        {{-- LEFT SIDEBAR: FILTERS --}}
        <div class="col-lg-3">
            <div class="filter-sidebar sticky-top animate__animated animate__fadeInLeft" style="top: 30px;">
                <div class="filter-title">
                    <i class="bi bi-sliders2-vertical me-3 text-primary"></i> Filter Option
                </div>

                <form action="{{ route('catalog.index') }}" method="GET">
                    
                    {{-- CATEGORY SECTION --}}
                    <div class="mb-5">
                        <p class="text-uppercase small fw-900 text-muted mb-3" style="letter-spacing: 1px;">Categories</p>
                        @foreach($categories as $cat)
                            <label class="form-check-custom" for="c-{{ $cat->id }}">
                                <input class="form-check-input-custom" type="radio" name="category" 
                                       value="{{ $cat->slug }}" id="c-{{ $cat->id }}"
                                       {{ request('category') == $cat->slug ? 'checked' : '' }}
                                       onchange="this.form.submit()">
                                <div class="d-flex justify-content-between w-100 align-items-center">
                                    <span class="fw-bold text-dark">{{ $cat->name }}</span>
                                    <span class="badge rounded-pill bg-light text-primary px-2 py-1">{{ $cat->products_count }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    {{-- PRICE RANGE SECTION --}}
                    <div class="mb-5">
                        <p class="text-uppercase small fw-900 text-muted mb-3" style="letter-spacing: 1px;">Price Range</p>
                        
                        <div class="price-input-group d-flex align-items-center mb-3">
                            <span class="text-muted small fw-bold">MIN</span>
                            <input type="number" name="min_price" class="form-control" 
                                   placeholder="0" value="{{ request('min_price') }}">
                        </div>

                        <div class="price-input-group d-flex align-items-center mb-4">
                            <span class="text-muted small fw-bold">MAX</span>
                            <input type="number" name="max_price" class="form-control" 
                                   placeholder="∞" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-apply">
                        APPLY FILTERS <i class="bi bi-arrow-right-short ms-1"></i>
                    </button>

                    <a href="{{ route('catalog.index') }}" class="btn btn-link w-100 text-muted mt-3 text-decoration-none small fw-bold">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> RESET FILTERS
                    </a>
                </form>
            </div>
        </div>

        {{-- RIGHT CONTENT: PRODUCT GRID --}}
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
                            <h3 class="fw-900 text-dark">No Products Found</h3>
                            <p class="text-muted mx-auto mb-4" style="max-width: 400px;">
                                We couldn't find any products matching your current filters. Try adjusting your price range or category.
                            </p>
                            <a href="{{ route('catalog.index') }}" class="btn btn-primary rounded-pill px-5 py-3 fw-bold">
                                VIEW ALL PRODUCTS
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- PAGINATION --}}
            @if($products->hasPages())
                <div class="mt-5 d-flex justify-content-center">
                    <div class="p-3 bg-white rounded-pill shadow-sm">
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection