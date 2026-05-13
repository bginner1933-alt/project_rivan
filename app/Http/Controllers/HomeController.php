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
            // Tambahkan pengecekan wishlist jika user login
            ->when(auth()->check(), function ($query) {
                $query->withExists(['wishlists as is_wishlisted' => function ($q) {
                    $q->where('user_id', auth()->id());
                }]);
            })
            ->active()
            ->inStock()
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        // Lakukan hal yang sama untuk latestProducts jika diperlukan
        $latestProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->when(auth()->check(), function ($query) {
                $query->withExists(['wishlists as is_wishlisted' => function ($q) {
                    $q->where('user_id', auth()->id());
                }]);
            })
            ->active()
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        // ==========================================================
        // 3. PRODUK TERJUAL
        // ==========================================================
        $featuredProducts = Product::withSum([
            'orderItems as total_sold' => function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('status', 'completed');
                });
            }
        ], 'quantity')->latest()->take(8)->get();

        return view('home', compact('categories', 'featuredProducts', 'latestProducts'));
    }
}