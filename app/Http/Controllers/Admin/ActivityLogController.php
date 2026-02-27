<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $logs = $query->paginate(20)->withQueryString();
        $users = User::orderBy('username')->get();
        $types = ActivityLog::distinct()->pluck('type');

        return view('admin.activity-logs.index', compact('logs', 'users', 'types'));
    }

    public function destroy(ActivityLog $activityLog)
    {
        $activityLog->delete();
        return redirect()->route('admin.activity-logs.index')->with('success', 'Activity log record erased.');
    }
}
