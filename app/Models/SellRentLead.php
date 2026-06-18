<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellRentLead extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_SEEN = 'seen';

    public const INTENT_SELL = 'sell';
    public const INTENT_RENT = 'rent';

    protected $fillable = [
        'intent',
        'rent_frequency',
        'location',
        'category',
        'property_type',
        'bedrooms',
        'area_sqft',
        'furnishing',
        'urgency',
        'name',
        'phone',
        'email',
        'status',
    ];
}
