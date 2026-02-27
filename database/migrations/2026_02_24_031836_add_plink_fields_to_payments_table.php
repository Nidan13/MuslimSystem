<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'payment_url')) {
                $table->string('payment_url')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_url');
            }
            if (!Schema::hasColumn('payments', 'bank_code')) {
                $table->string('bank_code')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'va_number')) {
                $table->string('va_number')->nullable()->after('bank_code');
            }
            if (!Schema::hasColumn('payments', 'qr_string')) {
                $table->text('qr_string')->nullable()->after('va_number');
            }
            if (!Schema::hasColumn('payments', 'payload')) {
                $table->json('payload')->nullable()->after('qr_string');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['payment_url', 'payment_method', 'bank_code', 'va_number', 'qr_string', 'payload']);
        });
    }
};
