<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class QuranController extends Controller
{
    /**
     * Get list of completed surah IDs for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $completedSurahs = DB::table('user_quran_progress')
            ->where('user_id', $user->id)
            ->where('is_completed', true)
            ->pluck('surah_id');

        return response()->json([
            'success' => true,
            'data' => $completedSurahs
        ]);
    }

    /**
     * Toggle completion status for a specific surah ID.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'surah_id' => 'required|integer|min:1|max:114',
        ]);

        $user = Auth::user();
        $surahId = $request->surah_id;

        $progress = DB::table('user_quran_progress')
            ->where('user_id', $user->id)
            ->where('surah_id', $surahId)
            ->first();

        if ($progress) {
            // Toggle
            $newStatus = !$progress->is_completed;
            DB::table('user_quran_progress')
                ->where('id', $progress->id)
                ->update([
                    'is_completed' => $newStatus,
                    'updated_at' => now(),
                ]);
        } else {
            // Create new entry
            $newStatus = true;
            DB::table('user_quran_progress')->insert([
                'user_id' => $user->id,
                'surah_id' => $surahId,
                'is_completed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'Surah marked as completed' : 'Surah marked as incomplete',
            'data' => [
                'surah_id' => $surahId,
                'is_completed' => $newStatus
            ]
        ]);
    }

    /**
     * Get user's recent reading history.
     */
    public function getHistory()
    {
        $user = Auth::user();
        $history = \App\Models\QuranReadingHistory::where('user_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($h) {
                return [
                    'surahNo' => $h->surah_no,
                    'ayahNo' => $h->ayah_no,
                    'surahName' => $h->surah_name,
                    'juzNo' => $h->juz_no,
                    'timestamp' => $h->updated_at->toIso8601String(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Save reading history (Ayah).
     */
    public function saveHistory(Request $request)
    {
        $request->validate([
            'surah_no' => 'required|integer|min:1|max:114',
            'ayah_no' => 'required|integer|min:1',
            'surah_name' => 'required|string',
            'juz_no' => 'nullable|integer',
        ]);

        $user = Auth::user();

        // Check if exact ayah was already saved
        $existing = \App\Models\QuranReadingHistory::where('user_id', $user->id)
            ->where('surah_no', $request->surah_no)
            ->where('ayah_no', $request->ayah_no)
            ->first();

        if ($existing) {
            $existing->touch(); // Just update updated_at to move it to top
        } else {
            \App\Models\QuranReadingHistory::create([
                'user_id' => $user->id,
                'surah_no' => $request->surah_no,
                'ayah_no' => $request->ayah_no,
                'surah_name' => $request->surah_name,
                'juz_no' => $request->juz_no,
            ]);

            // Auto-complete Quran Daily Task & Give XP
            $quranTaskId = \App\Models\DailyTask::where(function($q) {
                $q->where('name', 'like', '%Quran%')
                  ->orWhere('name', 'like', '%Ngaji%');
            })->value('id');

            if ($quranTaskId) {
                $today = now()->toDateString();
                $alreadyDone = \App\Models\UserDailyTask::where('user_id', $user->id)
                    ->where('daily_task_id', $quranTaskId)
                    ->where('date', $today)
                    ->where('is_completed', true)
                    ->exists();

                if (!$alreadyDone) {
                    \App\Models\UserDailyTask::updateOrCreate(
                        ['user_id' => $user->id, 'daily_task_id' => $quranTaskId, 'date' => $today],
                        ['is_completed' => true]
                    );

                    $task = \App\Models\DailyTask::find($quranTaskId);
                    $exp = $task ? ($task->reward_exp ?? 20) : 20;
                    $user->gainExp($exp);
                    
                    // Award some Soul Points/Gold logic can also go here if needed
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'History saved successfully'
        ]);
    }
}
