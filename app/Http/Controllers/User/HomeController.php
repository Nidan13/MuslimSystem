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
        
        // 2. Daily Tasks & Summary
        // Reusing logic from DailyTaskController::index but optimized
        $date = $request->query('date', now()->toDateString());
        // For read-only home view, we just return empty or what exists. 
        // 2. Daily Tasks & Summary
        // Fetch ACTIVE Custom Tasks (Definitions)
        $tasks = DailyTask::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        // Fetch Completions for today
        $taskIds = $tasks->pluck('id');
        $completions = \App\Models\UserDailyTask::where('user_id', $user->id)
            ->whereIn('daily_task_id', $taskIds)
            ->where('date', $date)
            ->get()
            ->keyBy('daily_task_id');

        // Map tasks to include completion status
        $mappedTasks = $tasks->map(function ($task) use ($completions) {
            $completion = $completions->get($task->id);
            // Add transient properties for frontend
            $task->is_completed = !is_null($completion);
            $task->completed_at = $completion?->completed_at;
            return $task;
        });

        $completedCount = $mappedTasks->where('is_completed', true)->count();
        $totalCount = $mappedTasks->count();
        $totalPoints = $mappedTasks->sum('soul_points');
        $earnedPoints = $mappedTasks->where('is_completed', true)->sum('soul_points');
        
        $taskSummary = [
            'completed_count' => $completedCount,
            'total_count' => $totalCount,
            'earned_points' => $earnedPoints,
            'total_points' => $totalPoints,
            'progress_percentage' => $totalCount > 0 ? round($completedCount / $totalCount, 2) : 0,
        ];

        // 3. Prayer Summary
        $today = now()->toDateString();
        // Database ENUM: 'subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'
        $prayerNames = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
        
        // --- NEW: Apply punishments before showing summary ---
        // (Assuming you want to trigger punishment whenever user checks home)
        if (class_exists(\App\Http\Controllers\User\PrayerController::class)) {
            app(\App\Http\Controllers\User\PrayerController::class)->triggerPunishment($user);
        }

        $logs = PrayerLog::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('prayer_name');
            
        $prayerCompletedCount = 0;
        foreach ($prayerNames as $name) {
            if (isset($logs[$name]) && $logs[$name]->is_completed) {
                $prayerCompletedCount++;
            }
        }
        $prayerSummary = [
            'completed_count' => $prayerCompletedCount,
            'total_count' => 5, // Fixed 5 mandatory prayers
        ];

        // 4. Notifications Count
        $unreadNotifCount = Notification::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // 5. Quran Progress Count
        // Ensure we only count unique surahs
        $quranCompletedCount = DB::table('user_quran_progress')
            ->where('user_id', $user->id)
            ->where('is_completed', 1) // Explicitly use 1
            ->count(); // Simplify first to see if distinct is the issue
            
        // Debug Log
        \Illuminate\Support\Facades\Log::info("Home Data for user {$user->id}: Quran Completed = {$quranCompletedCount}");

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'daily_tasks' => [
                    'tasks' => $mappedTasks,
                    'summary' => $taskSummary
                ],
                'prayer_summary' => $prayerSummary,
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
