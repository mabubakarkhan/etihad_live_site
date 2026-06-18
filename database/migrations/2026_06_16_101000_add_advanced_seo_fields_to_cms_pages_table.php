<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            if (! Schema::hasColumn('cms_pages', 'meta_robots')) {
                $table->string('meta_robots', 120)->nullable()->after('canonical_url');
            }
            if (! Schema::hasColumn('cms_pages', 'og_title')) {
                $table->string('og_title')->nullable()->after('meta_robots');
            }
            if (! Schema::hasColumn('cms_pages', 'og_description')) {
                $table->string('og_description', 500)->nullable()->after('og_title');
            }
            if (! Schema::hasColumn('cms_pages', 'og_image')) {
                $table->string('og_image')->nullable()->after('og_description');
            }
            if (! Schema::hasColumn('cms_pages', 'twitter_card')) {
                $table->string('twitter_card', 40)->nullable()->after('og_image');
            }
            if (! Schema::hasColumn('cms_pages', 'twitter_title')) {
                $table->string('twitter_title')->nullable()->after('twitter_card');
            }
            if (! Schema::hasColumn('cms_pages', 'twitter_description')) {
                $table->string('twitter_description', 500)->nullable()->after('twitter_title');
            }
            if (! Schema::hasColumn('cms_pages', 'twitter_image')) {
                $table->string('twitter_image')->nullable()->after('twitter_description');
            }
            if (! Schema::hasColumn('cms_pages', 'structured_data_json')) {
                $table->longText('structured_data_json')->nullable()->after('twitter_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            foreach ([
                'meta_robots',
                'og_title',
                'og_description',
                'og_image',
                'twitter_card',
                'twitter_title',
                'twitter_description',
                'twitter_image',
                'structured_data_json',
            ] as $column) {
                if (Schema::hasColumn('cms_pages', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
