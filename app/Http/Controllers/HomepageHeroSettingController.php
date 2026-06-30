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

        $validated = $request->validate([
            'hero_image_path' => ['nullable', 'string', 'max:500'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'hero_image_alt' => ['required', 'string', 'max:255'],
            'tagline' => ['required', 'string', 'max:500'],
            'heading_line_1' => ['required', 'string', 'max:255'],
            'heading_line_2' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'cta_text' => ['required', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'scroll_text' => ['required', 'string', 'max:255'],
        ]);

        $setting->fill(collect($validated)->except([
            'hero_image_path',
            'remove_hero_image',
        ])->all());

        $this->applyHomepageMediaPath($request, $setting, 'hero_image');

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_hero_updated', 'Homepage main hero section updated.');
        }

        return redirect()->route('admin.homepage-hero.edit')->with('status', 'Homepage hero saved.');
    }
}
