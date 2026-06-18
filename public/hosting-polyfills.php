<?php

/**
 * mbstring polyfill — Laravel calls mb_* from Illuminate\Support namespace.
 */
namespace Illuminate\Support {
    if (! function_exists(__NAMESPACE__ . '\\mb_split')) {
        function mb_split(string $pattern, string $string, int $limit = -1): array|false
        {
            $regex = $pattern === '\s+' ? '/\s+/u' : '/' . str_replace('/', '\\/', $pattern) . '/u';
            $parts = $limit >= 0 ? preg_split($regex, $string, $limit) : preg_split($regex, $string);

            return $parts === false ? false : $parts;
        }
    }
    if (! function_exists(__NAMESPACE__ . '\\mb_strlen')) {
        function mb_strlen(?string $string, ?string $encoding = null): int
        {
            return \strlen($string ?? '');
        }
    }
    if (! function_exists(__NAMESPACE__ . '\\mb_strtolower')) {
        function mb_strtolower(?string $string, ?string $encoding = null): string
        {
            return \strtolower($string ?? '');
        }
    }
    if (! function_exists(__NAMESPACE__ . '\\mb_strtoupper')) {
        function mb_strtoupper(?string $string, ?string $encoding = null): string
        {
            return \strtoupper($string ?? '');
        }
    }
    if (! function_exists(__NAMESPACE__ . '\\mb_substr')) {
        function mb_substr(?string $string, int $start, ?int $length = null, ?string $encoding = null): string
        {
            return $length === null ? \substr($string ?? '', $start) : \substr($string ?? '', $start, $length);
        }
    }
}

namespace {
    if (! function_exists('mb_split')) {
        function mb_split(string $pattern, string $string, int $limit = -1): array|false
        {
            return \Illuminate\Support\mb_split($pattern, $string, $limit);
        }
    }
    if (! function_exists('mb_strlen')) {
        function mb_strlen(?string $string, ?string $encoding = null): int
        {
            return \Illuminate\Support\mb_strlen($string, $encoding);
        }
    }
    if (! function_exists('mb_strtolower')) {
        function mb_strtolower(?string $string, ?string $encoding = null): string
        {
            return \Illuminate\Support\mb_strtolower($string, $encoding);
        }
    }
    if (! function_exists('mb_strtoupper')) {
        function mb_strtoupper(?string $string, ?string $encoding = null): string
        {
            return \Illuminate\Support\mb_strtoupper($string, $encoding);
        }
    }
    if (! function_exists('mb_substr')) {
        function mb_substr(?string $string, int $start, ?int $length = null, ?string $encoding = null): string
        {
            return \Illuminate\Support\mb_substr($string, $start, $length, $encoding);
        }
    }
    if (! defined('MB_CASE_UPPER')) {
        define('MB_CASE_UPPER', 0);
        define('MB_CASE_LOWER', 1);
        define('MB_CASE_TITLE', 2);
    }
    if (! function_exists('mb_convert_case')) {
        function mb_convert_case(?string $string, int $mode, ?string $encoding = null): string
        {
            $string = $string ?? '';

            return match ($mode) {
                MB_CASE_UPPER => \strtoupper($string),
                MB_CASE_LOWER => \strtolower($string),
                MB_CASE_TITLE => \ucwords(\strtolower($string)),
                default => $string,
            };
        }
    }
}
