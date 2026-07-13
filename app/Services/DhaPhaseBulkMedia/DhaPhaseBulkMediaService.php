<?php

namespace App\Services\DhaPhaseBulkMedia;

use App\Models\DhaPhase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class DhaPhaseBulkMediaService
{
    /** @var list<string> */
    private array $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function previewFromUpload(string $zipPath, DhaPhase $phase): array
    {
        $token = Str::random(48);
        $extractDir = storage_path('app/temp/dha-phase-bulk-media/' . $token);

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
        $plan = $this->buildPlan($contentRoot, $phase);

        Cache::put($this->cacheKey($token), [
            'phase_id' => $phase->id,
            'extract_dir' => $extractDir,
            'content_root' => $contentRoot,
        ], now()->addMinutes((int) config('dha_phase_bulk_media.cache_ttl_minutes', 60)));

        return [
            'token' => $token,
            'items' => $plan['items'],
            'warnings' => $plan['warnings'],
            'errors' => $plan['errors'],
            'can_import' => $plan['errors'] === [] && $plan['items'] !== [],
        ];
    }

    /** @return array{imported: list<string>, warnings: list<string>, skipped: list<string>} */
    public function import(string $token, DhaPhase $phase): array
    {
        $cached = Cache::pull($this->cacheKey($token));
        if (! is_array($cached) || (int) ($cached['phase_id'] ?? 0) !== (int) $phase->id) {
            throw new \RuntimeException('Import session expired or invalid. Please upload the ZIP again.');
        }

        $contentRoot = (string) ($cached['content_root'] ?? '');
        $extractDir = (string) ($cached['extract_dir'] ?? '');

        if ($contentRoot === '' || ! File::isDirectory($contentRoot)) {
            $this->cleanupDirectory($extractDir);
            throw new \RuntimeException('Extracted files are no longer available. Please upload again.');
        }

        $plan = $this->buildPlan($contentRoot, $phase);
        if ($plan['errors'] !== []) {
            $this->cleanupDirectory($extractDir);
            throw new \RuntimeException(implode(' ', $plan['errors']));
        }

        $imported = [];
        $warnings = $plan['warnings'];
        $skipped = [];
        $updates = [];

        foreach ($plan['items'] as $item) {
            $type = (string) ($item['type'] ?? '');
            try {
                if ($type === 'single') {
                    $field = (string) $item['field'];
                    $path = $this->storeLocalFile((string) $item['source'], $phase->id, (string) $item['storage_dir']);
                    $this->deleteIfReplaced($phase->{$field} ?? null);
                    $updates[$field] = $path;
                    $imported[] = (string) ($item['label'] ?? $field) . ': ' . basename((string) $item['source']);
                } elseif ($type === 'gallery') {
                    $paths = [];
                    foreach ($item['sources'] as $source) {
                        $paths[] = ['path' => $this->storeLocalFile((string) $source, $phase->id, 'gallery')];
                    }
                    foreach ($phase->image_gallery ?? [] as $old) {
                        if (! empty($old['path'])) {
                            $this->deleteIfReplaced($old['path']);
                        }
                    }
                    $updates['image_gallery'] = $paths;
                    $imported[] = 'Image gallery: ' . count($paths) . ' image(s)';
                } elseif ($type === 'plot_maps') {
                    $maps = [];
                    foreach ($item['sources'] as $source) {
                        $source = (string) $source;
                        $maps[] = [
                            'path' => $this->storeLocalFile($source, $phase->id, 'plot-maps'),
                            'title' => pathinfo(basename($source), PATHINFO_FILENAME),
                        ];
                    }
                    foreach ($phase->plot_maps ?? [] as $old) {
                        if (! empty($old['path'])) {
                            $this->deleteIfReplaced($old['path']);
                        }
                    }
                    $updates['plot_maps'] = $maps;
                    $imported[] = 'Plot maps: ' . count($maps) . ' image(s)';
                }
            } catch (\Throwable $e) {
                $skipped[] = ((string) ($item['label'] ?? 'Item')) . ': ' . $e->getMessage();
            }
        }

        if ($updates !== []) {
            $phase->update($updates);
        }

        $this->cleanupDirectory($extractDir);

        return compact('imported', 'warnings', 'skipped');
    }

    /** @return array{items: list<array<string, mixed>>, warnings: list<string>, errors: list<string>} */
    private function buildPlan(string $contentRoot, DhaPhase $phase): array
    {
        $items = [];
        $warnings = [];
        $errors = [];

        if (! File::isDirectory($contentRoot)) {
            return [
                'items' => [],
                'warnings' => [],
                'errors' => ['ZIP content root not found after extraction.'],
            ];
        }

        foreach (File::directories($contentRoot) as $dirPath) {
            $folder = strtolower(basename($dirPath));

            if (isset(config('dha_phase_bulk_media.single_folders')[$folder])) {
                $meta = config('dha_phase_bulk_media.single_folders')[$folder];
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

            if (isset(config('dha_phase_bulk_media.multi_folders')[$folder])) {
                $meta = config('dha_phase_bulk_media.multi_folders')[$folder];
                $files = $this->collectFiles($dirPath, (string) $meta['kind']);
                if ($files === []) {
                    continue;
                }
                $format = (string) ($meta['format'] ?? 'gallery');
                $items[] = [
                    'type' => $format,
                    'folder' => $folder,
                    'label' => $meta['label'],
                    'sources' => $files,
                    'files' => array_map('basename', $files),
                    'status' => 'ok',
                ];
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
            if ($sizeKb > (int) config('dha_phase_bulk_media.max_file_kb', 20480)) {
                throw new \RuntimeException(basename($path) . ' exceeds maximum file size.');
            }

            $files[] = $path;
        }

        usort($files, fn ($a, $b) => strnatcasecmp(basename($a), basename($b)));

        return $files;
    }

    private function storeLocalFile(string $sourcePath, int $phaseId, string $subdir): string
    {
        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $filename = uniqid('', true) . ($ext !== '' ? '.' . $ext : '');
        $dest = 'dha/phases/' . $phaseId . '/' . $subdir . '/' . $filename;

        Storage::disk('public')->makeDirectory('dha/phases/' . $phaseId . '/' . $subdir);
        Storage::disk('public')->put($dest, File::get($sourcePath));
        $this->mirrorToPublicStorage($dest);

        return $dest;
    }

    private function deleteIfReplaced(?string $path): void
    {
        if (is_string($path) && $path !== '') {
            Storage::disk('public')->delete($path);
            File::delete(public_path('storage/' . ltrim($path, '/')));
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
        return 'dha_phase_bulk_media:' . $token;
    }
}
