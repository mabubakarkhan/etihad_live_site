<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BlogTag extends Model
{
    protected $fillable = [
        'wp_term_id',
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'og_image',
    ];

    protected function casts(): array
    {
        return [
            'wp_term_id' => 'integer',
        ];
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag');
    }

    /**
     * WP-style tag URL: /tag/{slug}/
     */
    public function url(): string
    {
        return url('/tag/' . $this->slug);
    }
}
