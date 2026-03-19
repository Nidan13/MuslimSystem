<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DonationCampaign;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or ensure Categories exist
        $categories = [
            ['name' => 'Bencana Alam', 'slug' => 'bencana-alam', 'type' => 'donation', 'icon' => '🌋'],
            ['name' => 'Wakaf Masjid', 'slug' => 'wakaf-masjid', 'type' => 'donation', 'icon' => '🕌'],
            ['name' => 'Pendidikan', 'slug' => 'pendidikan-islam', 'type' => 'donation', 'icon' => '🎓'],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'type' => 'donation', 'icon' => '🏥'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 2. Create an Organizer user
        $organizer = User::updateOrCreate(
            ['email' => 'organizer@muslimapp.com'],
            [
                'username' => 'Mitra Peduli',
                'password' => Hash::make('password'),
                'role' => 'organizer',
                'gender' => 'Male',
                'referral_code' => 'MITRA001',
                'is_active' => true,
            ]
        );

        // 3. Create sample campaigns
        $campaigns = [
            [
                'title' => 'Bantuan Gempa Cianjur Tahap 2',
                'description' => 'Membantu pembangunan kembali rumah-rumah warga yang terdampak gempa bumi di Cianjur.',
                'target_amount' => 500000000,
                'collected_amount' => 125000000,
                'status' => 'active',
                'category_slug' => 'bencana-alam',
                'image' => 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53'
            ],
            [
                'title' => 'Wakaf Pembangunan Menara Masjid Al-Falah',
                'description' => 'Program wakaf pembangunan menara masjid yang akan menjadi ikon syiar Islam di lingkungan pesantren.',
                'target_amount' => 250000000,
                'collected_amount' => 75000000,
                'status' => 'active',
                'category_slug' => 'wakaf-masjid',
                'image' => 'https://images.unsplash.com/photo-1596464716127-f2a82984de30'
            ],
            [
                'title' => 'Beasiswa Santri Penghafal Al-Qur\'an',
                'description' => 'Program beasiswa pendidikan untuk 100 santri yatim dan dhuafa yang sedang menghafal Al-Qur\'an.',
                'target_amount' => 100000000,
                'collected_amount' => 95000000,
                'status' => 'active',
                'category_slug' => 'pendidikan-islam',
                'image' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7'
            ],
        ];

        foreach ($campaigns as $camp) {
            $cat = Category::where('slug', $camp['category_slug'])->first();
            DonationCampaign::updateOrCreate(
                ['slug' => Str::slug($camp['title'])],
                [
                    'organizer_id' => $organizer->id,
                    'category_id' => $cat->id ?? null,
                    'title' => $camp['title'],
                    'description' => $camp['description'],
                    'target_amount' => $camp['target_amount'],
                    'collected_amount' => $camp['collected_amount'],
                    'status' => $camp['status'],
                    'image' => $camp['image']
                ]
            );
        }
    }
}
