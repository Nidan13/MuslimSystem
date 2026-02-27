<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TodoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $todos = Todo::where('user_id', $user->id)
                     ->where('is_completed', false)
                     ->latest()
                     ->get();

        $completed = Todo::where('user_id', $user->id)
                         ->where('is_completed', true)
                         ->latest()
                         ->limit(10)
                         ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'active' => $todos,
                'completed' => $completed
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'difficulty' => 'required|in:trivial,easy,medium,hard',
            'due_date' => 'nullable|date',
            'checklist' => 'nullable|array',
        ]);

        $todo = Todo::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'notes' => $request->notes,
            'difficulty' => $request->difficulty,
            'due_date' => $request->due_date,
            'checklist' => $request->checklist,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'To-Do created successfully',
            'data' => $todo
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'notes' => 'nullable|string',
            'difficulty' => 'sometimes|required|in:trivial,easy,medium,hard',
            'due_date' => 'nullable|date',
            'checklist' => 'nullable|array',
        ]);

        $todo->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'To-Do updated successfully',
            'data' => $todo
        ]);
    }

    public function complete(Request $request, $id)
    {
        $user = Auth::user();
        $todo = Todo::where('user_id', $user->id)->findOrFail($id);

        if ($todo->is_completed) {
            return response()->json(['success' => false, 'message' => 'Task already completed'], 400);
        }

        $rewards = [
            'trivial' => ['xp' => 5, 'sp' => 2, 'gold' => 1],
            'easy'    => ['xp' => 10, 'sp' => 5, 'gold' => 2],
            'medium'  => ['xp' => 20, 'sp' => 10, 'gold' => 5],
            'hard'    => ['xp' => 40, 'sp' => 20, 'gold' => 10],
        ];

        $reward = $rewards[$todo->difficulty];
        $xpGained = $reward['xp'];
        $spGained = $reward['sp'];
        $goldGained = $reward['gold'];

        DB::transaction(function () use ($user, $todo, $xpGained, $spGained, $goldGained) {
            $todo->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);

            $user->current_exp += $xpGained;
            $user->soul_points += $spGained;
            // Assuming gold is soul_points for now or add a new column
            // For now let's just use soul_points as the main currency
            $user->save();
        });

        return response()->json([
            'success' => true,
            'message' => "Misi selesai! +$xpGained XP, +$spGained SP",
            'data' => [
                'xp_gained' => $xpGained,
                'sp_gained' => $spGained,
                'current_xp' => $user->current_exp,
                'current_sp' => $user->soul_points,
            ]
        ]);
    }

    public function destroy($id)
    {
        $todo = Todo::where('user_id', Auth::id())->findOrFail($id);
        $todo->delete();

        return response()->json([
            'success' => true,
            'message' => 'To-Do deleted successfully'
        ]);
    }
}
