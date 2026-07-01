<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageFooterSetting extends Model
{
    protected $fillable = [
        'footer_image',
        'footer_image_alt',
    ];

    /**
     * @return array<string, string|null>
     */
    public static function defaultAttributes(): array
    {
        return [
            'footer_image' => null,
            'footer_image_alt' => 'ETIHAD footer image',
        ];
    }

    public static function instance(): self
    {
        $row = static::query()->first();
        if ($row) {
            return $row;
        }

        return static::query()->create(static::defaultAttributes());
    }
}
