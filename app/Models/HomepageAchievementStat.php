<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageAchievementStat extends Model
{
    protected $fillable = [
        'sort_order',
        'value',
        'suffix',
        'label',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
