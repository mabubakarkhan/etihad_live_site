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

if (! function_exists('seo_map_viewer_bundle')) {
    /**
     * @param  'project'|'phase'  $entityType
     * @return array{title: string, description: string, keywords: string, canonical: string, image: string, type: string, robots: string, og_title: string, og_description: string, og_image: string, twitter_card: string, twitter_title: string, twitter_description: string, twitter_image: string, structured_data_json: string, geo_placename: string, geo_region: string}
     */
    function seo_map_viewer_bundle(object $entity, string $viewerUrl, string $entityType = 'project'): array
    {
        $appName = seo_str(config('app.name')) ?: 'Etihad Marketing';
        $entityTitle = seo_str($entity->title ?? 'Property');
        $mapHeading = seo_str($entity->map_section_heading ?? '');
        $mapTagline = seo_str($entity->map_section_tagline ?? '');
        $heading = $mapHeading !== '' ? $mapHeading : $entityTitle . ' Interactive Map';

        $metaTitle = seo_str($entity->map_section_meta_title ?? '');
        if ($metaTitle === '') {
            $metaTitle = $heading . ' | ' . $appName;
        }

        $location = $entityType === 'project'
            ? trim(seo_str($entity->city ?? '') . ', ' . seo_str($entity->state ?? ''), ', ')
            : 'Lahore, Punjab';

        $description = seo_str($entity->map_section_meta_description ?? '');
        if ($description === '') {
            $descriptionSource = $mapTagline !== ''
                ? $mapTagline
                : 'Explore the interactive master plan map for ' . $entityTitle . '. View sectors, plots, and development zones with ' . $appName . '.';
            if ($location !== '') {
                $descriptionSource .= ' Located in ' . $location . '.';
            }
            $description = seo_desc($descriptionSource, 165);
        } else {
            $description = seo_desc($description, 165);
        }

        $keywords = seo_str($entity->map_section_meta_keywords ?? '');
        if ($keywords === '') {
            $keywordParts = array_filter([
                $entityTitle,
                $heading,
                $mapTagline,
                $location,
                'interactive map',
                'master plan map',
                'plot map',
                'property map',
                'DHA Lahore map',
                'Lahore real estate',
                'Etihad Marketing map',
                $appName,
                $entityType === 'phase' ? 'DHA phase map' : 'project development map',
            ]);
            $recordKeywords = seo_str($entity->meta_keywords ?? '');
            if ($recordKeywords !== '') {
                $keywordParts[] = $recordKeywords;
            }
            $keywords = implode(', ', array_unique(array_filter(array_map(
                fn ($part) => seo_str($part),
                $keywordParts
            ))));
        }

        $image = method_exists($entity, 'mapSectionImageUrl') ? seo_str($entity->mapSectionImageUrl() ?? '') : '';
        if ($image === '' && seo_str($entity->featured_image ?? '') !== '') {
            $image = url('storage/' . ltrim((string) $entity->featured_image, '/'));
        }
        if ($image === '') {
            $image = asset('theme/images/all/1.jpg');
        }

        $ogTitle = seo_str($entity->map_section_meta_title ?? '') ?: ($heading . ' | Interactive Map');

        $parentUrl = $entityType === 'phase'
            ? route('dha.phase.show', ['phase' => $entity->slug])
            : route('project.show', ['slug' => $entity->slug]);

        return [
            'title' => $metaTitle,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => $parentUrl,
            'image' => $image,
            'type' => 'website',
            'robots' => 'noindex, nofollow, noarchive, nosnippet, noimageindex, max-snippet:0, max-image-preview:none',
            'og_title' => $ogTitle,
            'og_description' => $description,
            'og_image' => $image,
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $ogTitle,
            'twitter_description' => $description,
            'twitter_image' => $image,
            'geo_placename' => $location !== '' ? $location : 'Lahore',
            'geo_region' => 'PK-PB',
            'structured_data_json' => '',
        ];
    }
}
