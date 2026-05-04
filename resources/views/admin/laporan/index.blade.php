@extends('layouts.admin')

@section('title', 'Laporan Pengaduan')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Data Laporan Pengaduan</h1>
            <p class="text-muted small mb-0">Kelola dan pantau pengaduan dari pelanggan dengan mudah.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 18%;">Nama Pelapor</th>
                            <th style="width: 12%;">Order ID</th>
                            <th style="width: 12%;">Kategori</th>
                            <th style="width: 20%;">Pesan</th>
                            <th style="width: 8%;">Lampiran</th>
                            <th style="width: 10%; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $index => $item)
                        <tr>
                            <td class="fw-bold text-dark">{{ $index + 1 }}</td>
                            <td>
                                <span class="text-muted small">
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->name }}</span>
                            </td>
                            <td>
                                @if($item->order_id)
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $item->order_id }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = 'bg-secondary';
                                    if ($item->category == 'rusak') {
                                        $badgeClass = 'bg-danger';
                                    } elseif ($item->category == 'pengiriman') {
                                        $badgeClass = 'bg-warning text-dark';
                                    } elseif ($item->category == 'layanan') {
                                        $badgeClass = 'bg-primary';
                                    } else {
                                        $badgeClass = 'bg-info text-dark';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1 text-white">
                                    {{ ucfirst($item->category) }}
                                </span>
                            </td>
                            <td>
                                <span class="d-inline-block text-truncate" style="max-width: 180px;" title="{{ $item->message }}">
                                    {{ $item->message }}
                                </span>
                            </td>
                            <td>
                                @if($item->attachment_path)
                                    <a href="{{ asset('storage/' . $item->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3 shadow-sm">
                                        <i class="fas fa-file-alt me-1"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted fst-italic small">Tidak ada</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.laporanpengaduan.show', $item->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-secondary opacity-50 mb-3"></i>
                                <h6 class="text-secondary mb-0">Tidak ada data laporan pengaduan.</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection