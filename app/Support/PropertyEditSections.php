<?php

namespace App\Support;

class PropertyEditSections
{
    /** @return array<string, array{label: string, tab: string}> */
    public static function all(): array
    {
        return [
            'basic' => ['label' => 'Basic', 'tab' => 'tab-basic'],
            'status' => ['label' => 'Status', 'tab' => 'tab-status'],
            'featured-image' => ['label' => 'Featured image', 'tab' => 'tab-featured-image'],
            'address' => ['label' => 'Address', 'tab' => 'tab-address'],
            'videos' => ['label' => 'Video', 'tab' => 'tab-videos'],
            'gallery' => ['label' => 'Image gallery', 'tab' => 'tab-gallery'],
            'video-gallery' => ['label' => 'Video gallery', 'tab' => 'tab-video-gallery'],
            'price' => ['label' => 'Price', 'tab' => 'tab-price'],
            'property-type' => ['label' => 'Property type & area', 'tab' => 'tab-property-type'],
            'features' => ['label' => 'Features & nearby', 'tab' => 'tab-features'],
            'seo' => ['label' => 'SEO', 'tab' => 'tab-seo'],
            'amenities' => ['label' => 'Amenities', 'tab' => 'tab-amenities'],
        ];
    }

    public static function exists(string $slug): bool
    {
        return isset(self::all()[$slug]);
    }

    public static function tabFor(string $slug): ?string
    {
        return self::all()[$slug]['tab'] ?? null;
    }

    /** @return array<string, mixed> */
    public static function validationRules(string $slug, ?int $propertyId = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        if ($propertyId) {
            $slugRule[] = 'unique:properties,slug,' . $propertyId;
        } else {
            $slugRule[] = 'unique:properties,slug';
        }

        $bySection = [
            'basic' => [
                'dealer_id' => ['nullable', 'integer', 'min:0'],
                'title' => ['required', 'string', 'max:255'],
                'slug' => $slugRule,
                'project_type_ids' => ['nullable', 'array'],
                'project_type_ids.*' => ['exists:project_types,id'],
                'purpose' => ['nullable', 'string', 'in:sale,rent'],
                'description' => ['nullable', 'string'],
            ],
            'status' => [
                'status' => ['nullable', 'string', 'in:active,hold,inactive,close'],
                'is_hot' => ['nullable', 'boolean'],
            ],
            'featured-image' => [
                'featured_image_path' => ['nullable', 'string', 'max:500'],
                'remove_featured_image' => ['nullable', 'boolean'],
                'upload_token' => ['nullable', 'string', 'max:64'],
            ],
            'address' => [
                'state' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'address' => ['nullable', 'string'],
                'short_address' => ['nullable', 'string', 'max:500'],
                'town' => ['nullable', 'string', 'max:255'],
                'is_dha_property' => ['nullable', 'boolean'],
                'dha_phase_id' => ['nullable', 'integer', 'exists:dha_phases,id'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'google_map' => ['nullable', 'string'],
            ],
            'videos' => [
                'videos' => ['nullable', 'array'],
            ],
            'gallery' => [
                'gallery_paths' => ['nullable', 'array'],
                'gallery_paths.*' => ['nullable', 'string', 'max:500'],
                'gallery_order' => ['nullable', 'array'],
                'gallery_remove' => ['nullable', 'array'],
                'upload_token' => ['nullable', 'string', 'max:64'],
            ],
            'video-gallery' => [
                'video_gallery' => ['nullable', 'array'],
            ],
            'price' => [
                'price_string' => ['nullable', 'string', 'max:255'],
                'price_digits' => ['nullable', 'numeric', 'min:0'],
            ],
            'property-type' => [
                'property_type' => ['nullable', 'string', 'in:plot,home,plaza,flat,apartment,file'],
                'bedrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
                'bathrooms' => ['nullable', 'integer', 'min:0', 'max:50'],
                'garage' => ['nullable', 'integer', 'min:0', 'max:20'],
                'kitchen' => ['nullable', 'integer', 'min:0', 'max:20'],
                'area_marla' => ['nullable', 'numeric', 'min:0'],
                'area_kanal' => ['nullable', 'numeric', 'min:0'],
            ],
            'features' => [
                'features' => ['nullable', 'array'],
                'location_accessibility' => ['nullable', 'array'],
                'nearest_hospitals' => ['nullable', 'array'],
                'nearest_markets' => ['nullable', 'array'],
                'nearest_restaurants' => ['nullable', 'array'],
            ],
            'seo' => [
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string', 'max:500'],
                'meta_keywords' => ['nullable', 'string', 'max:500'],
                'canonical_url' => ['nullable', 'string', 'max:500'],
            ],
            'amenities' => [
                'amenities_description' => ['nullable', 'string'],
                'amenity_titles' => ['nullable', 'array'],
                'amenity_icons' => ['nullable', 'array'],
            ],
        ];

        return $bySection[$slug] ?? [];
    }
}
