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
<<<<<<< HEAD
        'dungeon_type_id', // Legacy
        'rank_tier_id', // Legacy
=======
        'dungeon_type_id',
        'rank_tier_id',
>>>>>>> main
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
<<<<<<< HEAD
        return $this->belongsTo(Category::class , 'category_id');
=======
        return $this->belongsTo(Category::class, 'category_id');
>>>>>>> main
    }

    public function rankCategory()
    {
<<<<<<< HEAD
        return $this->belongsTo(Category::class , 'rank_category_id');
=======
        return $this->belongsTo(Category::class, 'rank_category_id');
>>>>>>> main
    }

    public function dungeonType()
    {
        return $this->belongsTo(DungeonType::class, 'dungeon_type_id');
    }

    public function rankTier()
    {
        return $this->belongsTo(RankTier::class, 'rank_tier_id');
    }
}
