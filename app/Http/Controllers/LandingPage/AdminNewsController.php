<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\Headline;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AdminNewsController extends Controller
{
    public function index()
    {
        $news = Headline::with('category')->latest()->paginate(10);
        return view('admin.landing-page.news.index', compact('news'));
    }

    public function create()
    {
        $categories = Category::where('type', 'berita')->get();
        return view('admin.landing-page.news.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'summary'             => 'required|string',
            'content'             => 'required|string',
            'category_id'         => 'required|exists:categories,id',
            'image'               => 'nullable|image|max:4096',
            'extra_image_1'       => 'nullable|image|max:4096',
            'extra_image_2'       => 'nullable|image|max:4096',
            'is_active'           => 'nullable|boolean',
            'is_for_user'         => 'nullable|boolean',
            'is_for_landing_page' => 'nullable|boolean',
        ]);

        $item = new Headline($validated);
        $item->slug = Str::slug($validated['title']) . '-' . rand(1000, 9999);
        $item->is_for_user = $request->has('is_for_user');
        $item->is_active = $request->has('is_active');
        $item->is_for_landing_page = $request->has('is_for_landing_page');
        $item->tag = 'Warta';

        if ($request->hasFile('image')) {
            $item->image_url = '/storage/' . $request->file('image')->store('headlines/covers', 'public');
        }

        $extraImages = [];
        if ($request->hasFile('extra_image_1')) {
            $extraImages[] = '/storage/' . $request->file('extra_image_1')->store('headlines/extras', 'public');
        }
        if ($request->hasFile('extra_image_2')) {
            $extraImages[] = '/storage/' . $request->file('extra_image_2')->store('headlines/extras', 'public');
        }
        $item->images = $extraImages;

        $item->save();

        return redirect()->route('admin.landing-page.news.index')->with('success', 'Warta berhasil dipublikasikan.');
    }

    public function edit($id)
    {
        $news = Headline::findOrFail($id);
        $categories = Category::where('type', 'berita')->get();
        return view('admin.landing-page.news.edit', compact('news', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $item = Headline::findOrFail($id);

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'summary'             => 'required|string',
            'content'             => 'required|string',
            'category_id'         => 'required|exists:categories,id',
            'image'               => 'nullable|image|max:4096',
            'extra_image_1'       => 'nullable|image|max:4096',
            'extra_image_2'       => 'nullable|image|max:4096',
            'is_active'           => 'nullable|boolean',
            'is_for_user'         => 'nullable|boolean',
            'is_for_landing_page' => 'nullable|boolean',
            'remove_images'       => 'nullable|array'
        ]);

        $item->fill($validated);
        $item->is_active = $request->has('is_active');
        $item->is_for_user = $request->has('is_for_user');
        $item->is_for_landing_page = $request->has('is_for_landing_page');

        if ($request->hasFile('image')) {
            if ($item->getRawOriginal('image_url') && !str_starts_with($item->getRawOriginal('image_url'), 'http')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $item->getRawOriginal('image_url')));
            }
            $item->image_url = '/storage/' . $request->file('image')->store('headlines/covers', 'public');
        }

        $currentImages = $item->images ?? [];
        if ($request->remove_images) {
            foreach ($request->remove_images as $img) {
                if (!str_starts_with($img, 'http')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $img));
                }
                $currentImages = array_filter($currentImages, fn($i) => $i !== $img);
            }
        }

        if ($request->hasFile('extra_image_1')) {
            $currentImages[] = '/storage/' . $request->file('extra_image_1')->store('headlines/extras', 'public');
        }
        if ($request->hasFile('extra_image_2')) {
            $currentImages[] = '/storage/' . $request->file('extra_image_2')->store('headlines/extras', 'public');
        }
        
        $item->images = array_values($currentImages);
        $item->save();

        return redirect()->route('admin.landing-page.news.index')->with('success', 'Warta berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $news = Headline::findOrFail($id);
        if ($news->image_url && !str_starts_with($news->image_url, 'http')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $news->image_url));
        }
        if ($news->images) {
            foreach ($news->images as $img) {
                if (!str_starts_with($img, 'http')) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $img));
                }
            }
        }
        $news->delete();
        return redirect()->route('admin.landing-page.news.index')->with('success', 'Warta berhasil dihapus.');
    }
}
