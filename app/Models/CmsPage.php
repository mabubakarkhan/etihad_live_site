<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'heading',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'banner_image',
    ];

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
