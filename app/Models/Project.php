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
        'vr_tour_meta_title',
        'vr_tour_meta_description',
        'vr_tour_meta_keywords',
        'vr_tour_canonical_url',
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
        'testimonial_items',
        'invest_title',
        'invest_points',
        'invest_image',
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
            'testimonial_items' => 'array',
            'invest_points' => 'array',
            'title_descriptions' => 'array',
            'videos' => 'array',
            'gallery' => 'array',
            'hero_feature_cards' => 'array',
            'hero_stat_cards' => 'array',
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
}
