<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskTemplate;

class TaskTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // --- DAILY TASKS (Ibadah & Discipline) ---
            ['name' => 'Midnight Connection (Tahajud)', 'description' => 'Sholat sunnah di sepertiga malam terakhir.', 'category' => 'Spiritual', 'icon' => '🌌', 'soul_points' => 30, 'type' => 'task'],
            ['name' => 'Dawn Prayer (Subuh)', 'description' => 'Menunaikan sholat subuh tepat waktu.', 'category' => 'Spiritual', 'icon' => '🌅', 'soul_points' => 15, 'type' => 'task'],
            ['name' => 'Zenith Prayer (Dzuhur)', 'description' => 'Menunaikan sholat dzuhur tepat waktu.', 'category' => 'Spiritual', 'icon' => '☀️', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Golden Hour Prayer (Ashar)', 'description' => 'Menunaikan sholat ashar tepat waktu.', 'category' => 'Spiritual', 'icon' => '🌤️', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Twilight Prayer (Maghrib)', 'description' => 'Menunaikan sholat maghrib tepat waktu.', 'category' => 'Spiritual', 'icon' => '🌇', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Starlight Prayer (Isya)', 'description' => 'Menunaikan sholat isya tepat waktu.', 'category' => 'Spiritual', 'icon' => '🌃', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Sacred Recitation', 'description' => 'Membaca Al-Quran harian dengan tadabbur.', 'category' => 'Spiritual', 'icon' => '📖', 'soul_points' => 20, 'type' => 'task'],
            ['name' => 'Memorization Flow', 'description' => 'Menambah atau mengulang hafalan hari ini.', 'category' => 'Spiritual', 'icon' => '🧠', 'soul_points' => 25, 'type' => 'task'],
            ['name' => 'Morning Alms', 'description' => 'Sedekah subuh untuk membuka pintu rezeki.', 'category' => 'Social', 'icon' => '🪙', 'soul_points' => 20, 'type' => 'task'],
            ['name' => 'Spiritual Shield (Dzikir)', 'description' => 'Dzikir pagi dan petang untuk perlindungan.', 'category' => 'Spiritual', 'icon' => '📿', 'soul_points' => 15, 'type' => 'task'],

            // --- HABITS (Self-Improvement) ---
            ['name' => 'Hydro Hero (2L Water)', 'description' => 'Menjaga hidrasi tubuh tetap optimal.', 'category' => 'Health', 'icon' => '💧', 'soul_points' => 10, 'type' => 'habit'],
            ['name' => 'Sugar-Free Zone', 'description' => 'Menghindari konsumsi gula berlebih hari ini.', 'category' => 'Health', 'icon' => '🚫🍩', 'soul_points' => 15, 'type' => 'habit'],
            ['name' => 'Deep Reading', 'description' => 'Membaca buku minimal 15-30 menit.', 'category' => 'Education', 'icon' => '📑', 'soul_points' => 15, 'type' => 'habit'],
            ['name' => 'Night Rest (Before 11PM)', 'description' => 'Istirahat lebih awal untuk stamina esok hari.', 'category' => 'Health', 'icon' => '🛌', 'soul_points' => 15, 'type' => 'habit'],
            ['name' => 'Full Power Workout', 'description' => 'Latihan fisik atau olahraga rutin.', 'category' => 'Health', 'icon' => '💪', 'soul_points' => 20, 'type' => 'habit'],

            // --- TO-DOS (Goals) ---
            ['name' => 'Grand Khatam Mission', 'description' => 'Menyelesaikan target 30 Juz Al-Quran.', 'category' => 'Spiritual', 'icon' => '📜', 'soul_points' => 100, 'type' => 'todo'],
            ['name' => 'Project Launchpad', 'description' => 'Menyelesaikan milestone besar pekerjaan.', 'category' => 'Career', 'icon' => '🚀', 'soul_points' => 50, 'type' => 'todo'],
            ['name' => 'Inner Circle Reunion', 'description' => 'Berkumpul dengan keluarga atau orang tua.', 'category' => 'Social', 'icon' => '🏠', 'soul_points' => 30, 'type' => 'todo'],
            ['name' => 'Skill Level Up', 'description' => 'Menyelesaikan kursus atau sertifikasi baru.', 'category' => 'Education', 'icon' => '🎓', 'soul_points' => 40, 'type' => 'todo'],

            // --- JOURNALS (Reflection) ---
            ['name' => 'Gratitude Spark', 'description' => 'Mencatat 3 hal yang disyukuri hari ini.', 'category' => 'Personal', 'icon' => '💖', 'soul_points' => 15, 'type' => 'task'],
            ['name' => 'Daily Insight', 'description' => 'Refleksi pencapaian dan pelajaran hari ini.', 'category' => 'Personal', 'icon' => '📝', 'soul_points' => 15, 'type' => 'task'],
            ['name' => 'Soul Review', 'description' => 'Muhasabah diri sebelum tidur.', 'category' => 'Personal', 'icon' => '📓', 'soul_points' => 15, 'type' => 'task'],
        ];

        TaskTemplate::truncate();

        foreach ($templates as $template) {
            TaskTemplate::create($template);
        }
    }
}
