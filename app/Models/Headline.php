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
        'title',
        'content',
        'image_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function itemCategory()
    {
        return $this->belongsTo(Category::class , 'category_id');
    }
}
