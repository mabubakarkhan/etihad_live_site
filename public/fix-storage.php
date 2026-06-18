<?php
/**
 * Standalone storage mirror — no Laravel / .env token needed.
 * Upload to public/, open once, then DELETE.
 *
 * URL: /fix-storage.php?token=etihad-storage-fix
 */
header('Content-Type: application/json; charset=utf-8');

$expectedToken = 'etihad-storage-fix';
if (($_GET['token'] ?? '') !== $expectedToken) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid token. Use: /fix-storage.php?token=etihad-storage-fix',
    ], JSON_PRETTY_PRINT);
    exit;
}

$root = dirname(__DIR__);
$source = $root . '/storage/app/public';
$target = __DIR__ . '/storage';

if (! is_dir($source)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Source folder missing: storage/app/public/',
        'hint' => 'Upload storage/app/public/ from local first.',
    ], JSON_PRETTY_PRINT);
    exit;
}

if (! is_dir($target) && ! mkdir($target, 0755, true) && ! is_dir($target)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot create public/storage/'], JSON_PRETTY_PRINT);
    exit;
}

$copied = 0;
$skipped = 0;
$errors = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    $relative = substr($item->getPathname(), strlen($source) + 1);
    $dest = $target . DIRECTORY_SEPARATOR . $relative;

    try {
        if ($item->isDir()) {
            if (! is_dir($dest) && ! mkdir($dest, 0755, true)) {
                $errors[] = 'mkdir failed: ' . $relative;
            }
        } else {
            if (file_exists($dest)) {
                $skipped++;
                continue;
            }
            if (! is_dir(dirname($dest))) {
                mkdir(dirname($dest), 0755, true);
            }
            if (copy($item->getPathname(), $dest)) {
                $copied++;
            } else {
                $errors[] = 'copy failed: ' . $relative;
            }
        }
    } catch (Throwable $e) {
        $errors[] = $relative . ': ' . $e->getMessage();
    }
}

echo json_encode([
    'success' => $errors === [],
    'copied_files' => $copied,
    'skipped_existing' => $skipped,
    'source' => $source,
    'target' => $target,
    'errors' => $errors,
    'note' => 'Delete fix-storage.php from server after use.',
], JSON_PRETTY_PRINT);
