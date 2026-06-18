<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesHomepageMediaPaths;
use App\Models\ActivityLog;
use App\Models\HomepageVisionSetting;
use Illuminate\Http\Request;

class HomepageVisionSettingController extends Controller
{
    use HandlesHomepageMediaPaths;

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
            'ceo_image_path' => ['nullable', 'string', 'max:500'],
            'remove_ceo_image' => ['nullable', 'boolean'],
        ]);

        $setting->fill(collect($validated)->except(['ceo_image_path', 'remove_ceo_image'])->all());
        $this->applyHomepageMediaPath($request, $setting, 'ceo_image');

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_vision_updated', 'Homepage vision section updated.');
        }

        return redirect()->route('admin.homepage-vision.edit')->with('status', 'Vision section saved.');
    }
}
