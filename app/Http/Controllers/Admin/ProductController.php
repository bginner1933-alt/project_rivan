<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        // Ambil semua produk beserta kategori mereka (eager loading)
        $products = Product::with('category')->get();

        // Kirim data ke view
        return view('admin.products.index', compact('products'));
    }
}
