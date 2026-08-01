<?php

namespace App\Console\Commands;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDO;

class ImportWpBlogCommand extends Command
{
    protected $signature = 'blog:import-wp
        {--host=127.0.0.1}
        {--database=etihad_wp_import}
        {--username=root}
        {--password=}
        {--prefix=shergar_}';

    protected $description = 'Import WordPress blog posts/categories/tags/SEO from etihad_wp_import into Laravel blog tables';

    public function handle(): int
    {
        $host = (string) $this->option('host');
        $database = (string) $this->option('database');
        $username = (string) $this->option('username');
        $password = (string) $this->option('password');
        $prefix = (string) $this->option('prefix');

        $admin = User::query()->where('username', 'admin')->orderBy('id')->first()
            ?: User::query()->orderBy('id')->first();

        if (! $admin) {
            $this->error('No admin/user found in etihad users table.');

            return self::FAILURE;
        }

        try {
            $wp = new PDO(
                "mysql:host={$host};dbname={$database};charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (\Throwable $e) {
            $this->error('Cannot connect to WP import DB: ' . $e->getMessage());

            return self::FAILURE;
        }

        $postsTable = $prefix . 'posts';
        $postmetaTable = $prefix . 'postmeta';
        $termsTable = $prefix . 'terms';
        $termTaxTable = $prefix . 'term_taxonomy';
        $termRelTable = $prefix . 'term_relationships';
        $aioseoTable = $prefix . 'aioseo_posts';
        $optionsTable = $prefix . 'options';

        $siteUrl = rtrim((string) $wp->query("SELECT option_value FROM {$optionsTable} WHERE option_name='siteurl' LIMIT 1")->fetchColumn(), '/');

        $this->info("Importing blog data as author: {$admin->name} (#{$admin->id})");

        // Categories
        $catRows = $wp->query("
            SELECT t.term_id, t.name, t.slug, tt.description
            FROM {$termsTable} t
            INNER JOIN {$termTaxTable} tt ON tt.term_id = t.term_id
            WHERE tt.taxonomy = 'category'
        ")->fetchAll(PDO::FETCH_ASSOC);

        $categoryMap = [];
        foreach ($catRows as $row) {
            $slug = $this->uniqueSlug(BlogCategory::class, $row['slug'] ?: $row['name'], (int) $row['term_id']);
            $cat = BlogCategory::query()->updateOrCreate(
                ['wp_term_id' => (int) $row['term_id']],
                [
                    'name' => $row['name'],
                    'slug' => $slug,
                    'description' => $row['description'] ?: null,
                ]
            );
            $categoryMap[(int) $row['term_id']] = $cat->id;
        }
        $this->info('Categories: ' . count($categoryMap));

        // Tags
        $tagRows = $wp->query("
            SELECT t.term_id, t.name, t.slug
            FROM {$termsTable} t
            INNER JOIN {$termTaxTable} tt ON tt.term_id = t.term_id
            WHERE tt.taxonomy = 'post_tag'
        ")->fetchAll(PDO::FETCH_ASSOC);

        $tagMap = [];
        foreach ($tagRows as $row) {
            $slug = $this->uniqueSlug(BlogTag::class, $row['slug'] ?: $row['name'], (int) $row['term_id']);
            $tag = BlogTag::query()->updateOrCreate(
                ['wp_term_id' => (int) $row['term_id']],
                [
                    'name' => $row['name'],
                    'slug' => $slug,
                ]
            );
            $tagMap[(int) $row['term_id']] = $tag->id;
        }
        $this->info('Tags: ' . count($tagMap));

        // Posts (blog only)
        $posts = $wp->query("
            SELECT ID, post_title, post_name, post_excerpt, post_content, post_status, post_date, post_modified
            FROM {$postsTable}
            WHERE post_type = 'post'
              AND post_status IN ('publish', 'draft')
            ORDER BY post_date ASC
        ")->fetchAll(PDO::FETCH_ASSOC);

        $imported = 0;
        foreach ($posts as $p) {
            $wpId = (int) $p['ID'];
            $slug = $this->uniquePostSlug($p['post_name'] ?: $p['post_title'], $wpId);

            $seo = $wp->prepare("SELECT * FROM {$aioseoTable} WHERE post_id = ? LIMIT 1");
            $seo->execute([$wpId]);
            $seoRow = $seo->fetch(PDO::FETCH_ASSOC) ?: [];

            $thumbId = $this->metaValue($wp, $postmetaTable, $wpId, '_thumbnail_id');
            $featuredSource = null;
            if ($thumbId) {
                $attached = $this->metaValue($wp, $postmetaTable, (int) $thumbId, '_wp_attached_file');
                if ($attached) {
                    $featuredSource = $siteUrl . '/wp-content/uploads/' . ltrim($attached, '/');
                } else {
                    $guid = $wp->prepare("SELECT guid FROM {$postsTable} WHERE ID = ? LIMIT 1");
                    $guid->execute([(int) $thumbId]);
                    $featuredSource = $guid->fetchColumn() ?: null;
                }
            }

            $robots = $this->buildRobots($seoRow);
            $status = $p['post_status'] === 'publish' ? BlogPost::STATUS_PUBLISHED : BlogPost::STATUS_DRAFT;

            $post = BlogPost::query()->updateOrCreate(
                ['wp_post_id' => $wpId],
                [
                    'author_id' => $admin->id,
                    'title' => $p['post_title'] ?: ('Untitled #' . $wpId),
                    'slug' => $slug,
                    'excerpt' => $p['post_excerpt'] ?: Str::limit(strip_tags((string) $p['post_content']), 220),
                    'content' => $p['post_content'],
                    'featured_image' => null,
                    'featured_image_source' => $featuredSource,
                    'status' => $status,
                    'published_at' => $p['post_date'] ?: null,
                    'meta_title' => $this->cleanSeoText($seoRow['title'] ?? null) ?: null,
                    'meta_description' => $this->cleanSeoText($seoRow['description'] ?? null) ?: null,
                    'meta_keywords' => $this->cleanSeoText($seoRow['keywords'] ?? null) ?: null,
                    'canonical_url' => $seoRow['canonical_url'] ?? null,
                    'meta_robots' => $robots,
                    'og_title' => $this->cleanSeoText($seoRow['og_title'] ?? null) ?: null,
                    'og_description' => $this->cleanSeoText($seoRow['og_description'] ?? null) ?: null,
                    'og_image' => $seoRow['og_image_url'] ?? $seoRow['og_image_custom_url'] ?? null,
                    'twitter_title' => $this->cleanSeoText($seoRow['twitter_title'] ?? null) ?: null,
                    'twitter_description' => $this->cleanSeoText($seoRow['twitter_description'] ?? null) ?: null,
                    'twitter_card' => $seoRow['twitter_card'] ?? 'summary_large_image',
                ]
            );

            // Category / tag links
            $termSql = $wp->prepare("
                SELECT tt.term_id, tt.taxonomy
                FROM {$termRelTable} tr
                INNER JOIN {$termTaxTable} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
                WHERE tr.object_id = ?
                  AND tt.taxonomy IN ('category', 'post_tag')
            ");
            $termSql->execute([$wpId]);
            $termRows = $termSql->fetchAll(PDO::FETCH_ASSOC);

            $catIds = [];
            $tagIds = [];
            foreach ($termRows as $tr) {
                $termId = (int) $tr['term_id'];
                if ($tr['taxonomy'] === 'category' && isset($categoryMap[$termId])) {
                    $catIds[] = $categoryMap[$termId];
                }
                if ($tr['taxonomy'] === 'post_tag' && isset($tagMap[$termId])) {
                    $tagIds[] = $tagMap[$termId];
                }
            }
            $post->categories()->sync(array_values(array_unique($catIds)));
            $post->tags()->sync(array_values(array_unique($tagIds)));

            $imported++;
        }

        $this->info("Blog posts imported/updated: {$imported}");
        $this->info('Published: ' . BlogPost::query()->published()->count());
        $this->info('Drafts: ' . BlogPost::query()->where('status', BlogPost::STATUS_DRAFT)->count());

        return self::SUCCESS;
    }

    private function metaValue(PDO $wp, string $table, int $postId, string $key): ?string
    {
        $stmt = $wp->prepare("SELECT meta_value FROM {$table} WHERE post_id = ? AND meta_key = ? LIMIT 1");
        $stmt->execute([$postId, $key]);
        $val = $stmt->fetchColumn();

        return $val === false ? null : (string) $val;
    }

    private function uniqueSlug(string $modelClass, string $raw, int $wpTermId): string
    {
        $base = Str::slug($raw) ?: ('term-' . $wpTermId);
        $slug = $base;
        $i = 2;
        while ($modelClass::query()->where('slug', $slug)->where(function ($q) use ($wpTermId) {
            $q->whereNull('wp_term_id')->orWhere('wp_term_id', '!=', $wpTermId);
        })->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function uniquePostSlug(string $raw, int $wpPostId): string
    {
        $base = Str::slug($raw) ?: ('post-' . $wpPostId);
        $slug = $base;
        $i = 2;
        while (BlogPost::query()->where('slug', $slug)->where(function ($q) use ($wpPostId) {
            $q->whereNull('wp_post_id')->orWhere('wp_post_id', '!=', $wpPostId);
        })->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function cleanSeoText(?string $value): string
    {
        $value = (string) $value;
        // AIOSEO sometimes stores serialized/#hash# placeholders — strip tags only, keep readable text
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }

    /**
     * @param  array<string, mixed>  $seoRow
     */
    private function buildRobots(array $seoRow): string
    {
        if (empty($seoRow)) {
            return 'index, follow';
        }

        $parts = [];
        $parts[] = ! empty($seoRow['robots_noindex']) ? 'noindex' : 'index';
        $parts[] = ! empty($seoRow['robots_nofollow']) ? 'nofollow' : 'follow';
        if (! empty($seoRow['robots_noarchive'])) {
            $parts[] = 'noarchive';
        }
        if (! empty($seoRow['robots_nosnippet'])) {
            $parts[] = 'nosnippet';
        }

        return implode(', ', $parts);
    }
}
