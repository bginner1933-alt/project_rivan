@extends('layouts.admin')

@section('content')

<style>
:root{
    --blue:#2563eb;
    --blue-dark:#1e40af;
    --soft:#f4f8ff;
}

body{
    background: var(--soft);
}

/* CARD */
.admin-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(37,99,235,0.08);
}

/* HEADER */
.card-header{
    background: linear-gradient(135deg,var(--blue),var(--blue-dark));
    color:#fff;
    padding:18px 22px;
    border:none;
}

/* FORM STYLE */
.form-control{
    border-radius:12px;
    padding:10px 14px;
    border:1px solid #e5e7eb;
    transition:.2s;
}

.form-control:focus{
    border-color:var(--blue);
    box-shadow:0 0 0 3px rgba(37,99,235,0.15);
}

/* LABEL */
.form-label{
    font-weight:600;
    color:#1e3a8a;
    margin-bottom:6px;
}

/* IMAGE PREVIEW */
.img-preview{
    border-radius:14px;
    border:2px solid #e0ecff;
    padding:4px;
    background:#fff;
}

/* SWITCH */
.form-switch .form-check-input{
    width:42px;
    height:22px;
    cursor:pointer;
}

.form-switch .form-check-input:checked{
    background-color:var(--blue);
    border-color:var(--blue);
}

/* BUTTON */
.btn-primary{
    background:linear-gradient(135deg,var(--blue),var(--blue-dark));
    border:none;
    border-radius:12px;
    padding:10px 18px;
    transition:.2s;
}

.btn-primary:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(37,99,235,0.25);
}

.btn-light{
    border-radius:12px;
    background:#fff;
    border:1px solid #e5e7eb;
}

/* ANIMATION */
.fade-in{
    animation:fadeIn .4s ease;
}

@keyframes fadeIn{
    from{opacity:0;transform:translateY(10px);}
    to{opacity:1;transform:translateY(0);}
}

</style>


<div class="container-fluid py-4 fade-in">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <div class="card admin-card">

                {{-- HEADER --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold">Edit Kategori</h5>
                        <small class="opacity-75">Perbarui data kategori produk</small>
                    </div>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light btn-sm">
                        ← Kembali
                    </a>
                </div>

                {{-- BODY --}}
                <div class="card-body p-4 bg-white">

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- NAME --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="name"
                                   value="{{ old('name', $category->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- DESC --}}
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="4"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- IMAGE --}}
                        <div class="mb-3">
                            <label class="form-label d-block">Gambar Kategori</label>

                            @if($category->image)
                                <img src="{{ asset('storage/' . $category->image) }}"
                                     class="img-preview mb-2"
                                     width="140">
                            @endif

                            <input type="file" name="image" class="form-control">
                            <small class="text-muted">PNG, JPG maksimal 1MB</small>
                        </div>

                        {{-- STATUS --}}
                        <div class="form-check form-switch mb-4">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold">Kategori Aktif</label>
                        </div>

                        <hr>

                        {{-- BUTTON --}}
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                💾 Simpan Perubahan
                            </button>
                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection