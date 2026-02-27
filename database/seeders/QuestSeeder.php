<?php

namespace Database\Seeders;

use App\Models\Quest;
use Illuminate\Database\Seeder;

class QuestSeeder extends Seeder
{
    public function run(): void
    {
        $daily = \App\Models\QuestType::where('slug', 'daily')->first();
        $hidden = \App\Models\QuestType::where('slug', 'hidden')->first();
        $trial = \App\Models\QuestType::where('slug', 'trial')->first();
        $rankE = \App\Models\RankTier::where('slug', 'E')->first();

        // 1. Daily Quests (Mandatory)
        Quest::updateOrCreate(
            ['title' => 'The Preparation to be Great'],
            [
                'description' => 'Daily ritual to strengthen the soul and body.',
                'quest_type_id' => $daily->id,
                'rank_tier_id' => $rankE->id,
                'reward_exp' => 500,
                'reward_soul_points' => 100,
                'is_mandatory' => true,
                'penalty_fatigue' => 50,
                'starts_at' => now()->startOfDay(),
                'requirements' => [
                    'sholat_5_waktu' => 5,
                    'tilawah_juz' => 1,
                    'sedekah_subuh' => 1,
                ]
            ]
        );

        // 2. Hidden Quests (Midnight Quest)
        Quest::updateOrCreate(
            ['title' => 'Midnight Connection'],
            [
                'description' => 'A secret quest that appears in the stillness of the night.',
                'quest_type_id' => $hidden->id,
                'rank_tier_id' => $rankE->id,
                'reward_exp' => 2000,
                'reward_soul_points' => 500,
                'is_mandatory' => false,
                'starts_at' => now()->startOfDay(),
                'requirements' => [
                    'tahajud' => 1,
                ]
            ]
        );

        // 3. Rank-Up Trial (Rank E to D)
        Quest::updateOrCreate(
            ['title' => 'Rank-Up Trial: Consistency'],
            [
                'description' => 'Prove your worth by maintaining discipline.',
                'quest_type_id' => $trial->id,
                'rank_tier_id' => $rankE->id,
                'reward_exp' => 1000,
                'reward_soul_points' => 1000,
                'is_mandatory' => false,
                'starts_at' => now()->startOfDay(),
                'requirements' => [
                    'sholat_masjid_7_days' => 7,
                ]
            ]
        );
    }
}
