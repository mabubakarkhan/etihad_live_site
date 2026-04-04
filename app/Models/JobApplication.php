<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_SEEN = 'seen';
    public const STATUS_ACCEPT = 'accept';
    public const STATUS_CONSIDERING = 'considering';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'career_id',
        'name',
        'mobile',
        'email',
        'address',
        'city',
        'education',
        'comments',
        'cv_path',
        'status',
    ];

    public function career(): BelongsTo
    {
        return $this->belongsTo(Career::class);
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }
}
