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
        'category',
        'is_active',
    ];

    /**
     * Helper to extract YouTube video ID from URL
     */
    public function getVideoIdAttribute()
    {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $this->video_url, $match);
        return $match[1] ?? null;
    }
}
