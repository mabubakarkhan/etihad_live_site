<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DhaSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DhaSettingController extends Controller
{
    public function edit()
    {
        $dha = DhaSetting::instance();

        return view('admin.dha.edit', compact('dha'));
    }

    public function uploadMedia(Request $request): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'string', 'in:featured,cta_banner'],
            'file' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $folder = $request->input('type') === 'cta_banner' ? 'dha/settings/cta' : 'dha/settings/featured';
        $path = $request->file('file')->store($folder, 'public');
        $this->mirrorToPublicStorage($path);

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => asset('storage/' . ltrim($path, '/')),
            'message' => 'Image uploaded successfully.',
        ]);
    }

    public function update(Request $request)
    {
        $dha = DhaSetting::instance();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'heading' => ['nullable', 'string', 'max:255'],
            'hero_eyebrow' => ['nullable', 'string', 'max:120'],
            'hero_title_gold' => ['nullable', 'string', 'max:120'],
            'hero_title_white' => ['nullable', 'string', 'max:120'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_description' => ['nullable', 'string', 'max:2000'],
            'hero_btn_primary_label' => ['nullable', 'string', 'max:120'],
            'hero_btn_primary_url' => ['nullable', 'string', 'max:500'],
            'hero_btn_secondary_label' => ['nullable', 'string', 'max:120'],
            'hero_btn_secondary_url' => ['nullable', 'string', 'max:500'],
            'hero_stats' => ['nullable', 'array', 'max:5'],
            'hero_stats.*.icon' => ['nullable', 'string', 'max:80'],
            'hero_stats.*.value' => ['nullable', 'string', 'max:120'],
            'hero_stats.*.label' => ['nullable', 'string', 'max:120'],
            'phases_heading_eyebrow' => ['nullable', 'string', 'max:120'],
            'phases_heading_gold' => ['nullable', 'string', 'max:120'],
            'phases_heading_white' => ['nullable', 'string', 'max:120'],
            'view_all_label' => ['nullable', 'string', 'max:120'],
            'view_all_url' => ['nullable', 'string', 'max:500'],
            'why_choose_heading' => ['nullable', 'string', 'max:255'],
            'why_choose_items' => ['nullable', 'array', 'max:6'],
            'why_choose_items.*.icon' => ['nullable', 'string', 'max:80'],
            'why_choose_items.*.title' => ['nullable', 'string', 'max:120'],
            'why_choose_items.*.text' => ['nullable', 'string', 'max:500'],
            'lifestyle_eyebrow' => ['nullable', 'string', 'max:120'],
            'lifestyle_heading' => ['nullable', 'string', 'max:255'],
            'lifestyle_description' => ['nullable', 'string', 'max:2000'],
            'lifestyle_btn_label' => ['nullable', 'string', 'max:120'],
            'lifestyle_btn_url' => ['nullable', 'string', 'max:500'],
            'lifestyle_cards' => ['nullable', 'array', 'max:6'],
            'lifestyle_cards.*.label' => ['nullable', 'string', 'max:120'],
            'lifestyle_cards.*.image' => ['nullable', 'string', 'max:500'],
            'growth_heading' => ['nullable', 'string', 'max:255'],
            'growth_stats' => ['nullable', 'array', 'max:5'],
            'growth_stats.*.icon' => ['nullable', 'string', 'max:80'],
            'growth_stats.*.value' => ['nullable', 'string', 'max:120'],
            'growth_stats.*.label' => ['nullable', 'string', 'max:120'],
            'cta_banner_image_path' => ['nullable', 'string', 'max:500'],
            'remove_cta_banner_image' => ['nullable', 'boolean'],
            'cta_title_gold' => ['nullable', 'string', 'max:120'],
            'cta_title_white' => ['nullable', 'string', 'max:120'],
            'cta_description' => ['nullable', 'string', 'max:2000'],
            'cta_btn_primary_label' => ['nullable', 'string', 'max:120'],
            'cta_btn_primary_url' => ['nullable', 'string', 'max:500'],
            'cta_btn_secondary_label' => ['nullable', 'string', 'max:120'],
            'cta_btn_secondary_url' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'featured_image_path' => ['nullable', 'string', 'max:500'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $stats = [];
        foreach ($request->input('hero_stats', []) as $item) {
            if (! is_array($item)) {
                continue;
            }
            $value = trim((string) ($item['value'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            if ($value === '' && $label === '') {
                continue;
            }
            $stats[] = [
                'icon' => trim((string) ($item['icon'] ?? '')) ?: 'circle',
                'value' => $value,
                'label' => $label,
            ];
        }
        $validated['hero_stats'] = $stats ?: null;
        $validated['why_choose_items'] = $this->normalizeWhyChooseItems($request->input('why_choose_items', []));
        $validated['lifestyle_cards'] = $this->normalizeLifestyleCards($request->input('lifestyle_cards', []));
        $validated['growth_stats'] = $this->normalizeGrowthStats($request->input('growth_stats', []));

        $validated['slug'] = Str::slug($validated['slug'] ?? $validated['title']);
        $validated['status'] = $validated['status'] ?? DhaSetting::STATUS_ACTIVE;

        if ($request->boolean('remove_featured_image')) {
            if ($dha->featured_image) {
                $this->deleteStoredFile($dha->featured_image);
            }
            $validated['featured_image'] = null;
        } elseif ($request->filled('featured_image_path')) {
            $validated['featured_image'] = $request->input('featured_image_path');
        }

        if ($request->boolean('remove_cta_banner_image')) {
            if ($dha->cta_banner_image) {
                $this->deleteStoredFile($dha->cta_banner_image);
            }
            $validated['cta_banner_image'] = null;
        } elseif ($request->filled('cta_banner_image_path')) {
            $validated['cta_banner_image'] = $request->input('cta_banner_image_path');
        }

        unset(
            $validated['remove_featured_image'],
            $validated['featured_image_path'],
            $validated['remove_cta_banner_image'],
            $validated['cta_banner_image_path']
        );
        $dha->update($validated);

        if ($admin = admin_user()) {
            ActivityLog::record($admin, 'dha_settings_updated', 'DHA main page updated.');
        }

        return redirect()->route('admin.dha.edit')->with('status', 'DHA page saved.');
    }

    private function normalizeWhyChooseItems(array $items): ?array
    {
        $out = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $title = trim((string) ($item['title'] ?? ''));
            $text = trim((string) ($item['text'] ?? ''));
            if ($title === '' && $text === '') {
                continue;
            }
            $out[] = [
                'icon' => trim((string) ($item['icon'] ?? '')) ?: 'circle',
                'title' => $title,
                'text' => $text,
            ];
        }

        return $out ?: null;
    }

    private function normalizeLifestyleCards(array $items): ?array
    {
        $out = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            $image = trim((string) ($item['image'] ?? ''));
            if ($label === '' && $image === '') {
                continue;
            }
            $out[] = ['label' => $label, 'image' => $image];
        }

        return $out ?: null;
    }

    private function normalizeGrowthStats(array $items): ?array
    {
        $out = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $value = trim((string) ($item['value'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            if ($value === '' && $label === '') {
                continue;
            }
            $out[] = [
                'icon' => trim((string) ($item['icon'] ?? '')) ?: 'circle',
                'value' => $value,
                'label' => $label,
            ];
        }

        return $out ?: null;
    }

    private function deleteStoredFile(?string $path): void
    {
        if (!$path) {
            return;
        }
        Storage::disk('public')->delete($path);
        File::delete(public_path('storage/' . ltrim($path, '/')));
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
