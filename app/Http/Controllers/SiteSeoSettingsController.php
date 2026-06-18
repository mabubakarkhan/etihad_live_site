<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\SiteSeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSeoSettingsController extends Controller
{
    public function edit()
    {
        $siteSeo = SiteSeoSetting::instance();

        return view('admin.site_seo_settings.edit', compact('siteSeo'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'google_analytics_id' => ['nullable', 'string', 'max:40'],
            'google_tag_manager_id' => ['nullable', 'string', 'max:40'],
            'google_ads_id' => ['nullable', 'string', 'max:40'],
            'facebook_pixel_id' => ['nullable', 'string', 'max:40'],
            'tiktok_pixel_id' => ['nullable', 'string', 'max:40'],
            'linkedin_partner_id' => ['nullable', 'string', 'max:40'],
            'hotjar_id' => ['nullable', 'string', 'max:40'],
            'google_site_verification' => ['nullable', 'string', 'max:120'],
            'bing_site_verification' => ['nullable', 'string', 'max:120'],
            'facebook_domain_verification' => ['nullable', 'string', 'max:120'],
            'custom_head_code' => ['nullable', 'string'],
            'custom_body_open_code' => ['nullable', 'string'],
            'custom_body_close_code' => ['nullable', 'string'],
        ]);

        $siteSeo = SiteSeoSetting::instance();

        if ($request->boolean('remove_default_og_image') && $siteSeo->default_og_image) {
            Storage::disk('public')->delete($siteSeo->default_og_image);
            $validated['default_og_image'] = null;
        }

        if ($request->hasFile('default_og_image')) {
            if ($siteSeo->default_og_image) {
                Storage::disk('public')->delete($siteSeo->default_og_image);
            }
            $validated['default_og_image'] = $request->file('default_og_image')->store('seo', 'public');
        }

        $siteSeo->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'site_seo_settings_updated', 'Site SEO & tracking settings updated.');
        }

        return redirect()->route('admin.site-seo-settings.edit')->with('status', 'SEO & tracking settings updated.');
    }
}
