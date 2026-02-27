<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDailyTask extends Model
{
    protected $fillable = [
        'user_id',
        'daily_task_id',
        'completed_at',
        'date',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dailyTask()
    {
        return $this->belongsTo(DailyTask::class);
    }
}
