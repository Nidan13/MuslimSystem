<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Headline;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicNewsController extends Controller
{
    /**
     * Display all news articles.
     */
    public function index(Request $request)
    {
        $query = Headline::with('category')
            ->where('is_active', true)
            ->where('is_for_landing_page', true);

        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $news = $query->latest()
            ->paginate(9);

        // Fetch categories that have landing page news
        $categories = Category::byType('berita')
            ->active()
            ->withCount(['headlines as news_count' => function($q) {
                $q->where('is_active', true)->where('is_for_landing_page', true);
            }])
            ->get();

        return Inertia::render('News', [
            'news' => $news,
            'categories' => $categories,
            'selectedCategory' => $request->category,
        ]);
    }

    /**
     * Display a specific news article.
     */
    public function show($slug)
    {
        $article = Headline::with('category')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_for_landing_page', true)
            ->firstOrFail();
        
        $relatedQuery = Headline::where('id', '!=', $article->id)
            ->where('is_active', true)
            ->where('is_for_landing_page', true)
            ->with('category')
            ->limit(3)
            ->latest();

        if ($article->category_id) {
            $relatedNews = (clone $relatedQuery)
                ->where('category_id', $article->category_id)
                ->get();
            
            // Fallback to latest if not enough related in same category
            if ($relatedNews->count() < 3) {
                $ids = $relatedNews->pluck('id')->push($article->id);
                $fallback = (clone $relatedQuery)->whereNotIn('id', $ids)->limit(3 - $relatedNews->count())->get();
                $relatedNews = $relatedNews->concat($fallback);
            }
        } else {
            $relatedNews = $relatedQuery->get();
        }

        return Inertia::render('NewsDetail', [
            'article' => $article,
            'relatedNews' => $relatedNews,
        ]);
    }
}
