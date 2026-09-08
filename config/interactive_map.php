<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Interactive map owner types (admin URL segment => model class)
    |--------------------------------------------------------------------------
    */
    'owners' => [
        'projects' => \App\Models\Project::class,
        'dha-phases' => \App\Models\DhaPhase::class,
    ],

    'foreign_keys' => [
        'projects' => 'project_id',
        'dha-phases' => 'dha_phase_id',
    ],

    'storage_directory' => 'maps',

    /*
    |--------------------------------------------------------------------------
    | Places API key (admin interactive map search — server-side proxy only)
    |--------------------------------------------------------------------------
    | Set application restriction to "None" in Google Cloud; key is not used in browser.
    | Enable legacy "Places API" (not only Places API New) for autocomplete proxy.
    */
    'places_api_key' => env('INTERACTIVE_MAP_PLACES_API_KEY', env('GOOGLE_MAPS_API_KEY', '')),

    'defaults' => [
        // Default starter box near DHA Phase 1, Lahore
        'north' => 31.4857,
        'south' => 31.4677,
        'east' => 74.3951,
        'west' => 74.3731,
        'default_zoom' => 14,
        'min_zoom' => 10,
        'max_zoom' => 20,
        'overlay_opacity' => 0.85,
        'overlay_rotation' => 0,
        'overlay_visibility_zoom' => 14,
        'show_label_from_zoom' => 16,
        'is_active' => true,
    ],

];
