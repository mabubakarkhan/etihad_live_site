<?php

namespace App\Services\ProjectBulkMedia;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ProjectBulkMediaService
{
    /** @var list<string> */
    private array $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function previewFromUpload(string $zipPath, Project $project): array
    {
        $token = Str::random(48);
        $extractDir = storage_path('app/temp/project-bulk-media/' . $token);

        if (! File::isDirectory(dirname($extractDir))) {
            File::makeDirectory(dirname($extractDir), 0755, true);
        }
        File::makeDirectory($extractDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            $this->cleanupDirectory($extractDir);
            throw new \RuntimeException('Could not open ZIP file. Make sure it is a valid archive.');
        }

        if (! $zip->extractTo($extractDir)) {
            $zip->close();
            $this->cleanupDirectory($extractDir);
            throw new \RuntimeException('Could not extract ZIP file.');
        }
        $zip->close();

        $contentRoot = $this->resolveContentRoot($extractDir);
        $plan = $this->buildPlan($contentRoot, $project);

        Cache::put($this->cacheKey($token), [
            'project_id' => $project->id,
            'extract_dir' => $extractDir,
            'content_root' => $contentRoot,
        ], now()->addMinutes((int) config('project_bulk_media.cache_ttl_minutes', 60)));

        return [
            'token' => $token,
            'items' => $plan['items'],
            'warnings' => $plan['warnings'],
            'errors' => $plan['errors'],
            'can_import' => $plan['errors'] === [] && $plan['items'] !== [],
        ];
    }

    /** @return array{imported: list<string>, warnings: list<string>, skipped: list<string>} */
    public function import(string $token, Project $project): array
    {
        $cached = Cache::pull($this->cacheKey($token));
        if (! is_array($cached) || (int) ($cached['project_id'] ?? 0) !== (int) $project->id) {
            throw new \RuntimeException('Import session expired or invalid. Please upload the ZIP again.');
        }

        $contentRoot = (string) ($cached['content_root'] ?? '');
        $extractDir = (string) ($cached['extract_dir'] ?? '');

        if ($contentRoot === '' || ! File::isDirectory($contentRoot)) {
            $this->cleanupDirectory($extractDir);
            throw new \RuntimeException('Extracted files are no longer available. Please upload again.');
        }

        $plan = $this->buildPlan($contentRoot, $project);
        if ($plan['errors'] !== []) {
            $this->cleanupDirectory($extractDir);
            throw new \RuntimeException(implode(' ', $plan['errors']));
        }

        $imported = [];
        $warnings = $plan['warnings'];
        $skipped = [];
        $updates = [];

        foreach ($plan['items'] as $item) {
            if (($item['status'] ?? '') === 'skip') {
                $skipped[] = (string) ($item['message'] ?? 'Skipped item.');
                continue;
            }

            $type = (string) ($item['type'] ?? '');
            try {
                if ($type === 'single') {
                    $field = (string) $item['field'];
                    $path = $this->storeLocalFile((string) $item['source'], $project->id, (string) $item['storage_dir']);
                    $this->deleteIfReplaced($project->{$field} ?? null);
                    $updates[$field] = $path;
                    $imported[] = (string) ($item['label'] ?? $field) . ': ' . basename((string) $item['source']);
                } elseif ($type === 'gallery') {
                    $paths = [];
                    foreach ($item['sources'] as $i => $source) {
                        $paths[] = [
                            'path' => $this->storeLocalFile((string) $source, $project->id, 'gallery'),
                            'order' => $i,
                        ];
                    }
                    foreach ($project->gallery ?? [] as $old) {
                        if (! empty($old['path'])) {
                            Storage::disk('public')->delete($old['path']);
                        }
                    }
                    $updates['gallery'] = $paths;
                    $imported[] = 'Gallery: ' . count($paths) . ' image(s)';
                } elseif ($type === 'price_slider') {
                    $paths = [];
                    foreach ($item['sources'] as $source) {
                        $paths[] = $this->storeLocalFile((string) $source, $project->id, 'price-slider');
                    }
                    foreach ($project->price_slider_images ?? [] as $oldPath) {
                        if (is_string($oldPath) && $oldPath !== '') {
                            Storage::disk('public')->delete($oldPath);
                        }
                    }
                    $updates['price_slider_images'] = $paths;
                    $imported[] = 'Price slider: ' . count($paths) . ' image(s)';
                } elseif ($type === 'pricing_place') {
                    $cards = is_array($project->pricing_place_cards) ? $project->pricing_place_cards : [];
                    $changed = false;
                    foreach ($item['assignments'] as $assignment) {
                        $idx = (int) $assignment['index'];
                        if (! isset($cards[$idx])) {
                            $skipped[] = 'Pricing card ' . ($idx + 1) . ' does not exist in admin.';
                            continue;
                        }
                        $newPath = $this->storeLocalFile((string) $assignment['source'], $project->id, 'pricing-place');
                        if (! empty($cards[$idx]['image'])) {
                            Storage::disk('public')->delete($cards[$idx]['image']);
                        }
                        $cards[$idx]['image'] = $newPath;
                        $changed = true;
                        $imported[] = 'Pricing card ' . ($idx + 1) . ': ' . basename((string) $assignment['source']);
                    }
                    if ($changed) {
                        $updates['pricing_place_cards'] = $cards;
                    }
                } elseif ($type === 'detail_tabs') {
                    $tabs = is_array($project->project_detail_tabs) ? $project->project_detail_tabs : [];
                    $changed = false;
                    foreach ($item['assignments'] as $assignment) {
                        $idx = (int) $assignment['index'];
                        if (! isset($tabs[$idx])) {
                            $skipped[] = 'Detail tab ' . ($idx + 1) . ' does not exist in admin.';
                            continue;
                        }
                        $paths = [];
                        foreach ($assignment['sources'] as $source) {
                            $paths[] = $this->storeLocalFile((string) $source, $project->id, 'detail-tabs');
                        }
                        foreach ($tabs[$idx]['images'] ?? [] as $oldPath) {
                            if (is_string($oldPath) && $oldPath !== '') {
                                Storage::disk('public')->delete($oldPath);
                            }
                        }
                        $tabs[$idx]['images'] = $paths;
                        $changed = true;
                        $imported[] = 'Detail tab ' . ($idx + 1) . ': ' . count($paths) . ' image(s)';
                    }
                    if ($changed) {
                        $updates['project_detail_tabs'] = $tabs;
                    }
                }
            } catch (\Throwable $e) {
                $skipped[] = ((string) ($item['label'] ?? 'Item')) . ': ' . $e->getMessage();
            }
        }

        if ($updates !== []) {
            $project->update($updates);
        }

        $this->cleanupDirectory($extractDir);

        return compact('imported', 'warnings', 'skipped');
    }

    /** @return array{items: list<array<string, mixed>>, warnings: list<string>, errors: list<string>} */
    private function buildPlan(string $contentRoot, Project $project): array
    {
        $items = [];
        $warnings = [];
        $errors = [];
        $seenFolders = [];

        if (! File::isDirectory($contentRoot)) {
            return [
                'items' => [],
                'warnings' => [],
                'errors' => ['ZIP content root not found after extraction.'],
            ];
        }

        foreach (File::directories($contentRoot) as $dirPath) {
            $folder = strtolower(basename($dirPath));
            $seenFolders[] = $folder;

            if (isset(config('project_bulk_media.single_folders')[$folder])) {
                $meta = config('project_bulk_media.single_folders')[$folder];
                $files = $this->collectFiles($dirPath, (string) $meta['kind']);
                if ($files === []) {
                    continue;
                }
                if (count($files) > 1) {
                    $warnings[] = ucfirst($folder) . ': multiple files found; using ' . basename($files[0]) . '.';
                }
                $items[] = [
                    'type' => 'single',
                    'folder' => $folder,
                    'label' => $meta['label'],
                    'field' => $meta['field'],
                    'storage_dir' => $meta['storage_dir'],
                    'source' => $files[0],
                    'file' => basename($files[0]),
                    'status' => 'ok',
                ];
                continue;
            }

            if (isset(config('project_bulk_media.multi_folders')[$folder])) {
                $meta = config('project_bulk_media.multi_folders')[$folder];
                $files = $this->collectFiles($dirPath, (string) $meta['kind']);
                if ($files === []) {
                    continue;
                }
                if ($folder === 'gallery') {
                    $items[] = [
                        'type' => 'gallery',
                        'folder' => $folder,
                        'label' => $meta['label'],
                        'sources' => $files,
                        'files' => array_map('basename', $files),
                        'status' => 'ok',
                    ];
                } else {
                    $items[] = [
                        'type' => 'price_slider',
                        'folder' => $folder,
                        'label' => $meta['label'],
                        'sources' => $files,
                        'files' => array_map('basename', $files),
                        'status' => 'ok',
                    ];
                }
                continue;
            }

            if ($folder === config('project_bulk_media.pricing_place_dir')) {
                $assignments = $this->mapNumberedFiles($dirPath, 'image');
                if ($assignments === []) {
                    continue;
                }
                $cardCount = is_array($project->pricing_place_cards) ? count($project->pricing_place_cards) : 0;
                foreach ($assignments as $assignment) {
                    if ($assignment['index'] >= $cardCount) {
                        $warnings[] = 'pricing-place/' . basename((string) $assignment['source']) . ': no pricing card at position ' . ($assignment['index'] + 1) . ' (skipped on import).';
                    }
                }
                $items[] = [
                    'type' => 'pricing_place',
                    'folder' => $folder,
                    'label' => 'Pricing place cards',
                    'assignments' => $assignments,
                    'files' => array_map(fn ($a) => basename((string) $a['source']), $assignments),
                    'status' => 'ok',
                ];
                continue;
            }

            if ($folder === config('project_bulk_media.detail_tabs_dir')) {
                $assignments = [];
                foreach (File::directories($dirPath) as $tabDir) {
                    $tabFolder = basename($tabDir);
                    if (! preg_match('/^tab-(\d+)$/i', $tabFolder, $m)) {
                        $warnings[] = 'detail-tabs/' . $tabFolder . ': invalid folder name (use tab-1, tab-2, …).';
                        continue;
                    }
                    $tabIndex = max(0, (int) $m[1] - 1);
                    $files = $this->collectFiles($tabDir, 'image');
                    if ($files === []) {
                        continue;
                    }
                    $tabCount = is_array($project->project_detail_tabs) ? count($project->project_detail_tabs) : 0;
                    if ($tabIndex >= $tabCount) {
                        $warnings[] = 'detail-tabs/tab-' . ($tabIndex + 1) . ': no detail tab at that position (skipped on import).';
                    }
                    $assignments[] = [
                        'index' => $tabIndex,
                        'tab' => 'tab-' . ($tabIndex + 1),
                        'sources' => $files,
                        'files' => array_map('basename', $files),
                    ];
                }
                if ($assignments !== []) {
                    usort($assignments, fn ($a, $b) => $a['index'] <=> $b['index']);
                    $items[] = [
                        'type' => 'detail_tabs',
                        'folder' => $folder,
                        'label' => 'Detail tabs',
                        'assignments' => $assignments,
                        'status' => 'ok',
                    ];
                }
                continue;
            }

            $warnings[] = 'Unknown folder ignored: ' . $folder . '/';
        }

        return [
            'items' => $items,
            'warnings' => $warnings,
            'errors' => $errors,
        ];
    }

    private function resolveContentRoot(string $extractDir): string
    {
        $entries = array_values(array_filter(scandir($extractDir) ?: [], fn ($e) => $e !== '.' && $e !== '..'));
        $rootFiles = array_filter($entries, fn ($e) => is_file($extractDir . DIRECTORY_SEPARATOR . $e));
        $rootDirs = array_filter($entries, fn ($e) => is_dir($extractDir . DIRECTORY_SEPARATOR . $e));

        if ($rootFiles === [] && count($rootDirs) === 1) {
            return $extractDir . DIRECTORY_SEPARATOR . reset($rootDirs);
        }

        return $extractDir;
    }

    /** @return list<string> */
    private function collectFiles(string $directory, string $kind): array
    {
        $allowed = $kind === 'pdf' ? ['pdf'] : $this->imageExtensions;
        $files = [];

        foreach (File::allFiles($directory) as $file) {
            $path = $file->getPathname();
            $relative = str_replace('\\', '/', substr($path, strlen($directory)));
            if (str_contains($relative, '/..') || str_starts_with($relative, '../')) {
                continue;
            }

            $ext = strtolower($file->getExtension());
            if (! in_array($ext, $allowed, true)) {
                continue;
            }

            $sizeKb = (int) ceil($file->getSize() / 1024);
            if ($sizeKb > (int) config('project_bulk_media.max_file_kb', 20480)) {
                throw new \RuntimeException(basename($path) . ' exceeds maximum file size.');
            }

            $files[] = $path;
        }

        usort($files, fn ($a, $b) => strnatcasecmp(basename($a), basename($b)));

        return $files;
    }

    /**
     * @return list<array{index: int, source: string}>
     */
    private function mapNumberedFiles(string $directory, string $kind): array
    {
        $out = [];
        foreach ($this->collectFiles($directory, $kind) as $path) {
            $base = pathinfo(basename($path), PATHINFO_FILENAME);
            if (preg_match('/^(\d+)/', $base, $m)) {
                $out[] = [
                    'index' => max(0, (int) $m[1] - 1),
                    'source' => $path,
                ];
            } else {
                $out[] = [
                    'index' => count($out),
                    'source' => $path,
                ];
            }
        }

        usort($out, fn ($a, $b) => $a['index'] <=> $b['index']);

        return $out;
    }

    private function storeLocalFile(string $sourcePath, int $projectId, string $subdir): string
    {
        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $filename = uniqid('', true) . ($ext !== '' ? '.' . $ext : '');
        $dest = 'projects/' . $projectId . '/' . $subdir . '/' . $filename;

        Storage::disk('public')->makeDirectory('projects/' . $projectId . '/' . $subdir);
        Storage::disk('public')->put($dest, File::get($sourcePath));
        $this->mirrorToPublicStorage($dest);

        return $dest;
    }

    private function deleteIfReplaced(?string $path): void
    {
        if (is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
        }
    }

    private function mirrorToPublicStorage(string $storedPath): void
    {
        $source = storage_path('app/public/' . ltrim($storedPath, '/'));
        $destination = public_path('storage/' . ltrim($storedPath, '/'));
        if (! File::exists($source)) {
            return;
        }
        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);
    }

    private function cleanupDirectory(string $dir): void
    {
        if ($dir !== '' && File::isDirectory($dir)) {
            File::deleteDirectory($dir);
        }
    }

    private function cacheKey(string $token): string
    {
        return 'project_bulk_media:' . $token;
    }
}
