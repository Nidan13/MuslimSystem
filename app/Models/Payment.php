<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Donation;


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
                
                // 2. Aktifkan akun user baru (jika belum aktif)
                if (!$u->is_active) {
                    $u->update(['is_active' => 1]);
                }
                
                // 3. Logic Donasi (Jika ada)
                $donation = Donation::where('payment_id', $this->id)->first();
                if ($donation) {
                    $donation->update(['status' => 'completed']);
                    $campaign = $donation->campaign;
                    if ($campaign) {
                        $campaign->increment('collected_amount', $donation->amount);
                        
                        // Cek jika sudah mencapai target
                        if ($campaign->collected_amount >= $campaign->target_amount) {
                            $campaign->update(['status' => 'completed']);
                        }
                    }
                }

                // 4. Logic Komisi Referral (10%) - Hanya untuk pembayaran pertama/aktivasi
                // Sebaiknya cek apakah ini pembayaran aktivasi atau bukan. 
                // Diasumsikan pembayaran non-donasi adalah aktivasi.
                if (!$donation && $u->referred_by_id) {
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

                    // 5. Update SALDO si pengajak secara REAL
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

