<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesHomepageMediaPaths;
use App\Models\ActivityLog;
use App\Models\HomepageHeroSetting;
use Illuminate\Http\Request;

class HomepageHeroSettingController extends Controller
{
    use HandlesHomepageMediaPaths;

    public function edit()
    {
        $setting = HomepageHeroSetting::instance();

        return view('admin.homepage_hero.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageHeroSetting::instance();

        $request->validate([
            'hero_image_path' => ['nullable', 'string', 'max:500'],
            'remove_hero_image' => ['nullable', 'boolean'],
        ]);

        $this->applyHomepageMediaPath($request, $setting, 'hero_image');

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_hero_updated', 'Homepage main hero image updated.');
        }

        return redirect()->route('admin.homepage-hero.edit')->with('status', 'Homepage hero image saved.');
    }
}
