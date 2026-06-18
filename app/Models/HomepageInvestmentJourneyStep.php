<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageInvestmentJourneyStep extends Model
{
    protected $fillable = [
        'sort_order',
        'title',
        'description',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
