@extends('layouts.app')

@section('title', 'Selamat Datang')

@section('content')
<style>
    :root {
        --primary-blue: #3b82f6;
        --deep-blue: #1e40af;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    .hero-section {
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        padding: 100px 0;
        color: #ffffff;
        border-bottom-left-radius: 48px;
        border-bottom-right-radius: 48px;
        margin-top: -24px; /* Menyesuaikan jika ada navbar */
        box-shadow: 0 10px 30px rgba(30, 64, 175, 0.2);
    }

    .feature-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 32px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 5px solid var(--primary-blue);
    }

    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
    }
</style>

<div class="hero-section text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3 animate__animated animate__fadeInDown">Sewa & Beli Produk Pernikahan</h1>
        <p class="lead mb-4 opacity-75">Temukan berbagai barang berkualitas untuk dibeli atau disewa dengan mudah dan harga terbaik.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-light btn-lg rounded-pill px-4 fw-bold text-primary shadow-sm">
                <i class="bi bi-cart-check me-2"></i> Lihat Produk
            </a>
            
            {{-- Tombol Login atau Dashboard berdasarkan status login --}}
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </a>
            @endguest
        </div>
    </div>
</div>

{{-- Section Fitur / Keunggulan --}}
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-card h-100 text-center">
                <div class="fs-1 text-primary mb-3">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h5 class="fw-bold mb-3">Produk Berkualitas</h5>
                <p class="text-muted">Pilih dari berbagai perlengkapan dan produk pernikahan pilihan dengan kualitas terbaik.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="feature-card h-100 text-center">
                <div class="fs-1 text-primary mb-3">
                    <i class="bi bi-arrow-repeat"></i>
                </div>
                <h5 class="fw-bold mb-3">Sewa atau Beli</h5>
                <p class="text-muted">Fleksibilitas penuh untuk menyewa keperluan pesta maupun membelinya sesuai kebutuhan Anda.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="feature-card h-100 text-center">
                <div class="fs-1 text-primary mb-3">
                    <i class="bi bi-tags"></i>
                </div>
                <h5 class="fw-bold mb-3">Harga Terbaik</h5>
                <p class="text-muted">Dapatkan penawaran harga paling kompetitif untuk semua jenis kebutuhan pernikahan Anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection