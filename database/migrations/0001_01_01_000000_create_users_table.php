<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Core Identity
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('gender', ['Male', 'Female']);
            
            $table->foreignId('rank_tier_id')->nullable()->constrained('rank_tiers');
            $table->unsignedInteger('level')->default(1);
            $table->foreign('level')->references('level')->on('level_configs');
            $table->bigInteger('current_exp')->default(0);
            $table->bigInteger('overflow_exp')->default(0);
            $table->enum('job_class', ['Al-Hafizh', 'Al-Muhsin', 'Al-Mujahid'])->nullable();
            $table->bigInteger('soul_points')->default(0);
            
            // Affiliate System
            $table->string('referral_code')->unique();
            $table->foreignId('referred_by_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};