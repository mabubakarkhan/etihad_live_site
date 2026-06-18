<?php

if (! function_exists('homepage_asset_url')) {
    /**
     * Resolve a homepage CMS image/video path to a public URL.
     * Uses storage only when the file exists; otherwise falls back to the static homepage bundle asset.
     */
    function homepage_asset_url(?string $storagePath, string $assetBase, string $fallbackFilename): string
    {
        $storagePath = is_string($storagePath) ? trim($storagePath) : '';

        if ($storagePath !== '') {
            $relativePath = ltrim($storagePath, '/');

            $resolvedUrl = public_storage_url($relativePath);

            if ($resolvedUrl !== null) {
                return $resolvedUrl;
            }
        }

        return rtrim($assetBase, '/') . '/assets/' . ltrim($fallbackFilename, '/');
    }
}
