<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Circle;
use Illuminate\Http\Request;

class LeaderboardController extends Controller {
    public function users(Request $request) {
        $query = User::where('id', '!=', 1);

        if ($request->has('gender') && !empty($request->gender) && $request->gender !== 'all') {
            $query->where('gender', ucfirst(strtolower($request->gender)));
        }

        $users = $query->orderBy('level', 'desc')
            ->orderBy('current_exp', 'desc')
            ->limit(50)
            ->get(['id', 'username', 'level', 'current_exp', 'job_class', 'gender']);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function circles() {
        $circles = Circle::orderBy('level', 'desc')
            ->orderBy('xp', 'desc')
            ->limit(50)
            ->get(['id', 'name', 'level', 'xp', 'members_count', 'icon', 'color']);

        return response()->json([
            'success' => true,
            'data' => $circles
        ]);
    }
}
