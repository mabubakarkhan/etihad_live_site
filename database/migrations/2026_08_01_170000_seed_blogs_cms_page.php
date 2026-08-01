<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cms_pages')) {
            return;
        }

        $seedFile = database_path('data/blogs_cms_page_seed.php');
        if (! is_file($seedFile)) {
            return;
        }

        /** @var array<string, mixed> $seed */
        $seed = require $seedFile;
        $slug = (string) ($seed['slug'] ?? 'blogs');
        $now = now();

        $payload = [
            'name' => $seed['name'] ?? 'Blog',
            'heading' => $seed['heading'] ?? 'News',
            'content' => $seed['content'] ?? null,
            'meta_title' => $seed['meta_title'] ?? null,
            'meta_description' => $seed['meta_description'] ?? null,
            'meta_keywords' => $seed['meta_keywords'] ?? null,
            'canonical_url' => $seed['canonical_url'] ?? null,
            'banner_image' => $seed['banner_image'] ?? null,
            'updated_at' => $now,
        ];

        $advanced = [
            'meta_robots',
            'og_title',
            'og_description',
            'og_image',
            'twitter_card',
            'twitter_title',
            'twitter_description',
            'twitter_image',
            'structured_data_json',
        ];
        foreach ($advanced as $column) {
            if (Schema::hasColumn('cms_pages', $column) && array_key_exists($column, $seed)) {
                $payload[$column] = $seed[$column];
            }
        }

        $existing = DB::table('cms_pages')->where('slug', $slug)->first();
        if ($existing) {
            DB::table('cms_pages')->where('slug', $slug)->update($payload);
        } else {
            $payload['slug'] = $slug;
            $payload['created_at'] = $now;
            DB::table('cms_pages')->insert($payload);
        }
    }

    public function down(): void
    {
        DB::table('cms_pages')->where('slug', 'blogs')->delete();
    }
};
