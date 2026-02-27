<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prayers = [
            [
                'name' => 'Subuh',
                'slug' => 'subuh',
                'soul_points' => 100,
                'description' => 'Sholat fardhu di waktu fajar.',
                'icon' => '🌅',
            ],
            [
                'name' => 'Dzuhur',
                'slug' => 'dzuhur',
                'soul_points' => 100,
                'description' => 'Sholat fardhu di waktu siang hari.',
                'icon' => '☀️',
            ],
            [
                'name' => 'Ashar',
                'slug' => 'ashar',
                'soul_points' => 100,
                'description' => 'Sholat fardhu di waktu sore hari.',
                'icon' => '🌤️',
            ],
            [
                'name' => 'Maghrib',
                'slug' => 'maghrib',
                'soul_points' => 100,
                'description' => 'Sholat fardhu di waktu matahari terbenam.',
                'icon' => '🌆',
            ],
            [
                'name' => 'Isya',
                'slug' => 'isya',
                'soul_points' => 100,
                'description' => 'Sholat fardhu di waktu malam hari.',
                'icon' => '🌙',
            ],
        ];

        foreach ($prayers as $prayer) {
            \App\Models\Prayer::updateOrCreate(['slug' => $prayer['slug']], $prayer);
        }
    }
}
