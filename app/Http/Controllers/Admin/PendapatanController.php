<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class PendapatanController extends Controller
{
    public function index()
    {
        // Total revenue
        $totalRevenue = Order::where('status', 'completed')
            ->sum('total_amount');

        // Total semua order
        $totalOrders = Order::count();

        // Total order selesai
        $completedOrders = Order::where('status', 'completed')
            ->count();

        // LIST ORDER
        $orders = Order::with('user')
            ->latest()
            ->get();

        return view('admin.pendapatan.index', compact(
            'totalRevenue',
            'totalOrders',
            'completedOrders',
            'orders'
        ));
    }
}