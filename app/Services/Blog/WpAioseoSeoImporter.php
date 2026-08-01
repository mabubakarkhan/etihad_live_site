<?php

namespace App\Services\Blog;

use App\Models\BlogPost;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDO;
use Throwable;

class WpAioseoSeoImporter
{
    private string $prefix;

    private PDO $wp;

    /** @var array<string, mixed> */
    private array $stats = [
        'total_wp_seo_records' => 0,
        'total_blog_posts' => 0,
        'successfully_linked_posts' => 0,
        'updated_seo_records' => 0,
        'skipped_no_changes' => 0,
        'missing_blog_mappings' => 0,
        'missing_seo_records' => 0,
        'orphaned_seo_skipped' => 0,
        'template_fallback_applied' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    /** @var list<string> */
    private array $logLines = [];

    public function __construct(
        string $host = '127.0.0.1',
        string $database = 'etihad_wp_import',
        string $username = 'root',
        string $password = '',
        string $prefix = 'shergar_'
    ) {
        $this->prefix = $prefix;
        $this->wp = new PDO(
            "mysql:host={$host};dbname={$database};charset=utf8mb4",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    /**
     * @param  callable(int $processed, int $total): void|null  $onProgress
     * @return array{stats: array<string, mixed>, log_path: string}
     */
    public function run(int $chunkSize = 200, ?callable $onProgress = null): array
    {
        $this->log('Starting AIOSEO → Laravel blog SEO import');
        $this->inspectAndLogSchema();

        $aioseoTable = $this->prefix . 'aioseo_posts';
        $postsTable = $this->prefix . 'posts';

        $this->stats['total_wp_seo_records'] = (int) $this->wp->query("SELECT COUNT(*) FROM `{$aioseoTable}`")->fetchColumn();
        $this->stats['total_blog_posts'] = (int) BlogPost::query()->count();

        // Map Laravel blogs by wp_post_id and slug
        $blogsByWpId = BlogPost::query()->whereNotNull('wp_post_id')->get()->keyBy('wp_post_id');
        $blogsBySlug = BlogPost::query()->get()->keyBy(fn (BlogPost $p) => mb_strtolower($p->slug));

        $templates = $this->loadPostTemplates();
        $this->log('Post title template: ' . ($templates['title'] ?? '(none)'));
        $this->log('Post description template: ' . ($templates['description'] ?? '(none)'));
        $this->log('Site title: ' . ($templates['site_title'] ?? ''));
        $this->log('Separator: ' . ($templates['separator'] ?? ''));

        // 1) Process AIOSEO rows (skip non-blog orphans)
        $totalSeo = $this->stats['total_wp_seo_records'];
        $offset = 0;
        $processed = 0;

        while ($offset < $totalSeo) {
            $stmt = $this->wp->prepare("
                SELECT a.*, p.post_type, p.post_status, p.post_name, p.post_title, p.post_excerpt, p.post_content
                FROM `{$aioseoTable}` a
                LEFT JOIN `{$postsTable}` p ON p.ID = a.post_id
                ORDER BY a.id ASC
                LIMIT :limit OFFSET :offset
            ");
            $stmt->bindValue(':limit', $chunkSize, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows === []) {
                break;
            }

            DB::beginTransaction();
            try {
                foreach ($rows as $row) {
                    $processed++;
                    if (($row['post_type'] ?? null) !== 'post') {
                        $this->stats['orphaned_seo_skipped']++;
                        $this->stats['warnings'][] = "Skipped orphaned SEO post_id={$row['post_id']} type=" . ($row['post_type'] ?? 'null');
                        $this->log("SKIP orphan SEO post_id={$row['post_id']} type=" . ($row['post_type'] ?? 'null'));
                        continue;
                    }

                    $blog = $blogsByWpId->get((int) $row['post_id'])
                        ?: $blogsBySlug->get(mb_strtolower((string) ($row['post_name'] ?? '')));

                    if (! $blog) {
                        $this->stats['missing_blog_mappings']++;
                        $this->stats['warnings'][] = "No Laravel blog for WP post_id={$row['post_id']} slug=" . ($row['post_name'] ?? '');
                        $this->log("MISSING blog mapping for WP post_id={$row['post_id']}");
                        continue;
                    }

                    $payload = $this->mapAioseoRowToPayload($row, $templates);
                    $changed = $this->applySeoPayload($blog, $payload);
                    $this->stats['successfully_linked_posts']++;
                    if ($changed) {
                        $this->stats['updated_seo_records']++;
                        $this->log("UPDATED SEO blog_id={$blog->id} wp_post_id={$blog->wp_post_id}");
                    } else {
                        $this->stats['skipped_no_changes']++;
                    }
                }
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                $this->stats['errors'][] = $e->getMessage();
                $this->log('ERROR chunk offset=' . $offset . ': ' . $e->getMessage());
                throw $e;
            }

            if ($onProgress) {
                $onProgress(min($processed, $totalSeo), max($totalSeo, 1));
            }

            $offset += $chunkSize;
        }

        // 2) Blog posts with no AIOSEO row → apply dynamic templates (AIOSEO default behavior)
        $linkedWpIds = [];
        $seoPostIds = $this->wp->query("
            SELECT a.post_id
            FROM `{$aioseoTable}` a
            INNER JOIN `{$postsTable}` p ON p.ID = a.post_id AND p.post_type = 'post'
        ")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($seoPostIds as $id) {
            $linkedWpIds[(int) $id] = true;
        }

        $missing = BlogPost::query()
            ->whereNotNull('wp_post_id')
            ->orderBy('id')
            ->get();

        $this->stats['missing_seo_records'] = $missing->filter(fn (BlogPost $b) => ! isset($linkedWpIds[(int) $b->wp_post_id]))->count();

        $missing->chunk($chunkSize)->each(function ($chunk) use ($linkedWpIds, $templates, &$processed, $onProgress, $totalSeo) {
            DB::beginTransaction();
            try {
                foreach ($chunk as $blog) {
                    if (isset($linkedWpIds[(int) $blog->wp_post_id])) {
                        continue;
                    }

                    $wpPost = $this->fetchWpPost((int) $blog->wp_post_id);
                    $payload = $this->mapTemplateFallbackToPayload($blog, $wpPost, $templates);
                    $changed = $this->applySeoPayload($blog, $payload);
                    if ($changed) {
                        $this->stats['updated_seo_records']++;
                        $this->stats['template_fallback_applied']++;
                        $this->log("TEMPLATE SEO blog_id={$blog->id} wp_post_id={$blog->wp_post_id}");
                    } else {
                        $this->stats['skipped_no_changes']++;
                    }
                }
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                $this->stats['errors'][] = $e->getMessage();
                $this->log('ERROR template chunk: ' . $e->getMessage());
                throw $e;
            }

            if ($onProgress) {
                // keep progress bar saturated after SEO rows
                $onProgress(max($totalSeo, 1), max($totalSeo, 1));
            }
        });

        $logPath = $this->writeLogFile();
        $this->log('Finished. Log: ' . $logPath);

        return [
            'stats' => $this->stats,
            'log_path' => $logPath,
        ];
    }

    private function inspectAndLogSchema(): void
    {
        $aioseo = $this->prefix . 'aioseo_posts';
        $cols = $this->wp->query("SHOW COLUMNS FROM `{$aioseo}`")->fetchAll(PDO::FETCH_COLUMN);
        $this->log('AIOSEO columns: ' . implode(', ', $cols));

        $fieldMap = [
            'SEO Title' => 'title',
            'Meta Description' => 'description',
            'Focus Keyphrase' => 'keyphrases (JSON)',
            'Canonical URL' => 'canonical_url',
            'Robots Meta' => 'robots_* flags',
            'Open Graph Title' => 'og_title',
            'Open Graph Description' => 'og_description',
            'Open Graph Image' => 'og_image_url / og_image_custom_url',
            'Twitter Title' => 'twitter_title',
            'Twitter Description' => 'twitter_description',
            'Twitter Image' => 'twitter_image_url / twitter_image_custom_url',
            'Schema JSON' => 'schema / schema_type',
            'Breadcrumb settings' => 'global aioseo_options.breadcrumbs (no per-post column found)',
            'Redirect URL' => 'not present (no aioseo redirects table / column)',
        ];
        foreach ($fieldMap as $label => $col) {
            $this->log("Field map: {$label} → {$col}");
        }
    }

    /**
     * @return array{title: string, description: string, site_title: string, tagline: string, separator: string, schema_type: string}
     */
    private function loadPostTemplates(): array
    {
        $options = $this->optionJson('aioseo_options');
        $dynamic = $this->optionJson('aioseo_options_dynamic');

        $siteTitle = (string) $this->optionValue('blogname');
        $tagline = html_entity_decode((string) $this->optionValue('blogdescription'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $separator = html_entity_decode(
            (string) data_get($options, 'searchAppearance.global.separator', '-'),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        return [
            'title' => (string) data_get($dynamic, 'searchAppearance.postTypes.post.title', '#post_title #separator_sa #site_title'),
            'description' => (string) data_get($dynamic, 'searchAppearance.postTypes.post.metaDescription', '#post_excerpt'),
            'site_title' => $siteTitle,
            'tagline' => $tagline,
            'separator' => $separator !== '' ? $separator : '-',
            'schema_type' => (string) data_get($dynamic, 'searchAppearance.postTypes.post.schemaType', 'Article'),
        ];
    }

    private function optionValue(string $name): ?string
    {
        $table = $this->prefix . 'options';
        $stmt = $this->wp->prepare("SELECT option_value FROM `{$table}` WHERE option_name = ? LIMIT 1");
        $stmt->execute([$name]);
        $v = $stmt->fetchColumn();

        return $v === false ? null : (string) $v;
    }

    /**
     * @return array<string, mixed>
     */
    private function optionJson(string $name): array
    {
        $raw = $this->optionValue($name);
        if (! $raw) {
            return [];
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function fetchWpPost(int $wpPostId): ?array
    {
        $table = $this->prefix . 'posts';
        $stmt = $this->wp->prepare("SELECT ID, post_title, post_name, post_excerpt, post_content FROM `{$table}` WHERE ID = ? LIMIT 1");
        $stmt->execute([$wpPostId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, string>  $templates
     * @return array<string, mixed>
     */
    private function mapAioseoRowToPayload(array $row, array $templates): array
    {
        $keyphrasesRaw = $row['keyphrases'] ?? null;
        $focus = $this->extractFocusKeyphrase($keyphrasesRaw);

        $ogImage = $this->firstNonEmpty([
            $row['og_image_custom_url'] ?? null,
            $row['og_image_url'] ?? null,
        ]);
        $twitterImage = $this->firstNonEmpty([
            $row['twitter_image_custom_url'] ?? null,
            $row['twitter_image_url'] ?? null,
            $ogImage,
        ]);

        $title = $this->cleanText($row['title'] ?? null);
        $description = $this->cleanText($row['description'] ?? null);

        // Empty custom fields → resolve AIOSEO smart tags against this post
        if ($title === '') {
            $title = $this->renderTemplate($templates['title'], $row, $templates);
        }
        if ($description === '') {
            $description = $this->renderTemplate($templates['description'], $row, $templates);
        }

        $twitterTitle = $this->cleanText($row['twitter_title'] ?? null);
        $twitterDescription = $this->cleanText($row['twitter_description'] ?? null);
        if (! empty($row['twitter_use_og'])) {
            $twitterTitle = $twitterTitle !== '' ? $twitterTitle : $this->cleanText($row['og_title'] ?? null);
            $twitterDescription = $twitterDescription !== '' ? $twitterDescription : $this->cleanText($row['og_description'] ?? null);
        }

        return [
            'meta_title' => Str::limit($title, 255, ''),
            'meta_description' => Str::limit($description, 500, ''),
            'meta_keywords' => $this->cleanText($row['keywords'] ?? null),
            'focus_keyphrase' => $focus,
            'keyphrases_json' => is_string($keyphrasesRaw) && $keyphrasesRaw !== '' ? $keyphrasesRaw : null,
            'canonical_url' => $this->cleanText($row['canonical_url'] ?? null),
            'meta_robots' => $this->buildRobots($row),
            'og_title' => $this->cleanText($row['og_title'] ?? null),
            'og_description' => Str::limit($this->cleanText($row['og_description'] ?? null), 500, ''),
            'og_image' => $ogImage,
            'twitter_title' => $twitterTitle,
            'twitter_description' => Str::limit($twitterDescription, 500, ''),
            'twitter_card' => $this->normalizeTwitterCard($row['twitter_card'] ?? null),
            'twitter_image' => $twitterImage,
            'schema_json' => is_string($row['schema'] ?? null) && $row['schema'] !== '' ? $row['schema'] : null,
            'schema_type' => $this->cleanText($row['schema_type'] ?? null) ?: ($templates['schema_type'] ?? null),
            'breadcrumb_title' => null, // no per-post breadcrumb column in this DB
            'redirect_url' => null, // redirects addon table not present
            'seo_score' => isset($row['seo_score']) ? (int) $row['seo_score'] : null,
        ];
    }

    /**
     * @param  array<string, mixed>|null  $wpPost
     * @param  array<string, string>  $templates
     * @return array<string, mixed>
     */
    private function mapTemplateFallbackToPayload(BlogPost $blog, ?array $wpPost, array $templates): array
    {
        $context = [
            'post_title' => $wpPost['post_title'] ?? $blog->title,
            'post_excerpt' => $wpPost['post_excerpt'] ?? $blog->excerpt,
            'post_content' => $wpPost['post_content'] ?? $blog->content,
            'post_name' => $wpPost['post_name'] ?? $blog->slug,
        ];

        $title = $this->renderTemplate($templates['title'], $context, $templates);
        $description = $this->renderTemplate($templates['description'], $context, $templates);

        return [
            'meta_title' => Str::limit($title, 255, ''),
            'meta_description' => Str::limit($description, 500, ''),
            'meta_keywords' => null,
            'focus_keyphrase' => null,
            'keyphrases_json' => null,
            'canonical_url' => null,
            'meta_robots' => 'index, follow',
            'og_title' => null,
            'og_description' => null,
            'og_image' => Str::limit((string) ($blog->featured_image_source ?: ''), 500, '') ?: null,
            'twitter_title' => null,
            'twitter_description' => null,
            'twitter_card' => 'summary_large_image',
            'twitter_image' => Str::limit((string) ($blog->featured_image_source ?: ''), 500, '') ?: null,
            'schema_json' => null,
            'schema_type' => $templates['schema_type'] ?? 'Article',
            'breadcrumb_title' => null,
            'redirect_url' => null,
            'seo_score' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function applySeoPayload(BlogPost $blog, array $payload): bool
    {
        $seoKeys = [
            'meta_title', 'meta_description', 'meta_keywords', 'focus_keyphrase', 'keyphrases_json',
            'canonical_url', 'meta_robots', 'og_title', 'og_description', 'og_image',
            'twitter_title', 'twitter_description', 'twitter_card', 'twitter_image',
            'schema_json', 'schema_type', 'breadcrumb_title', 'redirect_url', 'seo_score',
        ];

        $dirty = false;
        foreach ($seoKeys as $key) {
            if (! array_key_exists($key, $payload)) {
                continue;
            }
            $incoming = $payload[$key];
            if ($incoming === null || $incoming === '') {
                continue; // only update when imported value is not empty
            }

            $current = $blog->{$key} ?? null;
            // Rule: update only if current empty OR values differ and incoming not empty
            // User: "update it only if the imported value is not empty" — still allow overwrite when incoming present
            if ((string) $current === (string) $incoming) {
                continue;
            }

            $blog->{$key} = $incoming;
            $dirty = true;
        }

        if ($dirty) {
            $blog->save();
        }

        return $dirty;
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  array<string, string>  $templates
     */
    private function renderTemplate(string $template, array $context, array $templates): string
    {
        $postTitle = (string) ($context['post_title'] ?? '');
        $excerpt = (string) ($context['post_excerpt'] ?? '');
        $content = (string) ($context['post_content'] ?? '');
        if ($excerpt === '') {
            $excerpt = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? ''), 160, '');
        } else {
            $excerpt = Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($excerpt)) ?? ''), 160, '');
        }

        $replacements = [
            '#post_title' => $postTitle,
            '#post_excerpt' => $excerpt,
            '#post_content' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($content)) ?? ''), 160, ''),
            '#site_title' => $templates['site_title'],
            '#tagline' => $templates['tagline'],
            '#separator_sa' => $templates['separator'],
            '#separator' => $templates['separator'],
        ];

        $out = strtr($template, $replacements);
        $out = html_entity_decode($out, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $out = preg_replace('/\s+/', ' ', $out) ?? $out;

        return trim($out);
    }

    private function extractFocusKeyphrase(mixed $raw): ?string
    {
        if (! is_string($raw) || $raw === '') {
            return null;
        }
        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            return null;
        }
        // Common AIOSEO shapes
        $candidates = [
            data_get($decoded, 'focus.keyphrase'),
            data_get($decoded, 'focus.score.keyphrase'),
            data_get($decoded, 'keyphrases.focus.keyphrase'),
            data_get($decoded, '0.keyphrase'),
        ];
        foreach ($candidates as $c) {
            $c = $this->cleanText(is_string($c) ? $c : null);
            if ($c !== '') {
                return $c;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function buildRobots(array $row): string
    {
        $parts = [];
        $parts[] = ! empty($row['robots_noindex']) ? 'noindex' : 'index';
        $parts[] = ! empty($row['robots_nofollow']) ? 'nofollow' : 'follow';
        if (! empty($row['robots_noarchive'])) {
            $parts[] = 'noarchive';
        }
        if (! empty($row['robots_nosnippet'])) {
            $parts[] = 'nosnippet';
        }
        if (! empty($row['robots_noimageindex'])) {
            $parts[] = 'noimageindex';
        }

        return implode(', ', $parts);
    }

    private function normalizeTwitterCard(?string $value): ?string
    {
        $value = $this->cleanText($value);
        if ($value === '' || $value === 'default') {
            return 'summary_large_image';
        }

        return $value;
    }

    private function cleanText(?string $value): string
    {
        if ($value === null) {
            return '';
        }
        $value = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return trim($value);
    }

    /**
     * @param  list<mixed>  $values
     */
    private function firstNonEmpty(array $values): ?string
    {
        foreach ($values as $v) {
            $v = $this->cleanText(is_string($v) ? $v : null);
            if ($v !== '') {
                return $v;
            }
        }

        return null;
    }

    private function log(string $message): void
    {
        $line = '[' . now()->toDateTimeString() . '] ' . $message;
        $this->logLines[] = $line;
        Log::channel('single')->info('[blog:import-seo] ' . $message);
    }

    private function writeLogFile(): string
    {
        $dir = storage_path('logs');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $path = $dir . DIRECTORY_SEPARATOR . 'blog-seo-import-' . now()->format('Ymd-His') . '.log';

        $summary = [
            'total_wp_seo_records' => $this->stats['total_wp_seo_records'],
            'total_blog_posts' => $this->stats['total_blog_posts'],
            'successfully_linked_posts' => $this->stats['successfully_linked_posts'],
            'updated_seo_records' => $this->stats['updated_seo_records'],
            'skipped_no_changes' => $this->stats['skipped_no_changes'],
            'missing_blog_mappings' => $this->stats['missing_blog_mappings'],
            'missing_seo_records' => $this->stats['missing_seo_records'],
            'orphaned_seo_skipped' => $this->stats['orphaned_seo_skipped'],
            'template_fallback_applied' => $this->stats['template_fallback_applied'],
            'errors_count' => count($this->stats['errors']),
            'warnings_count' => count($this->stats['warnings']),
        ];

        $body = "=== BLOG SEO IMPORT SUMMARY ===\n";
        foreach ($summary as $k => $v) {
            $body .= $k . ': ' . $v . "\n";
        }
        $body .= "\n=== ERRORS ===\n" . (empty($this->stats['errors']) ? "(none)\n" : implode("\n", $this->stats['errors']) . "\n");
        $body .= "\n=== WARNINGS ===\n" . (empty($this->stats['warnings']) ? "(none)\n" : implode("\n", $this->stats['warnings']) . "\n");
        $body .= "\n=== DETAIL LOG ===\n" . implode("\n", $this->logLines) . "\n";

        file_put_contents($path, $body);

        return $path;
    }
}
