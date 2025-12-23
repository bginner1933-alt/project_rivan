@extends('layouts.admin')

@section('content')
    <h1>Daftar Pesanan Saya</h1>

    @if($orders->isEmpty())
        <p>Belum ada pesanan.</p>
    @else
        <table border="1" cellpadding="5">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ number_format($order->total, 0, ',', '.') }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}">Detail</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
