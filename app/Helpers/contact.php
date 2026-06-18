<?php

if (! function_exists('contact_tel_href')) {
    function contact_tel_href(?string $phone): string
    {
        $phone = trim((string) $phone);

        return $phone === '' ? '' : 'tel:' . preg_replace('/\s+/', '', $phone);
    }
}

if (! function_exists('contact_whatsapp_href')) {
    function contact_whatsapp_href(?string $whatsapp): string
    {
        $raw = trim((string) $whatsapp);
        if ($raw === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $raw)) {
            return $raw;
        }

        $digits = preg_replace('/\D/', '', $raw);

        return $digits === '' ? '' : 'https://wa.me/' . $digits;
    }
}

if (! function_exists('contact_website_href')) {
    function contact_website_href(?string $website): string
    {
        $website = trim((string) $website);
        if ($website === '') {
            return '';
        }

        return preg_match('/^https?:\/\//i', $website) ? $website : 'https://' . ltrim($website, '/');
    }
}

if (! function_exists('contact_website_label')) {
    function contact_website_label(?string $website): string
    {
        $href = contact_website_href($website);
        if ($href === '') {
            return '';
        }

        return preg_replace('/^https?:\/\/(www\.)?/i', '', rtrim($href, '/'));
    }
}

if (! function_exists('contact_map_href')) {
    function contact_map_href(?object $contact): string
    {
        $mapUrl = trim((string) ($contact->map_url ?? ''));
        if ($mapUrl !== '') {
            return preg_match('/^https?:\/\//i', $mapUrl) ? $mapUrl : 'https://' . ltrim($mapUrl, '/');
        }

        $lat = $contact->latitude ?? null;
        $lng = $contact->longitude ?? null;
        if ($lat !== null && $lng !== null && $lat !== '' && $lng !== '') {
            return 'https://www.google.com/maps?q=' . rawurlencode((string) $lat . ',' . (string) $lng);
        }

        return '';
    }
}
