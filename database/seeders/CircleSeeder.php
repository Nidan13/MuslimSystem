<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Circle;
use App\Models\User;

class CircleSeeder extends Seeder {
    public function run(): void {
        $leader = User::first();
        if (!$leader) return;

        $circles = [
            [
                'name' => 'Sunnah Seekers',
                'description' => 'Waking up together for the ultimate start.',
                'level' => 24,
                'icon' => 'sunny',
                'color' => '#FFFFFF',
                'members_count' => 1200,
                'leader_id' => $leader->id,
            ],
            [
                'name' => 'Night Prayer Elite',
                'description' => 'Tahajjud warriors striving for spiritual excellence.',
                'level' => 42,
                'icon' => 'night',
                'color' => '#1A237E',
                'members_count' => 850,
                'leader_id' => $leader->id,
            ],
            [
                'name' => 'Daily Sadaqah',
                'description' => 'Small acts of charity every single day.',
                'level' => 8,
                'icon' => 'charity',
                'color' => '#00695C',
                'members_count' => 2400,
                'leader_id' => $leader->id,
            ],
            [
                'name' => 'Quran Daily',
                'description' => 'Completing one Juz every month as a group.',
                'level' => 25,
                'icon' => 'quran',
                'color' => '#FFFFFF',
                'members_count' => 842,
                'leader_id' => $leader->id,
            ],
            [
                'name' => 'Morning Adhkar',
                'description' => 'Start your day with the remembrance of Allah.',
                'level' => 15,
                'icon' => 'adhkar',
                'color' => '#EF6C00',
                'members_count' => 1100,
                'leader_id' => $leader->id,
            ],
            [
                'name' => 'Sunnah Fasting',
                'description' => 'Fasting Mondays and Thursdays together.',
                'level' => 5,
                'icon' => 'fasting',
                'color' => '#2E7D32',
                'members_count' => 890,
                'leader_id' => $leader->id,
            ],
            [
                'name' => 'Hadith Study',
                'description' => 'Weekly deep dive into Sahih Bukhari.',
                'level' => 18,
                'icon' => 'study',
                'color' => '#FFFFFF',
                'members_count' => 560,
                'leader_id' => $leader->id,
            ],
            [
                'name' => 'Community Helpers',
                'description' => 'Volunteering for local community events.',
                'level' => 12,
                'icon' => 'community',
                'color' => '#AD1457',
                'members_count' => 320,
                'leader_id' => $leader->id,
            ],
        ];

        foreach ($circles as $circle) {
            Circle::create($circle);
        }
    }
}
