<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Circle;
use App\Models\User;
use Illuminate\Http\Request;

class CircleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $circles = Circle::with('leader')->latest()->paginate(10);
        return view('admin.circles.index', compact('circles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.circles.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
        ]);

        $circle = Circle::create($validated);
        
        // Automatically add leader as member with 'leader' role
        $circle->members()->attach($validated['leader_id'], [
            'role' => 'leader',
            'joined_at' => now(),
        ]);

        return redirect()->route('admin.circles.index')->with('success', 'New Circle formed successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Circle $circle)
    {
        $circle->load(['leader', 'members']);
        return view('admin.circles.show', compact('circle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Circle $circle)
    {
        $users = User::all();
        return view('admin.circles.edit', compact('circle', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Circle $circle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'level' => 'required|integer|min:1',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
        ]);

        $oldLeaderId = $circle->leader_id;
        $circle->update($validated);

        // If leader changed, update roles in pivot
        if ($oldLeaderId != $validated['leader_id']) {
            // Demote old leader to member if they were leader
            $circle->members()->updateExistingPivot($oldLeaderId, ['role' => 'member']);
            
            // Promote or Add new leader
            if ($circle->members()->where('user_id', $validated['leader_id'])->exists()) {
                $circle->members()->updateExistingPivot($validated['leader_id'], ['role' => 'leader']);
            } else {
                $circle->members()->attach($validated['leader_id'], [
                    'role' => 'leader',
                    'joined_at' => now(),
                ]);
            }
        }

        return redirect()->route('admin.circles.index')->with('success', 'Circle configuration updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Circle $circle)
    {
        $circle->delete();
        return redirect()->route('admin.circles.index')->with('success', 'Circle dissolved from the System.');
    }
}
