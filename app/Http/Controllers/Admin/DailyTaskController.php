<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyTask;
use App\Models\User;
use Illuminate\Http\Request;

class DailyTaskController extends Controller
{
    /**
     * Display master daily tasks
     */
    public function index()
    {
        $tasks = DailyTask::master()->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.daily-tasks.index', compact('tasks'));
    }

    /**
     * Show form to create new master task
     */
    public function create()
    {
        return view('admin.daily-tasks.create');
    }

    /**
     * Store new master task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'soul_points' => 'required|integer|min:10|max:200',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        DailyTask::create([
            'user_id' => null, // Master task
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'soul_points' => $validated['soul_points'],
            'icon' => $validated['icon'] ?? '⭐',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.daily-tasks.index')->with('success', 'Master task berhasil ditambahkan!');
    }

    /**
     * Show master task detail
     */
    public function show($id)
    {
        $task = DailyTask::master()->findOrFail($id);
        
        return view('admin.daily-tasks.show', compact('task'));
    }

    /**
     * Show form to edit master task
     */
    public function edit($id)
    {
        $task = DailyTask::master()->findOrFail($id);
        
        return view('admin.daily-tasks.edit', compact('task'));
    }

    /**
     * Update master task
     */
    public function update(Request $request, $id)
    {
        $task = DailyTask::master()->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'soul_points' => 'required|integer|min:10|max:200',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $task->update($validated);

        return redirect()->route('admin.daily-tasks.index')->with('success', 'Master task berhasil diupdate!');
    }

    /**
     * Delete master task
     */
    public function destroy($id)
    {
        $task = DailyTask::master()->findOrFail($id);
        $task->delete();

        return redirect()->route('admin.daily-tasks.index')->with('success', 'Master task berhasil dihapus!');
    }

    /**
     * Show users who have custom tasks
     */
    public function usersWithCustomTasks()
    {
        $users = User::whereHas('dailyTasks')->withCount('dailyTasks')->paginate(10);
        
        return view('admin.daily-tasks.users', compact('users'));
    }

    /**
     * Show specific user's custom tasks
     */
    public function userCustomTasks($userId)
    {
        $user = User::findOrFail($userId);
        $tasks = DailyTask::custom($userId)->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.daily-tasks.user-tasks', compact('user', 'tasks'));
    }
}
