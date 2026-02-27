<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CircleController extends Controller {
    public function index() {
        $user = Auth::user();
        $circles = Circle::all()->map(function ($circle) use ($user) {
            $circle->is_joined = $user ? $circle->members()->where('user_id', $user->id)->exists() : false;
            return $circle;
        });

        return response()->json([
            'success' => true,
            'data' => $circles
        ]);
    }

    public function myCircles() {
        $user = Auth::user();
        $circles = $user->joinedCircles()->get()->map(function($circle) {
            $circle->is_joined = true;
            return $circle;
        });
        
        return response()->json([
            'success' => true,
            'data' => $circles
        ]);
    }

    public function show(Circle $circle) {
        $circle->load(['members' => function($query) {
            $query->select('users.id', 'users.username', 'users.job_class', 'users.level')
                  ->withPivot('role', 'joined_at', 'xp_contribution');
        }, 'leader:id,username,job_class,level']);

        $user = Auth::user();
        if ($user) {
            $member = $circle->members()->where('user_id', $user->id)->first();
            $circle->is_joined = $member ? true : false;
            $circle->my_role = $member ? $member->pivot->role : null;
        } else {
            $circle->is_joined = false;
            $circle->my_role = null;
        }

        // Calculate Rank
        $circle->rank = Circle::where('level', '>', $circle->level)
            ->orWhere(function($q) use ($circle) {
                $q->where('level', $circle->level)
                  ->where('xp', '>', $circle->xp);
            })->count() + 1;

        return response()->json([
            'success' => true,
            'data' => $circle
        ]);
    }

    public function join(Circle $circle) {
        $user = Auth::user();
        if (!$circle->members()->where('user_id', $user->id)->exists()) {
            $circle->members()->attach($user->id, ['joined_at' => now()]);
            $circle->increment('members_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil bergabung dengan Circle ' . $circle->name
        ]);
    }

    public function leave(Circle $circle) {
        $user = Auth::user();
        if ($circle->members()->where('user_id', $user->id)->exists()) {
            $circle->members()->detach($user->id);
            $circle->decrement('members_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar dari Circle ' . $circle->name
        ]);
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'invited_user_ids' => 'nullable|array',
            'invited_user_ids.*' => 'exists:users,id',
        ]);

        $user = Auth::user();

        // 👮 Check Requirement: 1000 EXP Cost
        if ($user->current_exp < 1000) {
            return response()->json([
                'success' => false,
                'message' => 'EXP tidak mencukupi! Butuh 1000 EXP untuk membuat Circle.'
            ], 403);
        }

        return \Illuminate\Support\Facades\DB::transaction(function() use ($request, $user) {
            // 🔥 Burn 1000 EXP
            $user->decrement('current_exp', 1000);
            
            // 📝 Log Activity
            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'xp_burn',
                'amount' => 1000,
                'description' => "Membuat Circle '{$request->name}' (-1000 EXP)"
            ]);

            $circle = Circle::create([
                'name' => $request->name,
                'description' => $request->description,
                'icon' => $request->icon ?? 'groups',
                'color' => $request->color ?? '#0E5E6F',
                'leader_id' => $user->id,
                'members_count' => 1, // Leader starts as member
            ]);

            // Leader joins automatically
            $circle->members()->attach($user->id, ['role' => 'leader', 'joined_at' => now()]);

            // Invite initial members
            if ($request->has('invited_user_ids')) {
                foreach ($request->invited_user_ids as $invitedId) {
                    if ($invitedId != $user->id) {
                        $circle->members()->attach($invitedId, ['role' => 'member', 'joined_at' => now()]);
                        $circle->increment('members_count');
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Circle '{$circle->name}' berhasil dibuat! 1000 EXP telah dipotong.",
                'data' => $circle
            ]);
        });
    }

    public function searchUsers(Request $request) {
        $query = $request->query('q');
        $users = \App\Models\User::where('username', 'LIKE', "%{$query}%")
            ->where('id', '!=', Auth::id())
            ->limit(10)
            ->get(['id', 'username', 'job_class', 'level']);

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function promote(Request $request, Circle $circle) {
        // 👮 Security Check: Only Leader can promote/demote
        if (Auth::id() !== $circle->leader_id) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Leader yang berhak nentuin jabatan, wok!'
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:leader,co-leader,member'
        ]);

        // Leader can't demote themselves via this endpoint (usually)
        if ($request->user_id == $circle->leader_id) {
            return response()->json(['success' => false, 'message' => 'Leader status cannot be changed here.'], 403);
        }

        $circle->members()->updateExistingPivot($request->user_id, [
            'role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User role updated successfully.'
        ]);
    }
}
