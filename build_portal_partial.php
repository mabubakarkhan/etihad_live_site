<?php
$h = file_get_contents(__DIR__ . '/html/index3.html');
$a = strpos($h, '<div class="wrapper">');
$b = strpos($h, '<!--content  end-->', $a);
if ($a === false || $b === false) {
    fwrite(STDERR, "Markers not found\n");
    exit(1);
}
$c = substr($h, $a, $b - $a);
$c = preg_replace('#(data-bg|src)="images/([^"]+)"#', '$1="{{ asset(\'theme/images/$2\') }}"', $c);
$c = str_replace("onclick=\"window.location.href='listing.html'\"", 'onclick="window.location.href=\'{{ url(\'/listing\') }}\'"', $c);
$c = str_replace('href="listing.html"', 'href="{{ url(\'/listing\') }}"', $c);
$c = str_replace('href="listing-single.html"', 'href="{{ url(\'/listing\') }}"', $c);
$c = str_replace('href="agent-single.html"', 'href="{{ route(\'team\') }}"', $c);
$c = str_replace('href="index.html"', 'href="{{ route(\'portal\') }}"', $c);
$breadcrumbOld = '<a href="#">Home</a><span>Home SlideShow</span>';
$breadcrumbNew = '<a href="{{ route(\'portal\') }}">Home</a><span>Slideshow</span>';
$c = str_replace($breadcrumbOld, $breadcrumbNew, $c);
$out = __DIR__ . '/resources/views/partials/portal-index3-content.blade.php';
file_put_contents($out, $c);
echo "Wrote $out\n";
