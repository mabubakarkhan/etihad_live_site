<?php
/**
 * TEMPORARY — test DB connection without modifying .env or using Artisan.
 * Open /db-test.php then DELETE this file after debugging.
 */
header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

echo "Database connection test\n";
echo "========================\n\n";
echo 'PHP: ' . PHP_VERSION . "\n";
echo 'Time: ' . date('c') . "\n\n";

$root = dirname(__DIR__);
$envPath = $root . '/.env';

if (! is_file($envPath)) {
    echo "FAIL: .env file not found at {$envPath}\n";
    exit;
}

echo ".env: found\n";

$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
        continue;
    }
    [$key, $value] = explode('=', $line, 2);
    $env[trim($key)] = trim($value, " \t\n\r\0\x0B'\"");
}

$required = ['DB_CONNECTION', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
foreach ($required as $key) {
    $present = array_key_exists($key, $env) && $env[$key] !== '';
    echo sprintf("%-16s %s\n", $key . ':', $present ? 'set' : 'MISSING/empty');
}

$appKey = $env['APP_KEY'] ?? '';
echo sprintf("%-16s %s\n", 'APP_KEY:', $appKey !== '' ? 'set (' . strlen($appKey) . ' chars)' : 'MISSING');

if (($env['APP_KEY'] ?? '') === '') {
    echo "\nWARNING: APP_KEY is missing. Laravel will fail to boot.\n";
}

$connection = $env['DB_CONNECTION'] ?? 'mysql';
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'] ?? '';
$username = $env['DB_USERNAME'] ?? '';
$password = $env['DB_PASSWORD'] ?? '';

echo "\nAttempting connection...\n";
echo "Driver: {$connection}\n";
echo "Host: {$host}:{$port}\n";
echo "Database: {$database}\n";
echo "User: {$username}\n\n";

try {
    if ($connection === 'sqlite') {
        $dsn = 'sqlite:' . ($database !== '' ? $database : $root . '/database/database.sqlite');
    } else {
        $dsn = "{$connection}:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    }

    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
    ]);

    echo "SUCCESS: Database connection OK\n";

    if ($connection !== 'sqlite') {
        $version = $pdo->query('SELECT VERSION()')->fetchColumn();
        echo 'Server version: ' . $version . "\n";
    }
} catch (Throwable $e) {
    echo "FAIL: Database connection error\n";
    echo 'Class: ' . get_class($e) . "\n";
    echo 'Message: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
}

echo "\nDelete db-test.php after debugging.\n";
