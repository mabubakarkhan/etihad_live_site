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
    'places_api_key' => env('INTERACTIVE_MAP_PLACES_API_KEY', ''),

    'defaults' => [
        'north' => 31.5300,
        'south' => 31.5100,
        'east' => 74.3700,
        'west' => 74.3400,
        'default_zoom' => 15,
        'min_zoom' => 10,
        'max_zoom' => 20,
        'overlay_opacity' => 0.85,
        'overlay_rotation' => 0,
        'overlay_visibility_zoom' => 14,
        'is_active' => true,
    ],

];
