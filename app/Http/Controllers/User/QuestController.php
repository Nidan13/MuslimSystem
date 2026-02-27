<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\UserQuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestController extends Controller
{
    /**
     * Get list of quests (optionally filtered by type)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $typeSlug = $request->query('type', 'daily'); // Default daily

        $questType = QuestType::where('slug', $typeSlug)->first();

        if (!$questType) {
            return response()->json([
                'success' => false,
                'message' => 'Quest type not found',
            ], 404);
        }

        // Get quests of this type available
        $now = now('Asia/Jakarta');
        $currentTime = $now->toTimeString();

        $quests = Quest::where('quest_type_id', $questType->id)
            ->where(function ($query) use ($now, $currentTime) {
                // Time-based visibility logic
                $query->where(function ($q) use ($now) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
                })
                ->where(function ($q) use ($currentTime) {
                    $q->where(function ($sq) use ($currentTime) {
                        // Regular range (e.g., 08:00 - 17:00)
                        $sq->whereColumn('start_time', '<=', 'end_time')
                           ->where('start_time', '<=', $currentTime)
                           ->where('end_time', '>=', $currentTime);
                    })
                    ->orWhere(function ($sq) use ($currentTime) {
                        // Overnight range (e.g., 23:00 - 05:00)
                        $sq->whereColumn('start_time', '>', 'end_time')
                           ->where(function ($ssq) use ($currentTime) {
                               $ssq->where('start_time', '<=', $currentTime)
                                   ->orWhere('end_time', '>=', $currentTime);
                           });
                    })
                    ->orWhere(function ($sq) {
                        // No time limit
                        $sq->whereNull('start_time')
                           ->whereNull('end_time');
                    });
                });
            })
            ->with(['questType', 'rankTier'])
            ->get();

    // Check user progress for each quest in ONE query for a specific date (Timezone Aware)
    $dateString = $request->query('date', now('Asia/Jakarta')->toDateString());
    $startOfDay = \Carbon\Carbon::parse($dateString, 'Asia/Jakarta')->startOfDay()->timezone('UTC');
    $endOfDay = \Carbon\Carbon::parse($dateString, 'Asia/Jakarta')->endOfDay()->timezone('UTC');

    $questIds = $quests->pluck('id');
    $userQuests = UserQuest::where('user_id', $user->id)
        ->whereIn('quest_id', $questIds)
        ->whereBetween('created_at', [$startOfDay, $endOfDay])
        ->get()
        ->keyBy('quest_id');

        $data = $quests->map(function ($quest) use ($userQuests, $typeSlug) {
            $userQuest = $userQuests->get($quest->id);

            // Determine Actions
            $canAccept = is_null($userQuest);
            $canComplete = false;
            $canProgress = false;
            $canCancel = false;

            if ($userQuest && $userQuest->status === 'pending') {
                $canCancel = true;
                
                // Check if requirements met
                $allMet = true;
                if ($quest->requirements) {
                    foreach ($quest->requirements as $key => $target) {
                        $current = $userQuest->progress[$key] ?? 0;
                        if ($current < $target) {
                            $allMet = false;
                            break;
                        }
                    }
                }
                
                if ($allMet) {
                    $canComplete = true; // Ready to claim reward
                } else {
                    $canProgress = true; // Still need update
                }
            }

            Log::info("Quest DEBUG [$typeSlug]: Quest ID {$quest->id} ('{$quest->title}') has status: " . ($userQuest ? $userQuest->status : 'available'));
            
            return [
                'id' => $quest->id,
                'title' => $quest->title,
                'description' => $quest->description,
                'rank' => $quest->rankTier->name ?? 'Novice', // Changed from 'F' to 'Novice'
                'type' => $typeSlug, // Changed from $quest->questType->slug ?? 'daily' to $typeSlug
                'reward_exp' => $quest->reward_exp,
                'reward_soul_points' => $quest->reward_soul_points,
                'is_mandatory' => $quest->is_mandatory,
                'penalty_fatigue' => $quest->penalty_fatigue,
                'requirements' => $quest->requirements,
                'status' => $userQuest ? $userQuest->status : 'available', // available, pending, completed, failed
                'progress' => $userQuest ? $userQuest->progress : null,
                'has_taken' => $userQuest ? true : false, // Changed from !is_null($userQuest)
                'actions' => [
                    'can_accept' => $canAccept,
                    'can_complete' => $canComplete,
                    'can_progress' => $canProgress,
                    'can_cancel' => $canCancel,
                ],
                // Added new fields below
                'starts_at' => $quest->starts_at?->toIso8601String(),
                'expires_at' => $quest->expires_at?->toIso8601String(),
                'time_limit' => $quest->time_limit,
                'start_time' => $quest->start_time,
                'end_time' => $quest->end_time,
            ];
        });

        Log::info('Quest API returned ' . $quests->count() . ' quests for type: ' . $typeSlug);
        
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }


    /**
     * Accept a quest
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function accept(Request $request, $id)
    {
        $user = $request->user();
        $quest = Quest::find($id);

        if (!$quest) {
            return response()->json(['success' => false, 'message' => 'Quest not found'], 404);
        }

        // Check if already taken TODAY (Timezone Aware check)
        $now = now('Asia/Jakarta');
        $startOfDay = $now->copy()->startOfDay()->timezone('UTC');
        $endOfDay = $now->copy()->endOfDay()->timezone('UTC');

        $existing = UserQuest::where('user_id', $user->id)
            ->where('quest_id', $id)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->first();

        if ($existing) {
            return response()->json(['success' => false, 'message' => 'Quest already accepted or completed today'], 422);
        }

        // --- TIME VALIDATION (Daily Window) ---
        // Note: starts_at/expires_at are already filtered in index().
        // We only enforce the recurring daily time window here.
        
        $currentTime = $now->toTimeString();
        if ($quest->start_time && $quest->end_time) {
            $isAvailable = false;
            // Ensure we only have HH:MM even if Laravel/DB returns full datetime or HH:MM:SS
            $start = substr(strlen($quest->start_time) > 8 ? substr($quest->start_time, -8) : $quest->start_time, 0, 5);
            $end = substr(strlen($quest->end_time) > 8 ? substr($quest->end_time, -8) : $quest->end_time, 0, 5);
            $currentTimeShort = substr($currentTime, 0, 5);
            
            \Log::info("Quest ID {$id} Time Check: Current: $currentTimeShort, Start: $start, End: $end");

            if ($start <= $end) {
                // Normal range (e.g., 08:00 - 17:00)
                if ($currentTimeShort >= $start && $currentTimeShort <= $end) {
                    $isAvailable = true;
                }
            } else {
                // Overnight range (e.g., 22:00 - 04:00)
                if ($currentTimeShort >= $start || $currentTimeShort <= $end) {
                    $isAvailable = true;
                }
            }

            if (!$isAvailable) {
                return response()->json([
                    'success' => false, 
                    'message' => 'This mission is currently closed. Come back later!',
                    'debug' => [
                        'current' => $currentTimeShort,
                        'start' => $start,
                        'end' => $end,
                        'full_current' => $currentTime
                    ]
                ], 422);
            }
        }

        // Initialize progress 0 for all requirements
        $initialProgress = [];
        if ($quest->requirements) {
            foreach ($quest->requirements as $key => $target) {
                $initialProgress[$key] = 0;
            }
        }

        $userQuest = UserQuest::create([
            'user_id' => $user->id,
            'quest_id' => $quest->id,
            'status' => 'pending',
            'progress' => $initialProgress,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quest accepted! Fight for Allah!',
            'data' => $userQuest,
        ]);
    }

    /**
     * Update quest progress
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProgress(Request $request, $id)
    {
        $user = $request->user();
        $userQuest = UserQuest::where('user_id', $user->id)
            ->where('quest_id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$userQuest) {
            return response()->json(['success' => false, 'message' => 'Active quest not found'], 404);
        }

        $validated = $request->validate([
            'progress_key' => 'required|string',
            'increment' => 'required|integer|min:1',
        ]);

        $currentProgress = $userQuest->progress ?? [];
        $key = $validated['progress_key'];

        if (!isset($currentProgress[$key])) {
             // If key doesn't exist (maybe new requirement), init 0
             $currentProgress[$key] = 0;
        }

        $currentProgress[$key] += $validated['increment'];
        
        // Update DB
        $userQuest->progress = $currentProgress;
        $userQuest->save();

        return response()->json([
            'success' => true,
            'message' => 'Progress updated',
            'data' => [
                'progress' => $currentProgress,
            ]
        ]);
    }

    /**
     * Complete quest and claim reward
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request, $id)
    {
        $user = $request->user();
        $userQuest = UserQuest::where('user_id', $user->id)
            ->where('quest_id', $id)
            ->where('status', 'pending')
            ->with('quest')
            ->first();

        if (!$userQuest) {
            return response()->json(['success' => false, 'message' => 'Active quest not found or already completed'], 404);
        }

        $quest = $userQuest->quest;
        $requirements = $quest->requirements;
        $progress = $userQuest->progress;

        // Verify requirements
        $allMet = true;
        if ($requirements) {
            foreach ($requirements as $key => $target) {
                $current = $progress[$key] ?? 0;
                if ($current < $target) {
                    $allMet = false;
                    break;
                }
            }
        }

        if (!$allMet) {
            return response()->json([
                'success' => false, 
                'message' => 'Requirements not met yet.',
                'progress' => $progress,
                'requirements' => $requirements
            ], 422);
        }

        // Time verification for completion (if needed)
        $now = now('Asia/Jakarta');

        $leveledUp = false;
        $oldLevel = $user->level;

        DB::transaction(function () use ($user, $userQuest, $quest, &$leveledUp) {
            // Update Status
            $userQuest->update([
                'status' => 'completed',
                'completed_at' => now('Asia/Jakarta'),
            ]);

            // Give Soul Points Reward
            $user->increment('soul_points', $quest->reward_soul_points);

            // Use gainExp to handle XP, Level, Rank, and Circle XP
            $leveledUp = $user->gainExp($quest->reward_exp);
        });
        
        $user->refresh();

        return response()->json([
            'success' => true,
            'message' => $leveledUp ? 'LEVEL UP! Your soul grows stronger!' : 'Quest Completed! Level Up Your Soul!',
            'leveled_up' => $leveledUp,
            'rewards' => [
                'exp' => $quest->reward_exp,
                'soul_points' => $quest->reward_soul_points,
            ],
            'user_stats' => [
                'level' => $user->level,
                'current_exp' => $user->current_exp,
                'soul_points' => $user->soul_points,
                'fatigue' => $user->fatigue,
            ]
        ]);
    }

    /**
     * Get detailed quest info
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        // Get the quest
        $quest = Quest::with(['questType', 'rankTier'])
            ->find($id);

        if (!$quest) {
            return response()->json(['success' => false, 'message' => 'Quest not found'], 404);
        }

        $userQuest = UserQuest::where('user_id', $user->id)
            ->where('quest_id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'quest' => $quest,
                'user_progress' => $userQuest,
            ]
        ]);
    }

    /**
     * Abandon a quest and suffer fatigue penalty
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $userQuest = UserQuest::where('user_id', $user->id)
            ->where('quest_id', $id)
            ->where('status', 'pending')
            ->first();

        if (!$userQuest) {
            return response()->json(['success' => false, 'message' => 'Active mission not found'], 404);
        }

        $quest = $userQuest->quest;

        DB::transaction(function () use ($user, $userQuest, $quest) {
            // Apply Penalty
            $user->increment('fatigue', $quest->penalty_fatigue);
            
            // Delete progress
            $userQuest->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Mission abandoned. Penalty of ' . $quest->penalty_fatigue . ' fatigue applied.',
            'user_stats' => [
                'fatigue' => $user->refresh()->fatigue
            ]
        ]);
    }
}
