<?php

namespace App\Models;

<<<<<<< HEAD
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
=======
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
>>>>>>> main

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
<<<<<<< HEAD
        'color',
        'icon',
        'is_active',
        'description'
=======
        'icon',
        'description',
        'color',
        'is_active',
>>>>>>> main
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

<<<<<<< HEAD
    // Constants for types to maintain consistency
    const TYPE_QUEST = 'quest';
    const TYPE_KAJIAN = 'kajian';
    const TYPE_SHOP = 'shop';
    const TYPE_DAILY_TASK = 'daily_task';
    const TYPE_DUNGEON = 'dungeon';
    const TYPE_RANK = 'rank';

    public function scopeActive($query)
=======
    /**
     * Scope a query to only include categories of a given type.
     */
    public function scopeByType(Builder $query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive(Builder $query)
>>>>>>> main
    {
        return $query->where('is_active', true);
    }

<<<<<<< HEAD
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
=======
    /**
     * Get the headlines associated with the category.
     */
    public function headlines()
    {
        return $this->hasMany(Headline::class);
>>>>>>> main
    }
}
