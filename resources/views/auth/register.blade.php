@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">
            <div class="card border-0 shadow-lg overflow-hidden rounded-4 animate__animated animate__fadeInUp">
                <div class="row g-0">
                    
                    {{-- Sisi Kiri: Visual Eksklusif --}}
                    <div class="col-lg-5 d-none d-lg-flex bg-primary align-items-center p-5 position-relative overflow-hidden text-white">
                        {{-- Konten Utama dengan Animasi Delay --}}
                        <div class="position-relative animate__animated animate__fadeInLeft animate__delay-1s" style="z-index: 2;">
                            <h2 class="fw-bold mb-3">Selamat Datang!</h2>
                            <p class="opacity-75">Bergabunglah bersama ribuan pengguna lainnya dan nikmati kemudahan belanja Anda.</p>
                            <hr class="w-25 border-2 opacity-100 mb-4">
                            
                            <ul class="list-unstyled">
                                <li class="mb-3 d-flex align-items-center gap-3 animate__animated animate__fadeInLeft" style="animation-delay: 1.2s;">
                                    <div class="icon-box bg-white bg-opacity-25 rounded-circle p-2">
                                        <i class="bi bi-patch-check-fill text-info"></i>
                                    </div>
                                    <span>Proses Cepat & Mudah</span>
                                </li>
                                <li class="d-flex align-items-center gap-3 animate__animated animate__fadeInLeft" style="animation-delay: 1.4s;">
                                    <div class="icon-box bg-white bg-opacity-25 rounded-circle p-2">
                                        <i class="bi bi-shield-lock-fill text-info"></i>
                                    </div>
                                    <span>Data Aman Terlindungi</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Elemen Dekoratif: Lingkaran Cahaya --}}
                        <div class="light-blob position-absolute" style="top: -10%; left: -10%; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(50px);"></div>
                        
                        {{-- Ikon Orang Dekoratif (Tetap halus) --}}
                        <div class="position-absolute animate__animated animate__pulse animate__infinite animate__slow" style="bottom: -20px; right: -20px; z-index: 1; opacity: 0.1;">
                            <i class="bi bi-person-circle" style="font-size: 15rem; color: #fff;"></i>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Form Registrasi --}}
                    <div class="col-lg-7 bg-white p-4 p-md-5">
                        <div class="mb-4 animate__animated animate__fadeIn" style="animation-delay: 0.5s;">
                            <h3 class="fw-bold text-dark mb-1">Daftar Akun</h3>
                            <p class="text-muted small">Lengkapi data diri Anda untuk memulai perjalanan baru.</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="animate__animated animate__fadeIn" style="animation-delay: 0.7s;">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-secondary">Nama Lengkap</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-primary"></i></span>
                                    <input name="name" type="text" class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" placeholder="Masukkan nama" value="{{ old('name') }}" required>
                                </div>
                                @error('name') <small class="text-danger animate__animated animate__shakeX d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-secondary">Alamat Email</label>
                                <div class="input-group custom-input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-primary"></i></span>
                                    <input name="email" type="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" placeholder="contoh@email.com" value="{{ old('email') }}" required>
                                </div>
                                @error('email') <small class="text-danger animate__animated animate__shakeX d-block">{{ $message }}</small> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small text-secondary">Password</label>
                                    <div class="input-group custom-input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-primary"></i></span>
                                        <input name="password" type="password" class="form-control bg-light border-start-0 @error('password') is-invalid @enderror" placeholder="Minimal 8 k" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold small text-secondary">Konfirmasi</label>
                                    <div class="input-group custom-input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-shield-check text-primary"></i></span>
                                        <input name="password_confirmation" type="password" class="form-control bg-light border-start-0" placeholder="Ulangi pass" required>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm hover-grow">
                                    Daftar Sekarang <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </div>

                            <p class="text-center small text-muted">
                                Sudah punya akun? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none hover-link">Login di sini</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Background Page */
    body { 
        background-color: #f8faff; 
        background-image: radial-gradient(#d1d9e6 0.5px, transparent 0.5px);
        background-size: 20px 20px;
    }

    /* Animasi Gradient Sisi Kiri */
    .bg-primary { 
        background: linear-gradient(135deg, #0d6efd 0%, #004dc7 100%) !important; 
        position: relative;
    }

    /* Custom Input Group Focus */
    .custom-input-group .form-control:focus {
        background-color: #fff !important;
        border-color: #0d6efd;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.1);
    }
    
    .custom-input-group .input-group-text {
        border-color: #f1f1f1;
        transition: 0.3s;
    }

    /* Efek Tombol */
    .hover-grow {
        transition: all 0.3s ease;
    }
    .hover-grow:hover {
        transform: scale(1.02) translateY(-3px);
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2) !important;
    }

    /* Icon Box Glassmorphism */
    .icon-box {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(5px);
    }

    /* Link Hover */
    .hover-link:hover {
        text-decoration: underline !important;
        color: #004dc7 !important;
    }

    /* Responsif font */
    @media (max-width: 576px) {
        .card { border-radius: 0 !important; }
    }
</style>
@endsection