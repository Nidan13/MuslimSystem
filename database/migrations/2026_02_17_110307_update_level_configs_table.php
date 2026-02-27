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
        Schema::table('level_configs', function (Blueprint $table) {
            if (Schema::hasColumn('level_configs', 'required_exp')) {
                $table->renameColumn('required_exp', 'xp_required');
            }
            if (!Schema::hasColumn('level_configs', 'stat_points_reward')) {
                $table->integer('stat_points_reward')->default(5);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('level_configs', function (Blueprint $table) {
            if (Schema::hasColumn('level_configs', 'xp_required')) {
                $table->renameColumn('xp_required', 'required_exp');
            }
            if (Schema::hasColumn('level_configs', 'stat_points_reward')) {
                $table->dropColumn('stat_points_reward');
            }
        });
    }
};
