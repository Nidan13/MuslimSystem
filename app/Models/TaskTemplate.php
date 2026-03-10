<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'category_id',
        'icon',
        'soul_points',
        'type',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
