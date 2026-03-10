<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HeadlineCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::byType('berita')->latest()->paginate(10);
        return view('admin.headline-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.headline-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['type'] = 'berita';
        $validated['slug'] = Str::slug($validated['name'] . '-berita');
        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('admin.headline-categories.index')->with('success', 'Kategori Berita berhasil ditambahkan.');
    }

    public function edit(Category $headlineCategory)
    {
        return view('admin.headline-categories.edit', ['category' => $headlineCategory]);
    }

    public function update(Request $request, Category $headlineCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($headlineCategory->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name'] . '-berita');
        }

        $validated['is_active'] = $request->has('is_active');

        $headlineCategory->update($validated);

        return redirect()->route('admin.headline-categories.index')->with('success', 'Kategori Berita berhasil diperbarui.');
    }

    public function destroy(Category $headlineCategory)
    {
        $headlineCategory->delete();
        return redirect()->route('admin.headline-categories.index')->with('success', 'Kategori Berita berhasil dihapus.');
    }
}
