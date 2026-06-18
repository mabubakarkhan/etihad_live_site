@php
    $seo = is_array($seo ?? null) ? $seo : [];
    $seoTitle = seo_str($seo['title'] ?? '');
    $seoDescription = seo_str($seo['description'] ?? '');
    $seoKeywords = seo_str($seo['keywords'] ?? '');
    $seoCanonical = seo_str($seo['canonical'] ?? (string) url()->current());
    $seoImage = seo_str($seo['image'] ?? '');
    $seoType = seo_str($seo['type'] ?? 'website') ?: 'website';
@endphp
<meta name="description" content="{{ e($seoDescription) }}">
<meta name="keywords" content="{{ e($seoKeywords) }}">
<link rel="canonical" href="{{ e($seoCanonical) }}">
<meta property="og:type" content="{{ e($seoType) }}">
<meta property="og:title" content="{{ e($seoTitle) }}">
<meta property="og:description" content="{{ e($seoDescription) }}">
<meta property="og:url" content="{{ e($seoCanonical) }}">
<meta property="og:image" content="{{ e($seoImage) }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ e($seoTitle) }}">
<meta name="twitter:description" content="{{ e($seoDescription) }}">
<meta name="twitter:image" content="{{ e($seoImage) }}">
