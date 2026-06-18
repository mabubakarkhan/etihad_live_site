<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageAboutSetting;
use Illuminate\Http\Request;

class HomepageAboutSettingController extends Controller
{
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
            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm', 'max:102400'],
            'center_image' => ['nullable', 'image', 'max:8192'],
            'secondary_image' => ['nullable', 'image', 'max:8192'],
            'remove_video' => ['nullable', 'boolean'],
            'remove_center_image' => ['nullable', 'boolean'],
            'remove_secondary_image' => ['nullable', 'boolean'],
        ]);

        $setting->fill(collect($validated)->except([
            'video',
            'center_image',
            'secondary_image',
            'remove_video',
            'remove_center_image',
            'remove_secondary_image',
        ])->all());

        if ($request->boolean('remove_video') && $setting->video) {
            public_storage_delete($setting->video);
            $setting->video = null;
        }

        if ($request->hasFile('video')) {
            if ($setting->video) {
                public_storage_delete($setting->video);
            }
            $setting->video = public_storage_store_upload($request->file('video'), 'homepage-about');
        }

        foreach (['center_image' => 'remove_center_image', 'secondary_image' => 'remove_secondary_image'] as $column => $removeFlag) {
            if ($request->boolean($removeFlag) && $setting->{$column}) {
                public_storage_delete($setting->{$column});
                $setting->{$column} = null;
            }

            if ($request->hasFile($column)) {
                if ($setting->{$column}) {
                    public_storage_delete($setting->{$column});
                }
                $setting->{$column} = public_storage_store_upload($request->file($column), 'homepage-about');
            }
        }

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_about_updated', 'Homepage About Etihad section updated.');
        }

        return redirect()->route('admin.homepage-about.edit')->with('status', 'About Etihad section saved.');
    }
}
