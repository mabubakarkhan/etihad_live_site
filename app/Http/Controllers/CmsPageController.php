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

        $cmsPage->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'cms_page_updated', "CMS page updated: {$cmsPage->name} ({$cmsPage->slug}).");
        }

        return redirect()->route('admin.cms-pages.edit', $cmsPage)->with('status', 'Page updated.');
    }
}
