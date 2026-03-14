<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            'downloadUrl' => env('APK_DOWNLOAD_URL', '#'),
            'auth' => [
                'user' => $request->user()?->load('rankTier'),
            ],
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
            'theme' => [
                'primary' => \App\Models\Setting::get('landing_page_primary_color', '#008b76'),
                'navbar' => \App\Models\Setting::get('landing_page_navbar_color', '#008b76'),
                'footer' => \App\Models\Setting::get('landing_page_footer_color', '#0a2f4c'),
            ],
        ];
    }
}
