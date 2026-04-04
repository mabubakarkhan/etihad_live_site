<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorDailyCount extends Model
{
    protected $fillable = [
        'date',
        'count',
        'count_own_listing',
        'count_dealer_listing',
        'count_projects',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'count' => 'integer',
            'count_own_listing' => 'integer',
            'count_dealer_listing' => 'integer',
            'count_projects' => 'integer',
        ];
    }
}
