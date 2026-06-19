{{-- resources/views/admin/products/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tambah Produk')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h3 mb-0 text-gray-800">Tambah Produk Baru</h2>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                {{-- Kolom Kiri: Informasi Utama --}}
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Informasi Produk</h5>
                            
                            {{-- Nama Produk --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nama Produk</label>
                                <input type="text" name="name" 
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="Masukkan nama produk...">
                                @error('name') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Deskripsi Produk</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="5">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                {{-- Kategori --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Kategori</label>
                                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                                        <option value="">Pilih Kategori...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Berat --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Berat (Gram)</label>
                                    <div class="input-group">
                                        <input type="number" name="weight" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight', 1) }}" min="1">
                                        <span class="input-group-text">gr</span>
                                    </div>
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN HARGA JUAL --}}
                    <div class="card shadow-sm border-0 border-start border-primary border-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="text-primary mb-3"><i class="bi bi-tag-fill"></i> Pengaturan Harga Jual</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Harga Asli</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}">                      
                                    </div>
                                    @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Harga Diskon (Opsional)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="discount_price" id="discount_price" class="form-control @error('discount_price') is-invalid @enderror" value="{{ old('discount_price') }}">
                                    </div>
                                    <small id="discount-info" class="text-muted mt-1 d-block">Kosongkan jika tidak ada diskon.</small>
                                    @error('discount_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div id="discount-preview" style="display: none;">
                                <div class="alert alert-success py-2 mt-2 mb-0">
                                    <small>Hemat: <span id="discount-amount" class="fw-bold"></span> (<span id="discount-percent"></span>)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN HARGA SEWA --}}
                    <div class="card shadow-sm border-0 border-start border-info border-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="text-info mb-3"><i class="bi bi-calendar-check-fill"></i> Pengaturan Harga Sewa</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Harga Sewa</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="rental_price" id="rental_price" class="form-control @error('rental_price') is-invalid @enderror" value="{{ old('rental_price') }}">
                                    </div>
                                    @error('rental_price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Durasi Sewa</label>
                                    <div class="input-group">
                                        <input type="number" name="rental_duration" id="rental_duration" class="form-control @error('rental_duration') is-invalid @enderror" value="{{ old('rental_duration', 1) }}" min="1">
                                        <span class="input-group-text">Hari</span>
                                    </div>
                                    @error('rental_duration') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <small class="text-muted">Isi bagian ini hanya jika produk tersedia untuk disewa.</small>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Media & Status --}}
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body p-4">
                            <h5 class="mb-3">Media & Status</h5>
                            
                            {{-- Stok --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Stok Produk</label>
                                <div class="input-group">
                                    <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 1) }}" min="0">
                                    <span class="input-group-text">Pcs</span>
                                </div>
                                @error('stock') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Gambar --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold">Gambar Produk</label>
                                <input type="file" id="imageInput" name="images[]" multiple class="form-control @error('images') is-invalid @enderror">
                                <small class="text-muted d-block mt-1">Bisa pilih lebih dari 1 gambar.</small>
                                @error('images') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                                {{-- PREVIEW SLIDER --}}
                                <div id="imagePreviewContainer" class="mt-3" style="display:none;">
                                    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner" id="carouselInner"></div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon"></span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                                            <span class="carousel-control-next-icon"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            {{-- Status --}}
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Aktifkan Produk</label>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">Produk Unggulan</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-save"></i> Simpan Produk
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const priceInput = document.getElementById('price');
    const discountPriceInput = document.getElementById('discount_price');
    const discountPreview = document.getElementById('discount-preview');
    const discountAmount = document.getElementById('discount-amount');
    const discountPercent = document.getElementById('discount-percent');
    const discountInfo = document.getElementById('discount-info');

    // =========================
    // DISCOUNT PREVIEW LOGIC
    // =========================
    function updateDiscountPreview() {
        const price = parseFloat(priceInput.value) || 0;
        const discountPrice = parseFloat(discountPriceInput.value) || 0;

        if (discountPrice > 0 && discountPrice < price) {
            const savings = price - discountPrice;
            const percent = Math.round((savings / price) * 100);

            discountAmount.textContent = 'Rp ' + savings.toLocaleString('id-ID');
            discountPercent.textContent = percent + '%';
            discountPreview.style.display = 'block';

            discountInfo.textContent = 'Harga diskon valid.';
            discountInfo.className = 'text-success fw-bold small mt-1 d-block';

        } else if (discountPrice >= price && discountPrice > 0) {
            discountPreview.style.display = 'none';
            discountInfo.textContent = 'Harga diskon harus lebih rendah dari harga asli!';
            discountInfo.className = 'text-danger fw-bold small mt-1 d-block';

        } else {
            discountPreview.style.display = 'none';
            discountInfo.textContent = 'Kosongkan jika tidak ada diskon.';
            discountInfo.className = 'text-muted small mt-1 d-block';
        }
    }

    priceInput.addEventListener('input', updateDiscountPreview);
    discountPriceInput.addEventListener('input', updateDiscountPreview);
    updateDiscountPreview();
});

// =========================
// IMAGE PREVIEW SLIDER
// =========================
const imageInput = document.getElementById('imageInput');
const carouselInner = document.getElementById('carouselInner');
const imagePreviewContainer = document.getElementById('imagePreviewContainer');

imageInput.addEventListener('change', function () {
    const files = this.files;
    carouselInner.innerHTML = '';

    if (files.length === 0) {
        imagePreviewContainer.style.display = 'none';
        return;
    }

    imagePreviewContainer.style.display = 'block';

    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const div = document.createElement('div');
            div.classList.add('carousel-item');
            if (index === 0) div.classList.add('active');

            div.innerHTML = `
                <img src="${e.target.result}" 
                     class="d-block w-100 rounded"
                     style="max-height:300px; object-fit:cover;">
            `;
            carouselInner.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection