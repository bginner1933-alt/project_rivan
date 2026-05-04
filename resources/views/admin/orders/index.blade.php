@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">

    {{-- KIRI: tombol kembali + judul --}}
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>

        <h2 class="h3 mb-0 text-gray-800">Daftar Pesanan</h2>
    </div>

    {{-- KANAN: aksi --}}
    <div class="d-flex gap-2">
        {{-- Tombol Hapus Semua --}}
        <form action="{{ route('admin.orders.delete-all') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA pesanan?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger shadow-sm">
                <i class="fas fa-trash-alt fa-sm text-white-50"></i> Hapus Semua
            </button>
        </form>
    </div>
</div>

{{-- Alert Success/Error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

        {{-- Filter Status --}}
        <ul class="nav nav-pills card-header-pills">
            <li class="nav-item">
                <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">Semua</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'pending']) }}">Pending</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'processing' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'processing']) }}">Diproses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['status' => 'completed']) }}">Selesai</a>
            </li>
        </ul>

        {{-- Tombol Hapus Terpilih --}}
        <button type="button" id="btn-bulk-delete" class="btn btn-outline-danger btn-sm" style="display: none;" onclick="confirmBulkDelete()">
            Hapus Terpilih (<span id="selected-count">0</span>)
        </button>
    </div>

    <div class="card-body p-0">
        <form id="form-bulk-delete" action="{{ route('admin.orders.bulk-delete') }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 40px;">
                                <input type="checkbox" id="select-all" class="form-check-input">
                            </th>
                            <th>No. Order</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4">
                                <input type="checkbox" name="selected_ids[]" value="{{ $order->id }}" class="form-check-input order-checkbox">
                            </td>
                            <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                            <td>
                                <div class="fw-bold">{{ $order->user->name }}</div>
                                <small class="text-muted">{{ $order->user->email }}</small>
                            </td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge bg-info text-dark">Diproses</span>
                                @elseif($order->status == 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-danger">Batal</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Tidak ada pesanan ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    <div class="card-footer bg-white">
        {{ $orders->links() }}
    </div>
</div>

<script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.order-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        toggleBulkDeleteBtn();
    });

    document.querySelectorAll('.order-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleBulkDeleteBtn);
    });

    function toggleBulkDeleteBtn() {
        const checkedCount = document.querySelectorAll('.order-checkbox:checked').length;
        const btn = document.getElementById('btn-bulk-delete');
        const countSpan = document.getElementById('selected-count');

        if (checkedCount > 0) {
            btn.style.display = 'block';
            countSpan.textContent = checkedCount;
        } else {
            btn.style.display = 'none';
        }
    }

    function confirmBulkDelete() {
        if (confirm('Yakin ingin menghapus pesanan yang dipilih?')) {
            document.getElementById('form-bulk-delete').submit();
        }
    }
</script>
@endsection