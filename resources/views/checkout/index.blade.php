@extends('layouts.app')

@section('title', 'Finalize Order')

@section('content')
<style>
    :root {
        --primary-blue: #3b82f6;
        --azure-dark: #1e40af;
        --glass-white: rgba(255, 255, 255, 0.9);
    }

    body {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ✨ HEADER & STEPPER */
    .checkout-header {
        padding: 40px 0 20px;
        text-align: center;
        color: white;
    }

    .checkout-title {
        font-weight: 800;
        font-size: 2.5rem;
        letter-spacing: -1.5px;
    }

    /* 📦 CARD STYLING */
    .checkout-card {
        background: var(--glass-white);
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

    /* 📝 FORM STYLING */
    .form-label {
        font-weight: 700;
        font-size: 0.85rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control {
        border-radius: 15px;
        padding: 12px 20px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        transition: 0.3s;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    /* 💰 SUMMARY BOX */
    .order-item {
        transition: 0.3s;
        border-radius: 15px;
    }

    .total-display {
        background: #f8fafc;
        border-radius: 20px;
        padding: 25px;
        border: 2px dashed #e2e8f0;
    }

    /* 🚀 BUTTON */
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

    .btn-pay:hover {
        background: var(--azure-dark);
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(30, 64, 175, 0.4);
        color: white;
    }

    .badge-qty {
        background: var(--primary-blue);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }
</style>

<div class="container py-4">
    <div class="checkout-header animate__animated animate__fadeInDown">
        <div class="d-flex justify-content-center align-items-center mb-2">
            <div class="badge bg-white text-primary rounded-pill px-3 py-2 fw-bold shadow-sm">STEP 2 OF 2</div>
        </div>
        <h1 class="checkout-title">Finalize Your Order<span class="opacity-50">.</span></h1>
    </div>

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-lg rounded-4 p-3 animate__animated animate__shakeX">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4 mt-2">
        {{-- Ringkasan Pesanan (Kiri) --}}
        <div class="col-lg-5 order-2 order-lg-1">
            <div class="checkout-card shadow-lg animate__animated animate__fadeInLeft">
                <div class="card-header-premium">
                    <h5 class="mb-0 fw-800 text-dark">Order Summary</h5>
                </div>
                <div class="card-body p-4">
                    <div class="order-list mb-4" style="max-height: 350px; overflow-y: auto;">
                        @foreach($cart->items as $item)
                            <div class="order-item d-flex align-items-center mb-3 p-2 hover-bg-light">
                                <div class="position-relative">
                                    <img src="{{ $item->product?->image_url ?? '#' }}" 
                                         class="rounded-3 border" width="65" height="65" style="object-fit: cover;">
                                    <span class="position-absolute top-0 start-100 translate-middle badge-qty shadow">
                                        {{ $item->quantity }}
                                    </span>
                                </div>
                                <div class="ms-4 flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-dark small">{{ Str::limit($item->product?->name, 35) }}</h6>
                                    <span class="text-muted smaller">Unit: Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-end fw-bold text-dark">
                                    Rp {{ number_format(($item->price ?? 0) * $item->quantity * ($item->duration ?? 1), 0, ',', '.') }}
                                </div>  
                            </div>
                        @endforeach
                    </div>

                    <div class="total-display">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary fw-medium">Subtotal</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($total - $shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary fw-medium">Shipping Cost</span>
                            <span class="fw-bold text-success">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                            <span class="h5 fw-800 text-dark mb-0">Total Amount</span>
                            <span class="h4 fw-900 text-primary mb-0">
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Pengiriman (Kanan) --}}
        <div class="col-lg-7 order-1 order-lg-2">
            <div class="checkout-card shadow-lg animate__animated animate__fadeInRight">
                <div class="card-header-premium d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-800 text-dark">Shipping Details</h5>
                    <i class="bi bi-truck text-primary fs-4"></i>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('checkout.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="form-label">Nama Lengkap Penerima</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light rounded-start-4"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="name" 
                                           class="form-control rounded-end-4 @error('name') is-invalid @enderror"
                                           placeholder="Masukkan nama lengkap..."
                                           value="{{ old('name', auth()->user()->name) }}" required>
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label">No. Handphone / WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light rounded-start-4"><i class="bi bi-whatsapp text-muted"></i></span>
                                    <input type="text" name="phone" 
                                           class="form-control rounded-end-4 @error('phone') is-invalid @enderror"
                                           placeholder="Contoh: 0812xxxxxxxx"
                                           value="{{ old('phone', auth()->user()->phone) }}" required>
                                </div>
                                @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12 mb-4">
                                <label class="form-label">Alamat Lengkap Tujuan</label>
                                <textarea name="address" rows="4" 
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Tuliskan alamat lengkap beserta kode pos..."
                                          required>{{ old('address', auth()->user()->address) }}</textarea>
                                @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="bg-blue-soft p-3 rounded-4 mb-4 d-flex align-items-start" style="background: #eff6ff;">
                            <i class="bi bi-shield-check text-primary me-3 fs-4"></i>
                            <p class="small text-muted mb-0">
                                <strong>Transaksi Aman:</strong> Dengan menekan tombol di bawah, Anda setuju dengan Syarat & Ketentuan kami. Pesanan Anda akan segera diproses setelah konfirmasi pembayaran.
                            </p>
                        </div>

                        <button type="submit" class="btn btn-pay w-100 shadow-lg">
                            Bayar Sekarang <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection