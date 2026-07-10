<?php

return [
    'max_zip_kb' => (int) env('PROJECT_BULK_MEDIA_MAX_ZIP_KB', 204800),
    'max_file_kb' => (int) env('PROJECT_BULK_MEDIA_MAX_FILE_KB', 20480),
    'cache_ttl_minutes' => 60,

    /** @var array<string, array{field: string, storage_dir: string, kind: string, label: string}> */
    'single_folders' => [
        'featured' => [
            'field' => 'featured_image',
            'storage_dir' => 'featured',
            'kind' => 'image',
            'label' => 'Featured image',
        ],
        'homepage' => [
            'field' => 'homepage_listing_image',
            'storage_dir' => 'homepage',
            'kind' => 'image',
            'label' => 'Homepage listing image',
        ],
        'pdf' => [
            'field' => 'project_file_pdf',
            'storage_dir' => 'pdf',
            'kind' => 'pdf',
            'label' => 'Brochure PDF',
        ],
        'vr-tour' => [
            'field' => 'vr_tour_image',
            'storage_dir' => 'vr-tour',
            'kind' => 'image',
            'label' => 'VR tour promo image',
        ],
        'invest' => [
            'field' => 'invest_image',
            'storage_dir' => 'invest',
            'kind' => 'image',
            'label' => 'Invest section image',
        ],
    ],

    /** @var array<string, array{field: string, storage_dir: string, kind: string, label: string}> */
    'multi_folders' => [
        'gallery' => [
            'field' => 'gallery',
            'storage_dir' => 'gallery',
            'kind' => 'image',
            'label' => 'Gallery',
        ],
        'price-slider' => [
            'field' => 'price_slider_images',
            'storage_dir' => 'price-slider',
            'kind' => 'image',
            'label' => 'Price slider',
        ],
    ],

    'pricing_place_dir' => 'pricing-place',
    'detail_tabs_dir' => 'detail-tabs',
];
