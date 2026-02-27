<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $hunterService;

    public function __construct(\App\Services\HunterService $hunterService)
    {
        $this->hunterService = $hunterService;
    }

    public function index()
    {
        $users = User::with(['userStat', 'rankTier'])->latest()->paginate(15);
        return view('admin.hunters.index', compact('users'));
    }

    public function create()
    {
        $rankTiers = \App\Models\RankTier::orderBy('min_level', 'asc')->get();
        return view('admin.hunters.create', compact('rankTiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:Male,Female',
            'rank_tier_id' => 'required|exists:rank_tiers,id',
            'level' => 'required|integer|min:1',
            'current_exp' => 'required|integer|min:0',
            'soul_points' => 'required|integer|min:0',
            'job_class' => 'nullable|string',
        ]);

        $this->hunterService->store($validated);

        return redirect()->route('admin.hunters.index')->with('success', 'New Hunter registered into the System.');
    }

    public function show(User $hunter)
    {
        $hunter->load(['userStat', 'rankTier']);
        
        // Aggregate Quest Statistics
        $completedQuests = \App\Models\UserQuest::where('user_id', $hunter->id)
            ->where('status', 'completed')
            ->get();
            
        $questStats = [];
        foreach ($completedQuests as $uq) {
            $progress = $uq->progress ?? [];
            foreach ($progress as $key => $value) {
                $label = ucwords(str_replace('_', ' ', $key));
                $questStats[$label] = ($questStats[$label] ?? 0) + (int)$value;
            }
        }

        // Fetch Habits, Todos, and Daily Tasks
        $habits = \App\Models\Habit::where('user_id', $hunter->id)->latest()->get();
        $todos = \App\Models\Todo::where('user_id', $hunter->id)->latest()->get();
        $userDailyTasks = \App\Models\UserDailyTask::with('dailyTask')
            ->where('user_id', $hunter->id)
            ->whereDate('date', now()->toDateString())
            ->get();

        return view('admin.hunters.show', [
            'user' => $hunter,
            'questStats' => $questStats,
            'completedCount' => $completedQuests->count(),
            'habits' => $habits,
            'todos' => $todos,
            'userDailyTasks' => $userDailyTasks
        ]);
    }

    public function edit(User $hunter)
    {
        $rankTiers = \App\Models\RankTier::orderBy('min_level', 'asc')->get();
        return view('admin.hunters.edit', ['user' => $hunter, 'rankTiers' => $rankTiers]);
    }

    public function update(Request $request, User $hunter)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$hunter->id,
            'email' => 'required|string|email|max:255|unique:users,email,'.$hunter->id,
            'gender' => 'required|in:Male,Female',
            'rank_tier_id' => 'required|exists:rank_tiers,id',
            'level' => 'required|integer|min:1',
            'current_exp' => 'required|integer|min:0',
            'soul_points' => 'required|integer|min:0',
            'job_class' => 'nullable|string',
        ]);

        $this->hunterService->update($hunter, $validated);

        return redirect()->route('admin.hunters.index')->with('success', 'Hunter profile synchronized.');
    }

    public function destroy(User $hunter)
    {
        if ($hunter->id === 1) {
            return redirect()->back()->with('error', 'Cannot eliminate the System Administrator.');
        }
        $hunter->delete();
        return redirect()->route('admin.hunters.index')->with('success', 'Hunter record erased.');
    }
}
