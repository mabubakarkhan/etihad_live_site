<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $metaTitle }}</title>
    @include('partials.seo-meta', ['seo' => [
        'title' => $metaTitle,
        'description' => $metaDescription,
        'keywords' => $metaKeywords ?? '',
        'canonical' => $canonical ?? request()->url(),
        'image' => $ogImage ?? asset('theme/images/all/1.jpg'),
        'type' => 'website',
    ]])
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/plugins.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/db-style.css') }}">
</head>
<body class="project-vr-tour-page">
    @include('partials.vr-tour-overlays', ['overlayPhone' => $overlayPhone ?? ''])
    <div class="project-vr-tour-frame-wrap">
        <iframe
            src="{{ $vrTourUrl }}"
            class="project-vr-tour-frame"
            allow="xr-spatial-tracking; fullscreen; accelerometer; gyroscope; autoplay; clipboard-write"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            loading="eager"
        ></iframe>
    </div>
</body>
</html>
