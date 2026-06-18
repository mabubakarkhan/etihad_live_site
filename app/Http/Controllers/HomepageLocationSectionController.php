<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesHomepageMediaPaths;
use App\Models\ActivityLog;
use App\Models\HomepageLocationSectionSetting;
use Illuminate\Http\Request;

class HomepageLocationSectionController extends Controller
{
    use HandlesHomepageMediaPaths;

    public function edit()
    {
        $setting = HomepageLocationSectionSetting::instance();

        return view('admin.homepage_location_section.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageLocationSectionSetting::instance();

        $request->validate([
            'map_background_image_path' => ['nullable', 'string', 'max:500'],
            'card_image_path' => ['nullable', 'string', 'max:500'],
            'pin_image_path' => ['nullable', 'string', 'max:500'],
            'remove_map_background_image' => ['nullable', 'boolean'],
            'remove_card_image' => ['nullable', 'boolean'],
            'remove_pin_image' => ['nullable', 'boolean'],
        ]);

        foreach ([
            'map_background_image' => 'remove_map_background_image',
            'card_image' => 'remove_card_image',
            'pin_image' => 'remove_pin_image',
        ] as $field => $removeFlag) {
            $this->applyHomepageMediaPath($request, $setting, $field, $removeFlag);
        }

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_location_section_updated', 'Homepage location section images updated.');
        }

        return redirect()->route('admin.homepage-location-section.edit')->with('status', 'Location section images saved. Office address and details load from Contact settings.');
    }
}
