<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\IslamicVideo;
use Illuminate\Http\Request;

class IslamicVideoController extends Controller
{
    public function index()
    {
        $videos = IslamicVideo::where('is_active', true)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $videos->map(function ($video) {
                return [
                    'title' => $video->title,
                    'channel' => $video->channel,
                    'videoId' => $video->video_id,
                    'duration' => $video->duration ?? '00:00',
                    'category' => $video->category,
                ];
            }),
        ]);
    }
}
