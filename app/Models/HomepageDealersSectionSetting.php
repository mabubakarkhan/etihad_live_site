<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageDealersSectionSetting extends Model
{
    protected $fillable = [
        'eyebrow',
        'title_line_1',
        'title_highlight',
        'description',
        'footer_note',
        'card_badge',
        'cta_label',
        'view_all_label',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaultAttributes(): array
    {
        return [
            'eyebrow' => 'Trusted Agents',
            'title_line_1' => 'Explore Our',
            'title_highlight' => 'Popular Agents',
            'description' => 'Meet verified Etihad dealers with active listings across DHA Lahore — browse profiles, property counts, and connect directly with the right agent.',
            'footer_note' => 'Scroll through featured agents on Etihad',
            'card_badge' => 'Trusted Agent',
            'cta_label' => 'View profile',
            'view_all_label' => 'View All Agents',
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
