<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDailyTask;
use App\Models\DailyTask;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // --- SELF-HEALING: Ensure Seed Data exists ---
        if (\App\Models\RankTier::count() === 0 || \App\Models\LevelConfig::count() === 0) {
            $this->ensureSeedData();
        }

        $this->repairUserStats($user);
        $user->refresh(); // Ensure we have latest level/exp from DB
        $user->load(['rankTier', 'userStat']);

        // Calculate Salah Consistency (Last 7 Days)
        $startWindow = Carbon::now()->subDays(6)->startOfDay();
        $endWindow = Carbon::now()->endOfDay();

        $salahTaskIds = DailyTask::where('name', 'like', 'Sholat%')->pluck('id');
        $salahCompletedCount = UserDailyTask::where('user_id', $user->id)
            ->whereIn('daily_task_id', $salahTaskIds)
            ->whereBetween('date', [$startWindow, $endWindow])
            ->count();
        
        // 5 Sholat per day * 7 days
        $totalSalahOpportunities = 5 * 7; 
        $salahConsistency = $totalSalahOpportunities > 0 ? round(($salahCompletedCount / $totalSalahOpportunities) * 100) : 0;

        // Calculate Quran Progress (Total Sessions)
        $quranTaskId = DailyTask::where('name', 'like', '%Al-Quran%')->value('id');
        $quranSessions = 0;
        if ($quranTaskId) {
            $quranSessions = UserDailyTask::where('user_id', $user->id)
                ->where('daily_task_id', $quranTaskId)
                ->count();
        }

        // Calculate Next Level XP from LevelConfig
        $levelConfig = \App\Models\LevelConfig::find($user->level);
        $nextLevelXp = $levelConfig ? $levelConfig->xp_required : ($user->level * 1000); 
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'avatar' => $user->avatar_url,
                    'gender' => $user->gender,
                    'rank' => $user->rankTier ? $user->rankTier->name : 'Novice',
                    'level' => $user->level,
                    'xp' => [
                        'current' => $user->current_exp,
                        'max' => $user->next_level_exp,
                        'progress' => $user->next_level_exp > 0 ? round(($user->current_exp / $user->next_level_exp) * 100) : 0,
                    ],
                    'stats' => [
                        'streak' => $user->streak,
                        'salah_consistency' => $salahConsistency,
                        'quran_progress' => "Juz " . (1 + floor($quranSessions / 20)),
                        'total_quran_sessions' => $quranSessions,
                        'attributes' => [
                            'sholat' => $user->sholat_count,
                            'ilmu' => $user->ilmu_count,
                            'adab' => $user->adab_count,
                            'istiqomah' => $user->streak, // Simplified for now
                        ],
                    ],
                    'hp' => [
                        'current' => $user->hp,
                        'max' => $user->max_hp,
                        'progress' => $user->max_hp > 0 ? round(($user->hp / $user->max_hp) * 100) : 0,
                    ],
                    'soul_points' => $user->soul_points,
                    'max_sp' => $user->max_sp,
                    'referral_code' => $user->referral_code,
                    'job_class' => $user->job_class ?? 'Newbie',
                    'followers_count' => \Illuminate\Support\Facades\Schema::hasTable('follows') ? $user->followers()->count() : 0,
                    'following_count' => \Illuminate\Support\Facades\Schema::hasTable('follows') ? $user->following()->count() : 0,
                    'rank_tier_id' => $user->rank_tier_id,
                    'is_menstruating' => (bool)$user->is_menstruating,
                    'is_active' => (bool)$user->is_active,
                    'balance' => (float)($user->balance ?? 0),
                ]
            ]
        ]);
    }

    public function show($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        $user->load(['rankTier', 'userStat']);

        // Calculate Salah Consistency (Last 7 Days)
        $startWindow = Carbon::now()->subDays(6)->startOfDay();
        $endWindow = Carbon::now()->endOfDay();

        $salahTaskIds = DailyTask::where('name', 'like', 'Sholat%')->pluck('id');
        $salahCompletedCount = UserDailyTask::where('user_id', $user->id)
            ->whereIn('daily_task_id', $salahTaskIds)
            ->whereBetween('date', [$startWindow, $endWindow])
            ->count();
        
        $totalSalahOpportunities = 5 * 7; 
        $salahConsistency = $totalSalahOpportunities > 0 ? round(($salahCompletedCount / $totalSalahOpportunities) * 100) : 0;

        // Calculate Quran Progress
        $quranTaskId = DailyTask::where('name', 'like', '%Al-Quran%')->value('id');
        $quranSessions = 0;
        if ($quranTaskId) {
            $quranSessions = UserDailyTask::where('user_id', $user->id)
                ->where('daily_task_id', $quranTaskId)
                ->count();
        }

        $activities = \App\Models\ActivityLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'avatar' => $user->avatar_url,
                    'gender' => $user->gender,
                    'rank' => $user->rankTier ? $user->rankTier->name : 'Novice',
                    'level' => $user->level,
                    'xp' => [
                        'current' => $user->current_exp,
                        'max' => $user->next_level_exp,
                        'progress' => $user->next_level_exp > 0 ? round(($user->current_exp / $user->next_level_exp) * 100) : 0,
                    ],
                    'stats' => [
                        'streak' => $user->streak,
                        'salah_consistency' => $salahConsistency,
                        'quran_progress' => "Juz " . (1 + floor($quranSessions / 20)),
                        'total_quran_sessions' => $quranSessions,
                        'attributes' => [
                            'sholat' => $user->sholat_count,
                            'ilmu' => $user->ilmu_count,
                            'adab' => $user->adab_count,
                            'istiqomah' => $user->streak,
                        ],
                    ],
                    'hp' => [
                        'current' => $user->hp,
                        'max' => $user->max_hp,
                        'progress' => $user->max_hp > 0 ? round(($user->hp / $user->max_hp) * 100) : 0,
                    ],
                    'job_class' => $user->job_class ?? 'Newbie',
                    'followers_count' => $user->followers()->count(),
                    'following_count' => $user->following()->count(),
                    'is_following' => Auth::user()->following()->where('following_id', $id)->exists(),
                ],
                'activities' => $activities
            ]
        ]);
    }

    public function penalty(Request $request)
    {
        $request->validate([
            'hp_loss' => 'required|integer|min:1',
            'reason' => 'required|string',
        ]);

        $user = Auth::user();
        $user->hp = max(0, $user->hp - $request->hp_loss);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Penalty applied successfully',
            'data' => [
                'current_hp' => $user->hp,
                'hp_loss' => $request->hp_loss,
                'reason' => $request->reason,
            ]
        ]);
    }

    /**
     * One-time check for all missed Maghrib/Isya since account creation
     */
    public function historyCheck(Request $request)
    {
        $user = Auth::user();
        
        // Cache key to prevent multiple heavy historical checks
        $cacheKey = "historical_penalty_check_" . $user->id;
        if (cache()->has($cacheKey)) {
            return response()->json([
                'success' => true,
                'message' => 'Historical check already performed recently.',
                'data' => ['checked' => true]
            ]);
        }

        $createdAt = $user->created_at->startOfDay();
        $yesterday = Carbon::yesterday()->endOfDay();
        
        if ($createdAt->gt($yesterday)) {
             return response()->json([
                'success' => true,
                'message' => 'New account, no history to check.',
                'data' => ['hp_loss' => 0]
            ]);
        }

        // 1. Get targets (Maghrib, Isya, Isha)
        $targetTaskIds = DailyTask::where(function($q) {
                $q->where('name', 'like', '%Maghrib%')
                  ->orWhere('name', 'like', '%Isya%')
                  ->orWhere('name', 'like', '%Isha%');
            })
            ->whereNull('user_id') // Master tasks only
            ->pluck('id');

        if ($targetTaskIds->isEmpty()) {
            return response()->json(['success' => true, 'message' => 'No target tasks found.']);
        }

        // 2. Count total opportunities
        $daysCount = $createdAt->diffInDays($yesterday) + 1;
        $totalPotentialPrayers = $daysCount * 2; // Maghrib + Isya/Isha

        // 3. Count completions
        $completedCount = UserDailyTask::where('user_id', $user->id)
            ->whereIn('daily_task_id', $targetTaskIds)
            ->whereBetween('date', [$createdAt->toDateString(), $yesterday->toDateString()])
            ->count();

        $missedCount = max(0, $totalPotentialPrayers - $completedCount);
        
        // 🚺 Respect menstruation status even for history (simplified)
        if ($user->is_menstruating) {
            $hpLoss = 0;
        } else {
            $hpLoss = $missedCount * 5; // Reduced from 10 to 5 per request
        }

        if ($hpLoss > 0) {
            $user->hp = max(0, $user->hp - $hpLoss);
            $user->save();
        }

        cache()->put($cacheKey, true, now()->addDays(7));

        return response()->json([
            'success' => true,
            'message' => 'Historical penalty check completed',
            'data' => [
                'days_checked' => $daysCount,
                'missed_count' => $missedCount,
                'hp_loss' => $hpLoss,
                'current_hp' => $user->hp
            ]
        ]);
    }

    private function repairUserStats($user)
    {
        $currentLevel = $user->level;
        $currentExp = $user->current_exp;
        $leveledUp = false;

        // Level Up Calculation
        while (true) {
            $levelConfig = \App\Models\LevelConfig::where('level', $currentLevel)->first();
            $required = $levelConfig ? $levelConfig->xp_required : ($currentLevel * 1000);
            
            // 🛑 Safety check: Prevent infinite loop if config is corrupted
            if ($required <= 0) break;

            if ($currentExp >= $required) {
                // Check if next level exists in config to prevent FK error
                $nextLevelExists = \App\Models\LevelConfig::where('level', $currentLevel + 1)->exists();
                if (!$nextLevelExists) break;

                $currentExp -= $required;
                $currentLevel += 1;
                $leveledUp = true;
            } else {
                break;
            }
        }

        if ($leveledUp) {
            $user->level = $currentLevel;
            $user->current_exp = $currentExp;
            $user->save();
            $user->refresh();
        }

        // --- NEW: Daily HP Reset (Wok's Request) ---
        $today = now()->toDateString();
        $cacheKey = "hp_daily_reset_" . $user->id;
        $lastReset = cache()->get($cacheKey);

        if ($lastReset !== $today) {
            \Log::info("Daily HP Reset triggered for user {$user->id}");
            $user->hp = $user->max_hp;
            $user->save();
            cache()->put($cacheKey, $today, now()->addDays(1));

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'daily_reset',
                'amount' => $user->max_hp,
                'description' => "Darah dipulihkan (Full HP) - Semangat Hunter Baru!"
            ]);
        }

        // Always check rank repair
        $user->updateRankTier();
    }

    private function ensureSeedData()
    {
        // Populate Level Configs (Ensure at least 100 levels)
        if (\App\Models\LevelConfig::count() < 100) {
            for ($i = 1; $i <= 100; $i++) {
                \App\Models\LevelConfig::updateOrCreate(
                    ['level' => $i],
                    ['xp_required' => $i * 1000]
                );
            }
        }

        // Populate Rank Tiers
        if (\App\Models\RankTier::count() < 6) {
            $ranks = [
                ['slug' => 'E', 'name' => 'Rank E', 'min_level' => 1, 'color_code' => 'text-slate-400'],
                ['slug' => 'D', 'name' => 'Rank D', 'min_level' => 10, 'color_code' => 'text-green-400'],
                ['slug' => 'C', 'name' => 'Rank C', 'min_level' => 30, 'color_code' => 'text-blue-400'],
                ['slug' => 'B', 'name' => 'Rank B', 'min_level' => 50, 'color_code' => 'text-purple-400'],
                ['slug' => 'A', 'name' => 'Rank A', 'min_level' => 70, 'color_code' => 'text-orange-400'],
                ['slug' => 'S', 'name' => 'Rank S', 'min_level' => 90, 'color_code' => 'text-red-500'],
            ];
            foreach ($ranks as $rank) {
                \App\Models\RankTier::updateOrCreate(['slug' => $rank['slug']], $rank);
            }
        }
    }

    public function debug()
    {
        return response()->json([
            'user' => Auth::user()->toArray(),
            'rank_tiers_count' => \App\Models\RankTier::count(),
            'rank_tiers' => \App\Models\RankTier::all(),
            'level_configs_count' => \App\Models\LevelConfig::count(),
            'current_level_config' => \App\Models\LevelConfig::find(Auth::user()->level),
        ]);
    }

    public function activities()
    {
        $activities = \App\Models\ActivityLog::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    public function update(Request $request) 
    {
        $user = Auth::user();
        $validated = $request->validate([
            'username' => 'sometimes|string|min:3|max:50|unique:users,username,'.$user->id,
            'gender' => 'sometimes|in:Male,Female,male,female',
            'job_class' => 'sometimes|string|max:50',
        ]);

        if (isset($validated['username'])) {
            $user->username = $validated['username'];
        }
        if (isset($validated['gender'])) {
            $user->gender = ucfirst(strtolower($validated['gender']));
        }
        if (isset($validated['job_class'])) {
            $user->job_class = $validated['job_class'];
        }

        $user->save();
        
        return response()->json([
            'success' => true, 
            'message' => 'Profile updated successfully', 
            'data' => [
                'user' => $user
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = Auth::user();

        if (!\Illuminate\Support\Facades\Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi lama tidak sesuai.'
            ], 400);
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui'
        ]);
    }
}
