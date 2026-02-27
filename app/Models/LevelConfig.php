<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelConfig extends Model
{
    use HasFactory;

    protected $primaryKey = 'level';
    public $incrementing = false;
    protected $fillable = ['level', 'xp_required', 'stat_points_reward'];
}
