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
        Schema::create('user_daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('daily_task_id')->constrained()->onDelete('cascade');
            $table->timestamp('completed_at')->nullable();
            $table->date('date'); // Track which day the task was completed
            $table->timestamps();
            
            // Prevent duplicate completions: user can only complete each task once per day
            $table->unique(['user_id', 'daily_task_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_daily_tasks');
    }
};
