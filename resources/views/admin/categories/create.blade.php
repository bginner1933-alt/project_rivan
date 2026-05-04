@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Breadcrumb & Header --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Kategori</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Kategori Baru
                    </h5>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><small>{{ $error }}</small></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Nama Kategori</label>
                            <input type="text" name="name" id="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name') }}" 
                                placeholder="Masukkan nama kategori (ex: Elektronik)" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" id="description" rows="3" 
                                class="form-control" 
                                placeholder="Tulis deskripsi singkat...">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label fw-semibold">Foto Kategori</label>
                            <div class="input-group">
                                <input type="file" name="image" id="image" class="form-control" accept="image/*">
                            </div>
                            <div class="form-text">Maksimal ukuran gambar 1MB (JPG, PNG, WebP)</div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-medium" for="is_active">Status Kategori Aktif</label>
                            </div>
                        </div>

                        <hr class="my-4 text-muted">

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">
                                <i class="fas fa-save me-1"></i> Simpan Data Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tambahkan Style CSS Tambahan agar tidak polos --}}
<style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 12px; }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
    }
    .btn-primary {
        background: linear-gradient(45deg, #0d6efd, #004fb0);
        border: none;
    }
</style>
@endsection