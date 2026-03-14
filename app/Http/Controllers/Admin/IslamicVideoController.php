<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IslamicVideo;
use Illuminate\Http\Request;

class IslamicVideoController extends Controller
{
    public function index()
    {
        // Eager load category to avoid property access conflicts and improve speed
        $videos = IslamicVideo::with('category')->latest()->paginate(10);
        return view('admin.islamic-videos.index', compact('videos'));
    }

    public function create()
    {
        $categories = \App\Models\Category::byType('kajian')->active()->get();
        return view('admin.islamic-videos.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'channel' => 'required|string|max:255',
            'video_url' => 'required|url',
            'duration' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        IslamicVideo::create($validated);

        return redirect()->route('admin.islamic-videos.index')
            ->with('success', 'Video Islami berhasil ditambahkan!');
    }

    public function edit(IslamicVideo $islamicVideo)
    {
        $categories = \App\Models\Category::byType('kajian')->active()->get();
        return view('admin.islamic-videos.edit', compact('islamicVideo', 'categories'));
    }

    public function update(Request $request, IslamicVideo $islamicVideo)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'channel' => 'required|string|max:255',
            'video_url' => 'required|url',
            'duration' => 'nullable|string|max:20',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        $islamicVideo->update($validated);

        return redirect()->route('admin.islamic-videos.index')
            ->with('success', 'Video Islami berhasil diperbarui!');
    }

    public function destroy(IslamicVideo $islamicVideo)
    {
        $islamicVideo->delete();
        return redirect()->route('admin.islamic-videos.index')
            ->with('success', 'Video Islami berhasil dihapus!');
    }
}