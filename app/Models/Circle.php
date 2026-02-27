<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Circle extends Model {
    protected $fillable = ['name', 'description', 'level', 'xp', 'icon', 'color', 'members_count', 'leader_id'];

    public function members(): BelongsToMany {
        return $this->belongsToMany(User::class)->withPivot('role', 'joined_at', 'xp_contribution')->withTimestamps();
    }

    public function leader(): BelongsTo {
        return $this->belongsTo(User::class, 'leader_id');
    }
}
