<?php

namespace App\Services;

use App\Models\Quest;
use Illuminate\Support\Facades\DB;

class QuestService
{
    /**
     * Create a new quest protocol.
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            return Quest::create($data);
        });
    }

    /**
     * Update an existing quest configuration.
     */
    public function update(Quest $quest, array $data)
    {
        return DB::transaction(function () use ($quest, $data) {
            $quest->update($data);
            return $quest;
        });
    }

    /**
     * Delete a quest from the system.
     */
    public function delete(Quest $quest)
    {
        return DB::transaction(function () use ($quest) {
            // Future logic: Check if any user is currently doing this quest
            return $quest->delete();
        });
    }
}
