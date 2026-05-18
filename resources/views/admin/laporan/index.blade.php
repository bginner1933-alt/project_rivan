@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')

@php
    $totalRevenue = $summary->total_revenue ?? 0;
@endphp

<div class="report-bg py-4">

    <div class="container-fluid">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-0">Laporan Penjualan</h3>
                <small class="text-muted">
                    Analisis performa bisnis Anda
                </small>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="filter-card mb-4">

            <form method="GET" class="row g-3 align-items-end">

                <div class="col-md-3">
                    <label class="form-label small text-muted">
                        Dari
                    </label>

                    <input
                        type="date"
                        name="date_from"
                        value="{{ $dateFrom }}"
                        class="form-control clean-input"
                    >
                </div>

                <div class="col-md-3">
                    <label class="form-label small text-muted">
                        Sampai
                    </label>

                    <input
                        type="date"
                        name="date_to"
                        value="{{ $dateTo }}"
                        class="form-control clean-input"
                    >
                </div>

                <div class="col-md-6 d-flex gap-2">

                    <button class="btn btn-primary px-4">
                        Filter
                    </button>

                    <a href="{{ route('admin.reports.export-sales', request()->all()) }}"
                       class="btn btn-success px-4">
                        Export
                    </a>

                </div>

            </form>

        </div>

        {{-- SUMMARY --}}
        <div class="row g-4 mb-4">

            {{-- TOTAL REVENUE --}}
            <div class="col-md-4">
                <div class="metric-card">

                    <div class="label">
                        Total Revenue
                    </div>

                    <div class="value">
                        Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}
                    </div>

                </div>
            </div>

            {{-- TOTAL ORDERS --}}
            <div class="col-md-4">
                <div class="metric-card">

                    <div class="label">
                        Total Orders
                    </div>

                    <div class="value">
                        {{ number_format($summary->total_orders ?? 0) }}
                    </div>

                </div>
            </div>

        </div>

        {{-- CONTENT --}}
        <div class="row g-4">

            {{-- CATEGORY --}}
            <div class="col-lg-4">

                <div class="panel-card">

                    <div class="panel-title">
                        Performa Kategori
                    </div>

                    @forelse($byCategory ?? [] as $cat)

                        @php
                            $percentage = $totalRevenue > 0
                                ? ($cat->total / $totalRevenue) * 100
                                : 0;
                        @endphp

                        <div class="cat-item">

                            <div class="d-flex justify-content-between align-items-center">

                                <div>
                                    <div class="fw-semibold">
                                        {{ $cat->name }}
                                    </div>

                                    <small class="text-muted">
                                        {{ number_format($percentage, 1) }}%
                                    </small>
                                </div>

                                <strong>
                                    Rp {{ number_format($cat->total, 0, ',', '.') }}
                                </strong>

                            </div>

                            <div class="bar">
                                <div
                                    style="width: {{ min($percentage, 100) }}%">
                                </div>
                            </div>

                        </div>

                    @empty

                        <div class="text-center text-muted py-4">
                            Tidak ada data kategori
                        </div>

                    @endforelse

                </div>

            </div>

            {{-- TABLE --}}
            <div class="col-lg-8">

                <div class="panel-card">

                    <div class="panel-title">
                        Transaksi
                    </div>

                    <div class="table-responsive">

                        <table class="table modern-table align-middle">

                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($orders ?? [] as $order)

                                    <tr>

                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                               class="order-id">

                                                #{{ $order->order_number }}

                                            </a>
                                        </td>

                                        <td class="text-muted">
                                            {{ $order->created_at->format('d M Y') }}
                                        </td>

                                        <td>

                                            <div class="fw-semibold">
                                                {{ $order->user->name ?? 'User Deleted' }}
                                            </div>

                                            <small class="text-muted">
                                                {{ $order->user->email ?? '-' }}
                                            </small>

                                        </td>

                                        <td class="text-end fw-bold">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4"
                                            class="text-center text-muted py-5">

                                            Tidak ada transaksi

                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    {{-- PAGINATION --}}
                    @if(isset($orders) && method_exists($orders, 'links'))
                        <div class="mt-3">
                            {{ $orders->appends(request()->all())->links() }}
                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

{{-- STYLE --}}
<style>

.report-bg{
    background:#f5f7fb;
    min-height:100vh;
}

/* FILTER */
.filter-card{
    background:#fff;
    padding:20px;
    border-radius:14px;
    box-shadow:0 6px 20px rgba(0,0,0,0.04);
}

/* INPUT */
.clean-input{
    border-radius:10px;
    border:1px solid #e5e9f2;
    padding:10px;
}

/* METRIC */
.metric-card{
    background:#fff;
    padding:20px;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.04);
}

.metric-card .label{
    font-size:12px;
    color:#888;
    text-transform:uppercase;
}

.metric-card .value{
    font-size:24px;
    font-weight:700;
    margin-top:5px;
}

/* PANEL */
.panel-card{
    background:#fff;
    padding:20px;
    border-radius:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.04);
    height:100%;
}

.panel-title{
    font-weight:700;
    margin-bottom:20px;
    font-size:16px;
}

/* CATEGORY */
.cat-item{
    margin-bottom:18px;
}

.bar{
    height:7px;
    background:#eef2f7;
    border-radius:999px;
    margin-top:8px;
    overflow:hidden;
}

.bar div{
    height:100%;
    background:#4f8cff;
    border-radius:999px;
    transition:.3s ease;
}

/* TABLE */
.modern-table{
    font-size:14px;
}

.modern-table thead th{
    border-bottom:1px solid #eef2f7;
    color:#777;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.modern-table tbody tr{
    border-color:#f3f4f6;
}

.order-id{
    text-decoration:none;
    font-weight:600;
    color:#4f8cff;
}

.order-id:hover{
    color:#2563eb;
}

</style>

@endsection