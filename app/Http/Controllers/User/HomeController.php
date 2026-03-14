<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\DailyTask;
use App\Models\PrayerLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\Headline;

class HomeController extends Controller
{
    /**
     * Get aggregated data for Home Screen
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Profile Data (Lightweight)
        // We ensure we load relationships needed for the header/avatar
        $user->load(['rankTier', 'userStat']);
        
        // --- NEW: Trigger Daily HP Reset (Full HP every day) ---
        app(\App\Http\Controllers\User\ProfileController::class)->checkDailyRestore($user);
        $user->refresh();
        
        // 2. Prayer Tasks (Mapped for Home Checklist)
        $today = now()->toDateString();
        $prayerNames = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
        
        $prayerLogs = PrayerLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('prayer_name');

        $mappedPrayers = collect($prayerNames)->map(function ($name) use ($prayerLogs) {
            $log = $prayerLogs->get($name);
            return [
                'id' => $name, 
                'name' => ucfirst($name),
                'is_completed' => $log ? $log->is_completed : false,
                'completed_at' => $log ? $log->completed_at : null,
                'soul_points' => 10,
            ];
        });

        $prayerCompletedCount = $mappedPrayers->where('is_completed', true)->count();
        $prayerSummary = [
            'completed_count' => $prayerCompletedCount,
            'total_count' => 5,
            'progress_percentage' => round($prayerCompletedCount / 5, 2),
        ];

        // 3. Prayer Global Trigger & Final Summary
        if (class_exists(\App\Http\Controllers\User\PrayerController::class)) {
            app(\App\Http\Controllers\User\PrayerController::class)->triggerPunishment($user);
        }

        // We use the already calculated $prayerSummary

        // 4. Notifications Count
        $unreadNotifCount = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // 5. Quran Progress Count
        $quranCompletedCount = DB::table('user_quran_progress')
            ->where('user_id', $user->id)
            ->where('is_completed', 1)
            ->count();
            
        // 6. Active Headlines
        $headlines = Headline::with('category')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Debug Log
        \Illuminate\Support\Facades\Log::info("Home Data for user {$user->id}: Quran Completed = {$quranCompletedCount}");

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'daily_tasks' => [
                    'tasks' => $mappedPrayers,
                    'summary' => $prayerSummary
                ],
                'prayer_summary' => $prayerSummary,
                'headlines' => $headlines,
                'notifications' => [
                    'unread_count' => $unreadNotifCount
                ],
                'quran' => [
                    'completed_surah_count' => $quranCompletedCount
                ]
            ]
        ]);
    }
}
