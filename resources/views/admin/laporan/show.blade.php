@extends('layouts.admin')

@section('title', 'Detail Laporan Pengaduan')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Detail Laporan Pengaduan</h1>
        <a href="{{ route('admin.laporanpengaduan.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informasi Laporan Pengaduan #{{ $laporan->id }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3 text-muted">Nama Pelapor</div>
                <div class="col-md-9 fw-bold">{{ $laporan->name }}</div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-md-3 text-muted">Order ID</div>
                <div class="col-md-9">{{ $laporan->order_id ?? '-' }}</div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-md-3 text-muted">Kategori</div>
                <div class="col-md-9">
                    <span class="badge bg-info text-dark">{{ $laporan->category }}</span>
                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-md-3 text-muted">Tanggal Laporan</div>
                <div class="col-md-9">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y H:i') }}</div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-md-3 text-muted">Isi Pesan</div>
                <div class="col-md-9">
                    <p class="p-3 bg-light rounded shadow-sm">{{ $laporan->message }}</p>
                </div>
            </div>
            <hr>
            <div class="row mb-3">
                <div class="col-md-3 text-muted">Lampiran / Bukti</div>
                <div class="col-md-9">
                    @if($laporan->attachment_path)
                        <a href="{{ asset('storage/' . $laporan->attachment_path) }}" target="_blank" class="btn btn-info btn-sm text-white shadow-sm">
                            <i class="fas fa-file-download"></i> Unduh / Lihat Lampiran
                        </a>
                    @else
                        <span class="text-muted">Tidak ada lampiran yang disertakan.</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection