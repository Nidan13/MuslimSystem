<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class ManualPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user')
            ->where('payment_method', 'MANUAL')
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $payments = $query->paginate(10);

        return view('admin.payments-manual.index', compact('payments'));
    }

    public function approve(Payment $payment)
    {
        if ($payment->payment_method !== 'MANUAL') {
            return back()->with('error', 'Hanya pembayaran manual yang bisa di-approve manual.');
        }

        try {
            $payment->markAsPaid();
            return back()->with('success', 'Pembayaran telah disetujui dan user telah diaktifkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Payment $payment, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        if ($payment->payment_method !== 'MANUAL') {
            return back()->with('error', 'Hanya pembayaran manual yang bisa di-reject.');
        }

        try {
            $payment->update([
                'status' => 'rejected',
                'payload' => json_encode(array_merge(
                    json_decode($payment->payload, true) ?? [],
                    ['rejection_reason' => $request->rejection_reason]
                ))
            ]);
            return back()->with('success', 'Pembayaran telah ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
