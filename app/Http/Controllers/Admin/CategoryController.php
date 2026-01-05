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
     * Menampilkan daftar kategori dengan caching dan pagination.
     */
    public function index()
    {
        // Ambil data kategori dari cache untuk performa lebih baik
        $categories = Cache::remember('global_categories', 3600, function () {
            return Category::select('id', 'name', 'slug', 'is_active', 'image', 'created_at')
                ->withCount('products') // Hitung jumlah produk di setiap kategori
                ->latest() // Urut berdasarkan terbaru
                ->paginate(10);
        });

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
        ]);

        // Upload gambar jika ada
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')
                ->store('categories', 'public');
        }

        // Generate slug otomatis
        $validated['slug'] = Str::slug($validated['name']);

        // Simpan ke database
        Category::create($validated);

        // Hapus cache agar data terbaru muncul
        Cache::forget('global_categories');

        return back()->with('success', 'Kategori berhasil ditambahkan!');
    }

    /**
     * Memperbarui data kategori.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:1024',
            'is_active' => 'boolean',
        ]);

        // Handle ganti gambar
        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')
                ->store('categories', 'public');
        }

        // Update slug sesuai nama terbaru
        $validated['slug'] = Str::slug($validated['name']);

        // Update database
        $category->update($validated);

        // Hapus cache agar data terbaru muncul
        Cache::forget('global_categories');

        return back()->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category)
    {
        // Cegah hapus kategori jika masih ada produk
        if ($category->products()->exists()) {
            return back()->with('error',
                'Kategori tidak dapat dihapus karena masih memiliki produk. Silahkan pindahkan atau hapus produk terlebih dahulu.');
        }

        // Hapus file gambar jika ada
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // Hapus record dari database
        $category->delete();

        // Hapus cache agar daftar kategori terbaru muncul
        Cache::forget('global_categories');

        return back()->with('success', 'Kategori berhasil dihapus!');
    }
}
