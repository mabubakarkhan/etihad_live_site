<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageChoiceSlide extends Model
{
    protected $fillable = [
        'sort_order',
        'heading_text',
        'counter_to',
        'counter_text',
        'description',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'counter_to' => 'integer',
    ];
}
