<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Daily Tasks
            ['name' => 'Spiritual', 'type' => 'daily_task', 'icon' => '🌙'],
            ['name' => 'Kesehatan', 'type' => 'daily_task', 'icon' => '💪'],
            ['name' => 'Pendidikan', 'type' => 'daily_task', 'icon' => '📚'],
            ['name' => 'Sosial', 'type' => 'daily_task', 'icon' => '🤝'],
            ['name' => 'Karir', 'type' => 'daily_task', 'icon' => '💼'],
            ['name' => 'Personal', 'type' => 'daily_task', 'icon' => '👤'],

            // Headlines (Berita)
            ['name' => 'Nasional', 'type' => 'berita', 'icon' => '🇮🇩'],
            ['name' => 'Internasional', 'type' => 'berita', 'icon' => '🌎'],
            ['name' => 'Edukasi', 'type' => 'berita', 'icon' => '🎓'],
            ['name' => 'Ekonomi', 'type' => 'berita', 'icon' => '📈'],
            ['name' => 'Teknologi', 'type' => 'berita', 'icon' => '💻'],

            // Islamic Videos (Kajian)
            ['name' => 'Aqidah', 'type' => 'kajian', 'icon' => '💎'],
            ['name' => 'Fiqh', 'type' => 'kajian', 'icon' => '⚖️'],
            ['name' => 'Akhlak', 'type' => 'kajian', 'icon' => '✨'],
            ['name' => 'Quran', 'type' => 'kajian', 'icon' => '📖'],
            ['name' => 'Hadits', 'type' => 'kajian', 'icon' => '📜'],
            ['name' => 'Sejarah', 'type' => 'kajian', 'icon' => '⏳'],

            // Quests
            ['name' => 'Main Quest', 'type' => 'quest', 'icon' => '🏆'],
            ['name' => 'Side Quest', 'type' => 'quest', 'icon' => '📜'],
            ['name' => 'Event Quest', 'type' => 'quest', 'icon' => '🎉'],

            // Dungeons
            ['name' => 'Solo Dungeon', 'type' => 'dungeon', 'icon' => '👤'],
            ['name' => 'Group Dungeon', 'type' => 'dungeon', 'icon' => '👥'],
            ['name' => 'Raid', 'type' => 'dungeon', 'icon' => '⚔️'],

            // Ranks
            ['name' => 'Bronze', 'type' => 'rank', 'icon' => '🥉'],
            ['name' => 'Silver', 'type' => 'rank', 'icon' => '🥈'],
            ['name' => 'Gold', 'type' => 'rank', 'icon' => '🥇'],
            ['name' => 'Platinum', 'type' => 'rank', 'icon' => '💎'],
            ['name' => 'Diamond', 'type' => 'rank', 'icon' => '🔷'],
            ['name' => 'Master', 'type' => 'rank', 'icon' => '👑'],
            ['name' => 'Legend', 'type' => 'rank', 'icon' => '🌟'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'] . '-' . $cat['type'])],
                [
                    'name' => $cat['name'],
                    'type' => $cat['type'],
                    'icon' => $cat['icon'],
                    'is_active' => true,
                ]
            );
        }
    }
}
