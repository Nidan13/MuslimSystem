<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuestCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::byType('quest')->latest()->paginate(10);
        return view('admin.quest-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.quest-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['type'] = 'quest';
        $validated['slug'] = Str::slug($validated['name'] . '-quest');
        $validated['is_active'] = $request->has('is_active');
        $validated['color'] = null;

        Category::create($validated);

        return redirect()->route('admin.quest-categories.index')->with('success', 'Kategori Misi berhasil ditambahkan.');
    }

    public function edit(Category $questCategory)
    {
        return view('admin.quest-categories.edit', ['category' => $questCategory]);
    }

    public function update(Request $request, Category $questCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($questCategory->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name'] . '-quest');
        }

        $validated['is_active'] = $request->has('is_active');

        $questCategory->update($validated);

        return redirect()->route('admin.quest-categories.index')->with('success', 'Kategori Misi berhasil diperbarui.');
    }

    public function destroy(Category $questCategory)
    {
        $questCategory->delete();
        return redirect()->route('admin.quest-categories.index')->with('success', 'Kategori Misi berhasil dihapus.');
    }
}