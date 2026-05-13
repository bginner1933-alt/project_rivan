@extends('layouts.app')

@section('title', 'Manajemen Pesanan')

@section('content')
<style>
    :root {
        --azure-main: #3b82f6;
        --bg-blue: #47a0ff; 
        --azure-dark: #1e3a8a;
        --dark-slate: #0f172a;
    }

    /* 🌌 BACKGROUND UTAMA (Azure Theme) */
    body {
        background: var(--bg-blue);
        background: linear-gradient(180deg, #47a0ff 0%, #3b82f6 100%);
        background-attachment: fixed;
        min-height: 100vh;
    }

    /* ✨ TITLE STYLING */
    .display-magazine {
        font-size: 3rem;
        font-weight: 800;
        color: white;
        letter-spacing: -2px;
        line-height: 1.1;
    }

    .text-header-small {
        color: rgba(255, 255, 255, 0.8) !important;
        font-weight: bold;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 3px;
        display: block;
    }

    /* ✨ CARD STYLING */
    .card-orders {
        background: #ffffff;
        border-radius: 24px;
        border: none;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    /* NAV PILLS CUSTOM */
    .nav-pills .nav-link {
        color: #64748b;
        font-weight: 600;
        border-radius: 50px;
        padding: 10px 20px;
        transition: 0.3s;
    }

    .nav-pills .nav-link.active {
        background-color: var(--azure-main) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: #f1f5f9;
        color: var(--azure-main);
    }

    /* TABLE STYLING */
    .table thead {
        background-color: #f8fafc;
    }

    .table thead th {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
        color: #64748b;
        padding: 20px;
        border: none;
    }

    .table tbody tr {
        transition: 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8faff;
    }

    /* AVATAR */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eff6ff;
        color: var(--azure-main);
        font-weight: 700;
    }

    /* BUTTONS */
    .btn-home-nav {
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        background: white;
        color: var(--azure-main);
        padding: 12px 24px;
        text-decoration: none;
        display: inline-block;
        border: none;
        transition: 0.3s;
    }

    .btn-home-nav:hover {
        background: var(--dark-slate);
        color: white;
        transform: translateY(-3px);
    }

    .btn-detail {
        background: var(--azure-main);
        color: white;
        border-radius: 50px;
        font-weight: 600;
        padding: 6px 18px;
        font-size: 0.85rem;
        border: none;
    }

    .btn-detail:hover {
        background: var(--azure-dark);
        color: white;
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
    }

    /* STATUS BADGE */
    .badge-status {
        padding: 6px 14px;
        font-weight: 600;
        border-radius: 50px;
        font-size: 0.75rem;
    }
</style>

<div class="container py-5">
    {{-- Header Section --}}
    <div class="row mb-5 align-items-end">
        <div class="col-md-8">
            <span class="text-header-small mb-2">Panel Administrasi</span>
            <h1 class="display-magazine">Manajemen <br><span>Pesanan.</span></h1>
        </div>
        <div class="col-md-4 text-md-end mt-4 mt-md-0">
            <a href="{{ route('home') }}" class="btn-home-nav shadow-sm">
                <i class="bi bi-house-door me-2"></i>Beranda
            </a>
        </div>
    </div>

    <div class="card card-orders">
        {{-- Filter Status --}}
        <div class="card-header bg-white border-bottom py-4 px-4">
            <ul class="nav nav-pills gap-2">
                <li class="nav-item">
                    <h2 align="center"><b>Daftar Pesanan</b></h2>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No. Order</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark">#{{ $order->order_number }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            {{ strtoupper(substr($order->user?->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">{{ $order->user?->name }}</div>
                                            <small class="text-muted">{{ $order->user?->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium" style="font-size: 0.85rem;">
                                        {{ $order->created_at->format('d M Y') }}
                                    </div>
                                    <small class="text-muted">{{ $order->created_at->format('H:i') }} WIB</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </td>
                               <td>
                                    @php
                                        $status = strtolower($order->status);

                                        $statusClass = match($status) {
                                            'pending' => 'bg-warning text-dark',
                                            'processing' => 'bg-primary',
                                            'delivered' => 'bg-info',
                                            'completed' => 'bg-success',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };

                                        $statusLabel = match($status) {
                                            'pending' => 'Menunggu',
                                            'processing' => 'Diproses',
                                            'delivered' => 'Dikirim',
                                            'completed' => 'Selesai',
                                            'cancelled' => 'Dibatalkan',
                                            default => ucfirst($order->status),
                                        };
                                    @endphp

                                    <span class="badge badge-status {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-detail">
                                        Detail <i class="bi bi-chevron-right ms-1 small"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-inbox text-muted opacity-25 display-1"></i>
                                        <h5 class="fw-bold text-dark mt-3">Tidak Ada Pesanan</h5>
                                        <p class="text-muted small">Pesanan yang Anda cari tidak ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white border-top py-4">
            <div class="d-flex justify-content-center">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection