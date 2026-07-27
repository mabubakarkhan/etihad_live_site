<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Prototype Map Overlay — isolated POC configuration
  |--------------------------------------------------------------------------
  |
  | This config is scoped to the prototype module only. It must not be
  | referenced by production Project or DHA Phase modules.
  |
  */

    'storage_directory' => 'prototype/maps',

    'max_upload_kb' => 204800, // 200 MB — supports large / 8K PNG overlays

    'allowed_mimes' => ['image/png'],

    'route_prefix' => 'prototype',

    'defaults' => [
        'north' => 31.5450,
        'south' => 31.4950,
        'east' => 74.3900,
        'west' => 74.3400,
        'default_zoom' => 15,
        'min_zoom' => 10,
        'max_zoom' => 20,
        'overlay_opacity' => 0.85,
        'overlay_rotation' => 0,
        'show_overlay_from_zoom' => null,
        'status' => 'draft',
    ],

    /*
    | Paths excluded from sitemap generation (when a sitemap is added later).
    */
    'sitemap_excluded_paths' => [
        '/prototype',
        '/prototype/*',
        '/admin/prototype/*',
    ],

];
