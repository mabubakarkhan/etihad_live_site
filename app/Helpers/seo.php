<?php

if (! function_exists('seo_str')) {
    function seo_str(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}

if (! function_exists('seo_desc')) {
    function seo_desc(?string $text, int $limit = 160): string
    {
        $text = seo_str($text ?? '');

        return $text === '' ? '' : \Illuminate\Support\Str::limit(strip_tags($text), $limit);
    }
}

if (! function_exists('seo_from_record')) {
    /**
     * @param  array{title?: string, description?: string, keywords?: string, canonical?: string, image?: string, type?: string}  $defaults
     * @return array{title: string, description: string, keywords: string, canonical: string, image: string, type: string}
     */
    function seo_from_record(?object $record, array $defaults = []): array
    {
        $title = seo_str($record->meta_title ?? '') ?: seo_str($defaults['title'] ?? '');
        $description = seo_str($record->meta_description ?? '');
        if ($description === '' && array_key_exists('description', $defaults)) {
            $description = seo_desc($defaults['description']);
        }
        $keywords = seo_str($record->meta_keywords ?? '') ?: seo_str($defaults['keywords'] ?? '');
        $canonical = seo_str($record->canonical_url ?? '') ?: seo_str($defaults['canonical'] ?? (string) url()->current());
        $image = seo_str($defaults['image'] ?? '');
        $type = seo_str($defaults['type'] ?? 'website') ?: 'website';

        return compact('title', 'description', 'keywords', 'canonical', 'image', 'type');
    }
}
