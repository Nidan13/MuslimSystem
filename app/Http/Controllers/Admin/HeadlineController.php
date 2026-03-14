<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Headline;
use App\Http\Requests\Admin\HeadlineRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HeadlineController extends Controller
{
    public function index()
    {
        // Renamed relationship to 'category' in Model
        $headlines = Headline::with('category')->latest()->paginate(10);
        return view('admin.headlines.index', compact('headlines'));
    }

    public function create()
    {
        $categories = \App\Models\Category::byType('berita')->active()->get();
        return view('admin.headlines.create', compact('categories'));
    }

    public function store(HeadlineRequest $request)
    {
        $validated = $request->validated();
        
        // Handle booleans
        $validated['is_active'] = $request->has('is_active');
        $validated['is_for_user'] = $request->has('is_for_user');
        $validated['is_for_landing_page'] = $request->has('is_for_landing_page');

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Handle category_id if empty string
        if (empty($validated['category_id'])) {
            $validated['category_id'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('headlines', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        unset($validated['image']);
        Headline::create($validated);

        return redirect()->route('admin.headlines.index')->with('success', 'Headline baru berhasil ditambahkan!');
    }

    public function edit(Headline $headline)
    {
        $categories = \App\Models\Category::byType('berita')->active()->get();
        return view('admin.headlines.edit', compact('headline', 'categories'));
    }

    public function update(HeadlineRequest $request, Headline $headline)
    {
        $validated = $request->validated();
        
        // Handle booleans
        $validated['is_active'] = $request->has('is_active');
        $validated['is_for_user'] = $request->has('is_for_user');
        $validated['is_for_landing_page'] = $request->has('is_for_landing_page');

        // Generate slug
        $validated['slug'] = Str::slug($validated['title']);

        // Handle category_id if empty string
        if (empty($validated['category_id'])) {
            $validated['category_id'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if stored locally
            if ($headline->image_url && str_starts_with($headline->image_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $headline->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('headlines', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        unset($validated['image']);
        $headline->update($validated);

        return redirect()->route('admin.headlines.index')->with('success', 'Headline berhasil diupdate!');
    }

    public function destroy(Headline $headline)
    {
        // Delete image file if stored locally
        if ($headline->image_url && str_starts_with($headline->image_url, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $headline->image_url);
            Storage::disk('public')->delete($oldPath);
        }

        $headline->delete();
        return redirect()->route('admin.headlines.index')->with('success', 'Headline berhasil dihapus!');
    }
}