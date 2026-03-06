<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CircleRaidParticipant extends Model
{
    protected $fillable = [
        'circle_id',
        'dungeon_id',
        'user_id',
        'status',
        'contribution_score',
        'is_rewarded',
    ];

    public function circle()
    {
        return $this->belongsTo(Circle::class);
    }

    public function dungeon()
    {
        return $this->belongsTo(Dungeon::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
