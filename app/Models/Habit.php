<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'notes',
        'difficulty',
        'is_positive',
        'is_negative',
        'frequency',
        'count',
        'icon',
        'color',
    ];

    protected $casts = [
        'is_positive' => 'boolean',
        'is_negative' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
