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
        // Add new columns to headlines table
        Schema::table('headlines', function (Blueprint $table) {
            $table->boolean('is_for_user')->default(true)->after('is_active');
            $table->boolean('is_for_landing_page')->default(false)->after('is_for_user');
            $table->text('summary')->nullable()->after('content');
            $table->string('slug')->nullable()->unique()->after('title');
            $table->json('images')->nullable()->after('image_url');
        });

        // Insert default theme settings
        DB::table('settings')->insertOrIgnore([
            ['key' => 'landing_page_primary_color', 'value' => '#008b76', 'type' => 'string'],
            ['key' => 'landing_page_secondary_color', 'value' => '#0a2f4c', 'type' => 'string'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('headlines', function (Blueprint $table) {
            $table->dropColumn(['is_for_user', 'is_for_landing_page', 'summary', 'slug']);
        });

        DB::table('settings')->whereIn('key', [
            'landing_page_primary_color',
            'landing_page_secondary_color'
        ])->delete();
    }
};
