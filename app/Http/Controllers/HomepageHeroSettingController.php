<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageHeroSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageHeroSettingController extends Controller
{
    public function edit()
    {
        $setting = HomepageHeroSetting::instance();

        return view('admin.homepage_hero.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageHeroSetting::instance();

        $validated = $request->validate([
            'hero_image' => ['nullable', 'image', 'max:8192'],
            'remove_hero_image' => ['nullable', 'boolean'],
        ]);

        if ($request->boolean('remove_hero_image') && $setting->hero_image) {
            Storage::disk('public')->delete($setting->hero_image);
            $setting->hero_image = null;
        }

        if ($request->hasFile('hero_image')) {
            if ($setting->hero_image) {
                Storage::disk('public')->delete($setting->hero_image);
            }
            $setting->hero_image = $request->file('hero_image')->store('homepage-hero', 'public');
        }

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_hero_updated', 'Homepage main hero image updated.');
        }

        return redirect()->route('admin.homepage-hero.edit')->with('status', 'Homepage hero image saved.');
    }
}
