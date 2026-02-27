<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('external_id')->nullable()->unique();
            $table->decimal('amount', 15, 2);
            $table->string('payment_url')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('bank_code')->nullable();
            $table->string('va_number')->nullable();
            $table->text('qr_string')->nullable();
            $table->json('payload')->nullable();
            $table->enum('status', ['pending', 'paid', 'expired', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
