@php
    $seo = is_array($seo ?? null) ? $seo : [];
    $noIndex = 'noindex, nofollow, noarchive, nosnippet, noimageindex, max-snippet:0, max-image-preview:none';
@endphp
<title>{{ e($seo['title'] ?? '') }}</title>

<meta name="title" content="{{ e($seo['title'] ?? '') }}">
<meta name="description" content="{{ e($seo['description'] ?? '') }}">
<meta name="keywords" content="{{ e($seo['keywords'] ?? '') }}">
<meta name="robots" content="{{ e($noIndex) }}">
<meta name="googlebot" content="{{ e($noIndex) }}">
<meta name="bingbot" content="{{ e($noIndex) }}">
<meta name="author" content="{{ e(config('app.name', 'Etihad Marketing')) }}">
<meta name="theme-color" content="#ffffff">

@if(!empty($seo['canonical']))
<link rel="canonical" href="{{ e($seo['canonical']) }}">
@endif

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('theme/images/favicon_io/apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('theme/images/favicon_io/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('theme/images/favicon_io/favicon-16x16.png') }}">
<link rel="shortcut icon" href="{{ asset('theme/images/favicon_io/favicon.ico') }}">

<meta property="og:site_name" content="{{ e(config('app.name', 'Etihad Marketing')) }}">
<meta property="og:locale" content="en_PK">
<meta property="og:type" content="{{ e($seo['type'] ?? 'website') }}">
<meta property="og:title" content="{{ e($seo['og_title'] ?? $seo['title'] ?? '') }}">
<meta property="og:description" content="{{ e($seo['og_description'] ?? $seo['description'] ?? '') }}">
<meta property="og:url" content="{{ e($seo['canonical'] ?? request()->url()) }}">
@if(!empty($seo['og_image']))
<meta property="og:image" content="{{ e($seo['og_image']) }}">
<meta property="og:image:alt" content="{{ e($seo['og_title'] ?? $seo['title'] ?? '') }}">
@endif

<meta name="twitter:card" content="{{ e($seo['twitter_card'] ?? 'summary_large_image') }}">
<meta name="twitter:title" content="{{ e($seo['twitter_title'] ?? $seo['og_title'] ?? $seo['title'] ?? '') }}">
<meta name="twitter:description" content="{{ e($seo['twitter_description'] ?? $seo['og_description'] ?? $seo['description'] ?? '') }}">
@if(!empty($seo['twitter_image']))
<meta name="twitter:image" content="{{ e($seo['twitter_image']) }}">
@endif

@if(!empty($seo['geo_placename']))
<meta name="geo.placename" content="{{ e($seo['geo_placename']) }}">
@endif
@if(!empty($seo['geo_region']))
<meta name="geo.region" content="{{ e($seo['geo_region']) }}">
@endif
