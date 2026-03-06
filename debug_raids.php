<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Dungeon;
use App\Models\CircleRaidParticipant;
use App\Models\User;

$data = [
    'dungeons' => Dungeon::all(['id', 'name', 'objective_type', 'objective_target', 'required_players']),
    'participants' => CircleRaidParticipant::all(),
    'users' => User::limit(5)->get(['id', 'username']),
];

echo json_encode($data, JSON_PRETTY_PRINT);
