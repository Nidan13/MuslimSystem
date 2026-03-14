<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LandingPageSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'image_url',
        'button_text',
        'button_url',
        'order',
        'is_active',
        'type',
        'style',
        'items',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'items' => 'array',
    ];

    public function getImageUrlAttribute($value)
    {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        
        // Remove leading slash if exists to prevent double slash with asset()
        $path = ltrim($value, '/');
        return asset($path);
    }
}
