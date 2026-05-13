@extends('layouts.admin')

@section('title', 'Data Penilaian Pelanggan')

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

    :root {
        --primary-blue: #0d6efd;
        --secondary-blue: #6ea8fe;
        --dark-blue: #052c65;
        --danger-red: #dc3545;
        --bg-light: #f8faff;
        --gradient-main: linear-gradient(135deg, #0d6efd 0%, #003d92 100%);
        --gradient-danger: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
    }

    body {
        background-color: #f0f4f8;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .rating-wrapper {
        padding: 2.5rem 0;
        animation: slideUp 0.6s ease-out;
    }

    .header-card {
        background: var(--gradient-main);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: -40px;
        box-shadow: 0 10px 25px rgba(13, 110, 253, 0.2);
        position: relative;
        z-index: 2;
    }

    .table-card {
        background: white;
        border: none;
        border-radius: 25px;
        padding-top: 50px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--gradient-main);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        border: 2px solid white;
    }

    .custom-table thead th {
        background-color: #f8f9fa;
        padding: 1.25rem;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--dark-blue);
        border-bottom: 2px solid #edf2f7;
    }

    .custom-table tbody td {
        padding: 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f4f8;
    }

    .rating-badge {
        padding: 6px 12px;
        border-radius: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .badge-puas {
        background: #fff9e6;
        color: #ffb100;
    }

    .badge-kecewa {
        background: #fff5f5;
        color: #dc3545;
    }

    .comment-bubble {
        background: #f1f5f9;
        padding: 10px 15px;
        border-radius: 0 15px 15px 15px;
        font-size: 0.9rem;
        color: #475569;
        display: inline-block;
        max-width: 350px;
        word-break: break-word;
    }

    .stat-card {
        border: none;
        border-radius: 20px;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

</style>

<div class="container-fluid rating-wrapper">

    <div class="row justify-content-center">

        <div class="col-lg-11">

            {{-- HEADER --}}
            <div class="header-card d-flex justify-content-between align-items-center">

                <div>

                    <h2 class="fw-bold mb-1">
                        Manajemen Feedback
                    </h2>

                    <p class="mb-0 opacity-75">
                        <i class="fas fa-chart-line me-2"></i>
                        Analisis kepuasan pelanggan
                    </p>

                </div>

                <div class="stats-box d-none d-md-flex gap-4 text-end">

                    <div>

                        <small class="d-block opacity-75">
                            Total Ulasan
                        </small>

                        <span class="fs-4 fw-bold">
                            {{ $ratings->total() }}
                        </span>

                    </div>

                </div>

            </div>

            {{-- TABLE --}}
            <div class="card table-card">

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table custom-table">

                            <thead>

                                <tr>

                                    <th style="width:25%;">
                                        Pelanggan
                                    </th>

                                    <th style="width:20%;">
                                        Penilaian
                                    </th>

                                    <th style="width:35%;">
                                        Komentar
                                    </th>

                                    <th style="width:20%;">
                                        Waktu & Tanggal
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($ratings as $rating)

                                <tr>

                                    {{-- PELANGGAN --}}
                                    <td>

                                        <div class="d-flex align-items-center">

                                            <div class="avatar-placeholder me-3">

                                                {{ strtoupper(substr($rating->name ?? 'U', 0, 1)) }}

                                            </div>

                                            <div>

                                                <div class="fw-bold text-dark mb-0">

                                                    {{ $rating->name ?? 'User' }}

                                                </div>

                                                <small class="{{ $rating->star <= 2 ? 'text-danger fw-bold' : 'text-primary' }}">

                                                    {{ $rating->star <= 2 ? 'Butuh Perhatian' : 'Pembeli' }}

                                                </small>

                                            </div>

                                        </div>

                                    </td>

                                    {{-- STAR --}}
                                    <td>

                                        <div class="rating-badge shadow-sm {{ $rating->star <= 2 ? 'badge-kecewa' : 'badge-puas' }}">

                                            <i class="fas fa-star"></i>

                                            {{ $rating->star }}.0

                                        </div>

                                        <div class="mt-1 d-block">

                                            @for($i = 1; $i <= 5; $i++)

                                                <i class="fas fa-star {{ $i <= $rating->star ? 'text-warning' : 'text-light' }}"
                                                   style="font-size:0.7rem;">
                                                </i>

                                            @endfor

                                        </div>

                                    </td>

                                    {{-- KOMENTAR --}}
                                    <td>

                                        <div class="comment-bubble">

                                            @if(!empty($rating->message))

                                                {{ $rating->message }}

                                            @else

                                                <span class="text-muted">
                                                    Tidak ada komentar
                                                </span>

                                            @endif

                                        </div>

                                    </td>

                                    {{-- TANGGAL --}}
                                    <td>

                                        <div class="d-flex flex-column">

                                            <span class="text-dark fw-semibold">

                                                <i class="far fa-calendar-check me-2 text-primary"></i>

                                                {{ $rating->created_at->format('d M, Y') }}

                                            </span>

                                            <small class="text-muted mt-1">

                                                <i class="far fa-clock me-2"></i>

                                                {{ $rating->created_at->diffForHumans() }}

                                            </small>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="4" class="text-center py-5">

                                        <i class="fas fa-folder-open fa-3x text-light mb-3"></i>

                                        <h5 class="text-muted">
                                            Data ulasan belum tersedia
                                        </h5>

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    {{-- PAGINATION --}}
                    <div class="p-4 d-flex justify-content-between align-items-center">

                        <div class="text-muted small">

                            Menampilkan

                            <b>{{ $ratings->count() }}</b>

                            dari

                            <b>{{ $ratings->total() }}</b>

                            ulasan

                        </div>

                        <div>

                            {{ $ratings->links('pagination::bootstrap-5') }}

                        </div>

                    </div>

                </div>

            </div>

            {{-- STATISTIK --}}
            <div class="row mt-4 g-4">

                {{-- PUAS --}}
                <div class="col-md-4">

                    <div class="card stat-card bg-primary text-white p-3 shadow-sm">

                        <div class="d-flex align-items-center justify-content-between">

                            <div>

                                <h6 class="mb-0 opacity-75">
                                    Sangat Puas
                                </h6>

                                <h3 class="mb-0 fw-bold">

                                    @php
                                        $totalData = $ratings->total() > 0 ? $ratings->total() : 1;
                                        $puas = $ratings->where('star', '>=', 4)->count();
                                        $persenPuas = ($puas / $totalData) * 100;
                                    @endphp

                                    {{ number_format($persenPuas, 1) }}%

                                </h3>

                                <small>
                                    {{ $puas }} Pelanggan
                                </small>

                            </div>

                            <i class="fas fa-smile-beam fa-3x opacity-50"></i>

                        </div>

                    </div>

                </div>

                {{-- TIDAK PUAS --}}
                <div class="col-md-4">

                    <div class="card stat-card text-white p-3 shadow-sm"
                         style="background: var(--gradient-danger);">

                        <div class="d-flex align-items-center justify-content-between">

                            <div>

                                <h6 class="mb-0 opacity-75">
                                    Tidak Puas
                                </h6>

                                <h3 class="mb-0 fw-bold">

                                    @php
                                        $tidakPuas = $ratings->where('star', '<=', 2)->count();
                                        $persenTidakPuas = ($tidakPuas / $totalData) * 100;
                                    @endphp

                                    {{ number_format($persenTidakPuas, 1) }}%

                                </h3>

                                <small>
                                    {{ $tidakPuas }} Keluhan
                                </small>

                            </div>

                            <i class="fas fa-frown fa-3x opacity-50"></i>

                        </div>

                    </div>

                </div>

                {{-- RATA RATA --}}
                <div class="col-md-4">

                    <div class="card stat-card bg-white p-3 shadow-sm border">

                        <div class="d-flex align-items-center justify-content-between">

                            <div>

                                <h6 class="mb-0 text-muted">
                                    Rata-rata
                                </h6>

                                <h3 class="mb-0 fw-bold text-dark">

                                    {{ number_format($ratings->avg('star'), 1) }} / 5.0

                                </h3>

                            </div>

                            <i class="fas fa-chart-bar fa-3x text-light"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection