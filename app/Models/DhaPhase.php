<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DhaPhase extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'title',
        'slug',
        'sort_order',
        'description',
        'hero_lead',
        'stat_location',
        'stat_total_area',
        'stat_total_plots',
        'stat_year_developed',
        'features_content',
        'market_insights',
        'contact_intro',
        'value_propositions',
        'attractions_heading',
        'attractions',
        'investment_reasons',
        'project_highlights',
        'help_bar_eyebrow',
        'help_bar_title',
        'help_bar_text',
        'featured_image',
        'card_image',
        'phase_pdf',
        'vr_tour_url',
        'show_map_button',
        'latitude',
        'longitude',
        'map_zoom',
        'google_map',
        'image_gallery',
        'video_gallery',
        'plot_maps',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'map_zoom' => 'integer',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'image_gallery' => 'array',
            'video_gallery' => 'array',
            'plot_maps' => 'array',
            'show_map_button' => 'boolean',
            'value_propositions' => 'array',
            'attractions' => 'array',
            'investment_reasons' => 'array',
            'project_highlights' => 'array',
        ];
    }

    /** @return list<array{icon: string, title: string, text: string}> */
    public function valuePropositions(): array
    {
        $items = $this->normalizeContentItems($this->value_propositions, self::defaultValuePropositions());

        return array_slice($items, 0, 4);
    }

    public function attractionsHeading(): string
    {
        return trim((string) ($this->attractions_heading ?: ''))
            ?: 'ATTRACTIONS NEAR ' . strtoupper($this->title);
    }

    /** @return list<array{icon: string, title: string, text: string, image?: string}> */
    public function attractions(): array
    {
        $items = $this->normalizeContentItems($this->attractions, self::defaultAttractions());

        return array_slice($items, 0, 6);
    }

    /** @return list<array{icon: string, title: string, text: string}> */
    public function investmentReasons(): array
    {
        $items = $this->normalizeContentItems($this->investment_reasons, self::defaultInvestmentReasons());

        return array_slice($items, 0, 6);
    }

    /** @return array<string, string> */
    public function projectHighlights(): array
    {
        $stored = is_array($this->project_highlights) ? $this->project_highlights : [];
        $defaults = self::defaultProjectHighlights($this);

        return array_merge($defaults, array_filter($stored, fn ($v) => $v !== null && $v !== ''));
    }

    /** @return array{eyebrow: string, title: string, text: string} */
    public function helpBar(): array
    {
        return [
            'eyebrow' => trim((string) ($this->help_bar_eyebrow ?: 'HAVE QUESTIONS?')),
            'title' => trim((string) ($this->help_bar_title ?: "We're Here to Help!")),
            'text' => trim((string) ($this->help_bar_text ?: 'Connect with our property experts for more details about ' . $this->title . '.')),
        ];
    }

    public function attractionImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return url('storage/' . ltrim($path, '/'));
    }

    /** @return list<array{url: string, alt: string}> */
    public function galleryImages(): array
    {
        $gallery = is_array($this->image_gallery) ? $this->image_gallery : [];
        $images = [];

        foreach ($gallery as $item) {
            $path = is_array($item) ? ($item['path'] ?? '') : $item;
            $path = trim((string) $path);
            if ($path === '') {
                continue;
            }
            $images[] = [
                'url' => url('storage/' . ltrim($path, '/')),
                'alt' => trim((string) (is_array($item) ? ($item['alt'] ?? '') : '')) ?: $this->title,
            ];
        }

        return $images;
    }

    public function hasGallery(): bool
    {
        return $this->galleryImages() !== [];
    }

    public function hasPhasePdf(): bool
    {
        return trim((string) ($this->phase_pdf ?? '')) !== '';
    }

    public function phasePdfUrl(): ?string
    {
        if (! $this->hasPhasePdf()) {
            return null;
        }

        return url('storage/' . ltrim((string) $this->phase_pdf, '/'));
    }

    public function hasVrTour(): bool
    {
        return trim((string) ($this->vr_tour_url ?? '')) !== '';
    }

    public function vrTourUrl(): ?string
    {
        $url = trim((string) ($this->vr_tour_url ?? ''));

        return $url !== '' ? $url : null;
    }

    public function vrTourPageUrl(): ?string
    {
        if (! $this->hasVrTour()) {
            return null;
        }

        return route('dha.phase.vr-tour', ['phase' => $this->slug]);
    }

    public function hasMapData(): bool
    {
        if ($this->latitude !== null && $this->longitude !== null) {
            return true;
        }

        if (trim((string) ($this->google_map ?? '')) !== '') {
            return true;
        }

        $plots = is_array($this->plot_maps) ? $this->plot_maps : [];

        foreach ($plots as $plot) {
            $path = is_array($plot) ? trim((string) ($plot['path'] ?? '')) : '';
            if ($path !== '') {
                return true;
            }
        }

        return false;
    }

    public function showMapButton(): bool
    {
        return (bool) $this->show_map_button && $this->hasMapData();
    }

    public function mapPageUrl(): string
    {
        return route('dha.phase.map', $this->slug);
    }

    public function mapEmbedUrl(): ?string
    {
        $raw = trim((string) ($this->google_map ?? ''));
        if ($raw !== '') {
            if (preg_match('/src=["\']([^"\']+)["\']/i', $raw, $matches)) {
                return $matches[1];
            }
            if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
                return $raw;
            }
        }

        if ($this->latitude !== null && $this->longitude !== null) {
            $zoom = (int) ($this->map_zoom ?: 14);

            return 'https://www.google.com/maps?q=' . $this->latitude . ',' . $this->longitude . '&z=' . $zoom . '&output=embed';
        }

        return null;
    }

    /** @return list<array{path: string, title: string, url: string}> */
    public function plotMapItems(): array
    {
        $plots = is_array($this->plot_maps) ? $this->plot_maps : [];
        $out = [];

        foreach ($plots as $plot) {
            if (! is_array($plot)) {
                continue;
            }
            $path = trim((string) ($plot['path'] ?? ''));
            if ($path === '') {
                continue;
            }
            $out[] = [
                'path' => $path,
                'title' => trim((string) ($plot['title'] ?? '')),
                'url' => url('storage/' . ltrim($path, '/')),
            ];
        }

        return $out;
    }

    /** @param mixed $stored @param list<array{icon: string, title: string, text: string}> $defaults */
    private function normalizeContentItems(mixed $stored, array $defaults): array
    {
        if (! is_array($stored) || $stored === []) {
            return $defaults;
        }

        $out = [];
        foreach ($defaults as $i => $default) {
            $row = is_array($stored[$i] ?? null) ? $stored[$i] : [];
            $title = trim((string) ($row['title'] ?? ''));
            $text = trim((string) ($row['text'] ?? ''));
            if ($title === '' && $text === '') {
                $out[] = $default;
                continue;
            }
            $out[] = [
                'icon' => trim((string) ($row['icon'] ?? $default['icon'])) ?: $default['icon'],
                'title' => $title ?: $default['title'],
                'text' => $text ?: $default['text'],
                'image' => trim((string) ($row['image'] ?? '')),
            ];
        }

        return $out;
    }

    /** @return list<array{icon: string, title: string, text: string}> */
    public static function defaultValuePropositions(): array
    {
        return [
            ['icon' => 'map-pin', 'title' => 'High Demand', 'text' => 'Most sought-after location in DHA'],
            ['icon' => 'shield-check', 'title' => 'Secure Investment', 'text' => 'Stable growth and high ROI'],
            ['icon' => 'gem', 'title' => 'Premium Lifestyle', 'text' => 'World class amenities and living'],
            ['icon' => 'navigation', 'title' => 'Excellent Connectivity', 'text' => 'Easy access to all major areas of Lahore'],
        ];
    }

    /** @return list<array{icon: string, title: string, text: string, image: string}> */
    public static function defaultAttractions(): array
    {
        return [
            ['icon' => 'map-pin', 'title' => 'Prime & Central Location', 'text' => 'Easy access to all major destinations in Lahore', 'image' => ''],
            ['icon' => 'trees', 'title' => 'Parks & Green Spaces', 'text' => 'Lush green parks and open areas', 'image' => ''],
            ['icon' => 'building-2', 'title' => 'Mosques', 'text' => 'Beautiful mosques within the community', 'image' => ''],
            ['icon' => 'store', 'title' => 'Commercial Hubs', 'text' => 'Nearby markets, malls & business centers', 'image' => ''],
            ['icon' => 'graduation-cap', 'title' => 'Top Educational Institutions', 'text' => 'Reputed schools & colleges in close proximity', 'image' => ''],
            ['icon' => 'shield', 'title' => 'Secure & Gated Community', 'text' => '24/7 security for a safe and peaceful living', 'image' => ''],
        ];
    }

    /** @return list<array{icon: string, title: string, text: string}> */
    public static function defaultInvestmentReasons(): array
    {
        return [
            ['icon' => 'map-pin', 'title' => 'Prime & Central Location', 'text' => 'Strategically located in the heart of Lahore'],
            ['icon' => 'trending-up', 'title' => 'High Rental Yield', 'text' => 'Excellent rental income potential'],
            ['icon' => 'layout-grid', 'title' => 'Developed Infrastructure', 'text' => 'Fully developed with modern utilities'],
            ['icon' => 'line-chart', 'title' => 'Strong Future Growth', 'text' => 'Consistent price appreciation over the years'],
            ['icon' => 'leaf', 'title' => 'Safe & Peaceful Environment', 'text' => 'Clean, green and secure surroundings'],
            ['icon' => 'users', 'title' => 'Trusted & Established Community', 'text' => 'A well-planned and established neighborhood'],
        ];
    }

    /** @return array<string, string> */
    public static function defaultProjectHighlights(self $phase): array
    {
        return [
            'tag_primary' => 'Commercial',
            'tag_secondary' => 'Sale',
            'location' => $phase->stat_location ?: 'Canal Bank Rd, DHA Phase 1, Lahore',
            'total_views' => $phase->stat_total_plots ?: '54,541+',
            'developed_year' => $phase->stat_year_developed ?: '2002',
            'register_title' => 'Register Interest',
            'register_text' => 'Get updates and alerts about this listing.',
            'register_url' => '#dha-contact',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'dha_phase_id');
    }

    public function projectTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProjectType::class, 'dha_phase_project_type')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFrontOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Square box image path; falls back to banner (featured_image) when unset. */
    public function resolveCardImagePath(): ?string
    {
        if (! empty($this->card_image)) {
            return $this->card_image;
        }

        return $this->featured_image ?: null;
    }

    public function cardImageUrl(): string
    {
        $path = $this->resolveCardImagePath();

        return $path
            ? url('storage/' . ltrim($path, '/'))
            : asset('theme/images/all/1.jpg');
    }

    public function cardPhaseNumber(): string
    {
        if ($this->sort_order) {
            return str_pad((string) $this->sort_order, 2, '0', STR_PAD_LEFT);
        }

        if (preg_match('/(\d+)/', (string) $this->title, $matches)) {
            return str_pad($matches[1], 2, '0', STR_PAD_LEFT);
        }

        return '01';
    }

    public function cardTagline(): string
    {
        $lead = trim((string) ($this->hero_lead ?? ''));
        if ($lead !== '') {
            return \Illuminate\Support\Str::limit($lead, 72);
        }

        $defaults = [
            'Prime & Iconic',
            'Luxury Redefined',
            'Modern Living',
            'Elite Community',
            'Premium Investment',
            'Urban Excellence',
            'Prime Location',
            'Secure Living',
            'Smart Infrastructure',
            'Exclusive Lifestyle',
            'High ROI Potential',
        ];

        $index = max(0, (int) $this->sort_order - 1);

        return $defaults[$index % count($defaults)];
    }

    public function bannerImageUrl(): string
    {
        return $this->heroVisualUrl();
    }

    /** Hero visual: featured banner, gallery fallback, then community masterplan placeholder. */
    public function heroVisualUrl(): string
    {
        if (! empty($this->featured_image)) {
            return url('storage/' . ltrim($this->featured_image, '/'));
        }

        $gallery = is_array($this->image_gallery) ? $this->image_gallery : [];
        foreach ($gallery as $item) {
            $path = is_array($item) ? ($item['path'] ?? '') : $item;
            if ($path) {
                return url('storage/' . ltrim($path, '/'));
            }
        }

        return asset('theme/images/bg/6.jpg');
    }

    /** @return array{gold: string, white: string} */
    public function heroTitleParts(): array
    {
        $title = trim((string) ($this->title ?? ''));
        if (preg_match('/^(DHA)\s+(.+)$/i', $title, $matches)) {
            return ['gold' => $matches[1], 'white' => $matches[2]];
        }

        return ['gold' => '', 'white' => $title];
    }

    public static function booted(): void
    {
        static::creating(function (DhaPhase $phase) {
            if (empty($phase->slug)) {
                $phase->slug = Str::slug($phase->title);
            }
            if (!$phase->sort_order) {
                $phase->sort_order = (int) static::max('sort_order') + 1;
            }
        });
    }
}
