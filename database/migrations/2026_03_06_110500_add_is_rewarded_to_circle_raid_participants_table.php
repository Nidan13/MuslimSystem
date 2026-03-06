<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('circle_raid_participants', function (Blueprint $table) {
            $table->boolean('is_rewarded')->default(false)->after('contribution_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circle_raid_participants', function (Blueprint $table) {
            $table->dropColumn('is_rewarded');
        });
    }
};
