<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogPost extends Model
{
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_DRAFT = 'draft';

    protected $fillable = [
        'wp_post_id',
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_source',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyphrase',
        'keyphrases_json',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_card',
        'twitter_image',
        'schema_json',
        'schema_type',
        'breadcrumb_title',
        'redirect_url',
        'seo_score',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'wp_post_id' => 'integer',
            'author_id' => 'integer',
            'seo_score' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_post_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag');
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Exact WP-style permalink: /{Y}/{m}/{d}/{slug}/
     */
    public function url(): string
    {
        $date = $this->published_at ?? $this->created_at ?? now();

        return url(sprintf(
            '/%s/%s/%s/%s',
            $date->format('Y'),
            $date->format('m'),
            $date->format('d'),
            $this->slug
        ));
    }

    public function seoImage(): string
    {
        if ($this->og_image) {
            return $this->og_image;
        }
        if ($this->featured_image) {
            return str_starts_with($this->featured_image, 'http')
                ? $this->featured_image
                : asset('storage/' . ltrim($this->featured_image, '/'));
        }

        return (string) ($this->featured_image_source ?: '');
    }

    public function displayImage(?string $fallback = null): string
    {
        $image = $this->seoImage();
        if ($image !== '') {
            return $image;
        }

        return $fallback ?? asset('theme/images/bg/8.jpg');
    }
}
