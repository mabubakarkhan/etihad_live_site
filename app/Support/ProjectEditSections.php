<?php

namespace App\Support;

class ProjectEditSections
{
    /** @return array<string, array{label: string, tab: string}> */
    public static function all(): array
    {
        return [
            'basics' => ['label' => 'Basics', 'tab' => 'tab-basics'],
            'status' => ['label' => 'Status', 'tab' => 'tab-status'],
            'address' => ['label' => 'Address', 'tab' => 'tab-address'],
            'media' => ['label' => 'Hero section', 'tab' => 'tab-media'],
            'featured-video' => ['label' => 'Featured video', 'tab' => 'tab-featured-video'],
            'gallery' => ['label' => 'Gallery', 'tab' => 'tab-gallery'],
            'features' => ['label' => 'Unique features', 'tab' => 'tab-features'],
            'pricing-place' => ['label' => 'Pricing place', 'tab' => 'tab-pricing-place'],
            'price-slider' => ['label' => 'Price Slider', 'tab' => 'tab-price-slider'],
            'map-section' => ['label' => 'Interactive map', 'tab' => 'tab-map-section'],
            'detail-tabs' => ['label' => 'Detail tabs', 'tab' => 'tab-detail-tabs'],
            'tabs-follow-content' => ['label' => 'After tabs content', 'tab' => 'tab-tabs-follow-content'],
            'vr-tour' => ['label' => 'VR Tour', 'tab' => 'tab-vr-tour'],
            'booking-procedure' => ['label' => 'Booking Procedure', 'tab' => 'tab-booking-procedure'],
            'social-proof' => ['label' => 'Testimonials + Invest', 'tab' => 'tab-social-proof'],
            'videos' => ['label' => 'Videos', 'tab' => 'tab-videos'],
            'seo' => ['label' => 'SEO', 'tab' => 'tab-seo'],
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
    public static function validationRules(string $slug, ?int $projectId = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255'];
        if ($projectId) {
            $slugRule[] = 'unique:projects,slug,' . $projectId;
        } else {
            $slugRule[] = 'unique:projects,slug';
        }

        $common = [
            'upload_token' => ['nullable', 'string', 'max:64'],
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['nullable', 'string', 'max:500'],
            'gallery_order' => ['nullable', 'array'],
            'gallery_remove' => ['nullable', 'array'],
        ];

        $bySection = [
            'basics' => [
                'project_type_ids' => ['nullable', 'array'],
                'project_type_ids.*' => ['exists:project_types,id'],
                'title' => ['required', 'string', 'max:255'],
                'slug' => $slugRule,
                'price' => ['nullable', 'string', 'max:255'],
                'launch_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
                'description' => ['nullable', 'string'],
            ],
            'status' => [
                'status' => ['nullable', 'string', 'in:active,hold,inactive,close'],
            ],
            'address' => [
                'state' => ['nullable', 'string', 'max:255'],
                'city' => ['nullable', 'string', 'max:255'],
                'short_address' => ['nullable', 'string'],
                'full_address' => ['nullable', 'string'],
                'google_map' => ['nullable', 'string'],
                'latitude' => ['nullable', 'numeric', 'between:-90,90'],
                'longitude' => ['nullable', 'numeric', 'between:-180,180'],
                'address_image_path' => ['nullable', 'string', 'max:500'],
                'remove_address_image' => ['nullable', 'boolean'],
            ],
            'media' => [
                'logo_path' => ['nullable', 'string', 'max:500'],
                'featured_image_path' => ['nullable', 'string', 'max:500'],
                'homepage_listing_image_path' => ['nullable', 'string', 'max:500'],
                'project_file_pdf_path' => ['nullable', 'string', 'max:500'],
                'remove_logo' => ['nullable', 'boolean'],
                'remove_featured_image' => ['nullable', 'boolean'],
                'remove_homepage_listing_image' => ['nullable', 'boolean'],
                'remove_project_file_pdf' => ['nullable', 'boolean'],
                'hero_feature_titles' => ['nullable', 'array'],
                'hero_feature_icons' => ['nullable', 'array'],
                'hero_feature_colors' => ['nullable', 'array'],
                'hero_stat_labels' => ['nullable', 'array'],
                'hero_stat_values' => ['nullable', 'array'],
                'hero_stat_icons' => ['nullable', 'array'],
            ],
            'featured-video' => [
                'remove_featured_video' => ['nullable', 'boolean'],
                'featured_youtube_url' => ['nullable', 'string', 'max:2000'],
                'featured_video_title' => ['nullable', 'string', 'max:255'],
                'featured_video_description' => ['nullable', 'string'],
            ],
            'vr-tour' => [
                'vr_tour_url' => ['nullable', 'string', 'max:2000'],
                'vr_tour_image_path' => ['nullable', 'string', 'max:500'],
                'remove_vr_tour_image' => ['nullable', 'boolean'],
                'vr_tour_meta_title' => ['nullable', 'string', 'max:255'],
                'vr_tour_meta_description' => ['nullable', 'string', 'max:500'],
                'vr_tour_meta_keywords' => ['nullable', 'string', 'max:500'],
                'vr_tour_canonical_url' => ['nullable', 'string', 'max:500'],
            ],
            'booking-procedure' => [
                'booking_procedure_heading' => ['nullable', 'string', 'max:255'],
                'booking_procedure_content' => ['nullable', 'string'],
                'booking_procedure_documents_heading' => ['nullable', 'string', 'max:255'],
                'booking_step_titles' => ['nullable', 'array'],
                'booking_step_descriptions' => ['nullable', 'array'],
                'booking_document_labels' => ['nullable', 'array'],
                'booking_document_icons' => ['nullable', 'array'],
            ],
            'features' => [
                'feature_titles' => ['nullable', 'array'],
                'feature_icons' => ['nullable', 'array'],
            ],
            'pricing-place' => [
                'pricing_place_titles' => ['nullable', 'array'],
                'pricing_place_prices' => ['nullable', 'array'],
                'pricing_place_feature_1' => ['nullable', 'array'],
                'pricing_place_feature_2' => ['nullable', 'array'],
                'pricing_place_feature_3' => ['nullable', 'array'],
                'pricing_place_feature_4' => ['nullable', 'array'],
                'pricing_place_button_text' => ['nullable', 'array'],
                'pricing_place_is_popular' => ['nullable', 'array'],
                'existing_pricing_place_images' => ['nullable', 'array'],
            ],
            'price-slider' => [
                'price_slider_image_paths' => ['nullable', 'array'],
                'price_slider_image_paths.*' => ['nullable', 'string', 'max:500'],
            ],
            'social-proof' => [
                'testimonial_quotes' => ['nullable', 'array'],
                'testimonial_names' => ['nullable', 'array'],
                'testimonial_roles' => ['nullable', 'array'],
                'invest_title' => ['nullable', 'string', 'max:255'],
                'invest_points' => ['nullable', 'array'],
                'invest_image_path' => ['nullable', 'string', 'max:500'],
            ],
            'map-section' => [
                'map_section_heading' => ['nullable', 'string', 'max:255'],
                'map_section_tagline' => ['nullable', 'string', 'max:500'],
                'map_section_url' => ['nullable', 'string', 'max:2000'],
                'map_section_meta_title' => ['nullable', 'string', 'max:255'],
                'map_section_meta_description' => ['nullable', 'string', 'max:500'],
                'map_section_meta_keywords' => ['nullable', 'string', 'max:500'],
                'map_section_image_path' => ['nullable', 'string', 'max:500'],
                'remove_map_section_image' => ['nullable', 'boolean'],
            ],
            'detail-tabs' => [
                'detail_tab_labels' => ['nullable', 'array'],
                'detail_tab_labels.*' => ['nullable', 'string', 'max:255'],
                'detail_tab_icons' => ['nullable', 'array'],
                'detail_tab_icons.*' => ['nullable', 'string', 'max:64'],
                'detail_tab_headings' => ['nullable', 'array'],
                'detail_tab_headings.*' => ['nullable', 'string', 'max:255'],
                'detail_tab_details' => ['nullable', 'array'],
                'detail_tab_bullets' => ['nullable', 'array'],
                'detail_tab_image_paths' => ['nullable', 'array'],
                'detail_tab_image_paths.*' => ['nullable', 'array'],
                'detail_tab_image_paths.*.*' => ['nullable', 'string', 'max:500'],
            ],
            'tabs-follow-content' => [
                'tabs_follow_content' => ['nullable', 'string'],
            ],
            'videos' => [
                'video_urls' => ['nullable', 'array'],
            ],
            'gallery' => $common,
            'seo' => [
                'meta_title' => ['nullable', 'string', 'max:255'],
                'meta_description' => ['nullable', 'string', 'max:500'],
                'meta_keywords' => ['nullable', 'string', 'max:500'],
                'canonical_url' => ['nullable', 'string', 'max:500'],
            ],
        ];

        return array_merge($common, $bySection[$slug] ?? []);
    }
}
