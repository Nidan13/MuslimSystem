<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IslamicVideoCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::byType('kajian')->latest()->paginate(10);
        return view('admin.islamic-video-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.islamic-video-categories.create');
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

        $validated['type'] = 'kajian';
        $validated['slug'] = Str::slug($validated['name'] . '-kajian');
        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('admin.islamic-video-categories.index')->with('success', 'Kategori Kajian berhasil ditambahkan.');
    }

    public function edit(Category $islamicVideoCategory)
    {
        return view('admin.islamic-video-categories.edit', ['category' => $islamicVideoCategory]);
    }

    public function update(Request $request, Category $islamicVideoCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($islamicVideoCategory->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name'] . '-kajian');
        }

        $validated['is_active'] = $request->has('is_active');

        $islamicVideoCategory->update($validated);

        return redirect()->route('admin.islamic-video-categories.index')->with('success', 'Kategori Kajian berhasil diperbarui.');
    }

    public function destroy(Category $islamicVideoCategory)
    {
        $islamicVideoCategory->delete();
        return redirect()->route('admin.islamic-video-categories.index')->with('success', 'Kategori Kajian berhasil dihapus.');
    }
}