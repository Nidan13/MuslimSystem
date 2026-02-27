<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HabitController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $habits = Habit::where('user_id', $user->id)->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $habits
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'difficulty' => 'required|in:trivial,easy,medium,hard',
            'is_positive' => 'required|boolean',
            'is_negative' => 'required|boolean',
            'frequency' => 'required|in:daily,weekly,monthly',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $user = Auth::user();

        $habit = Habit::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'notes' => $request->notes,
            'difficulty' => $request->difficulty,
            'is_positive' => $request->is_positive,
            'is_negative' => $request->is_negative,
            'frequency' => $request->frequency,
            'icon' => $request->icon,
            'color' => $request->color,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Habit created successfully',
            'data' => $habit
        ], 201);
    }

    public function score(Request $request, $id)
    {
        $request->validate([
            'direction' => 'required|in:up,down'
        ]);

        $user = Auth::user();
        $habit = Habit::where('user_id', $user->id)->findOrFail($id);
        $direction = $request->direction;

        // Reward/Penalty values based on difficulty
        $rewards = [
            'trivial' => ['xp' => 5, 'sp' => 2, 'hp_loss' => 1],
            'easy'    => ['xp' => 10, 'sp' => 5, 'hp_loss' => 2],
            'medium'  => ['xp' => 20, 'sp' => 10, 'hp_loss' => 4],
            'hard'    => ['xp' => 40, 'sp' => 20, 'hp_loss' => 8],
        ];

        $currentReward = $rewards[$habit->difficulty];
        $msg = "";
        $xpGained = 0;
        $spGained = 0;
        $hpLost = 0;

        DB::transaction(function () use ($user, $habit, $direction, $currentReward, &$msg, &$xpGained, &$spGained, &$hpLost) {
            if ($direction === 'up' && $habit->is_positive) {
                $xpGained = $currentReward['xp'];
                $spGained = $currentReward['sp'];
                
                $user->increment('soul_points', $spGained);
                $habit->count += 1;
                
                // Use gainExp to handle XP, Level, Rank, and Circle XP
                $user->gainExp($xpGained);

                $msg = "Kebiasaan positif! +$xpGained XP, +$spGained SP" . ($user->joinedCircles()->exists() ? " & +$xpGained Clan XP 🛡️" : "");
            } elseif ($direction === 'down' && $habit->is_negative) {
                $hpLost = $currentReward['hp_loss'];
                $user->hp = max(0, $user->hp - $hpLost);
                $habit->count -= 1; // Or just track separately, but for now we follow UI count
                
                $msg = "Kebiasaan negatif! -$hpLost HP";
            }

            $user->save();
            $habit->save();
        });

        return response()->json([
            'success' => true,
            'message' => $msg,
            'data' => [
                'current_hp' => $user->hp,
                'current_xp' => $user->current_exp,
                'current_sp' => $user->soul_points,
                'habit_count' => $habit->count,
                'xp_gained' => $xpGained,
                'sp_gained' => $spGained,
                'hp_lost' => $hpLost,
            ]
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $habit = Habit::where('user_id', $user->id)->findOrFail($id);
        $habit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Habit deleted successfully'
        ]);
    }
}
