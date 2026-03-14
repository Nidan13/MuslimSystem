<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('provider');
            $table->string('price');
            $table->float('rating')->default(0);
            $table->string('icon')->default('store');
            $table->string('color_start')->default('#0F172A');
            $table->string('color_end')->default('#1E293B');
            $table->string('badge')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
