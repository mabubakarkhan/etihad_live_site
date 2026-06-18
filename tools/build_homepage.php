<?php

/**
 * Builds the Laravel root homepage from homepage.html + homepage/assets.
 * Run: php tools/build_homepage.php
 */

$root = dirname(__DIR__);
$sourceHtml = $root . '/homepage.html';
$assetSource = $root . '/homepage/assets';
$destDir = $root . '/public/homepage';
$destHtml = $destDir . '/index.html';
$destAssets = $destDir . '/assets';
$destDist = $destDir . '/dist';

if (! is_file($sourceHtml)) {
    fwrite(STDERR, "Missing source file: {$sourceHtml}\n");
    exit(1);
}

if (! is_dir($assetSource)) {
    fwrite(STDERR, "Missing asset folder: {$assetSource}\n");
    exit(1);
}

function sync_directory(string $source, string $destination): int
{
    if (! is_dir($source)) {
        return 0;
    }

    if (! is_dir($destination) && ! mkdir($destination, 0755, true) && ! is_dir($destination)) {
        throw new RuntimeException("Unable to create directory: {$destination}");
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    $count = 0;

    foreach ($iterator as $item) {
        $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

        if ($item->isDir()) {
            if (! is_dir($target) && ! mkdir($target, 0755, true) && ! is_dir($target)) {
                throw new RuntimeException("Unable to create directory: {$target}");
            }
            continue;
        }

        $targetDir = dirname($target);
        if (! is_dir($targetDir) && ! mkdir($targetDir, 0755, true) && ! is_dir($targetDir)) {
            throw new RuntimeException("Unable to create directory: {$targetDir}");
        }

        if (! file_exists($target) || filemtime($item->getPathname()) > filemtime($target) || filesize($item->getPathname()) !== filesize($target)) {
            if (! copy($item->getPathname(), $target)) {
                throw new RuntimeException("Unable to copy {$item->getPathname()} to {$target}");
            }
            $count++;
        }
    }

    return $count;
}

function resolve_dist_source(string $root): ?string
{
    $candidates = [
        $root . '/homepage/dist',
        $root . '/homepage/assets/dist',
    ];

    foreach ($candidates as $candidate) {
        if (is_dir($candidate)) {
            return $candidate;
        }
    }

    return null;
}

$html = file_get_contents($sourceHtml);

// Root-relative asset paths → relative (resolved via <base> injected by the route).
$html = str_replace('/assets/', 'assets/', $html);

// Static export links → site root (replaced when served).
$html = str_replace('href="index.html"', 'href="__HOME_URL__"', $html);
$html = str_replace("href='index.html'", "href='__HOME_URL__'", $html);
$html = str_replace('content="index.html"', 'content="__HOME_CANONICAL__"', $html);

$distSource = resolve_dist_source($root);

// Only fall back to CDN when no local dist bundle exists.
if ($distSource === null) {
    $cdnScripts = [
        'dist/gsap.min.js' => 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js',
        'dist/ScrollTrigger.min.js' => 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js',
        'dist/TextPlugin.min.js' => 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/TextPlugin.min.js',
        'dist/swiper-bundle.min.css' => 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        'dist/swiper-bundle.min.js' => 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        'dist/lenis.min.js' => 'https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js',
    ];

    foreach ($cdnScripts as $local => $remote) {
        $html = str_replace('href="' . $local . '"', 'href="' . $remote . '"', $html);
        $html = str_replace('src="' . $local . '"', 'src="' . $remote . '"', $html);
    }
}

if (! is_dir($destDir) && ! mkdir($destDir, 0755, true) && ! is_dir($destDir)) {
    fwrite(STDERR, "Unable to create directory: {$destDir}\n");
    exit(1);
}

file_put_contents($destHtml, $html);

$copiedAssets = sync_directory($assetSource, $destAssets);
$copiedDist = $distSource ? sync_directory($distSource, $destDist) : 0;

echo "Wrote {$destHtml}\n";
echo "Synced {$copiedAssets} homepage asset file(s) to public/homepage/assets/\n";

if ($distSource) {
    echo "Synced {$copiedDist} dist file(s) from {$distSource} to public/homepage/dist/\n";
} else {
    echo "Note: no homepage dist bundle found — GSAP plugins will use CDN fallbacks only.\n";
}
