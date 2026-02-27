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
        Schema::table('dungeons', function (Blueprint $table) {
            $table->unsignedInteger('required_players')->default(1)->after('reward_soul_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dungeons', function (Blueprint $table) {
            $table->dropColumn('required_players');
        });
    }
};
