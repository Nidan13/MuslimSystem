<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserStat;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $rankS = \App\Models\RankTier::where('slug', 'S')->first();
        $rankE = \App\Models\RankTier::where('slug', 'E')->first();

        // 1. Suntik User Admin Utama
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // Unik berdasarkan email
            [
                'username' => 'SuperAdmin',
                'password' => Hash::make('password123'),
                'gender' => 'Male',
                'rank_tier_id' => $rankS->id,
                'level' => 99,
                'current_exp' => 0,
                'overflow_exp' => 0,
                'job_class' => 'Al-Mujahid',
                'soul_points' => 1000000,
                'referral_code' => 'ADMIN001',
                'referred_by_id' => null,
            ]
        );

        // 2. Suntik Statistik S-Rank (STR, INT, WIS, VIT)
        UserStat::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'str' => 99,
                'int' => 99,
                'wis' => 99,
                'vit' => 99,
            ]
        );

        // 3. (Opsional) Suntik User Contoh untuk Tes Affiliate
        $subUser = User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'username' => 'HunterLevelE',
                'password' => Hash::make('password123'),
                'gender' => 'Female',
                'rank_tier_id' => $rankE->id,
                'level' => 1,
                'referral_code' => 'HUNTER001',
                'referred_by_id' => $admin->id,
            ]
        );

        UserStat::updateOrCreate(
            ['user_id' => $subUser->id],
            ['str' => 5, 'int' => 5, 'wis' => 5, 'vit' => 5]
        );
    }
}