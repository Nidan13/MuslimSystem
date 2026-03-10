<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Headline;

class HeadlineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Headline::create([
            'tag' => 'SYSTEM',
            'category' => 'Update',
            'title' => 'Selamat Datang di Muslim System!',
            'content' => 'Sistem manajemen hunter muslim kini telah diperbarui. Silakan lapor jika ada bug wok!',
            'image_url' => 'https://muslim-system.com/banner-welcome.jpg',
            'is_active' => true,
        ]);

        Headline::create([
            'tag' => 'EVENT',
            'category' => 'Ramadhan',
            'title' => 'Event Persiapan Ramadhan',
            'content' => 'Persiapkan dirimu untuk menyambut bulan suci dengan misi-misi khusus.',
            'image_url' => 'https://muslim-system.com/banner-ramadhan.jpg',
            'is_active' => true,
        ]);
    }
}
