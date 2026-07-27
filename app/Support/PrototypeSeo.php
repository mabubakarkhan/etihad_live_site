<?php

namespace App\Support;

class PrototypeSeo
{
    /**
     * Determine whether a URL path should be excluded from sitemap generation.
     */
    public static function isExcludedFromSitemap(string $path): bool
    {
        $path = '/' . ltrim($path, '/');
        $patterns = config('prototype_map.sitemap_excluded_paths', []);

        foreach ($patterns as $pattern) {
            $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';

            if (preg_match($regex, $path)) {
                return true;
            }
        }

        return false;
    }
}
