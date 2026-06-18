@php
    $metaTitle = $phase->title . ' - VR Tour - ' . config('app.name');
    $metaDescriptionRaw = is_string($phase->meta_description ?? null) && trim($phase->meta_description) !== ''
        ? $phase->meta_description
        : (is_string($phase->description ?? null) && trim($phase->description) !== ''
            ? $phase->description
            : $phase->title);
    $metaDescription = \Illuminate\Support\Str::limit(strip_tags((string) $metaDescriptionRaw), 160);
    $metaKeywords = is_string($phase->meta_keywords ?? null) ? trim($phase->meta_keywords) : '';
    $canonical = request()->url();
    $ogImage = $phase->featured_image
        ? url('storage/' . ltrim($phase->featured_image, '/'))
        : asset('theme/images/all/1.jpg');
@endphp
@include('layouts.vr-tour', [
    'metaTitle' => $metaTitle,
    'metaDescription' => $metaDescription,
    'metaKeywords' => $metaKeywords,
    'canonical' => $canonical,
    'ogImage' => $ogImage,
    'vrTourUrl' => $vrTourUrl,
    'overlayPhone' => $overlayPhone ?? '',
])
