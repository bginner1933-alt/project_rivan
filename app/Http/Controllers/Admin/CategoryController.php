<?php
// app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar kategori dengan caching per halaman.
     */
    public function index(Request $request)
    {
        $page = $request->get('page', 1); // Ambil halaman saat ini
        $perPage = 10; // Jumlah item per halaman

        // Cache per halaman
        $categories = Cache::remember("global_categories_page_$page", 3600, function () use ($perPage) {
            return Category::select('id', 'name', 'slug', 'is_active', 'image', 'created_at')
                ->withCount('products') // Hitung jumlah produk per kategori
                ->latest()
                ->paginate($perPage);
        });

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menyimpan kategori baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        // Hapus semua cache halaman
        $this->clearCategoryCache();

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Memperbarui kategori.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')
                ->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        // Hapus semua cache halaman
        $this->clearCategoryCache();

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        // Hapus semua cache halaman
        $this->clearCategoryCache();

        return back()->with('success', 'Kategori berhasil dihapus!');
    }

    /**
     * Hapus semua cache kategori (semua halaman).
     */
    protected function clearCategoryCache()
    {
        $pages = ceil(Category::count() / 10); // jumlah halaman cache
        for ($i = 1; $i <= $pages; $i++) {
            Cache::forget("global_categories_page_$i");
        }
    }
}
