<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

/**
 * Ensures showcase project media exists when the content seed ran before copy used the wrong storage root.
 */
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
        $slug = (string) ($seed['slug'] ?? 'first');
        $marker = (string) ($seed['seed_marker'] ?? 'etihad_showcase_seed_v1');

        $project = Project::query()->where('slug', $slug)->first();
        if (! $project || ! str_contains((string) ($project->meta_keywords ?? ''), $marker)) {
            return;
        }

        $featuredPath = (string) ($project->featured_image ?? '');
        if ($featuredPath !== '' && public_storage_exists($featuredPath)) {
            return;
        }

        $basePath = 'projects/' . $project->id;
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

        $pricingCards = is_array($project->pricing_place_cards) ? $project->pricing_place_cards : [];
        foreach (array_values((array) ($sources['pricing_place'] ?? [])) as $idx => $source) {
            if (! isset($pricingCards[$idx]) || ! is_array($pricingCards[$idx])) {
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
        $detailTabs = is_array($project->project_detail_tabs) ? $project->project_detail_tabs : [];
        foreach ($detailTabs as $tabIndex => $tabRow) {
            if (! is_array($tabRow)) {
                continue;
            }
            $seedTab = is_array($seed['detail_tabs'][$tabIndex] ?? null) ? $seed['detail_tabs'][$tabIndex] : [];
            $images = [];
            foreach (array_values((array) ($seedTab['image_indexes'] ?? [])) as $imageIndex) {
                $source = $detailTabSources[(int) $imageIndex] ?? null;
                if ($source === null) {
                    continue;
                }
                $path = $this->copyAsset(
                    $source,
                    $basePath . '/detail-tabs/showcase-tab-' . $tabIndex . '-' . (count($images) + 1) . $this->extensionFromSource((string) $source)
                );
                if ($path !== null) {
                    $images[] = $path;
                }
            }
            $detailTabs[$tabIndex]['images'] = $images;
        }

        if ($featured !== null) {
            $project->featured_image = $featured;
        }
        if ($homepageListing !== null) {
            $project->homepage_listing_image = $homepageListing;
        }
        if ($logo !== null) {
            $project->logo = $logo;
        }
        if ($addressImage !== null) {
            $project->address_image = $addressImage;
        }
        if ($mapImage !== null) {
            $project->map_section_image = $mapImage;
        }
        if ($vrImage !== null) {
            $project->vr_tour_image = $vrImage;
        }
        if ($investImage !== null) {
            $project->invest_image = $investImage;
        }
        if ($galleryPaths !== []) {
            $project->gallery = $galleryPaths;
        }
        if ($pricingCards !== []) {
            $project->pricing_place_cards = $pricingCards;
        }
        if ($priceSliderImages !== []) {
            $project->price_slider_images = $priceSliderImages;
        }
        if ($detailTabs !== []) {
            $project->project_detail_tabs = $detailTabs;
        }

        $project->save();
    }

    public function down(): void
    {
        // Media refresh; no automatic rollback.
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

        public_storage_mirror_file($destRelative);

        return $destRelative;
    }

    private function extensionFromSource(string $source): string
    {
        $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));

        return $ext !== '' ? '.' . $ext : '.webp';
    }
};
