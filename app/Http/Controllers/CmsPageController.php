<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CmsPageController extends Controller
{
    public function index()
    {
        $pages = CmsPage::orderBy('name', 'asc')->get();
        return view('admin.cms_pages.index', compact('pages'));
    }

    public function edit(CmsPage $cmsPage)
    {
        return view('admin.cms_pages.edit', compact('cmsPage'));
    }

    public function update(Request $request, CmsPage $cmsPage)
    {
        $validated = $request->validate([
            'heading' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'meta_robots' => ['nullable', 'string', 'max:120'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'twitter_card' => ['nullable', 'string', 'max:40'],
            'twitter_title' => ['nullable', 'string', 'max:255'],
            'twitter_description' => ['nullable', 'string', 'max:500'],
            'structured_data_json' => ['nullable', 'string'],
        ]);

        if ($request->boolean('remove_banner_image') && $cmsPage->banner_image) {
            Storage::disk('public')->delete($cmsPage->banner_image);
            $validated['banner_image'] = null;
        }

        if ($request->hasFile('banner_image')) {
            if ($cmsPage->banner_image) {
                Storage::disk('public')->delete($cmsPage->banner_image);
            }
            $path = $request->file('banner_image')->store('cms/banners', 'public');
            $validated['banner_image'] = $path;
        }

        if ($request->boolean('remove_og_image') && $cmsPage->og_image) {
            Storage::disk('public')->delete($cmsPage->og_image);
            $validated['og_image'] = null;
        }

        if ($request->hasFile('og_image')) {
            if ($cmsPage->og_image) {
                Storage::disk('public')->delete($cmsPage->og_image);
            }
            $validated['og_image'] = $request->file('og_image')->store('cms/seo', 'public');
        }

        if ($request->boolean('remove_twitter_image') && $cmsPage->twitter_image) {
            Storage::disk('public')->delete($cmsPage->twitter_image);
            $validated['twitter_image'] = null;
        }

        if ($request->hasFile('twitter_image')) {
            if ($cmsPage->twitter_image) {
                Storage::disk('public')->delete($cmsPage->twitter_image);
            }
            $validated['twitter_image'] = $request->file('twitter_image')->store('cms/seo', 'public');
        }

        $cmsPage->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'cms_page_updated', "CMS page updated: {$cmsPage->name} ({$cmsPage->slug}).");
        }

        return redirect()->route('admin.cms-pages.edit', $cmsPage)->with('status', 'Page updated.');
    }
}
