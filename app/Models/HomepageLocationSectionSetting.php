<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageLocationSectionSetting extends Model
{
    protected $fillable = [
        'map_background_image',
        'card_image',
        'pin_image',
    ];

    public static function instance(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::query()->create([]);
    }
}
