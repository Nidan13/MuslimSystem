<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')->constrained('users');
            $table->decimal('amount', 15, 2);
            $table->integer('tier'); // 1 = Level 1, 2 = Level 2
            $table->enum('status', ['Pending', 'Success'])->default('Pending');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('commissions');
    }
};