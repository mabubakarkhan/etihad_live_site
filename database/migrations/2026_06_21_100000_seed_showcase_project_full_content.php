<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('projects')) {
            return;
        }

        $seedFile = database_path('data/showcase_project_seed.php');
        if (! is_file($seedFile)) {
            return;
        }

        /** @var array<string, mixed> $seed */
        $seed = require $seedFile;
        $marker = (string) ($seed['seed_marker'] ?? 'etihad_showcase_seed_v1');
        $slug = (string) ($seed['slug'] ?? 'first');

        $project = Project::query()->where('slug', $slug)->first();
        if ($project && str_contains((string) ($project->meta_keywords ?? ''), $marker)) {
            return;
        }

        if (! $project) {
            $project = new Project([
                'title' => (string) ($seed['title'] ?? 'Etihad Town Phase 1'),
                'slug' => $slug,
                'status' => 'active',
                'sort_order' => 1,
            ]);
            $project->save();
        }

        $projectId = (int) $project->id;
        $basePath = 'projects/' . $projectId;

        /** @var array<string, mixed> $sources */
        $sources = is_array($seed['asset_sources'] ?? null) ? $seed['asset_sources'] : [];

        $featured = $this->copyAsset($sources['featured'] ?? null, $basePath . '/featured/showcase-featured.webp');
        $homepageListing = $this->copyAsset($sources['homepage_listing'] ?? null, $basePath . '/homepage/showcase-listing.webp');
        $logo = $this->copyAsset($sources['logo'] ?? null, $basePath . '/logo/showcase-logo.png');
        $addressImage = $this->copyAsset($sources['address'] ?? null, $basePath . '/address/showcase-address.webp');
        $mapImage = $this->copyAsset($sources['map_section'] ?? null, $basePath . '/map/showcase-map.webp');
        $vrImage = $this->copyAsset($sources['vr_tour'] ?? null, $basePath . '/vr-tour/showcase-vr.webp');
        $investImage = $this->copyAsset($sources['invest'] ?? null, $basePath . '/invest/showcase-invest.webp');

        $galleryPaths = [];
        foreach (array_values((array) ($sources['gallery'] ?? [])) as $idx => $source) {
            $path = $this->copyAsset($source, $basePath . '/gallery/showcase-' . ($idx + 1) . $this->extensionFromSource((string) $source));
            if ($path !== null) {
                $galleryPaths[] = ['path' => $path, 'order' => $idx];
            }
        }

        $pricingCards = is_array($seed['pricing_place_cards'] ?? null) ? $seed['pricing_place_cards'] : [];
        foreach (array_values((array) ($sources['pricing_place'] ?? [])) as $idx => $source) {
            if (! isset($pricingCards[$idx])) {
                break;
            }
            $path = $this->copyAsset($source, $basePath . '/pricing/showcase-card-' . ($idx + 1) . $this->extensionFromSource((string) $source));
            if ($path !== null) {
                $pricingCards[$idx]['image'] = $path;
            }
        }

        $priceSliderImages = [];
        foreach (array_values((array) ($sources['price_slider'] ?? [])) as $idx => $source) {
            $path = $this->copyAsset($source, $basePath . '/price-slider/showcase-' . ($idx + 1) . $this->extensionFromSource((string) $source));
            if ($path !== null) {
                $priceSliderImages[] = $path;
            }
        }

        $detailTabSources = array_values((array) ($sources['detail_tab_images'] ?? []));
        $detailTabs = [];
        foreach (is_array($seed['detail_tabs'] ?? null) ? $seed['detail_tabs'] : [] as $tabRow) {
            $images = [];
            foreach (array_values((array) ($tabRow['image_indexes'] ?? [])) as $imageIndex) {
                $source = $detailTabSources[(int) $imageIndex] ?? null;
                if ($source === null) {
                    continue;
                }
                $path = $this->copyAsset(
                    $source,
                    $basePath . '/detail-tabs/showcase-tab-' . count($detailTabs) . '-' . (count($images) + 1) . $this->extensionFromSource((string) $source)
                );
                if ($path !== null) {
                    $images[] = $path;
                }
            }
            $detailTabs[] = [
                'label' => (string) ($tabRow['label'] ?? ''),
                'icon' => (string) ($tabRow['icon'] ?? 'fa-circle-info'),
                'heading' => (string) ($tabRow['heading'] ?? ''),
                'detail' => (string) ($tabRow['detail'] ?? ''),
                'bullets' => (string) ($tabRow['bullets'] ?? ''),
                'images' => $images,
            ];
        }

        $attributes = is_array($seed['attributes'] ?? null) ? $seed['attributes'] : [];
        $metaKeywords = trim((string) ($attributes['meta_keywords'] ?? ''));
        if (! str_contains($metaKeywords, $marker)) {
            $metaKeywords = trim($metaKeywords . ($metaKeywords !== '' ? ', ' : '') . $marker);
        }

        $project->fill(array_merge($attributes, [
            'title' => (string) ($seed['title'] ?? $project->title),
            'slug' => $slug,
            'meta_keywords' => $metaKeywords,
            'featured_image' => $featured ?? $project->featured_image,
            'homepage_listing_image' => $homepageListing ?? $project->homepage_listing_image,
            'logo' => $logo ?? $project->logo,
            'address_image' => $addressImage ?? $project->address_image,
            'map_section_image' => $mapImage ?? $project->map_section_image,
            'vr_tour_image' => $vrImage ?? $project->vr_tour_image,
            'invest_image' => $investImage ?? $project->invest_image,
            'gallery' => $galleryPaths !== [] ? $galleryPaths : $project->gallery,
            'pricing_place_cards' => $pricingCards !== [] ? $pricingCards : $project->pricing_place_cards,
            'price_slider_images' => $priceSliderImages !== [] ? $priceSliderImages : $project->price_slider_images,
            'hero_feature_cards' => $seed['hero_feature_cards'] ?? $project->hero_feature_cards,
            'hero_stat_cards' => $seed['hero_stat_cards'] ?? $project->hero_stat_cards,
            'unique_features' => $seed['unique_features'] ?? $project->unique_features,
            'testimonial_items' => $seed['testimonial_items'] ?? $project->testimonial_items,
            'invest_points' => $seed['invest_points'] ?? $project->invest_points,
            'videos' => $seed['videos'] ?? $project->videos,
            'booking_procedure' => $seed['booking_procedure'] ?? $project->booking_procedure,
            'tabs_follow_content' => $seed['tabs_follow_content'] ?? $project->tabs_follow_content,
            'project_detail_tabs' => $detailTabs !== [] ? $detailTabs : $project->project_detail_tabs,
        ]));

        if (! $project->sort_order) {
            $project->sort_order = 1;
        }

        $project->save();

        $typeId = DB::table('project_types')->where('slug', 'residential')->value('id')
            ?? DB::table('project_types')->orderBy('id')->value('id');

        if ($typeId && Schema::hasTable('project_project_type')) {
            $exists = DB::table('project_project_type')
                ->where('project_id', $project->id)
                ->where('project_type_id', $typeId)
                ->exists();

            if (! $exists) {
                DB::table('project_project_type')->insert([
                    'project_id' => $project->id,
                    'project_type_id' => $typeId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Content seed; no automatic rollback.
    }

    private function copyAsset(mixed $publicRelativeSource, string $storageRelativeDest): ?string
    {
        $publicRelativeSource = trim((string) $publicRelativeSource);
        if ($publicRelativeSource === '') {
            return null;
        }

        $source = public_path(ltrim($publicRelativeSource, '/'));
        if (! is_file($source)) {
            return null;
        }

        $destRelative = ltrim(str_replace('\\', '/', $storageRelativeDest), '/');
        $dest = public_storage_path($destRelative);
        File::ensureDirectoryExists(dirname($dest));

        if (! is_file($dest)) {
            File::copy($source, $dest);
        }

        if (function_exists('public_storage_mirror_file')) {
            public_storage_mirror_file($destRelative);
        }

        return $destRelative;
    }

    private function extensionFromSource(string $source): string
    {
        $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));

        return $ext !== '' ? '.' . $ext : '.webp';
    }
};
