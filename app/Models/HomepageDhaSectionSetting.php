<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageDhaSectionSetting extends Model
{
    protected $fillable = [
        'eyebrow',
        'title_line_1',
        'title_highlight',
        'description',
        'footer_note',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaultAttributes(): array
    {
        return [
            'eyebrow' => 'Defence Housing Authority',
            'title_line_1' => 'Discover',
            'title_highlight' => 'DHA Phases',
            'description' => 'Explore every active DHA phase across Lahore — master-planned communities with premium plots, modern infrastructure, and strong long-term investment potential.',
            'footer_note' => 'Scroll through all DHA phases on Etihad',
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
