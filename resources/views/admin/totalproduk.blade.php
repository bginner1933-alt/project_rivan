@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">📊 Laporan Stok & Penjualan</h2>
                    <p class="text-muted">Pantau performa produk dan jumlah stok yang tersedia.</p>
                </div>

                <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i> Cetak Laporan
                </button>
            </div>

            {{-- TABLE --}}
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">

                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Rank</th>
                                    <th class="py-3">Produk</th>
                                    <th class="py-3">Kategori</th>
                                    <th class="py-3">Harga</th>
                                    <th class="py-3 text-center">Stok</th>
                                    <th class="py-3 text-center">Terjual</th>
                                    <th class="py-3 text-center pe-4">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($products as $index => $product)
                                <tr>

                                    {{-- RANK --}}
                                    <td class="ps-4">
                                        @if($index == 0)
                                            🥇
                                        @elseif($index == 1)
                                            🥈
                                        @elseif($index == 2)
                                            🥉
                                        @else
                                            <span class="text-muted">#{{ $index + 1 }}</span>
                                        @endif
                                    </td>

                                    {{-- PRODUCT --}}
                                    <td>
                                        <div class="d-flex align-items-center">

                                            {{-- IMAGE FIX START --}}
                                            <img src="{{ $product->image_url ?? 'https://placehold.co/400x400?text=No+Image' }}"
                                                class="rounded-3 me-3 border"
                                                style="width: 50px; height: 50px; object-fit: cover;"
                                                onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                                            <div>
                                                <div class="fw-bold">{{ $product->name }}</div>
                                                <small class="text-muted">ID: #PROD-{{ $product->id }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- CATEGORY --}}
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                                        </span>
                                    </td>

                                    {{-- PRICE --}}
                                    <td class="fw-bold">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </td>

                                    {{-- STOCK --}}
                                    <td class="text-center">
                                        <span class="fw-bold {{ $product->stock <= 5 ? 'text-danger' : '' }}">
                                            {{ $product->stock }}
                                        </span>
                                    </td>

                                    {{-- SOLD --}}
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            {{ $product->sold ?? 0 }}
                                        </span>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="text-center pe-4">
                                        @if($product->stock > 0)
                                            <span class="text-success">● Tersedia</span>
                                        @else
                                            <span class="text-danger">● Habis</span>
                                        @endif
                                    </td>

                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        Tidak ada produk
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

<style>
.table tbody tr:hover {
    background: #f8faff;
}
</style>
@endsection