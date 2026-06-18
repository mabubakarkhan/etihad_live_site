<?php
/**
 * Temporary diagnostics — upload to public/ on live, open /etihad-diag.php, then DELETE.
 */
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "Etihad diagnostics\n";
echo "==================\n\n";
echo 'PHP: ' . PHP_VERSION . "\n";
echo 'Time: ' . date('c') . "\n";
echo 'Script: ' . __FILE__ . "\n\n";

$root = dirname(__DIR__);
echo "App root: {$root}\n\n";

$checks = [
    'vendor/autoload.php' => $root . '/vendor/autoload.php',
    'bootstrap/app.php' => $root . '/bootstrap/app.php',
    'routes/web.php' => $root . '/routes/web.php',
    '.env' => $root . '/.env',
    'storage/logs' => $root . '/storage/logs',
];

foreach ($checks as $label => $path) {
    echo sprintf("%-20s %s\n", $label . ':', is_file($path) || is_dir($path) ? 'OK' : 'MISSING');
}

echo "\n--- routes/web.php syntax ---\n";
$routesFile = $root . '/routes/web.php';
if (is_file($routesFile)) {
    $src = file_get_contents($routesFile) ?: '';
    echo 'Size: ' . strlen($src) . " bytes\n";
    echo 'Lines: ' . substr_count($src, "\n") + 1 . "\n";
    token_get_all($src);
    echo "php tokenizer OK (no parse error)\n";
} else {
    echo "routes/web.php not found\n";
}

echo "\n--- Composer autoload ---\n";
try {
    require $root . '/vendor/autoload.php';
    echo "autoload OK\n";
} catch (Throwable $e) {
    echo 'autoload FAIL: ' . $e->getMessage() . "\n";
    exit;
}

echo "\n--- Laravel bootstrap ---\n";
try {
    $app = require $root . '/bootstrap/app.php';
    echo 'bootstrap OK: ' . get_class($app) . "\n";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "kernel bootstrap OK\n";
    echo 'APP_ENV: ' . config('app.env') . "\n";
    echo 'APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false') . "\n";
} catch (Throwable $e) {
    echo "bootstrap FAIL\n";
    echo 'Class: ' . get_class($e) . "\n";
    echo 'Message: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}

echo "\n--- HTTP kernel handle /up ---\n";
try {
    $request = Illuminate\Http\Request::create('/up', 'GET');
    $response = $app->handle($request);
    echo 'Status: ' . $response->getStatusCode() . "\n";
    echo substr((string) $response->getContent(), 0, 500) . "\n";
} catch (Throwable $e) {
    echo "handle FAIL\n";
    echo 'Class: ' . get_class($e) . "\n";
    echo 'Message: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n--- Recent log (last 40 lines) ---\n";
$logDir = $root . '/storage/logs';
if (is_dir($logDir)) {
    $logs = glob($logDir . '/laravel*.log') ?: [];
    rsort($logs);
    if ($logs !== []) {
        $lines = file($logs[0]) ?: [];
        echo implode('', array_slice($lines, -40));
    } else {
        echo "No laravel.log found\n";
    }
} else {
    echo "storage/logs missing\n";
}

echo "\nDone. Delete this file after use.\n";
