<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\PortalAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PortalAdController extends Controller
{
    public function edit()
    {
        $ads = PortalAd::query()
            ->whereIn('slug', ['properties', 'dealers'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->keyBy('slug');

        return view('admin.portal_ads.edit', compact('ads'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'properties_image' => ['nullable', 'image', 'max:8192'],
            'dealers_image' => ['nullable', 'image', 'max:8192'],
        ]);

        $ads = PortalAd::query()
            ->whereIn('slug', ['properties', 'dealers'])
            ->get()
            ->keyBy('slug');

        foreach (['properties', 'dealers'] as $slug) {
            $field = $slug . '_image';
            if (!$request->hasFile($field) || !isset($ads[$slug])) {
                continue;
            }

            $ad = $ads[$slug];
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
                File::delete(public_path('storage/' . ltrim($ad->image, '/')));
            }

            $storedPath = $request->file($field)->store('portal-ads', 'public');
            $this->mirrorToPublicStorage($storedPath);

            $ad->update([
                'image' => $storedPath,
            ]);
        }

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'portal_ads_updated', 'Portal ads images updated.');
        }

        return redirect()->route('admin.portal-ads.edit')->with('status', 'Portal ads updated.');
    }

    private function mirrorToPublicStorage(string $storedPath): void
    {
        $source = storage_path('app/public/' . ltrim($storedPath, '/'));
        $destination = public_path('storage/' . ltrim($storedPath, '/'));

        if (!File::exists($source)) {
            return;
        }

        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);
    }
}
