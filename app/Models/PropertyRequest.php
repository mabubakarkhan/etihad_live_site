<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyRequest extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_SEEN = 'seen';

    protected $fillable = [
        'property_id',
        'project_id',
        'type',
        'dealer_id',
        'name',
        'phone',
        'email',
        'property_type',
        'budget',
        'message',
        'status',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeSeen($query)
    {
        return $query->where('status', self::STATUS_SEEN);
    }

    public function scopeProjectRequests($query)
    {
        return $query->where('project_id', '>', 0);
    }

    public function scopePropertyRequests($query)
    {
        return $query->where('property_id', '>', 0);
    }
}
