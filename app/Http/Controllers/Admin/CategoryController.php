<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;

        $categories = Cache::remember("global_categories_page_$page", 3600, function () use ($perPage) {
            return Category::select('id', 'name', 'slug', 'is_active', 'image', 'created_at')
                ->withCount('products')
                ->latest()
                ->paginate($perPage);
        });

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories',
            'description' => 'nullable|string|max:500',
            // PERBAIKAN: max dinaikkan jadi 2048 (2MB)
            'image' => 'nullable|image|max:2048', 
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);
        
        Category::create($validated);
        $this->clearCategoryCache();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:500',
            // PERBAIKAN: max dinaikkan jadi 2048 (2MB)
            'image' => 'nullable|image|max:2048', 
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        $validated['slug'] = Str::slug($validated['name']);
        $category->update($validated);
        $this->clearCategoryCache();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    // ... (sisanya sama seperti kodingan kamu sebelumnya)

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki produk.');
        }

        DB::transaction(function () use ($category) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $category->delete();
        });

        $this->clearCategoryCache();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
    }

    public function deleteAll()
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            if ($category->products()->exists()) {
                return redirect()->route('admin.categories.index')
                    ->with('error', "Gagal! Kategori '{$category->name}' masih memiliki produk.");
            }
        }

        DB::transaction(function () use ($categories) {
            foreach ($categories as $category) {
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
            }
            Category::query()->delete();
        });

        $this->clearCategoryCache();
        return redirect()->route('admin.categories.index')->with('success', 'Semua kategori berhasil dikosongkan!');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected', []);
        if (empty($ids)) {
            return back()->with('error', 'Pilih kategori yang ingin dihapus.');
        }

        $categories = Category::whereIn('id', $ids)->get();

        DB::transaction(function () use ($categories) {
            foreach ($categories as $category) {
                if (!$category->products()->exists()) {
                    if ($category->image) {
                        Storage::disk('public')->delete($category->image);
                    }
                    $category->delete();
                }
            }
        });

        $this->clearCategoryCache();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori terpilih berhasil dihapus.');
    }

    protected function clearCategoryCache()
    {
        Cache::flush(); 
    }
}