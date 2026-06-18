<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageHeroSetting extends Model
{
    protected $fillable = [
        'hero_image',
    ];

    public static function instance(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::query()->create([
            'hero_image' => null,
        ]);
    }
}
