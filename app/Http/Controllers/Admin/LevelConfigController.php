<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\LevelConfig;

class LevelConfigController extends Controller
{
    public function index()
    {
        $configs = LevelConfig::orderBy('level', 'asc')->paginate(20);
        return view('admin.level-configs.index', compact('configs'));
    }

    public function create()
    {
        return view('admin.level-configs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'level' => 'required|integer|unique:level_configs,level',
            'xp_required' => 'required|numeric|min:1',
            'stat_points_reward' => 'required|integer|min:0',
        ]);

        LevelConfig::create($validated);
        return redirect()->route('admin.level-configs.index')->with('success', 'Level configuration added.');
    }

    public function show(LevelConfig $levelConfig)
    {
        return view('admin.level-configs.show', ['config' => $levelConfig]);
    }

    public function edit(LevelConfig $levelConfig)
    {
        return view('admin.level-configs.edit', ['config' => $levelConfig]);
    }

    public function update(Request $request, LevelConfig $levelConfig)
    {
        $validated = $request->validate([
            'xp_required' => 'required|numeric|min:1',
            'stat_points_reward' => 'required|integer|min:0',
        ]);

        $levelConfig->update($validated);
        return redirect()->route('admin.level-configs.index')->with('success', 'Level requirement updated.');
    }

    public function destroy(LevelConfig $levelConfig)
    {
        $levelConfig->delete();
        return redirect()->back()->with('success', 'Level deleted.');
    }
}
