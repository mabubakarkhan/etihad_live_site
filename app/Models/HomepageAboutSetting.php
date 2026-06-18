<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageAboutSetting extends Model
{
    protected $fillable = [
        'tagline_about',
        'tagline_vision',
        'heading_line_1',
        'heading_line_2',
        'video',
        'media_caption_1',
        'media_caption_2',
        'about_para_1_lead',
        'about_para_1_highlight',
        'about_para_2_lead',
        'about_para_2_highlight',
        'vision_para_1_highlight',
        'vision_para_1_body',
        'vision_para_2_lead',
        'vision_para_2_highlight',
        'vision_para_2_body',
        'center_image',
        'secondary_image',
        'cta_text',
        'cta_url',
        'affiliated_text',
        'affiliated_url',
    ];

    /**
     * @return array<string, string|null>
     */
    public static function defaultAttributes(): array
    {
        return [
            'tagline_about' => 'ABOUT ETIHAD',
            'tagline_vision' => 'OUR VISION',
            'heading_line_1' => 'REFLECT THE SPIRIT',
            'heading_line_2' => 'OF INNOVATION',
            'video' => null,
            'media_caption_1' => 'Enhancing lifestyles through exceptional interior and exterior design.',
            'media_caption_2' => 'Today, we stand tall knowing that our journey has been and is worth it!',
            'about_para_1_lead' => 'Etihad is an established and well-renowned Renovation & fit-out company ',
            'about_para_1_highlight' => 'in Pakistan. Also known as one of the leading Fast Track Projects Service Providers in the Pakistan.',
            'about_para_2_lead' => 'Etihad has only expanded in terms of projects, experience, distinctive solutions, and an eye for aesthetics in its ',
            'about_para_2_highlight' => '13 years in the sector. We believe in bringing visions to life.',
            'vision_para_1_highlight' => 'We redefine ',
            'vision_para_1_body' => 'the urban skyline with architectural designs that merge elegance, innovation, and functionality.',
            'vision_para_2_lead' => 'Our vision is to create ',
            'vision_para_2_highlight' => 'iconic spaces that transcend time,',
            'vision_para_2_body' => ' reflecting the sophisticated and avant-garde spirit of Etihad living.',
            'center_image' => null,
            'secondary_image' => null,
            'cta_text' => 'Learn more',
            'cta_url' => 'javascript:void(0);',
            'affiliated_text' => 'Affiliated pages',
            'affiliated_url' => 'javascript://',
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

    public function mediaUrl(string $field, string $assetBase, string $fallbackFilename): string
    {
        return homepage_asset_url($this->{$field} ?? null, $assetBase, $fallbackFilename);
    }
}
