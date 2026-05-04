@extends('layouts.admin')

@section('title', 'Manajemen Koleksi Kategori')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="container-fluid py-4 luxury-theme animate__animated animate__fadeIn">
    <div class="row">
        <div class="col-lg-12">
            
            {{-- Alert Notifikasi --}}
            @if(session('success'))
                <div class="alert alert-wedding-success alert-dismissible fade show animate__animated animate__fadeInUp shadow" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-stars me-2 fs-4 text-gold"></i>
                        <div><strong>Berhasil!</strong> {{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate__animated animate__shakeX shadow" role="alert">
                    <i class="bi bi-exclamation-octagon me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-lg border-0 rounded-4 overflow-hidden blur-card">
                {{-- Header --}}
                <div class="card-header p-4 d-flex justify-content-between align-items-center wedding-gradient">
                    <div class="header-text">
                        <h4 class="mb-0 serif text-white">Katalog Kategori</h4>
                        <small class="text-white-50 italic">Kelola arsip pengelompokan produk mahakarya Anda</small>
                    </div>
                    <div class="header-actions d-flex gap-2">
                        {{-- Tombol Hapus Terpilih (Muncul via JS) --}}
                        <button type="button" id="bulkDeleteBtn" class="btn btn-glass-danger btn-sm rounded-pill px-3" style="display:none;">
                            <i class="bi bi-trash-fill me-1"></i> Hapus Terpilih
                        </button>

                        {{-- Tombol Tambah --}}
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-gold-shimmer btn-sm rounded-pill px-4">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Baru
                        </a>

                        {{-- Tombol Kosongkan (Dipindah ke dropdown/kecil agar tidak salah klik) --}}
                        <form action="{{ route('admin.categories.deleteAll') }}" method="POST" class="d-inline" onsubmit="return confirm('PERHATIAN: Hapus SELURUH data kategori?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline-light btn-sm rounded-pill opacity-50" title="Kosongkan Semua">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body p-0 bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 custom-wedding-table">
                            <thead>
                                <tr>
                                    <th width="60" class="text-center">
                                        <input type="checkbox" id="selectAll" class="form-check-input custom-check">
                                    </th>
                                    <th>Informasi Kategori</th>
                                    <th class="text-center">Total Produk</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-5">Manajemen</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $index => $category)
                                    <tr class="category-row animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.05 }}s">
                                        <td class="text-center">
                                            <input type="checkbox" name="selected[]" value="{{ $category->id }}" class="category-checkbox form-check-input custom-check">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="img-wrapper me-3">
                                                    @if($category->image)
                                                        <img src="{{ Storage::url($category->image) }}" class="rounded-3 shadow-sm object-fit-cover" width="50" height="50">
                                                    @else
                                                        <div class="no-img-placeholder rounded-3">
                                                            <i class="bi bi-tag"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-navy h6 mb-0">{{ $category->name }}</div>
                                                    <small class="text-gold font-monospace">{{ $category->slug }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="product-count-badge">{{ $category->products_count }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($category->is_active)
                                                <span class="status-pill active"><span class="dot"></span> Aktif</span>
                                            @else
                                                <span class="status-pill inactive"><span class="dot"></span> Draft</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-5">
                                            <div class="action-btns d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-action edit" data-bs-toggle="tooltip" title="Ubah Detail">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>

                                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action delete" onclick="return confirm('Hapus kategori ini?')" data-bs-toggle="tooltip" title="Hapus">
                                                        <i class="bi bi-eraser-fill"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="animate__animated animate__pulse animate__infinite">
                                                <i class="bi bi-inbox display-1 text-light"></i>
                                                <p class="text-muted italic mt-3">Belum ada kategori ditemukan.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 py-4 d-flex justify-content-center">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Tersembunyi untuk Bulk Delete --}}
<form id="bulkDeleteForm" action="{{ route('admin.categories.bulkDelete') }}" method="POST" style="display: none;">
    @csrf @method('DELETE')
    <div id="bulkDeleteInputs"></div>
</form>

<style>
    /* ... (Style tetap sama seperti kode Anda sebelumnya) ... */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,600&family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');
    :root { --royal-navy: #0d6efd; --deep-azure: #0d6efd; --luxury-gold: #c5a059; --soft-blue: #f0f7ff; }
    .luxury-theme { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fbff; min-height: 100vh; }
    .serif { font-family: 'Playfair Display', serif; }
    .italic { font-style: italic; }
    .text-navy { color: var(--royal-navy); }
    .text-gold { color: var(--luxury-gold); }
    .blur-card { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.4); }
    .wedding-gradient { background: linear-gradient(135deg, var(--royal-navy) 0%, var(--deep-azure) 100%); border-bottom: 3px solid var(--luxury-gold); }
    .btn-gold-shimmer { background: linear-gradient(145deg, #c5a059, #af8b46); color: white; border: none; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.3s ease; }
    .btn-gold-shimmer:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(197, 160, 89, 0.4); color: white; }
    .btn-glass-danger { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(5px); color: #ffbaba; border: 1px solid rgba(255,255,255,0.2); }
    .btn-glass-danger:hover { background: #dc3545; color: white; }
    .custom-wedding-table thead th { background-color: var(--soft-blue); color: var(--royal-navy); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; padding: 1.2rem 1rem; border: none; }
    .category-row { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .category-row:hover { background-color: var(--soft-blue) !important; transform: translateX(10px); }
    .status-pill { padding: 6px 16px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; text-transform: uppercase; }
    .status-pill.active { background: #e6fcf5; color: #099268; border: 1px solid #c3fae8; }
    .status-pill.inactive { background: #f1f3f5; color: #495057; border: 1px solid #e9ecef; }
    .status-pill .dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
    .img-wrapper img { transition: 0.5s ease; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .no-img-placeholder { width: 50px; height: 50px; background: var(--soft-blue); color: var(--luxury-gold); display: flex; align-items: center; justify-content: center; }
    .product-count-badge { background: white; border: 2px solid var(--soft-blue); padding: 5px 15px; border-radius: 12px; font-weight: 800; color: var(--royal-navy); }
    .btn-action { width: 38px; height: 38px; border-radius: 10px; border: none; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; text-decoration: none; }
    .btn-action.edit { background: #e7f5ff; color: #1971c2; }
    .btn-action.delete { background: #fff5f5; color: #fa5252; }
    .alert-wedding-success { background: var(--royal-navy); color: white; border-left: 6px solid var(--luxury-gold); border-radius: 12px; }
    .custom-check:checked { background-color: var(--luxury-gold); border-color: var(--luxury-gold); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.category-checkbox');
        const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        const bulkDeleteInputs = document.getElementById('bulkDeleteInputs');

        // Fungsi kontrol tombol Bulk Delete
        function toggleBulkBtn() {
            const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkDeleteBtn.style.display = 'block';
                bulkDeleteBtn.innerHTML = `<i class="bi bi-trash-fill me-1"></i> Hapus (${checkedCount})`;
            } else {
                bulkDeleteBtn.style.display = 'none';
            }
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    cb.closest('tr').classList.toggle('table-primary', this.checked);
                });
                toggleBulkBtn();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                this.closest('tr').classList.toggle('table-primary', this.checked);
                toggleBulkBtn();
            });
        });

        // Eksekusi Bulk Delete
        bulkDeleteBtn.addEventListener('click', function() {
            if(confirm('Hapus kategori yang dipilih?')) {
                bulkDeleteInputs.innerHTML = '';
                document.querySelectorAll('.category-checkbox:checked').forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected[]';
                    input.value = cb.value;
                    bulkDeleteInputs.appendChild(input);
                });
                bulkDeleteForm.submit();
            }
        });

        // Tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) });
    });
</script>
@endsection