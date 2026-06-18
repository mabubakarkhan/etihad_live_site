<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellRentPageSetting extends Model
{
    public const STATUS_ACTIVE = 'active';

    protected $fillable = [
        'hero_image',
        'hero_bubbles',
        'valuation_heading',
        'valuation_price',
        'valuation_badge',
        'valuation_meta',
        'valuation_chart_image',
        'valuation_copy',
        'transactions_heading',
        'transaction_stats',
        'transactions',
        'transactions_copy',
        'faqs_heading',
        'faqs',
        'form_submit_label',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hero_bubbles' => 'array',
            'valuation_meta' => 'array',
            'transaction_stats' => 'array',
            'transactions' => 'array',
            'faqs' => 'array',
        ];
    }

    public static function instance(): self
    {
        $row = static::first();
        if ($row) {
            return $row;
        }

        $seed = require database_path('data/sell_rent_page_seed.php');

        return static::create([
            'hero_image' => $seed['hero_image'],
            'hero_bubbles' => $seed['hero_bubbles'],
            'valuation_heading' => $seed['valuation_heading'],
            'valuation_price' => $seed['valuation_price'],
            'valuation_badge' => $seed['valuation_badge'],
            'valuation_meta' => $seed['valuation_meta'],
            'valuation_chart_image' => $seed['valuation_chart_image'],
            'valuation_copy' => $seed['valuation_copy'],
            'transactions_heading' => $seed['transactions_heading'],
            'transaction_stats' => $seed['transaction_stats'],
            'transactions' => $seed['transactions'],
            'transactions_copy' => $seed['transactions_copy'],
            'faqs_heading' => $seed['faqs_heading'],
            'faqs' => $seed['faqs'],
            'form_submit_label' => $seed['form_submit_label'],
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public function heroImageUrl(): ?string
    {
        return $this->storageImageUrl($this->hero_image);
    }

    public function hasHeroImage(): bool
    {
        return trim((string) ($this->hero_image ?? '')) !== '';
    }

    public function valuationChartUrl(): ?string
    {
        return $this->storageImageUrl($this->valuation_chart_image);
    }

    /** @return list<array{label: string, value: string, position: int}> */
    public function heroBubbles(): array
    {
        $items = is_array($this->hero_bubbles) ? $this->hero_bubbles : [];
        $out = [];

        foreach ($items as $i => $item) {
            if (! is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            $value = trim((string) ($item['value'] ?? ''));
            if ($label === '' && $value === '') {
                continue;
            }
            $position = (int) ($item['position'] ?? ($i + 1));
            $out[] = [
                'label' => $label,
                'value' => $value,
                'position' => max(1, min(3, $position)),
            ];
        }

        return $out !== [] ? $out : self::defaultHeroBubbles();
    }

    /** @return list<array{label: string, value: string, highlight: bool}> */
    public function valuationMeta(): array
    {
        return $this->normalizeMetaRows($this->valuation_meta, self::defaultValuationMeta());
    }

    /** @return list<array{label: string, value: string, change: string, is_up: bool}> */
    public function transactionStats(): array
    {
        $items = is_array($this->transaction_stats) ? $this->transaction_stats : [];
        $out = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            $value = trim((string) ($item['value'] ?? ''));
            if ($label === '' && $value === '') {
                continue;
            }
            $out[] = [
                'label' => $label,
                'value' => $value,
                'change' => trim((string) ($item['change'] ?? '')),
                'is_up' => (bool) ($item['is_up'] ?? false),
            ];
        }

        return $out !== [] ? $out : self::defaultTransactionStats();
    }

    /** @return list<array{date: string, location: string, price: string, type: string}> */
    public function transactions(): array
    {
        $items = is_array($this->transactions) ? $this->transactions : [];
        $out = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $date = trim((string) ($item['date'] ?? ''));
            $location = trim((string) ($item['location'] ?? ''));
            $price = trim((string) ($item['price'] ?? ''));
            $type = trim((string) ($item['type'] ?? ''));
            if ($date === '' && $location === '' && $price === '') {
                continue;
            }
            $out[] = [
                'date' => $date,
                'location' => $location,
                'price' => $price,
                'type' => $type,
            ];
        }

        return $out !== [] ? $out : self::defaultTransactions();
    }

    /** @return list<array{question: string, answer: string}> */
    public function faqs(): array
    {
        $items = is_array($this->faqs) ? $this->faqs : [];
        $out = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $question = trim((string) ($item['question'] ?? ''));
            $answer = trim((string) ($item['answer'] ?? ''));
            if ($question === '' && $answer === '') {
                continue;
            }
            $out[] = [
                'question' => $question,
                'answer' => $answer,
            ];
        }

        return $out !== [] ? $out : self::defaultFaqs();
    }

    public function formSubmitLabel(): string
    {
        return trim((string) ($this->form_submit_label ?: 'Continue')) ?: 'Continue';
    }

    public function storageImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url('storage/' . ltrim($path, '/'));
    }

    /** @param mixed $stored @param list<array{label: string, value: string, highlight: bool}> $defaults */
    private function normalizeMetaRows(mixed $stored, array $defaults): array
    {
        if (! is_array($stored) || $stored === []) {
            return $defaults;
        }

        $out = [];
        foreach ($stored as $item) {
            if (! is_array($item)) {
                continue;
            }
            $label = trim((string) ($item['label'] ?? ''));
            $value = trim((string) ($item['value'] ?? ''));
            if ($label === '' && $value === '') {
                continue;
            }
            $out[] = [
                'label' => $label,
                'value' => $value,
                'highlight' => (bool) ($item['highlight'] ?? false),
            ];
        }

        return $out !== [] ? $out : $defaults;
    }

    /** @return list<array{label: string, value: string, position: int}> */
    public static function defaultHeroBubbles(): array
    {
        return (require database_path('data/sell_rent_page_seed.php'))['hero_bubbles'];
    }

    /** @return list<array{label: string, value: string, highlight: bool}> */
    public static function defaultValuationMeta(): array
    {
        return (require database_path('data/sell_rent_page_seed.php'))['valuation_meta'];
    }

    /** @return list<array{label: string, value: string, change: string, is_up: bool}> */
    public static function defaultTransactionStats(): array
    {
        return (require database_path('data/sell_rent_page_seed.php'))['transaction_stats'];
    }

    /** @return list<array{date: string, location: string, price: string, type: string}> */
    public static function defaultTransactions(): array
    {
        return (require database_path('data/sell_rent_page_seed.php'))['transactions'];
    }

    /** @return list<array{question: string, answer: string}> */
    public static function defaultFaqs(): array
    {
        return (require database_path('data/sell_rent_page_seed.php'))['faqs'];
    }
}
