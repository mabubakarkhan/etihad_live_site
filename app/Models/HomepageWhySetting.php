<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageWhySetting extends Model
{
    protected $fillable = [
        'heading_line_1',
        'heading_line_2',
        'description',
        'scroll_label',
        'image_left',
        'image_center',
        'image_right',
        'image_center_back',
    ];

    /**
     * @return array<string, string|null>
     */
    public static function defaultAttributes(): array
    {
        return [
            'heading_line_1' => 'WHY CHOOSE',
            'heading_line_2' => 'ETIHAD?',
            'description' => 'To achieve flawless interior design from planning to execution, you need a skilled real estate and property development consultant in Pakistan. Our experienced team delivers customized solutions, prioritizing client satisfaction and handling projects of all sizes with precision.',
            'scroll_label' => 'SCROLL',
            'image_left' => null,
            'image_center' => null,
            'image_right' => null,
            'image_center_back' => null,
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

    public function imageUrl(string $field, string $assetBase, string $fallbackFilename): string
    {
        return homepage_asset_url($this->{$field} ?? null, $assetBase, $fallbackFilename);
    }
}
