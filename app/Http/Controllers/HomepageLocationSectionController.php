<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageLocationSectionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageLocationSectionController extends Controller
{
    public function edit()
    {
        $setting = HomepageLocationSectionSetting::instance();

        return view('admin.homepage_location_section.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageLocationSectionSetting::instance();

        $request->validate([
            'map_background_image' => ['nullable', 'image', 'max:8192'],
            'card_image' => ['nullable', 'image', 'max:8192'],
            'pin_image' => ['nullable', 'image', 'max:4096'],
            'remove_map_background_image' => ['nullable', 'boolean'],
            'remove_card_image' => ['nullable', 'boolean'],
            'remove_pin_image' => ['nullable', 'boolean'],
        ]);

        foreach ([
            'map_background_image' => 'remove_map_background_image',
            'card_image' => 'remove_card_image',
            'pin_image' => 'remove_pin_image',
        ] as $field => $removeFlag) {
            if ($request->boolean($removeFlag) && $setting->{$field}) {
                Storage::disk('public')->delete($setting->{$field});
                $setting->{$field} = null;
            }

            if ($request->hasFile($field)) {
                if ($setting->{$field}) {
                    Storage::disk('public')->delete($setting->{$field});
                }
                $setting->{$field} = $request->file($field)->store('homepage-location', 'public');
            }
        }

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_location_section_updated', 'Homepage location section images updated.');
        }

        return redirect()->route('admin.homepage-location-section.edit')->with('status', 'Location section images saved. Office address and details load from Contact settings.');
    }
}
