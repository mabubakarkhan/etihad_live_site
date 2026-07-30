<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DhaFileRateSetting extends Model
{
    protected $fillable = [
        'heading',
        'subheading',
        'details',
        'default_cta_label',
        'default_cta_url',
        'is_published',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'twitter_card',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultAttributes(): array
    {
        return [
            'heading' => 'DHA File Rates',
            'subheading' => 'Current DHA Lahore file rates by phase & plot size',
            'details' => 'Browse the latest DHA file rates across phases. Prices are indicative and may change — contact our advisors for the latest availability and deals.',
            'default_cta_label' => 'Enquire Now',
            'default_cta_url' => null,
            'is_published' => true,
            'meta_title' => 'DHA File Rates Lahore | Latest Phase Prices | Etihad Marketing',
            'meta_description' => 'Check current DHA Lahore file rates by phase and plot size. Compare prices and enquire with Etihad Marketing advisors for the latest deals.',
            'meta_keywords' => 'DHA file rates, DHA Lahore rates, DHA phase prices, DHA plot rates, Etihad Marketing',
            'canonical_url' => null,
            'meta_robots' => 'index, follow',
            'og_title' => 'DHA File Rates Lahore | Etihad Marketing',
            'og_description' => 'Latest DHA Lahore file rates by phase and plot size. Enquire with Etihad Marketing.',
            'twitter_card' => 'summary_large_image',
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
