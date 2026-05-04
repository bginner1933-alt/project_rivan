{{-- ================================================
     FILE: resources/views/partials/flash-messages.blade.php
     ================================================ --}}

<style>
    .flash-alert {
        border: none;
        border-radius: 14px;
        padding: 14px 18px;
        font-size: 0.92rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        animation: slideDown 0.4s ease;
    }

    .flash-icon {
        font-size: 1.2rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #e8fff3, #d1fae5);
        color: #065f46;
    }

    .alert-danger {
        background: linear-gradient(135deg, #ffe8e8, #fee2e2);
        color: #991b1b;
    }

    .alert-info {
        background: linear-gradient(135deg, #e8f3ff, #dbeafe);
        color: #1e3a8a;
    }

    .btn-close {
        opacity: 0.5;
    }

    .btn-close:hover {
        opacity: 1;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-10px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .flash-wrapper {
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 9999;
        width: 320px;
    }
</style>

<div class="flash-wrapper">

    {{-- Success --}}
    @if(session('success'))
        <div class="alert flash-alert alert-success alert-dismissible fade show mb-2" role="alert">
            <i class="bi bi-check-circle-fill flash-icon me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Error --}}
    @if(session('error'))
        <div class="alert flash-alert alert-danger alert-dismissible fade show mb-2" role="alert">
            <i class="bi bi-x-circle-fill flash-icon me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Info --}}
    @if(session('info'))
        <div class="alert flash-alert alert-info alert-dismissible fade show mb-2" role="alert">
            <i class="bi bi-info-circle-fill flash-icon me-2"></i>
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validation --}}
    @if($errors->any())
        <div class="alert flash-alert alert-danger alert-dismissible fade show mb-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill flash-icon me-2"></i>
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

</div>