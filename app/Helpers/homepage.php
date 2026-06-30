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

if (! function_exists('homepage_about_copy')) {
    /**
     * Normalize About/Vision copy for display (spacing after sentence punctuation).
     */
    function homepage_about_copy(?string $text): string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        return preg_replace('/([.!?])([^\s])/', '$1 $2', $text) ?? $text;
    }
}
