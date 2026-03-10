<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dungeon extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'rank_category_id',
        'dungeon_type_id', // Legacy
        'rank_tier_id', // Legacy
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

    public function category()
    {
        return $this->belongsTo(Category::class , 'category_id');
    }

    public function rankCategory()
    {
        return $this->belongsTo(Category::class , 'rank_category_id');
    }

    public function dungeonType()
    {
        return $this->belongsTo(DungeonType::class);
    }

    public function rankTier()
    {
        return $this->belongsTo(RankTier::class);
    }
}
