<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.map-embed-seo', ['seo' => $seo ?? []])
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/plugins.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/pages/map-embed-viewer.css') }}">
</head>
<body class="em-vw-root">
    @include('partials.map-embed-overlays')
    <div class="em-vw-stage" id="em-vw-stage" data-p="{{ $payload ?? '' }}"></div>
    <script src="{{ asset('theme/js/map-embed-viewer.js') }}"></script>
</body>
</html>
