<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    /**
     * Get affiliate stats (balance, total commissions)
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        $totalEarned = Commission::where('recipient_id', $user->id)
            ->where('status', 'Success')
            ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $user->balance,
                'total_earned' => $totalEarned,
                'referral_code' => $user->referral_code,
                'total_referrals' => Commission::where('recipient_id', $user->id)->count(),
            ]
        ]);
    }

    /**
     * List of commissions received
     */
    public function commissions(Request $request)
    {
        $commissions = Commission::with('referredUser:id,username,avatar')
            ->where('recipient_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $commissions
        ]);
    }

    /**
     * Request withdrawal
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'bank_name' => 'required|string',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
        ]);

        $user = $request->user();

        if ($user->balance < $request->amount) {
            return response()->json(['message' => 'Saldo tidak mencukupi'], 400);
        }

        DB::transaction(function () use ($user, $request) {
            $user->decrement('balance', $request->amount);

            Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_name' => $request->account_name,
                'status' => 'pending'
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Permintaan penarikan berhasil diajukan'
        ]);
    }

    /**
     * List withdrawal history
     */
    public function withdrawals(Request $request)
    {
        $withdrawals = Withdrawal::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $withdrawals
        ]);
    }
}
