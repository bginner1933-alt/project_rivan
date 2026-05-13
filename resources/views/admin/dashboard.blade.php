@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<div class="container-fluid px-4 py-4 animate__animated animate__fadeIn">
    
    {{-- 1. Elegant Header --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="fw-extrabold text-dark mb-1">📈 Overview Bisnis</h3>
            <p class="text-muted mb-0">Laporan performa toko Anda hari ini.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-4">
                <i class="bi bi-calendar3 text-primary me-2"></i>
                <span class="fw-bold text-dark small">{{ now()->format('d M Y') }}</span>
            </div>
        </div>
    </div>

    {{-- 2. Modern Stats Cards --}}
    <div class="row g-4 mb-4">
        {{-- Revenue Card --}}
        <div class="col-12 col-xl-4"> 
            <a href="{{ route('admin.pendapatan') }}" class="text-decoration-none stat-card-wrapper">
                <div class="card border-0 shadow-lg primary-gradient card-stat position-relative overflow-hidden h-100">
                    <div class="position-absolute end-0 bottom-0 p-3 opacity-10" style="transform: translate(10%, 20%);">
                        <i class="bi bi-wallet2" style="font-size: 8rem; color: #fff;"></i>
                    </div>

                    <div class="card-body p-4 position-relative" style="z-index: 2;">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 bg-white bg-opacity-25 p-3 rounded-4 shadow-sm glass-icon">
                                <i class="bi bi-currency-dollar text-white fs-2"></i>
                            </div>

                            <div class="flex-grow-1 ms-4">
                                <div class="text-white text-opacity-75 fw-medium small text-uppercase mb-1" style="letter-spacing: 0.5px;">
                                    Total Pendapatan
                                </div>
                                <div class="text-white fw-bold fs-3 mb-1">
                                    Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="d-flex align-items-center text-white text-opacity-75" style="font-size: 0.85rem;">
                                    <span class="badge bg-success bg-opacity-25 text-success me-2 px-2 py-1 border border-success border-opacity-25">
                                        <i class="bi bi-graph-up-arrow me-1"></i> +12%
                                    </span>
                                    Performa Transaksi
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Small Cards Container --}}
        <div class="col-12 col-xl-8">
            <div class="row g-4">
                {{-- Perlu Diproses --}}
                <div class="col-md-4">
                    <a href="{{ route('admin.proses') }}" class="text-decoration-none stat-card-wrapper">
                        <div class="card border-0 shadow-sm h-100 card-stat rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 mb-3">
                                    <i class="bi bi-box-seam fs-4"></i>
                                </div>
                                <p class="text-muted text-uppercase fw-bold mb-1 small">Perlu Diproses</p>
                                <h3 class="fw-extrabold mb-0">{{ $stats['pending_orders'] ?? 0 }}</h3>
                            </div>
                            <div class="progress rounded-0" style="height: 4px; background: transparent;">
                                <div class="progress-bar bg-warning" style="width: 100%"></div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Stok Menipis --}}
                <div class="col-md-4">
                    <a href="{{ route('admin.stokmenipis') }}" class="text-decoration-none stat-card-wrapper">
                        <div class="card border-0 shadow-sm h-100 card-stat rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-3 mb-3">
                                    <i class="bi bi-exclamation-triangle fs-4"></i>
                                </div>
                                <p class="text-muted text-uppercase fw-bold mb-1 small">Stok Menipis</p>
                                <h3 class="fw-extrabold mb-0 text-dark">{{ $stats['low_stock'] ?? 0 }}</h3>
                            </div>
                            <div class="progress rounded-0" style="height: 4px; background: transparent;">
                                <div class="progress-bar bg-danger" style="width: 100%"></div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- Total Produk --}}
                <div class="col-md-4">
                    <a href="{{ route('admin.totalproduk') }}" class="text-decoration-none stat-card-wrapper">
                        <div class="card border-0 shadow-sm h-100 card-stat rounded-4 overflow-hidden">
                            <div class="card-body p-4">
                                <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 mb-3">
                                    <i class="bi bi-tags fs-4"></i>
                                </div>
                                <p class="text-muted text-uppercase fw-bold mb-1 small">Total Produk</p>
                                <h3 class="fw-extrabold mb-0 text-dark">{{ $stats['products'] ?? 0 }}</h3>
                            </div>
                            <div class="progress rounded-0" style="height: 4px; background: transparent;">
                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- 3. Revenue Chart --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-0 px-4 mt-2">
                    <h5 class="card-title mb-0 fw-bold">Grafik Penjualan (7 Hari)</h5>
                    <i class="bi bi-graph-up text-primary"></i>
                </div>
                <div class="card-body px-4 pb-4">
                    <div style="height: 320px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Recent Orders --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100 rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center mt-2">
                    <h5 class="fw-bold mb-0">Pesanan Terbaru</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentOrders as $order)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-4 py-3 border-light hover-bg-light transition cursor-pointer">
                                <div>
                                    <div class="fw-bold text-dark">#{{ $order->order_number }}</div>
                                    <small class="text-muted">{{ Str::limit($order->user->name ?? 'Guest', 15) }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-dark small">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</div>
                                    <span class="badge {{ ($order->status ?? '') == 'completed' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill" style="font-size: 0.7rem;">
                                        {{ strtoupper($order->status ?? 'pending') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-center text-muted small fst-italic">Belum ada pesanan terbaru</div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-3 border-0">
                    <a href="{{ route('admin.orders.index') }}" class="text-decoration-none fw-bold small text-primary">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. Top Selling Products --}}
    <div class="card border-0 shadow-sm mb-5 rounded-4 overflow-hidden">
        <div class="card-header bg-white py-4 px-4 d-flex justify-content-between align-items-center border-0">
            <h5 class="card-title mb-0 fw-bold">💎 Produk Terlaris</h5>
            <span class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill">Trend Bulan Ini</span>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="row g-4">
                @forelse($topProducts as $product)
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <div class="text-center p-3 rounded-4 bg-light hover-shadow transition h-100 border border-transparent hover-border-primary">
                            <div class="position-relative mb-3">
                                <img src="{{ $product->image_url }}" class="rounded-3 shadow-sm w-100" style="height: 120px; object-fit: cover;">
                                <span class="position-absolute top-0 start-0 badge bg-dark bg-opacity-75 m-2">{{ $product->sold }} Terjual</span>
                            </div>
                            <h6 class="text-truncate fw-bold mb-1 small">{{ $product->name }}</h6>
                            <p class="text-primary fw-extrabold small mb-0">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-4 small">Belum ada produk terlaris</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- MODERN UI STYLES --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body { 
        font-family: 'Plus Jakarta Sans', sans-serif; 
        background-color: #f8fafc; 
        color: #334155;
    }

    .fw-extrabold { font-weight: 800; }
    
    /* Card Enhancements */
    .card-stat { 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
    }
    
    .stat-card-wrapper { 
        display: block; 
    }
    
    .stat-card-wrapper:hover { 
        transform: translateY(-5px); 
    }

    .primary-gradient { 
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); 
    }

    .glass-icon {
        backdrop-filter: blur(10px); 
        border: 1px solid rgba(255,255,255,0.3);
    }

    .icon-shape { 
        width: 48px; 
        height: 48px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }

    .bg-soft-primary { background-color: #eef2ff; }
    .transition { transition: all 0.3s ease; }
    
    /* Interactive States */
    .hover-bg-light:hover { background-color: #f1f5f9; cursor: pointer; }
    .hover-shadow:hover { 
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); 
        border-color: #4f46e5 !important;
    }
    
    .cursor-pointer { cursor: pointer; }
</style>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueChart->pluck('date') ?? []) !!},
                datasets: [{
                    label: 'Pendapatan',
                    data: {!! json_encode($revenueChart->pluck('total') ?? []) !!},
                    borderColor: '#4f46e5',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return 'Total: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: (value) => 'Rp ' + new Intl.NumberFormat('id-ID', { notation: "compact" }).format(value)
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection