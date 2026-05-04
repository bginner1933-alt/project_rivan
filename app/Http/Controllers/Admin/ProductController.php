<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    /**
     * List produk
     */
    public function index(Request $request): View
{
    $products = Product::query()
        // Ambil semua kolom yang diperlukan untuk tampilan tabel
        ->select([
            'id', 'name', 'price', 'discount_price', 
            'rental_price', 'rental_unit', 'slug', 
            'category_id', 'stock', 'created_at'
        ])
        // Gunakan eager loading untuk kategori dan gambar
        ->with(['category:id,name', 'images']) 
        ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
        ->when($request->category, fn($q, $c) => $q->where('category_id', $c))
        ->latest()
        ->paginate(15)
        ->withQueryString();

    $categories = Cache::remember('global_categories', 3600, function () {
        return Category::select('id', 'name')->orderBy('name')->get();
    });

    return view('admin.products.index', compact('products', 'categories'));
}

    /**
     * Form create
     */
    public function create(): View
    {
        $categories = Cache::remember('global_categories', 3600, function () {
            return Category::select('id', 'name')
                ->withCount('products')
                ->orderBy('name')
                ->get();
        });

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store produk
     */
    public function store(StoreProductRequest $request): RedirectResponse
{
    try {
        DB::beginTransaction();

        $data = $request->validated();

        // 🔥 FIX RENTAL (NO EMPTY CHECK BUG)
        $data['rental_price'] = $data['rental_price'] ?? null;
        $data['rental_unit'] = $data['rental_unit'] ?? null;

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            $this->uploadImages($request->file('images'), $product);
        }

        DB::commit();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    } catch (\Exception $e) {
        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', 'Gagal menyimpan: ' . $e->getMessage());

            dd($request->all());
    }
    }

    /**
     * Detail produk
     */
    public function show(Product $product): View
    {
        $product->load([
            'category:id,name',
            'images:id,product_id,image_path,is_primary',
            'orderItems'
        ]);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Form edit
     */
    public function edit(Product $product): View
    {
        $categories = Cache::remember('global_categories', 3600, function () {
            return Category::select('id', 'name')
                ->withCount('products')
                ->orderBy('name')
                ->get();
        });

        $product->load('images');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update produk
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // ✅ FIX RENTAL
            $data['rental_price'] = $request->filled('rental_price')
                ? $request->rental_price
                : null;

            $data['rental_unit'] = $request->filled('rental_unit')
                ? $request->rental_unit
                : null;

            // 🔥 FIX CHECKBOX
            $data['is_active'] = $request->has('is_active');
            $data['is_featured'] = $request->has('is_featured');

            $product->update($data);

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    /**
     * Hapus 1 produk
     */
    public function destroy(Product $product): RedirectResponse
    {
        // ❗ CEK DULU apakah produk dipakai di pesanan
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Produk tidak bisa dihapus karena sudah pernah dipesan.');
        }

        try {
            $product->delete(); // gambar akan dihapus otomatis dari Model

            return redirect()->route('admin.products.index')
                ->with('success', 'Produk dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = $request->input('selected', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada produk dipilih.');
        }

        $deleted = 0;
        $skipped = 0;

        $products = Product::whereIn('id', $ids)->get();

        foreach ($products as $product) {

            // ❗ skip kalau masih dipakai
            if ($product->orderItems()->exists()) {
                $skipped++;
                continue;
            }

            $product->delete();
            $deleted++;
        }

        return back()->with('success', "$deleted produk dihapus, $skipped dilewati karena masih digunakan.");
    }

    /**
     * Delete semua produk
     */
    public function deleteAll(): RedirectResponse
    {
        $products = Product::all();

        $deleted = 0;
        $skipped = 0;

        foreach ($products as $product) {

            if ($product->orderItems()->exists()) {
                $skipped++;
                continue;
            }

            $product->delete();
            $deleted++;
        }

        return back()->with('success', "Semua produk diproses. $deleted dihapus, $skipped dilewati.");
    }

    // ================= HELPER =================

    protected function uploadImages(array $files, Product $product): void
    {
        $isFirst = $product->images()->count() === 0;

        foreach ($files as $index => $file) {
            $filename = 'product-' . $product->id . '-' . time() . '-' . $index . '.' . $file->extension();
            $path = $file->storeAs('products', $filename, 'public');

            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $isFirst && $index === 0,
                'sort_order' => $product->images()->count() + $index,
            ]);
        }
    }

    protected function deleteImages(array $imageIds): void
    {
        $images = ProductImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }

    protected function setPrimaryImage(Product $product, int $imageId): void
    {
        $product->images()->update(['is_primary' => false]);

        $product->images()
            ->where('id', $imageId)
            ->update(['is_primary' => true]);
    }

    public function scopeLate($query)
{
    return $query->whereNull('returned_at')
                 ->where('end_date', '<', now());
}

    public function returnRental($id)
{
    $rental = Rental::findOrFail($id);

    if ($rental->returned_at) {
        return back()->with('error', 'Barang sudah dikembalikan.');
    }

    $rental->update([
        'returned_at' => now(),
        'status' => 'returned',
    ]);

    // OPTIONAL: kembalikan stok produk
    $rental->product->increment('stock', 1);

    return back()->with('success', 'Barang berhasil dikembalikan.');
}
}