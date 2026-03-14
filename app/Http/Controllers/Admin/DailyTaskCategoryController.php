<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DailyTaskCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::byType('daily_task')->latest()->paginate(10);
        return view('admin.daily-task-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.daily-task-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['type'] = 'daily_task';
        $validated['slug'] = Str::slug($validated['name'] . '-daily-task');
        $validated['is_active'] = $request->has('is_active');
        $validated['color'] = null;

        Category::create($validated);

        return redirect()->route('admin.daily-task-categories.index')->with('success', 'Kategori Tugas Harian berhasil ditambahkan.');
    }

    public function edit(Category $dailyTaskCategory)
    {
        return view('admin.daily-task-categories.edit', ['category' => $dailyTaskCategory]);
    }

    public function update(Request $request, Category $dailyTaskCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($dailyTaskCategory->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name'] . '-daily-task');
        }

        $validated['is_active'] = $request->has('is_active');

        $dailyTaskCategory->update($validated);

        return redirect()->route('admin.daily-task-categories.index')->with('success', 'Kategori Tugas Harian berhasil diperbarui.');
    }

    public function destroy(Category $dailyTaskCategory)
    {
        $dailyTaskCategory->delete();
        return redirect()->route('admin.daily-task-categories.index')->with('success', 'Kategori Tugas Harian berhasil dihapus.');
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> main
