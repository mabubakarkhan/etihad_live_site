<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/hosting-polyfills.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

try {
    if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
        require $maintenance;
    }

    require __DIR__ . '/../vendor/autoload.php';

    /** @var Application $app */
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $app->handleRequest(Request::capture());
} catch (Throwable $e) {
    if (! headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code(500);
    }

    $h = static fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Error</title>';
    echo '<style>body{font:16px/1.5 Arial,sans-serif;background:#1a1a2e;color:#eee;padding:24px;max-width:900px;margin:0 auto}';
    echo 'h1{color:#ff6b6b}pre{background:#16213e;padding:16px;border-radius:8px;overflow:auto;white-space:pre-wrap;font-size:13px}</style></head><body>';
    echo '<h1>Site error</h1>';
    echo '<p><b>' . $h(get_class($e)) . '</b></p>';
    echo '<p>' . $h($e->getMessage()) . '</p>';
    echo '<p><b>File:</b> ' . $h($e->getFile()) . '</p>';
    echo '<p><b>Line:</b> ' . $h((string) $e->getLine()) . '</p>';
    echo '<h2>Stack trace</h2><pre>' . $h($e->getTraceAsString()) . '</pre>';
    echo '</body></html>';
    exit;
}
