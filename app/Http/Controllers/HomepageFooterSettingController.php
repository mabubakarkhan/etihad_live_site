<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesHomepageMediaPaths;
use App\Models\ActivityLog;
use App\Models\HomepageFooterSetting;
use Illuminate\Http\Request;

class HomepageFooterSettingController extends Controller
{
    use HandlesHomepageMediaPaths;

    public function edit()
    {
        $setting = HomepageFooterSetting::instance();

        return view('admin.homepage_footer.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = HomepageFooterSetting::instance();

        $validated = $request->validate([
            'footer_image_path' => ['nullable', 'string', 'max:500'],
            'remove_footer_image' => ['nullable', 'boolean'],
            'footer_image_alt' => ['required', 'string', 'max:255'],
        ]);

        $setting->fill(collect($validated)->except([
            'footer_image_path',
            'remove_footer_image',
        ])->all());

        $this->applyHomepageMediaPath($request, $setting, 'footer_image');

        $setting->save();

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'homepage_footer_updated', 'Homepage footer image updated.');
        }

        return redirect()->route('admin.homepage-footer.edit')->with('status', 'Footer image saved.');
    }
}
