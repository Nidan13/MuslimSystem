<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Updating categories table...\n";

if (Schema::hasTable('categories')) {
    if (!Schema::hasColumn('categories', 'metadata')) {
        Schema::table('categories', function (Blueprint $table) {
            $table->json('metadata')->nullable()->after('description');
        });
        echo "Added metadata column to categories table\n";
    }
    else {
        echo "metadata column already exists\n";
    }
}

echo "Done\n";
