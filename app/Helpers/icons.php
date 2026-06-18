<?php

if (! function_exists('iconify_url')) {
    /**
     * Build Iconify CDN URL. Legacy values without ":" use heroicons-outline.
     */
    function iconify_url(?string $iconRef, int $height = 24): string
    {
        $iconRef = trim((string) $iconRef);
        if ($iconRef === '') {
            return '';
        }

        // Legacy Font Awesome class strings saved before Iconify picker (e.g. "fa-light fa-key").
        if (preg_match('/^fa-(light|solid|regular|brands)\s+fa-([a-z0-9-]+)$/i', $iconRef, $m)) {
            $style = match (strtolower($m[1])) {
                'solid' => 'fa6-solid',
                'regular' => 'fa6-regular',
                'brands' => 'fa6-brands',
                default => 'fa6-regular',
            };
            $iconRef = $style . ':' . $m[2];
        } elseif (! str_contains($iconRef, ':')) {
            $iconRef = 'heroicons-outline:' . $iconRef;
        }

        [$set, $name] = explode(':', $iconRef, 2);
        $set = trim($set);
        $name = trim($name);
        if ($set === '' || $name === '') {
            return '';
        }

        return 'https://api.iconify.design/' . rawurlencode($set) . '/' . rawurlencode($name) . '.svg?height=' . $height;
    }
}

if (! function_exists('real_estate_icon_picker_options')) {
    /**
     * Curated property / real-estate icons (MDI + Tabler + legacy Heroicons).
     *
     * @return array<int, array{ref: string, label: string, keywords: string}>
     */
    function real_estate_icon_picker_options(): array
    {
        $icons = [
            // Homes & buildings
            ['ref' => 'mdi:home', 'label' => 'home', 'keywords' => 'house property residential'],
            ['ref' => 'mdi:home-outline', 'label' => 'home-outline', 'keywords' => 'house property'],
            ['ref' => 'mdi:home-city', 'label' => 'home-city', 'keywords' => 'apartment building urban'],
            ['ref' => 'mdi:home-city-outline', 'label' => 'home-city-outline', 'keywords' => 'apartment building'],
            ['ref' => 'mdi:home-modern', 'label' => 'home-modern', 'keywords' => 'modern house villa'],
            ['ref' => 'mdi:home-variant', 'label' => 'home-variant', 'keywords' => 'house property'],
            ['ref' => 'mdi:home-group', 'label' => 'home-group', 'keywords' => 'community housing society'],
            ['ref' => 'mdi:home-search', 'label' => 'home-search', 'keywords' => 'search property listing'],
            ['ref' => 'mdi:home-plus', 'label' => 'home-plus', 'keywords' => 'new property add'],
            ['ref' => 'mdi:office-building', 'label' => 'office-building', 'keywords' => 'commercial tower office'],
            ['ref' => 'mdi:office-building-outline', 'label' => 'office-building-outline', 'keywords' => 'commercial office'],
            ['ref' => 'mdi:domain', 'label' => 'domain', 'keywords' => 'building estate complex'],
            ['ref' => 'mdi:city', 'label' => 'city', 'keywords' => 'urban skyline town'],
            ['ref' => 'mdi:city-variant', 'label' => 'city-variant', 'keywords' => 'city town location'],
            ['ref' => 'mdi:warehouse', 'label' => 'warehouse', 'keywords' => 'industrial storage boundary wall'],
            ['ref' => 'mdi:store', 'label' => 'store', 'keywords' => 'shop retail commercial'],
            ['ref' => 'mdi:storefront', 'label' => 'storefront', 'keywords' => 'shop retail'],
            ['ref' => 'mdi:sign-real-estate', 'label' => 'sign-real-estate', 'keywords' => 'for sale rent board'],
            ['ref' => 'tabler:building', 'label' => 'building', 'keywords' => 'tower block flats'],
            ['ref' => 'tabler:building-community', 'label' => 'building-community', 'keywords' => 'society community'],
            ['ref' => 'tabler:building-estate', 'label' => 'building-estate', 'keywords' => 'estate housing'],
            ['ref' => 'tabler:building-skyscraper', 'label' => 'building-skyscraper', 'keywords' => 'high rise tower'],
            ['ref' => 'tabler:home', 'label' => 'home-tabler', 'keywords' => 'house property'],
            ['ref' => 'tabler:home-2', 'label' => 'home-2', 'keywords' => 'house villa'],
            ['ref' => 'tabler:smart-home', 'label' => 'smart-home', 'keywords' => 'automation iot'],
            // Location
            ['ref' => 'mdi:map-marker', 'label' => 'map-marker', 'keywords' => 'location pin address'],
            ['ref' => 'mdi:map-marker-outline', 'label' => 'map-marker-outline', 'keywords' => 'location address'],
            ['ref' => 'mdi:map', 'label' => 'map', 'keywords' => 'location area map'],
            ['ref' => 'mdi:compass', 'label' => 'compass', 'keywords' => 'direction navigation'],
            ['ref' => 'mdi:compass-outline', 'label' => 'compass-outline', 'keywords' => 'direction'],
            ['ref' => 'tabler:map-pin', 'label' => 'map-pin', 'keywords' => 'location address'],
            ['ref' => 'tabler:map-2', 'label' => 'map-2', 'keywords' => 'location area'],
            // Access & structure
            ['ref' => 'mdi:key', 'label' => 'key', 'keywords' => 'possession access ownership'],
            ['ref' => 'mdi:key-variant', 'label' => 'key-variant', 'keywords' => 'possession key'],
            ['ref' => 'mdi:door', 'label' => 'door', 'keywords' => 'entrance entry unit'],
            ['ref' => 'mdi:door-open', 'label' => 'door-open', 'keywords' => 'entrance open'],
            ['ref' => 'mdi:garage', 'label' => 'garage', 'keywords' => 'parking car port'],
            ['ref' => 'mdi:garage-open', 'label' => 'garage-open', 'keywords' => 'parking garage'],
            ['ref' => 'mdi:gate', 'label' => 'gate', 'keywords' => 'gated community entrance'],
            ['ref' => 'mdi:elevator', 'label' => 'elevator', 'keywords' => 'lift building'],
            ['ref' => 'mdi:stairs', 'label' => 'stairs', 'keywords' => 'floors staircase'],
            ['ref' => 'mdi:floor-plan', 'label' => 'floor-plan', 'keywords' => 'layout plan blueprint'],
            ['ref' => 'mdi:ruler-square', 'label' => 'ruler-square', 'keywords' => 'area size marla kanal'],
            ['ref' => 'mdi:ruler', 'label' => 'ruler', 'keywords' => 'measurement size'],
            ['ref' => 'mdi:texture-box', 'label' => 'texture-box', 'keywords' => 'plot area land'],
            ['ref' => 'tabler:door', 'label' => 'door-tabler', 'keywords' => 'entrance unit'],
            ['ref' => 'tabler:key', 'label' => 'key-tabler', 'keywords' => 'possession'],
            // Rooms & interiors
            ['ref' => 'mdi:bed', 'label' => 'bed', 'keywords' => 'bedroom room'],
            ['ref' => 'mdi:bed-double', 'label' => 'bed-double', 'keywords' => 'bedroom master'],
            ['ref' => 'mdi:bed-king-outline', 'label' => 'bed-king', 'keywords' => 'bedroom'],
            ['ref' => 'mdi:sofa', 'label' => 'sofa', 'keywords' => 'living lounge furniture'],
            ['ref' => 'mdi:shower', 'label' => 'shower', 'keywords' => 'bathroom wash'],
            ['ref' => 'mdi:bathtub', 'label' => 'bathtub', 'keywords' => 'bathroom bath'],
            ['ref' => 'mdi:toilet', 'label' => 'toilet', 'keywords' => 'bathroom wc'],
            ['ref' => 'mdi:sink', 'label' => 'sink', 'keywords' => 'kitchen bathroom'],
            ['ref' => 'mdi:stove', 'label' => 'stove', 'keywords' => 'kitchen cooking'],
            ['ref' => 'mdi:fridge', 'label' => 'fridge', 'keywords' => 'kitchen appliance'],
            ['ref' => 'mdi:washing-machine', 'label' => 'washing-machine', 'keywords' => 'laundry appliance'],
            ['ref' => 'mdi:air-conditioner', 'label' => 'air-conditioner', 'keywords' => 'ac cooling hvac'],
            ['ref' => 'mdi:fan', 'label' => 'fan', 'keywords' => 'ventilation cooling'],
            ['ref' => 'tabler:bed', 'label' => 'bed-tabler', 'keywords' => 'bedroom'],
            ['ref' => 'tabler:bath', 'label' => 'bath', 'keywords' => 'bathroom'],
            // Outdoor & amenities
            ['ref' => 'mdi:pool', 'label' => 'pool', 'keywords' => 'swimming amenity'],
            ['ref' => 'mdi:tree', 'label' => 'tree', 'keywords' => 'park green garden'],
            ['ref' => 'mdi:flower', 'label' => 'flower', 'keywords' => 'garden landscape'],
            ['ref' => 'mdi:fence', 'label' => 'fence', 'keywords' => 'boundary wall compound'],
            ['ref' => 'mdi:balcony', 'label' => 'balcony', 'keywords' => 'terrace outdoor'],
            ['ref' => 'mdi:window-closed-variant', 'label' => 'window', 'keywords' => 'glass facade'],
            ['ref' => 'mdi:tennis', 'label' => 'tennis', 'keywords' => 'sports court'],
            ['ref' => 'mdi:basketball', 'label' => 'basketball', 'keywords' => 'sports court'],
            ['ref' => 'mdi:dumbbell', 'label' => 'dumbbell', 'keywords' => 'gym fitness'],
            ['ref' => 'mdi:spa', 'label' => 'spa', 'keywords' => 'wellness sauna'],
            ['ref' => 'tabler:pool', 'label' => 'pool-tabler', 'keywords' => 'swimming'],
            ['ref' => 'tabler:trees', 'label' => 'trees', 'keywords' => 'park green'],
            // Parking & transport
            ['ref' => 'mdi:car', 'label' => 'car', 'keywords' => 'parking vehicle'],
            ['ref' => 'mdi:car-side', 'label' => 'car-side', 'keywords' => 'parking vehicle'],
            ['ref' => 'mdi:parking', 'label' => 'parking', 'keywords' => 'car park slot'],
            ['ref' => 'mdi:truck', 'label' => 'truck', 'keywords' => 'delivery logistics'],
            ['ref' => 'tabler:car', 'label' => 'car-tabler', 'keywords' => 'parking'],
            ['ref' => 'tabler:car-garage', 'label' => 'car-garage', 'keywords' => 'garage parking'],
            // Utilities & security
            ['ref' => 'mdi:water', 'label' => 'water', 'keywords' => 'supply utility sui'],
            ['ref' => 'mdi:water-pump', 'label' => 'water-pump', 'keywords' => 'bore supply'],
            ['ref' => 'mdi:gas-station', 'label' => 'gas-station', 'keywords' => 'gas sui utility'],
            ['ref' => 'mdi:lightning-bolt', 'label' => 'lightning-bolt', 'keywords' => 'electricity power'],
            ['ref' => 'mdi:power-plug', 'label' => 'power-plug', 'keywords' => 'electricity connection'],
            ['ref' => 'mdi:wifi', 'label' => 'wifi', 'keywords' => 'internet connectivity'],
            ['ref' => 'mdi:cctv', 'label' => 'cctv', 'keywords' => 'security camera surveillance'],
            ['ref' => 'mdi:shield-check', 'label' => 'shield-check', 'keywords' => 'security safe gated'],
            ['ref' => 'mdi:lock', 'label' => 'lock', 'keywords' => 'secure gated'],
            ['ref' => 'mdi:security', 'label' => 'security', 'keywords' => 'guard protection'],
            // Finance & legal
            ['ref' => 'mdi:cash', 'label' => 'cash', 'keywords' => 'price payment money'],
            ['ref' => 'mdi:currency-usd', 'label' => 'currency-usd', 'keywords' => 'price money pkr'],
            ['ref' => 'mdi:hand-coin', 'label' => 'hand-coin', 'keywords' => 'investment payment'],
            ['ref' => 'mdi:chart-line', 'label' => 'chart-line', 'keywords' => 'investment growth roi'],
            ['ref' => 'mdi:trending-up', 'label' => 'trending-up', 'keywords' => 'growth appreciation'],
            ['ref' => 'mdi:scale-balance', 'label' => 'scale-balance', 'keywords' => 'legal disputed title'],
            ['ref' => 'mdi:file-document', 'label' => 'file-document', 'keywords' => 'file balloted document noc'],
            ['ref' => 'mdi:ticket', 'label' => 'ticket', 'keywords' => 'ballot file token'],
            // Nearby places
            ['ref' => 'mdi:hospital-building', 'label' => 'hospital', 'keywords' => 'medical health nearby'],
            ['ref' => 'mdi:school', 'label' => 'school', 'keywords' => 'education nearby'],
            ['ref' => 'mdi:bank', 'label' => 'bank', 'keywords' => 'finance atm nearby'],
            ['ref' => 'mdi:mosque', 'label' => 'mosque', 'keywords' => 'worship nearby'],
            // Legacy Heroicons (existing saved values)
            ['ref' => 'heroicons-outline:home', 'label' => 'home-legacy', 'keywords' => 'heroicons house'],
            ['ref' => 'heroicons-outline:office-building', 'label' => 'office-legacy', 'keywords' => 'heroicons building'],
            ['ref' => 'heroicons-outline:location-marker', 'label' => 'location-legacy', 'keywords' => 'heroicons pin'],
            ['ref' => 'heroicons-outline:key', 'label' => 'key-legacy', 'keywords' => 'heroicons possession'],
            ['ref' => 'heroicons-outline:map', 'label' => 'map-legacy', 'keywords' => 'heroicons location'],
            ['ref' => 'heroicons-outline:check', 'label' => 'check-legacy', 'keywords' => 'heroicons verified'],
            ['ref' => 'heroicons-outline:star', 'label' => 'star-legacy', 'keywords' => 'heroicons featured'],
            ['ref' => 'heroicons-outline:flag', 'label' => 'flag-legacy', 'keywords' => 'heroicons marker'],
            ['ref' => 'heroicons-outline:briefcase', 'label' => 'briefcase-legacy', 'keywords' => 'heroicons commercial'],
        ];

        return $icons;
    }
}
