<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesHomepageMediaPaths;
use App\Models\ActivityLog;
use App\Models\HomepageWhySetting;
use Illuminate\Http\Request;

class HomepageWhySettingController extends Controller
{
    use HandlesHomepageMediaPaths;

    public function edit()
    {
        $setting = HomepageWhySetting::instance();

        return view('admin.homepage_why.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageWhySetting::instance();

        $validated = $request->validate([
            'heading_line_1' => ['required', 'string', 'max:255'],
            'heading_line_2' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'scroll_label' => ['required', 'string', 'max:64'],
            'contemporary_heading' => ['required', 'string', 'max:255'],
            'image_left_path' => ['nullable', 'string', 'max:500'],
            'image_center_path' => ['nullable', 'string', 'max:500'],
            'image_right_path' => ['nullable', 'string', 'max:500'],
            'image_center_back_path' => ['nullable', 'string', 'max:500'],
            'remove_image_left' => ['nullable', 'boolean'],
            'remove_image_center' => ['nullable', 'boolean'],
            'remove_image_right' => ['nullable', 'boolean'],
            'remove_image_center_back' => ['nullable', 'boolean'],
        ]);

        $setting->fill(collect($validated)->except([
            'image_left_path',
            'image_center_path',
            'image_right_path',
            'image_center_back_path',
            'remove_image_left',
            'remove_image_center',
            'remove_image_right',
            'remove_image_center_back',
        ])->all());

        foreach ([
            'image_left' => 'remove_image_left',
            'image_center' => 'remove_image_center',
            'image_right' => 'remove_image_right',
            'image_center_back' => 'remove_image_center_back',
        ] as $column => $removeFlag) {
            $this->applyHomepageMediaPath($request, $setting, $column, $removeFlag);
        }

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_why_updated', 'Homepage Why Choose section updated.');
        }

        return redirect()->route('admin.homepage-why.edit')->with('status', 'Why Choose section saved.');
    }
}
