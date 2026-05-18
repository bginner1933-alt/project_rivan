<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\LaporanPengaduan;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // 1. Stats
        $stats = [
            'users'          => User::count(),
            'products'       => Product::count(),
            'categories'     => Category::count(),
            'total_orders'   => Order::count(),
            'total_revenue'  => Order::where('status', 'completed')
                                       ->orWhere('status', 'delivered')
                                       ->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock'      => Product::where('stock', '<', 5)->count(),
        ];

        // 2. Recent Orders (5 terbaru)
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        // 3. Top Selling Products (6 produk terlaris)
        $topProducts = Product::join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.price',
                'products.stock',
                DB::raw('SUM(order_items.quantity) as sold')
            )
            ->groupBy(
                'products.id',
                'products.name',
                'products.category_id',
                'products.price',
                'products.stock'
            )
            ->orderByDesc('sold')
            ->take(6)
            ->get();

        // 4. Revenue chart (7 hari terakhir)
        $revenueChart = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        // Kirim semua data ke view. Variabel $Chart dihapus karena tidak digunakan/undefined.
        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'revenueChart'));
    }

   public function show($id)
    {
        // Ambil data laporan pengaduan berdasarkan ID
        $laporan = LaporanPengaduan::findOrFail($id);

        // Tampilkan view
        return view('admin.laporan.show', compact('laporan'));
    }
    public function stokMenipis()
    {
        // Ambil produk yang stoknya di bawah batas (misal: 5)
        $products = Product::where('stock', '<', 5)
                           ->orderBy('stock', 'asc')
                           ->get();

        return view('admin.stokmenipis', compact('products'));
    }

    public function totalProduk()
    {
        // Ambil semua produk beserta kategori dan jumlah terjual
        $products = Product::with('category')
                           ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                           ->select('products.*', DB::raw('SUM(order_items.quantity) as sold'))
                           ->groupBy('products.id')
                           ->orderByDesc('sold')
                           ->get();

        return view('admin.totalproduk', compact('products'));
    }

    public function proses()
    {
        // Ambil semua order yang sedang diproses
        $orders = Order::where('status', 'processing')
                       ->with('user')
                       ->latest()
                       ->get();

        return view('admin.proses', compact('orders'));
    }

    public function pendapatan()
    {
        $orders = Order::with('user')
            ->whereIn('status', ['completed', 'processing'])
            ->latest()
            ->get();

        // hitung total
        $totalRevenue = Order::whereIn('status', ['completed', 'processing'])
            ->sum('total_amount');

        return view('admin.pendapatan', compact('orders', 'totalRevenue'));
    }

    public function laporanPengaduan()
    {
        // 1. Ambil semua data laporan pengaduan dari database
        $laporan = LaporanPengaduan::latest()->get();

        // 2. Kirim data ke view admin.laporan.index
        return view('admin.reports.index', compact('laporan'));
    }
}