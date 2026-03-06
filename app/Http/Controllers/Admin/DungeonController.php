<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dungeon;
use Illuminate\Http\Request;

class DungeonController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 12);
        $query = Dungeon::with(['dungeonType', 'rankTier'])->latest();
        
        if ($limit === 'all') {
            $dungeons = $query->get();
        } else {
            $dungeons = $query->paginate((int)$limit);
        }

        return view('admin.dungeons.index', compact('dungeons'));
    }

    public function create()
    {
        $dungeonTypes = \App\Models\DungeonType::all();
        $rankTiers = \App\Models\RankTier::orderBy('min_level', 'desc')->get();
        return view('admin.dungeons.create', compact('dungeonTypes', 'rankTiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dungeon_type_id' => 'required|exists:dungeon_types,id',
            'rank_tier_id' => 'nullable|exists:rank_tiers,id',
            'min_level_requirement' => 'required|integer|min:1',
            'reward_exp' => 'required|integer|min:0',
            'required_players' => 'required|integer|min:1',
            'objective_type' => 'nullable|string|in:quran,prayer,kajian,habit,journal',
            'objective_target' => 'nullable|integer|min:0',
            'loot_pool' => 'nullable|array',
        ]);

        \Log::info('Dungeon Creation Attempt:', $validated);
        try {
            $dungeon = Dungeon::create($validated);
            \Log::info('Dungeon Created:', ['id' => $dungeon->id]);
        } catch (\Exception $e) {
            \Log::error('Dungeon Creation Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Database error: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.dungeons.index')->with('success', 'A new Dungeon gate has opened.');
    }

    public function show(Dungeon $dungeon)
    {
        $dungeon->load(['dungeonType', 'rankTier']);
        return view('admin.dungeons.show', compact('dungeon'));
    }

    public function edit(Dungeon $dungeon)
    {
        $dungeonTypes = \App\Models\DungeonType::all();
        $rankTiers = \App\Models\RankTier::all();
        return view('admin.dungeons.edit', compact('dungeon', 'dungeonTypes', 'rankTiers'));
    }

    public function update(Request $request, Dungeon $dungeon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dungeon_type_id' => 'required|exists:dungeon_types,id',
            'rank_tier_id' => 'nullable|exists:rank_tiers,id',
            'min_level_requirement' => 'required|integer|min:1',
            'reward_exp' => 'required|integer|min:0',
            'required_players' => 'required|integer|min:1',
            'objective_type' => 'nullable|string|in:quran,prayer,kajian,habit,journal',
            'objective_target' => 'nullable|integer|min:0',
            'loot_pool' => 'nullable|array',
        ]);

        try {
            $dungeon->update($validated);
        } catch (\Exception $e) {
            \Log::error('Dungeon Update Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Database error: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.dungeons.index')->with('success', 'Dungeon configuration stabilized.');
    }

    public function destroy(Dungeon $dungeon)
    {
        $dungeon->delete();
        return redirect()->route('admin.dungeons.index')->with('success', 'Dungeon gate successfully closed.');
    }
}
