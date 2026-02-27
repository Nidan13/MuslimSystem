<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankTier extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'color_code', 'min_level', 'description'];

    public function minLevelConfig()
    {
        return $this->belongsTo(LevelConfig::class, 'min_level', 'level');
    }

    public function getMinXpRequiredAttribute()
    {
        return $this->minLevelConfig?->required_exp ?? 0;
    }

    public function getMinLevelRequirementAttribute()
    {
        return $this->min_level;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function quests()
    {
        return $this->hasMany(Quest::class);
    }

    public function dungeons()
    {
        return $this->hasMany(Dungeon::class);
    }
}
