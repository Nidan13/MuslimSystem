<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->has('type')) {
            $query->byType($request->type);
        }

        $categories = $query->latest()->paginate(10);

        $counts = Category::selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        return view('admin.categories.index', compact('categories', 'counts'));
    }

    public function create()
    {
        $types = [
            Category::TYPE_QUEST => 'Quest',
            Category::TYPE_KAJIAN => 'Kajian',
            Category::TYPE_SHOP => 'Shop',
            Category::TYPE_DAILY_TASK => 'Daily Task',
            Category::TYPE_DUNGEON => 'Dungeon',
            Category::TYPE_RANK => 'Rank',
        ];

        return view('admin.categories.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:quest,kajian,shop,daily_task,dungeon,rank',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['type']);
        $validated['is_active'] = $request->has('is_active');

        Category::create($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $types = [
            Category::TYPE_QUEST => 'Quest',
            Category::TYPE_KAJIAN => 'Kajian',
            Category::TYPE_SHOP => 'Shop',
            Category::TYPE_DAILY_TASK => 'Daily Task',
            Category::TYPE_DUNGEON => 'Dungeon',
            Category::TYPE_RANK => 'Rank',
        ];

        return view('admin.categories.edit', compact('category', 'types'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:quest,kajian,shop,daily_task,dungeon,rank',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'metadata' => 'nullable|array',
        ]);

        if ($category->name !== $validated['name'] || $category->type !== $validated['type']) {
            $validated['slug'] = Str::slug($validated['name'] . '-' . $validated['type']);
        }

        $validated['is_active'] = $request->has('is_active');

        $category->update($validated);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
