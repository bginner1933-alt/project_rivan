@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeIn">
                
                {{-- Header Visual --}}
                <div class="bg-primary p-4 text-center text-white position-relative">
                    <div class="position-relative" style="z-index: 2;">
                        <div class="display-4 mb-3">
                            <i class="bi bi-envelope-check animate__animated animate__tada animate__infinite animate__slow d-inline-block"></i>
                        </div>
                        <h4 class="fw-bold mb-0">Verifikasi Email Anda</h4>
                    </div>
                    {{-- Dekorasi --}}
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10">
                        <i class="bi bi-envelope-fill" style="font-size: 10rem; position: absolute; bottom: -20px; right: -20px;"></i>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 text-center">
                    @if (session('resent'))
                        <div class="alert alert-success border-0 shadow-sm animate__animated animate__flipInX" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        {{ __('Sebelum melanjutkan, silakan periksa email Anda untuk menemukan link verifikasi.') }}
                        <br>
                        {{ __('Jangan lupa periksa folder spam jika tidak menemukannya di kotak masuk.') }}
                    </p>

                    <div class="bg-light p-4 rounded-4 mb-4 border">
                        <span class="text-secondary small d-block mb-2">Tidak menerima email?</span>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold hover-grow">
                                {{ __('Kirim Ulang Link Verifikasi') }} 
                                <i class="bi bi-send-fill ms-2"></i>
                            </button>
                        </form>
                    </div>

                    <a href="{{ route('home') }}" class="text-decoration-none small fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>

            <p class="text-center mt-4 text-muted small">
                Butuh bantuan? <a href="#" class="text-primary text-decoration-none">Hubungi Support</a>
            </p>
        </div>
    </div>
</div>

<style>
    body { 
        background-color: #f8faff;
        background-image: radial-gradient(#d1d9e6 0.5px, transparent 0.5px);
        background-size: 20px 20px;
    }

    .bg-primary { 
        background: linear-gradient(135deg, #0d6efd 0%, #004dc7 100%) !important; 
    }

    .card { border: none; }

    .hover-grow {
        transition: all 0.3s ease;
    }

    .hover-grow:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2) !important;
    }

    .bi-envelope-check {
        filter: drop-shadow(0 5px 15px rgba(0,0,0,0.2));
    }

    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
    }
</style>
@endsection