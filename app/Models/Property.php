<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Property extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_HOLD = 'hold';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_CLOSE = 'close';

    public const PURPOSE_SALE = 'sale';
    public const PURPOSE_RENT = 'rent';

    protected $fillable = [
        'title',
        'slug',
        'status',
        'dealer_id',
        'purpose',
        'description',
        'featured_image',
        'city',
        'state',
        'address',
        'short_address',
        'town',
        'latitude',
        'longitude',
        'google_map',
        'price_string',
        'price_digits',
        'property_type',
        'bedrooms',
        'bathrooms',
        'garage',
        'kitchen',
        'area_marla',
        'area_kanal',
        'amenities_description',
        'videos',
        'gallery',
        'video_gallery',
        'features',
        'location_accessibility',
        'nearest_hospitals',
        'nearest_markets',
        'nearest_restaurants',
        'amenities',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'is_hot',
    ];

    protected function casts(): array
    {
        return [
            'is_hot' => 'boolean',
            'price_digits' => 'decimal:2',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'area_marla' => 'decimal:2',
            'area_kanal' => 'decimal:2',
            'videos' => 'array',
            'gallery' => 'array',
            'video_gallery' => 'array',
            'features' => 'array',
            'location_accessibility' => 'array',
            'nearest_hospitals' => 'array',
            'nearest_markets' => 'array',
            'nearest_restaurants' => 'array',
            'amenities' => 'array',
        ];
    }

    public function projectTypes(): BelongsToMany
    {
        return $this->belongsToMany(ProjectType::class, 'property_project_type')->withTimestamps();
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function isOwnListing(): bool
    {
        return $this->dealer_id === 0;
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public static function booted(): void
    {
        static::creating(function (Property $property) {
            if (empty($property->slug)) {
                $property->slug = Str::slug($property->title);
            }
        });
    }
}
