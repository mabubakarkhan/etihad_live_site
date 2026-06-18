<?php
/**
 * TEMPORARY diagnostics — upload to public/, open /etihad-diag.php, then DELETE.
 * Does not modify .env. No Artisan/SSH required.
 */
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "Etihad Laravel diagnostics\n";
echo "==========================\n\n";
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
    'bootstrap/cache' => $root . '/bootstrap/cache',
];

echo "--- File / folder checks ---\n";
foreach ($checks as $label => $path) {
    $exists = is_file($path) || is_dir($path);
    echo sprintf("%-22s %s\n", $label . ':', $exists ? 'OK' : 'MISSING');
}

echo "\n--- Writable checks ---\n";
echo 'storage/logs writable: ' . (is_dir($root . '/storage/logs') && is_writable($root . '/storage/logs') ? 'yes' : 'NO') . "\n";
echo 'bootstrap/cache writable: ' . (is_dir($root . '/bootstrap/cache') && is_writable($root . '/bootstrap/cache') ? 'yes' : 'NO') . "\n";
echo 'storage/framework writable: ' . (is_dir($root . '/storage/framework') && is_writable($root . '/storage/framework') ? 'yes' : 'NO') . "\n";

$envPath = $root . '/.env';
if (is_file($envPath)) {
    $envRaw = file_get_contents($envPath) ?: '';
    echo "\n--- .env keys (values not shown) ---\n";
    foreach (['APP_KEY', 'APP_ENV', 'APP_DEBUG', 'DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME'] as $key) {
        $present = (bool) preg_match('/^' . preg_quote($key, '/') . '=.+/m', $envRaw);
        echo sprintf("%-16s %s\n", $key . ':', $present ? 'set' : 'MISSING');
    }
}

echo "\n--- routes/web.php ---\n";
$routesFile = $root . '/routes/web.php';
if (is_file($routesFile)) {
    $src = file_get_contents($routesFile) ?: '';
    echo 'Size: ' . strlen($src) . " bytes\n";
    echo 'Lines: ' . (substr_count($src, "\n") + 1) . "\n";
    token_get_all($src);
    echo "Tokenizer OK (no PHP parse error)\n";
} else {
    echo "routes/web.php not found\n";
}

echo "\n--- Composer autoload ---\n";
try {
    if (! is_file($root . '/vendor/autoload.php')) {
        throw new RuntimeException('vendor/autoload.php missing');
    }
    require $root . '/vendor/autoload.php';
    echo "autoload OK\n";
} catch (Throwable $e) {
    echo 'autoload FAIL: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
    exit;
}

echo "\n--- Laravel bootstrap ---\n";
try {
    $app = require $root . '/bootstrap/app.php';
    echo 'bootstrap/app.php OK: ' . get_class($app) . "\n";
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "Application kernel bootstrap OK\n";
    echo 'APP_ENV: ' . config('app.env') . "\n";
    echo 'APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false') . "\n";
    echo 'APP_KEY set: ' . (config('app.key') ? 'yes' : 'NO') . "\n";
} catch (Throwable $e) {
    echo "BOOT FAIL\n";
    echo 'Class: ' . get_class($e) . "\n";
    echo 'Message: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit;
}

echo "\n--- HTTP kernel /up ---\n";
try {
    $request = Illuminate\Http\Request::create('/up', 'GET');
    $response = $app->handle($request);
    echo 'Status: ' . $response->getStatusCode() . "\n";
    echo substr((string) $response->getContent(), 0, 400) . "\n";
} catch (Throwable $e) {
    echo "Request handle FAIL\n";
    echo 'Class: ' . get_class($e) . "\n";
    echo 'Message: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n--- Recent laravel.log (last 50 lines) ---\n";
$logDir = $root . '/storage/logs';
if (is_dir($logDir)) {
    $logs = glob($logDir . '/laravel*.log') ?: [];
    rsort($logs);
    if ($logs !== []) {
        $lines = file($logs[0]) ?: [];
        echo 'Log file: ' . basename($logs[0]) . "\n\n";
        echo implode('', array_slice($lines, -50));
    } else {
        echo "No laravel.log found yet\n";
    }
} else {
    echo "storage/logs missing\n";
}

echo "\n\nDone. Delete etihad-diag.php, test.php, db-test.php after fixing.\n";
