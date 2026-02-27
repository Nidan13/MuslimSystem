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
        Schema::table('quests', function (Blueprint $table) {
            $table->dateTime('starts_at')->nullable()->after('requirements');
            $table->dateTime('expires_at')->nullable()->after('starts_at');
            $table->integer('time_limit')->nullable()->after('expires_at'); // in minutes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quests', function (Blueprint $table) {
            $table->dropColumn(['starts_at', 'expires_at', 'time_limit']);
        });
    }
};
