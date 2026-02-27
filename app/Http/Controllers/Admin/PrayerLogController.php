<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerLog;
use App\Models\User;
use Illuminate\Http\Request;

class PrayerLogController extends Controller
{
    public function index(Request $request)
    {
        $query = PrayerLog::with('user')->latest();

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('prayer_name') && $request->prayer_name) {
            $query->where('prayer_name', $request->prayer_name);
        }

        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }

        $logs = $query->paginate(15)->withQueryString();
        $users = User::orderBy('username')->get();

        return view('admin.prayer-logs.index', compact('logs', 'users'));
    }

    public function destroy(PrayerLog $prayerLog)
    {
        $prayerLog->delete();
        return redirect()->route('admin.prayer-logs.index')->with('success', 'Prayer log record erased.');
    }
}
