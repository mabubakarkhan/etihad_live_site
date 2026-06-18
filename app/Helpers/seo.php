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

if (! function_exists('seo_homepage_bundle')) {
    /**
     * @return array{title: string, description: string, keywords: string, canonical: string, image: string, type: string, robots: string, og_title: string, og_description: string, twitter_card: string, twitter_title: string, twitter_description: string, twitter_image: string, structured_data_json: string}
     */
    function seo_homepage_bundle(?object $cmsPage, ?object $siteSeo = null, array $defaults = []): array
    {
        $base = seo_from_record($cmsPage, $defaults);
        $defaultImage = seo_str($siteSeo?->default_og_image ?? '');
        if ($defaultImage !== '' && ! preg_match('/^https?:\/\//i', $defaultImage)) {
            $defaultImage = asset('storage/' . ltrim($defaultImage, '/'));
        }

        if ($base['image'] === '' && $defaultImage !== '') {
            $base['image'] = $defaultImage;
        }

        $ogImage = seo_str($cmsPage?->og_image ?? '');
        if ($ogImage !== '' && ! preg_match('/^https?:\/\//i', $ogImage)) {
            $ogImage = asset('storage/' . ltrim($ogImage, '/'));
        } elseif ($ogImage === '') {
            $ogImage = $base['image'];
        }

        $twitterImage = seo_str($cmsPage?->twitter_image ?? '');
        if ($twitterImage !== '' && ! preg_match('/^https?:\/\//i', $twitterImage)) {
            $twitterImage = asset('storage/' . ltrim($twitterImage, '/'));
        } elseif ($twitterImage === '') {
            $twitterImage = $ogImage;
        }

        return array_merge($base, [
            'robots' => seo_str($cmsPage?->meta_robots ?? '') ?: 'index, follow',
            'og_title' => seo_str($cmsPage?->og_title ?? '') ?: $base['title'],
            'og_description' => seo_str($cmsPage?->og_description ?? '') ?: $base['description'],
            'og_image' => $ogImage,
            'twitter_card' => seo_str($cmsPage?->twitter_card ?? '') ?: 'summary_large_image',
            'twitter_title' => seo_str($cmsPage?->twitter_title ?? '') ?: (seo_str($cmsPage?->og_title ?? '') ?: $base['title']),
            'twitter_description' => seo_str($cmsPage?->twitter_description ?? '') ?: (seo_str($cmsPage?->og_description ?? '') ?: $base['description']),
            'twitter_image' => $twitterImage,
            'structured_data_json' => seo_str($cmsPage?->structured_data_json ?? ''),
        ]);
    }
}

if (! function_exists('seo_from_record')) {
    /**
     * @param  array{title?: string, description?: string, keywords?: string, canonical?: string, image?: string, type?: string}  $defaults
     * @return array{title: string, description: string, keywords: string, canonical: string, image: string, type: string}
     */
    function seo_from_record(?object $record, array $defaults = []): array
    {
        $title = seo_str($record?->meta_title ?? '') ?: seo_str($defaults['title'] ?? '');
        $description = seo_str($record?->meta_description ?? '');
        if ($description === '' && array_key_exists('description', $defaults)) {
            $description = seo_desc($defaults['description']);
        }
        $keywords = seo_str($record?->meta_keywords ?? '') ?: seo_str($defaults['keywords'] ?? '');
        $canonical = seo_str($record?->canonical_url ?? '') ?: seo_str($defaults['canonical'] ?? (string) url()->current());
        $image = seo_str($defaults['image'] ?? '');
        $type = seo_str($defaults['type'] ?? 'website') ?: 'website';

        return compact('title', 'description', 'keywords', 'canonical', 'image', 'type');
    }
}
