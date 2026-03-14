<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dungeon;
use Illuminate\Http\Request;

use App\Models\Category;

class DungeonController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 12);
<<<<<<< HEAD
        // Eager load relationships for performance
        $query = Dungeon::with(['category', 'rankCategory', 'dungeonType', 'rankTier'])->latest();
=======
        $query = Dungeon::with(['dungeonType', 'rankTier'])->latest();
>>>>>>> main

        if ($limit === 'all') {
            $dungeons = $query->get();
        }
        else {
            $dungeons = $query->paginate((int)$limit);
        }

        return view('admin.dungeons.index', compact('dungeons'));
    }

    public function create()
    {
<<<<<<< HEAD
        $dungeonCategories = Category::byType(Category::TYPE_DUNGEON)->active()->get();
        $rankCategories = Category::byType(Category::TYPE_RANK)->active()->get();
        return view('admin.dungeons.create', compact('dungeonCategories', 'rankCategories'));
=======
        $types = \App\Models\DungeonType::all();
        $rankTiers = \App\Models\RankTier::all();
        return view('admin.dungeons.create', compact('types', 'rankTiers'));
>>>>>>> main
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'rank_category_id' => 'nullable|exists:categories,id',
            'min_level_requirement' => 'required|integer|min:1',
            'reward_exp' => 'required|integer|min:0',
            'required_players' => 'required|integer|min:1',
            'objective_type' => 'nullable|string|in:quran,prayer,kajian,habit,journal',
            'objective_target' => 'nullable|integer|min:0',
            'loot_pool' => 'nullable|array',
        ]);

        // Fix: Provide legacy NOT NULL fields if schema hasn't been migrated to nullable yet
        $validated['dungeon_type_id'] = 1; // Default to first type
        $validated['rank_tier_id'] = 1; // Default to first tier

        try {
            Dungeon::create($validated);
        }
        catch (\Exception $e) {
            \Log::error('Dungeon Creation Failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Database error: ' . $e->getMessage()]);
        }

        return redirect()->route('admin.dungeons.index')->with('success', 'A new Dungeon gate has opened.');
    }

    public function show(Dungeon $dungeon)
    {
        $dungeon->load(['category', 'rankCategory']);
        return view('admin.dungeons.show', compact('dungeon'));
    }

    public function edit(Dungeon $dungeon)
    {
<<<<<<< HEAD
        $dungeonCategories = Category::byType(Category::TYPE_DUNGEON)->active()->get();
        $rankCategories = Category::byType(Category::TYPE_RANK)->active()->get();
        return view('admin.dungeons.edit', compact('dungeon', 'dungeonCategories', 'rankCategories'));
=======
        $types = \App\Models\DungeonType::all();
        $rankTiers = \App\Models\RankTier::all();
        return view('admin.dungeons.edit', compact('dungeon', 'types', 'rankTiers'));
>>>>>>> main
    }

    public function update(Request $request, Dungeon $dungeon)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'rank_category_id' => 'nullable|exists:categories,id',
            'min_level_requirement' => 'required|integer|min:1',
            'reward_exp' => 'required|integer|min:0',
            'required_players' => 'required|integer|min:1',
            'objective_type' => 'nullable|string|in:quran,prayer,kajian,habit,journal',
            'objective_target' => 'nullable|integer|min:0',
            'loot_pool' => 'nullable|array',
        ]);

        try {
            $dungeon->update($validated);
        }
        catch (\Exception $e) {
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