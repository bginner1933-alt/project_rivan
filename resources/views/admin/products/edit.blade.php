@extends('layouts.admin')

@section('page-title', 'Edit Produk: ' . $product->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{-- Alert Error --}}
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Kolom Kiri: Informasi Produk --}}
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Informasi Umum</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nama Produk</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $product->name) }}" placeholder="Contoh: Sepatu Lari Azure Premium">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Harga (Rp)</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                                   value="{{ old('price', $product->price) }}">
                                        </div>
                                        @error('price') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Kategori</label>
                                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                {{-- BAGIAN HARGA SEWA --}}
                                <div class="card shadow-sm mb-4 border-start border-info border-4 bg-light">
                                    <div class="card-header bg-transparent border-0 pb-0">
                                        <h6 class="mb-0 text-info fw-bold">
                                            <i class="bi bi-calendar-check-fill"></i> Pengaturan Harga Sewa
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Harga Sewa</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" name="rental_price"
                                                        class="form-control @error('rental_price') is-invalid @enderror"
                                                        value="{{ old('rental_price', $product->rental_price) }}">
                                                </div>
                                                @error('rental_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Satuan Sewa</label>
                                                <select name="rental_unit"
                                                    class="form-select @error('rental_unit') is-invalid @enderror">
                                                    <option value="">Pilih Satuan...</option>
                                                    <option value="hour" {{ old('rental_unit', $product->rental_unit) == 'hour' ? 'selected' : '' }}>Per Jam</option>
                                                    <option value="day" {{ old('rental_unit', $product->rental_unit) == 'day' ? 'selected' : '' }}>Per Hari</option>
                                                    <option value="week" {{ old('rental_unit', $product->rental_unit) == 'week' ? 'selected' : '' }}>Per Minggu</option>
                                                    <option value="month" {{ old('rental_unit', $product->rental_unit) == 'month' ? 'selected' : '' }}>Per Bulan</option>
                                                </select>
                                                @error('rental_unit') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <small class="text-muted">Kosongkan jika tidak disewakan.</small>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Deskripsi</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description', $product->description) }}</textarea>
                                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Manajemen Gambar --}}
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Gambar Produk</h5>
                                <small class="text-muted">Pilih satu sebagai gambar utama</small>
                            </div>
                            <div class="card-body">
                                <div class="row g-3 mb-4">
                                    @foreach($product->images as $image)
                                    <div class="col-6 col-md-3">
                                        <div class="card h-100 border position-relative">
                                            <img src="{{ asset('imgages /' . $image->image_path) }}" class="card-img-top p-2" style="height: 150px; object-fit: cover;">
                                            <div class="card-body p-2 bg-light border-top">
                                                <div class="form-check small mb-1">
                                                    <input class="form-check-input" type="radio" name="primary_image" 
                                                           id="primary_{{ $image->id }}" value="{{ $image->id }}" 
                                                           {{ $image->is_primary ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="primary_{{ $image->id }}">Utama</label>
                                                </div>
                                                <div class="form-check small text-danger">
                                                    <input class="form-check-input" type="checkbox" name="delete_images[]" 
                                                           id="delete_{{ $image->id }}" value="{{ $image->id }}">
                                                    <label class="form-check-label fw-bold" for="delete_{{ $image->id }}">Hapus</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Ganti/Tambah Gambar Baru</label>
                                    <input type="file" name="images[]" class="form-control @error('images') is-invalid @enderror" multiple accept="image/*">
                                    <small class="text-muted">Bisa pilih lebih dari satu gambar sekaligus.</small>
                                    @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kolom Kanan: Status & Simpan --}}
                    <div class="col-lg-4">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Publikasi & Visibilitas</h5>
                            </div>
                            <div class="card-body">
                                {{-- Switch Status Aktif & Unggulan --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold d-block">Opsi Produk</label>
                                    
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" 
                                            {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">Aktifkan Produk</label>
                                    </div>

                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" 
                                            {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-bold text-primary" for="is_featured">Produk Unggulan ★</label>
                                    </div>
                                </div>

                                <hr>

                                {{-- Status Stok --}}
                                <div class="mb-3">
                                    <label class="form-label d-block fw-bold">Status Stok</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stock_status" value="1" {{ old('stock_status', $product->stock_status) == 1 ? 'checked' : '' }}>
                                        <label class="form-check-label">Tersedia</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="stock_status" value="0" {{ old('stock_status', $product->stock_status) == 0 ? 'checked' : '' }}>
                                        <label class="form-check-label">Habis</label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jumlah Stok</label>
                                    <div class="input-group">
                                        <input type="number" name="stock" 
                                            class="form-control @error('stock') is-invalid @enderror"
                                            value="{{ old('stock', $product->stock) }}" 
                                            min="0" placeholder="0">
                                        <span class="input-group-text">Unit</span>
                                    </div>
                                    @error('stock') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>

                                <hr class="my-4">
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                                </div>
                            </div>
                            <div class="card-footer bg-light py-3">
                                <small class="text-muted d-block">
                                    <i class="bi bi-clock-history"></i> Dibuat: {{ $product->created_at->format('d/m/Y H:i') }}
                                </small>
                                <small class="text-muted d-block">
                                    <i class="bi bi-pencil-square"></i> Update: {{ $product->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Styling agar input radio dan checkbox terlihat lebih jelas */
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    /* Warna merah khusus untuk checkbox hapus */
    .form-check-input[name="delete_images[]"]:checked {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .form-switch .form-check-input:checked {
        background-color: #198754; /* Hijau untuk status aktif */
        border-color: #198754;
    }
    .form-switch .form-check-input[name="is_featured"]:checked {
        background-color: #ffc107; /* Kuning untuk unggulan */
        border-color: #ffc107;
    }
    .card {
        border: none;
        border-radius: 10px;
    }
</style>
@endsection