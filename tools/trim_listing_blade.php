<?php
$f = __DIR__ . '/../resources/views/listing.blade.php';
$lines = file($f);
$out = array_merge(array_slice($lines, 0, 69), array_slice($lines, 276));
file_put_contents($f, implode('', $out));
echo count($lines) . ' -> ' . count($out) . PHP_EOL;
