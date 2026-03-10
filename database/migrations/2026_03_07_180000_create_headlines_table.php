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
        Schema::create('headlines', function (Blueprint $row) {
            $row->id();
            $row->string('tag');
            $row->string('category')->nullable();
            $row->string('title');
            $row->text('content')->nullable();
            $row->string('image_url')->nullable();
            $row->boolean('is_active')->default(true);
            $row->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('headlines');
    }
};
