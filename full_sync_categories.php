<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;

echo "Starting Comprehensive Sync...\n";

// 1. Add missing columns if needed
if (Schema::hasTable('dungeons') && !Schema::hasColumn('dungeons', 'category_id')) {
    Schema::table('dungeons', function (Blueprint $table) {
        $table->foreignId('category_id')->nullable()->after('dungeon_type_id')->constrained('categories')->nullOnDelete();
    });
    echo "Added category_id to dungeons\n";
}

if (Schema::hasTable('quests') && !Schema::hasColumn('quests', 'rank_category_id')) {
    Schema::table('quests', function (Blueprint $table) {
        $table->foreignId('rank_category_id')->nullable()->after('rank_tier_id')->constrained('categories')->nullOnDelete();
    });
    echo "Added rank_category_id to quests\n";
}

if (Schema::hasTable('dungeons') && !Schema::hasColumn('dungeons', 'rank_category_id')) {
    Schema::table('dungeons', function (Blueprint $table) {
        $table->foreignId('rank_category_id')->nullable()->after('rank_tier_id')->constrained('categories')->nullOnDelete();
    });
    echo "Added rank_category_id to dungeons\n";
}

// 2. Sync Media Categories (Hardcoded)
$mediaCategories = ['Umum', 'Kajian Intensif', 'Siniar Islami', 'Sejarah Islam', 'Panduan Amalan'];
foreach ($mediaCategories as $mc) {
    DB::table('categories')->updateOrInsert(
    ['name' => $mc, 'type' => 'kajian'],
    ['slug' => Str::slug($mc . '-kajian'), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
    );
}
echo "Synced Media categories\n";

// 3. Sync Dungeon Types
if (Schema::hasTable('dungeon_types')) {
    $dungeonTypes = DB::table('dungeon_types')->get();
    foreach ($dungeonTypes as $dt) {
        DB::table('categories')->updateOrInsert(
        ['name' => $dt->name, 'type' => 'dungeon'],
        [
            'slug' => $dt->slug . '-dungeon',
            'is_active' => true,
            'metadata' => json_encode(['max_participants' => $dt->max_participants]),
            'created_at' => now(),
            'updated_at' => now()
        ]
        );
        $cat = DB::table('categories')->where('name', $dt->name)->where('type', 'dungeon')->first();
        if ($cat) {
            DB::table('dungeons')->where('dungeon_type_id', $dt->id)->update(['category_id' => $cat->id]);
        }
    }
}
echo "Synced Dungeon categories\n";

// 4. Sync Rank Tiers
if (Schema::hasTable('rank_tiers')) {
    $rankTiers = DB::table('rank_tiers')->get();
    foreach ($rankTiers as $rt) {
        // Map color_code to color (if hex)
        $color = (strpos($rt->color_code, '#') === 0) ? $rt->color_code : null;

        DB::table('categories')->updateOrInsert(
        ['name' => $rt->name, 'type' => 'rank'],
        [
            'slug' => $rt->slug . '-rank',
            'is_active' => true,
            'color' => $color,
            'description' => $rt->description,
            'metadata' => json_encode(['min_level' => $rt->min_level, 'old_color_code' => $rt->color_code]),
            'created_at' => now(),
            'updated_at' => now()
        ]
        );
        $cat = DB::table('categories')->where('name', $rt->name)->where('type', 'rank')->first();
        if ($cat) {
            DB::table('quests')->where('rank_tier_id', $rt->id)->update(['rank_category_id' => $cat->id]);
            DB::table('dungeons')->where('rank_tier_id', $rt->id)->update(['rank_category_id' => $cat->id]);
        }
    }
}
echo "Synced Rank categories\n";

echo "Sync Finished!\n";
