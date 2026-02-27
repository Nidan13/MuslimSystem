<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('user')->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(10);

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    public function update(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:completed,rejected',
            'rejection_reason' => 'required_if:status,rejected'
        ]);

        try {
            DB::transaction(function () use ($request, $withdrawal) {
                $withdrawal->update([
                    'status' => $request->status,
                    'rejection_reason' => $request->rejection_reason,
                    'processed_at' => now()
                ]);

                if ($request->status === 'rejected') {
                    // Refund to user balance
                    $user = $withdrawal->user;
                    $user->increment('balance', $withdrawal->amount);
                }
            });

            return back()->with('success', 'Permintaan penarikan telah diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
