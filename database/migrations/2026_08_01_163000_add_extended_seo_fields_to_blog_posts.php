<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        migration_add_column_if_missing('blog_posts', 'focus_keyphrase', function (Blueprint $table) {
            $table->string('focus_keyphrase', 255)->nullable()->after('meta_keywords');
        });

        migration_add_column_if_missing('blog_posts', 'keyphrases_json', function (Blueprint $table) {
            $table->longText('keyphrases_json')->nullable()->after('focus_keyphrase');
        });

        migration_add_column_if_missing('blog_posts', 'twitter_image', function (Blueprint $table) {
            $table->string('twitter_image', 500)->nullable()->after('twitter_card');
        });

        migration_add_column_if_missing('blog_posts', 'schema_json', function (Blueprint $table) {
            $table->longText('schema_json')->nullable()->after('twitter_image');
        });

        migration_add_column_if_missing('blog_posts', 'schema_type', function (Blueprint $table) {
            $table->string('schema_type', 80)->nullable()->after('schema_json');
        });

        migration_add_column_if_missing('blog_posts', 'breadcrumb_title', function (Blueprint $table) {
            $table->string('breadcrumb_title', 255)->nullable()->after('schema_type');
        });

        migration_add_column_if_missing('blog_posts', 'redirect_url', function (Blueprint $table) {
            $table->string('redirect_url', 500)->nullable()->after('breadcrumb_title');
        });

        migration_add_column_if_missing('blog_posts', 'seo_score', function (Blueprint $table) {
            $table->unsignedInteger('seo_score')->nullable()->after('redirect_url');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        Schema::table('blog_posts', function (Blueprint $table) {
            foreach ([
                'focus_keyphrase',
                'keyphrases_json',
                'twitter_image',
                'schema_json',
                'schema_type',
                'breadcrumb_title',
                'redirect_url',
                'seo_score',
            ] as $column) {
                if (Schema::hasColumn('blog_posts', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
