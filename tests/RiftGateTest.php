<?php
require __DIR__.'/../autoload_app.php';

use App\Models\User;
use App\Models\Circle;
use App\Models\Dungeon;
use App\Models\CircleRaidParticipant;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

// 1. Get a dummy user and circle
$user = User::first();
$circle = Circle::first();

if (!$user || !$circle) {
    die("Error: User or Circle not found. Run seeders first.\n");
}

echo "Testing for User: {$user->username} in Circle: {$circle->name}\n";

// 2. Create a test Dungeon (Rift Gate)
$dungeon = Dungeon::updateOrCreate(
    ['name' => 'VERIFICATION RAID'],
    [
        'description' => 'A test dungeon to verify progress logic.',
        'dungeon_type_id' => 1, // Raid
        'rank_tier_id' => 1,
        'min_level_requirement' => 1,
        'required_players' => 1,
        'reward_exp' => 1000,
        'objective_type' => 'prayer',
        'objective_target' => 5
    ]
);

// 3. User joins the lobby
CircleRaidParticipant::updateOrCreate(
    [
        'circle_id' => $circle->id,
        'dungeon_id' => $dungeon->id,
        'user_id' => $user->id,
    ],
    ['status' => 'ready', 'contribution_score' => 0]
);

echo "Joined RAID: {$dungeon->name}. Initial Progress: 0/5\n";

// 4. Simulate prayer completion
echo "Simulating 3 prayer completions...\n";
$user->updateRiftGateProgress('prayer', 3);

$participant = CircleRaidParticipant::where('user_id', $user->id)
    ->where('dungeon_id', $dungeon->id)
    ->first();

echo "Current Contribution: {$participant->contribution_score}/5\n";
echo "Status: {$participant->status}\n";

// 5. Complete the objective
echo "Simulating 3 more prayer completions (exceeding target)...\n";
$user->updateRiftGateProgress('prayer', 3);

$participant->refresh();
echo "Final Contribution: {$participant->contribution_score}/5\n";
echo "Final Status: {$participant->status}\n";

if ($participant->status === 'cleared') {
    echo "\nSUCCESS: Rift Gate progress logic is working!\n";
    
    // Check reward
    $log = ActivityLog::where('user_id', $user->id)
        ->where('type', 'raid_cleared')
        ->latest()
        ->first();
        
    if ($log) {
        echo "Activity Log Found: {$log->description}\n";
    }
} else {
    echo "\nFAILURE: Rift Gate status is not 'cleared'.\n";
}
