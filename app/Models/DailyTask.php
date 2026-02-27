<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'soul_points',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userCompletions()
    {
        return $this->hasMany(UserDailyTask::class);
    }

    // Scope for master tasks (created by admin)
    public function scopeMaster($query)
    {
        return $query->whereNull('user_id');
    }

    // Scope for user custom tasks
    public function scopeCustom($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
