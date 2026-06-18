<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Property;
use App\Support\PropertyEditSections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesPropertySectionUpdates
{
    protected function applyPropertySection(Request $request, Property $property, string $section, array $validated, int $dealerId): void
    {
        $uploadToken = $request->input('upload_token');

        switch ($section) {
            case 'basic':
                $data = array_intersect_key($validated, array_flip([
                    'title', 'slug', 'purpose', 'description', 'dealer_id',
                ]));
                $data['dealer_id'] = $dealerId;
                $rawSlug = trim((string) ($data['slug'] ?? ''));
                $data['slug'] = $rawSlug !== '' ? Str::slug($rawSlug) : Str::slug($data['title']);
                $base = $data['slug'];
                $i = 1;
                while (Property::where('slug', $data['slug'])->where('id', '!=', $property->id)->exists()) {
                    $data['slug'] = $base . '-' . $i++;
                }
                $property->update($data);
                $property->projectTypes()->sync($request->input('project_type_ids', []));
                return;

            case 'status':
                $property->update([
                    'status' => $validated['status'] ?? $property->status ?? 'active',
                    'is_hot' => $request->boolean('is_hot'),
                ]);
                return;

            case 'featured-image':
                $this->applyPropertyFeaturedImage($request, $property);
                return;

            case 'address':
                $property->update(array_merge(
                    array_intersect_key($validated, array_flip([
                        'state', 'city', 'address', 'short_address', 'town',
                        'latitude', 'longitude', 'google_map',
                    ])),
                    [
                        'dha_phase_id' => $request->boolean('is_dha_property') && $request->filled('dha_phase_id')
                            ? (int) $request->input('dha_phase_id')
                            : null,
                    ]
                ));
                return;

            case 'videos':
                $property->update([
                    'videos' => array_values(array_filter(array_map('trim', (array) $request->input('videos', [])))),
                ]);
                return;

            case 'gallery':
                $this->applyPropertyGallery($request, $property);
                return;

            case 'video-gallery':
                $property->update([
                    'video_gallery' => array_values(array_filter(array_map('trim', (array) $request->input('video_gallery', [])))),
                ]);
                return;

            case 'price':
                $property->update(array_intersect_key($validated, array_flip(['price_string', 'price_digits'])));
                return;

            case 'property-type':
                $data = array_intersect_key($validated, array_flip([
                    'property_type', 'bedrooms', 'bathrooms', 'garage', 'kitchen', 'area_marla', 'area_kanal',
                ]));
                $areaMarla = $request->input('area_marla');
                $areaKanal = $request->input('area_kanal');
                if ($areaMarla !== null && $areaMarla !== '') {
                    $marla = (float) $areaMarla;
                    $data['area_marla'] = $marla;
                    $data['area_kanal'] = round($marla / 20, 2);
                } elseif ($areaKanal !== null && $areaKanal !== '') {
                    $kanal = (float) $areaKanal;
                    $data['area_kanal'] = $kanal;
                    $data['area_marla'] = round($kanal * 20, 2);
                }
                $property->update($data);
                return;

            case 'features':
                $property->update([
                    'features' => array_values(array_filter(array_map('trim', (array) $request->input('features', [])))),
                    'location_accessibility' => array_values(array_filter(array_map('trim', (array) $request->input('location_accessibility', [])))),
                    'nearest_hospitals' => array_values(array_filter(array_map('trim', (array) $request->input('nearest_hospitals', [])))),
                    'nearest_markets' => array_values(array_filter(array_map('trim', (array) $request->input('nearest_markets', [])))),
                    'nearest_restaurants' => array_values(array_filter(array_map('trim', (array) $request->input('nearest_restaurants', [])))),
                ]);
                return;

            case 'seo':
                $property->update(array_intersect_key($validated, array_flip([
                    'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
                ])));
                return;

            case 'amenities':
                $property->update([
                    'amenities_description' => $validated['amenities_description'] ?? null,
                    'amenities' => $this->normalizeAmenities($request),
                ]);
                return;
        }
    }

    protected function validatePropertySection(Request $request, Property $property, string $section): array
    {
        return $request->validate(PropertyEditSections::validationRules($section, $property->id));
    }

    protected function applyPropertyFeaturedImage(Request $request, Property $property): void
    {
        $disk = 'public';
        $uploadToken = $request->input('upload_token');
        $updates = [];

        if ($request->boolean('remove_featured_image')) {
            if ($property->featured_image) {
                Storage::disk($disk)->delete($property->featured_image);
            }
            $updates['featured_image'] = null;
        } elseif ($request->filled('featured_image_path')) {
            $path = trim((string) $request->input('featured_image_path'));
            if ($this->isAllowedMediaPath($path, $property->id, $uploadToken)) {
                $finalPath = $this->finalizeMediaPath($path, $property->id, $uploadToken);
                if ($property->featured_image && $property->featured_image !== $finalPath) {
                    Storage::disk($disk)->delete($property->featured_image);
                }
                $updates['featured_image'] = $finalPath;
            }
        }

        if (!empty($updates)) {
            $property->update($updates);
        }
        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('properties/staging/' . $uploadToken);
        }
    }

    protected function applyPropertyGallery(Request $request, Property $property): void
    {
        $disk = 'public';
        $uploadToken = $request->input('upload_token');
        $paths = (array) $request->input('gallery_paths', []);
        $order = (array) $request->input('gallery_order', []);
        $remove = (array) $request->input('gallery_remove', []);
        $out = [];
        foreach ($paths as $i => $path) {
            $path = trim((string) $path);
            if ($path === '' || in_array($path, $remove, true)) {
                continue;
            }
            if (!$this->isAllowedMediaPath($path, $property->id, $uploadToken)) {
                continue;
            }
            $finalPath = $this->finalizeMediaPath($path, $property->id, $uploadToken);
            $out[] = ['path' => $finalPath, 'order' => isset($order[$i]) ? (int) $order[$i] : $i];
        }
        foreach ($remove as $path) {
            if ($path && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
        usort($out, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        $property->update(['gallery' => $out]);
        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('properties/staging/' . $uploadToken);
        }
    }
}
