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
        'content',
        'image_url',
        'is_active',
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