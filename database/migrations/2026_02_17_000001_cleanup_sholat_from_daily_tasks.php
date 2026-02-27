<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove master tasks related to Sholat
        DB::table('daily_tasks')
            ->whereNull('user_id')
            ->where(function ($query) {
                $query->where('name', 'like', '%Sholat%')
                      ->orWhere('name', 'like', '%Prayer%');
            })
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to undo deletion without re-seeding
    }
};
