@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

@section('content')
<style>
    :root {
        --azure-main: #3b82f6;
        --azure-light: #eff6ff;
        --dark-slate: #0f172a;
    }

    body {
        background: linear-gradient(180deg, #47a0ff 0%, #3b82f6 100%);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ✨ TITLE SECTION */
    .invoice-header {
        position: relative;
        padding: 40px 0;
    }

    .invoice-title {
        font-weight: 800;
        font-size: 3rem;
        color: white;
        letter-spacing: -2px;
        line-height: 0.9;
    }

    /* 📄 CARD INVOICE */
    .card-invoice {
        background: #ffffff;
        border-radius: 35px;
        border: none;
        box-shadow: 0 25px 70px rgba(0,0,0,0.2);
        overflow: hidden;
        position: relative;
    }

    /* Watermark Effect */
    .card-invoice::before {
        content: "#{{ $order->order_number }}";
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 5rem;
        font-weight: 900;
        color: rgba(0,0,0,0.03);
        z-index: 0;
        pointer-events: none;
    }

    .card-body { position: relative; z-index: 1; }

    /* 🏷️ STATUS BADGE EXCLUSIVE */
    .status-container {
        display: inline-flex;
        align-items: center;
        padding: 8px 20px;
        border-radius: 15px;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .status-pending { background: #fef3c7; color: #92400e; }
    .status-delivered { background: #dcfce7; color: #166534; }
    .status-processing { background: #dbeafe; color: #1e40af; }

    /* 📊 TABLE CUSTOM */
    .table-premium thead th {
        background: #f8fafc;
        border: none;
        color: #64748b;
        font-size: 0.7rem;
        text-transform: uppercase;
        padding: 20px;
        letter-spacing: 1.5px;
    }

    .table-premium tbody td {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        color: var(--dark-slate);
        font-weight: 500;
    }

    /* 🛡️ PAYMENT SECTION */
    .payment-footer {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        padding: 40px;
    }

    .btn-pay-now {
        background: var(--dark-slate);
        color: white;
        border-radius: 20px;
        padding: 20px 50px;
        font-weight: 800;
        font-size: 1.1rem;
        border: none;
        transition: 0.4s;
        width: 100%;
        max-width: 400px;
    }

    .btn-pay-now:hover {
        background: #000;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        color: white;
    }

    .info-label {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 5px;
        display: block;
    }
</style>

<div class="container py-5">
    {{-- Top Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('orders.index') }}" class="text-white text-decoration-none fw-bold small">
            <i class="bi bi-chevron-left me-2"></i> KEMBALI KE RIWAYAT
        </a>
        <div class="text-white-50 small fw-bold">DITERBITKAN PADA {{ $order->created_at->format('d/m/Y') }}</div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            <div class="card-invoice animate__animated animate__fadeInUp">
                {{-- Invoice Header Inside Card --}}
                <div class="p-4 p-md-5 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <span class="text-primary fw-900 fs-4 mb-2 d-block">TASTY FOOD.</span>
                            <h1 class="invoice-title text-dark">Invoice<br><span class="text-primary">#{{ $order->order_number }}</span></h1>
                        </div>
                        <div class="col-md-6 text-md-end mt-4 mt-md-0">
                            @php
                                $statusSlug = strtolower($order->status);
                                $statusClass = match($statusSlug) {
                                    'pending' => 'status-pending',
                                    'delivered' => 'status-delivered',
                                    'processing' => 'status-processing',
                                    default => 'bg-secondary text-white',
                                };
                            @endphp
                            <div class="status-container {{ $statusClass }}">
                                <i class="bi bi-patch-check-fill me-2"></i> {{ $order->status }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    {{-- Address & Details Grid --}}
                    <div class="row g-0">
                        <div class="col-md-4 border-end p-4 p-md-5 bg-light bg-opacity-50">
                            <div class="mb-4">
                                <span class="info-label">Penerima</span>
                                <h6 class="fw-800 text-dark mb-1">{{ $order->shipping_name }}</h6>
                                <p class="text-muted small mb-0">{{ $order->shipping_phone }}</p>
                            </div>
                            <div>
                                <span class="info-label">Alamat Pengiriman</span>
                                <p class="text-dark small mb-0 fw-medium" style="line-height: 1.6;">
                                    {{ $order->shipping_address }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="col-md-8 p-0">
                            <div class="table-responsive">
                                <table class="table table-premium mb-0">
                                    <thead>
                                        <tr>
                                            <th>Item Detail</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <span class="d-block fw-800 text-dark">{{ $item->product_name }}</span>
                                                <span class="text-muted smaller">Harga per unit: Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                            </td>
                                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                            <td class="text-end fw-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Totals --}}
                            <div class="p-4 p-md-5">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted fw-bold small uppercase">ONGKOS KIRIM</span>
                                    <span class="text-dark fw-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                                    <span class="h6 fw-900 text-dark mb-0">TOTAL DIBAYARKAN</span>
                                    <span class="h3 fw-900 text-primary mb-0">
                                        Rp {{ number_format($order->discount_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Payment Section --}}
                @if($order->payment_status === 'unpaid' && $order->snap_token)
                <div class="payment-footer text-center">
                    <div class="mb-4">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Midtrans.png" height="30" class="opacity-50 grayscale mb-2" alt="Midtrans">
                        <p class="text-muted smaller mb-0">Secure payment via Midtrans Encryption</p>
                    </div>
                    <button id="pay-button" class="btn btn-pay-now shadow-lg">
                        SELESAIKAN PEMBAYARAN <i class="bi bi-shield-lock-fill ms-2"></i>
                    </button>
                </div>
                @else
                <div class="bg-light p-4 text-center">
                    <span class="text-muted small fw-bold">Metode Pembayaran: Midtrans Snap</span>
                </div>
                @endif
            </div>

            <div class="text-center mt-5">
                <button onclick="window.print()" class="btn btn-link text-white text-decoration-none opacity-75 small">
                    <i class="bi bi-printer me-2"></i> CETAK INVOICE INI
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(!empty($order->snap_token))
<script src="{{ config('midtrans.snap_url') }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const payButton = document.getElementById('pay-button');
        if (!payButton) return;

        payButton.addEventListener('click', function () {
            payButton.disabled = true;
            payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menghubungkan...';

            snap.pay('{{ $order->snap_token }}', {
                onSuccess() { window.location.href = '{{ route("orders.success", $order) }}'; },
                onPending() { window.location.href = '{{ route("orders.pending", $order) }}'; },
                onError() {
                    alert('Pembayaran gagal. Silakan coba lagi.');
                    payButton.disabled = false;
                    payButton.innerHTML = 'BAYAR SEKARANG';
                },
                onClose() {
                    payButton.disabled = false;
                    payButton.innerHTML = 'BAYAR SEKARANG';
                }
            });
        });
    });
</script>
@endif
@endpush