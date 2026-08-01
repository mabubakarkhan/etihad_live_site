<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('blog_categories')) {
            Schema::table('blog_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('blog_categories', 'meta_title')) {
                    $table->string('meta_title')->nullable()->after('description');
                }
                if (! Schema::hasColumn('blog_categories', 'meta_description')) {
                    $table->string('meta_description', 500)->nullable()->after('meta_title');
                }
                if (! Schema::hasColumn('blog_categories', 'meta_keywords')) {
                    $table->string('meta_keywords', 500)->nullable()->after('meta_description');
                }
                if (! Schema::hasColumn('blog_categories', 'canonical_url')) {
                    $table->string('canonical_url', 500)->nullable()->after('meta_keywords');
                }
                if (! Schema::hasColumn('blog_categories', 'meta_robots')) {
                    $table->string('meta_robots', 120)->nullable()->after('canonical_url');
                }
                if (! Schema::hasColumn('blog_categories', 'og_title')) {
                    $table->string('og_title')->nullable()->after('meta_robots');
                }
                if (! Schema::hasColumn('blog_categories', 'og_description')) {
                    $table->string('og_description', 500)->nullable()->after('og_title');
                }
                if (! Schema::hasColumn('blog_categories', 'og_image')) {
                    $table->string('og_image', 500)->nullable()->after('og_description');
                }
            });
        }

        if (Schema::hasTable('blog_tags')) {
            Schema::table('blog_tags', function (Blueprint $table) {
                if (! Schema::hasColumn('blog_tags', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
                if (! Schema::hasColumn('blog_tags', 'meta_title')) {
                    $table->string('meta_title')->nullable()->after('description');
                }
                if (! Schema::hasColumn('blog_tags', 'meta_description')) {
                    $table->string('meta_description', 500)->nullable()->after('meta_title');
                }
                if (! Schema::hasColumn('blog_tags', 'meta_keywords')) {
                    $table->string('meta_keywords', 500)->nullable()->after('meta_description');
                }
                if (! Schema::hasColumn('blog_tags', 'canonical_url')) {
                    $table->string('canonical_url', 500)->nullable()->after('meta_keywords');
                }
                if (! Schema::hasColumn('blog_tags', 'meta_robots')) {
                    $table->string('meta_robots', 120)->nullable()->after('canonical_url');
                }
                if (! Schema::hasColumn('blog_tags', 'og_title')) {
                    $table->string('og_title')->nullable()->after('meta_robots');
                }
                if (! Schema::hasColumn('blog_tags', 'og_description')) {
                    $table->string('og_description', 500)->nullable()->after('og_title');
                }
                if (! Schema::hasColumn('blog_tags', 'og_image')) {
                    $table->string('og_image', 500)->nullable()->after('og_description');
                }
            });
        }
    }

    public function down(): void
    {
        $seoCols = [
            'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
            'meta_robots', 'og_title', 'og_description', 'og_image',
        ];

        if (Schema::hasTable('blog_categories')) {
            Schema::table('blog_categories', function (Blueprint $table) use ($seoCols) {
                foreach ($seoCols as $col) {
                    if (Schema::hasColumn('blog_categories', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('blog_tags')) {
            Schema::table('blog_tags', function (Blueprint $table) use ($seoCols) {
                foreach (array_merge(['description'], $seoCols) as $col) {
                    if (Schema::hasColumn('blog_tags', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
