<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrayerLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prayer_name',
        'date',
        'is_completed',
        'completed_at',
        'scheduled_at',
        'is_punished',
        'punished_at',
    ];

    protected $casts = [
        'date' => 'date',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_punished' => 'boolean',
        'punished_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
