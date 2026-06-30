<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageHeroSetting extends Model
{
    protected $fillable = [
        'hero_image',
        'hero_image_alt',
        'tagline',
        'heading_line_1',
        'heading_line_2',
        'description',
        'cta_text',
        'cta_url',
        'scroll_text',
    ];

    /**
     * @return array<string, string|null>
     */
    public static function defaultAttributes(): array
    {
        return [
            'hero_image' => null,
            'hero_image_alt' => 'ETIHAD hero screen 1',
            'tagline' => 'We Make Passive Investing In Real Estate Simple',
            'heading_line_1' => 'BUILDING',
            'heading_line_2' => 'VISIONS',
            'description' => 'Invest in the region’s first integrated luxury and active eco-conscious development society, a project of the Defence Housing Authority expanded throughout Pakistan.',
            'cta_text' => 'Contact Us',
            'cta_url' => '/contact-us',
            'scroll_text' => 'SCROLL TO EXPLORE',
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
