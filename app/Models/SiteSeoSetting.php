<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSeoSetting extends Model
{
    protected $fillable = [
        'google_analytics_id',
        'google_tag_manager_id',
        'google_ads_id',
        'facebook_pixel_id',
        'tiktok_pixel_id',
        'linkedin_partner_id',
        'hotjar_id',
        'google_site_verification',
        'bing_site_verification',
        'facebook_domain_verification',
        'default_og_image',
        'custom_head_code',
        'custom_body_open_code',
        'custom_body_close_code',
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
