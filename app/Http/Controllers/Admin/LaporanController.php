<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Tanggal
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | ORDERS
        |--------------------------------------------------------------------------
        */

        $orders = Order::with(['items', 'user'])
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('status', 'completed')
            ->latest()
            ->paginate(20);

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $summary = Order::whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->where('status', 'completed')
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue
            ')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | CATEGORY ANALYTICS
        |--------------------------------------------------------------------------
        */

        $byCategory = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereDate('orders.created_at', '>=', $dateFrom)
            ->whereDate('orders.created_at', '<=', $dateTo)
            ->where('orders.status', 'completed')
            ->groupBy('categories.id', 'categories.name')
            ->select(
                'categories.name',
                DB::raw('SUM(order_items.subtotal) as total')
            )
            ->orderByDesc('total')
            ->get();

        return view('admin.laporan.index', compact(
            'orders',
            'summary',
            'byCategory',
            'dateFrom',
            'dateTo'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT EXCEL
    |--------------------------------------------------------------------------
    */

    public function exportSales(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo   = $request->date_to ?? now()->toDateString();

        return Excel::download(
            new SalesReportExport($dateFrom, $dateTo),
            "laporan-penjualan-{$dateFrom}-sd-{$dateTo}.xlsx"
        );
    }
}