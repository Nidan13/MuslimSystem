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
        'category_id',
        'is_active',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function userCompletions()
    {
        return $this->hasMany(UserDailyTask::class);
    }

    public function scopeMaster($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeCustom($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
