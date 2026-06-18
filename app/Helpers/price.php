<?php

if (! function_exists('clean_price_string')) {
    /** Remove literal "rupees" from stored price text (site uses PKR currency code). */
    function clean_price_string(?string $priceString): string
    {
        if ($priceString === null || $priceString === '') {
            return '';
        }

        return trim(preg_replace('/\s*rupees\s*/i', ' ', $priceString));
    }
}

if (! function_exists('format_price')) {
    /**
     * Format price for display using site currency (PKR).
     * Use price_digits when available, else price_string, else "On Request".
     */
    function format_price($priceDigits = null, ?string $priceString = null): string
    {
        $currency = config('app.currency', 'PKR');

        if ($priceDigits !== null && $priceDigits !== '') {
            return $currency . ' ' . number_format((float) $priceDigits, 2);
        }

        $priceString = clean_price_string($priceString);
        if ($priceString !== '') {
            return $currency . ' ' . $priceString;
        }

        return $currency . ' On Request';
    }
}

if (! function_exists('dealer_profile_url')) {
    function dealer_profile_url(?object $dealer): ?string
    {
        if (! $dealer || empty($dealer->slug)) {
            return null;
        }

        return url('/our-team/' . ltrim((string) $dealer->slug, '/'));
    }
}
