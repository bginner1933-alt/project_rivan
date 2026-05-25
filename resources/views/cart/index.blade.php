{{-- resources/views/cart/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<style>
    :root {
        --azure-main: #3b82f6;
        --azure-soft: #eff6ff;
        --dark-slate: #0f172a;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    body {
        background: linear-gradient(180deg, #3b82f6 0%, #1e40af 100%);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ✨ MODERN HEADER */
    .cart-header-section {
        padding: 40px 0;
    }

    .cart-title {
        font-weight: 800;
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        color: white;
        letter-spacing: -2px;
        line-height: 1;
    }

    /* 📦 ITEM CARD STYLING */
    .cart-item-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border-radius: 28px;
        border: 1px solid rgba(255,255,255,0.3);
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        margin-bottom: 1.5rem;
    }

    .cart-item-card:hover {
        transform: scale(1.01);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .product-img-wrapper {
        border-radius: 20px;
        overflow: hidden;
        width: 110px;
        height: 110px;
        flex-shrink: 0;
        background: #f1f5f9;
    }

    /* 🔢 QUANTITY CONTROL */
    .qty-wrapper {
        background: #f8fafc;
        border-radius: 14px;
        padding: 6px;
        display: inline-flex;
        align-items: center;
        border: 1px solid #e2e8f0;
    }

    .qty-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: none;
        background: white;
        color: var(--azure-main);
        font-weight: 800;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .qty-btn:hover { 
        background: var(--azure-main); 
        color: white; 
    }

    .qty-input-hidden {
        width: 50px;
        background: transparent;
        border: none;
        text-align: center;
        font-weight: 800;
        color: var(--dark-slate);
        font-size: 1rem;
    }

    /* 💳 SUMMARY SIDEBAR */
    .summary-card {
        background: #ffffff;
        border-radius: 35px;
        border: none;
        padding: 35px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.25);
    }

    .btn-checkout {
        background: var(--dark-slate);
        color: white;
        border-radius: 18px;
        padding: 20px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border: none;
        width: 100%;
        transition: 0.4s;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
    }

    .btn-checkout:hover {
        background: #000;
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        color: white;
    }

    /* 🗑️ DELETE BUTTON */
    .btn-remove {
        color: #ef4444;
        background: #fff1f2;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }

    .btn-remove:hover {
        background: #ef4444;
        color: white;
        transform: rotate(90deg);
    }

    .badge-category {
        background: var(--azure-soft);
        color: var(--azure-main);
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 50px;
    }
</style>

<div class="container py-5">
    <div class="cart-header-section animate__animated animate__fadeIn">
        <span class="text-uppercase fw-bold text-white-50 small letter-spacing-3">Review Pesanan</span>
        <h1 class="cart-title">Keranjang <span class="text-white-50">Belanja.</span></h1>
    </div>

    @if(!empty($cartItems) && count($cartItems) > 0)
        <div class="row g-4 mt-2">
            {{-- ITEM LIST --}}
            <div class="col-lg-8 animate__animated animate__fadeInLeft">
                @foreach($cartItems as $index => $item)
                    @php $product = $item['product'] ?? null; @endphp
                    @if($product)
                        <div class="cart-item-card d-flex align-items-center flex-wrap flex-md-nowrap shadow-sm">
                            {{-- Image --}}
                            <div class="product-img-wrapper me-4 shadow-sm">
                                <img src="{{ $product->image_url ?? asset('images/default-product.jpg') }}" class="w-100 h-100" style="object-fit: cover;">
                            </div>

                            {{-- Product Detail --}}
                            <div class="flex-grow-1 mt-3 mt-md-0">
                                <span class="badge-category mb-2 d-inline-block">{{ $product->category->name ?? 'Premium Item' }}</span>
                                <h5 class="fw-800 text-dark mb-1">{{ Str::limit($product->name ?? 'Produk Tanpa Nama', 50) }}</h5>
                                
                                <p class="text-primary fw-bold fs-5 mb-0">
                                    Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }}
                                    @if(($item['type'] ?? 'buy') === 'rent')
                                        <span class="text-muted small fw-normal">/ {{ $item['duration'] ?? 1 }} {{ $item['unit'] ?? 'day' }}</span>
                                    @endif
                                </p>
                            </div>

                            {{-- Actions (Qty & Subtotal) --}}
                            <div class="d-flex align-items-center gap-4 mt-4 mt-md-0 ms-md-auto w-100 w-md-auto justify-content-between">
                                {{-- 🛠️ DISINI SUDAH FIXED: Menggunakan @method('PATCH') yang benar --}}
                                {{-- GANTI FORM QUANTITY LAMA DENGAN INI --}}
                                <form id="form-update-{{ $item['id'] }}" action="{{ route('cart.update', $item['id']) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="qty-wrapper">
                                        <button type="button" class="qty-btn" onclick="this.nextElementSibling.stepDown(); document.getElementById('form-update-{{ $item['id'] }}').requestSubmit();">
                                            <i class="bi bi-dash"></i>
                                        </button>
                                        
                                        <input type="number" name="quantity" value="{{ $item['quantity'] ?? 1 }}" min="1" max="{{ $product->stock ?? 99 }}" class="qty-input-hidden" readonly>
                                        
                                        <button type="button" class="qty-btn" onclick="this.previousElementSibling.stepUp(); document.getElementById('form-update-{{ $item['id'] }}').requestSubmit();">
                                            <i class="bi bi-plus"></i>
                                        </button>
                                    </div>
                                </form>

                                <div class="text-end px-2" style="min-width: 140px;">
                                    <span class="small text-muted d-block fw-bold text-uppercase" style="font-size: 0.65rem;">Subtotal</span>
                                    <span class="fw-800 text-dark fs-5">Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}</span>
                                </div>

                                <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-remove" onclick="return confirm('Hapus item ini?')">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- CHECKOUT PANEL --}}
            <div class="col-lg-4 animate__animated animate__fadeInRight">
                <div class="summary-card" style="top: 100px;">
                    <h4 class="fw-800 text-dark mb-4">Ringkasan Pesanan</h4>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted fw-600">Total Produk</span>
                        <span class="fw-800 text-dark">{{ $totalQuantity ?? 0 }} Unit</span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted fw-600">Pajak (PPN 11%)</span>
                        <span class="text-success fw-bold small">Sudah Termasuk</span>
                    </div>

                    <hr class="opacity-10 mb-4">

                    <div class="p-4 rounded-4 mb-4" style="background: #f8fafc; border: 2px solid #eff6ff;">
                        <span class="fw-bold text-muted small text-uppercase d-block mb-1">Total Pembayaran</span>
                        <h2 class="fw-800 text-primary mb-0">
                            Rp {{ number_format($total ?? 0, 0, ',', '.') }}
                        </h2>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn btn-checkout mb-3 shadow-lg">
                        Checkout Sekarang <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                    </a>
                    
                    <a href="{{ route('catalog.index') }}" class="btn btn-link w-100 text-center text-decoration-none text-muted small fw-bold">
                        <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    @else
        {{-- EMPTY STATE --}}
        <div class="animate__animated animate__zoomIn">
            <div class="text-center py-5 px-4 bg-white shadow-lg rounded-5" style="border-radius: 40px !important;">
                <div class="mb-4 d-inline-block p-4 rounded-circle" style="background: #f0f9ff;">
                    <i class="bi bi-cart-x text-primary" style="font-size: 5rem; opacity: 0.5;"></i>
                </div>
                <h2 class="fw-800 text-dark">Keranjang Kosong.</h2>
                <p class="text-muted mb-5 mx-auto" style="max-width: 400px;">
                    Sepertinya kamu belum memilih item mewah untuk dibawa pulang. Mari jelajahi koleksi terbaik kami.
                </p>
                <a href="{{ route('catalog.index') }}" class="btn btn-primary px-5 py-3 rounded-pill fw-800 shadow-lg text-uppercase" style="letter-spacing: 1px;">
                    Mulai Belanja
                </a>
            </div>
        </div>
    @endif
</div>
@endsection