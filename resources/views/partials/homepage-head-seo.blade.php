@php
    $seo = is_array($seo ?? null) ? $seo : [];
@endphp
    <title>{{ e($seo['title'] ?? '') }}</title>

    <meta name="title" content="{{ e($seo['title'] ?? '') }}" />
    <meta name="description" content="{{ e($seo['description'] ?? '') }}" />
    <meta name="robots" content="{{ e($seo['robots'] ?? 'index, follow') }}" />

    <link rel="canonical" href="{{ e($seo['canonical'] ?? '') }}" />

    <meta name="keywords" content="{{ e($seo['keywords'] ?? '') }}" />

    <meta property="og:title" content="{{ e($seo['og_title'] ?? $seo['title'] ?? '') }}" />
    <meta property="og:description" content="{{ e($seo['og_description'] ?? $seo['description'] ?? '') }}" />
    <meta property="og:type" content="{{ e($seo['type'] ?? 'website') }}" />
    <meta property="og:url" content="{{ e($seo['canonical'] ?? '') }}" />
@if(!empty($seo['og_image']))
    <meta property="og:image" content="{{ e($seo['og_image']) }}" />
@endif

    <meta name="twitter:card" content="{{ e($seo['twitter_card'] ?? 'summary_large_image') }}" />
    <meta name="twitter:title" content="{{ e($seo['twitter_title'] ?? $seo['og_title'] ?? $seo['title'] ?? '') }}" />
    <meta name="twitter:description" content="{{ e($seo['twitter_description'] ?? $seo['og_description'] ?? $seo['description'] ?? '') }}" />
@if(!empty($seo['twitter_image']))
    <meta name="twitter:image" content="{{ e($seo['twitter_image']) }}" />
@endif
@if(!empty($seo['structured_data_json']))
    <script type="application/ld+json">{!! $seo['structured_data_json'] !!}</script>
@endif
