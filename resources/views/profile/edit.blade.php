@extends('layouts.app')

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

    .profile-header-text h2 {
        font-size: 2.5rem;
        font-weight: 800;
        letter-spacing: -1.5px;
    }

    .profile-card {
        background: rgba(255,255,255,0.92);
        backdrop-filter: blur(15px);
        border-radius: 24px;
        margin-bottom: 2rem;
        border: none;
        overflow: hidden;
        box-shadow: 0 10px 35px rgba(0,0,0,0.08);
    }

    .card-header-luxury {
        padding: 22px 30px;
        font-weight: 700;
        font-size: .9rem;
        letter-spacing: 1px;
        color: var(--azure-main);
        border-bottom: 1px solid #f1f1f1;
        background: white;
    }

    .card-body-luxury {
        padding: 30px;
    }

    .btn-back-home {
        background: white;
        color: var(--azure-main);
        border-radius: 50px;
        padding: 10px 24px;
        font-weight: 700;
        text-decoration: none;
        transition: .3s;
    }

    .btn-back-home:hover {
        background: #111827;
        color: white;
        transform: translateY(-2px);
    }

    .form-control {
        border-radius: 12px !important;
        padding: 12px 15px !important;
    }

    .form-control:focus {
        border-color: var(--azure-main) !important;
        box-shadow: 0 0 0 4px rgba(59,130,246,.15) !important;
    }

    .alert-custom {
        background: white;
        border-radius: 14px;
        border-left: 4px solid #10b981;
    }
</style>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-end mb-5">

                <div class="profile-header-text">
                    <span class="text-white opacity-75 small fw-bold">
                        ACCOUNT SETTINGS
                    </span>

                    <h2 class="text-white mb-0">
                        Profil Saya
                    </h2>
                </div>

                <a href="{{ route('home') }}" class="btn-back-home">
                    <i class="bi bi-house-door-fill me-2"></i>
                    Beranda
                </a>

            </div>

            {{-- ALERT --}}
            @if(session('success'))

                <div class="alert alert-custom alert-dismissible fade show shadow-sm mb-4">

                    <i class="bi bi-check-circle-fill text-success me-2"></i>

                    {{ session('success') }}

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="alert">
                    </button>

                </div>

            @endif

            {{-- FOTO --}}
            <div class="profile-card">

                <div class="card-header-luxury">
                    <i class="bi bi-camera me-2"></i>
                    Foto Profil
                </div>

                <div class="card-body-luxury">
                    @include('profile.partials.update-avatar-form')
                </div>

            </div>

            {{-- INFO --}}
            <div class="profile-card">

                <div class="card-header-luxury">
                    <i class="bi bi-person-vcard me-2"></i>
                    Informasi Profil
                </div>

                <div class="card-body-luxury">
                    @include('profile.partials.update-profile-information-form')
                </div>

            </div>

            {{-- PASSWORD --}}
            <div class="profile-card">

                <div class="card-header-luxury">
                    <i class="bi bi-shield-lock me-2"></i>
                    Keamanan Password
                </div>

                <div class="card-body-luxury">
                    @include('profile.partials.update-password-form')
                </div>

            </div>

            {{-- DELETE --}}
            <div class="profile-card border-start border-4 border-danger">

                <div class="card-header-luxury text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Danger Zone
                </div>

                <div class="card-body-luxury">
                    @include('profile.partials.delete-user-form')
                </div>

            </div>

        </div>

    </div>

</div>

@endsection