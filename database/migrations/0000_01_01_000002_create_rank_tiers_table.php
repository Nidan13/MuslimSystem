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
        Schema::create('rank_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // E, D, C, B, A, S
            $table->string('name');
            $table->string('color_code')->nullable(); // For UI badges
            $table->unsignedInteger('min_level')->default(1);
            $table->foreign('min_level')->references('level')->on('level_configs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rank_tiers');
    }
};
