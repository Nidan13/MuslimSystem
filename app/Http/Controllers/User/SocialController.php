<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialController extends Controller
{
    /**
     * Search users by username
     */
    public function search(Request $request)
    {
        $query = $request->query('q');
        if (empty($query)) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $users = User::where('username', 'like', "%{$query}%")
            ->where('id', '!=', Auth::id())
            ->where('id', '!=', 1) // Exclude Admin
            ->limit(20)
            ->get(['id', 'username', 'level', 'job_class']);

        // Append if current user is following them
        $followingIds = Auth::user()->following()->pluck('following_id')->toArray();
        $users->map(function ($user) use ($followingIds) {
            $user->is_following = in_array($user->id, $followingIds);
            return $user;
        });

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Follow a user
     */
    public function follow($id)
    {
        $userToFollow = User::findOrFail($id);
        
        if ($id == Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Cannot follow yourself'], 400);
        }

        if (Auth::user()->following()->where('following_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Already following this user'], 400);
        }

        Auth::user()->following()->attach($id);

        // Log activity for the follower
        \App\Models\ActivityLog::create([
            'user_id' => Auth::id(),
            'type' => 'social_follow',
            'amount' => 0,
            'description' => 'Mulai mengikuti ' . $userToFollow->username
        ]);

        // Create notification for the user who IS BEING followed
        \App\Models\Notification::create([
            'user_id' => $id, // Recipient
            'actor_id' => Auth::id(), // Triggerer
            'type' => 'follow',
            'data' => [
                'message' => Auth::user()->username . ' mulai mengikuti anda',
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User followed successfully'
        ]);
    }

    /**
     * Unfollow a user
     */
    public function unfollow($id)
    {
        if (!Auth::user()->following()->where('following_id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Not following this user'], 400);
        }

        Auth::user()->following()->detach($id);

        return response()->json([
            'success' => true,
            'message' => 'User unfollowed successfully'
        ]);
    }

    /**
     * Get followers list
     */
    public function followers($id)
    {
        $user = User::findOrFail($id);
        $followers = $user->followers()->get(['users.id', 'username', 'level', 'job_class', 'avatar']);
        
        $followingIds = Auth::user()->following()->pluck('following_id')->toArray();
        $followers->map(function ($follower) use ($followingIds) {
            $follower->is_following = in_array($follower->id, $followingIds);
            return $follower;
        });

        return response()->json([
            'success' => true,
            'data' => $followers
        ]);
    }

    /**
     * Get following list
     */
    public function following($id)
    {
        $user = User::findOrFail($id);
        $following = $user->following()->get(['users.id', 'username', 'level', 'job_class', 'avatar']);

        $followingIds = Auth::user()->following()->pluck('following_id')->toArray();
        $following->map(function ($follow) use ($followingIds) {
            $follow->is_following = in_array($follow->id, $followingIds);
            return $follow;
        });

        return response()->json([
            'success' => true,
            'data' => $following
        ]);
    }
}
