<?php
$lines = file(__DIR__ . '/../resources/views/listing.blade.php');
$block = implode('', array_slice($lines, 67, 209));
file_put_contents(__DIR__ . '/../resources/views/partials/listing-search-block.blade.php', $block);
echo 'lines ' . substr_count($block, "\n") . PHP_EOL;
