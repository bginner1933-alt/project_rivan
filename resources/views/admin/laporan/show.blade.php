@extends('layouts.admin')

@section('title', 'Detail Laporan Pengaduan')

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

    .detail-header{
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        border-radius:24px;
        padding:32px;
        color:white;
        box-shadow:0 15px 40px rgba(37,99,235,.2);
    }

    .detail-header h1{
        font-weight:800;
        letter-spacing:-1px;
    }

    .btn-back{
        background:white;
        color:#2563eb;
        border:none;
        border-radius:14px;
        padding:12px 20px;
        font-weight:700;
        transition:.3s;
    }

    .btn-back:hover{
        background:#1e3a8a;
        color:white;
        transform:translateY(-2px);
    }

    .detail-card{
        border:none;
        border-radius:24px;
        overflow:hidden;
        background:white;
        box-shadow:0 10px 35px rgba(0,0,0,.05);
    }

    .detail-card-header{
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        padding:24px 30px;
        color:white;
    }

    .detail-card-header h5{
        font-weight:700;
        margin:0;
    }

    .detail-card-body{
        padding:35px;
    }

    .info-row{
        padding:20px 0;
        border-bottom:1px solid #eef2ff;
    }

    .info-row:last-child{
        border-bottom:none;
    }

    .label-title{
        color:#64748b;
        font-weight:600;
        font-size:.9rem;
    }

    .content-value{
        color:#0f172a;
        font-weight:600;
    }

    .message-box{
        background:#f8fbff;
        border:1px solid #dbeafe;
        border-radius:18px;
        padding:22px;
        line-height:1.8;
        color:#334155;
    }

    .badge-category{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:10px 16px;
        border-radius:50px;
        font-size:.8rem;
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

    .attachment-box{
        background:#eff6ff;
        border:1px dashed #93c5fd;
        border-radius:18px;
        padding:20px;
    }

    .btn-download{
        background:linear-gradient(135deg,#2563eb,#3b82f6);
        border:none;
        color:white;
        border-radius:14px;
        padding:12px 18px;
        font-weight:700;
        transition:.3s;
        text-decoration:none;
        display:inline-block;
    }

    .btn-download:hover{
        transform:translateY(-2px);
        color:white;
        box-shadow:0 10px 20px rgba(37,99,235,.2);
    }

    .info-icon{
        width:42px;
        height:42px;
        border-radius:14px;
        background:#eff6ff;
        color:#2563eb;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:1rem;
    }
</style>

@php

    $badgeClass = 'badge-lainnya';

    if($laporan->category == 'rusak'){
        $badgeClass = 'badge-rusak';
    }elseif($laporan->category == 'pengiriman'){
        $badgeClass = 'badge-pengiriman';
    }elseif($laporan->category == 'layanan'){
        $badgeClass = 'badge-layanan';
    }

@endphp

<div class="container-fluid px-4 py-4">

    {{-- HEADER --}}
    <div class="detail-header mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

            <div>

                <span class="text-white opacity-75 small text-uppercase fw-bold">
                    Admin Panel
                </span>

                <h1 class="mb-2">
                    Detail Laporan Pengaduan
                </h1>

                <p class="mb-0 opacity-75">
                    Informasi lengkap laporan pelanggan dan detail pengaduan.
                </p>

            </div>

            <a href="{{ route('admin.laporanpengaduan.index') }}"
               class="btn-back">

                <i class="fas fa-arrow-left me-2"></i>
                Kembali

            </a>

        </div>

    </div>

    {{-- CARD --}}
    <div class="detail-card">

        {{-- HEADER CARD --}}
        <div class="detail-card-header">

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h5>
                        Laporan Pengaduan #{{ $laporan->id }}
                    </h5>

                    <small class="opacity-75">
                        Dibuat pada
                        {{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y H:i') }}
                    </small>
                </div>

                <div>

                    <span class="badge-category {{ $badgeClass }}">

                        <i class="fas fa-tag"></i>

                        {{ ucfirst($laporan->category) }}

                    </span>

                </div>

            </div>

        </div>

        {{-- BODY --}}
        <div class="detail-card-body">

            {{-- NAMA --}}
            <div class="row info-row align-items-center">

                <div class="col-md-3">

                    <div class="d-flex align-items-center gap-3">

                        <div class="info-icon">
                            <i class="fas fa-user"></i>
                        </div>

                        <div class="label-title">
                            Nama Pelapor
                        </div>

                    </div>

                </div>

                <div class="col-md-9">

                    <div class="content-value">
                        {{ $laporan->name }}
                    </div>

                </div>

            </div>

            {{-- ORDER --}}
            <div class="row info-row align-items-center">

                <div class="col-md-3">

                    <div class="d-flex align-items-center gap-3">

                        <div class="info-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>

                        <div class="label-title">
                            Order ID
                        </div>

                    </div>

                </div>

                <div class="col-md-9">

                    <div class="content-value">

                        @if($laporan->order_id)

                            #{{ $laporan->order_id }}

                        @else

                            <span class="text-muted">
                                Tidak ada
                            </span>

                        @endif

                    </div>

                </div>

            </div>

            {{-- TANGGAL --}}
            <div class="row info-row align-items-center">

                <div class="col-md-3">

                    <div class="d-flex align-items-center gap-3">

                        <div class="info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>

                        <div class="label-title">
                            Tanggal Laporan
                        </div>

                    </div>

                </div>

                <div class="col-md-9">

                    <div class="content-value">

                        {{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y H:i') }} WIB

                    </div>

                </div>

            </div>

            {{-- PESAN --}}
            <div class="row info-row">

                <div class="col-md-3">

                    <div class="d-flex align-items-center gap-3">

                        <div class="info-icon">
                            <i class="fas fa-comment-dots"></i>
                        </div>

                        <div class="label-title">
                            Isi Pengaduan
                        </div>

                    </div>

                </div>

                <div class="col-md-9">

                    <div class="message-box">

                        {{ $laporan->message }}

                    </div>

                </div>

            </div>

            {{-- LAMPIRAN --}}
            <div class="row info-row">

                <div class="col-md-3">

                    <div class="d-flex align-items-center gap-3">

                        <div class="info-icon">
                            <i class="fas fa-paperclip"></i>
                        </div>

                        <div class="label-title">
                            Lampiran Bukti
                        </div>

                    </div>

                </div>

                <div class="col-md-9">

                    <div class="attachment-box">

                        @if($laporan->attachment_path)

                            <a href="{{ asset('storage/' . $laporan->attachment_path) }}"
                               target="_blank"
                               class="btn-download">

                                <i class="fas fa-download me-2"></i>
                                Lihat / Download Lampiran

                            </a>

                        @else

                            <span class="text-muted">
                                Tidak ada lampiran yang disertakan.
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection