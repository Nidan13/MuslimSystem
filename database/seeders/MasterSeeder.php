<?php

namespace Database\Seeders;

use App\Models\QuestType;
use App\Models\RankTier;
use App\Models\DungeonType;
use Illuminate\Database\Seeder;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Level Configurations (Priority 1)
        for ($i = 1; $i <= 100; $i++) {
            \App\Models\LevelConfig::updateOrCreate(
                ['level' => $i],
                ['xp_required' => $i * 1000] // Simple linear for start, can be changed later
            );
        }

        // 1. Quest Types
        $questTypes = [
            ['slug' => 'daily', 'name' => 'Daily Quest', 'description' => 'Mandatory missions for daily discipline.'],
            ['slug' => 'hidden', 'name' => 'Hidden Quest', 'description' => 'Secret missions triggered by specific actions.'],
            ['slug' => 'trial', 'name' => 'Rank-Up Trial', 'description' => 'Dangerous trials to advance to higher hunter ranks.'],
            ['slug' => 'raid', 'name' => 'System Raid', 'description' => 'Collective boss battles for grand rewards.'],
        ];

        foreach ($questTypes as $type) {
            QuestType::updateOrCreate(['slug' => $type['slug']], $type);
        }

        // 2. Rank Tiers
        $ranks = [
            ['slug' => 'E', 'name' => 'Rank E', 'min_level' => 1, 'color_code' => 'text-slate-400'],
            ['slug' => 'D', 'name' => 'Rank D', 'min_level' => 10, 'color_code' => 'text-green-400'],
            ['slug' => 'C', 'name' => 'Rank C', 'min_level' => 30, 'color_code' => 'text-blue-400'],
            ['slug' => 'B', 'name' => 'Rank B', 'min_level' => 50, 'color_code' => 'text-purple-400'],
            ['slug' => 'A', 'name' => 'Rank A', 'min_level' => 70, 'color_code' => 'text-orange-400'],
            ['slug' => 'S', 'name' => 'Rank S', 'min_level' => 90, 'color_code' => 'text-red-500'],
        ];

        foreach ($ranks as $rank) {
            RankTier::updateOrCreate(['slug' => $rank['slug']], $rank);
        }

        // 3. Dungeon Types
        $dungeonTypes = [
            ['slug' => 'solo', 'name' => 'Instant Dungeon', 'max_participants' => 1],
            ['slug' => 'party', 'name' => 'Party Dungeon', 'max_participants' => 4],
            ['slug' => 'raid', 'name' => 'Great Raid', 'max_participants' => 100],
        ];

        foreach ($dungeonTypes as $dType) {
            DungeonType::updateOrCreate(['slug' => $dType['slug']], $dType);
        }
    }
}
