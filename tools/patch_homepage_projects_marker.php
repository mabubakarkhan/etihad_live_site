<?php

$html = file_get_contents(__DIR__ . '/../homepage.html');
$html = preg_replace(
    '/(<div class="dha-showcase__cards">\s*)(?:<article class="dha-showcase__card">.*?<\/article>\s*)+/s',
    '$1__HOMEPAGE_PROJECTS_CARDS__' . PHP_EOL . '              ',
    $html
);

file_put_contents(__DIR__ . '/../homepage.html', $html);
echo substr_count($html, '__HOMEPAGE_PROJECTS_CARDS__') . " markers inserted\n";
