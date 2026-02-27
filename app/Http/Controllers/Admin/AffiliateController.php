<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Commission;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    public function index()
    {
        // Get users who have referral_code
        $affiliates = User::whereNotNull('referral_code')
            ->withCount(['commissions', 'referrals'])
            ->withSum('commissions', 'amount')
            ->paginate(10);

        return view('admin.affiliates.index', compact('affiliates'));
    }

    public function show(User $user)
    {
        $user->loadCount(['commissions', 'withdrawals']);
        
        $referrals = User::where('referred_by_id', $user->id)
            ->withCount('payments')
            ->get();

        $commissions = Commission::where('recipient_id', $user->id)
            ->with('referredUser', 'payment')
            ->latest()
            ->get();

        $withdrawals = Withdrawal::where('user_id', $user->id)
            ->latest()
            ->get();

        $totalCommission = $commissions->sum('amount');
        $totalWithdrawn = $withdrawals->whereIn('status', ['completed', 'approved'])->sum('amount');
        $balance = $user->balance;

        return view('admin.affiliates.show', compact(
            'user', 
            'referrals', 
            'commissions', 
            'withdrawals',
            'totalCommission',
            'totalWithdrawn',
            'balance'
        ));
    }
}
