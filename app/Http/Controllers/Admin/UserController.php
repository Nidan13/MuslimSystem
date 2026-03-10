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
            'level' => 'nullable|integer|min:1',
            'current_exp' => 'nullable|integer|min:0',
            'soul_points' => 'nullable|integer|min:0',
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

        // --- SYNCHRONIZED STATS WITH USER APP ---

        // 1. Total Surah (Completed Surahs from progress table)
        // This is the most "hard" progress metric for Quran
        $totalSurah = \DB::table('user_quran_progress')
            ->where('user_id', $hunter->id)
            ->where('is_completed', true)
            ->count();

        // 2. Total Sholat (Using User Model Accessor)
        $totalSholat = $hunter->sholat_count;

        // 3. Total Misi (Completed Quests - matches 'Adab' in User App Profile)
        $totalMisi = \App\Models\UserQuest::where('user_id', $hunter->id)
            ->where('status', 'completed')
            ->count();

        // 4. Total Kajian (Non-Quran Ilmu activities)
        // Logic: Total Ilmu Count from model MINUS the Quran specific parts
        $quranHistory = \App\Models\QuranReadingHistory::where('user_id', $hunter->id)->count();
        $totalKajian = max(0, $hunter->ilmu_count - ($quranHistory + $totalSurah));

        // Add manual Activity Logs for Kajian/Videos (if any)
        $totalKajian += \App\Models\ActivityLog::where('user_id', $hunter->id)
            ->where(function ($q) {
            $q->where('type', 'like', '%video%')
                ->orWhere('description', 'like', '%menonton%')
                ->orWhere('description', 'like', '%kajian%')
                ->orWhere('description', 'like', '%tahsin%');
        })->count();

        // 5. Habit Matrix (Sum of all habit repetitions)
        $habits = \App\Models\Habit::where('user_id', $hunter->id)->latest()->get();
        $totalHabit = $habits->sum('count');

        // 6. Daily Task (Total completions)
        $totalDailyTask = \App\Models\UserDailyTask::where('user_id', $hunter->id)->count();
        $dailyTaskToday = \App\Models\UserDailyTask::where('user_id', $hunter->id)
            ->whereDate('date', now()->toDateString())
            ->count();

        // --- RADAR CHART DATA (Pentagon) ---
        $radarData = [
            'labels' => ['SURAH', 'SHOLAT', 'MISI', 'KAJIAN', 'HABIT'],
            'values' => [
                min(100, ($totalSurah / 114) * 100), // Target 114 Surahs
                min(100, ($totalSholat / 100) * 100), // Milestone 100 Sholat
                min(100, ($totalMisi / 50) * 100), // Milestone 50 Misi
                min(100, ($totalKajian / 30) * 100), // Milestone 30 Kajian
                min(100, ($totalHabit / 100) * 100), // Milestone 100 Habit Nodes
            ]
        ];

        // Fetch Todos and Daily Tasks
        $todos = \App\Models\Todo::where('user_id', $hunter->id)->latest()->get();
        $userDailyTasks = \App\Models\UserDailyTask::with('dailyTask')
            ->where('user_id', $hunter->id)
            ->whereDate('date', now()->toDateString())
            ->get();

        return view('admin.hunters.show', [
            'user' => $hunter,
            'questStats' => $questStats,
            'completedCount' => $totalMisi,
            'habits' => $habits,
            'todos' => $todos,
            'userDailyTasks' => $userDailyTasks,
            'stats' => [
                'totalSurah' => $totalSurah,
                'totalSholat' => $totalSholat,
                'totalMisi' => $totalMisi,
                'totalKajian' => $totalKajian,
                'totalHabit' => $totalHabit,
                'totalDailyTask' => $totalDailyTask,
                'dailyTaskToday' => $dailyTaskToday,
            ],
            'radarData' => $radarData
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
            'username' => 'required|string|max:255|unique:users,username,' . $hunter->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $hunter->id,
            'password' => 'nullable|string|min:8',
            'gender' => 'required|in:Male,Female',
            'rank_tier_id' => 'required|exists:rank_tiers,id',
            'level' => 'nullable|integer|min:1',
            'current_exp' => 'nullable|integer|min:0',
            'soul_points' => 'nullable|integer|min:0',
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
