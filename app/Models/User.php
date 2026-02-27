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
        'username', 'email', 'password', 'gender', 'rank_tier_id', 'level', 
        'current_exp', 'overflow_exp', 'job_class', 'soul_points', 'fatigue',
        'referral_code', 'referred_by_id', 'hp', 'max_hp', 'is_menstruating',
        'menstruation_started_at', 'balance', 'is_active'
    ];

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
        $dailyCount = $this->userDailyTasks()->whereHas('dailyTask', function($q) {
            $q->where('name', 'like', 'Sholat%');
        })->count();

        // Count from mandatory quests (sholat_5_waktu)
        $questProgress = $this->userQuests()->where('status', 'completed')->get()->sum(function($uq) {
            if (empty($uq->progress)) return 0;
            return $uq->progress['sholat_5_waktu'] ?? 0;
        });

        return $dailyCount + $questProgress;
    }

    public function getIlmuCountAttribute()
    {
        return $this->userDailyTasks()->whereHas('dailyTask', function($q) {
            $q->where('name', 'like', '%Al-Quran%')->orWhere('name', 'like', '%Ilmu%');
        })->count();
    }

    public function getAdabCountAttribute()
    {
        return $this->userDailyTasks()->whereHas('dailyTask', function($q) {
            $q->where('name', 'like', '%Dzikir%')->orWhere('name', 'like', '%Adab%');
        })->count();
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
}
