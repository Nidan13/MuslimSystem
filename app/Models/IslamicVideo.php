<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IslamicVideo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'channel',
        'video_url',
        'duration',
<<<<<<< HEAD
        'category',
=======
>>>>>>> main
        'category_id',
        'is_active',
    ];

    public function category()
    {
<<<<<<< HEAD
        return $this->belongsTo(Category::class);
    }

    /**
     * Helper to extract YouTube video ID from URL
     */
=======
        return $this->belongsTo(Category::class, 'category_id');
    }

>>>>>>> main
    public function getVideoIdAttribute()
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->video_url, $match);
        return $match[1] ?? null;
    }
}
