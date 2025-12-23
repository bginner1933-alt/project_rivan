<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        // Ambil orders milik user yang login
        $orders = Order::where('user_id', Auth::id())->get();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // Pastikan user hanya bisa lihat order miliknya
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }
}
