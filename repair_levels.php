<?php

use App\Models\LevelConfig;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "REPAIRING LEVEL CONFIGS...\n";

for ($i = 1; $i <= 100; $i++) {
    $config = LevelConfig::updateOrCreate(
        ['level' => $i],
        ['xp_required' => $i * 1000]
    );
    echo "Level $i: " . $config->xp_required . " EXP\n";
}

echo "REPAIR COMPLETE.\n";
