<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\DungeonType;

class DungeonTypeController extends Controller
{
    public function index()
    {
        $types = DungeonType::withCount('dungeons')->get();
        return view('admin.dungeon-types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.dungeon-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:dungeon_types,slug',
            'name' => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1',
        ]);

        DungeonType::create($validated);
        return redirect()->back()->with('success', 'Dungeon Type created successfully.');
    }

    public function show(DungeonType $dungeonType)
    {
        $dungeonType->load('dungeons');
        return view('admin.dungeon-types.show', compact('dungeonType'));
    }

    public function edit(DungeonType $dungeonType)
    {
        return view('admin.dungeon-types.edit', compact('dungeonType'));
    }

    public function update(Request $request, DungeonType $dungeonType)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:dungeon_types,slug,' . $dungeonType->id,
            'name' => 'required|string|max:255',
            'max_participants' => 'required|integer|min:1',
        ]);

        $dungeonType->update($validated);
        return redirect()->back()->with('success', 'Dungeon Type updated successfully.');
    }

    public function destroy(DungeonType $dungeonType)
    {
        if ($dungeonType->dungeons()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete: Dungeon Type has associated dungeons.');
        }
        $dungeonType->delete();
        return redirect()->back()->with('success', 'Dungeon Type deleted.');
    }
}
