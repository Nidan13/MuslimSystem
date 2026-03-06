<?php
require __DIR__.'/autoload_app.php'; // Simplified bootstrap for scripts

use App\Models\Dungeon;
use App\Models\CircleRaidParticipant;

echo "--- DUNGEONS ---\n";
foreach (Dungeon::all() as $d) {
    echo "ID: {$d->id} | Name: {$d->name} | Type: {$d->objective_type} | Target: {$d->objective_target}\n";
}

echo "\n--- PARTICIPANTS ---\n";
foreach (CircleRaidParticipant::all() as $p) {
    echo "ID: {$p->id} | User: {$p->user_id} | Circle: {$p->circle_id} | Dungeon: {$p->dungeon_id} | Score: {$p->contribution_score} | Status: {$p->status}\n";
}
