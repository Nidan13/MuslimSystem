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
        Schema::create('user_activities', function (Blueprint $row) {
            $row->id();
            $row->foreignId('user_id')->constrained()->onDelete('cascade');
            $row->string('page_name');
            $row->integer('seconds_spent')->default(0);
            $row->date('active_date');
            $row->timestamps();

            $row->unique(['user_id', 'page_name', 'active_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_activities');
    }
};
