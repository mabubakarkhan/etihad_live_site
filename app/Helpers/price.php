<?php

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

        if ($priceString !== null && $priceString !== '') {
            return $currency . ' ' . $priceString;
        }

        return $currency . ' On Request';
    }
}
