@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-9">
            <div class="card border-0 shadow-lg overflow-hidden rounded-4 animate__animated animate__zoomIn">
                <div class="row g-0">
                    
                    {{-- Sisi Kiri: Visual --}}
                    <div class="col-lg-5 d-none d-lg-flex bg-primary align-items-center p-5 position-relative overflow-hidden text-white">
                        <div class="position-relative animate__animated animate__fadeInLeft animate__delay-1s" style="z-index: 2;">
                            <h2 class="fw-bold mb-3">Selamat Datang Kembali!</h2>
                            <p class="opacity-75">Masuk untuk mengelola akun Anda dan melihat update terbaru dari kami.</p>
                            <hr class="w-25 border-2 opacity-100 mb-4">
                            
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="icon-box bg-white bg-opacity-25 rounded-circle p-2">
                                    <i class="bi bi-shield-check text-info"></i>
                                </div>
                                <span class="small">Keamanan akun prioritas kami.</span>
                            </div>
                        </div>
                        <div class="light-blob position-absolute" style="bottom: -10%; left: -10%; width: 250px; height: 250px; background: rgba(255,255,255,0.1); border-radius: 50%; filter: blur(40px);"></div>
                        <div class="position-absolute animate__animated animate__pulse animate__infinite animate__slow" style="top: -20px; right: -20px; z-index: 1; opacity: 0.1;">
                            <i class="bi bi-lock-fill" style="font-size: 15rem; color: #fff;"></i>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Form Login --}}
                    <div class="col-lg-7 bg-white p-4 p-md-5">
                        <div class="mb-4 animate__animated animate__fadeInDown" style="animation-delay: 0.5s;">
                            <h3 class="fw-bold text-dark mb-1">🔐 Login Akun</h3>
                            <p class="text-muted small">Silakan masukkan email dan password Anda.</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="animate__animated animate__fadeIn" style="animation-delay: 0.8s;">
                            @csrf

                            {{-- Field Email --}}
                            <div class="mb-3">
                           {{-- Ganti bagian @error email kamu dengan ini agar pesan error tampil lebih rapi --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold small text-secondary">Email</label>
                            <div class="input-group custom-input-group">
                                <span class="input-group-text bg-light border-end-0 text-primary">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input id="email" type="email" class="form-control bg-light border-start-0 @error('email') is-invalid @enderror" 
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="nama@email.com">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block mt-2 animate__animated animate__fadeIn">
                                    <small class="text-danger fw-bold">{!! $message !!}</small>
                                </div>
                            @enderror
                        </div>

                        {{-- Untuk Password, hapus @error('email') yang nyelip di bawah password dan ganti dengan @error('password') --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <label for="password" class="form-label fw-bold small text-secondary">Password</label>
                                @if (Route::has('password.request'))
                                    <a class="text-decoration-none small fw-bold text-primary" href="{{ route('password.request') }}">Lupa Password?</a>
                                @endif
                            </div>
                            <div class="input-group custom-input-group position-relative">
                                <span class="input-group-text bg-light border-end-0 text-primary">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input id="password" type="password" class="form-control bg-light border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="current-password" placeholder="minimal 8 karakter" style="padding-right: 45px;">
                                <span class="input-group-text bg-light border-start-0 cursor-pointer" id="togglePassword" style="cursor: pointer;">
                                    <i class="bi bi-eye-slash text-muted" id="eyeIcon"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block mt-2">
                                    <small class="text-danger">{{ $message }}</small>
                                </div>
                            @enderror
                        </div>  

                            {{-- Remember Me --}}
                            <div class="mb-4 form-check">
                                <input class="form-check-input custom-checkbox" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small text-muted" for="remember">
                                    Ingat saya di perangkat ini
                                </label>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm hover-grow">
                                    Masuk Sekarang <i class="bi bi-box-arrow-in-right ms-2"></i>
                                </button>
                            </div>

                            <div class="position-relative my-4">
                                <hr class="text-muted opacity-25">
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 small text-muted text-uppercase">Atau masuk dengan</span>
                            </div>

                            <div class="d-grid gap-2 mb-4">
                                <a href="{{ route('auth.google') }}" class="btn btn-outline-light border text-dark rounded-pill fw-semibold shadow-sm hover-grow-sm">
                                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" width="18" class="me-2">
                                    Google
                                </a>
                            </div>

                            <p class="text-center small text-muted mb-0">
                                Belum punya akun? 
                                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none hover-link">Daftar Gratis</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT JAVASCRIPT UNTUK FITUR MATA --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            // Toggle tipe input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle ikon mata
            eyeIcon.classList.toggle('bi-eye');
            eyeIcon.classList.toggle('bi-eye-slash');
        });
    let attempts = 0;
    const loginForm = document.querySelector('form');

    loginForm.addEventListener('submit', function(e) {
        // Cek apakah ada pesan error dari Laravel
        const errorMsg = document.querySelector('.invalid-feedback');
        if (errorMsg) {
            attempts++;
        }

        if (attempts >= 3) {
            alert('Coba klik "Lupa Password" atau daftar akun baru Anda');
            attempts = 0; // Reset counter setelah alert
        }
    });

    });
    
</script>

<style>
    /* Background Pattern */
    body { 
        background-color: #f8faff;
        background-image: radial-gradient(#d1d9e6 0.5px, transparent 0.5px);
        background-size: 20px 20px;
    }

    /* Card & Primary Colors */
    .bg-primary { background: linear-gradient(135deg, #0d6efd 0%, #004dc7 100%) !important; }
    .card { border: none; }

    /* Input Focus State */
    .custom-input-group .form-control:focus {
        background-color: #fff !important;
        border-color: #0d6efd;
        box-shadow: 0 0 10px rgba(13, 110, 253, 0.1);
    }

    /* Hover Animations */
    .hover-grow { transition: all 0.3s ease; }
    .hover-grow:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(13, 110, 253, 0.2) !important;
    }

    /* Link Style */
    .hover-link:hover { text-decoration: underline !important; }

    /* Error Message Styling */
    .invalid-feedback {
        font-size: 0.85rem;
        margin-top: 0.25rem;
    }
    .invalid-feedback a {
        color: #dc3545;
        text-decoration: underline;
        font-weight: bold;
    }
    .invalid-feedback a:hover {
        color: #b02a37;
    }
</style>
@endsection