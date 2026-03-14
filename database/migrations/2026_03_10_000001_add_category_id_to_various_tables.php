<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Daily Tasks
        if (Schema::hasTable('daily_tasks')) {
            Schema::table('daily_tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_tasks', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('icon')->constrained('categories')->onDelete('set null');
                }
            });
        }

        // Islamic Videos
        if (Schema::hasTable('islamic_videos')) {
            Schema::table('islamic_videos', function (Blueprint $table) {
                if (!Schema::hasColumn('islamic_videos', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
                }
            });

            // Migrate existing string categories to related categories
            $videos = DB::table('islamic_videos')->whereNotNull('category')->select('category')->distinct()->get();
            foreach ($videos as $v) {
                $slug = Str::slug($v->category . '-kajian');
                DB::table('categories')->updateOrInsert(
                    ['slug' => $slug, 'type' => 'kajian'],
                    ['name' => $v->category, 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
                );
                $category = DB::table('categories')->where('slug', $slug)->first();
                if ($category) {
                    DB::table('islamic_videos')->where('category', $v->category)->whereNull('category_id')->update(['category_id' => $category->id]);
                }
            }

            Schema::table('islamic_videos', function (Blueprint $table) {
                if (Schema::hasColumn('islamic_videos', 'category')) {
                    $table->renameColumn('category', 'category_legacy');
                }
            });
        }

        // Quests
        if (Schema::hasTable('quests')) {
            Schema::table('quests', function (Blueprint $table) {
                if (!Schema::hasColumn('quests', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('description')->constrained('categories')->onDelete('set null');
                }
                if (!Schema::hasColumn('quests', 'rank_category_id')) {
                    $table->foreignId('rank_category_id')->nullable()->after('category_id')->constrained('categories')->onDelete('set null');
                }
                // Make old columns nullable
                $table->foreignId('quest_type_id')->nullable()->change();
                $table->foreignId('rank_tier_id')->nullable()->change();
            });

            // Migrate quest types to categories
            if (Schema::hasTable('quest_types')) {
                $types = DB::table('quest_types')->get();
                foreach ($types as $t) {
                    $slug = Str::slug($t->name . '-quest');
                    DB::table('categories')->updateOrInsert(
                        ['slug' => $slug, 'type' => 'quest'],
                        ['name' => $t->name, 'icon' => $t->icon ?? '📜', 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
                    );
                    $category = DB::table('categories')->where('slug', $slug)->first();
                    if ($category) {
                        DB::table('quests')->where('quest_type_id', $t->id)->update(['category_id' => $category->id]);
                    }
                }
            }

            // Migrate rank tiers to categories (type=rank)
            if (Schema::hasTable('rank_tiers')) {
                $ranks = DB::table('rank_tiers')->get();
                foreach ($ranks as $r) {
                    $slug = Str::slug($r->name . '-rank');
                    DB::table('categories')->updateOrInsert(
                        ['slug' => $slug, 'type' => 'rank'],
                        ['name' => $r->name, 'description' => $r->description ?? null, 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
                    );
                    $category = DB::table('categories')->where('slug', $slug)->first();
                    if ($category) {
                        DB::table('quests')->where('rank_tier_id', $r->id)->update(['rank_category_id' => $category->id]);
                    }
                }
            }
        }

        // Dungeons
        if (Schema::hasTable('dungeons')) {
            Schema::table('dungeons', function (Blueprint $table) {
                if (!Schema::hasColumn('dungeons', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('description')->constrained('categories')->onDelete('set null');
                }
                if (!Schema::hasColumn('dungeons', 'rank_category_id')) {
                    $table->foreignId('rank_category_id')->nullable()->after('category_id')->constrained('categories')->onDelete('set null');
                }
                // Make old columns nullable
                $table->foreignId('dungeon_type_id')->nullable()->change();
                $table->foreignId('rank_tier_id')->nullable()->change();
            });

            // Migrate dungeon types to categories
            if (Schema::hasTable('dungeon_types')) {
                $types = DB::table('dungeon_types')->get();
                foreach ($types as $t) {
                    $slug = Str::slug($t->name . '-dungeon');
                    DB::table('categories')->updateOrInsert(
                        ['slug' => $slug, 'type' => 'dungeon'],
                        ['name' => $t->name, 'icon' => $t->icon ?? '⚔️', 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
                    );
                    $category = DB::table('categories')->where('slug', $slug)->first();
                    if ($category) {
                        DB::table('dungeons')->where('dungeon_type_id', $t->id)->update(['category_id' => $category->id]);
                    }
                }
            }

            // Map rank tiers for dungeons
            if (Schema::hasTable('rank_tiers')) {
                $ranks = DB::table('rank_tiers')->get();
                foreach ($ranks as $r) {
                    $slug = Str::slug($r->name . '-rank');
                    $category = DB::table('categories')->where('slug', $slug)->first();
                    if ($category) {
                        DB::table('dungeons')->where('rank_tier_id', $r->id)->update(['rank_category_id' => $category->id]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('dungeons')) {
            Schema::table('dungeons', function (Blueprint $table) {
                if (Schema::hasColumn('dungeons', 'rank_category_id')) {
                    $table->dropForeign(['rank_category_id']);
                    $table->dropColumn('rank_category_id');
                }
                if (Schema::hasColumn('dungeons', 'category_id')) {
                    $table->dropForeign(['category_id']);
                    $table->dropColumn('category_id');
                }
            });
        }

        if (Schema::hasTable('quests')) {
            Schema::table('quests', function (Blueprint $table) {
                if (Schema::hasColumn('quests', 'rank_category_id')) {
                    $table->dropForeign(['rank_category_id']);
                    $table->dropColumn('rank_category_id');
                }
                if (Schema::hasColumn('quests', 'category_id')) {
                    $table->dropForeign(['category_id']);
                    $table->dropColumn('category_id');
                }
            });
        }

        if (Schema::hasTable('islamic_videos')) {
            Schema::table('islamic_videos', function (Blueprint $table) {
                if (Schema::hasColumn('islamic_videos', 'category_legacy')) {
                    $table->renameColumn('category_legacy', 'category');
                }
                if (Schema::hasColumn('islamic_videos', 'category_id')) {
                    $table->dropForeign(['category_id']);
                    $table->dropColumn('category_id');
                }
            });
        }

        if (Schema::hasTable('daily_tasks')) {
            Schema::table('daily_tasks', function (Blueprint $table) {
                if (Schema::hasColumn('daily_tasks', 'category_id')) {
                    $table->dropForeign(['category_id']);
                    $table->dropColumn('category_id');
                }
            });
        }
    }
};
