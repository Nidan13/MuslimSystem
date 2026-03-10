<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Database Connection: " . DB::connection()->getDatabaseName() . "\n";
echo "Users table has job_class: " . (Schema::hasColumn('users', 'job_class') ? 'YES' : 'NO') . "\n";

$migrations = DB::table('migrations')->orderBy('id', 'desc')->limit(5)->get();
echo "Last 5 migrations:\n";
foreach($migrations as $m) {
    echo "- " . $m->migration . "\n";
}
