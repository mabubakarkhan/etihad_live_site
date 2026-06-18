<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesHomepageMediaPaths;
use App\Models\ActivityLog;
use App\Models\HomepageAboutSetting;
use Illuminate\Http\Request;

class HomepageAboutSettingController extends Controller
{
    use HandlesHomepageMediaPaths;

    public function edit()
    {
        $setting = HomepageAboutSetting::instance();

        return view('admin.homepage_about.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageAboutSetting::instance();

        $validated = $request->validate([
            'tagline_about' => ['required', 'string', 'max:255'],
            'tagline_vision' => ['required', 'string', 'max:255'],
            'heading_line_1' => ['required', 'string', 'max:255'],
            'heading_line_2' => ['required', 'string', 'max:255'],
            'media_caption_1' => ['required', 'string', 'max:1000'],
            'media_caption_2' => ['nullable', 'string', 'max:1000'],
            'about_para_1_lead' => ['required', 'string', 'max:2000'],
            'about_para_1_highlight' => ['required', 'string', 'max:2000'],
            'about_para_2_lead' => ['required', 'string', 'max:2000'],
            'about_para_2_highlight' => ['required', 'string', 'max:2000'],
            'vision_para_1_highlight' => ['required', 'string', 'max:500'],
            'vision_para_1_body' => ['required', 'string', 'max:2000'],
            'vision_para_2_lead' => ['required', 'string', 'max:500'],
            'vision_para_2_highlight' => ['required', 'string', 'max:500'],
            'vision_para_2_body' => ['required', 'string', 'max:2000'],
            'cta_text' => ['required', 'string', 'max:255'],
            'cta_url' => ['nullable', 'string', 'max:2048'],
            'affiliated_text' => ['required', 'string', 'max:255'],
            'affiliated_url' => ['nullable', 'string', 'max:2048'],
            'video_path' => ['nullable', 'string', 'max:500'],
            'center_image_path' => ['nullable', 'string', 'max:500'],
            'secondary_image_path' => ['nullable', 'string', 'max:500'],
            'remove_video' => ['nullable', 'boolean'],
            'remove_center_image' => ['nullable', 'boolean'],
            'remove_secondary_image' => ['nullable', 'boolean'],
        ]);

        $setting->fill(collect($validated)->except([
            'video_path',
            'center_image_path',
            'secondary_image_path',
            'remove_video',
            'remove_center_image',
            'remove_secondary_image',
        ])->all());

        $this->applyHomepageMediaPath($request, $setting, 'video', 'remove_video');

        foreach (['center_image' => 'remove_center_image', 'secondary_image' => 'remove_secondary_image'] as $column => $removeFlag) {
            $this->applyHomepageMediaPath($request, $setting, $column, $removeFlag);
        }

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_about_updated', 'Homepage About Etihad section updated.');
        }

        return redirect()->route('admin.homepage-about.edit')->with('status', 'About Etihad section saved.');
    }
}
