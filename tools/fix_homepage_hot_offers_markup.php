<?php

$html = file_get_contents(__DIR__ . '/../homepage.html');

$html = preg_replace(
    '/<div class="popular-listings__panels">\s*__HOMEPAGE_HOT_OFFERS_PANELS__<\/div>.*?(\s*<div class="popular-listings__panel-glow"><\/div>)/s',
    '<div class="popular-listings__panels">' . PHP_EOL . '              __HOMEPAGE_HOT_OFFERS_PANELS__' . PHP_EOL . '            </div>$1',
    $html
);

file_put_contents(__DIR__ . '/../homepage.html', $html);
echo substr_count($html, '__HOMEPAGE_HOT_OFFERS_PANELS__') . " cleaned hot-offers markers\n";
