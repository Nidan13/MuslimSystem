<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DailyTask;

class DailyTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // No master tasks for now, as prayers are handled by PrayerLog
        // and other activities should be user-defined journals.
        // If we want master "recommended" journals, they can be added here.
        
        DailyTask::whereNull('user_id')->delete();
    }
}
