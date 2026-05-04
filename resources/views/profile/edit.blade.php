@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            {{-- Header Section --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    {{-- Teks judul putih agar kontras dengan background biru --}}
                    <h2 class="fw-bold mb-0 text-white" style="letter-spacing: -0.5px;">Profil Saya</h2>
                    <p class="text-white text-opacity-75 mb-0" style="font-size: 0.9rem;">Kelola informasi akun dan keamanan</p>
                </div>
                <a href="{{ route('home') }}" class="btn btn-light border-0 px-4 shadow-sm" style="border-radius: 8px; font-weight: 600; font-size: 0.9rem; color: #0d6efd;">
                    Beranda
                </a>
            </div>

            {{-- Alert Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-white alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: white; color: #0d6efd;">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 1. Avatar Information --}}
            <div class="card mb-4 border-0 shadow-lg" style="border-radius: 15px;">
                <div class="card-header bg-white fw-bold py-3" style="border-bottom: 1px solid #f0f0f0 !important; border-radius: 15px 15px 0 0;">
                    <i class="bi bi-person-circle me-2 text-primary"></i>Foto Profil
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-avatar-form')
                </div>
            </div>

            {{-- 2. Profile Information --}}
            <div class="card mb-4 border-0 shadow-lg" style="border-radius: 15px;">
                <div class="card-header bg-white fw-bold py-3" style="border-bottom: 1px solid #f0f0f0 !important; border-radius: 15px 15px 0 0;">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Profil
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 3. Update Password --}}
            <div class="card mb-4 border-0 shadow-lg" style="border-radius: 15px;">
                <div class="card-header bg-white fw-bold py-3" style="border-bottom: 1px solid #f0f0f0 !important; border-radius: 15px 15px 0 0;">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>Keamanan Password
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 4. Delete Account --}}
            <div class="card border-0 shadow-lg mb-5" style="border-radius: 15px;">
                <div class="card-header bg-white text-danger fw-bold py-3" style="border-bottom: 1px solid #f0f0f0 !important; border-radius: 15px 15px 0 0;">
                    <i class="bi bi-exclamation-triangle me-2"></i>Zona Bahaya
                </div>
                <div class="card-body p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Biru Tema */
    body {
        /* Menggunakan Gradient Biru agar tidak terlihat flat/polos */
        background: linear-gradient(135deg, #0d6efd 0%, #4facfe 100%);
        background-attachment: fixed; /* Background tetap saat scroll */
        min-height: 100vh;
        color: #262626;
    }

    /* Card Styling */
    .card {
        background-color: rgba(255, 255, 255, 0.95); /* Sedikit transparan agar menyatu */
        backdrop-filter: blur(10px); /* Efek kaca halus */
    }

    /* Tombol Utama */
    .btn-primary {
        background-color: #0095f6 !important;
        border: none !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        box-shadow: 0 4px 10px rgba(0, 149, 246, 0.3);
    }

    .btn-primary:hover {
        background-color: #1877f2 !important;
        transform: translateY(-1px);
    }

    /* Input Fields */
    .form-control {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
    }

    .form-control:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
    }

    label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #444;@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<style>
    :root {
        --azure-main: #3b82f6;
        --azure-gradient: linear-gradient(135deg, #47a0ff 0%, #3b82f6 100%);
    }

    body {
        background: var(--azure-gradient);
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* ✨ HEADER STYLING */
    .profile-header-text h2 {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -1.5px;
        text-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    /* 💎 GLASS CARD STYLING */
    .profile-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 2rem;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .card-header-luxury {
        background: transparent;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 25px 30px 15px;
        font-weight: 800;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 1.5px;
        color: var(--azure-main);
    }

    .card-body-luxury {
        padding: 30px;
    }

    /* 🚀 BUTTONS & INPUTS */
    .btn-back-home {
        background: white;
        color: var(--azure-main);
        border-radius: 50px;
        font-weight: 700;
        padding: 10px 25px;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-back-home:hover {
        background: #0f172a;
        color: white;
        transform: scale(1.05);
    }

    /* Minimalist Alert */
    .alert-custom {
        background: white;
        border-radius: 16px;
        border-left: 5px solid #10b981;
        color: #064e3b;
        font-weight: 600;
    }

    /* Custom Form Control for Profile */
    .form-control {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 15px !important;
        background: #f8fafc !important;
    }

    .form-control:focus {
        background: white !important;
        border-color: var(--azure-main) !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }

    .text-danger-luxury {
        color: #ef4444;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-end mb-5 animate__animated animate__fadeInDown">
                <div class="profile-header-text">
                    <span class="text-white text-uppercase fw-bold opacity-75 small letter-spacing-3">Account Settings</span>
                    <h2 class="text-white mt-1 mb-0">Profil Saya</h2>
                </div>
                <a href="{{ route('home') }}" class="btn-back-home shadow-lg">
                    <i class="bi bi-house-door-fill me-2"></i>Beranda
                </a>
            </div>

            {{-- NOTIFICATION --}}
            @if (session('success'))
                <div class="alert alert-custom alert-dismissible fade show shadow-sm mb-4 p-3 animate__animated animate__bounceIn" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 1. AVATAR SECTION --}}
            <div class="profile-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-header-luxury">
                    <i class="bi bi-camera me-2"></i> Foto Profil
                </div>
                <div class="card-body-luxury">
                    @include('profile.partials.update-avatar-form')
                </div>
            </div>

            {{-- 2. PROFILE INFORMATION --}}
            <div class="profile-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-header-luxury">
                    <i class="bi bi-person-vcard me-2"></i> Informasi Personal
                </div>
                <div class="card-body-luxury">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- 3. SECURITY SECTION --}}
            <div class="profile-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="card-header-luxury">
                    <i class="bi bi-shield-lock me-2"></i> Keamanan Akun
                </div>
                <div class="card-body-luxury">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 4. DANGER ZONE --}}
            <div class="profile-card border-danger animate__animated animate__fadeInUp" style="animation-delay: 0.4s; border-left: 4px solid #ef4444;">
                <div class="card-header-luxury text-danger-luxury">
                    <i class="bi bi-exclamation-octagon me-2"></i> Danger Zone
                </div>
                <div class="card-body-luxury">
                    <p class="small text-muted mb-4">Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.</p>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
    }
</style>
@endsection