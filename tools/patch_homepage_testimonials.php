<?php

$path = dirname(__DIR__) . '/homepage.html';
$html = file_get_contents($path);

$wrapperStart = '<div class="reviews-swiper-wrapper">';
$bgStart = '<div class="reviews__background-image">';
$posStart = strpos($html, $wrapperStart);

if ($posStart === false) {
    fwrite(STDERR, "reviews-swiper-wrapper not found\n");
    exit(1);
}

$contentStart = $posStart + strlen($wrapperStart);
$posBg = strpos($html, $bgStart, $contentStart);

if ($posBg === false) {
    fwrite(STDERR, "reviews__background-image not found\n");
    exit(1);
}

$html = substr($html, 0, $contentStart)
    . "\n                __HOMEPAGE_TESTIMONIALS_SLIDES__\n              </div>\n\n              "
    . substr($html, $posBg);

file_put_contents($path, $html);
echo "Patched homepage.html\n";
