<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Career extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_DRAFT = 'draft';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'title',
        'slug',
        'location',
        'department',
        'education',
        'experience',
        'timings',
        'joining_month',
        'employment_type',
        'salary_range',
        'vacancies',
        'apply_before',
        'apply_email',
        'apply_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'requirements',
        'status',
        'sort_order',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public static function booted(): void
    {
        static::creating(function (Career $career) {
            if (empty($career->slug) && !empty($career->title)) {
                $career->slug = Str::slug($career->title);
            }
        });
    }
}
