<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_campaign_id')->constrained('donation_campaigns');
            $table->string('title');
            $table->text('content');
            $table->json('images')->nullable();
            $table->decimal('amount_spent', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_reports');
    }
};
