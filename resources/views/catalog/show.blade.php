@extends('layouts.app')

@section('title', $product->name)

@section('content')
<style>
    :root {
        --primary-blue: #3b82f6;
        --deep-blue: #1e40af;
        --glass-bg: rgba(255, 255, 255, 0.9);
        --radius-extra: 35px; /* 🟢 Konsisten dengan halaman katalog */
    }

    body { 
        background: linear-gradient(135deg, #47a0ff 0%, #1e3a8a 50%, #1e40af 100%);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .product-container {
        padding-top: 60px;
        padding-bottom: 80px;
    }

    /* 🖼️ IMAGE SHOWCASE */
    .image-showcase {
        position: sticky;
        top: 100px;
        background: rgba(255, 255, 255, 0.1);
        padding: 40px;
        border-radius: var(--radius-extra);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .main-product-img {
        max-height: 450px;
        width: 100%;
        object-fit: contain;
        filter: drop-shadow(0 20px 40px rgba(0,0,0,0.3));
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    
    .main-product-img:hover {
        transform: scale(1.05);
    }

    .thumb-img {
        width: 75px;
        height: 75px;
        object-fit: cover;
        border-radius: 15px;
        cursor: pointer;
        border: 3px solid transparent;
        transition: 0.3s;
    }

    .thumb-img:hover {
        border-color: var(--primary-blue);
        transform: translateY(-5px);
    }

    /* 📄 INFO CARD */
    .info-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        border-radius: var(--radius-extra);
        padding: 40px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.4);
    }

    .badge-category {
        background: var(--primary-blue);
        color: white;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* 🎚️ MODE SWITCH */
    .mode-selector {
        background: rgba(0,0,0,0.05);
        padding: 8px;
        border-radius: 20px;
        display: inline-flex;
        gap: 5px;
        margin-bottom: 25px;
    }

    .mode-btn {
        padding: 10px 25px;
        border-radius: 15px;
        cursor: pointer;
        font-weight: 700;
        transition: 0.3s;
        user-select: none;
    }

    /* 🔘 FORM ELEMENTS */
    .form-control, .form-select {
        border-radius: 15px;
        padding: 12px 15px;
        border: 2px solid #eee;
        font-weight: 600;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: none;
    }

    .btn-action {
        border-radius: 20px;
        padding: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.3s;
    }

    .btn-action:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>

<div class="container product-container">
    {{-- TOMBOL KEMBALI --}}
    <div class="mb-4">
        <a href="{{ route('catalog.index') }}" class="text-white text-decoration-none fw-bold">
            <i class="bi bi-arrow-left me-2"></i> KEMBALI KE KATALOG
        </a>
    </div>

    <div class="row g-5">
        {{-- BAGIAN GAMBAR --}}
        <div class="col-lg-6">
            <div class="image-showcase text-center animate__animated animate__fadeInLeft">
                <img src="{{ $product->image_url }}" class="main-product-img" id="main-image">

                @if($product->images->count() > 0)
                    <div class="d-flex gap-3 justify-content-center mt-4">
                        {{-- Gambar Utama --}}
                        <img src="{{ $product->image_url }}" class="thumb-img" onclick="changeImage(this.src)">
                        
                        {{-- Gambar Tambahan --}}
                        @foreach($product->images as $img)
                            <img src="{{ asset('storage/'.$img->image_path) }}"
                                 class="thumb-img"
                                 onclick="changeImage(this.src)">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- BAGIAN INFO --}}
        <div class="col-lg-6">
            <div class="info-card animate__animated animate__fadeInRight">
                <div class="mb-3">
                    <span class="badge-category">{{ $product->category->name ?? 'Produk' }}</span>
                </div>

                <h1 class="fw-900 mb-3" style="color: var(--dark-slate); letter-spacing: -1px;">{{ $product->name }}</h1>

                <p class="text-muted mb-4" style="line-height: 1.8;">
                    {{ $product->description }}
                </p>

                <hr class="opacity-10 mb-4">

                {{-- HARGA --}}
                {{-- HARGA --}}
                <div class="mb-4">

                    @if($product->price > 0 || $product->rental_price > 0)

                        <span class="text-muted small d-block fw-bold mb-1">
                            HARGA ESTIMASI
                        </span>

                        <h2 class="fw-900 mb-0 
                            {{ $product->price ? 'text-primary' : 'text-info' }}"
                            id="price-display"

                            data-buy="Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}"

                            data-rent="Rp {{ number_format($product->rental_price ?? 0, 0, ',', '.') }} / {{ $product->rental_unit }}">

                            @if($product->price > 0)
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            @elseif($product->rental_price > 0)
                                Rp {{ number_format($product->rental_price, 0, ',', '.') }} / {{ $product->rental_unit }}
                            @endif

                        </h2>

                    @endif

                </div>

                {{-- MODE SWITCH --}}
                @if($product->price > 0 && $product->rental_price > 0)
                    <div class="mb-4">
                        <label class="fw-800 small d-block mb-2 text-muted">OPSI TRANSAKSI</label>
                        <div class="d-flex gap-3">
                            <div class="form-check custom-option">
                                <input class="form-check-input"
                                    type="radio"
                                    name="mode"
                                    id="modeBuy"
                                    value="buy"
                                    {{ $product->price ? 'checked' : '' }}
                                    onchange="setMode(this.value)">
                                <label class="form-check-label fw-bold" for="modeBuy">Beli Produk</label>
                            </div>
                            <div class="form-check custom-option">
                                <input class="form-check-input"
                                    type="radio"
                                    name="mode"
                                    id="modeRent"
                                    value="rent"
                                    {{ !$product->price && $product->rental_price ? 'checked' : '' }}
                                    onchange="setMode(this.value)">
                                <label class="form-check-label fw-bold" for="modeRent">Sewa Produk</label>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- FORM BELI --}}
                @if($product->price > 0)
                <form id="buyForm" action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="type" value="buy">

                    <button type="submit" class="btn btn-primary btn-action w-100 py-3 shadow">
                        <i class="bi bi-cart-plus-fill me-2"></i> TAMBAH KE KERANJANG
                    </button>
                </form>
                @endif

                {{-- FORM SEWA --}}
                @if($product->rental_price > 0)
                    <form id="rentForm" action="{{ route('cart.add') }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="type" value="rent">

                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="small fw-bold mb-1">JUMLAH ITEM</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="small fw-bold mb-1">DURASI</label>
                                <input type="number" name="duration" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-12">
                                <label class="small fw-bold mb-1">SATUAN WAKTU</label>
                                <select name="unit" class="form-select" required>
                                    <option value="hour">Jam</option>
                                    <option value="day" selected>Hari</option>
                                    <option value="week">Minggu</option>
                                    <option value="month">Bulan</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-info btn-action w-100 py-3 text-white shadow">
                            <i class="bi bi-calendar-check me-2"></i> MULAI SEWA SEKARANG
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>

<script>
    // Ganti Gambar Utama
    function changeImage(src) {
        const mainImg = document.getElementById('main-image');
        mainImg.style.opacity = '0';
        setTimeout(() => {
            mainImg.src = src;
            mainImg.style.opacity = '1';
        }, 200);
    }

    // Ganti Mode (Beli/Sewa)
    function setMode(mode) {
        const buyForm = document.getElementById('buyForm');
        const rentForm = document.getElementById('rentForm');
        const priceDisplay = document.getElementById('price-display');

        const buyPrice = priceDisplay.dataset.buy;
        const rentPrice = priceDisplay.dataset.rent;

        if (mode === 'buy') {
            if (buyForm) buyForm.style.display = 'block';
            if (rentForm) rentForm.style.display = 'none';
            priceDisplay.innerText = buyPrice;
            priceDisplay.classList.replace('text-info', 'text-primary');
        } 
        else {
            if (buyForm) buyForm.style.display = 'none';
            if (rentForm) rentForm.style.display = 'block';
            priceDisplay.innerText = rentPrice === "0" ? 'Harga Tidak Tersedia' : rentPrice;
            priceDisplay.classList.replace('text-primary', 'text-info');
        }
    }

    // AUTO MODE
    document.addEventListener('DOMContentLoaded', function () {

        const hasBuyPrice = @json((bool) $product->price);
        const hasRentalPrice = @json((bool) $product->rental_price);

        // kalau tidak ada harga beli tapi ada harga sewa
        if (!hasBuyPrice && hasRentalPrice) {
            setMode('rent');
        }
    });
</script>
@endsection