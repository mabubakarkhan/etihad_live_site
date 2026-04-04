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
        'status',
        'price',
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
        'featured_youtube_url',
        'featured_video_title',
        'featured_video_description',
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
            'title_descriptions' => 'array',
            'videos' => 'array',
            'gallery' => 'array',
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

    public static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->slug)) {
                $project->slug = Str::slug($project->title);
            }
        });
    }
}
