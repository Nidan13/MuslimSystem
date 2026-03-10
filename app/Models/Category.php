<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'color',
        'icon',
        'is_active',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Constants for types to maintain consistency
    const TYPE_QUEST = 'quest';
    const TYPE_KAJIAN = 'kajian';
    const TYPE_SHOP = 'shop';
    const TYPE_DAILY_TASK = 'daily_task';
    const TYPE_DUNGEON = 'dungeon';
    const TYPE_RANK = 'rank';

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
