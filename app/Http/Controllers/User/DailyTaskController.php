<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\UserDailyTask;
use App\Models\PrayerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DailyTaskController extends Controller
{
    /**
     * Get today's custom daily tasks with completion status
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $date = $request->query('date', now()->toDateString());
        $today = now()->toDateString();
        
        // Only get tasks belonging to the user (Custom Tasks)
        // Master tasks (Sholat) are now handled by PrayerController
        $tasks = DailyTask::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();
            
        $taskIds = $tasks->pluck('id');

        // Fetch completions for the specific date
        $completions = UserDailyTask::where('user_id', $user->id)
            ->whereIn('daily_task_id', $taskIds)
            ->where('date', $date)
            ->get()
            ->keyBy('daily_task_id');
        
        $mappedTasks = $tasks->map(function ($task) use ($completions) {
            $completion = $completions->get($task->id);
            
            return [
                'id' => $task->id,
                'name' => $task->name,
                'description' => $task->description,
                'soul_points' => $task->soul_points,
                'icon' => $task->icon,
                'is_completed' => !is_null($completion),
                'completed_at' => $completion?->completed_at,
                'is_custom' => true,
            ];
        });
        
        $completedCount = $mappedTasks->where('is_completed', true)->count();
        $totalCount = $mappedTasks->count();
        $earnedPoints = $mappedTasks->where('is_completed', true)->sum('soul_points');
        $totalPoints = $mappedTasks->sum('soul_points');
        
        // --- MISSING TASK PENALTY ---
        // Refactored to check Prayer Logs for mandatory activities
        if ($date === $today) {
            $this->applyMissedPrayerPenalty($user);
            $user->refresh();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tasks' => $mappedTasks->values(),
                'summary' => [
                    'completed_count' => $completedCount,
                    'total_count' => $totalCount,
                    'earned_points' => $earnedPoints,
                    'total_points' => $totalPoints,
                    'progress_percentage' => $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 2) : 0,
                    'hp_remaining' => $user->hp,
                ],
            ],
        ]);
    }

    /**
     * Deduct 2 HP for each missed obligatory prayer yesterday
     */
    private function applyMissedPrayerPenalty($user)
    {
        // 🚺 Skip penalty if currently menstruating
        if ($user->is_menstruating) {
            return;
        }

        $yesterday = now()->subDay()->toDateString();
        
        if ($user->created_at->startOfDay()->gt(Carbon::parse($yesterday)->startOfDay())) {
            return;
        }
        
        $cacheKey = "prayer_penalty_check_{$user->id}_{$yesterday}";
        if (cache()->has($cacheKey)) {
            return;
        }

        $prayers = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];
        $completedPrayers = PrayerLog::where('user_id', $user->id)
            ->where('date', $yesterday)
            ->where('is_completed', true)
            ->count();
        
        $missedCount = count($prayers) - $completedPrayers;

        if ($missedCount > 0) {
            // Deduct 5 HP per missed prayer as requested by user
            $newHp = max(0, $user->hp - ($missedCount * 5));
            $user->update(['hp' => $newHp]);
        }

        cache()->put($cacheKey, true, now()->addDays(2));
    }

    /**
     * Store new custom task
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        $customTaskCount = DailyTask::where('user_id', $user->id)->count();
        if ($customTaskCount >= 20) { // Limit increased for journals
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 20 jurnal kustom! Hapus jurnal lama untuk menambah yang baru.',
            ], 422);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'soul_points' => 'required|integer|min:5|max:100',
            'icon' => 'nullable|string|max:50',
        ]);
        
        $task = DailyTask::create([
            'user_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'soul_points' => $validated['soul_points'],
            'icon' => $validated['icon'] ?? '📝',
            'is_active' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil ditambahkan!',
            'data' => [
                'task' => [
                    'id' => $task->id,
                    'name' => $task->name,
                    'is_custom' => true,
                ],
            ],
        ], 201);
    }

    /**
     * Update custom task
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $task = DailyTask::where('user_id', $user->id)->find($id);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal tidak ditemukan.',
            ], 404);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'soul_points' => 'required|integer|min:5|max:100',
            'icon' => 'nullable|string|max:10',
        ]);
        
        $task->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil diupdate!',
        ]);
    }

    /**
     * Delete custom task
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $task = DailyTask::where('user_id', $user->id)->find($id);
        
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal tidak ditemukan.',
            ], 404);
        }
        
        $task->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Jurnal berhasil dihapus!',
        ]);
    }

    /**
     * Mark a daily task as complete
     */
    public function complete(Request $request, $taskId)
    {
        $user = $request->user();
        $today = now()->toDateString();
        
        $dailyTask = DailyTask::where('user_id', $user->id)->find($taskId);
        
        if (!$dailyTask) {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal tidak ditemukan.',
            ], 404);
        }
        
        $existing = UserDailyTask::where('user_id', $user->id)
            ->where('daily_task_id', $taskId)
            ->where('date', $today)
            ->first();
        
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Jurnal sudah diselesaikan hari ini!',
            ], 422);
        }
        
        DB::transaction(function () use ($user, $dailyTask, $today) {
            UserDailyTask::create([
                'user_id' => $user->id,
                'daily_task_id' => $dailyTask->id,
                'completed_at' => now(),
                'date' => $today,
            ]);
            
            $user->increment('soul_points', $dailyTask->soul_points);
            $user->gainExp($dailyTask->soul_points);
        });
        
        return response()->json([
            'success' => true,
            'message' => "Jurnal selesai! +{$dailyTask->soul_points} SP",
            'data' => [
                'soul_points_earned' => $dailyTask->soul_points,
                'total_soul_points' => $user->refresh()->soul_points,
            ],
        ]);
    }

    /**
     * Mark a daily task as uncomplete
     */
    public function uncomplete(Request $request, $taskId)
    {
        $user = $request->user();
        $today = now()->toDateString();
        
        $completion = UserDailyTask::where('user_id', $user->id)
            ->where('daily_task_id', $taskId)
            ->where('date', $today)
            ->first();
            
        if ($completion) {
            $completion->delete();
        }

        return response()->json([
            'success' => true,
            'message' => "Status jurnal diubah.",
        ]);
    }
}
