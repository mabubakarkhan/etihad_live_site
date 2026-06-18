<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DhaSetting extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'title',
        'slug',
        'heading',
        'hero_eyebrow',
        'hero_title_gold',
        'hero_title_white',
        'hero_subtitle',
        'hero_description',
        'hero_btn_primary_label',
        'hero_btn_primary_url',
        'hero_btn_secondary_label',
        'hero_btn_secondary_url',
        'hero_stats',
        'phases_heading_eyebrow',
        'phases_heading_gold',
        'phases_heading_white',
        'view_all_label',
        'view_all_url',
        'why_choose_heading',
        'why_choose_items',
        'lifestyle_eyebrow',
        'lifestyle_heading',
        'lifestyle_description',
        'lifestyle_btn_label',
        'lifestyle_btn_url',
        'lifestyle_cards',
        'growth_heading',
        'growth_stats',
        'cta_banner_image',
        'cta_title_gold',
        'cta_title_white',
        'cta_description',
        'cta_btn_primary_label',
        'cta_btn_primary_url',
        'cta_btn_secondary_label',
        'cta_btn_secondary_url',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hero_stats' => 'array',
            'why_choose_items' => 'array',
            'lifestyle_cards' => 'array',
            'growth_stats' => 'array',
        ];
    }

    public static function instance(): self
    {
        $row = static::first();
        if ($row) {
            return $row;
        }

        return static::create([
            'title' => 'DHA Lahore',
            'slug' => 'dha',
            'heading' => 'DHA Lahore',
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function heroVisualUrl(): string
    {
        if (! empty($this->featured_image)) {
            return url('storage/' . ltrim($this->featured_image, '/'));
        }

        return asset('theme/images/bg/6.jpg');
    }

    /** @return array{gold: string, white: string} */
    public function heroTitleParts(): array
    {
        $gold = trim((string) ($this->hero_title_gold ?? ''));
        $white = trim((string) ($this->hero_title_white ?? ''));

        if ($gold || $white) {
            return ['gold' => $gold, 'white' => $white];
        }

        $heading = trim((string) ($this->heading ?: $this->title ?: ''));
        if (preg_match('/^(DHA)\s+(.+)$/i', $heading, $matches)) {
            return ['gold' => $matches[1], 'white' => $matches[2]];
        }

        return ['gold' => '', 'white' => $heading];
    }

    public function heroEyebrow(): string
    {
        return trim((string) ($this->hero_eyebrow ?: 'WELCOME TO'));
    }

    public function heroSubtitle(): string
    {
        return trim((string) ($this->hero_subtitle ?: ''));
    }

    public function heroDescription(): string
    {
        return trim((string) ($this->hero_description ?: ''));
    }

    /** @return array{label: string, url: string} */
    public function heroPrimaryButton(): array
    {
        return [
            'label' => trim((string) ($this->hero_btn_primary_label ?: 'EXPLORE PROJECTS')),
            'url' => trim((string) ($this->hero_btn_primary_url ?: '#dha-phases')) ?: '#dha-phases',
        ];
    }

    /** @return array{label: string, url: string} */
    public function heroSecondaryButton(): array
    {
        return [
            'label' => trim((string) ($this->hero_btn_secondary_label ?: 'VIEW PHASES')),
            'url' => trim((string) ($this->hero_btn_secondary_url ?: '#dha-phases')) ?: '#dha-phases',
        ];
    }

    /** @return list<array{icon: string, value: string, label: string}> */
    public function heroStats(): array
    {
        $items = is_array($this->hero_stats) ? $this->hero_stats : [];
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $value = trim((string) ($item['value'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            if ($value === '' && $label === '') {
                continue;
            }
            $normalized[] = [
                'icon' => trim((string) ($item['icon'] ?? 'circle')) ?: 'circle',
                'value' => $value,
                'label' => $label,
            ];
        }

        if ($normalized !== []) {
            return array_slice($normalized, 0, 5);
        }

        return self::defaultHeroStats();
    }

    /** @return list<array{icon: string, value: string, label: string}> */
    public static function defaultHeroStats(): array
    {
        return [
            ['icon' => 'users', 'value' => '54,541+', 'label' => 'Total Plots'],
            ['icon' => 'map', 'value' => '9', 'label' => 'Phases'],
            ['icon' => 'shield-check', 'value' => '100%', 'label' => 'Secure Community'],
            ['icon' => 'tree-pine', 'value' => '25+', 'label' => 'Parks & Green Areas'],
            ['icon' => 'building-2', 'value' => '10+', 'label' => 'Mosques'],
        ];
    }

    /** @return array{eyebrow: string, gold: string, white: string} */
    public function phasesHeading(): array
    {
        return [
            'eyebrow' => trim((string) ($this->phases_heading_eyebrow ?: 'EXPLORE')),
            'gold' => trim((string) ($this->phases_heading_gold ?: 'DHA')),
            'white' => trim((string) ($this->phases_heading_white ?: 'PHASES')),
        ];
    }

    /** @return array{label: string, url: string} */
    public function viewAllPropertiesButton(): array
    {
        $url = trim((string) ($this->view_all_url ?: ''));
        if ($url === '' || $url === '/listing') {
            $url = route('listing');
        } elseif (! str_starts_with($url, 'http') && ! str_starts_with($url, '/')) {
            $url = '/' . ltrim($url, '/');
        }

        return [
            'label' => trim((string) ($this->view_all_label ?: 'VIEW ALL PROPERTIES')),
            'url' => $url,
        ];
    }

    public function whyChooseHeading(): string
    {
        return trim((string) ($this->why_choose_heading ?: 'WHY CHOOSE DHA LAHORE?'));
    }

    /** @return list<array{icon: string, title: string, text: string}> */
    public function whyChooseItems(): array
    {
        return $this->normalizeIconItems($this->why_choose_items, self::defaultWhyChooseItems(), 6);
    }

    /** @return array{eyebrow: string, heading: string, description: string, btn: array{label: string, url: string}} */
    public function lifestyleBlock(): array
    {
        return [
            'eyebrow' => trim((string) ($this->lifestyle_eyebrow ?: 'A LIFESTYLE')),
            'heading' => trim((string) ($this->lifestyle_heading ?: 'BEYOND EXCELLENCE')),
            'description' => trim((string) ($this->lifestyle_description ?: '')),
            'btn' => [
                'label' => trim((string) ($this->lifestyle_btn_label ?: 'DISCOVER MORE')),
                'url' => trim((string) ($this->lifestyle_btn_url ?: '#dha-phases')) ?: '#dha-phases',
            ],
        ];
    }

    /** @return list<array{label: string, image: string, image_url: string}> */
    public function lifestyleCards(): array
    {
        $stored = is_array($this->lifestyle_cards) ? $this->lifestyle_cards : [];
        $defaults = self::defaultLifestyleCards();
        $out = [];

        foreach ($defaults as $i => $default) {
            $row = is_array($stored[$i] ?? null) ? $stored[$i] : [];
            $label = trim((string) ($row['label'] ?? '')) ?: $default['label'];
            $image = trim((string) ($row['image'] ?? ''));
            $out[] = [
                'label' => $label,
                'image' => $image,
                'image_url' => $this->storageImageUrl($image) ?: asset('theme/images/all/' . (($i % 3) + 1) . '.jpg'),
            ];
        }

        return $out;
    }

    public function growthHeading(): string
    {
        return trim((string) ($this->growth_heading ?: 'STRONG TODAY, STRONGER TOMORROW'));
    }

    /** @return list<array{icon: string, value: string, label: string}> */
    public function growthStats(): array
    {
        $items = is_array($this->growth_stats) ? $this->growth_stats : [];
        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $value = trim((string) ($item['value'] ?? ''));
            $label = trim((string) ($item['label'] ?? ''));
            if ($value === '' && $label === '') {
                continue;
            }
            $normalized[] = [
                'icon' => trim((string) ($item['icon'] ?? 'circle')) ?: 'circle',
                'value' => $value,
                'label' => $label,
            ];
        }

        return $normalized !== [] ? array_slice($normalized, 0, 5) : self::defaultGrowthStats();
    }

    public function ctaBannerUrl(): string
    {
        if (! empty($this->cta_banner_image)) {
            return url('storage/' . ltrim($this->cta_banner_image, '/'));
        }

        return $this->heroVisualUrl();
    }

    /** @return array{gold: string, white: string, description: string, primary: array{label: string, url: string}, secondary: array{label: string, url: string}} */
    public function ctaBanner(): array
    {
        $desc = trim((string) ($this->cta_description ?: ''));
        $lines = $desc !== '' ? preg_split('/\r\n|\r|\n/', $desc) : [];

        return [
            'gold' => trim((string) ($this->cta_title_gold ?: 'YOUR FUTURE')),
            'white' => trim((string) ($this->cta_title_white ?: 'STARTS HERE')),
            'lines' => array_values(array_filter(array_map('trim', $lines))),
            'description' => $desc,
            'primary' => [
                'label' => trim((string) ($this->cta_btn_primary_label ?: 'EXPLORE PROJECTS')),
                'url' => trim((string) ($this->cta_btn_primary_url ?: '#dha-phases')) ?: '#dha-phases',
            ],
            'secondary' => [
                'label' => trim((string) ($this->cta_btn_secondary_label ?: 'BOOK A SITE VISIT')),
                'url' => trim((string) ($this->cta_btn_secondary_url ?: '/contact-us')) ?: '/contact-us',
            ],
        ];
    }

    public function storageImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return url('storage/' . ltrim($path, '/'));
    }

    /** @param mixed $stored @param list<array{icon: string, title: string, text: string}> $defaults */
    private function normalizeIconItems(mixed $stored, array $defaults, int $max): array
    {
        if (! is_array($stored) || $stored === []) {
            return array_slice($defaults, 0, $max);
        }

        $out = [];
        foreach ($defaults as $i => $default) {
            if ($i >= $max) {
                break;
            }
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
            ];
        }

        return $out;
    }

    /** @return list<array{icon: string, title: string, text: string}> */
    public static function defaultWhyChooseItems(): array
    {
        return [
            ['icon' => 'map-pin', 'title' => 'Prime Location', 'text' => 'Strategically located in the heart of Lahore with excellent connectivity'],
            ['icon' => 'shield-check', 'title' => 'Secure Living', 'text' => '24/7 security with gated communities and surveillance'],
            ['icon' => 'trending-up', 'title' => 'High ROI', 'text' => 'Consistent property appreciation and rental yields'],
            ['icon' => 'building-2', 'title' => 'Modern Infrastructure', 'text' => 'World-class roads, utilities, and urban planning'],
            ['icon' => 'graduation-cap', 'title' => 'Top Schools', 'text' => 'Access to premier educational institutions'],
            ['icon' => 'heart-pulse', 'title' => 'Healthcare', 'text' => 'Nearby hospitals and medical facilities'],
        ];
    }

    /** @return list<array{label: string, image: string}> */
    public static function defaultLifestyleCards(): array
    {
        return [
            ['label' => 'PARKS & GREEN AREAS', 'image' => ''],
            ['label' => 'GRAND MOSQUES', 'image' => ''],
            ['label' => 'SPORTS COMPLEX', 'image' => ''],
            ['label' => 'COMMERCIAL HUBS', 'image' => ''],
            ['label' => 'FINE DINING', 'image' => ''],
            ['label' => 'CLUB HOUSES', 'image' => ''],
        ];
    }

    /** @return list<array{icon: string, value: string, label: string}> */
    public static function defaultGrowthStats(): array
    {
        return [
            ['icon' => 'trending-up', 'value' => '15-20%', 'label' => 'Average Annual ROI'],
            ['icon' => 'building', 'value' => '100%', 'label' => 'Developed Phases'],
            ['icon' => 'users', 'value' => '50,000+', 'label' => 'Happy Families'],
            ['icon' => 'award', 'value' => 'Premium', 'label' => 'Living Standard'],
            ['icon' => 'globe', 'value' => 'Global', 'label' => 'Recognition'],
        ];
    }
}
