<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Get Summary Stats
        // Total Pendapatan Platform = Total System Fee (hasil potongan aktivasi)
        $totalSystemFee = Payment::where('status', 'paid')->sum('system_fee');
        
        // Total Komisi Hunter = Total Affiliate Fee (bonus referral aktivasi)
        $totalHunterComm = Payment::where('status', 'paid')->sum('affiliate_fee');

        $stats = [
            'total_bruto' => Payment::where('status', 'paid')->sum('amount'),
            'total_system_fee' => $totalSystemFee,
            'total_affiliate_fee' => $totalHunterComm,
        ];

        // 2. Get Real SHU Breakdown from PaymentDistribution Table (Historical Data)
        $shu_breakdown = \App\Models\PaymentDistribution::whereHas('payment', function($q) {
                $q->where('status', 'paid');
            })
            ->select('category_name as name', DB::raw('SUM(amount) as amount'))
            ->groupBy('category_name')
            ->get();

        // 3. Monthly Stats (Last 6 Months) - Fixed for PostgreSQL
        $monthlyData = Payment::where('status', 'paid')
            ->select(
                DB::raw('SUM(system_fee) as revenue'),
                DB::raw("TO_CHAR(paid_at, 'MM') as month"),
                DB::raw("TO_CHAR(paid_at, 'YYYY') as year")
            )
            ->groupBy(DB::raw("TO_CHAR(paid_at, 'YYYY')"), DB::raw("TO_CHAR(paid_at, 'MM')"))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get();

        // Recent Revenue Logs
        $logs = Payment::with(['user:id,username'])
            ->where('status', 'paid')
            ->where('system_fee', '>', 0)
            ->orderBy('paid_at', 'desc')
            ->paginate(15);

        return view('admin.reports.revenue', compact('stats', 'monthlyData', 'logs', 'shu_breakdown'));
    }
}
