<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

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
        try {
            return static::where('slug', $slug)->first();
        } catch (QueryException $e) {
            Log::warning('Failed to load cms_pages by slug', [
                'slug' => $slug,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public static function findSafe(int $id): ?self
    {
        try {
            return static::find($id);
        } catch (QueryException $e) {
            Log::warning('Failed to load cms_pages by id', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
