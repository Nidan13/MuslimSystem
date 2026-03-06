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
                    'id' => $video->id,
                    'title' => $video->title,
                    'channel' => $video->channel,
                    'videoId' => $video->video_id,
                    'duration' => $video->duration ?? '00:00',
                    'category' => $video->category,
                ];
            }),
        ]);
    }

    public function logCompletion(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:islamic_videos,id',
        ]);

        $user = $request->user();
        
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_lecture_logs')) {
            return response()->json([
                'success' => false,
                'message' => 'Layanan track kajian belum siap (Migration missing).',
            ]);
        }

        // One-time completion: check if user has EVER completed this video
        $exists = \App\Models\UserLectureLog::where('user_id', $user->id)
            ->where('islamic_video_id', $request->video_id)
            ->exists();

        if (!$exists) {
            \App\Models\UserLectureLog::create([
                'user_id' => $user->id,
                'islamic_video_id' => $request->video_id,
                'watched_at' => now(),
            ]);

            // Give some rewards
            $user->gainExp(50); // Studying gives 50 EXP

            // Trigger Rift Gate Progress
            $user->updateRiftGateProgress('kajian', 1);
            
            return response()->json([
                'success' => true,
                'message' => 'Kajian berhasil dicatat. +50 EXP',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kajian sudah dicatat sebelumnya.',
        ]);
    }
}
