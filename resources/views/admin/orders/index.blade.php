@extends('layouts.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
<style>
    .card { border-radius: 12px !important; }
    .table thead th { background-color: #f8f9fc !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #858796; border-bottom: 2px solid #e3e6f0; }
    .badge { font-weight: 600; padding: 0.5em 0.8em; border-radius: 50px; }
    .nav-pills .nav-link { color: #6e707e; border-radius: 8px; font-weight: 500; transition: all 0.3s; }
    .nav-pills .nav-link.active { background-color: #4e73df !important; color: white !important; box-shadow: 0 4px 6px -1px rgba(78, 115, 223, 0.2); }
    .btn-bulk { transition: all 0.2s; }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 mb-1 text-gray-800 fw-bold">Daftar Pesanan</h2>
        <p class="text-muted mb-0">Kelola semua pesanan pelanggan dari satu tempat.</p>
    </div>

    <form action="{{ route('admin.orders.delete-all') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus SEMUA pesanan?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-outline-danger shadow-sm">
            <i class="fas fa-trash-alt me-1"></i> Hapus Semua
        </button>
    </form>
</div>

@if(session('success') || session('error'))
    <div class="alert {{ session('success') ? 'alert-success' : 'alert-danger' }} border-0 shadow-sm mb-4">
        {{ session('success') ?? session('error') }}
    </div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4">
        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('admin.orders.index', request()->except('status')) }}">Semua</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'pending' ? 'active' : '' }}" href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'pending'])) }}">Pending</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'processing' ? 'active' : '' }}" href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'processing'])) }}">Diproses</a></li>
                <li class="nav-item"><a class="nav-link {{ request('status') == 'completed' ? 'active' : '' }}" href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => 'completed'])) }}">Selesai</a></li>
            </ul>

            <button type="button" id="btn-bulk-delete" class="btn btn-danger btn-bulk px-4" style="display: none;" onclick="confirmBulkDelete()">
                <i class="fas fa-trash me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
            </button>
        </div>

        <ul class="nav nav-pills mt-3">
            <li class="nav-item"><a class="nav-link {{ !request('payment') ? 'active' : '' }}" href="{{ route('admin.orders.index', request()->except('payment')) }}">Semua Pembayaran</a></li>
            <li class="nav-item"><a class="nav-link {{ request('payment') == 'unpaid' ? 'active' : '' }}" href="{{ route('admin.orders.index', array_merge(request()->all(), ['payment' => 'unpaid'])) }}">Belum Bayar</a></li>
            <li class="nav-item"><a class="nav-link {{ request('payment') == 'paid' ? 'active' : '' }}" href="{{ route('admin.orders.index', array_merge(request()->all(), ['payment' => 'paid'])) }}">Sudah Bayar</a></li>
            <li class="nav-item"><a class="nav-link {{ request('payment') == 'cod' ? 'active' : '' }}" href="{{ route('admin.orders.index', array_merge(request()->all(), ['payment' => 'cod'])) }}">COD</a></li>
        </ul>
    </div>

    <div class="card-body p-0">
        <form id="form-bulk-delete" action="{{ route('admin.orders.bulk-delete') }}" method="POST">
            @csrf @method('DELETE')
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" style="width: 40px;"><input type="checkbox" id="select-all" class="form-check-input"></th>
                            <th>No. Order</th>
                            <th>Customer</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td class="ps-4"><input type="checkbox" name="selected_ids[]" value="{{ $order->id }}" class="form-check-input order-checkbox"></td>
                            <td class="fw-bold text-primary">#{{ $order->order_number }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $order->user->name }}</div>
                                <div class="text-muted small">{{ $order->user->email }}</div>
                            </td>
                            <td class="text-muted">{{ $order->created_at->format('d M Y') }}</td>
                            <td class="fw-bold text-dark">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $statusColors = ['pending' => 'bg-warning', 'processing' => 'bg-info', 'completed' => 'bg-success', 'cancelled' => 'bg-danger'];
                                @endphp
                                <span class="badge {{ $statusColors[$order->status] ?? 'bg-secondary' }} text-white">{{ ucfirst($order->status) }}</span>
                                
                                @if(strtolower($order->payment_method ?? '') === 'cod')
                                    <span class="badge" style="background:#f97316; color:white;">COD</span>
                                @elseif($order->payment_status === 'paid')
                                    <span class="badge bg-success text-white">Lunas</span>
                                @else
                                    <span class="badge bg-secondary text-white">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-primary rounded-pill px-3">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">Tidak ada data pesanan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <div class="card-footer bg-white border-0 py-3">{{ $orders->links() }}</div>
</div>

<script>
    document.getElementById('select-all').addEventListener('change', function() {
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
        toggleBulkDeleteBtn();
    });

    document.querySelectorAll('.order-checkbox').forEach(cb => {
        cb.addEventListener('change', toggleBulkDeleteBtn);
    });

    function toggleBulkDeleteBtn() {
        const checked = document.querySelectorAll('.order-checkbox:checked').length;
        const btn = document.getElementById('btn-bulk-delete');
        btn.style.display = checked > 0 ? 'block' : 'none';
        document.getElementById('selected-count').textContent = checked;
    }

    function confirmBulkDelete() {
        if (confirm('Yakin ingin menghapus pesanan yang dipilih?')) {
            document.getElementById('form-bulk-delete').submit();
        }
    }
</script>
@endsection