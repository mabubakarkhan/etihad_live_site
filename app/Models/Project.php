<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Project extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_HOLD = 'hold';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_CLOSE = 'close';

    protected $fillable = [
        'title',
        'slug',
        'sort_order',
        'status',
        'price',
        'launch_year',
        'description',
        'state',
        'city',
        'short_address',
        'full_address',
        'google_map',
        'latitude',
        'longitude',
        'address_image',
        'logo',
        'featured_image',
        'homepage_listing_image',
        'hero_feature_cards',
        'hero_stat_cards',
        'featured_youtube_url',
        'featured_video_title',
        'featured_video_description',
        'vr_tour_url',
        'vr_tour_image',
        'vr_tour_meta_title',
        'vr_tour_meta_description',
        'vr_tour_meta_keywords',
        'vr_tour_canonical_url',
        'booking_procedure',
        'about_developers',
        'developer_logo',
        'project_file_pdf',
        'noc_planning_content',
        'noc_planning_image',
        'future_note_title',
        'future_note_content',
        'extra_section_title',
        'extra_section_content',
        'unique_features',
        'price_plan_section_title',
        'price_plan_items',
        'faqs',
        'plans',
        'pricing_place_cards',
        'price_slider_images',
        'testimonial_items',
        'invest_title',
        'invest_points',
        'invest_image',
        'map_section_heading',
        'map_section_tagline',
        'map_section_image',
        'map_section_url',
        'map_section_meta_title',
        'map_section_meta_description',
        'map_section_meta_keywords',
        'project_detail_tabs',
        'tabs_follow_content',
        'title_descriptions',
        'videos',
        'gallery',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
    ];

    protected function casts(): array
    {
        return [
            'unique_features' => 'array',
            'price_plan_items' => 'array',
            'faqs' => 'array',
            'plans' => 'array',
            'pricing_place_cards' => 'array',
            'price_slider_images' => 'array',
            'booking_procedure' => 'array',
            'testimonial_items' => 'array',
            'invest_points' => 'array',
            'title_descriptions' => 'array',
            'videos' => 'array',
            'gallery' => 'array',
            'hero_feature_cards' => 'array',
            'hero_stat_cards' => 'array',
            'project_detail_tabs' => 'array',
        ];
    }

    public function projectTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProjectType::class, 'project_project_type')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeFrontOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('id');
    }

    public static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
            if (!$project->sort_order) {
                $project->sort_order = (int) static::max('sort_order') + 1;
            }
        });
    }

    public function mapSectionImageUrl(): ?string
    {
        $path = trim((string) ($this->map_section_image ?? ''));

        return $path !== '' ? url('storage/' . ltrim($path, '/')) : null;
    }

    public function mapSectionUrl(): ?string
    {
        $url = trim((string) ($this->map_section_url ?? ''));

        return $url !== '' ? $url : null;
    }

    public function hasMapSection(): bool
    {
        return $this->mapSectionImageUrl() !== null && $this->mapSectionUrl() !== null;
    }

    public function mapSectionViewerUrl(): ?string
    {
        if (! $this->hasMapSection()) {
            return null;
        }

        return route('project.interactive-map', ['project' => $this]);
    }

    public function vrTourImageUrl(): ?string
    {
        $path = trim((string) ($this->vr_tour_image ?? ''));

        return $path !== '' ? asset('storage/' . ltrim($path, '/')) : null;
    }

    public function hasVrTourPromo(): bool
    {
        return $this->vrTourImageUrl() !== null && trim((string) ($this->vr_tour_url ?? '')) !== '';
    }

    /** @return array{heading: string, content: string, documents_heading: string, steps: array<int, array{title: string, description: string}>, documents: array<int, array{icon: string, label: string}>} */
    public function bookingProcedureData(): array
    {
        $raw = is_array($this->booking_procedure ?? null) ? $this->booking_procedure : [];

        return [
            'heading' => trim((string) ($raw['heading'] ?? '')),
            'content' => (string) ($raw['content'] ?? ''),
            'documents_heading' => trim((string) ($raw['documents_heading'] ?? 'Required Documents')) ?: 'Required Documents',
            'steps' => array_values(array_filter(array_map(function ($step) {
                if (! is_array($step)) {
                    return null;
                }
                $title = trim((string) ($step['title'] ?? ''));
                $description = trim((string) ($step['description'] ?? ''));

                return ($title !== '' || $description !== '') ? [
                    'title' => $title,
                    'description' => $description,
                ] : null;
            }, $raw['steps'] ?? []))),
            'documents' => array_values(array_filter(array_map(function ($doc) {
                if (! is_array($doc)) {
                    return null;
                }
                $label = trim((string) ($doc['label'] ?? ''));
                if ($label === '') {
                    return null;
                }
                $icon = trim((string) ($doc['icon'] ?? 'fa-circle-check'));
                if ($icon !== '' && ! str_contains($icon, 'fa-')) {
                    $icon = 'fa-' . ltrim($icon, '-');
                }

                return ['icon' => $icon, 'label' => $label];
            }, $raw['documents'] ?? []))),
        ];
    }

    public function hasBookingProcedure(): bool
    {
        $data = $this->bookingProcedureData();

        return $data['heading'] !== ''
            || trim(strip_tags($data['content'])) !== ''
            || $data['steps'] !== []
            || $data['documents'] !== [];
    }
}
