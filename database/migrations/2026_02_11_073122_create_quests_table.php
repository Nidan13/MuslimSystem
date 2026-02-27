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
        Schema::create('quests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Link to Master Data
            $table->foreignId('quest_type_id')->constrained('quest_types');
            $table->foreignId('rank_tier_id')->constrained('rank_tiers');
            
            $table->integer('reward_exp')->default(0);
            $table->integer('reward_soul_points')->default(0);
            $table->boolean('is_mandatory')->default(false);
            $table->integer('penalty_fatigue')->default(0);
            $table->json('requirements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quests');
    }
};
