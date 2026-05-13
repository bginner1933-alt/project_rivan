{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Daftar Pengguna')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="container-fluid py-4 luxury-admin animate__animated animate__fadeIn">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden blur-card">
        
        {{-- Header --}}
        <div class="card-header p-4 wedding-gradient text-center">
            <div class="animate__animated animate__zoomIn">
                <h4 class="mb-0 serif text-white italic" style="letter-spacing: 2px;">Daftar Anggota Eksklusif</h4>
                <div class="mx-auto my-2" style="width: 50px; height: 2px; background: var(--luxury-gold);"></div>
                <small class="text-white-50 italic">Koleksi identitas anggota dalam galeri berharga</small>
            </div>
        </div>

        <div class="card-body bg-white p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table mb-0">
                    <thead>
                        <tr>
                            <th width="90" class="text-center small-caps">ID</th>
                            <th width="120" class="text-center small-caps">Profil</th>
                            <th class="small-caps">Nama</th>
                            <th class="small-caps">Email</th>
                            <th class="small-caps text-center">Role</th>
                            <th width="200" class="text-end small-caps pe-5">Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($users as $index => $user)
                            <tr class="product-row animate__animated animate__fadeInUp"
                                style="animation-delay: {{ 0.1 + ($index * 0.05) }}s">

                                {{-- ID --}}
                                <td class="text-center">
                                    <span class="text-muted font-monospace small">
                                        #{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                {{-- Avatar --}}
                                <td class="text-center">
                                    <div class="image-container">
                                        <div class="no-image-placeholder shadow-sm rounded-circle border-2 border-white">
                                            <span class="serif fw-bold text-uppercase">
                                                {{ substr($user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nama --}}
                                <td>
                                    <div class="fw-bold text-navy h6 mb-0 serif">
                                        {{ $user->name }}
                                    </div>
                                </td>

                                {{-- Email --}}
                                <td>
                                    <span class="text-muted italic" style="font-size: 0.9rem;">
                                        {{ $user->email }}
                                    </span>
                                </td>

                                {{-- ROLE (BARU) --}}
                                <td class="text-center">
                                    @php
                                        $role = $user->role ?? 'user';

                                        $badge = 'bg-secondary';

                                        if ($role == 'admin') {
                                            $badge = 'bg-danger';
                                        } elseif ($role == 'seller') {
                                            $badge = 'bg-primary';
                                        } elseif ($role == 'user') {
                                            $badge = 'bg-success';
                                        }
                                    @endphp

                                    <span class="badge {{ $badge }} px-3 py-2 rounded-pill">
                                        {{ ucfirst($role) }}
                                    </span>
                                </td>

                                {{-- Tanggal --}}
                                <td class="text-end pe-5">
                                    <span class="price-tag px-4">
                                        {{ $user->created_at->format('d M Y') }}
                                    </span>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="text-center mt-4 animate__animated animate__fadeInUp" style="animation-delay: 0.8s">
        <p class="small-caps opacity-50 italic" style="font-size: 0.6rem;">
            Royal Inventory System • Bandung Edition
        </p>
    </div>
</div>

<style>
    :root {
        --royal-navy: #1e3a8a;
        --soft-blue: #f0f7ff;
        --navy-light: #e3f2fd;
    }

    .luxury-admin {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fbff;
        min-height: 100vh;
    }

    .serif { font-family: 'Playfair Display', serif; }
    .text-navy { color: var(--royal-navy); }
    .italic { font-style: italic; }

    .small-caps {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: var(--royal-navy);
    }

    .blur-card {
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.4);
    }

    .wedding-gradient {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
    }

    .custom-table thead th {
        background-color: var(--soft-blue);
        border: none;
        padding: 1rem;
    }

    .product-row {
        transition: .3s;
        border-bottom: 1px solid #f1f1f1;
    }

    .product-row:hover {
        background: var(--soft-blue);
        transform: translateX(4px);
    }

    .image-container {
        width: 45px;
        height: 45px;
        margin: auto;
    }

    .no-image-placeholder {
        width: 100%;
        height: 100%;
        display:flex;
        align-items:center;
        justify-content:center;
        background: var(--navy-light);
        border-radius: 50%;
        color:#1e3a8a;
        font-weight:700;
    }

    .price-tag {
        background:white;
        border:1px solid var(--navy-light);
        padding:6px 14px;
        border-radius:50px;
        font-weight:700;
    }
</style>

@endsection