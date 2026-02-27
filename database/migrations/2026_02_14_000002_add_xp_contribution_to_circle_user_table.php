<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('circle_user', 'xp_contribution')) {
            Schema::table('circle_user', function (Blueprint $table) {
                $table->integer('xp_contribution')->default(0)->after('role');
            });
        }
    }

    public function down(): void {
        Schema::table('circle_user', function (Blueprint $table) {
            if (Schema::hasColumn('circle_user', 'xp_contribution')) {
                $table->dropColumn('xp_contribution');
            }
        });
    }
};
