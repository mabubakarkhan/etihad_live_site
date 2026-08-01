<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Imports blog categories, tags, posts, and pivots from database/data/blog_seed.json
 * (exported from local). Safe to re-run: skips when blog_posts already has rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['blog_categories', 'blog_tags', 'blog_posts', 'blog_post_category', 'blog_post_tag'] as $table) {
            if (! Schema::hasTable($table)) {
                return;
            }
        }

        if (DB::table('blog_posts')->count() > 0) {
            return;
        }

        $path = database_path('data/blog_seed.json');
        if (! is_file($path)) {
            return;
        }

        $raw = file_get_contents($path);
        if ($raw === false || $raw === '') {
            return;
        }

        /** @var array<string, mixed>|null $data */
        $data = json_decode($raw, true);
        if (! is_array($data)) {
            return;
        }

        $authorId = DB::table('users')->orderBy('id')->value('id');

        $this->withoutForeignKeyChecks(function () use ($data, $authorId) {
            $this->insertRows('blog_categories', $data['categories'] ?? [], [
                'id', 'wp_term_id', 'name', 'slug', 'description', 'created_at', 'updated_at',
            ]);

            $this->insertRows('blog_tags', $data['tags'] ?? [], [
                'id', 'wp_term_id', 'name', 'slug', 'created_at', 'updated_at',
            ]);

            $postColumns = [
                'id', 'wp_post_id', 'title', 'slug', 'excerpt', 'content',
                'featured_image', 'featured_image_source', 'status', 'published_at',
                'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
                'meta_robots', 'og_title', 'og_description', 'og_image',
                'twitter_title', 'twitter_description', 'twitter_card',
                'created_at', 'updated_at',
            ];
            $extraSeo = [
                'focus_keyphrase', 'keyphrases_json', 'twitter_image', 'schema_json',
                'schema_type', 'breadcrumb_title', 'redirect_url', 'seo_score',
            ];
            foreach ($extraSeo as $col) {
                if (Schema::hasColumn('blog_posts', $col)) {
                    $postColumns[] = $col;
                }
            }

            $posts = [];
            foreach (($data['posts'] ?? []) as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $row['author_id'] = $authorId;
                $posts[] = $row;
            }
            $postColumns[] = 'author_id';
            $this->insertRows('blog_posts', $posts, $postColumns);

            $this->insertRows('blog_post_category', $data['post_category'] ?? [], [
                'id', 'blog_post_id', 'blog_category_id',
            ]);

            $this->insertRows('blog_post_tag', $data['post_tag'] ?? [], [
                'id', 'blog_post_id', 'blog_tag_id',
            ]);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        $this->withoutForeignKeyChecks(function () {
            if (Schema::hasTable('blog_post_tag')) {
                DB::table('blog_post_tag')->delete();
            }
            if (Schema::hasTable('blog_post_category')) {
                DB::table('blog_post_category')->delete();
            }
            DB::table('blog_posts')->delete();
            if (Schema::hasTable('blog_tags')) {
                DB::table('blog_tags')->delete();
            }
            if (Schema::hasTable('blog_categories')) {
                DB::table('blog_categories')->delete();
            }
        });
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<string>  $columns
     */
    private function insertRows(string $table, array $rows, array $columns): void
    {
        $chunk = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $item = [];
            foreach ($columns as $col) {
                $item[$col] = $row[$col] ?? null;
            }
            $chunk[] = $item;
            if (count($chunk) >= 50) {
                DB::table($table)->insert($chunk);
                $chunk = [];
            }
        }
        if ($chunk !== []) {
            DB::table($table)->insert($chunk);
        }
    }

    private function withoutForeignKeyChecks(callable $callback): void
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $callback();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
};
