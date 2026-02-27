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
            // Daily Tasks
            ['name' => 'Sholat Tahajud', 'description' => 'Sholat sunnah di sepertiga malam', 'category' => 'Ibadah', 'icon' => '🌙', 'soul_points' => 20, 'type' => 'task'],
            ['name' => 'Sholat Witir', 'description' => 'Sholat sunnah penutup sholat malam', 'category' => 'Ibadah', 'icon' => '🕌', 'soul_points' => 15, 'type' => 'task'],
            ['name' => 'Sholat Rawatib', 'description' => 'Sholat sunnah sebelum/sesudah sholat fardhu', 'category' => 'Ibadah', 'icon' => '✨', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Sholat Subuh', 'description' => 'Menunaikan sholat subuh berjamaah', 'category' => 'Ibadah', 'icon' => '🌄', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Sholat Dzuhur', 'description' => 'Menunaikan sholat dzuhur tepat waktu', 'category' => 'Ibadah', 'icon' => '☀️', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Sholat Ashar', 'description' => 'Menunaikan sholat ashar tepat waktu', 'category' => 'Ibadah', 'icon' => '🌅', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Sholat Magrib', 'description' => 'Menunaikan sholat magrib berjamaah', 'category' => 'Ibadah', 'icon' => '🌇', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Sholat Isya', 'description' => 'Menunaikan sholat isya berjamaah', 'category' => 'Ibadah', 'icon' => '🌌', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Shodaqoh Shubuh', 'description' => 'Bersedekah setelah sholat subuh', 'category' => 'Ibadah', 'icon' => '💰', 'soul_points' => 15, 'type' => 'task'],
            ['name' => 'Harwat (Hadir Tepat Waktu)', 'description' => 'Berada di masjid sebelum adzan', 'category' => 'Personal', 'icon' => '⌚', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Baca Quran', 'description' => 'Membaca Al-Quran harian', 'category' => 'Ibadah', 'icon' => '📖', 'soul_points' => 15, 'type' => 'task'],
            ['name' => 'Hafalan', 'description' => 'Menambah atau murojaah hafalan', 'category' => 'Ibadah', 'icon' => '🧠', 'soul_points' => 20, 'type' => 'task'],
            ['name' => 'Baca Buku', 'description' => 'Membaca buku pengetahuan/agama', 'category' => 'Productive', 'icon' => '📚', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Jalan Pagi', 'description' => 'Berjalan santai menghirup udara segar', 'category' => 'Health', 'icon' => '🚶', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Work Out', 'description' => 'Olahraga rutin menjaga stamina', 'category' => 'Health', 'icon' => '💪', 'soul_points' => 15, 'type' => 'task'],
            ['name' => 'Berkebun', 'description' => 'Merawat tanaman di halaman', 'category' => 'Personal', 'icon' => '🌿', 'soul_points' => 10, 'type' => 'task'],
            ['name' => 'Sholat Tobat', 'description' => 'Sholat memohon ampunan Allah', 'category' => 'Ibadah', 'icon' => '🤲', 'soul_points' => 20, 'type' => 'task'],

            // Habits
            ['name' => 'Minum Air Putih 2L', 'description' => 'Jaga hidrasi tubuh sepanjang hari', 'category' => 'Kesehatan', 'icon' => '💧', 'soul_points' => 5, 'type' => 'habit'],
            ['name' => 'Bangun Subuh', 'description' => 'Bangun sebelum adzan subuh', 'category' => 'Disiplin', 'icon' => '⏰', 'soul_points' => 10, 'type' => 'habit'],
            ['name' => 'Dzikir Pagi Petang', 'description' => 'Senantiasa mengingat Allah', 'category' => 'Spiritual', 'icon' => '📿', 'soul_points' => 10, 'type' => 'habit'],
            ['name' => 'Sabar & Gak Ngeluh', 'description' => 'Menjaga lisan dari keluhan', 'category' => 'Karakter', 'icon' => '🤐', 'soul_points' => 15, 'type' => 'habit'],

            // To-Dos
            ['name' => 'Khatam Quran Ramadan', 'description' => 'Target menyelesaikan 30 Juz', 'category' => 'Target', 'icon' => '📜', 'soul_points' => 100, 'type' => 'todo'],
            ['name' => 'Kunjungi Orang Tua', 'description' => 'Silaturahmi ke rumah orang tua', 'category' => 'Relasi', 'icon' => '🤝', 'soul_points' => 50, 'type' => 'todo'],
            ['name' => 'Kursus Tahsin', 'description' => 'Memperbaiki bacaan Al-Quran', 'category' => 'Ilmu', 'icon' => '🎓', 'soul_points' => 30, 'type' => 'todo'],
        ];

        foreach ($templates as $template) {
            TaskTemplate::create($template);
        }
    }
}
