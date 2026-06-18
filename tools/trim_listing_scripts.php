<?php
$f = __DIR__ . '/../resources/views/listing.blade.php';
$lines = file($f);
$replacement = "@push('scripts')\n@include('partials.listing-page-scripts')\n@endpush\n";
$out = array_merge(array_slice($lines, 0, 109), [$replacement]);
file_put_contents($f, implode('', $out));
echo count($lines) . ' -> ' . count($out) . PHP_EOL;
