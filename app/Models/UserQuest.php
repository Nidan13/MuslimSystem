<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserQuest extends Model
{
    protected $fillable = [
        'user_id',
        'quest_id',
        'status',
        'progress',
        'completed_at',
    ];

    protected $casts = [
        'progress' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quest()
    {
        return $this->belongsTo(Quest::class);
    }
}
