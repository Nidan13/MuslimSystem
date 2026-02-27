<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RankTier;

class RankTierController extends Controller
{
    public function index()
    {
        $rankTiers = RankTier::withCount('users')->with('minLevelConfig')->orderBy('min_level', 'asc')->get();
        return view('admin.rank-tiers.index', compact('rankTiers'));
    }

    public function create()
    {
        return view('admin.rank-tiers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:rank_tiers,slug',
            'name' => 'required|string|max:255',
            'min_level_requirement' => 'required|integer|min:1',
            'color_code' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data = $validated;
        $data['min_level'] = $validated['min_level_requirement'];
        unset($data['min_level_requirement']);

        RankTier::create($data);
        return redirect()->route('admin.rank-tiers.index')->with('success', 'Rank Tier created successfully.');
    }

    public function show(RankTier $rankTier)
    {
        $rankTier->load(['users', 'quests', 'dungeons']);
        return view('admin.rank-tiers.show', compact('rankTier'));
    }

    public function edit(RankTier $rankTier)
    {
        return view('admin.rank-tiers.edit', compact('rankTier'));
    }

    public function update(Request $request, RankTier $rankTier)
    {
        $validated = $request->validate([
            'slug' => 'required|unique:rank_tiers,slug,' . $rankTier->id,
            'name' => 'required|string|max:255',
            'min_level_requirement' => 'required|integer|min:1',
            'color_code' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $data = $validated;
        $data['min_level'] = $validated['min_level_requirement'];
        unset($data['min_level_requirement']);

        $rankTier->update($data);
        return redirect()->route('admin.rank-tiers.index')->with('success', 'Rank Tier updated successfully.');
    }

    public function destroy(RankTier $rankTier)
    {
        if ($rankTier->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete: Rank Tier is assigned to hunters.');
        }
        $rankTier->delete();
        return redirect()->back()->with('success', 'Rank Tier deleted.');
    }
}
