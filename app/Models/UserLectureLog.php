<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLectureLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'islamic_video_id',
        'watched_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function islamicVideo()
    {
        return $this->belongsTo(IslamicVideo::class);
    }
}
