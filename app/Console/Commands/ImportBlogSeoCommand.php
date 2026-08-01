<?php

namespace App\Console\Commands;

use App\Services\Blog\WpAioseoSeoImporter;
use Illuminate\Console\Command;
use Throwable;

class ImportBlogSeoCommand extends Command
{
    protected $signature = 'blog:import-seo
        {--host=127.0.0.1 : WP import DB host}
        {--database=etihad_wp_import : WP import database name}
        {--username=root : WP import DB username}
        {--password= : WP import DB password}
        {--prefix=shergar_ : WordPress table prefix}
        {--chunk=200 : Records per batch}';

    protected $description = 'Import All In One SEO (AIOSEO) metadata into existing Laravel blog posts (SEO fields only)';

    public function handle(): int
    {
        $this->info('Inspecting AIOSEO schema and importing SEO into existing blog posts…');
        $this->warn('Only SEO fields will be updated. Content/categories/tags/author/dates will not change.');

        try {
            $importer = new WpAioseoSeoImporter(
                host: (string) $this->option('host'),
                database: (string) $this->option('database'),
                username: (string) $this->option('username'),
                password: (string) ($this->option('password') ?? ''),
                prefix: (string) $this->option('prefix'),
            );

            $chunk = max(1, (int) $this->option('chunk'));
            $bar = $this->output->createProgressBar(1);
            $bar->start();

            $result = $importer->run($chunk, function (int $processed, int $total) use ($bar) {
                $bar->setMaxSteps(max($total, 1));
                $bar->setProgress(min($processed, max($total, 1)));
            });

            $bar->finish();
            $this->newLine(2);

            $stats = $result['stats'];
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total WordPress SEO records (aioseo_posts)', $stats['total_wp_seo_records']],
                    ['Total Laravel blog posts', $stats['total_blog_posts']],
                    ['Successfully linked from aioseo_posts', $stats['successfully_linked_posts']],
                    ['Updated SEO records', $stats['updated_seo_records']],
                    ['Skipped (no changes)', $stats['skipped_no_changes']],
                    ['Missing blog mappings', $stats['missing_blog_mappings']],
                    ['Missing per-post SEO rows (used templates)', $stats['missing_seo_records']],
                    ['Template fallback applied', $stats['template_fallback_applied']],
                    ['Orphaned SEO skipped (non-blog)', $stats['orphaned_seo_skipped']],
                    ['Errors', count($stats['errors'])],
                    ['Warnings', count($stats['warnings'])],
                ]
            );

            $this->info('Detailed log: ' . $result['log_path']);

            if (! empty($stats['errors'])) {
                $this->error('Completed with errors. See log for details.');

                return self::FAILURE;
            }

            $this->info('SEO import completed successfully.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('SEO import failed: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
