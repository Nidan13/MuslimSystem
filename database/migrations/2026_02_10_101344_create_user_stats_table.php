<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('user_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Statistik Hunter (Solo Leveling Style)
            $table->integer('str')->default(0); // Strength: Berdasarkan habit fisik
            $table->integer('int')->default(0); // Intelligence: Berdasarkan habit belajar
            $table->integer('wis')->default(0); // Wisdom: Berdasarkan habit refleksi
            $table->integer('vit')->default(0); // Vitality: Berdasarkan habit kesehatan
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('user_stats');
    }
};