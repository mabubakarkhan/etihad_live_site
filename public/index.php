<?php

/**
 * TEMPORARY LIVE DEBUG — set to false and re-upload after HTTP 500 is fixed.
 * Does not modify .env. Remove debug block when site is stable.
 */
$ETIHAD_LIVE_DEBUG = true;

if ($ETIHAD_LIVE_DEBUG) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (! function_exists('etihad_render_boot_error')) {
    function etihad_render_boot_error(string $stage, Throwable $e): never
    {
        if (! headers_sent()) {
            header('Content-Type: text/html; charset=utf-8');
            http_response_code(500);
        }

        $safe = static fn (?string $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Laravel boot error</title>';
        echo '<style>body{font-family:Consolas,Monaco,monospace;background:#111;color:#eee;padding:20px;line-height:1.5}';
        echo 'h1{color:#f87171;font-size:20px}h2{color:#fbbf24;font-size:16px;margin-top:24px}';
        echo 'pre{background:#1f2937;padding:16px;border-radius:8px;overflow:auto;white-space:pre-wrap;word-break:break-word}';
        echo '.ok{color:#4ade80}.bad{color:#f87171}</style></head><body>';
        echo '<h1>Laravel bootstrap failed</h1>';
        echo '<p><strong>Stage:</strong> ' . $safe($stage) . '</p>';
        echo '<p><strong>Exception:</strong> ' . $safe(get_class($e)) . '</p>';
        echo '<p><strong>Message:</strong> ' . $safe($e->getMessage()) . '</p>';
        echo '<p><strong>File:</strong> ' . $safe($e->getFile()) . '</p>';
        echo '<p><strong>Line:</strong> ' . $safe((string) $e->getLine()) . '</p>';
        echo '<h2>Stack trace</h2><pre>' . $safe($e->getTraceAsString()) . '</pre>';
        echo '<h2>Quick checks</h2><pre>';

        $root = dirname(__DIR__);
        $paths = [
            'vendor/autoload.php' => $root . '/vendor/autoload.php',
            'bootstrap/app.php' => $root . '/bootstrap/app.php',
            'routes/web.php' => $root . '/routes/web.php',
            '.env' => $root . '/.env',
            'storage/logs' => $root . '/storage/logs',
            'bootstrap/cache' => $root . '/bootstrap/cache',
        ];

        foreach ($paths as $label => $path) {
            $exists = is_file($path) || is_dir($path);
            echo ($exists ? '[OK] ' : '[MISSING] ') . $label . "\n";
        }

        echo 'storage/logs writable: ' . (is_dir($root . '/storage/logs') && is_writable($root . '/storage/logs') ? 'yes' : 'no') . "\n";
        echo 'bootstrap/cache writable: ' . (is_dir($root . '/bootstrap/cache') && is_writable($root . '/bootstrap/cache') ? 'yes' : 'no') . "\n";
        echo 'PHP version: ' . PHP_VERSION . "\n";
        echo '</pre>';
        echo '<p>Also open <a href="/test.php" style="color:#93c5fd">/test.php</a>, ';
        echo '<a href="/db-test.php" style="color:#93c5fd">/db-test.php</a>, ';
        echo '<a href="/etihad-diag.php" style="color:#93c5fd">/etihad-diag.php</a></p>';
        echo '<p><strong>After fixing:</strong> set <code>$ETIHAD_LIVE_DEBUG = false</code> in public/index.php or restore the original file.</p>';
        echo '</body></html>';
        exit;
    }
}

try {
    // Determine if the application is in maintenance mode...
    if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    $autoload = __DIR__ . '/../vendor/autoload.php';
    if (! is_file($autoload)) {
        throw new RuntimeException('Missing vendor/autoload.php — run composer install on server or upload the vendor folder.');
    }

    require $autoload;

    $bootstrap = __DIR__ . '/../bootstrap/app.php';
    if (! is_file($bootstrap)) {
        throw new RuntimeException('Missing bootstrap/app.php');
    }

    /** @var Application $app */
    $app = require_once $bootstrap;

    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    if ($ETIHAD_LIVE_DEBUG) {
        etihad_render_boot_error('index.php bootstrap', $e);
    }

    if (! headers_sent()) {
        http_response_code(500);
    }

    echo 'Server Error';
    exit;
}
