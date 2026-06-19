@extends('layouts.app')

@section('title', 'Finalize Order')

@section('content')
<style>
    :root {
        --primary-blue: #3b82f6;
        --azure-dark: #1e40af;
    }

    body {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .checkout-header { padding: 40px 0 20px; text-align: center; color: white; }
    .checkout-title  { font-weight: 800; font-size: 2.5rem; letter-spacing: -1.5px; }

    .checkout-card {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(10px);
        border-radius: 30px;
        border: none;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        overflow: hidden;
    }

    .card-header-premium {
        background: white;
        padding: 25px 30px;
        border-bottom: 1px solid #f1f5f9;
    }

    .form-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control, .form-select {
        border-radius: 15px;
        padding: 12px 20px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        transition: 0.3s;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-blue);
        background: white;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.1);
    }

    .total-display {
        background: #f8fafc;
        border-radius: 20px;
        padding: 25px;
        border: 2px dashed #e2e8f0;
    }

    .btn-pay {
        background: var(--primary-blue);
        color: white;
        border-radius: 18px;
        padding: 18px;
        font-weight: 800;
        font-size: 1.1rem;
        border: none;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .btn-pay:hover:not(:disabled) {
        background: var(--azure-dark);
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(30,64,175,0.4);
        color: white;
    }
    .btn-pay:disabled { opacity: 0.6; cursor: not-allowed; }

    .badge-qty {
        background: var(--primary-blue); color: white;
        width: 24px; height: 24px; border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 0.7rem;
    }

    /* ── City Search Dropdown ── */
    .city-search-wrapper { position: relative; }

    .city-search-input {
        border-radius: 15px !important;
        padding: 12px 20px !important;
        border: 2px solid #f1f5f9 !important;
        background: #f8fafc !important;
        transition: 0.3s;
        width: 100%;
    }
    .city-search-input:focus {
        border-color: var(--primary-blue) !important;
        background: white !important;
        box-shadow: 0 0 0 4px rgba(59,130,246,0.1) !important;
        outline: none;
    }

    .city-dropdown {
        position: absolute;
        top: calc(100% + 6px);
        left: 0; right: 0;
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        max-height: 280px;
        overflow-y: auto;
        z-index: 1000;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        display: none;
    }
    .city-dropdown.show { display: block; }

    .city-item {
        padding: 12px 16px;
        cursor: pointer;
        border-bottom: 1px solid #f8fafc;
        transition: 0.15s;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .city-item:last-child { border-bottom: none; }
    .city-item:hover, .city-item.active { background: #eff6ff; }

    .city-item-name { font-weight: 700; font-size: 0.9rem; color: #1e293b; }
    .city-item-province { font-size: 0.75rem; color: #94a3b8; }
    .city-item-type {
        font-size: 0.65rem; font-weight: 700;
        padding: 2px 8px; border-radius: 6px;
        background: #f1f5f9; color: #64748b;
        flex-shrink: 0;
    }

    .city-no-result {
        padding: 20px;
        text-align: center;
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .city-selected-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eff6ff;
        border: 1.5px solid #bfdbfe;
        border-radius: 10px;
        padding: 6px 12px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #1e40af;
        margin-top: 8px;
    }
    .city-selected-badge .clear-city {
        cursor: pointer;
        color: #93c5fd;
        font-size: 1rem;
        line-height: 1;
    }
    .city-selected-badge .clear-city:hover { color: #1e40af; }

    /* ── Shipping Options ── */
    .skeleton-line {
        height: 14px; border-radius: 8px;
        background: linear-gradient(90deg,#e2e8f0 25%,#f1f5f9 50%,#e2e8f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        margin-bottom: 8px;
    }
    @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

    .courier-group { border: 2px solid #e2e8f0; border-radius: 18px; overflow: hidden; margin-bottom: 12px; }
    .courier-group-header {
        background: #f8fafc; padding: 10px 16px;
        font-size: 0.75rem; font-weight: 800; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.8px;
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
        width: 18px; height: 18px; border-radius: 50%;
        border: 2px solid #cbd5e1; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center; transition: 0.2s;
    }
    .service-option:checked + .service-label .service-radio-dot {
        border-color: var(--primary-blue); background: var(--primary-blue);
    }
    .service-option:checked + .service-label .service-radio-dot::after {
        content: ''; width: 6px; height: 6px; border-radius: 50%; background: white; display: block;
    }
    .service-name  { font-weight: 700; font-size: 0.9rem; color: #1e293b; }
    .service-desc  { font-size: 0.75rem; color: #94a3b8; margin-top: 2px; }
    .service-etd   { font-size: 0.75rem; color: #10b981; font-weight: 600; white-space: nowrap; }
    .service-cost  { font-weight: 800; font-size: 0.9rem; color: var(--primary-blue); white-space: nowrap; }

    .jarak-info {
        font-size: 0.78rem; color: #94a3b8;
        text-align: right; margin-bottom: 8px;
    }

    .free-shipping-badge {
        background: linear-gradient(135deg,#10b981,#059669);
        color: white; border-radius: 14px; padding: 12px 18px;
        display: flex; align-items: center; gap: 10px;
        font-weight: 700; font-size: 0.9rem;
    }
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

    <div id="js-data"
         data-subtotal="{{ $subtotal }}"
         data-shipping-url="{{ route('checkout.shipping') }}"
         style="display:none"></div>

    <div class="row g-4 mt-2">

        {{-- ─── Order Summary ─── --}}
        <div class="col-lg-5 order-2 order-lg-1">
            <div class="checkout-card shadow-lg animate__animated animate__fadeInLeft">
                <div class="card-header-premium">
                    <h5 class="mb-0 fw-800 text-dark">Order Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="order-list mb-4" style="max-height: 350px; overflow-y: auto;">
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
                                    <h6 class="mb-0 fw-bold text-dark small">{{ Str::limit($item->product?->name, 35) }}</h6>
                                    <span class="text-muted" style="font-size:0.75rem;">
                                        @if($item->product?->rental_price)
                                            Rp {{ number_format($item->product->rental_price,0,',','.') }}/hari
                                        @else
                                            Rp {{ number_format($item->price ?? 0,0,',','.') }}
                                        @endif
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

        {{-- ─── Shipping Form ─── --}}
        <div class="col-lg-7 order-1 order-lg-2">
            <div class="checkout-card shadow-lg animate__animated animate__fadeInRight">
                <div class="card-header-premium d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-800 text-dark">Detail Pengiriman</h5>
                    <i class="bi bi-truck text-primary fs-4"></i>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                        @csrf
                        <input type="hidden" name="shipping_cost"   id="hidden-shipping-cost"   value="0">
                        <input type="hidden" name="courier"         id="hidden-courier"         value="">
                        <input type="hidden" name="courier_service" id="hidden-courier-service" value="">
                        <input type="hidden" name="city_id"         id="hidden-city-id"         value="">

                        <div class="row">
                            {{-- Nama --}}
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Nama Lengkap Penerima</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light rounded-start-4">
                                        <i class="bi bi-person text-muted"></i>
                                    </span>
                                    <input type="text" name="name"
                                           class="form-control rounded-end-4 @error('name') is-invalid @enderror"
                                           placeholder="Masukkan nama lengkap..."
                                           value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Telepon --}}
                            <div class="col-md-12 mb-4">
                                <label class="form-label">No. Handphone / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light rounded-start-4">
                                        <i class="bi bi-whatsapp text-muted"></i>
                                    </span>
                                    <input type="text" name="phone" minlength="10" maxlength="15"
                                           pattern="[0-9]{10,15}"
                                           class="form-control rounded-end-4 @error('phone') is-invalid @enderror"
                                           placeholder="Contoh: 081234567890"
                                           value="{{ old('phone', auth()->user()->phone) }}" required>
                                </div>
                                @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- City Search ── Shopee-style ── --}}
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Kota / Kabupaten Tujuan</label>
                                <div class="city-search-wrapper">
                                    <input type="text"
                                           id="city-search-input"
                                           class="city-search-input form-control"
                                           placeholder="Cari kota atau kabupaten..."
                                           autocomplete="off">
                                    <div class="city-dropdown" id="city-dropdown"></div>
                                </div>
                                <div id="city-selected-display" style="display:none;"></div>
                                @error('city_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Shipping Options --}}
                            <div class="col-md-12 mb-4" id="shipping-panel" style="display:none;">
                                <label class="form-label">Pilih Layanan Pengiriman</label>

                                <div id="shipping-skeleton" style="display:none;">
                                    <div class="skeleton-line" style="width:40%;height:10px;"></div>
                                    <div class="skeleton-line" style="width:100%;height:44px;"></div>
                                    <div class="skeleton-line" style="width:100%;height:44px;"></div>
                                </div>

                                <div id="jarak-info" class="jarak-info" style="display:none;"></div>
                                <div id="shipping-options"></div>

                                <div id="shipping-error" class="alert alert-warning rounded-4 small" style="display:none;">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    <span id="shipping-error-msg"></span>
                                    <button type="button" class="btn btn-sm btn-outline-warning ms-2 rounded-3"
                                            onclick="retryShipping()">Coba Lagi</button>
                                </div>
                            </div>

                            {{-- Alamat --}}
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Alamat Lengkap Tujuan</label>
                                <textarea name="address" rows="4"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Tuliskan alamat lengkap beserta nama jalan, nomor rumah, RT/RW, dan kode pos..."
                                          required>{{ old('address', auth()->user()->address) }}</textarea>
                                @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="rounded-4 mb-4 d-flex align-items-start p-3" style="background:#eff6ff;">
                            <i class="bi bi-shield-check text-primary me-3 fs-4"></i>
                            <p class="small text-muted mb-0">
                                <strong>Transaksi Aman:</strong> Pesanan diproses setelah konfirmasi pembayaran.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-pay w-100 shadow-lg" id="btn-pay" disabled>
                            <span id="btn-pay-text">Pilih kota tujuan dulu</span>
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
    const jsData      = document.getElementById('js-data');
    const subtotal    = parseFloat(jsData.dataset.subtotal);
    const shippingUrl = jsData.dataset.shippingUrl;

    // Elements
    const cityInput      = document.getElementById('city-search-input');
    const cityDropdown   = document.getElementById('city-dropdown');
    const cityDisplay    = document.getElementById('city-selected-display');
    const shippingPanel  = document.getElementById('shipping-panel');
    const skeleton       = document.getElementById('shipping-skeleton');
    const optionsBox     = document.getElementById('shipping-options');
    const errorBox       = document.getElementById('shipping-error');
    const errorMsg       = document.getElementById('shipping-error-msg');
    const jarakInfo      = document.getElementById('jarak-info');
    const btnPay         = document.getElementById('btn-pay');
    const btnPayText     = document.getElementById('btn-pay-text');
    const hiddenCost     = document.getElementById('hidden-shipping-cost');
    const hiddenCourier  = document.getElementById('hidden-courier');
    const hiddenService  = document.getElementById('hidden-courier-service');
    const hiddenCityId   = document.getElementById('hidden-city-id');
    const displayShip    = document.getElementById('display-shipping');
    const displayTotal   = document.getElementById('display-total');

    let allCities    = [];
    let selectedCity = null;
    let lastCityId   = null;
    let searchTimer  = null;

    // ── Format Rupiah ─────────────────────────────────────────
    function rupiah(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    // ── Load all cities from API ──────────────────────────────
    fetch('/cities')
        .then(r => r.json())
        .then(data => {
            console.log('Cities loaded:', data.length);
            allCities = data;
        })
        .catch(err => console.error(err));

    // ── Search cities ─────────────────────────────────────────
    cityInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();

        if (q.length < 2) {
            cityDropdown.classList.remove('show');
            return;
        }

        searchTimer = setTimeout(() => {
            // Search from loaded list first
            const filtered = allCities.filter(c =>
                c.name.toLowerCase().includes(q.toLowerCase()) ||
                c.province.toLowerCase().includes(q.toLowerCase())
            ).slice(0, 20);

            renderCityDropdown(filtered, q);
        }, 200);
    });

    cityInput.addEventListener('focus', function () {
        if (this.value.length >= 2) cityDropdown.classList.add('show');
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.city-search-wrapper')) {
            cityDropdown.classList.remove('show');
        }
    });

    // ── Render dropdown ───────────────────────────────────────
    function renderCityDropdown(cities, query) {
        cityDropdown.innerHTML = '';

        if (!cities.length) {
            cityDropdown.innerHTML = `<div class="city-no-result"><i class="bi bi-search me-2"></i>Kota tidak ditemukan</div>`;
            cityDropdown.classList.add('show');
            return;
        }

        cities.forEach(city => {
            const div = document.createElement('div');
            div.className = 'city-item';
            div.innerHTML = `
                <div>
                    <div class="city-item-name">${highlightText(city.name, query)}</div>
                    <div class="city-item-province">${highlightText(city.province, query)}</div>
                </div>
                <span class="city-item-type">${city.type}</span>
            `;
            div.addEventListener('click', () => selectCity(city));
            cityDropdown.appendChild(div);
        });

        cityDropdown.classList.add('show');
    }

    function highlightText(text, query) {
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark style="background:#dbeafe;padding:0;border-radius:3px;">$1</mark>');
    }

    // ── Select city ───────────────────────────────────────────
    function selectCity(city) {
        selectedCity = city;
        hiddenCityId.value = city.id;

        cityInput.value = '';
        cityDropdown.classList.remove('show');

        // Tampilkan badge kota terpilih
        cityDisplay.style.display = 'block';
        cityDisplay.innerHTML = `
            <div class="city-selected-badge">
                <i class="bi bi-geo-alt-fill"></i>
                ${city.name}, ${city.province}
                <span class="clear-city" onclick="clearCity()" title="Ganti kota">✕</span>
            </div>
        `;

        fetchShipping(city.id);
    }

    window.clearCity = function () {
        selectedCity = null;
        hiddenCityId.value = '';
        cityDisplay.style.display = 'none';
        cityDisplay.innerHTML = '';
        cityInput.value = '';
        shippingPanel.style.display = 'none';
        disablePay('Pilih kota tujuan dulu');
        jarakInfo.style.display = 'none';
    };

    // ── Fetch shipping ────────────────────────────────────────
    function fetchShipping(cityId) {
        lastCityId = cityId;
        disablePay('Memuat ongkir...');

        shippingPanel.style.display = 'block';
        skeleton.style.display      = 'block';
        optionsBox.innerHTML        = '';
        errorBox.style.display      = 'none';
        jarakInfo.style.display     = 'none';

        const params = new URLSearchParams({ city_id: cityId, subtotal: subtotal });

        fetch(`${shippingUrl}?${params}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                skeleton.style.display = 'none';

                if (!data.success) {
                    showShippingError(data.message || 'Gagal memuat ongkir.');
                    return;
                }

                if (data.free_shipping) {
                    optionsBox.innerHTML = `
                        <div class="free-shipping-badge">
                            <i class="bi bi-gift-fill fs-5"></i>
                            <div>
                                <div>${data.message}</div>
                                <div style="font-size:0.75rem;opacity:0.85;font-weight:500;">Pesananmu bebas biaya pengiriman</div>
                            </div>
                        </div>`;
                    enablePay('free', 'GRATIS', 0);
                    updateSummary(0, true);
                    return;
                }

                // Tampilkan info jarak
                if (data.jarak_km) {
                    jarakInfo.style.display = 'block';
                    jarakInfo.innerHTML = `<i class="bi bi-signpost-2 me-1"></i>Jarak ke <strong>${data.kota}</strong>: ~${data.jarak_km} km`;
                }

                renderShippingOptions(data.couriers);
            })
            .catch(() => {
                skeleton.style.display = 'none';
                showShippingError('Koneksi bermasalah. Periksa internet kamu.');
            });
    }

    window.retryShipping = function () {
        if (lastCityId) fetchShipping(lastCityId);
    };

    function showShippingError(msg) {
        errorMsg.textContent   = msg;
        errorBox.style.display = 'block';
    }

    // ── Render shipping options ───────────────────────────────
    function renderShippingOptions(couriers) {
        optionsBox.innerHTML = '';

        couriers.forEach(group => {
            const groupEl = document.createElement('div');
            groupEl.className = 'courier-group';

            const header = document.createElement('div');
            header.className   = 'courier-group-header';
            header.textContent = group.courier_name;
            groupEl.appendChild(header);

            group.services.forEach((svc, idx) => {
                const radioId = `svc_${group.courier_code}_${idx}`;

                const input = document.createElement('input');
                input.type      = 'radio';
                input.name      = 'shipping_choice';
                input.id        = radioId;
                input.className = 'service-option';
                input.dataset.courier = group.courier_code;
                input.dataset.service = svc.service;
                input.dataset.cost    = svc.cost;

                const label = document.createElement('label');
                label.htmlFor   = radioId;
                label.className = 'service-label';
                label.innerHTML = `
                    <div class="service-radio-dot"></div>
                    <div class="flex-grow-1">
                        <div class="service-name">${group.courier_name} ${svc.service}</div>
                        <div class="service-desc">${svc.description}</div>
                    </div>
                    <div class="text-end">
                        <div class="service-cost">${rupiah(svc.cost)}</div>
                        <div class="service-etd"><i class="bi bi-clock me-1"></i>${svc.etd_label}</div>
                    </div>
                `;

                input.addEventListener('change', function () {
                    enablePay(this.dataset.courier, this.dataset.service, parseFloat(this.dataset.cost));
                    updateSummary(parseFloat(this.dataset.cost));
                });

                groupEl.appendChild(input);
                groupEl.appendChild(label);
            });

            optionsBox.appendChild(groupEl);
        });
    }

    // ── Summary & button helpers ──────────────────────────────
    function updateSummary(cost, isFree = false) {
        if (isFree) {
            displayShip.textContent  = 'GRATIS';
            displayShip.style.color  = '#10b981';
        } else {
            displayShip.textContent  = rupiah(cost);
            displayShip.style.color  = '#1e293b';
        }
        displayTotal.textContent = rupiah(subtotal + (isFree ? 0 : cost));
    }

    function enablePay(courier, service, cost) {
        hiddenCost.value    = cost;
        hiddenCourier.value = courier;
        hiddenService.value = service;
        btnPayText.textContent = 'Bayar Sekarang';
        btnPay.disabled = false;
    }

    function disablePay(label = 'Pilih layanan pengiriman dulu') {
        hiddenCost.value    = 0;
        hiddenCourier.value = '';
        hiddenService.value = '';
        btnPayText.textContent   = label;
        btnPay.disabled          = true;
        displayShip.textContent  = '—';
        displayShip.style.color  = '#94a3b8';
        displayTotal.textContent = rupiah(subtotal);
    }
});
</script>
@endsection