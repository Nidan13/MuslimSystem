<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DungeonType extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'max_participants'];

    public function dungeons()
    {
        return $this->hasMany(Dungeon::class);
    }
}
