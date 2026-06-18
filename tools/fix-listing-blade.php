<?php
$path = dirname(__DIR__) . '/resources/views/listing.blade.php';
$content = file_get_contents($path);
$pattern = '/(@push\(\'styles\'\)\s*\n<link rel="stylesheet" href="\{\{ asset\(\'theme\/css\/pages\/listing\.css\'\) \}\}">\s*\n<link href="https:\/\/cdn\.jsdelivr\.net\/npm\/tom-select@2\.3\.1\/dist\/css\/tom-select\.css" rel="stylesheet">\s*\n@endpush\s*\n).*?(\n@section\(\'content\'\))/s';
$content = preg_replace($pattern, '$1$2', $content, 1, $count);
if ($count !== 1) {
    fwrite(STDERR, "Fix failed, count={$count}\n");
    exit(1);
}
file_put_contents($path, $content);
echo "Fixed listing.blade.php\n";
