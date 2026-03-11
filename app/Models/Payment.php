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
        'paid_at',
        'system_fee',
        'net_amount',
        'affiliate_fee'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donation()
    {
        return $this->hasOne(Donation::class);
    }

    public function distributions()
    {
        return $this->hasMany(PaymentDistribution::class);
    }

    public function markAsPaid()
    {
        if ($this->status === 'paid') return;

        \Illuminate\Support\Facades\DB::transaction(function() {
            $u = $this->user;
            $donation = Donation::where('payment_id', $this->id)->first();
            
            // 1. Ambil Total Platform Fee Rate dari Setting (Global)
            // HANYA BERLAKU JIKA BUKAN DONASI (Misal: Aktivasi Akun)
            $systemFee = 0;
            if (!$donation) {
                $platformRate = (float) \App\Models\Setting::get('total_system_fee_percentage', 0);
                $systemFee = $this->amount * ($platformRate / 100);
            }
            
            $affiliateFee = 0;
            if (!$donation && $u && $u->referred_by_id) {
                // Biaya Afiliasi tetap 10% untuk aktivasi
                $affiliateFee = $this->amount * 0.1;
            }
            
            $netAmount = $this->amount - ($systemFee + $affiliateFee);

            // 2. Update status pembayaran & simpan pembagian dana
            $this->update([
                'status' => 'paid', 
                'paid_at' => now(),
                'system_fee' => $systemFee,
                'net_amount' => $netAmount,
                'affiliate_fee' => $affiliateFee
            ]);

            // 3. Simpan Detail Distribusi SHU (Potongan Internal Admin)
            if ($systemFee > 0) {
                $categories = \App\Models\DistributionCategory::where('is_active', true)->get();
                foreach ($categories as $cat) {
                    $distAmount = $systemFee * ($cat->percentage / 100);
                    PaymentDistribution::create([
                        'payment_id' => $this->id,
                        'distribution_category_id' => $cat->id,
                        'category_name' => $cat->name,
                        'percentage' => $cat->percentage,
                        'amount' => $distAmount
                    ]);
                }
            }
            
            if ($u) {
                \Log::info('Activating User ID: ' . $u->id);
                
                // 3. Aktifkan akun user baru (jika belum aktif)
                if (!$u->is_active) {
                    $u->update(['is_active' => 1]);
                }
                
                // 4. Logic Donasi (Jika ada)
                if ($donation) {
                    $donation->update(['status' => 'completed']);
                    $campaign = $donation->campaign;
                    if ($campaign) {
                        // YANG MASUK KE KAMPANYE ADALAH NET AMOUNT (SETELAH POTONGAN PLATFORM)
                        $campaign->increment('collected_amount', $netAmount);
                        
                        // Cek jika sudah mencapai target
                        if ($campaign->collected_amount >= $campaign->target_amount) {
                            $campaign->update(['status' => 'completed']);
                        }
                    }
                }

                // 5. Logic Komisi Referral
                if (!$donation && $u->referred_by_id && $affiliateFee > 0) {
                    \Log::info("Giving Commission to Referrer: {$u->referred_by_id}. Amount: {$affiliateFee}");

                    Commission::create([
                        'recipient_id'      => $u->referred_by_id,
                        'referred_user_id'  => $u->id,
                        'payment_id'        => $this->id,
                        'amount'            => $affiliateFee,
                        'tier'              => 1,
                        'status'            => 'Success'
                    ]);

                    $referrer = User::find($u->referred_by_id);
                    if ($referrer) {
                        $referrer->increment('balance', $affiliateFee);
                    }
                }
            }
        });
    }
}

