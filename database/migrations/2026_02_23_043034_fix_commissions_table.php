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
        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignId('referred_user_id')->after('recipient_id')->nullable()->constrained('users');
            $table->foreignId('payment_id')->after('referred_user_id')->nullable()->constrained('payments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropForeign(['referred_user_id']);
            $table->dropForeign(['payment_id']);
            $table->dropColumn(['referred_user_id', 'payment_id']);
        });
    }
};
