<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dungeon extends Model
{
    protected $fillable = [
        'name',
        'description',
        'dungeon_type_id',
        'rank_tier_id',
        'min_level_requirement',
        'reward_exp',
        'required_players',
        'objective_type',
        'objective_target',
        'loot_pool',
    ];

    protected $casts = [
        'loot_pool' => 'array',
    ];

    public function dungeonType()
    {
        return $this->belongsTo(DungeonType::class);
    }

    public function rankTier()
    {
        return $this->belongsTo(RankTier::class);
    }
}
