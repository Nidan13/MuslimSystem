<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quest;
use App\Models\Dungeon;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_quests' => Quest::count(),
            'total_dungeons' => Dungeon::count(),
            'total_income' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $recentUsers = User::latest()->limit(5)->get();
        $recentPayments = Payment::with('user')->where('status', 'paid')->latest()->limit(5)->get();

        // User growth (7 days)
        $userGrowth = User::where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Financial flow (7 days)
        $financialFlow = Payment::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPayments', 'userGrowth', 'financialFlow'));
    }
}
