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
        Schema::table('headlines', function (Blueprint $table) {
            $table->text('summary')->nullable()->after('title');
            $table->boolean('is_for_user')->default(false)->after('is_active');
            $table->boolean('is_for_landing_page')->default(true)->after('is_for_user');
            $table->json('images')->nullable()->after('image_url');
        });
    }

    public function down(): void
    {
        Schema::table('headlines', function (Blueprint $table) {
            $table->dropColumn(['summary', 'is_for_user', 'is_for_landing_page', 'images']);
        });
    }
};
