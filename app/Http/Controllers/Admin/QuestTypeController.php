<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\QuestType;

class QuestTypeController extends Controller
{
    public function index()
    {
        $types = QuestType::withCount('quests')->get();
        return view('admin.quest-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.quest-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:quest_types,slug',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        QuestType::create($validated);
        return redirect()->back()->with('success', 'Quest Type created successfully.');
    }

    public function show(QuestType $questType)
    {
        $questType->load('quests');
        return view('admin.quest-types.show', compact('questType'));
    }

    public function edit(QuestType $questType)
    {
        return view('admin.quest-types.edit', compact('questType'));
    }

    public function update(Request $request, QuestType $questType)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:quest_types,slug,' . $questType->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $questType->update($validated);
        return redirect()->back()->with('success', 'Quest Type updated successfully.');
    }

    public function destroy(QuestType $questType)
    {
        if ($questType->quests()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete: Quest Type is in use.');
        }
        $questType->delete();
        return redirect()->back()->with('success', 'Quest Type deleted.');
    }
}
