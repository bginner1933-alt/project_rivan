@extends('layouts.admin')

@section('title', 'Pesanan Perlu Diproses')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid px-4 py-4 bg-soft">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <div>
            <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i>
                Pesanan Perlu Diproses
            </h2>
            <div class="text-muted small">
                Dashboard monitoring order masuk & status pengiriman
            </div>
        </div>

        <div class="badge-soft">
            <i class="bi bi-hourglass-split me-1"></i>
            {{ $orders->count() }} Order Aktif
        </div>

    </div>

    {{-- CARD --}}
    <div class="card premium-card border-0">

        <div class="table-responsive">
            <table class="table align-middle mb-0 modern-table">

                <thead>
                    <tr>
                        <th>No. Order</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($orders as $order)
                    <tr class="table-row">

                        <td class="order-id">
                            #{{ $order->order_number }}
                        </td>

                        <td>
                            <div class="user-box">

                                <div class="avatar">
                                    {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                                </div>

                                <div>
                                    <div class="fw-semibold text-dark">
                                        {{ $order->user->name ?? 'Guest' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $order->user->email ?? '-' }}
                                    </small>
                                </div>

                            </div>
                        </td>

                        <td class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </td>

                        <td class="text-end fw-bold text-primary">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>

                        <td class="text-center">

                            <div class="action-group">

                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="btn btn-light btn-sm action-btn">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <form action="{{ route('admin.orders.update-status', $order) }}"
                                      method="POST">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="completed">

                                    <button class="btn btn-primary btn-sm action-btn">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>

                            </div>

                        </td>

                    </tr>
                    @empty

                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h6 class="mt-3">Tidak ada pesanan</h6>
                                <p class="text-muted small">Semua pesanan sudah diproses</p>
                            </div>
                        </td>
                    </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

</div>

<style>

.bg-soft{
    background:#f6f8fc;
    min-height:100vh;
}

.premium-card{
    border-radius:18px;
    overflow:hidden;
    background:#fff;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
}

.modern-table thead{
    background: linear-gradient(135deg,#0d6efd,#4f8cff);
    color:white;
}

.modern-table thead th{
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:1px;
    padding:16px;
}

.table-row:hover{
    background:#f3f7ff;
}

.order-id{
    font-weight:700;
    color:#0d6efd;
}

.user-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.avatar{
    width:38px;
    height:38px;
    border-radius:12px;
    background:linear-gradient(135deg,#0d6efd,#6ea8fe);
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
}

.action-group{
    display:flex;
    gap:8px;
    justify-content:center;
}

.action-btn{
    width:36px;
    height:36px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:10px;
}

.badge-soft{
    background:#eaf2ff;
    color:#0d6efd;
    padding:8px 14px;
    border-radius:999px;
    font-size:13px;
    font-weight:600;
}

.empty-state i{
    font-size:50px;
    opacity:.2;
}

</style>

@endsection