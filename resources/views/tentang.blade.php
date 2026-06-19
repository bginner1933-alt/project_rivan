@extends('layouts.app')

@section('title', 'Tentang Kami')

@section('content')
<style>
    :root {
        --primary-blue: #3b82f6;
        --deep-blue: #1e40af;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    body { 
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .about-container {
        margin-top: 60px;
        margin-bottom: 60px;
    }

    .about-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 50px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .feature-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-top: 5px solid var(--primary-blue);
        height: 100%;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--deep-blue);
    }
</style>

<div class="container about-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="about-card">
                
                {{-- Header --}}
                <div class="text-center mb-5">
                    <h1 class="fw-bold text-dark display-5 mb-3">Tentang Wangi 🤩</h1>
                    <p class="text-muted fs-5">Menghadirkan pengalaman belanja dan sewa barang berkualitas dengan teknologi modern dan terpercaya sejak 2024.</p>
                </div>

                <hr class="my-5 border-secondary opacity-25">

                {{-- Visi & Misi --}}
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="fs-1 text-primary mb-3"><i class="bi bi-eye-fill"></i></div>
                            <h3 class="fw-bold text-dark mb-3">Visi Kami</h3>
                            <p class="text-secondary mb-0">Menjadi platform e-commerce dan penyewaan terdepan yang memberikan kemudahan, kecepatan, dan kenyamanan bagi masyarakat dalam memenuhi kebutuhan sehari-hari maupun proyek tertentu.</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="feature-card">
                            <div class="fs-1 text-primary mb-3"><i class="bi bi-rocket-takeoff-fill"></i></div>
                            <h3 class="fw-bold text-dark mb-3">Misi Kami</h3>
                            <ul class="text-secondary ps-3 mb-0">
                                <li class="mb-2">Menyediakan produk berkualitas dengan harga yang kompetitif.</li>
                                <li class="mb-2">Memberikan kemudahan dalam sistem sewa dan beli barang secara fleksibel.</li>
                                <li class="mb-2">Mengembangkan teknologi layanan pelanggan yang responsif dan aman.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Mengapa Memilih Kami --}}
                <div class="mb-5">
                    <h2 class="fw-bold text-dark text-center mb-4">Mengapa Memilih Kami?</h2>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="feature-card text-center">
                                <div class="fs-1 text-primary mb-3"><i class="bi bi-shield-check"></i></div>
                                <h5 class="fw-bold text-dark">Keamanan Transaksi</h5>
                                <p class="text-muted small mb-0">Didukung oleh sistem pembayaran yang terenkripsi dan aman untuk berbagai metode.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card text-center">
                                <div class="fs-1 text-primary mb-3"><i class="bi bi-lightning-charge"></i></div>
                                <h5 class="fw-bold text-dark">Layanan Cepat</h5>
                                <p class="text-muted small mb-0">Proses pemesanan dan pengiriman yang efisien agar barang sampai tepat waktu.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="feature-card text-center">
                                <div class="fs-1 text-primary mb-3"><i class="bi bi-patch-check"></i></div>
                                <h5 class="fw-bold text-dark">Kualitas Terjamin</h5>
                                <p class="text-muted small mb-0">Setiap barang yang disewa maupun dijual melalui tahap kontrol kualitas yang ketat.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection