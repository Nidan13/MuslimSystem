<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'title' => 'Paket Umroh Reguler',
                'provider' => 'PT. Travel Berkah',
                'price' => 'Rp 28.500.000',
                'rating' => 4.8,
                'icon' => 'flight_takeoff',
                'color_start' => '#0F172A',
                'color_end' => '#1E293B',
                'badge' => 'Terlaris',
            ],
            [
                'title' => 'Hewan Kurban Sapi',
                'provider' => 'Peternakan Mulia',
                'price' => 'Mulai Rp 20.000.000',
                'rating' => 4.9,
                'icon' => 'pets',
                'color_start' => '#065F46',
                'color_end' => '#047857',
                'badge' => 'Terbaik',
            ],
            [
                'title' => 'Layanan Aqiqah Penuh',
                'provider' => 'Aqiqah Nusantara',
                'price' => 'Rp 2.500.000',
                'rating' => 4.7,
                'icon' => 'child_friendly',
                'color_start' => '#7C2D12',
                'color_end' => '#9A3412',
                'badge' => 'Promo',
            ],
            [
                'title' => 'Paket Umroh Plus Turki',
                'provider' => 'Travel Hijrah',
                'price' => 'Rp 35.000.000',
                'rating' => 5.0,
                'icon' => 'mosque',
                'color_start' => '#312E81',
                'color_end' => '#4338CA',
                'badge' => null,
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(
                ['title' => $vendor['title']],
                $vendor
            );
        }
    }
}
