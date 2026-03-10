<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$results = [];

$tables = ['quest_types', 'dungeon_types', 'rank_tiers', 'islamic_videos', 'shop_items', 'task_templates', 'categories'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        $results[$table] = [
            'count' => DB::table($table)->count(),
            'data' => DB::table($table)->limit(10)->get()->toArray()
        ];
    }
    else {
        $results[$table] = 'Not found';
    }
}

file_put_contents('deep_debug.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Done deep debug\n";
