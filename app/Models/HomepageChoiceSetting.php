<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageChoiceSetting extends Model
{
    protected $fillable = [
        'section_heading',
        'scroll_label_desktop',
        'scroll_label_mobile',
        'background_image',
        'background_image_portrait',
    ];

    /**
     * @return array<string, string|null>
     */
    public static function defaultAttributes(): array
    {
        return [
            'section_heading' => 'MAKE YOUR CHOICE',
            'scroll_label_desktop' => 'scroll',
            'scroll_label_mobile' => 'drag',
            'background_image' => null,
            'background_image_portrait' => null,
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

    /**
     * @return \Illuminate\Support\Collection<int, HomepageChoiceSlide>
     */
    public static function orderedSlides()
    {
        return HomepageChoiceSlide::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function backgroundUrl(string $assetBase, bool $portrait = false): string
    {
        if ($portrait) {
            $path = $this->background_image_portrait ?: $this->background_image;

            return homepage_asset_url($path, $assetBase, 'choice-background-CtCIvw6A.avif');
        }

        return homepage_asset_url($this->background_image, $assetBase, 'choice-background-CtCIvw6A.avif');
    }
}
