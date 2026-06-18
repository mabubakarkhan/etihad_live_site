<?php
$css = file_get_contents(__DIR__ . '/../public/homepage/assets/homepage-DUfNMqJf.css');
if (preg_match_all('/\.why[^}]{0,500}/', $css, $m)) {
    foreach ($m[0] as $chunk) {
        if (str_contains($chunk, 'background') || str_contains($chunk, 'img')) {
            echo $chunk . "\n---\n";
        }
    }
}
