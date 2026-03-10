<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quest;
use App\Models\Dungeon;
use App\Models\Payment;
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

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPayments'));
    }
}
