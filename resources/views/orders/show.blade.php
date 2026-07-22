@extends('layouts.app')

@section('title', 'Pesanan #' . $order->order_number)

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

    .invoice-title {
        font-weight: 800;
        font-size: 3rem;
        color: white;
        letter-spacing: -2px;
        line-height: 0.9;
    }

    .card-invoice {
        background: #ffffff;
        border-radius: 35px;
        border: none;
        box-shadow: 0 25px 70px rgba(0,0,0,0.2);
        overflow: hidden;
        position: relative;
    }

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

    .card-body {
        position: relative;
        z-index: 1;
    }

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
    .status-completed { background: #dcfce7; color: #166534; }

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

    .btn-pay-now:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* ⭐ STYLE BARU: Tombol COD */
    .btn-pay-cod {
        background: #e2e8f0;
        color: var(--dark-slate);
        border-radius: 20px;
        padding: 15px 50px;
        font-weight: 700;
        font-size: 1rem;
        border: 2px dashed #cbd5e1;
        transition: 0.3s;
        width: 100%;
        max-width: 400px;
    }

    .btn-pay-cod:hover {
        background: #fef3c7;
        color: #92400e;
        border-color: #fbbf24;
    }

    .info-label {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 5px;
        display: block;
    }

    /* ⭐ RATING */
    .rating-card {
        background: white;
        border-radius: 35px;
        box-shadow: 0 25px 70px rgba(0,0,0,0.15);
    }

    .rating-stars {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 10px;
    }

    .rating-stars input { display: none; }

    .rating-stars label {
        font-size: 2.8rem;
        color: #cbd5e1;
        cursor: pointer;
        transition: 0.3s;
    }

    .rating-stars label:hover,
    .rating-stars label:hover ~ label,
    .rating-stars input:checked ~ label {
        color: #fbbf24;
        transform: scale(1.1);
    }

    .btn-rating {
        background: #0f172a;
        color: white;
        border: none;
        border-radius: 999px;
        padding: 14px 40px;
        font-weight: 700;
        transition: .3s;
    }

    .btn-rating:hover {
        background: #000;
        transform: translateY(-2px);
        color: white;
    }

    .btn-pay-cod {
        background-color: #ffffff;
        border: 1px solid #e0e0e0;
        color: #333;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-pay-cod:hover {
        background-color: #f8f9fa;
        border-color: #28a745;
        color: #28a745;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
    }

    .alert-token-gagal {
        border-radius: 20px;
        border: none;
        background: #fee2e2;
        color: #991b1b;
        padding: 25px 30px;
        text-align: left;
    }

    @media print {
        body { background: white; }
        .btn-pay-now, .btn-pay-cod, .rating-card, .btn-link, .d-flex a, .payment-footer { display: none !important; }
    }
</style>

<div class="container py-5">

    {{-- Session Flash Message Sukses Ganti COD --}}
    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 p-3 shadow mb-4 animate__animated animate__fadeInDown">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 p-3 shadow mb-4 animate__animated animate__fadeInDown">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        </div>
    @endif

    {{-- NAVIGASI ATAS --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('orders.index') }}" class="text-white text-decoration-none fw-bold small">
            <i class="bi bi-chevron-left me-2"></i> KEMBALI KE RIWAYAT
        </a>
        <div class="text-white-50 small fw-bold text-uppercase">
            DITERBITKAN PADA {{ $order->created_at->format('d/m/Y') }}
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">

            <div class="card-invoice animate__animated animate__fadeInUp">

                {{-- HEADER --}}
                <div class="p-4 p-md-5 border-bottom">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1 class="invoice-title text-dark">
                                Wangi Project <br>
                                <span class="text-primary">#{{ $order->order_number }}</span>
                            </h1>
                        </div>

                        <div class="col-md-6 text-md-end mt-4 mt-md-0">
                            @php
                                $statusSlug = strtolower($order->status);
                                $statusLabel = match($statusSlug) {
                                    'pending' => 'Menunggu Pembayaran',
                                    'delivered' => 'Dalam Pengiriman',
                                    'processing' => 'Sedang Diproses',
                                    'completed' => 'Selesai',
                                    default => $order->status,
                                };
                                $statusClass = match($statusSlug) {
                                    'pending' => 'status-pending',
                                    'delivered' => 'status-delivered',
                                    'processing' => 'status-processing',
                                    'completed' => 'status-completed',
                                    default => 'bg-secondary text-white',
                                };
                            @endphp

                            <span class="info-label mb-1">Status Pesanan Anda</span>
                            <div class="status-container {{ $statusClass }}">
                                <i class="bi bi-patch-check-fill me-2"></i>
                                {{ $statusLabel }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BODY --}}
                <div class="card-body p-0">
                    <div class="row g-0">
                        {{-- ALAMAT & INFORMASI --}}
                        <div class="col-md-4 border-end p-4 p-md-5 bg-light bg-opacity-50">
                            <div class="mb-4">
                                <span class="info-label">Penerima</span>
                                <h6 class="fw-800 text-dark mb-1">{{ $order->shipping_name }}</h6>
                                <p class="text-muted small mb-0">{{ $order->shipping_phone }}</p>
                            </div>
                            <div class="mb-4">
                                <span class="info-label">Alamat Pengiriman</span>
                                <p class="text-dark small mb-0 fw-medium" style="line-height: 1.6;">
                                    {{ $order->shipping_address }}
                                </p>
                            </div>
                            <div>
                                <span class="info-label">Metode Pembayaran</span>
                                <span class="badge {{ strtolower($order->payment_method ?? '') === 'cod' ? 'bg-warning text-dark' : 'bg-dark' }} fw-bold text-uppercase px-3 py-2 rounded-3">
                                    {{ $order->payment_method ?? 'Midtrans Snap' }}
                                </span>
                            </div>
                        </div>

                        {{-- TABEL ITEM --}}
                        <div class="col-md-8 p-0">
                            <div class="table-responsive">
                                <table class="table table-premium mb-0">
                                    <thead>
                                        <tr>
                                            <th>Detail Produk</th>
                                            <th class="text-center">Jumlah</th>
                                            <th>Durasi</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <span class="d-block fw-800 text-dark">{{ $item->product_name }}</span>
                                                <span class="text-muted smaller">
                                                    @if($item->product?->rental_price)
                                                        Rp {{ number_format($item->product->rental_price, 0, ',', '.') }}/hari
                                                    @else
                                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                            <td class="text-center">
                                                @if($item->product?->rental_price)
                                                    {{ $item->product->rental_duration }} Hari
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end fw-800">
                                                @if($item->product?->rental_price)
                                                    Rp {{ number_format(($item->product->rental_price ?? 0) * $item->quantity, 0, ',', '.') }}
                                                @else
                                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- TOTAL PEMBAYARAN --}}
                            <div class="p-4 p-md-5">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted fw-bold small">SUBTOTAL UTAMA</span>
                                    <span class="text-dark fw-bold">Rp {{ number_format($order->items->sum('subtotal'), 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted fw-bold small">ONGKOS KIRIM (MURNI)</span>
                                    <span class="text-dark fw-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary fw-medium">BIAYA BERAT TAMBAHAN</span>
                                    <span class="fw-bold text-dark">
                                        Rp {{ number_format($order->weight_cost, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-3">
                                    <span class="h6 fw-900 text-dark mb-0">TOTAL TAGIHAN</span>
                                    <span class="h3 fw-900 text-primary mb-0">
                                        Rp {{ number_format($order->items->sum('subtotal') + $order->shipping_cost + ($order->weight_cost ?? $order->biaya_berat ?? 0), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEKSI TOMBOL PEMBAYARAN --}}
                @if(strtolower($order->payment_method ?? '') === 'cod')

                    {{-- SUDAH COD --}}
                    <div class="payment-footer text-center bg-light border-top animate__animated animate__fadeIn">
                        <div class="alert alert-warning d-inline-block border-0 rounded-4 px-4 py-3 shadow-sm mb-0">
                            <div class="d-flex align-items-center gap-3 text-start">
                                <div class="fs-1 text-warning"><i class="bi bi-cash-coin"></i></div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-1">Metode Pembayaran: Bayar di Tempat (COD)</h6>
                                    <p class="text-muted small mb-0">
                                        Pesanan Anda sedang diproses. Silakan siapkan uang pas sebesar <strong>Rp {{ number_format($order->items->sum('subtotal') + $order->shipping_cost + ($order->weight_cost ?? $order->biaya_berat ?? 0), 0, ',', '.') }}</strong> saat kurir mengantarkan barang.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($order->payment_status === 'unpaid' && $order->snap_token)

                    {{-- BELUM COD, BELUM BAYAR, DAN TOKEN SUDAH SIAP --}}
                    <div class="payment-footer text-center">
                        <div class="mb-4">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/b/b1/Midtrans.png" height="30" class="opacity-50 mb-2" alt="Midtrans">
                            <p class="text-muted smaller mb-0">Silakan pilih salah satu metode pembayaran di bawah ini:</p>
                        </div>

                        {{-- Opsi 1: Bayar via Midtrans --}}
                        <div class="mb-3">
                            <button id="pay-button" class="btn btn-pay-now shadow-lg">
                                BAYAR SEKARANG (ONLINE) <i class="bi bi-shield-lock-fill ms-2"></i>
                            </button>
                        </div>

                        {{-- Opsi 2: Tombol Ganti ke COD --}}
                        <div>
                            <form action="{{ route('orders.convertToCod', $order->id) }}" method="POST" id="formCod-{{ $order->id }}">
                                @csrf
                                @method('PUT')

                                <button type="button"
                                        class="btn btn-pay-cod shadow-sm"
                                        onclick="showConfirm(
                                            'Ubah ke COD?',
                                            'Anda yakin ingin mengubah metode pembayaran menjadi COD?',
                                            () => document.getElementById('formCod-{{ $order->id }}').submit()
                                        )">
                                    <i class="bi bi-truck me-2"></i> BAYAR DI TEMPAT (COD)
                                </button>
                            </form>
                        </div>
                    </div>

                @elseif($order->payment_status === 'unpaid' && !$order->snap_token)

                    {{-- BELUM COD, BELUM BAYAR, TAPI TOKEN GAGAL DIBUAT --}}
                    <div class="payment-footer text-center">
                        <div class="alert-token-gagal d-inline-block mb-4">
                            <div class="d-flex align-items-start gap-3">
                                <div class="fs-2"><i class="bi bi-exclamation-octagon-fill"></i></div>
                                <div>
                                    <h6 class="fw-bold mb-1">Gagal Memuat Metode Pembayaran Online</h6>
                                    <p class="small mb-0">
                                        Terjadi kendala saat menyiapkan pembayaran online untuk pesanan ini.
                                        Silakan muat ulang halaman, atau pilih Bayar di Tempat (COD) di bawah ini.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button onclick="window.location.reload()" class="btn btn-pay-now shadow-lg">
                                <i class="bi bi-arrow-clockwise me-2"></i> MUAT ULANG HALAMAN
                            </button>
                        </div>

                        <div>
                            <form action="{{ route('orders.convertToCod', $order->id) }}" method="POST" id="formCod-{{ $order->id }}">
                                @csrf
                                @method('PUT')

                                <button type="button"
                                        class="btn btn-pay-cod shadow-sm"
                                        onclick="showConfirm(
                                            'Ubah ke COD?',
                                            'Anda yakin ingin mengubah metode pembayaran menjadi COD?',
                                            () => document.getElementById('formCod-{{ $order->id }}').submit()
                                        )">
                                    <i class="bi bi-truck me-2"></i> BAYAR DI TEMPAT (COD)
                                </button>
                            </form>
                        </div>
                    </div>

                @else

                    {{-- SUDAH LUNAS --}}
                    <div class="bg-light p-3 text-center border-top">
                        <span class="text-muted small fw-bold">Metode Pembayaran: {{ strtoupper($order->payment_method ?? 'Midtrans') }} (LUNAS)</span>
                    </div>

                @endif
            </div>

            {{-- ⭐ SEKSI RATING (MUNCUL JIKA SELESAI) --}}
            @php
                $hasRated = \App\Models\Rating::where('order_id', $order->id)->exists();
            @endphp

            @if(strtolower($order->status) === 'completed')
                @if(!$hasRated)
                    <div class="rating-card mt-4 p-4 p-md-5 animate__animated animate__fadeInUp">
                        <div class="text-center mb-4">
                            <div style="font-size: 4rem;">⭐</div>
                            <h3 class="fw-bold mb-2">Bagaimana Kualitas Produk Kami?</h3>
                            <p class="text-muted">Berikan ulasan Anda untuk membantu kami meningkatkan layanan.</p>
                        </div>

                        <form action="{{ route('rating.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="rating-stars mb-4">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" required>
                                    <label for="star{{ $i }}">★</label>
                                @endfor
                            </div>

                            <div class="mb-4">
                                <textarea name="message" rows="4" class="form-control rounded-4 shadow-sm border-0 bg-light p-3"
                                          placeholder="Ceritakan pengalaman Anda belanja di Wangi Project..." required></textarea>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn-rating">Kirim Ulasan Sekarang</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="rating-card mt-4 p-4 p-md-5 text-center animate__animated animate__fadeIn">
                        <div class="mb-2" style="font-size: 3.5rem;">✅</div>
                        <h4 class="fw-bold text-dark">Ulasan Berhasil Terkirim!</h4>
                        <p class="text-muted mb-0">Terima kasih telah meluangkan waktu untuk memberikan penilaian.</p>
                    </div>
                @endif
            @endif

            {{-- TOMBOL CETAK --}}
            <div class="text-center mt-5 mb-5">
                <button onclick="window.print()" class="btn btn-link text-white text-decoration-none opacity-75 small">
                    <i class="bi bi-printer me-2"></i> CETAK INVOICE (PDF)
                </button>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($order->payment_status === 'unpaid' && strtolower($order->payment_method ?? '') !== 'cod' && !empty($order->snap_token))
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
                    alert('Maaf, terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                    payButton.disabled = false;
                    payButton.innerHTML = 'BAYAR SEKARANG (ONLINE) <i class="bi bi-shield-lock-fill ms-2"></i>';
                },
                onClose() {
                    payButton.disabled = false;
                    payButton.innerHTML = 'BAYAR SEKARANG (ONLINE) <i class="bi bi-shield-lock-fill ms-2"></i>';
                }
            });
        });

        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i> Memproses...';
                }
            });
        });
    });
</script>
@endif
@endpush