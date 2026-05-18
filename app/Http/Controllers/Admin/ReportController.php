<?php
// app/Http/Controllers/Admin/ReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\SalesReportExport;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // Satukan fungsi index langsung menangani pemrosesan data laporan
    public function index(Request $request)
    {
        // 1. Tentukan Default Tanggal
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? now()->toDateString();

        // 2. Query Utama (Tabel Transaksi Detail) dengan Paginate
        $orders = Order::with(['items', 'user']) 
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid') 
            ->latest() 
            ->paginate(20);

        // 3. Query Summary
        $summary = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('payment_status', 'paid')
            ->selectRaw('COUNT(*) as total_orders, SUM(total_amount) as total_revenue')
            ->first();

        // 4. Query Analitik: Penjualan per Kategori
        $byCategory = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereDate('orders.created_at', '>=', $dateFrom)
            ->whereDate('orders.created_at', '<=', $dateTo)
            ->where('orders.payment_status', 'paid')
            ->groupBy('categories.id', 'categories.name')
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.subtotal) as total') 
            )
            ->orderByDesc('total') 
            ->get();

        return view('admin.reports.index', compact('orders', 'summary', 'byCategory', 'dateFrom', 'dateTo'));
    }

    // Hapus fungsi public function sales(Request $request) yang lama karena sudah disatukan di atas.

    /**
     * Handle download Excel.
     */
    public function exportSales(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->date_to ?? now()->toDateString();

        return Excel::download(
            new SalesReportExport($dateFrom, $dateTo),
            "laporan-penjualan-{$dateFrom}-sd-{$dateTo}.xlsx"
        );
    }
}