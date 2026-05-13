@extends('layouts.admin')

@section('page-title', 'Manajemen Koleksi Produk')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="container-fluid py-4 luxury-admin animate__animated animate__fadeIn">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden blur-card">
        
        {{-- Header --}}
        <div class="card-header p-4 d-flex justify-content-between align-items-center bg-primary bg-gradient border-bottom border-info">
            <div class="animate__animated animate__slideInLeft">
                <h4 class="mb-0 fw-bold text-white"><i class="bi bi-box-seam me-2"></i>Daftar Produk</h4>
                <small class="text-white-50">Kelola inventaris eksklusif dan katalog berharga Anda</small>
            </div>
            <div class="animate__animated animate__slideInRight d-flex gap-2">
                <a href="{{ route('home') }}" class="btn btn-info text-white rounded-pill px-4 shadow-sm fw-bold">
                    <i class="bi bi-house-door me-1"></i> Beranda
                </a>
                <a href="{{ route('admin.products.create') }}" class="btn btn-info text-white rounded-pill px-4 shadow-sm fw-bold">
                    <i class="fas fa-plus me-1"></i> Tambah Koleksi
                </a>
            </div>
        </div>

        <div class="card-body bg-white p-4">
            {{-- Toolbar & Filter --}}
            <div class="row g-3 mb-4 align-items-center">
                <div class="col-md-7 d-flex gap-2">
                    <button type="button" id="btnBulkDelete" class="btn btn-danger btn-sm px-4 rounded-pill shadow-sm" style="display: none;">
                        <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
                    </button>

                    <form action="{{ route('admin.products.deleteAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus seluruh database produk?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm px-3 rounded-pill border-2">
                            <i class="fas fa-trash-alt"></i> Hapus Semua
                        </button>
                    </form>
                </div>
                <div class="col-md-5">
                    <form method="GET" class="search-box">
                        <div class="input-group shadow-sm rounded-pill overflow-hidden">
                            <span class="input-group-text bg-light border-0 text-primary">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control bg-light border-0 px-2" placeholder="Cari nama mahakarya..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary px-4">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle custom-table">
                    <thead>
                        <tr class="bg-light text-nowrap">
                            <th width="50" class="text-center ps-4 border-0 text-primary">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th width="80" class="text-center border-0 text-primary small-caps">ID</th>
                            <th width="100" class="text-center border-0 text-primary small-caps">Pratinjau</th>
                            <th class="border-0 text-primary small-caps">Detail Produk</th>
                            <th width="100" class="text-center border-0 text-primary small-caps">Stok</th>
                            <th width="150" class="text-end border-0 text-primary small-caps">Harga Jual</th>
                            <th width="150" class="text-center border-0 text-primary small-caps">Status Sewa</th>
                            <th width="120" class="text-center pe-4 border-0 text-primary small-caps">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="product-row">
                                <td class="text-center ps-4">
                                    <input type="checkbox" value="{{ $product->id }}" class="product-checkbox form-check-input">
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-primary border">#{{ str_pad($product->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="image-container shadow-sm border border-2 border-white rounded-3 overflow-hidden mx-auto">
                                        @php 
                                            $img = $product->images->where('is_primary', 1)->first() ?? $product->images->first(); 
                                        @endphp
                                        @if($img)
                                            <img src="{{ asset('storage/' . $img->image_path) }}" class="product-img" alt="{{ $product->name }}">
                                        @else
                                            <div class="no-image-placeholder small"><i class="fas fa-image"></i></div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark mb-0">{{ $product->name }}</div>
                                    <span class="badge bg-info bg-opacity-10 text-info mt-1" style="font-size: 0.65rem;">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($product->stock <= 0)
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger px-3">Habis</span>
                                    @else
                                        <span class="text-dark fw-semibold">{{ $product->stock }} <small class="text-muted">Unit</small></span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex flex-column">
                                        
                                        {{-- Harga Diskon (utama kalau ada) --}}
                                        @if($product->discount_price)
                                            <span class="fw-bold text-dark">
                                                Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                            </span>

                                            {{-- Harga Asli dicoret --}}
                                            <small class="text-danger text-decoration-line-through" style="font-size: 0.7rem;">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </small>
                                        @else
                                            {{-- Kalau tidak ada diskon, tampilkan harga asli saja --}}
                                            <span class="fw-bold text-dark">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                        @endif

                                    </div>
                                </td>
                                <td class="text-center">
                                    @if(!is_null($product->rental_price) && $product->rental_price > 0)
                                        @php
                                            $unitLabels = ['hour' => 'Jam', 'day' => 'Hari', 'week' => 'Minggu', 'month' => 'Bulan'];
                                        @endphp
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-primary">Rp {{ number_format($product->rental_price, 0, ',', '.') }}</span>
                                            <small class="text-muted" style="font-size: 0.65rem;">per {{ $unitLabels[$product->rental_unit] ?? 'Hari' }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted" style="font-size: 0.75rem;">—</span>
                                    @endif
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary rounded-circle action-btn">
                                            <i class="fas fa-pen-fancy"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn" onclick="return confirm('Hapus koleksi ini?')">
                                                <i class="fas fa-eraser"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-box-open text-light display-1"></i>
                                    <p class="text-muted mt-3">Galeri Mahakarya Kosong.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Pagination --}}
            <div class="mt-4 d-flex justify-content-between align-items-center border-top pt-4">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $products->firstItem() ?? 0 }}</strong> - <strong>{{ $products->lastItem() ?? 0 }}</strong> dari {{ $products->total() }} Produk
                </div>
                <div>{{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Form for Bulk Delete --}}
<form id="formBulkDelete" action="{{ route('admin.products.bulkDelete') }}" method="POST" style="display:none;">
    @csrf @method('DELETE')
    <div id="hiddenInputsContainer"></div>
</form>

<style>
    .luxury-admin { background-color: #f4f7f6; min-height: 100vh; }
    .card { border-radius: 15px !important; border: none; }
    .bg-primary.bg-gradient { background: linear-gradient(135deg, #1e3a8a 0%, #1e3a8a 100%) !important; }
    .image-container { width: 45px; height: 45px; transition: all 0.3s ease; }
    .image-container:hover { transform: scale(1.2); z-index: 10; }
    .product-img { width: 100%; height: 100%; object-fit: cover; }
    .no-image-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f8f9fa; color: #ced4da; }
    .custom-table thead th { font-weight: 700; text-transform: uppercase; font-size: 0.65rem; letter-spacing: 1px; padding: 15px 10px; background-color: #f8f9fa; }
    .product-row:hover { background-color: #f8fbff !important; }
    .action-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const countSpan = document.getElementById('selectedCount');
        const formBulkDelete = document.getElementById('formBulkDelete');
        const hiddenInputsContainer = document.getElementById('hiddenInputsContainer');

        function updateBulkButton() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            btnBulkDelete.style.display = checkedCount > 0 ? 'inline-block' : 'none';
            countSpan.textContent = checkedCount;
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
                cb.closest('tr').classList.toggle('table-primary', this.checked);
            });
            updateBulkButton();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                cb.closest('tr').classList.toggle('table-primary', this.checked);
                updateBulkButton();
                selectAll.checked = document.querySelectorAll('.product-checkbox:checked').length === checkboxes.length;
            });
        });

        btnBulkDelete.addEventListener('click', function() {
            if (confirm('Hapus produk terpilih?')) {
                hiddenInputsContainer.innerHTML = '';
                document.querySelectorAll('.product-checkbox:checked').forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected[]';
                    input.value = cb.value;
                    hiddenInputsContainer.appendChild(input);
                });
                formBulkDelete.submit();
            }
        });
    });
</script>
@endsection