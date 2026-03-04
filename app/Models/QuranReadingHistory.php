<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuranReadingHistory extends Model
{
    protected $fillable = ['user_id', 'surah_no', 'surah_name', 'ayah_no', 'juz_no'];
    //
}
