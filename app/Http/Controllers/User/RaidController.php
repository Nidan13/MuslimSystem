<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\Dungeon;
use App\Models\DungeonType;
use App\Models\CircleRaidParticipant;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RaidController extends Controller
{
    /**
     * Get available raids for a specific circle
     */
    public function index(Circle $circle)
    {
        // Get all available dungeons for this circle (regardless of solo/party/raid classification)
        // because the user wants 'Rift Gates' to appear in the circle section.
        $raids = Dungeon::where('min_level_requirement', '<=', $circle->level)
            ->with(['rankTier'])
            ->get()
            ->map(function($raid) use ($circle) {
                // Count real participants in this circle for this dungeon
                $participants = CircleRaidParticipant::where('circle_id', $circle->id)
                    ->where('dungeon_id', $raid->id)
                    ->get();
                
                $participantCount = $participants->count();
                $raid->current_players = $participantCount;
                
                // Determine status and progress
                // Check if anyone is already 'cleared' for this dungeon in this circle
                $isCleared = $participants->where('status', 'cleared')->isNotEmpty();
                
                if ($isCleared) {
                    $raid->status = 'cleared';
                    $raid->progress = 1.0;
                } else if ($participantCount >= $raid->required_players) {
                    $raid->status = 'in_progress';
                    
                    // Calculate progress based on collective contribution score
                    $totalContribution = $participants->sum('contribution_score');
                    if ($raid->objective_target > 0) {
                        $raid->progress = min(1.0, $totalContribution / $raid->objective_target);
                    } else {
                        $raid->progress = 0.1; // Default starting progress if no target
                    }
                } else if ($participantCount > 0) {
                    $raid->status = 'waiting';
                    $raid->progress = 0.0;
                } else {
                    $raid->status = 'open';
                    $raid->progress = 0.0;
                }

                $raid->is_participating = $participants->where('user_id', Auth::id())->isNotEmpty();
                
                $raid->rank = $raid->rankTier->slug ?? 'OPEN';
                return $raid;
            })
            // Filter out cleared raids from index (they move to "Misi Sukses")
            ->filter(fn($raid) => $raid->status !== 'cleared')
            ->values();

        return response()->json([
            'success' => true,
            'data' => $raids
        ]);
    }
    /**
     * Store a new dungeon (quest) created by circle leader
     */
    public function store(Request $request, Circle $circle)
    {
        // Only leader or co-leader can create quests
        $user = Auth::user();
        $member = $circle->members()->where('user_id', $user->id)->first();
        if (!$member || !in_array($member->pivot->role, ['leader', 'co-leader'])) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya Leader/Co-Leader yang bisa membuat Rift Gate!'
            ], 403);
        }

        $request->validate([
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'rank'             => 'nullable|string|in:OPEN,E,D,C,B,A,S',
            'min_level'        => 'nullable|integer|min:1',
            'required_players' => 'nullable|integer|min:1',
            'reward_exp'       => 'nullable|integer|min:1',
            'objective_type'   => 'nullable|string|in:prayer,sholat,quran,kajian,habit,journal,custom',
            'objective_target' => 'nullable|integer|min:1',
        ]);

        // Get raid dungeon type
        $raidType = DungeonType::where('slug', 'raid')->first();
        if (!$raidType) {
            return response()->json([
                'success' => false,
                'message' => 'Raid system not initialized. Contact admin.'
            ], 500);
        }

        // Get rank tier
        $rankTier = null;
        if ($request->rank && $request->rank !== 'OPEN') {
            $rankTier = \App\Models\RankTier::where('slug', $request->rank)->first();
            if (!$rankTier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rank tier not found.'
                ], 422);
            }
        }

        $objectiveType = $request->objective_type;
        if ($objectiveType === 'sholat') $objectiveType = 'prayer';

        $dungeon = Dungeon::create([
            'name'                  => $request->name,
            'description'           => $request->description ?? '',
            'dungeon_type_id'       => $raidType->id,
            'rank_tier_id'          => $rankTier->id ?? null,
            'min_level_requirement' => $request->min_level ?? 1,
            'required_players'      => $request->required_players ?? 2,
            'reward_exp'            => $request->reward_exp ?? 100,
            'objective_type'        => $objectiveType,
            'objective_target'      => $request->objective_target ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Rift Gate '{$dungeon->name}' berhasil dibuka!",
            'data'    => $dungeon
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

    /**
     * Get cleared/completed raids for a circle (Misi Sukses)
     */
    public function cleared(Circle $circle)
    {
        // Get all dungeons that have been cleared by this circle
        // A raid is 'cleared' when participant count >= required_players AND all are marked ready
        $clearedRaids = DB::table('dungeons')
            ->join('circle_raid_participants as crp', 'dungeons.id', '=', 'crp.dungeon_id')
            ->leftJoin('rank_tiers', 'dungeons.rank_tier_id', '=', 'rank_tiers.id')
            ->where('crp.circle_id', $circle->id)
            ->where('crp.status', 'cleared')
            ->select(
                'dungeons.id',
                'dungeons.name',
                'dungeons.description',
                'dungeons.reward_exp',
                'dungeons.min_level_requirement',
                'dungeons.required_players',
                'dungeons.objective_type',
                'dungeons.objective_target',
                'rank_tiers.name as rank',
                DB::raw('MIN(crp.updated_at) as cleared_at'),
                DB::raw('SUM(crp.contribution_score) as total_contribution')
            )
            ->groupBy(
                'dungeons.id', 'dungeons.name', 'dungeons.description',
                'dungeons.reward_exp', 'dungeons.min_level_requirement',
                'dungeons.required_players', 'dungeons.objective_type',
                'dungeons.objective_target', 'rank_tiers.name'
            )
            ->orderByDesc('cleared_at')
            ->get()
            ->map(function($raid) use ($circle) {
                // Get participants with their contribution scores
                $participants = CircleRaidParticipant::with('user:id,username,level')
                    ->where('circle_id', $circle->id)
                    ->where('dungeon_id', $raid->id)
                    ->where('status', 'cleared')
                    ->get()
                    ->map(fn($p) => [
                        'user' => $p->user,
                        'contribution_score' => $p->contribution_score,
                    ]);

                $raid->participants = $participants;
                $participation = CircleRaidParticipant::where('circle_id', $circle->id)
                    ->where('dungeon_id', $raid->id)
                    ->where('user_id', Auth::id())
                    ->first();
                
                $raid->is_participating = !empty($participation);
                $raid->is_rewarded = $participation ? (bool)$participation->is_rewarded : false;
                
                return $raid;
            });

        return response()->json([
            'success' => true,
            'data' => $clearedRaids
        ]);
    }

    public function claim(Circle $circle, Dungeon $dungeon)
    {
        $user = Auth::user();
        
        $participation = CircleRaidParticipant::where('circle_id', $circle->id)
            ->where('dungeon_id', $dungeon->id)
            ->where('user_id', $user->id)
            ->where('status', 'cleared')
            ->first();

        if (!$participation) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terlibat dalam misi ini atau misi belum selesai.'
            ], 403);
        }

        if ($participation->is_rewarded) {
            return response()->json([
                'success' => false,
                'message' => 'Hadiah sudah diklaim.'
            ], 400);
        }

        DB::transaction(function() use ($user, $dungeon, $participation, $circle) {
            // 1. Grant EXP
            $user->gainExp($dungeon->reward_exp);
            
            // 2. Grant Soul Points removed as per user request

            // 3. Mark as rewarded
            $participation->update(['is_rewarded' => true]);

            // 4. Activity Log
            ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'raid_reward_claimed',
                'amount' => $dungeon->reward_exp,
                'description' => "Klaim Hadiah Rift Gate: {$dungeon->name} (Circle: {$circle->name})"
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Hadiah berhasil diklaim!',
            'data' => [
                'exp_gained' => $dungeon->reward_exp,
                'new_exp' => $user->exp,
                'new_level' => $user->level
            ]
        ]);
    }
}
