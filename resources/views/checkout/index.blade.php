@extends('layouts.app')
@section('title', 'Finalize Order')
@section('content')
<style>
    :root { --primary-blue: #3b82f6; --azure-dark: #1e40af; }
    body {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        background-attachment: fixed; min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .checkout-header { padding: 40px 0 20px; text-align: center; color: white; }
    .checkout-title  { font-weight: 800; font-size: 2.5rem; letter-spacing: -1.5px; }
    .checkout-card {
        background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);
        border-radius: 30px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.15); overflow: hidden;
    }
    .card-header-premium { background: white; padding: 25px 30px; border-bottom: 1px solid #f1f5f9; }
    .form-label { font-weight: 700; font-size: 0.85rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-control {
        border-radius: 15px; padding: 12px 20px; border: 2px solid #f1f5f9;
        background: #f8fafc; transition: 0.3s;
    }
    .form-control:focus {
        border-color: var(--primary-blue); background: white;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
    }
    .total-display { background: #f8fafc; border-radius: 20px; padding: 25px; border: 2px dashed #e2e8f0; }
    .btn-pay {
        background: var(--primary-blue); color: white; border-radius: 18px;
        padding: 18px; font-weight: 800; font-size: 1.1rem; border: none;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-pay:hover:not(:disabled) {
        background: var(--azure-dark); transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(30,64,175,0.4); color: white;
    }
    .btn-pay:disabled { opacity: 0.6; cursor: not-allowed; }
    .badge-qty {
        background: var(--primary-blue); color: white; width: 24px; height: 24px;
        border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;
    }

    /* ── Searchable Dropdown ── */
    .sdropdown-wrapper { position: relative; }

    .sdropdown-trigger {
        width: 100%; border-radius: 15px; padding: 12px 20px;
        border: 2px solid #f1f5f9; background: #f8fafc;
        display: flex; align-items: center; justify-content: space-between;
        cursor: pointer; transition: 0.3s; user-select: none; gap: 10px;
    }
    .sdropdown-trigger:hover  { border-color: #cbd5e1; }
    .sdropdown-trigger.active { border-color: var(--primary-blue); background: white; box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
    .sdropdown-trigger.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }

    .sdt-icon  { color: #94a3b8; font-size: 1rem; flex-shrink: 0; }
    .sdt-text  { flex-grow: 1; font-size: 0.95rem; color: #94a3b8; }
    .sdt-text.selected { color: #1e293b; font-weight: 600; }
    .sdt-arrow { color: #94a3b8; font-size: 0.75rem; flex-shrink: 0; transition: 0.2s; }
    .sdt-arrow.open { transform: rotate(180deg); }

    .sdt-clear {
        color: #93c5fd; cursor: pointer; font-size: 1.1rem; flex-shrink: 0;
        line-height: 1; display: none;
    }
    .sdt-clear:hover { color: var(--primary-blue); }
    .sdt-clear.show { display: block; }

    /* Dropdown panel */
    .sdd-panel {
        position: absolute; top: calc(100% + 6px); left: 0; right: 0;
        background: white; border: 2px solid #e2e8f0; border-radius: 18px;
        z-index: 1050; box-shadow: 0 12px 32px rgba(0,0,0,0.12);
        display: none; overflow: hidden;
    }
    .sdd-panel.show { display: block; }

    .sdd-search-wrap { padding: 10px 12px; border-bottom: 1px solid #f1f5f9; }
    .sdd-search {
        width: 100%; border: 2px solid #e2e8f0; border-radius: 10px;
        padding: 8px 14px 8px 36px; font-size: 0.875rem;
        background: #f8fafc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 12px center;
        outline: none; transition: 0.2s;
    }
    .sdd-search:focus { border-color: var(--primary-blue); background-color: white; }

    .sdd-list { max-height: 240px; overflow-y: auto; }

    .sdd-item {
        padding: 11px 16px; cursor: pointer; transition: 0.15s;
        border-bottom: 1px solid #f8fafc; font-size: 0.9rem; color: #1e293b;
    }
    .sdd-item:last-child  { border-bottom: none; }
    .sdd-item:hover       { background: #eff6ff; }
    .sdd-item.active      { background: #eff6ff; color: var(--primary-blue); font-weight: 700; }
    .sdd-item mark        { background: #dbeafe; padding: 0; border-radius: 3px; color: inherit; }

    .sdd-empty { padding: 20px; text-align: center; color: #94a3b8; font-size: 0.85rem; }
    .sdd-loading { padding: 20px; text-align: center; color: #94a3b8; font-size: 0.85rem; }

    /* ── Location Steps connector ── */
    .location-steps { display: flex; flex-direction: column; gap: 0; }
    .location-step  { position: relative; padding-bottom: 10px; }
    .step-line {
        position: absolute; left: 19px; bottom: 0; width: 2px; height: 10px;
        background: linear-gradient(#e2e8f0, #bfdbfe);
    }
    .location-step:last-child .step-line { display: none; }

    /* Breadcrumb */
    .loc-breadcrumb {
        display: none; align-items: center; gap: 6px; flex-wrap: wrap;
        background: #eff6ff; border: 1.5px solid #bfdbfe;
        border-radius: 12px; padding: 10px 14px; margin-top: 6px;
        font-size: 0.8rem; font-weight: 600; color: #1e40af;
    }
    .loc-breadcrumb.show { display: flex; }
    .loc-breadcrumb .sep { color: #93c5fd; }

    /* ── Shipping Options ── */
    .skeleton-line {
        height: 44px; border-radius: 12px; margin-bottom: 8px;
        background: linear-gradient(90deg,#e2e8f0 25%,#f1f5f9 50%,#e2e8f0 75%);
        background-size: 200% 100%; animation: shimmer 1.4s infinite;
    }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    .courier-group { border: 2px solid #e2e8f0; border-radius: 18px; overflow: hidden; margin-bottom: 12px; }
    .courier-group-header {
        background: #f8fafc; padding: 10px 16px; font-size: 0.75rem;
        font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px;
        border-bottom: 1px solid #e2e8f0;
    }
    .service-option { display: none; }
    .service-label {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 16px; cursor: pointer; transition: 0.2s;
        border-bottom: 1px solid #f1f5f9; gap: 12px;
    }
    .service-label:last-of-type { border-bottom: none; }
    .service-label:hover { background: #eff6ff; }
    .service-option:checked + .service-label { background: #eff6ff; border-left: 3px solid var(--primary-blue); }
    .service-radio-dot {
        width: 18px; height: 18px; border-radius: 50%; border: 2px solid #cbd5e1;
        flex-shrink: 0; display: flex; align-items: center; justify-content: center; transition: 0.2s;
    }
    .service-option:checked + .service-label .service-radio-dot { border-color: var(--primary-blue); background: var(--primary-blue); }
    .service-option:checked + .service-label .service-radio-dot::after { content: ''; width: 6px; height: 6px; border-radius: 50%; background: white; display: block; }
    .service-name { font-weight: 700; font-size: 0.9rem; color: #1e293b; }
    .service-desc { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }
    .service-etd  { font-size: 0.75rem; color: #10b981; font-weight: 600; white-space: nowrap; }
    .service-cost { font-weight: 800; font-size: 0.9rem; color: var(--primary-blue); white-space: nowrap; }
    .jarak-info   { font-size: 0.78rem; color: #94a3b8; text-align: right; margin-bottom: 8px; }
</style>

<div class="container py-4">
    <div class="checkout-header animate__animated animate__fadeInDown">
        <div class="d-flex justify-content-center align-items-center mb-2">
            <div class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">STEP 2 OF 2</div>
        </div>
        <h1 class="checkout-title">pesan sekarang juga <span class="opacity-50">.</span></h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-lg rounded-4 p-3 animate__animated animate__shakeX">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- 🔥 DI BAGIAN INI KITA MASUKKAN TOTAL BERAT SECARA OTOMATIS BERDASARKAN HASIL SUM QUANTITY * WEIGHT DARI RELASI CART ITEMS --}}
    <div id="js-data"
        data-subtotal="{{ $subtotal }}"
        data-total-weight="{{ $totalWeight }}" {{-- 🔥 Ambil langsung dari variabel Controller yang sudah akurat --}}
        data-cities-url="{{ route('checkout.cities') }}"
        data-districts-url="{{ route('checkout.districts') }}"
        data-shipping-url="{{ route('checkout.shipping') }}"
        style="display:none"></div>

    <div class="row g-4 mt-2">

        {{-- Order Summary --}}
        <div class="col-lg-5 order-2 order-lg-1">
            <div class="checkout-card shadow-lg animate__animated animate__fadeInLeft">
                <div class="card-header-premium">
                    <h5 class="mb-0 fw-800 text-dark">Order Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="order-list mb-4" style="max-height:350px;overflow-y:auto;">
                        @foreach($cart->items as $item)
                            <div class="d-flex align-items-center mb-3 p-2" style="border-radius:15px;">
                                <div class="position-relative">
                                    <img src="{{ $item->product?->image_url ?? '#' }}"
                                         class="rounded-3 border" width="65" height="65" style="object-fit:cover;">
                                    <span class="position-absolute top-0 start-100 translate-middle badge-qty shadow">
                                        {{ $item->quantity }}
                                    </span>
                                </div>
                                <div class="ms-4 flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark small">{{ Str::limit($item->product?->name,35) }}</h6>
                                    <span class="text-muted" style="font-size:0.75rem;">
                                        @if($item->product?->rental_price)
                                            Rp {{ number_format($item->product->rental_price,0,',','.') }}/hari
                                        @else
                                            Rp {{ number_format($item->price??0,0,',','.') }}
                                        @endif
                                        <br>
                                        <small class="text-secondary">
                                            Berat: 
                                            @php
                                                // Cek semua kemungkinan nama kolom berat di tabel produk kamu
                                                $beratSatuan = $item->product->weight ?? $item->product->berat ?? $item->product->weight_formatted ?? 0;
                                                
                                                // Jika masih 0, kita beri fallback 1000 gr agar hitungan ongkir/biaya berat tidak rusak
                                                if ($beratSatuan <= 0) {
                                                    $beratSatuan = 1000; 
                                                }
                                                
                                                $totalBeratItem = $beratSatuan * $item->quantity;
                                            @endphp
                                            {{ $totalBeratItem }} gr
                                        </small>
                                    </span>
                                </div>
                                <div class="text-end fw-bold text-dark small">
                                    @if($item->product?->rental_price)
                                        Rp {{ number_format(($item->product->rental_price??0)*$item->quantity,0,',','.') }}
                                    @else
                                        Rp {{ number_format(($item->price??0)*$item->quantity,0,',','.') }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="total-display">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fw-medium">Subtotal</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($subtotal,0,',','.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fw-medium">Biaya Berat</span>
                            <span class="fw-bold text-dark" id="display-weight-cost">—</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary fw-medium">Ongkir</span>
                            <span class="fw-bold" id="display-shipping" style="color:#94a3b8;">—</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <span class="h5 fw-800 text-dark mb-0">Total</span>
                            <span class="h4 fw-900 text-primary mb-0" id="display-total">
                                Rp {{ number_format($subtotal,0,',','.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shipping Form --}}
        <div class="col-lg-7 order-1 order-lg-2">
            <div class="checkout-card shadow-lg animate__animated animate__fadeInRight">
                <div class="card-header-premium d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-800 text-dark">Detail Pengiriman</h5>
                    <i class="bi bi-truck text-primary fs-4"></i>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                        @csrf
                        <input type="hidden" name="shipping_cost"   id="h-cost"     value="0">
                        <input type="hidden" name="courier"         id="h-courier"  value="">
                        <input type="hidden" name="courier_service" id="h-service"  value="">
                        <input type="hidden" name="province_code"   id="h-province" value="">
                        <input type="hidden" name="city_code"       id="h-city"     value="">
                        <input type="hidden" name="district_code"   id="h-district" value="">
                        <input type="hidden" name="weight_cost"     id="h-weight-cost" value="0">

                        <div class="row">
                            {{-- Nama --}}
                            <div class="col-12 mb-4">
                                <label class="form-label">Nama Lengkap Penerima</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light rounded-start-4"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="name"
                                           class="form-control rounded-end-4 @error('name') is-invalid @enderror"
                                           placeholder="Masukkan nama lengkap..."
                                           value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Telepon --}}
                            <div class="col-12 mb-4">
                                <label class="form-label">No. Handphone / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light rounded-start-4"><i class="bi bi-whatsapp text-muted"></i></span>
                                    <input type="text" name="phone" minlength="10" maxlength="15" pattern="[0-9]{10,15}"
                                           class="form-control rounded-end-4 @error('phone') is-invalid @enderror"
                                           placeholder="Contoh: 081234567890"
                                           value="{{ old('phone', auth()->user()->phone) }}" required>
                                </div>
                                @error('phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- ── 3-Level Searchable Location ── --}}
                            <div class="col-12 mb-4">
                                <label class="form-label">Lokasi Pengiriman</label>

                                <div class="location-steps">
                                    {{-- Provinsi --}}
                                    <div class="location-step">
                                        <div class="sdropdown-wrapper" id="prov-wrap">
                                            <div class="sdropdown-trigger" id="prov-trigger" onclick="toggleDropdown('prov')">
                                                <i class="bi bi-map sdt-icon"></i>
                                                <span class="sdt-text" id="prov-label">Pilih Provinsi</span>
                                                <span class="sdt-clear" id="prov-clear" onclick="clearLevel('prov', event)">✕</span>
                                                <i class="bi bi-chevron-down sdt-arrow" id="prov-arrow"></i>
                                            </div>
                                            <div class="sdd-panel" id="prov-panel">
                                                <div class="sdd-search-wrap">
                                                    <input type="text" class="sdd-search" id="prov-search"
                                                           placeholder="Cari provinsi..." oninput="filterList('prov')">
                                                </div>
                                                <div class="sdd-list" id="prov-list"></div>
                                            </div>
                                        </div>
                                        <div class="step-line"></div>
                                    </div>

                                    {{-- Kota --}}
                                    <div class="location-step" id="city-step" style="display:none;">
                                        <div class="sdropdown-wrapper" id="city-wrap">
                                            <div class="sdropdown-trigger disabled" id="city-trigger" onclick="toggleDropdown('city')">
                                                <i class="bi bi-building sdt-icon"></i>
                                                <span class="sdt-text" id="city-label">Pilih Kota / Kabupaten</span>
                                                <span class="sdt-clear" id="city-clear" onclick="clearLevel('city', event)">✕</span>
                                                <i class="bi bi-chevron-down sdt-arrow" id="city-arrow"></i>
                                            </div>
                                            <div class="sdd-panel" id="city-panel">
                                                <div class="sdd-search-wrap">
                                                    <input type="text" class="sdd-search" id="city-search"
                                                           placeholder="Cari kota..." oninput="filterList('city')">
                                                </div>
                                                <div class="sdd-list" id="city-list">
                                                    <div class="sdd-loading"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat...</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-line"></div>
                                    </div>

                                    {{-- Kecamatan --}}
                                    <div class="location-step" id="dist-step" style="display:none;">
                                        <div class="sdropdown-wrapper" id="dist-wrap">
                                            <div class="sdropdown-trigger disabled" id="dist-trigger" onclick="toggleDropdown('dist')">
                                                <i class="bi bi-geo-alt sdt-icon"></i>
                                                <span class="sdt-text" id="dist-label">Pilih Kecamatan</span>
                                                <span class="sdt-clear" id="dist-clear" onclick="clearLevel('dist', event)">✕</span>
                                                <i class="bi bi-chevron-down sdt-arrow" id="dist-arrow"></i>
                                            </div>
                                            <div class="sdd-panel" id="dist-panel">
                                                <div class="sdd-search-wrap">
                                                    <input type="text" class="sdd-search" id="dist-search"
                                                           placeholder="Cari kecamatan..." oninput="filterList('dist')">
                                                </div>
                                                <div class="sdd-list" id="dist-list">
                                                    <div class="sdd-loading"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat...</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Breadcrumb --}}
                                <div class="loc-breadcrumb" id="loc-breadcrumb"></div>

                                @error('province_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                @error('city_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                @error('district_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            {{-- Shipping Options --}}
                            <div class="col-12 mb-4" id="shipping-panel" style="display:none;">
                                <label class="form-label">Pilih Layanan Pengiriman</label>
                                <div id="shipping-skeleton" style="display:none;">
                                    <div class="skeleton-line"></div>
                                    <div class="skeleton-line"></div>
                                    <div class="skeleton-line" style="width:70%;"></div>
                                </div>
                                <div id="jarak-info" class="jarak-info" style="display:none;"></div>
                                <div id="shipping-options"></div>
                                <div id="shipping-error" class="alert alert-warning rounded-4 small" style="display:none;">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <span id="shipping-error-msg"></span>
                                    <button type="button" class="btn btn-sm btn-outline-warning ms-2 rounded-3" onclick="retryShipping()">Coba Lagi</button>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="col-12 mb-4">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="address" rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Nama jalan, nomor rumah, RT/RW, kode pos..."
                                          required>{{ old('address', auth()->user()->address) }}</textarea>
                                @error('address')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        {{-- Pilihan Metode Pembayaran --}}
                        {{-- <div class="col-12 mb-4">
                            <label class="form-label">Metode Pembayaran</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay-midtrans" value="midtrans" checked autocomplete="off">
                                    <label class="btn btn-outline-primary w-100 py-3 rounded-4 fw-bold" for="pay-midtrans">
                                        <i class="bi bi-credit-card me-2"></i>Online Payment
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay-cod" value="cod" autocomplete="off">
                                    <label class="btn btn-outline-primary w-100 py-3 rounded-4 fw-bold" for="pay-cod">
                                        <i class="bi bi-cash-coin me-2"></i>Bayar di Tempat (COD)
                                    </label>
                                </div>
                            </div>
                            @error('payment_method')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div> --}}

                        <div class="rounded-4 mb-4 d-flex align-items-start p-3" style="background:#eff6ff;">
                            <i class="bi bi-shield-check text-primary me-3 fs-4"></i>
                            <p class="small text-muted mb-0">
                                <strong>Transaksi Aman:</strong> Pesanan diproses setelah konfirmasi pembayaran.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-pay w-100 shadow-lg" id="btn-pay" disabled>
                            <span id="btn-pay-text">Pilih lokasi pengiriman dulu</span>
                            <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const jsData       = document.getElementById('js-data');
    const subtotal     = parseFloat(jsData.dataset.subtotal);
    const totalWeight  = parseInt(jsData.dataset.totalWeight) || 1000; // 🔥 Membaca data-total-weight dinamis dari DOM
    const citiesUrl    = jsData.dataset.citiesUrl;
    const districtsUrl = jsData.dataset.districtsUrl;
    const shippingUrl  = jsData.dataset.shippingUrl;

    // ── State ─────────────────────────────────────────────────
    const state = {
        prov: { code: null, name: null, items: [] },
        city: { code: null, name: null, items: [] },
        dist: { code: null, name: null, items: [] },
    };
    let lastDistrictCode = null;

    // ── Helpers ───────────────────────────────────────────────
    const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
    const el     = id => document.getElementById(id);

    // ── Populate provinces on load ────────────────────────────
    const provinces = @json($provinces->map(fn($p) => ['code' => $p->code, 'name' => $p->name]));
    state.prov.items = provinces;
    renderList('prov', provinces);

    // ── Dropdown toggle ───────────────────────────────────────
    window.toggleDropdown = function (key) {
        const trigger = el(`${key}-trigger`);
        if (trigger.classList.contains('disabled')) return;

        const panel  = el(`${key}-panel`);
        const arrow  = el(`${key}-arrow`);
        const search = el(`${key}-search`);
        const isOpen = panel.classList.contains('show');

        closeAll();
        if (!isOpen) {
            panel.classList.add('show');
            trigger.classList.add('active');
            arrow.classList.add('open');
            search.value = '';
            filterList(key);
            setTimeout(() => search.focus(), 50);
        }
    };

    function closeAll() {
        ['prov','city','dist'].forEach(k => {
            el(`${k}-panel`).classList.remove('show');
            el(`${k}-trigger`).classList.remove('active');
            el(`${k}-arrow`).classList.remove('open');
        });
    }

    document.addEventListener('click', e => {
        if (!e.target.closest('.sdd-panel') && !e.target.closest('.sdt-arrow') &&
            !e.target.closest('.sdt-text') && !e.target.closest('.sdt-icon') &&
            !e.target.closest('.sdd-search')) {
            if (!e.target.closest('.sdropdown-trigger')) closeAll();
        }
    });

    // ── Filter / search list ──────────────────────────────────
    window.filterList = function (key) {
        const q     = el(`${key}-search`).value.toLowerCase().trim();
        const items = state[key].items;
        const filtered = q ? items.filter(i => i.name.toLowerCase().includes(q)) : items;
        renderList(key, filtered, q);
    };

    // ── Render list items ─────────────────────────────────────
    function renderList(key, items, query = '') {
        const listEl = el(`${key}-list`);
        if (!items.length) {
            listEl.innerHTML = `<div class="sdd-empty"><i class="bi bi-search me-2"></i>Tidak ditemukan</div>`;
            return;
        }
        listEl.innerHTML = items.map(item => {
            const name = query
                ? item.name.replace(new RegExp(`(${query})`, 'gi'), '<mark>$1</mark>')
                : item.name;
            const isActive = state[key].code === item.code ? 'active' : '';
            return `<div class="sdd-item ${isActive}" onclick="selectItem('${key}','${item.code}',\`${item.name}\`)">${name}</div>`;
        }).join('');
    }

    // ── Select item ───────────────────────────────────────────
    window.selectItem = function (key, code, name) {
        state[key].code = code;
        state[key].name = name;

        el(`${key}-label`).textContent = name;
        el(`${key}-label`).classList.add('selected');
        el(`${key}-clear`).classList.add('show');
        closeAll();
        updateBreadcrumb();

        if (key === 'prov') {
            resetLevel('city'); resetLevel('dist');
            el('h-province').value = code;
            el('h-city').value     = '';
            el('h-district').value = '';

            el('city-step').style.display = 'block';
            el('city-trigger').classList.add('disabled');
            el('city-list').innerHTML = `<div class="sdd-loading"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat...</div>`;

            fetch(`${citiesUrl}?province_code=${code}`)
                .then(r => r.json())
                .then(data => {
                    state.city.items = data;
                    renderList('city', data);
                    el('city-trigger').classList.remove('disabled');
                });

            el('dist-step').style.display = 'none';
            el('shipping-panel').style.display = 'none';
            disablePay('Pilih kota dulu');
        }

        if (key === 'city') {
            resetLevel('dist');
            el('h-city').value     = code;
            el('h-district').value = '';

            el('dist-step').style.display = 'block';
            el('dist-trigger').classList.add('disabled');
            el('dist-list').innerHTML = `<div class="sdd-loading"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Memuat...</div>`;

            fetch(`${districtsUrl}?city_code=${code}`)
                .then(r => r.json())
                .then(data => {
                    state.dist.items = data;
                    renderList('dist', data);
                    el('dist-trigger').classList.remove('disabled');
                });

            el('shipping-panel').style.display = 'none';
            disablePay('Pilih kecamatan dulu');
        }

        if (key === 'dist') {
            el('h-district').value = code;
            fetchShipping(code);
        }
    };

    // ── Clear a level ─────────────────────────────────────────
    window.clearLevel = function (key, event) {
        event.stopPropagation();
        resetLevel(key);
        if (key === 'prov') {
            resetLevel('city'); resetLevel('dist');
            el('city-step').style.display = 'none';
            el('dist-step').style.display = 'none';
            el('h-province').value = '';
        }
        if (key === 'city') {
            resetLevel('dist');
            el('dist-step').style.display = 'none';
            el('h-city').value = '';
        }
        if (key === 'dist') {
            el('h-district').value = '';
        }
        el('shipping-panel').style.display = 'none';
        disablePay('Pilih lokasi pengiriman dulu');
        updateBreadcrumb();
    };

    function resetLevel(key) {
        const defaults = { prov: 'Pilih Provinsi', city: 'Pilih Kota / Kabupaten', dist: 'Pilih Kecamatan' };
        state[key].code = null; state[key].name = null;
        el(`${key}-label`).textContent = defaults[key];
        el(`${key}-label`).classList.remove('selected');
        el(`${key}-clear`).classList.remove('show');
        if (key !== 'prov') el(`${key}-trigger`).classList.add('disabled');
        renderList(key, state[key].items);
    }

    // ── Breadcrumb Generator ──────────────────────────────────
    function updateBreadcrumb() {
        const parts = [];
        if (state.prov.name) parts.push(state.prov.name);
        if (state.city.name) parts.push(state.city.name);
        if (state.dist.name) parts.push(state.dist.name);

        const breadEl = el('loc-breadcrumb');
        if (parts.length > 0) {
            breadEl.innerHTML = parts.map((p, idx) => `<span>${p}</span>${idx < parts.length - 1 ? '<span class="sep">/</span>' : ''}`).join('');
            breadEl.classList.add('show');
        } else {
            breadEl.classList.remove('show');
        }
    }

    // ── Fetch Shipping Cost + BIAYA BERAT ───────────────────────────────────
    window.fetchShipping = function (districtCode) {
        lastDistrictCode = districtCode;
        
        const panel     = el('shipping-panel');
        const skeleton  = el('shipping-skeleton');
        const optionsEl = el('shipping-options');
        const errorEl   = el('shipping-error');
        const jarakEl   = el('jarak-info');

        panel.style.display   = 'block';
        skeleton.style.display = 'block';
        optionsEl.innerHTML   = '';
        errorEl.style.display = 'none';
        if (jarakEl) jarakEl.style.display = 'none';
        disablePay('Memuat opsi ongkos kirim...');

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                          || document.querySelector('input[name="_token"]')?.value;

        // 🔥 BERAT DINAMIS BERHASIL DIKIRIM KE CONTROLLER
        fetch(`${shippingUrl}?district_code=${districtCode}&subtotal=${subtotal}&weight=${totalWeight}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(r => {
            if (!r.ok) {
                return r.json().then(err => { throw err; });
            }
            return r.json();
        })
        .then(res => {
            skeleton.style.display = 'none';
            
            if (!res.success) {
                showShippingError(res.message || 'Layanan pengiriman tidak tersedia.');
                return;
            }

            if (res.free_shipping) {
                optionsEl.innerHTML = `
                    <div class="alert alert-success d-flex align-items-center animate__animated animate__fadeIn">
                        <i class="bi bi-gift-fill me-2 fs-4"></i>
                        <div>${res.message}</div>
                    </div>
                `;
                applyShipping(0, 'free', 'free');
                return;
            }

            if (!res.couriers || res.couriers.length === 0) {
                showShippingError('Tidak ada kurir pengiriman yang tersedia saat ini.');
                return;
            }

            // Tampilkan info berat
            if (res.weight_kg) {
                jarakEl.style.display = 'block';
                jarakEl.innerHTML = `<i class="bi bi-box-seam me-1"></i>Total Berat Paket: <strong>${res.weight_kg} kg</strong> (${totalWeight} gr)`;
            }

            let html = '';
            res.couriers.forEach(group => {
                html += `
                    <div class="courier-group animate__animated animate__fadeInUp">
                        <div class="courier-group-header">${group.courier_name}</div>
                `;

                group.services.forEach(serv => {
                    const totalCost = serv.total_cost;  
                    const costText  = totalCost === 0 ? 'GRATIS' : rupiah(totalCost);
                    const etdText   = serv.etd_label
                        ? `<div class="service-etd"><i class="bi bi-clock me-1"></i>${serv.etd_label}</div>`
                        : '';

                    html += `
                        <input type="radio" name="shipping_service_select"
                            id="ship-${group.courier_code}-${serv.service}"
                            class="service-option"
                            value="${totalCost}"
                            data-courier="${group.courier_code}"
                            data-service="${serv.service}"
                            data-biaya-berat="${serv.biaya_berat}"
                            onclick="applyShipping(${totalCost}, '${group.courier_code}', '${serv.service}', ${serv.biaya_berat})">
                        <label for="ship-${group.courier_code}-${serv.service}" class="service-label">
                            <div class="d-flex align-items-center gap-3">
                                <div class="service-radio-dot"></div>
                                <div>
                                    <div class="service-name">${group.courier_name} ${serv.service}</div>
                                    <div class="service-desc">${serv.description || 'Layanan Pengiriman'}</div>
                                    <div class="service-desc text-muted" style="font-size: 0.75rem;">
                                        Tarif: ${rupiah(serv.cost)} + Tambahan Berat: ${rupiah(serv.biaya_berat)}
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="service-cost">${costText}</div>
                                ${etdText}
                            </div>
                        </label>
                    `;
                });

                html += `</div>`;
            });

            optionsEl.innerHTML = html;
            disablePay('Pilih layanan pengiriman');
        })
        .catch(err => {
            skeleton.style.display = 'none';
            console.error('Penyebab Validasi/Auth Gagal:', err);
            const errorMsg = err.message || (err.errors ? Object.values(err.errors).flat().join(', ') : 'Terjadi masalah koneksi ke server.');
            showShippingError(errorMsg);
        });
    };

    window.retryShipping = function() {
        if (lastDistrictCode) fetchShipping(lastDistrictCode);
    };

    function showShippingError(msg) {
        el('shipping-error-msg').textContent = msg;
        el('shipping-error').style.display = 'block';
        disablePay('Pengiriman tidak tersedia');
    }

    // ── Apply Ongkir & Update Grand Total ───────────────────
    window.applyShipping = function (totalCost, courier, service, biayaBerat) {
        console.log('applyShipping dipanggil:', { totalCost, courier, service, biayaBerat });
        
        if (el('h-cost')) el('h-cost').value = totalCost;
        if (el('h-courier')) el('h-courier').value = courier;
        if (el('h-service')) el('h-service').value = service;
        if (el('h-weight-cost')) el('h-weight-cost').value = biayaBerat || 0;

        if (document.getElementsByName('shipping_cost')[0])
            document.getElementsByName('shipping_cost')[0].value = totalCost;
        if (document.getElementsByName('courier')[0])
            document.getElementsByName('courier')[0].value = courier;
        if (document.getElementsByName('courier_service')[0])
            document.getElementsByName('courier_service')[0].value = service;
        if (document.getElementsByName('weight_cost')[0])
            document.getElementsByName('weight_cost')[0].value = biayaBerat || 0;

        const ongkirMurni = totalCost - (biayaBerat || 0);
        const shippingDisp = el('display-shipping');
        if (shippingDisp) {
            shippingDisp.textContent = ongkirMurni <= 0 ? 'GRATIS' : rupiah(ongkirMurni);
            shippingDisp.style.color = ongkirMurni <= 0 ? '#10b981' : '#1e293b';
        }

        const weightDisp = el('display-weight-cost');
        if (weightDisp) {
            weightDisp.textContent = biayaBerat > 0 ? rupiah(biayaBerat) : 'Gratis';
            weightDisp.style.color = '#1e293b';
        }

        if (el('display-total'))
            el('display-total').textContent = rupiah(subtotal + totalCost);

        const btnPay = el('btn-pay');
        if (btnPay) {
            btnPay.removeAttribute('disabled');
            if (el('btn-pay-text')) el('btn-pay-text').textContent = 'Lanjutkan ke Pembayaran';
        }
    };

    // ── Disable Pay Button Helper ───────────────────────────
    function disablePay(text) {
        const btnPay = el('btn-pay');
        if (btnPay) {
            btnPay.setAttribute('disabled', 'true');
            if (el('btn-pay-text')) el('btn-pay-text').textContent = text;
        }
        
        if (el('display-shipping')) {
            el('display-shipping').textContent = '—';
            el('display-shipping').style.color = '#94a3b8';
        }
        if (el('display-weight-cost')) el('display-weight-cost').textContent = '—';
        if (el('display-total')) el('display-total').textContent = rupiah(subtotal);
    }
});     
</script>
@endsection