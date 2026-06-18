<?php

use Illuminate\Support\Facades\Storage;

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

            if (Storage::disk('public')->exists($relativePath)) {
                return url('storage/' . $relativePath);
            }
        }

        return rtrim($assetBase, '/') . '/assets/' . ltrim($fallbackFilename, '/');
    }
}
