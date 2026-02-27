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
}
