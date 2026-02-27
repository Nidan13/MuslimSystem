<?php

namespace Database\Seeders;

use App\Models\Dungeon;
use Illuminate\Database\Seeder;

class DungeonSeeder extends Seeder
{
    public function run(): void
    {
        $raid = \App\Models\DungeonType::where('slug', 'raid')->first();
        $solo = \App\Models\DungeonType::where('slug', 'solo')->first();
        
        $rankA = \App\Models\RankTier::where('slug', 'A')->first();
        $rankB = \App\Models\RankTier::where('slug', 'B')->first();

        Dungeon::updateOrCreate(
            ['name' => 'The Sloth Monster Raid'],
            [
                'description' => 'A collective effort to defeat the monster of laziness. Requires heavy consistency in tilawah.',
                'rank_tier_id' => $rankB->id,
                'dungeon_type_id' => $raid->id,
                'min_level_requirement' => 5,
                'reward_soul_points' => 5000,
            ]
        );

        Dungeon::updateOrCreate(
            ['name' => 'Morning Prayer Circle'],
            [
                'description' => 'Complete Fajr prayer in congregation for 40 consecutive days.',
                'rank_tier_id' => $rankA->id,
                'dungeon_type_id' => $solo->id,
                'min_level_requirement' => 1,
                'reward_soul_points' => 2500,
            ]
        );

        // --- NEW: Low Level Quests for New Circles ---
        $rankE = \App\Models\RankTier::where('slug', 'E')->first();
        $rankD = \App\Models\RankTier::where('slug', 'D')->first();

        Dungeon::updateOrCreate(
            ['name' => 'Basic Adab Training'],
            [
                'description' => 'A fundamental quest for new hunters. Focus on daily greetings and manners.',
                'rank_tier_id' => $rankE->id,
                'dungeon_type_id' => $raid->id,
                'min_level_requirement' => 1,
                'required_players' => 2,
                'reward_soul_points' => 500,
            ]
        );

        Dungeon::updateOrCreate(
            ['name' => 'The Whispering Shadow'],
            [
                'description' => 'A D-Rank cooperative mission. Requires basic coordination against laziness.',
                'rank_tier_id' => $rankD->id,
                'dungeon_type_id' => $raid->id,
                'min_level_requirement' => 1,
                'required_players' => 2,
                'reward_soul_points' => 1200,
            ]
        );
    }
}
