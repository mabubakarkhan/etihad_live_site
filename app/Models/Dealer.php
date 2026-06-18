<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Dealer extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    protected $fillable = [
        'name',
        'slug',
        'status',
        'show_homepage',
        'show_homepage_ad',
        'email',
        'phone',
        'whatsapp',
        'mobile',
        'address',
        'city',
        'state',
        'profile_pic',
        'info_detail',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'banner_image',
    ];

    protected $casts = [
        'show_homepage' => 'boolean',
        'show_homepage_ad' => 'boolean',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'dealer_id');
    }

    public function getPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function profileUrl(): ?string
    {
        return $this->slug ? route('dealer.show', $this->slug) : null;
    }

    public static function booted(): void
    {
        static::creating(function (Dealer $dealer) {
            if (empty($dealer->slug) && ! empty($dealer->name)) {
                $dealer->slug = Str::slug($dealer->name);
            }
        });
    }
}
