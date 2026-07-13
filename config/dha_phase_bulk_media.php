<?php

return [
    'max_zip_kb' => (int) env('DHA_PHASE_BULK_MEDIA_MAX_ZIP_KB', 204800),
    'max_file_kb' => (int) env('DHA_PHASE_BULK_MEDIA_MAX_FILE_KB', 20480),
    'cache_ttl_minutes' => 60,

    /** @var array<string, array{field: string, storage_dir: string, kind: string, label: string}> */
    'single_folders' => [
        'featured' => [
            'field' => 'featured_image',
            'storage_dir' => 'featured',
            'kind' => 'image',
            'label' => 'Featured / banner image',
        ],
        'card' => [
            'field' => 'card_image',
            'storage_dir' => 'card',
            'kind' => 'image',
            'label' => 'Card image',
        ],
        'pdf' => [
            'field' => 'phase_pdf',
            'storage_dir' => 'pdf',
            'kind' => 'pdf',
            'label' => 'Phase brochure PDF',
        ],
    ],

    /** @var array<string, array{field: string, storage_dir: string, kind: string, label: string, format: string}> */
    'multi_folders' => [
        'gallery' => [
            'field' => 'image_gallery',
            'storage_dir' => 'gallery',
            'kind' => 'image',
            'label' => 'Image gallery',
            'format' => 'gallery',
        ],
        'plot-maps' => [
            'field' => 'plot_maps',
            'storage_dir' => 'plot-maps',
            'kind' => 'image',
            'label' => 'Plot maps',
            'format' => 'plot_maps',
        ],
    ],
];
