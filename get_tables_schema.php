<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$results = [
    'dungeons' => Schema::getColumnListing('dungeons'),
    'quests' => Schema::getColumnListing('quests'),
    'rank_tiers' => Schema::getColumnListing('rank_tiers'),
    'dungeon_types' => Schema::getColumnListing('dungeon_types'),
];
file_put_contents('tables_schema.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Done\n";
