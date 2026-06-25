<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Project;
use App\Support\ProjectEditSections;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesProjectSectionUpdates
{
    protected function applyProjectSection(Request $request, Project $project, string $section, array $validated): void
    {
        $uploadToken = $request->input('upload_token');
        $updates = [];

        switch ($section) {
            case 'basics':
                $updates = array_intersect_key($validated, array_flip(['title', 'slug', 'price', 'description']));
                $updates['slug'] = $this->uniqueProjectSlug(
                    trim((string) ($updates['slug'] ?? '')) !== '' ? (string) $updates['slug'] : (string) $updates['title'],
                    $project->id
                );
                $project->update($updates);
                $project->projectTypes()->sync($request->input('project_type_ids', []));
                return;

            case 'status':
                $project->update(['status' => $validated['status'] ?? $project->status ?? 'active']);
                return;

            case 'address':
                $updates = array_intersect_key($validated, array_flip([
                    'state', 'city', 'short_address', 'full_address', 'google_map', 'latitude', 'longitude',
                ]));
                $updates = array_merge($updates, $this->processProjectSingleMedia($request, $project, [
                    'address_image_path' => 'address_image',
                ], ['remove_address_image' => 'address_image'], $uploadToken));
                $project->update($updates);
                return;

            case 'media':
                $updates = $this->processProjectSingleMedia($request, $project, [
                    'logo_path' => 'logo',
                    'featured_image_path' => 'featured_image',
                    'homepage_listing_image_path' => 'homepage_listing_image',
                    'project_file_pdf_path' => 'project_file_pdf',
                ], [
                    'remove_logo' => 'logo',
                    'remove_featured_image' => 'featured_image',
                    'remove_homepage_listing_image' => 'homepage_listing_image',
                    'remove_project_file_pdf' => 'project_file_pdf',
                ], $uploadToken);
                $updates['hero_feature_cards'] = $this->normalizeHeroFeatureCards($request);
                $updates['hero_stat_cards'] = $this->normalizeHeroStatCards($request);
                $project->update($updates);
                return;

            case 'featured-video':
                if ($request->boolean('remove_featured_video')) {
                    $project->update([
                        'featured_youtube_url' => null,
                        'featured_video_title' => null,
                        'featured_video_description' => null,
                    ]);
                    return;
                }
                $project->update(array_intersect_key($validated, array_flip([
                    'featured_youtube_url', 'featured_video_title', 'featured_video_description',
                ])));
                return;

            case 'vr-tour':
                $updates = array_intersect_key($validated, array_flip([
                    'vr_tour_url', 'vr_tour_meta_title', 'vr_tour_meta_description',
                    'vr_tour_meta_keywords', 'vr_tour_canonical_url',
                ]));
                $updates = array_merge($updates, $this->processProjectSingleMedia($request, $project, [
                    'vr_tour_image_path' => 'vr_tour_image',
                ], [
                    'remove_vr_tour_image' => 'vr_tour_image',
                ], $uploadToken));
                $project->update($updates);
                return;

            case 'booking-procedure':
                $project->update(['booking_procedure' => $this->normalizeBookingProcedure($request)]);
                return;

            case 'about':
                $updates = ['about_developers' => $validated['about_developers'] ?? null];
                $updates = array_merge($updates, $this->processProjectSingleMedia($request, $project, [
                    'developer_logo_path' => 'developer_logo',
                ], ['remove_developer_logo' => 'developer_logo'], $uploadToken));
                $project->update($updates);
                return;

            case 'pdf':
                $project->update($this->processProjectSingleMedia($request, $project, [
                    'project_file_pdf_path' => 'project_file_pdf',
                ], ['remove_project_file_pdf' => 'project_file_pdf'], $uploadToken));
                return;

            case 'noc':
                $updates = ['noc_planning_content' => $validated['noc_planning_content'] ?? null];
                $updates = array_merge($updates, $this->processProjectSingleMedia($request, $project, [
                    'noc_planning_image_path' => 'noc_planning_image',
                ], ['remove_noc_planning_image' => 'noc_planning_image'], $uploadToken));
                $project->update($updates);
                return;

            case 'future-note':
                $project->update(array_intersect_key($validated, array_flip(['future_note_title', 'future_note_content'])));
                return;

            case 'extra':
                $project->update(array_intersect_key($validated, array_flip(['extra_section_title', 'extra_section_content'])));
                return;

            case 'features':
                $project->update(['unique_features' => $this->normalizeUniqueFeatures($request)]);
                return;

            case 'price-plan':
                $project->update([
                    'price_plan_section_title' => $validated['price_plan_section_title'] ?? null,
                    'price_plan_items' => array_values(array_filter((array) $request->input('price_plan_items', []))),
                ]);
                return;

            case 'faqs':
                $project->update(['faqs' => $this->normalizeFaqs($request)]);
                return;

            case 'plans':
                $project->update($this->processProjectPlansOnly($request, $project, $uploadToken));
                return;

            case 'pricing-place':
                $project->update($this->processProjectPricingOnly($request, $project, $uploadToken));
                return;

            case 'price-slider':
                $project->update(['price_slider_images' => $this->normalizePriceSliderImages($request)]);
                return;

            case 'social-proof':
                $updates = [
                    'testimonial_items' => $this->normalizeTestimonialItems($request),
                    'invest_title' => $validated['invest_title'] ?? null,
                    'invest_points' => $this->normalizeInvestPoints($request),
                ];
                $updates = array_merge($updates, $this->processProjectSingleMedia($request, $project, [
                    'invest_image_path' => 'invest_image',
                ], [], $uploadToken));
                $project->update($updates);
                return;

            case 'map-section':
                $updates = array_intersect_key($validated, array_flip([
                    'map_section_heading',
                    'map_section_tagline',
                    'map_section_url',
                    'map_section_meta_title',
                    'map_section_meta_description',
                    'map_section_meta_keywords',
                ]));
                $updates = array_merge($updates, $this->processProjectSingleMedia($request, $project, [
                    'map_section_image_path' => 'map_section_image',
                ], [
                    'remove_map_section_image' => 'map_section_image',
                ], $uploadToken));
                $project->update($updates);
                return;

            case 'detail-tabs':
                $project->update(['project_detail_tabs' => $this->normalizeDetailTabs($request, $project)]);
                return;

            case 'tabs-follow-content':
                $project->update(array_intersect_key($validated, array_flip([
                    'tabs_follow_content',
                ])));
                return;

            case 'title-desc':
                $project->update(['title_descriptions' => $this->normalizeTitleDescriptions($request)]);
                return;

            case 'videos':
                $project->update(['videos' => $this->normalizeVideos($request)]);
                return;

            case 'gallery':
                $project->update($this->processProjectGalleryOnly($request, $project, $uploadToken));
                return;

            case 'seo':
                $project->update(array_intersect_key($validated, array_flip([
                    'meta_title', 'meta_description', 'meta_keywords', 'canonical_url',
                ])));
                return;
        }
    }

    /** @param array<string, string> $pathMap @param array<string, string> $removeMap */
    protected function processProjectSingleMedia(
        Request $request,
        Project $project,
        array $pathMap,
        array $removeMap,
        ?string $uploadToken
    ): array {
        $disk = 'public';
        $updates = [];

        foreach ($removeMap as $removeKey => $fieldKey) {
            if ($request->boolean($removeKey)) {
                $path = $project->{$fieldKey};
                if ($path) {
                    Storage::disk($disk)->delete($path);
                }
                $updates[$fieldKey] = null;
            }
        }

        foreach ($pathMap as $pathKey => $fieldKey) {
            if (array_key_exists($fieldKey, $updates) && $updates[$fieldKey] === null) {
                continue;
            }
            if (!$request->filled($pathKey)) {
                continue;
            }
            $path = trim((string) $request->input($pathKey));
            if ($path === '' || !$this->isAllowedProjectMediaPath($path, $project->id, $uploadToken)) {
                continue;
            }
            $finalPath = $this->finalizeProjectMediaPath($path, $project->id, $uploadToken);
            if ($project->{$fieldKey} && $project->{$fieldKey} !== $finalPath) {
                Storage::disk($disk)->delete($project->{$fieldKey});
            }
            $updates[$fieldKey] = $finalPath;
        }

        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('projects/staging/' . $uploadToken);
        }

        return $updates;
    }

    protected function processProjectPlansOnly(Request $request, Project $project, ?string $uploadToken): array
    {
        $disk = 'public';
        $planTitles = (array) $request->input('plan_titles', []);
        $existingPlanImages = (array) $request->input('existing_plan_images', []);
        $currentPlans = $project->plans ?? [];
        $plans = [];
        foreach ($planTitles as $i => $title) {
            $title = trim($title ?? '');
            $imagePath = trim((string) ($existingPlanImages[$i] ?? $currentPlans[$i]['image'] ?? ''));
            if ($imagePath !== '' && $this->isAllowedProjectMediaPath($imagePath, $project->id, $uploadToken)) {
                $imagePath = $this->finalizeProjectMediaPath($imagePath, $project->id, $uploadToken);
            } elseif ($imagePath !== '' && !str_starts_with($imagePath, 'projects/' . $project->id . '/')) {
                $imagePath = trim((string) ($currentPlans[$i]['image'] ?? ''));
            }
            $plans[] = ['title' => $title, 'image' => $imagePath];
        }
        $newPlans = array_values(array_filter($plans, fn ($p) => $p['title'] !== '' || $p['image'] !== ''));
        $this->deleteOrphanedStorageFiles(
            array_filter(array_column($currentPlans, 'image')),
            array_filter(array_column($newPlans, 'image')),
            $disk
        );
        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('projects/staging/' . $uploadToken);
        }
        return ['plans' => $newPlans];
    }

    protected function processProjectPricingOnly(Request $request, Project $project, ?string $uploadToken): array
    {
        $disk = 'public';
        $data = $this->buildProjectData($request, [], $project);
        $pricingCards = $data['pricing_place_cards'] ?? [];
        $currentPricing = is_array($project->pricing_place_cards) ? $project->pricing_place_cards : [];

        foreach ($pricingCards as $i => $card) {
            $imagePath = trim((string) ($card['image'] ?? ''));
            if ($imagePath !== '' && $this->isAllowedProjectMediaPath($imagePath, $project->id, $uploadToken)) {
                $pricingCards[$i]['image'] = $this->finalizeProjectMediaPath($imagePath, $project->id, $uploadToken);
            } elseif ($imagePath !== '' && !str_starts_with($imagePath, 'projects/' . $project->id . '/')) {
                $pricingCards[$i]['image'] = trim((string) ($currentPricing[$i]['image'] ?? ''));
            }
        }

        $this->deleteOrphanedStorageFiles(
            array_filter(array_column($currentPricing, 'image')),
            array_filter(array_column($pricingCards, 'image')),
            $disk
        );
        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('projects/staging/' . $uploadToken);
        }
        return ['pricing_place_cards' => $pricingCards];
    }

    protected function processProjectGalleryOnly(Request $request, Project $project, ?string $uploadToken): array
    {
        $disk = 'public';
        $galleryRemove = (array) $request->input('gallery_remove', []);
        $galleryPaths = (array) $request->input('gallery_paths', []);
        $galleryOrder = (array) $request->input('gallery_order', []);
        $gallery = [];
        foreach ($galleryPaths as $i => $path) {
            $path = trim((string) $path);
            if ($path === '' || in_array($path, $galleryRemove, true)) {
                continue;
            }
            if (!$this->isAllowedProjectMediaPath($path, $project->id, $uploadToken)) {
                continue;
            }
            $finalPath = $this->finalizeProjectMediaPath($path, $project->id, $uploadToken);
            $order = isset($galleryOrder[$i]) ? (int) $galleryOrder[$i] : $i;
            $gallery[] = ['path' => $finalPath, 'order' => $order];
        }
        foreach ($galleryRemove as $path) {
            if ($path && Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
        usort($gallery, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        if ($uploadToken) {
            Storage::disk($disk)->deleteDirectory('projects/staging/' . $uploadToken);
        }
        return ['gallery' => $gallery];
    }

    /** @return list<array{title: string, icon: string, color: string}> */
    protected function normalizeHeroFeatureCards(Request $request): array
    {
        $titles = (array) $request->input('hero_feature_titles', []);
        $icons = (array) $request->input('hero_feature_icons', []);
        $colors = (array) $request->input('hero_feature_colors', []);
        $allowedColors = ['green', 'purple', 'orange', 'blue'];
        $cards = [];

        foreach ($titles as $i => $title) {
            $title = trim((string) $title);
            if ($title === '') {
                continue;
            }
            $icon = trim((string) ($icons[$i] ?? 'fa-star'));
            $color = trim((string) ($colors[$i] ?? 'green'));
            if (! in_array($color, $allowedColors, true)) {
                $color = 'green';
            }
            $cards[] = [
                'title' => $title,
                'icon' => $icon !== '' ? $icon : 'fa-star',
                'color' => $color,
            ];
            if (count($cards) >= 4) {
                break;
            }
        }

        return $cards;
    }

    /** @return list<array{label: string, value: string, icon: string}> */
    protected function normalizeHeroStatCards(Request $request): array
    {
        $labels = (array) $request->input('hero_stat_labels', []);
        $values = (array) $request->input('hero_stat_values', []);
        $icons = (array) $request->input('hero_stat_icons', []);
        $cards = [];

        foreach ($labels as $i => $label) {
            $label = trim((string) $label);
            $value = trim((string) ($values[$i] ?? ''));
            if ($label === '' && $value === '') {
                continue;
            }
            $icon = trim((string) ($icons[$i] ?? 'fa-circle-info'));
            $cards[] = [
                'label' => $label !== '' ? $label : 'Label',
                'value' => $value !== '' ? $value : '—',
                'icon' => $icon !== '' ? $icon : 'fa-circle-info',
            ];
            if (count($cards) >= 4) {
                break;
            }
        }

        return $cards;
    }

    protected function validateProjectSection(Request $request, Project $project, string $section): array
    {
        return $request->validate(ProjectEditSections::validationRules($section, $project->id));
    }
}
