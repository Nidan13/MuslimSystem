<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'external_id',
        'amount',
        'payment_url',
        'payment_method',
        'bank_code',
        'va_number',
        'qr_string',
        'payload',
        'status',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsPaid()
    {
        if ($this->status === 'paid') return;

        \Illuminate\Support\Facades\DB::transaction(function() {
            // 1. Update status pembayaran
            $this->update(['status' => 'paid', 'paid_at' => now()]);
            
            $u = $this->user;
            if ($u) {
                \Log::info('Activating User ID: ' . $u->id);
                
                // 2. Aktifkan akun user baru
                $u->update(['is_active' => 1]);
                
                // 3. Logic Komisi Referral (10%)
                if ($u->referred_by_id) {
                    $commissionAmount = $this->amount * 0.1;
                    \Log::info("Giving Commission to Referrer: {$u->referred_by_id}. Amount: {$commissionAmount}");

                    // Catat histori komisi
                    Commission::create([
                        'recipient_id'      => $u->referred_by_id,
                        'referred_user_id'  => $u->id,
                        'payment_id'        => $this->id,
                        'amount'            => $commissionAmount,
                        'tier'              => 1,
                        'status'            => 'Success'
                    ]);

                    // 4. Update SALDO si pengajak secara REAL
                    $referrer = User::find($u->referred_by_id);
                    if ($referrer) {
                        $referrer->increment('balance', $commissionAmount);
                        \Log::info("User ID {$referrer->id} balance updated. New balance: {$referrer->balance}");
                    }
                }
            }
        });
    }
}
