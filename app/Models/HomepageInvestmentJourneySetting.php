<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageInvestmentJourneySetting extends Model
{
    protected $fillable = [
        'title_line_1',
        'title_highlight',
    ];

    /**
     * @return array<string, string>
     */
    public static function defaultAttributes(): array
    {
        return [
            'title_line_1' => 'Real Estate Investment',
            'title_highlight' => 'Journey',
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
     * @return \Illuminate\Support\Collection<int, HomepageInvestmentJourneyStep>
     */
    public static function orderedSteps()
    {
        return HomepageInvestmentJourneyStep::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }
}
