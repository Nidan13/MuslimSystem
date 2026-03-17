<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityController extends Controller
{
    public function log(Request $request)
    {
        $request->validate([
            'page_name' => 'required|string',
            'seconds' => 'nullable|integer',
            'seconds_spent' => 'nullable|integer'
        ]);

        $seconds = $request->input('seconds') ?? $request->input('seconds_spent') ?? 0;
        if ($seconds <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Invalid duration'], 422);
        }

        $user = $request->user();
        $today = now()->toDateString();

        // Use firstOrCreate then increment to avoid database locks and handle raw expressions safely
        $activity = UserActivity::firstOrCreate(
            [
                'user_id' => $user->id,
                'page_name' => $request->page_name,
                'active_date' => $today
            ],
            [
                'seconds_spent' => 0
            ]
        );

        $activity->increment('seconds_spent', $seconds);

        return response()->json([
            'status' => 'success',
            'message' => 'Activity logged successfully'
        ]);
    }
}
