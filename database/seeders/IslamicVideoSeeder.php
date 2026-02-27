<?php

namespace Database\Seeders;

use App\Models\IslamicVideo;
use Illuminate\Database\Seeder;

class IslamicVideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $videos = [
            [
                'title' => 'Keajaiban Sedekah: Membuka Pintu Rezeki',
                'channel' => 'Cahaya Islam',
                'video_url' => 'https://www.youtube.com/watch?v=2E7w8S_pX98',
                'duration' => '10:15',
                'category' => 'Amalan',
                'is_active' => true,
            ],
            [
                'title' => 'Adab Tidur Sesuai Sunnah Rasulullah',
                'channel' => 'Kajian Praktis',
                'video_url' => 'https://www.youtube.com/watch?v=8N_Rj_B5Uiw',
                'duration' => '07:45',
                'category' => 'Amalan',
                'is_active' => true,
            ],
            [
                'title' => 'Kisah Sahabat: Keberanian Ali bin Abi Thalib',
                'channel' => 'Sejarah Muslim',
                'video_url' => 'https://www.youtube.com/watch?v=v7I79YpE3v8',
                'duration' => '15:20',
                'category' => 'Sejarah',
                'is_active' => true,
            ],
            [
                'title' => 'Pentingnya Menuntut Ilmu dalam Islam',
                'channel' => 'Ustadz Adi Hidayat Official',
                'video_url' => 'https://www.youtube.com/watch?v=8mP5x2T2X_A',
                'duration' => '12:45',
                'category' => 'Kajian',
                'is_active' => true,
            ],
            [
                'title' => 'Sabar dalam Menghadapi Ujian Hidup',
                'channel' => 'Kajian Musawarah',
                'video_url' => 'https://www.youtube.com/watch?v=p8itJ_0vK_A',
                'duration' => '08:15',
                'category' => 'Kajian',
                'is_active' => true,
            ],
        ];

        foreach ($videos as $video) {
            IslamicVideo::updateOrCreate(
                ['video_url' => $video['video_url']],
                $video
            );
        }
    }
}
