<?php

$base = dirname(__DIR__);
$outDir = $base . '/public/theme/css/pages';
if (! is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

$map = [
    'resources/views/dealer.blade.php' => 'dealer.css',
    'resources/views/portal.blade.php' => 'portal.css',
    'resources/views/team.blade.php' => 'team.css',
    'resources/views/listing.blade.php' => 'listing.css',
    'resources/views/property.blade.php' => 'property-page.css',
    'resources/views/projects.blade.php' => 'projects.css',
    'resources/views/project-new.blade.php' => 'project-new.css',
    'resources/views/project-old.blade.php' => 'project-old.css',
    'resources/views/careers/index.blade.php' => 'careers.css',
    'resources/views/careers/job.blade.php' => 'careers-job.css',
    'resources/views/errors/404.blade.php' => 'error-404.css',
    'resources/views/partials/wishlist-panel-items.blade.php' => 'wishlist-panel.css',
];

foreach ($map as $rel => $cssFile) {
    $path = $base . '/' . $rel;
    $content = file_get_contents($path);
    if (! preg_match('/<style>\s*(.*?)\s*<\/style>/s', $content, $m)) {
        fwrite(STDERR, "No style block: {$rel}\n");
        continue;
    }
    $css = trim($m[1]) . "\n";
    file_put_contents($outDir . '/' . $cssFile, $css);
    echo "Wrote {$cssFile} (" . strlen($css) . " bytes) from {$rel}\n";
}
