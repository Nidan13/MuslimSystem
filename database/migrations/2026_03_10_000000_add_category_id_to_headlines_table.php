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
        if (Schema::hasTable('headlines')) {
            Schema::table('headlines', function (Blueprint $table) {
                if (!Schema::hasColumn('headlines', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('category')->constrained('categories')->onDelete('set null');
                }
            });

            // Migrate existing string categories to related categories
            $headlines = DB::table('headlines')->whereNotNull('category')->select('category')->distinct()->get();
            foreach ($headlines as $h) {
                $slug = Str::slug($h->category . '-berita');
                DB::table('categories')->updateOrInsert(
                ['slug' => $slug, 'type' => 'berita'],
                ['name' => $h->category, 'is_active' => true, 'updated_at' => now(), 'created_at' => now()]
                );
                $category = DB::table('categories')->where('slug', $slug)->first();
                if ($category) {
                    DB::table('headlines')->where('category', $h->category)->whereNull('category_id')->update(['category_id' => $category->id]);
                }
            }

            // Rename old column to avoid model property conflict
            Schema::table('headlines', function (Blueprint $table) {
                if (Schema::hasColumn('headlines', 'category')) {
                    $table->renameColumn('category', 'category_legacy');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('headlines')) {
            Schema::table('headlines', function (Blueprint $table) {
                if (Schema::hasColumn('headlines', 'category_legacy')) {
                    $table->renameColumn('category_legacy', 'category');
                }
                if (Schema::hasColumn('headlines', 'category_id')) {
                    $table->dropForeign(['category_id']);
                    $table->dropColumn('category_id');
                }
            });
        }
    }
<<<<<<< HEAD
};
=======
};
>>>>>>> main
