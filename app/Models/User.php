<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ActivityLog;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'gender',
        'rank_tier_id',
        'level',
        'current_exp',
        'overflow_exp',
        'soul_points',
        'fatigue',
        'referral_code',
        'referred_by_id',
        'hp',
        'max_hp',
        'is_menstruating',
        'menstruation_started_at',
        'balance',
        'is_active',
        'role',
        'google_id',   // FIX
        'avatar'       // FIX
    ];

    public function isOrganizer()
    {
        return $this->role === 'organizer';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }


    public function getExperienceAttribute()
    {
        return $this->current_exp;
    }

    public function setExperienceAttribute($value)
    {
        $this->attributes['current_exp'] = $value;
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        // Default avatar based on gender
        // Male: https://ui-avatars.com/api/?name=[name]&background=0D8ABC&color=fff
        // Female: https://ui-avatars.com/api/?name=[name]&background=E91E63&color=fff
        $bg = $this->gender === 'Female' ? 'E91E63' : '0D8ABC';
        return "https://ui-avatars.com/api/?name=" . urlencode($this->username) . "&background=$bg&color=fff&bold=true";
    }

    public function rankTier()
    {
        return $this->belongsTo(RankTier::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function userStat()
    {
        return $this->hasOne(UserStat::class);
    }

    public function dailyTasks()
    {
        return $this->hasMany(DailyTask::class);
    }

    public function userDailyTasks()
    {
        return $this->hasMany(UserDailyTask::class);
    }

    public function userQuests()
    {
        return $this->hasMany(UserQuest::class);
    }

    public function joinedCircles()
    {
        return $this->belongsToMany(Circle::class)->withPivot('role', 'joined_at', 'xp_contribution')->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'recipient_id');
    }

    public function receivedCommissions()
    {
        return $this->hasMany(Commission::class, 'referred_user_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    /**
     * Calculate Real Spiritual Attributes
     */
    public function getSholatCountAttribute()
    {
        return \App\Models\PrayerLog::where('user_id', $this->id)
            ->where('is_completed', true)
            ->count();
    }

    public function getIlmuCountAttribute()
    {
        $quranHistory = \App\Models\QuranReadingHistory::where('user_id', $this->id)->count();
        $quranSurah = \DB::table('user_quran_progress')->where('user_id', $this->id)->where('is_completed', true)->count();
        $dailyCount = $this->userDailyTasks()->whereHas('dailyTask', function($q) {
            $q->where('name', 'like', '%Quran%')->orWhere('name', 'like', '%Surah%')->orWhere('name', 'like', '%Ngaji%');
        })->count();
        
        return $quranHistory + $quranSurah + $dailyCount;
    }

    public function getLectureCountAttribute()
    {
        try {
            return DB::table('user_lecture_logs')
                ->where('user_id', $this->id)
                ->distinct('islamic_video_id')
                ->count('islamic_video_id');
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getWawasanCountAttribute()
    {
        return $this->lecture_count;
    }

    public function getAdabCountAttribute()
    {
        return $this->userDailyTasks()->whereHas('dailyTask', function($q) {
            $q->where('name', 'like', '%Dzikir%')->orWhere('name', 'like', '%Adab%');
        })->count();
    }

    public function getHabitCountAttribute()
    {
        return \App\Models\Habit::where('user_id', $this->id)->sum('count');
    }

    public function getJournalCountAttribute()
    {
        return $this->userDailyTasks()->count();
    }

    public function getTodoCountAttribute()
    {
        return \App\Models\Todo::where('user_id', $this->id)->where('is_completed', true)->count();
    }

    /**
     * Update user's rank based on their current level
     */
    public function updateRankTier()
    {
        $newRank = RankTier::where('min_level', '<=', $this->level)
            ->orderBy('min_level', 'desc')
            ->first();

        if ($newRank && $this->rank_tier_id != $newRank->id) {
            $this->rank_tier_id = $newRank->id;
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Get the XP required for the next level
     */
    public function getNextLevelExpAttribute()
    {
        $config = LevelConfig::where('level', $this->level)->first();
        return $config ? $config->xp_required : ($this->level * 1000);
    }

    /**
     * Get Maximum Soul Points (SP)
     */
    public function getMaxSpAttribute()
    {
        // For now, let's make it fixed 1000 or dynamic based on level if needed
        return 1000;
    }

    public function gainExp($amount)
    {
        if ($amount <= 0) return false;

        $leveledUp = false;
        
        DB::transaction(function () use ($amount, &$leveledUp) {
            $this->increment('current_exp', $amount);
            $this->refresh();

            // Log activity
            ActivityLog::create([
                'user_id' => $this->id,
                'type' => 'xp_gain',
                'amount' => $amount,
                'description' => "Mendapatkan $amount EXP"
            ]);

            // Level Up Loop
            while (true) {
                $levelConfig = LevelConfig::where('level', $this->level)->first();
                $required = $levelConfig ? $levelConfig->xp_required : ($this->level * 1000);
                
                \Log::info("User ID {$this->id} Level Up Check: Lvl: {$this->level}, Exp: {$this->current_exp}, Required: $required");

                // 🛑 Safety check: If required exp is 0 or less, something is wrong with the config.
                // Stop to prevent infinite loop.
                if ($required <= 0) {
                    \Log::error("User ID {$this->id} has invalid LevelConfig for level {$this->level}: xp_required is $required. Stopping gainExp.");
                    break;
                }

                // 🛑 Cap level at 100
                if ($this->level >= 100) {
                    \Log::info("User ID {$this->id} reached MAX LEVEL (100). XP continues to accumulate.");
                    break;
                }

                if ($this->current_exp >= $required) {
                    $this->current_exp -= $required;
                    $this->level += 1;
                    $leveledUp = true;

                    ActivityLog::create([
                        'user_id' => $this->id,
                        'type' => 'level_up',
                        'amount' => $this->level,
                        'description' => "Naik ke Level {$this->level}"
                    ]);
                } else {
                    break;
                }
            }
            
            // Final save for any XP changes or Level Ups
            $this->save();
            $this->refresh(); // Final refresh to sync everything

            // Rank Promotion
            if ($leveledUp) {
                $this->updateRankTier();
            }

            // Circle XP Rewards
            $circles = $this->joinedCircles;
            foreach ($circles as $circle) {
                // Global circle XP
                $circle->increment('xp', $amount);
                
                // --- NEW: Track member contribution ---
                DB::table('circle_user')
                    ->where('user_id', $this->id)
                    ->where('circle_id', $circle->id)
                    ->increment('xp_contribution', $amount);
                
                // Level Up Logic: Level * 1000
                $nextLevelXp = $circle->level * 1000;
                if ($circle->xp >= $nextLevelXp) {
                    $circle->increment('level');
                    $circle->update(['xp' => $circle->xp - $nextLevelXp]);
                }
            }
        });

        return $leveledUp;
    }

    /**
     * Deduct Experience with Level Down handling.
     */
    public function removeExp($amount)
    {
        if ($amount <= 0) return false;

        $leveledDown = false;
        
        DB::transaction(function () use ($amount, &$leveledDown) {
            $this->current_exp -= $amount;

            // Level Down Loop
            while ($this->current_exp < 0) {
                if ($this->level > 1) {
                    $this->level -= 1;
                    $leveledDown = true;
                    
                    // Get required XP of the NEW (lower) level to calculate remaining negative balance
                    $levelConfig = LevelConfig::where('level', $this->level)->first();
                    $required = $levelConfig ? $levelConfig->xp_required : ($this->level * 1000);
                    
                    if ($required <= 0) $required = 1000; // Safety
                    
                    $this->current_exp += $required; // e.g. -10 + 1000 = 990

                    ActivityLog::create([
                        'user_id' => $this->id,
                        'type' => 'level_down',
                        'amount' => $this->level,
                        'description' => "Turun ke Level {$this->level} karena penalti"
                    ]);
                } else {
                    $this->current_exp = 0;
                    break;
                }
            }
            
            $this->save();
            $this->refresh();

            // Rank Demotion check
            if ($leveledDown) {
                $this->updateRankTier();
            }
        });

        return $leveledDown;
    }

    public function updateRiftGateProgress(string $type, int $amount)
    {
        if ($amount <= 0) return;

        $circles = $this->joinedCircles;
        foreach ($circles as $circle) {
            // Find active raids for this user in this circle that match the objective type
            $raids = CircleRaidParticipant::where('circle_id', $circle->id)
                ->where('user_id', $this->id)
                ->where('status', 'ready') // 'ready' means currently in lobby or in progress
                ->whereHas('dungeon', function ($q) use ($type) {
                    $q->where('objective_type', $type);
                })
                ->get();

            foreach ($raids as $participant) {
                $dungeon = $participant->dungeon;
                
                // 1. Increment individual contribution
                $participant->increment('contribution_score', $amount);
                
                // 2. Check total collective progress for this specific raid instance in this circle
                $totalScore = CircleRaidParticipant::where('circle_id', $circle->id)
                    ->where('dungeon_id', $dungeon->id)
                    ->sum('contribution_score');

                \Log::info("Rift Gate Progress Check", [
                    'user' => $this->username,
                    'circle' => $circle->name,
                    'raid' => $dungeon->name,
                    'type' => $type,
                    'added' => $amount,
                    'current_total' => $totalScore,
                    'target' => $dungeon->objective_target
                ]);

                // 3. Check if completed
                if ($totalScore >= $dungeon->objective_target) {
                    // Mission Success!
                    $this->completeRiftGate($circle, $dungeon);
                }
            }
        }
    }

    /**
     * Logic to finalize a cleared Rift Gate.
     */
    protected function completeRiftGate($circle, $dungeon)
    {
        DB::transaction(function () use ($circle, $dungeon) {
            // 1. Mark all participants as 'cleared'
            $participants = CircleRaidParticipant::where('circle_id', $circle->id)
                ->where('dungeon_id', $dungeon->id)
                ->where('status', 'ready')
                ->get();

            foreach ($participants as $p) {
                $p->update(['status' => 'cleared']);
                
                // 2. Grant rewards to each participant (Moved to manual claim)
                /*
                $user = $p->user;
                if ($user) {
                    $user->gainExp($dungeon->reward_exp);
                    $user->increment('soul_points', $dungeon->reward_soul_points ?? 0);
                    
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'type' => 'raid_cleared',
                        'amount' => $dungeon->reward_exp,
                        'description' => "Misi Berhasil: {$dungeon->name} (Circle: {$circle->name})"
                    ]);
                }
                */
            }

            \Log::info("Rift Gate CLEARED!", [
                'circle' => $circle->name,
                'raid' => $dungeon->name,
                'reward' => $dungeon->reward_exp
            ]);
        });
    }
}
