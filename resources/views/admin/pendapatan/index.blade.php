        @extends('layouts.admin')

        @section('title', 'Laporan Pendapatan')

        @section('content')
        <div class="container-fluid px-4">
            <div class="row animate__animated animate__fadeIn">
                <div class="col-12">
                    
                    {{-- Summary Header --}}
                    <div class="card border-0 shadow-sm rounded-4 bg-primary text-white mb-4 overflow-hidden position-relative">
                        <div class="card-body p-4 position-relative" style="z-index: 2;">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="text-white-50 mb-1">Total Pendapatan Terakumulasi</h5>
                                    <h2 class="display-5 fw-bold mb-0">
            Rp {{ number_format($totalRevenue, 0, ',', '.') }}                            </h2>
                                    <p class="mb-0 mt-2 text-white-50 small">
                                        <i class="bi bi-info-circle me-1"></i> Data diambil dari pesanan dengan status <strong>Delivered</strong>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end d-none d-md-block">
                                    <i class="bi bi-cash-stack display-1 opacity-25"></i>
                                </div>
                            </div>
                        </div>
                        {{-- Dekorasi Abstrak --}}
                        <div class="position-absolute top-0 end-0 translate-middle-y mt-5 me-5 opacity-10">
                            <i class="bi bi-currency-dollar" style="font-size: 15rem;"></i>
                        </div>
                    </div>

                    {{-- Table Card --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                            <h5 class="fw-bold mb-0 text-dark">Riwayat Transaksi Selesai</h5>
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="window.print()">
                                <i class="bi bi-file-earmark-excel me-1"></i> Export Laporan
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-secondary">
                                        <tr>
                                            <th class="ps-4 py-3 small fw-bold">NO. INVOICE</th>
                                            <th class="py-3 small fw-bold">PELANGGAN</th>
                                            <th class="py-3 small fw-bold text-center">TANGGAL SELESAI</th>
                                            <th class="py-3 small fw-bold text-center">METODE</th>
                                            <th class="py-3 small fw-bold text-end pe-4">JUMLAH PENDAPATAN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders as $order)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-bold text-dark">#{{ $order->order_number }}</div>
                                                    <span class="badge bg-success bg-opacity-10 text-success small" style="font-size: 0.65rem;">
                                                        <i class="bi bi-check-all me-1"></i> DELIVERED
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 35px; height: 35px;">
                                                            {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-dark small">{{ $order->user->name ?? 'Guest' }}</div>
                                                            <div class="text-muted" style="font-size: 0.75rem;">{{ $order->user->email ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="text-muted small">
                                                        {{ $order->updated_at->format('d/m/Y') }}<br>
                                                        <small class="opacity-75">{{ $order->updated_at->format('H:i') }} WIB</small>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge rounded-pill border text-secondary small fw-normal px-3">
                                                        {{ $order->payment_method ?? 'Transfer' }}
                                                    </span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <span class="fw-bold text-success">
                                                        + Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <img src="https://illustrations.popsy.co/amber/no-data.svg" alt="No data" style="width: 150px;" class="mb-3 opacity-50">
                                                    <p class="text-muted">Belum ada pendapatan yang masuk.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <p class="text-center text-muted mt-4 small">
                        Menampilkan {{ $orders->count() }} transaksi terakhir yang berhasil diselesaikan.
                    </p>
                </div>
            </div>
        </div>

        <style>
            .bg-primary {
                background: linear-gradient(135deg, #4e73df 0%, #224abe 100%) !important;
            }

            .table thead th {
                letter-spacing: 0.05rem;
                border-bottom: 1px solid #f0f0f0;
            }

            .table tbody tr {
                transition: all 0.2s ease;
            }

            .table tbody tr:hover {
                background-color: #fcfdfe;
            }

            .avatar-sm {
                box-shadow: inset 0 0 5px rgba(0,0,0,0.05);
            }

            @media print {
                .bg-primary { background: #4e73df !important; -webkit-print-color-adjust: exact; }
                .btn, .nav, .card-header button { display: none !important; }
            }
        </style>
        @endsection