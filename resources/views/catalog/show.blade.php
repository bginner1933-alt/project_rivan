@extends('layouts.app')

@section('title', $product->name)

@section('content')
<style>
:root {
    --primary-blue: #3b82f6;
    --deep-blue: #1e40af;
    --glass-bg: rgba(255, 255, 255, 0.85);
}

body { 
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    min-height: 100vh;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.product-container {
    margin-top: 50px;
    margin-bottom: 50px;
}

.image-showcase {
    position: sticky;
    top: 100px;
}

.main-product-img {
    max-height: 500px;
    width: 100%;
    object-fit: contain;
    filter: drop-shadow(0 20px 30px rgba(0,0,0,0.2));
}

.info-card {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 30px;
    padding: 30px;
}
</style>

<div class="container product-container">

    <div class="row g-5">

        {{-- IMAGE --}}
        <div class="col-lg-6">
            <div class="image-showcase text-center">
                <img src="{{ $product->image_url }}" class="main-product-img" id="main-image">

                @if($product->images->count() > 1)
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        @foreach($product->images as $img)
                            <img src="{{ asset('storage/'.$img->image_path) }}"
                                 style="width:70px;height:70px;object-fit:cover;border-radius:10px;cursor:pointer"
                                 onclick="document.getElementById('main-image').src=this.src">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- INFO --}}
        <div class="col-lg-6">
            <div class="info-card">

                <h2 class="fw-bold">{{ $product->name }}</h2>

                <p class="text-muted">{{ Str::limit($product->description, 120) }}</p>

                {{-- PRICE --}}
                <h3 class="text-primary fw-bold" id="price-display"
                    data-buy="{{ $product->formatted_price }}"
                    data-rent="@if($product->rental_price) Rp {{ number_format($product->rental_price, 0, ',', '.') }} / {{ $product->rental_unit }} @else 0 @endif">
                    {{ $product->formatted_price }}
                </h3>

                {{-- MODE SWITCH --}}
                @if($product->rental_price > 0)
                    <div class="mb-3">
                        <label class="fw-bold small">Pilih Mode</label>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="buy" checked onchange="setMode(this.value)">
                            <label>Beli (Keranjang)</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="mode" value="rent" onchange="setMode(this.value)">
                            <label>Sewa</label>
                        </div>
                    </div>
                @endif

                {{-- ================= BUY ================= --}}
                <form id="buyForm" action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="type" value="buy">

                    <button type="submit" class="btn btn-primary w-100">
                        Tambah ke Keranjang
                    </button>
                </form>

                {{-- ================= RENT ================= --}}
                @if($product->rental_price > 0)
                    <form id="rentForm" action="{{ route('cart.add') }}" method="POST" style="display:none;">
                        @csrf

                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="type" value="rent">

                        <div class="row g-2">

                            <div class="col-6">
                                <label class="small">Jumlah</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                            </div>

                            <div class="col-6">
                                <label class="small">Durasi</label>
                                <input type="number" name="duration" class="form-control" value="1" min="1" required>
                            </div>

                            <div class="col-12">
                                <label class="small">Satuan</label>
                                <select name="unit" class="form-select" required>
                                    <option value="hour">Jam</option>
                                    <option value="day">Hari</option>
                                    <option value="week">Minggu</option>
                                    <option value="month">Bulan</option>
                                </select>
                            </div>

                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-info w-100 text-white">
                                    Sewa (Masuk Keranjang)
                                </button>
                            </div>

                        </div>
                    </form>
                @endif

            </div>
        </div>

    </div>
</div>

@if($product->rental_price > 0)
<script>
function setMode(mode) {
    const buyForm = document.getElementById('buyForm');
    const rentForm = document.getElementById('rentForm');
    const priceDisplay = document.getElementById('price-display');

    const buyPrice = priceDisplay.dataset.buy;
    const rentPrice = priceDisplay.dataset.rent;

    if (mode === 'buy') {
        buyForm.style.display = 'block';
        rentForm.style.display = 'none';
        priceDisplay.innerText = buyPrice;
    } 
    else {
        buyForm.style.display = 'none';
        rentForm.style.display = 'block';

        if (!rentPrice || rentPrice === "0") {
            priceDisplay.innerText = 'Harga Sewa Belum Tersedia';
        } else {
            priceDisplay.innerText = rentPrice;
        }
    }
}
</script>
@endif

@endsection