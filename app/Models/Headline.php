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
        'summary',
        'content',
        'image_url',
        'images',
        'is_active',
        'is_for_user',
        'is_for_landing_page',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_for_user' => 'boolean',
        'is_for_landing_page' => 'boolean',
        'images' => 'array',
    ];

    public function getImageUrlAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        return url($value);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}