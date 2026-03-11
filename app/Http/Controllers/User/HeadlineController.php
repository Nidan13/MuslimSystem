<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Headline;
use Illuminate\Http\Request;

class HeadlineController extends Controller
{
    /**
     * Get all active headlines
     */
    public function index()
    {
        $headlines = Headline::with('category')
            ->where('is_active', true)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $headlines
        ]);
    }

    /**
     * Get headline detail
     */
    public function show($id)
    {
        $headline = Headline::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $headline
        ]);
    }
}
