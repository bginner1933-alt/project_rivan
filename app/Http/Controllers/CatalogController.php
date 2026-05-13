<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Menampilkan halaman catalog publik dengan fitur filter lengkap.
     */
    public function index(Request $request)
    {
        // BASE QUERY
        $query = Product::query()
            ->with(['category', 'primaryImage'])
            ->available();

        /*
        |--------------------------------------------------------------------------
        | FILTER SEARCH
        |--------------------------------------------------------------------------
        */
        if ($request->filled('q')) {
            $query->search($request->q);
        }

       /*
        |--------------------------------------------------------------------------
        | FILTER BELI / SEWA / KATEGORI
        |--------------------------------------------------------------------------
        */
if ($request->filled('category')) {

    // =========================
    // BELI SAJA
    // =========================
    if ($request->category == 'beli') {

        $query->whereNotNull('price')
              ->where('price', '>', 0)
              ->where(function ($q) {
                  $q->whereNull('rental_price')
                    ->orWhere('rental_price', 0);
              });

    }

    // =========================
    // SEWA SAJA
    // =========================
    elseif ($request->category == 'sewa') {

        $query->whereNotNull('rental_price')
              ->where('rental_price', '>', 0)
              ->where(function ($q) {
                  $q->whereNull('price')
                    ->orWhere('price', 0);
              });

    }

    // =========================
    // BELI DAN SEWA
    // =========================
    elseif ($request->category == 'all') {

        $query->whereNotNull('price')
              ->where('price', '>', 0)
              ->whereNotNull('rental_price')
              ->where('rental_price', '>', 0);

    }

    // =========================
    // KATEGORI BIASA
    // =========================
    else {

        $query->byCategory($request->category);

    }
}

        /*
        |--------------------------------------------------------------------------
        | FILTER HARGA
        |--------------------------------------------------------------------------
        */
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */
        $sort = $request->get('sort', 'newest');

        $query->when($sort === 'price_asc', fn($q) => $q->orderBy('price', 'asc'))
              ->when($sort === 'price_desc', fn($q) => $q->orderBy('price', 'desc'))
              ->when($sort === 'name_asc', fn($q) => $q->orderBy('name', 'asc'))
              ->when($sort === 'name_desc', fn($q) => $q->orderBy('name', 'desc'))
              ->when($sort === 'newest', fn($q) => $q->latest());

        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */
        $products = $query->paginate(12)->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | SIDEBAR DATA
        |--------------------------------------------------------------------------
        */
        $categories = Category::active()
            ->withCount(['products' => fn($q) => $q->available()])
            ->having('products_count', '>', 0)
            ->orderBy('name')
            ->get();

        $priceRange = Product::available()
            ->selectRaw('MIN(price) as min, MAX(price) as max')
            ->first();

        return view('catalog.index', compact(
            'products',
            'categories',
            'priceRange'
        ));
    }

    /**
     * Detail Produk
     */
    public function show($slug)
    {
        $product = Product::available()
            ->with([
                'category',
                'images',
                'primaryImage',
                'firstImage'
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        return view('catalog.show', compact('product'));
    }
}