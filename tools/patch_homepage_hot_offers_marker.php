<?php

$html = file_get_contents(__DIR__ . '/../homepage.html');

if (str_contains($html, '__HOMEPAGE_HOT_OFFERS_PANELS__')) {
    fwrite(STDERR, "Hot offers markers already present.\n");
    exit(0);
}

$html = preg_replace(
    '/(<div class="popular-listings__panels">\s*)(?:<div class="popular-listings__panel"[\s\S]*?<\/div>\s*)+(<\/div>\s*\n\s*<\/div>\s*\n\s*<div class="popular-listings__panel-glow">)/',
    '$1__HOMEPAGE_HOT_OFFERS_PANELS__$2',
    $html
);

file_put_contents(__DIR__ . '/../homepage.html', $html);
echo substr_count($html, '__HOMEPAGE_HOT_OFFERS_PANELS__') . " hot-offers panel markers inserted\n";
