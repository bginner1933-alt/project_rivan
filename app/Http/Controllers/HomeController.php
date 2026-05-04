<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda (homepage).
     *
     * Konten:
     * - Kategori populer
     * - Produk unggulan (featured)
     * - Produk terbaru
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ==========================================================
        // 1. KATEGORI POPULER
        // ==========================================================
        $categories = Category::query()
            ->active()
            ->withCount([
                'activeProducts' => function ($q) {
                    $q->where('is_active', true)
                      ->where('stock', '>', 0);
                }
            ])
            ->having('active_products_count', '>', 0)
            ->orderBy('name')
            ->take(6)
            ->get();

        // ==========================================================
        // 2. PRODUK UNGGULAN (FEATURED)
        // ==========================================================
        $featuredProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->active()
            ->inStock()
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        // ==========================================================
        // 3. PRODUK TERBARU
        // ==========================================================
        $latestProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->active()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        // ==========================================================
        // 4. KIRIM KE VIEW
        // ==========================================================
        return view('home', compact(
            'categories',
            'featuredProducts',
            'latestProducts'
        ));
    }
}