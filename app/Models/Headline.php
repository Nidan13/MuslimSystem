<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Headline extends Model
{
    use HasFactory;

    protected $fillable = [
        'tag',
        'category_id',
        'category_legacy',
        'title',
        'slug',
        'summary',
        'content',
        'image_url',
        'images',
        'is_active',
        'is_for_user',
        'is_for_landing_page',
    ];

    protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
        'is_for_user' => 'boolean',
        'is_for_landing_page' => 'boolean',
    ];

    public function getImageUrlAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        
        // Remove leading slash if exists to prevent double slash with asset()
        $path = ltrim($value, '/');
        return asset($path);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}