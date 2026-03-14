<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'distribution_category_id',
        'category_name',
        'percentage',
        'amount'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
