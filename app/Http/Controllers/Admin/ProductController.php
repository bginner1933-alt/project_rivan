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

class ProductController extends Controller
{
    /**
     * ===============================
     * LIST PRODUK
     * ===============================
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->select([
                'id',
                'name',
                'price',
                'discount_price',
                'rental_price',
                'slug',
                'category_id',
                'stock',
                'rental_duration',
                'weight',
                'created_at'
            ])
            ->with([
                'category:id,name',
                'images'
            ])
            ->when($request->search, fn($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
            )
            ->when($request->category, fn($q, $c) =>
                $q->where('category_id', $c)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Cache::remember('global_categories', 3600, function () {
            return Category::select('id', 'name')
                ->orderBy('name')
                ->get();
        });

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * ===============================
     * FORM CREATE
     * ===============================
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
     * ===============================
     * STORE PRODUK
     * ===============================
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {

            DB::beginTransaction();

            $data = $request->validated();

            // FIX RENTAL
            $data['rental_price'] = $data['rental_price'] ?? null;
            $data['rental_duration'] = $data['rental_duration'] ?? null;

            // FIX WEIGHT (Jika di request form kosong, beri default nilai 0)
            $data['weight'] = $request->filled('weight') ? $request->weight : 0; // 🔥 FIX 2

            // FIX CHECKBOX
            $data['is_active'] = $request->has('is_active');
            $data['is_featured'] = $request->has('is_featured');

            $product = Product::create($data);

            // MULTIPLE IMAGE
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
                ->with('error', 'Gagal menyimpan produk: ' . $e->getMessage());
        }
        
    }

    /**
     * ===============================
     * DETAIL PRODUK
     * ===============================
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
     * ===============================
     * FORM EDIT
     * ===============================
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
     * ===============================
     * UPDATE PRODUK
     * ===============================
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        try {

            DB::beginTransaction();

            $data = $request->validated();

            // FIX RENTAL
            $data['rental_price'] = $request->filled('rental_price')
                ? $request->rental_price
                : null;

            $data['rental_duration'] = $request->filled('rental_duration')
                ? $request->rental_duration
                : null;

            // FIX WEIGHT (Sama seperti store, tangkap data request pembaruan berat)
            $data['weight'] = $request->filled('weight') ? $request->weight : 0; // 🔥 FIX 3

            // FIX CHECKBOX
            $data['is_active'] = $request->has('is_active');
            $data['is_featured'] = $request->has('is_featured');

            // UPDATE DATA PRODUK
            $product->update($data);

            /**
             * ===============================
             * DELETE IMAGE
             * ===============================
             */
            if ($request->filled('delete_images')) {
                $this->deleteImages($request->delete_images);
            }

            /**
             * ===============================
             * SET PRIMARY IMAGE
             * ===============================
             */
            if ($request->filled('primary_image')) {
                $this->setPrimaryImage($product, $request->primary_image);
            }

            /**
             * ===============================
             * UPLOAD MULTIPLE IMAGE
             * ===============================
             */
            if ($request->hasFile('images')) {
                $this->uploadImages($request->file('images'), $product);
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil diperbarui!');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal update produk: ' . $e->getMessage());
        }
    }

    /**
     * ===============================
     * HAPUS PRODUK
     * ===============================
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return back()->with(
                'error',
                'Produk tidak bisa dihapus karena sudah pernah dipesan.'
            );
        }

        try {

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $product->delete();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Produk berhasil dihapus!');

        } catch (\Exception $e) {

            return back()->with(
                'error',
                'Gagal menghapus produk: ' . $e->getMessage()
            );
        }
    }

    /**
     * ===============================
     * BULK DELETE
     * ===============================
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

            if ($product->orderItems()->exists()) {
                $skipped++;
                continue;
            }

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $product->delete();

            $deleted++;
        }

        return back()->with(
            'success',
            "$deleted produk dihapus, $skipped dilewati."
        );
    }

    /**
     * ===============================
     * DELETE ALL
     * ===============================
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

            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $product->delete();

            $deleted++;
        }

        return back()->with(
            'success',
            "Semua produk diproses. $deleted dihapus, $skipped dilewati."
        );
    }

    /**
     * ===============================
     * HELPER UPLOAD IMAGE
     * ===============================
     */
    protected function uploadImages(array $files, Product $product): void
    {
        $isFirst = $product->images()->count() === 0;

        foreach ($files as $index => $file) {

            $filename =
                'product-' .
                $product->id .
                '-' .
                time() .
                '-' .
                $index .
                '.' .
                $file->getClientOriginalExtension();

            $path = $file->storeAs(
                'products',
                $filename,
                'public'
            );

            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $isFirst && $index === 0,
                'sort_order' => $product->images()->count() + $index,
            ]);
        }
    }

    /**
     * ===============================
     * DELETE IMAGE
     * ===============================
     */
    protected function deleteImages(array $imageIds): void
    {
        $images = ProductImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {

            Storage::disk('public')->delete($image->image_path);

            $image->delete();
        }
    }

    /**
     * ===============================
     * SET PRIMARY IMAGE
     * ===============================
     */
    protected function setPrimaryImage(Product $product, int $imageId): void
    {
        $product->images()->update([
            'is_primary' => false
        ]);

        $product->images()
            ->where('id', $imageId)
            ->update([
                'is_primary' => true
            ]);
    }
}