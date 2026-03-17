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
            
            // 1. Inisialisasi
            $systemFee = 0;
            $affiliateFee = 0;
            $isSupport = str_contains($this->external_id, 'SUP');

            // 2. Tentukan Base Alokasi (Contoh: Total 500rb)
            if (!$donation && $u) {
                // Potong Affiliate 10% DULU (Jika ada referral & bukan support)
                if (!$isSupport && $u->referred_by_id) {
                    $affiliateFee = $this->amount * 0.1; // 50rb
                }
                
                // Sisa inilah yang baru dibagi ke alokasi (Contoh: 450rb)
                $allocationBase = $this->amount - $affiliateFee;

                // 3. Hitung & Simpan Detail Distribusi SHU (Dari sisa 450rb tadi)
                $totalAllocated = 0;
                $categories = \App\Models\DistributionCategory::where('is_active', true)
                    ->where('name', 'NOT ILIKE', '%afiliasi%')
                    ->where('name', 'NOT ILIKE', '%referral%')
                    ->where('name', 'NOT ILIKE', '%komisi%')
                    ->get();

                foreach ($categories as $cat) {
                    // Jatah admin dihitung dari base 450rb tadi
                    $distAmount = $allocationBase * ($cat->percentage / 100);
                    $totalAllocated += $distAmount;

                    PaymentDistribution::create([
                        'payment_id' => $this->id,
                        'distribution_category_id' => $cat->id,
                        'category_name' => $cat->name,
                        'percentage' => $cat->percentage,
                        'amount' => $distAmount
                    ]);
                }

                // Jatah System (Income Admin) adalah gabungan semua alokasi tadi
                $systemFee = $totalAllocated;
            }
            
            $netAmount = $this->amount - $systemFee - $affiliateFee;

            // 4. Update status pembayaran
            $this->update([
                'status' => 'paid', 
                'paid_at' => now(),
                'system_fee' => $systemFee,
                'net_amount' => $netAmount,
                'affiliate_fee' => $affiliateFee
            ]);

            if ($u) {
                \Log::info('Processing Payment for User ID: ' . $u->id);
                
                // Aktifkan akun jika ini pendaftaran baru
                if ($isActivation && !$u->is_active) {
                    $u->update(['is_active' => 1]);
                }
                
                // 4. Logic Donasi (Jika ada)
                if ($donation) {
                    $donation->update(['status' => 'completed']);
                    $campaign = $donation->campaign;
                    if ($campaign) {
                        // YANG MASUK KE KAMPANYE ADALAH NET AMOUNT (SETELAH POTONGAN PLATFORM)
                        $campaign->increment('collected_amount', $this->amount - $systemFee);
                        
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

