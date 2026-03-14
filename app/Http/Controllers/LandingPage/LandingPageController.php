<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\LandingPageSection;
use App\Models\Headline;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LandingPageController extends Controller
{
    /**
     * Display the public landing page.
     */
    public function index()
    {
        $sections = LandingPageSection::where('is_active', true)
            ->orderBy('order')
            ->get();

        $latestNews = Headline::with('category')
            ->where('is_active', true)
            ->where('is_for_landing_page', true)
            ->latest()
            ->take(3)
            ->get();

        return Inertia::render('LandingPage', [
            'appName' => config('app.name'),
            'downloadUrl' => env('APK_DOWNLOAD_URL', '#'),
            'sections' => $sections,
            'latestNews' => $latestNews,
        ]);
    }

    /**
     * Admin: Display list of sections.
     */
    public function adminIndex()
    {
        $sections = LandingPageSection::orderBy('order')->get();
        return view('admin.landing-page.sections.index', compact('sections'));
    }

    /**
     * Admin: CRUD methods would go here...
     * For now, I'll focus on the public view as requested by user to "imagine it first"
     */
}
