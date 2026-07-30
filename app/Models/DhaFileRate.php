<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class DhaFileRate extends Model
{
    public const CATEGORY_RESIDENTIAL = 'residential';
    public const CATEGORY_COMMERCIAL = 'commercial';

    protected $fillable = [
        'file_number',
        'plot_size',
        'category',
        'file_type',
        'dha_phase_id',
        'price',
        'price_digits',
        'cta_label',
        'cta_url',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'dha_phase_id' => 'integer',
        ];
    }

    public function dhaPhase(): BelongsTo
    {
        return $this->belongsTo(DhaPhase::class, 'dha_phase_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFrontOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function categoryLabel(): string
    {
        return match ($this->category) {
            self::CATEGORY_COMMERCIAL => 'Commercial',
            self::CATEGORY_RESIDENTIAL => 'Residential',
            default => $this->category ? ucfirst((string) $this->category) : '',
        };
    }

    /**
     * Type suggestions: common defaults + every type already saved by admin.
     *
     * @return list<string>
     */
    public static function typeSuggestions(): array
    {
        $defaults = ['Allocation', 'Affidavit', 'Open', 'Balloting'];

        $saved = static::query()
            ->whereNotNull('file_type')
            ->where('file_type', '!=', '')
            ->distinct()
            ->orderBy('file_type')
            ->pluck('file_type')
            ->all();

        return Collection::make(array_merge($defaults, $saved))
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique(fn ($v) => mb_strtolower($v))
            ->values()
            ->all();
    }

    /**
     * @return array{residential: string, commercial: string}
     */
    public static function categoryOptions(): array
    {
        return [
            self::CATEGORY_RESIDENTIAL => 'Residential',
            self::CATEGORY_COMMERCIAL => 'Commercial',
        ];
    }
}
