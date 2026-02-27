<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\Dungeon;
use App\Models\DungeonType;
use App\Models\CircleRaidParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaidController extends Controller
{
    /**
     * Get available raids for a specific circle
     */
    public function index(Circle $circle)
    {
        $raidType = DungeonType::where('slug', 'raid')->first();
        
        if (!$raidType) {
            return response()->json([
                'success' => false,
                'message' => 'Raid system not initialized'
            ], 404);
        }

        // Get dungeons of type 'raid'
        $raids = Dungeon::where('dungeon_type_id', $raidType->id)
            ->where('min_level_requirement', '<=', $circle->level)
            ->with(['rankTier'])
            ->get()
            ->map(function($raid) use ($circle) {
                // Count real participants in this circle for this dungeon
                $participantCount = CircleRaidParticipant::where('circle_id', $circle->id)
                    ->where('dungeon_id', $raid->id)
                    ->count();
                
                $raid->current_players = $participantCount;
                
                // Determine status based on player count
                if ($participantCount >= $raid->required_players) {
                    $raid->status = 'in_progress';
                    $raid->progress = 0.1; // Starting progress
                } else if ($participantCount > 0) {
                    $raid->status = 'waiting';
                    $raid->progress = 0.0;
                } else {
                    $raid->status = 'open';
                    $raid->progress = 0.0;
                }
                
                $raid->rank = $raid->rankTier->name ?? 'E';
                return $raid;
            });

        return response()->json([
            'success' => true,
            'data' => $raids
        ]);
    }

    /**
     * Join a dungeon raid lobby
     */
    public function joinLobby(Circle $circle, Dungeon $dungeon)
    {
        $user = Auth::user();
        
        // Ensure user is member of this circle
        if (!$circle->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this Circle!'
            ], 403);
        }

        // Check if already in lobby
        $existing = CircleRaidParticipant::where('circle_id', $circle->id)
            ->where('dungeon_id', $dungeon->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'You are already in the lobby!'
            ]);
        }

        // Check if full
        $currentCount = CircleRaidParticipant::where('circle_id', $circle->id)
            ->where('dungeon_id', $dungeon->id)
            ->count();

        if ($currentCount >= $dungeon->required_players) {
            return response()->json([
                'success' => false,
                'message' => 'Lobby is full! The Gate is closing!'
            ], 400);
        }

        // Join
        CircleRaidParticipant::create([
            'circle_id' => $circle->id,
            'dungeon_id' => $dungeon->id,
            'user_id' => $user->id,
            'status' => 'ready'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entered the Gate Registry. Waiting for other hunters...'
        ]);
    }
}
