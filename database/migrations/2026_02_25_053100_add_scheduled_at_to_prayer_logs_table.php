<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prayer_logs', function (Blueprint $table) {
            $table->timestamp('scheduled_at')->nullable()->after('date');
            $table->boolean('is_punished')->default(false)->after('is_completed');
            $table->timestamp('punished_at')->nullable()->after('is_punished');
        });
    }

    public function down(): void
    {
        Schema::table('prayer_logs', function (Blueprint $table) {
            $table->dropColumn(['scheduled_at', 'is_punished', 'punished_at']);
        });
    }
};
