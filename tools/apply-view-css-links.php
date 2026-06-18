<?php

$base = dirname(__DIR__);

$replacements = [
    'resources/views/dealer.blade.php' => [
        'css' => 'dealer.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/dealer.css') }}\">\n@endpush",
    ],
    'resources/views/portal.blade.php' => [
        'css' => 'portal.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/portal.css') }}\">\n@endpush",
    ],
    'resources/views/team.blade.php' => [
        'css' => 'team.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/team.css') }}\">\n@endpush",
    ],
    'resources/views/listing.blade.php' => [
        'css' => 'listing.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/listing.css') }}\">\n@endpush",
    ],
    'resources/views/projects.blade.php' => [
        'css' => 'projects.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/projects.css') }}\">\n@endpush",
    ],
    'resources/views/project-old.blade.php' => [
        'css' => 'project-old.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/project-old.css') }}\">\n@endpush",
    ],
    'resources/views/careers/index.blade.php' => [
        'css' => 'careers.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/careers.css') }}\">\n@endpush",
    ],
    'resources/views/careers/job.blade.php' => [
        'css' => 'careers-job.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/careers-job.css') }}\">\n@endpush",
    ],
    'resources/views/errors/404.blade.php' => [
        'css' => 'error-404.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/error-404.css') }}\">\n@endpush",
    ],
    'resources/views/partials/wishlist-panel-items.blade.php' => [
        'css' => 'wishlist-panel.css',
        'pattern' => '/<style>.*?<\/style>\s*/s',
        'replacement' => '',
    ],
    'resources/views/project-new.blade.php' => [
        'css' => 'project-new.css',
        'pattern' => '/@push\(\'styles\'\)\s*<style>.*?<\/style>\s*@endpush/s',
        'replacement' => "@push('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/project-new.css') }}\">\n@endpush",
    ],
];

foreach ($replacements as $rel => $cfg) {
    $path = $base . '/' . $rel;
    $content = file_get_contents($path);
    $updated = preg_replace($cfg['pattern'], $cfg['replacement'], $content, 1, $count);
    if ($count === 0) {
        fwrite(STDERR, "No match: {$rel}\n");
        continue;
    }
    file_put_contents($path, $updated);
    echo "Updated {$rel}\n";
}

// property.blade.php: keep meta in @push('styles') but replace style block only
$propPath = $base . '/resources/views/property.blade.php';
$prop = file_get_contents($propPath);
$prop = preg_replace('/<style>.*?<\/style>\s*/s', "<link rel=\"stylesheet\" href=\"{{ asset('theme/css/pages/property-page.css') }}\">\n", $prop, 1, $count);
if ($count) {
    file_put_contents($propPath, $prop);
    echo "Updated property.blade.php\n";
}
