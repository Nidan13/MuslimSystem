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
        Schema::create('dungeons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Link to Master Data
            $table->foreignId('dungeon_type_id')->constrained('dungeon_types');
            $table->foreignId('rank_tier_id')->constrained('rank_tiers');
            
            $table->unsignedInteger('min_level_requirement')->default(1);
            $table->foreign('min_level_requirement')->references('level')->on('level_configs');
            $table->integer('reward_soul_points')->default(0);
            $table->json('loot_pool')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dungeons');
    }
};
