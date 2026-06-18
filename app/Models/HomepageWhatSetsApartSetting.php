<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageWhatSetsApartSetting extends Model
{
    protected $fillable = [
        'title_line_1',
        'title_highlight',
        'subtitle',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaultAttributes(): array
    {
        return [
            'title_line_1' => 'What',
            'title_highlight' => 'Set Us Apart?',
            'subtitle' => 'At Etihad Marketing, we combine expertise, innovation, and customer-centric solutions to deliver exceptional real estate experiences in Pakistan.',
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
     * @return \Illuminate\Support\Collection<int, HomepageWhatSetsApartCard>
     */
    public static function orderedCards()
    {
        return HomepageWhatSetsApartCard::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
