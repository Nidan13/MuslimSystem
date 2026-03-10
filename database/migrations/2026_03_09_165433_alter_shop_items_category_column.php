<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add category_id to features
        if (Schema::hasTable('quests')) {
            Schema::table('quests', function (Blueprint $table) {
                if (!Schema::hasColumn('quests', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('quest_type_id')->constrained('categories')->onDelete('set null');
                }
            });
        }

        if (Schema::hasTable('islamic_videos')) {
            Schema::table('islamic_videos', function (Blueprint $table) {
                if (!Schema::hasColumn('islamic_videos', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
                }
            });
        }

        if (Schema::hasTable('shop_items')) {
            Schema::table('shop_items', function (Blueprint $table) {
                if (!Schema::hasColumn('shop_items', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
                }
            });
        }

        if (Schema::hasTable('task_templates')) {
            Schema::table('task_templates', function (Blueprint $table) {
                if (!Schema::hasColumn('task_templates', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
                }
            });
        }

        if (Schema::hasTable('daily_tasks')) {
            Schema::table('daily_tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('daily_tasks', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('icon')->constrained('categories')->onDelete('set null');
                }
            });
        }

        // 2. Fix Dungeons Legacy Constraints (Make Nullable)
        if (Schema::hasTable('dungeons')) {
            Schema::table('dungeons', function (Blueprint $table) {
                if (Schema::hasColumn('dungeons', 'dungeon_type_id')) {
                    $table->unsignedBigInteger('dungeon_type_id')->nullable()->change();
                }
                if (Schema::hasColumn('dungeons', 'rank_tier_id')) {
                    $table->unsignedBigInteger('rank_tier_id')->nullable()->change();
                }
                // Also add category columns forungeons if not present
                if (!Schema::hasColumn('dungeons', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('description')->constrained('categories')->onDelete('set null');
                }
                if (!Schema::hasColumn('dungeons', 'rank_category_id')) {
                    $table->foreignId('rank_category_id')->nullable()->after('category_id')->constrained('categories')->onDelete('set null');
                }
            });
        }

        // 3. Migrate existing data
        $this->migrateExistingData();

        // 4. Resolve Property Conflicts (Rename old category columns)
        $this->resolvePropertyConflicts();
    }

    private function migrateExistingData()
    {
        // Migrate Quests from QuestType
        if (Schema::hasTable('quest_types') && Schema::hasTable('quests')) {
            $questTypes = DB::table('quest_types')->get();
            foreach ($questTypes as $qt) {
                // Check if category already exists to avoid duplicates
                $category = DB::table('categories')->where('slug', $qt->slug)->where('type', 'quest')->first();
                if (!$category) {
                    $catId = DB::table('categories')->insertGetId([
                        'name' => $qt->name,
                        'slug' => $qt->slug,
                        'type' => 'quest',
                        'description' => $qt->description,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                else {
                    $catId = $category->id;
                }
                DB::table('quests')->where('quest_type_id', $qt->id)->whereNull('category_id')->update(['category_id' => $catId]);
            }
        }

        // Migrate Islamic Videos
        if (Schema::hasTable('islamic_videos')) {
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
        }

        // Migrate Shop Items
        if (Schema::hasTable('shop_items')) {
            $shopCategories = ['border', 'title', 'name_color', 'consumable'];
            foreach ($shopCategories as $sc) {
                $slug = Str::slug($sc . '-shop');
                $exists = DB::table('categories')->where('slug', $slug)->first();
                if (!$exists) {
                    $catId = DB::table('categories')->insertGetId([
                        'name' => ucfirst(str_replace('_', ' ', $sc)),
                        'slug' => $slug,
                        'type' => 'shop',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                else {
                    $catId = $exists->id;
                }
                DB::table('shop_items')->where('category', $sc)->whereNull('category_id')->update(['category_id' => $catId]);
            }
        }

        // Migrate Task Templates
        if (Schema::hasTable('task_templates')) {
            $taskTemplates = DB::table('task_templates')->whereNotNull('category')->select('category')->distinct()->get();
            foreach ($taskTemplates as $tt) {
                $slug = Str::slug($tt->category . '-task');
                DB::table('categories')->updateOrInsert(
                ['slug' => $slug, 'type' => 'daily_task'],
                ['name' => $tt->category, 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
                );
                $category = DB::table('categories')->where('slug', $slug)->first();
                if ($category) {
                    DB::table('task_templates')->where('category', $tt->category)->whereNull('category_id')->update(['category_id' => $category->id]);
                }
            }
        }
    }

    private function resolvePropertyConflicts()
    {
        // Renaming columns to avoid conflict with $model->category relation
        // In SQLite, renaming is sometimes tricky, but Schema::table handles it.

        if (Schema::hasTable('shop_items') && Schema::hasColumn('shop_items', 'category')) {
            Schema::table('shop_items', function (Blueprint $table) {
                $table->renameColumn('category', 'category_legacy');
            });
        }

        if (Schema::hasTable('islamic_videos') && Schema::hasColumn('islamic_videos', 'category')) {
            Schema::table('islamic_videos', function (Blueprint $table) {
                $table->renameColumn('category', 'category_legacy');
            });
        }

        if (Schema::hasTable('task_templates') && Schema::hasColumn('task_templates', 'category')) {
            Schema::table('task_templates', function (Blueprint $table) {
                $table->renameColumn('category', 'category_legacy');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting renames
        if (Schema::hasTable('shop_items') && Schema::hasColumn('shop_items', 'category_legacy')) {
            Schema::table('shop_items', function (Blueprint $table) {
                $table->renameColumn('category_legacy', 'category');
            });
        }
    // ... and so on for others if needed, but usually down() is for rollback
    }
};
