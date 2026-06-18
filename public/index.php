<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$polyfill = __DIR__ . '/../bootstrap/hosting-polyfills.php';
if (! is_file($polyfill)) {
    $polyfill = __DIR__ . '/hosting-polyfills.php';
}
require $polyfill;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
