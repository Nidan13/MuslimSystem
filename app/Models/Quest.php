<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    protected $fillable = [
        'title',
        'description',
        'quest_type_id',
        'rank_tier_id',
        'reward_exp',
        'reward_soul_points',
        'is_mandatory',
        'penalty_fatigue',
        'requirements',
        'starts_at',
        'expires_at',
        'time_limit',
        'start_time',
        'end_time',
    ];

    public function questType()
    {
        return $this->belongsTo(QuestType::class);
    }

    public function rankTier()
    {
        return $this->belongsTo(RankTier::class);
    }

    protected $casts = [
        'requirements' => 'array',
        'is_mandatory' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function userQuests()
    {
        return $this->hasMany(UserQuest::class);
    }
}
