<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\HomepageVisionSetting;
use Illuminate\Http\Request;

class HomepageVisionSettingController extends Controller
{
    public function edit()
    {
        $setting = HomepageVisionSetting::instance();

        return view('admin.homepage_vision.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageVisionSetting::instance();

        $validated = $request->validate([
            'tagline' => ['required', 'string', 'max:255'],
            'heading_line_1' => ['required', 'string', 'max:255'],
            'heading_line_2' => ['required', 'string', 'max:255'],
            'message_paragraph_1' => ['required', 'string', 'max:5000'],
            'message_paragraph_2_highlight' => ['nullable', 'string', 'max:500'],
            'message_paragraph_2_body' => ['required', 'string', 'max:5000'],
            'ceo_name' => ['required', 'string', 'max:255'],
            'ceo_title' => ['required', 'string', 'max:255'],
            'ceo_image' => ['nullable', 'image', 'max:8192'],
            'remove_ceo_image' => ['nullable', 'boolean'],
        ]);

        $setting->fill(collect($validated)->except(['ceo_image', 'remove_ceo_image'])->all());

        if ($request->boolean('remove_ceo_image') && $setting->ceo_image) {
            public_storage_delete($setting->ceo_image);
            $setting->ceo_image = null;
        }

        if ($request->hasFile('ceo_image')) {
            if ($setting->ceo_image) {
                public_storage_delete($setting->ceo_image);
            }
            $setting->ceo_image = public_storage_store_upload($request->file('ceo_image'), 'homepage-vision');
        }

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_vision_updated', 'Homepage vision section updated.');
        }

        return redirect()->route('admin.homepage-vision.edit')->with('status', 'Vision section saved.');
    }
}
