<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Models\QuestType;
use App\Models\RankTier;
use App\Http\Requests\Admin\QuestRequest;
use App\Services\QuestService;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    protected $questService;

    public function __construct(QuestService $questService)
    {
        $this->questService = $questService;
    }

    public function index()
    {
        $quests = Quest::with(['questType', 'rankTier'])->latest()->paginate(10);
        return view('admin.quests.index', compact('quests'));
    }

    public function create()
    {
        $questTypes = QuestType::all();
        $rankTiers = RankTier::all();
        return view('admin.quests.create', compact('questTypes', 'rankTiers'));
    }

    public function store(QuestRequest $request)
    {
        $validated = $request->validated();

        // Defaults for optional fields
        $validated['reward_exp'] = $validated['reward_exp'] ?? 0;
        $validated['reward_soul_points'] = $validated['reward_soul_points'] ?? 0;
        $validated['penalty_fatigue'] = $validated['penalty_fatigue'] ?? 0;

        // Process requirements into a dictionary
        $requirements = [];
        if (!empty($validated['req_keys']) && !empty($validated['req_values'])) {
            foreach ($validated['req_keys'] as $index => $key) {
                if (!empty($key) && isset($validated['req_values'][$index])) {
                    $requirements[$key] = (int) $validated['req_values'][$index];
                }
            }
        }
        $validated['requirements'] = $requirements;

        $this->questService->store($validated);

        return redirect()->route('admin.quests.index')->with('success', 'New quest added into the System.');
    }

    public function show(Quest $quest)
    {
        $quest->load(['questType', 'rankTier']);
        return view('admin.quests.show', compact('quest'));
    }

    public function edit(Quest $quest)
    {
        $questTypes = QuestType::all();
        $rankTiers = RankTier::all();
        return view('admin.quests.edit', compact('quest', 'questTypes', 'rankTiers'));
    }

    public function update(QuestRequest $request, Quest $quest)
    {
        $validated = $request->validated();

        // Defaults for optional fields
        $validated['reward_exp'] = $validated['reward_exp'] ?? 0;
        $validated['reward_soul_points'] = $validated['reward_soul_points'] ?? 0;
        $validated['penalty_fatigue'] = $validated['penalty_fatigue'] ?? 0;

        // Process requirements into a dictionary
        $requirements = [];
        if (!empty($validated['req_keys']) && !empty($validated['req_values'])) {
            foreach ($validated['req_keys'] as $index => $key) {
                if (!empty($key) && isset($validated['req_values'][$index])) {
                    $requirements[$key] = (int) $validated['req_values'][$index];
                }
            }
        }
        $validated['requirements'] = $requirements;

        $this->questService->update($quest, $validated);

        return redirect()->route('admin.quests.index')->with('success', 'Quest updated successfully.');
    }

    public function destroy(Quest $quest)
    {
        $this->questService->delete($quest);
        return redirect()->route('admin.quests.index')->with('success', 'Quest erased from the System.');
    }
}
