<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSection;
use App\Models\Headline;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSectionController extends Controller
{
    /**
     * Display the admin master hub for landing page.
     */
    public function hub()
    {
        $sectionCount = LandingPageSection::count();
        $newsCount = Headline::where('is_for_landing_page', true)->count();
        $categoryCount = Category::byType('berita')->active()->count();

        $theme = [
            'primary' => Setting::get('landing_page_primary_color', '#008b76'),
            'navbar' => Setting::get('landing_page_navbar_color', '#008b76'),
            'footer' => Setting::get('landing_page_footer_color', '#0a2f4c'),
        ];

        return view('admin.landing-page.index', compact('sectionCount', 'newsCount', 'categoryCount', 'theme'));
    }

    /**
     * Update landing page theme colors.
     */
    public function updateTheme(Request $request)
    {
        $request->validate([
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'navbar_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'footer_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        Setting::set('landing_page_primary_color', $request->primary_color);
        Setting::set('landing_page_navbar_color', $request->navbar_color);
        Setting::set('landing_page_footer_color', $request->footer_color);

        return redirect()->back()->with('success', 'Tema warna global berhasil diperbarui.');
    }

    public function index()
    {
        $sections = LandingPageSection::orderBy('order')->get();
        return view('admin.landing-page.sections.index', compact('sections'));
    }

    public function create()
    {
        return view('admin.landing-page.sections.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|string',
            'style' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'order' => 'integer',
            'image_url' => 'nullable|image|max:2048',
            'items' => 'nullable|array',
            'items.*.title' => 'nullable|string',
            'items.*.description' => 'nullable|string',
            'items.*.icon' => 'nullable|string',
            'items.*.button_text' => 'nullable|string',
            'items.*.button_url' => 'nullable|string',
            'items.*.image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image_url')) {
            $path = $request->file('image_url')->store('landing-page', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        if (empty($validated['style'])) {
            $validated['style'] = 'default';
        }

        if (isset($validated['items']) && is_array($validated['items'])) {
            $processedItems = [];
            foreach ($validated['items'] as $index => $itemData) {
                if ($request->hasFile("items.{$index}.image")) {
                    $path = $request->file("items.{$index}.image")->store('landing-page/items', 'public');
                    $itemData['image_url'] = '/storage/' . $path;
                }
                unset($itemData['image']); // Remove the uploaded file object from the array
                $processedItems[] = $itemData;
            }
            $validated['items'] = $processedItems;
        } else {
            $validated['items'] = null;
        }

        LandingPageSection::create($validated);

        return redirect()->route('admin.landing-page.sections.index')
            ->with('success', 'Section berhasil inisialisasi.');
    }

    public function edit(LandingPageSection $section)
    {
        return view('admin.landing-page.sections.edit', compact('section'));
    }

    public function update(Request $request, LandingPageSection $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|string',
            'style' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|string|max:255',
            'order' => 'integer',
            'image_url' => 'nullable|image|max:2048',
            'items' => 'nullable|array',
            'items.*.title' => 'nullable|string',
            'items.*.description' => 'nullable|string',
            'items.*.icon' => 'nullable|string',
            'items.*.button_text' => 'nullable|string',
            'items.*.button_url' => 'nullable|string',
            'items.*.image' => 'nullable|image|max:2048',
            'items.*.old_image' => 'nullable|string',
        ]);

        if ($request->hasFile('image_url')) {
            if ($section->image_url) {
                $oldPath = str_replace('/storage/', '', $section->image_url);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image_url')->store('landing-page', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        if (empty($validated['style'])) {
            $validated['style'] = 'default';
        }

        if (isset($validated['items']) && is_array($validated['items'])) {
            $processedItems = [];
            foreach ($validated['items'] as $index => $itemData) {
                if ($request->hasFile("items.{$index}.image")) {
                    $path = $request->file("items.{$index}.image")->store('landing-page/items', 'public');
                    $itemData['image_url'] = '/storage/' . $path;
                    
                    // Delete old item image if it exists and is replaced
                    if (!empty($itemData['old_image'])) {
                        $oldItemPath = str_replace('/storage/', '', $itemData['old_image']);
                        Storage::disk('public')->delete($oldItemPath);
                    }
                } else {
                    // Retain old image if no new image is uploaded
                    if (!empty($itemData['old_image'])) {
                        $itemData['image_url'] = $itemData['old_image'];
                    }
                }
                unset($itemData['image']);
                unset($itemData['old_image']);
                $processedItems[] = $itemData;
            }
            $validated['items'] = $processedItems;
        } else {
            $validated['items'] = null;
        }

        $section->update($validated);

        return redirect()->route('admin.landing-page.sections.index')
            ->with('success', 'Section berhasil diperbarui.');
    }

    public function destroy(LandingPageSection $section)
    {
        if ($section->image_url) {
            $oldPath = str_replace('/storage/', '', $section->image_url);
            Storage::disk('public')->delete($oldPath);
        }
        $section->delete();
        return redirect()->route('admin.landing-page.sections.index')
            ->with('success', 'Section berhasil dihapus.');
    }
}
