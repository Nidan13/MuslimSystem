<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_campaign_id',
        'title',
        'content',
        'images',
        'amount_spent'
    ];

    protected $casts = [
        'images' => 'array'
    ];

    public function campaign()
    {
        return $this->belongsTo(DonationCampaign::class, 'donation_campaign_id');
    }
}
