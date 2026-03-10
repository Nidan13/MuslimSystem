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
                $table->foreignId('category_id')->nullable()->after('quest_type_id')->constrained('categories')->onDelete('set null');
            });
        }

        if (Schema::hasTable('islamic_videos')) {
            Schema::table('islamic_videos', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
            });
        }

        if (Schema::hasTable('shop_items')) {
            Schema::table('shop_items', function (Blueprint $table) {
                if (!Schema::hasColumn('shop_items', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('id')->constrained('categories')->onDelete('set null');
                }
            });
        }

        if (Schema::hasTable('task_templates')) {
            Schema::table('task_templates', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
            });
        }

        if (Schema::hasTable('daily_tasks')) {
            Schema::table('daily_tasks', function (Blueprint $table) {
                $table->foreignId('category_id')->nullable()->after('icon')->constrained('categories')->onDelete('set null');
            });
        }

        // 2. Migrate existing data
        $this->migrateExistingData();
    }

    private function migrateExistingData()
    {
        // Migrate Quests from QuestType
        if (Schema::hasTable('quest_types')) {
            $questTypes = DB::table('quest_types')->get();
            foreach ($questTypes as $qt) {
                $catId = DB::table('categories')->insertGetId([
                    'name' => $qt->name,
                    'slug' => $qt->slug,
                    'type' => 'quest',
                    'description' => $qt->description,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('quests')->where('quest_type_id', $qt->id)->update(['category_id' => $catId]);
            }
        }

        // Migrate Islamic Videos
        if (Schema::hasTable('islamic_videos')) {
            $videos = DB::table('islamic_videos')->select('category')->distinct()->get();
            foreach ($videos as $v) {
                if (!$v->category)
                    continue;
                DB::table('categories')->updateOrInsert(
                ['name' => $v->category, 'type' => 'kajian'],
                ['slug' => Str::slug($v->category . '-kajian'), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
                $category = DB::table('categories')->where('name', $v->category)->where('type', 'kajian')->first();
                if ($category) {
                    DB::table('islamic_videos')->where('category', $v->category)->update(['category_id' => $category->id]);
                }
            }
        }

        // Migrate Shop Items
        if (Schema::hasTable('shop_items')) {
            $shopCategories = ['border', 'title', 'name_color', 'consumable'];
            foreach ($shopCategories as $sc) {
                $catId = DB::table('categories')->insertGetId([
                    'name' => ucfirst(str_replace('_', ' ', $sc)),
                    'slug' => Str::slug($sc . '-shop'),
                    'type' => 'shop',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('shop_items')->where('category', $sc)->update(['category_id' => $catId]);
            }
        }

        // Migrate Task Templates
        if (Schema::hasTable('task_templates')) {
            $taskTemplates = DB::table('task_templates')->select('category')->distinct()->get();
            foreach ($taskTemplates as $tt) {
                if (!$tt->category)
                    continue;
                DB::table('categories')->updateOrInsert(
                ['name' => $tt->category, 'type' => 'daily_task'],
                ['slug' => Str::slug($tt->category . '-task'), 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
                );
                $category = DB::table('categories')->where('name', $tt->category)->where('type', 'daily_task')->first();
                if ($category) {
                    DB::table('task_templates')->where('category', $tt->category)->update(['category_id' => $category->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('quests')) {
            Schema::table('quests', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }

        if (Schema::hasTable('islamic_videos')) {
            Schema::table('islamic_videos', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }

        if (Schema::hasTable('shop_items')) {
            Schema::table('shop_items', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }

        if (Schema::hasTable('task_templates')) {
            Schema::table('task_templates', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            });
        }
    }
};
