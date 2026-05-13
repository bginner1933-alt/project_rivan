@extends('layouts.admin')

@section('title', 'Laporan Pengaduan')

@section('content')

<style>
    :root{
        --primary-blue:#2563eb;
        --soft-blue:#eff6ff;
        --dark-blue:#1e3a8a;
        --border-blue:#dbeafe;
    }

    body{
        background:#f4f8ff;
    }

    .page-header{
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        border-radius:24px;
        padding:32px;
        color:white;
        box-shadow:0 15px 40px rgba(37,99,235,.2);
    }

    .page-header h1{
        font-weight:800;
        letter-spacing:-1px;
    }

    .modern-card{
        border:none;
        border-radius:24px;
        overflow:hidden;
        background:white;
        box-shadow:0 10px 35px rgba(0,0,0,.05);
    }

    .modern-card .card-header{
        background:white;
        border-bottom:1px solid #eef2ff;
        padding:22px 28px;
    }

    .table-modern{
        margin-bottom:0;
    }

    .table-modern thead{
        background:#f8fbff;
    }

    .table-modern thead th{
        border:none;
        color:#64748b;
        font-size:.75rem;
        text-transform:uppercase;
        letter-spacing:.5px;
        font-weight:700;
        padding:18px 16px;
        white-space:nowrap;
    }

    .table-modern tbody tr{
        transition:.2s ease;
    }

    .table-modern tbody tr:hover{
        background:#f8fbff;
    }

    .table-modern tbody td{
        padding:18px 16px;
        border-color:#f1f5f9;
        vertical-align:middle;
    }

    .badge-custom{
        border-radius:50px;
        padding:8px 14px;
        font-size:.75rem;
        font-weight:700;
    }

    .badge-rusak{
        background:#fee2e2;
        color:#dc2626;
    }

    .badge-pengiriman{
        background:#fef3c7;
        color:#d97706;
    }

    .badge-layanan{
        background:#dbeafe;
        color:#2563eb;
    }

    .badge-lainnya{
        background:#cffafe;
        color:#0891b2;
    }

    .order-badge{
        background:#eff6ff;
        color:#2563eb;
        border:1px solid #bfdbfe;
        padding:7px 12px;
        border-radius:12px;
        font-size:.75rem;
        font-weight:700;
    }

    .btn-view{
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        border:none;
        color:white;
        border-radius:12px;
        padding:9px 16px;
        font-size:.8rem;
        font-weight:600;
        transition:.3s;
    }

    .btn-view:hover{
        transform:translateY(-2px);
        color:white;
        box-shadow:0 10px 20px rgba(37,99,235,.2);
    }

    .btn-file{
        border-radius:12px;
        padding:8px 14px;
        font-size:.8rem;
        font-weight:600;
        border:1px solid #bfdbfe;
        color:#2563eb;
        background:#eff6ff;
        transition:.3s;
    }

    .btn-file:hover{
        background:#2563eb;
        color:white;
    }

    .empty-state{
        padding:70px 20px;
    }

    .empty-icon{
        width:90px;
        height:90px;
        display:flex;
        align-items:center;
        justify-content:center;
        margin:auto;
        border-radius:50%;
        background:#eff6ff;
        color:#2563eb;
        font-size:2rem;
    }

    .text-message{
        max-width:220px;
    }

    .table-responsive{
        border-radius:20px;
    }
</style>

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="page-header mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>
                <h1 class="mb-2">
                    Data Laporan Pengaduan
                </h1>

                <p class="mb-0 opacity-75">
                    Kelola dan pantau seluruh laporan pelanggan dengan tampilan modern.
                </p>
            </div>

            <div class="text-end">
                <div class="bg-white bg-opacity-10 rounded-4 px-4 py-3">
                    <small class="d-block opacity-75">
                        Total Pengaduan
                    </small>

                    <h3 class="fw-bold mb-0">
                        {{ count($laporan) }}
                    </h3>
                </div>
            </div>

        </div>

    </div>

    {{-- CARD --}}
    <div class="modern-card">

        <div class="card-header d-flex justify-content-between align-items-center">

            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    Daftar Pengaduan
                </h5>

                <small class="text-muted">
                    Seluruh laporan terbaru pelanggan
                </small>
            </div>

            <div>
                <span class="badge bg-primary rounded-pill px-3 py-2">
                    Admin Panel
                </span>
            </div>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-modern align-middle">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tanggal</th>
                            <th>Nama Pelapor</th>
                            <th>Order ID</th>
                            <th>Kategori</th>
                            <th>Pesan</th>
                            <th>Lampiran</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($laporan as $index => $item)

                            @php
                                $badgeClass = 'badge-lainnya';

                                if ($item->category == 'rusak') {
                                    $badgeClass = 'badge-rusak';
                                } elseif ($item->category == 'pengiriman') {
                                    $badgeClass = 'badge-pengiriman';
                                } elseif ($item->category == 'layanan') {
                                    $badgeClass = 'badge-layanan';
                                }
                            @endphp

                            <tr>

                                {{-- NOMOR --}}
                                <td class="fw-bold text-primary">
                                    #{{ $index + 1 }}
                                </td>

                                {{-- TANGGAL --}}
                                <td>
                                    <div class="fw-semibold text-dark small">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                                    </div>

                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('H:i') }} WIB
                                    </small>
                                </td>

                                {{-- NAMA --}}
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $item->name }}
                                    </div>
                                </td>

                                {{-- ORDER --}}
                                <td>

                                    @if($item->order_id)

                                        <span class="order-badge">
                                            #{{ $item->order_id }}
                                        </span>

                                    @else

                                        <span class="text-muted small">
                                            Tidak ada
                                        </span>

                                    @endif

                                </td>

                                {{-- KATEGORI --}}
                                <td>

                                    <span class="badge-custom {{ $badgeClass }}">
                                        {{ ucfirst($item->category) }}
                                    </span>

                                </td>

                                {{-- PESAN --}}
                                <td>

                                    <div class="text-message text-truncate"
                                         title="{{ $item->message }}">

                                        {{ $item->message }}

                                    </div>

                                </td>

                                {{-- LAMPIRAN --}}
                                <td>

                                    @if($item->attachment_path)

                                        <a href="{{ asset('storage/' . $item->attachment_path) }}"
                                           target="_blank"
                                           class="btn btn-file">

                                            <i class="fas fa-paperclip me-1"></i>
                                            Lihat

                                        </a>

                                    @else

                                        <span class="text-muted small fst-italic">
                                            Tidak ada
                                        </span>

                                    @endif

                                </td>

                                {{-- AKSI --}}
                                <td class="text-center">

                                    <a href="{{ route('admin.laporanpengaduan.show', $item->id) }}"
                                       class="btn btn-view">

                                        <i class="fas fa-eye me-1"></i>
                                        Detail

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8">

                                    <div class="empty-state text-center">

                                        <div class="empty-icon mb-4">
                                            <i class="fas fa-inbox"></i>
                                        </div>

                                        <h5 class="fw-bold text-dark">
                                            Belum Ada Pengaduan
                                        </h5>

                                        <p class="text-muted mb-0">
                                            Semua laporan pelanggan akan muncul di sini secara otomatis.
                                        </p>

                                    </div>

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