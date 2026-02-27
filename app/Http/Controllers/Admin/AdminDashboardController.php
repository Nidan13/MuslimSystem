<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Quest;
use App\Models\Dungeon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_quests' => Quest::count(),
            'total_dungeons' => Dungeon::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
