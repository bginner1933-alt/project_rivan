@extends('layouts.admin')

@section('title', 'Stok Menipis')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 text-danger fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Produk dengan Stok Menipis
                    </h5>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">Kembali ke Dashboard</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th class="text-center">Sisa Stok</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $product->name }}</div>
                                    </td>
                                    <td>{{ $product->category->name ?? 'Tanpa Kategori' }}</td>
                                    <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger px-3 py-2">
                                            {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil-square me-1"></i> Update Stok
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                                        Hebat! Tidak ada produk dengan stok kritis (di bawah 5).
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection