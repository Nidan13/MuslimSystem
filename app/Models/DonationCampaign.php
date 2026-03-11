<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'organizer_id',
        'category_id',
        'title',
        'slug',
        'description',
        'image',
        'target_amount',
        'collected_amount',
        'status',
        'rejection_reason',
        'deadline'
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function reports()
    {
        return $this->hasMany(DonationReport::class);
    }

    protected static function booted()
    {
        static::deleting(function ($campaign) {
            // Delete related reports
            $campaign->reports()->delete();
            
            // Delete related donations
            $campaign->donations()->delete();
            
            // Note: If you have payments strictly tied to these donations without their own lifecycle, 
            // you might need to handle them, but usually payments are independent records.
        });
    }
}
