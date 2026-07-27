<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prototype Map Viewer{{ $selected ? ' · ' . $selected->title : '' }} | Etihad GIS POC</title>
    @include('prototype.partials.head-meta')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="{{ asset('theme/css/prototype/map-overlay.css') }}">
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
    <div class="prototype-shell prototype-shell--viewer">
        <header class="prototype-header prototype-header--compact">
            <div>
                <p class="prototype-badge">Internal POC</p>
                <h1 class="text-xl font-semibold">{{ $selected?->title ?? 'Interactive Map Preview' }}</h1>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                @if($overlays->count() > 1)
                    <select id="prototype-overlay-switcher" class="prototype-select">
                        @foreach($overlays as $overlay)
                            <option value="{{ route('prototype.interactive-map.show', $overlay) }}" @selected($selected && $selected->id === $overlay->id)>
                                {{ $overlay->title }}
                            </option>
                        @endforeach
                    </select>
                @endif
                <a href="{{ route('prototype.dashboard') }}" class="prototype-btn prototype-btn--sm">Dashboard</a>
                <a href="{{ route('admin.prototype.interactive-map.index', $selected ? ['overlay' => $selected->id] : []) }}" class="prototype-btn prototype-btn--sm">Editor</a>
            </div>
        </header>

        <div id="prototype-map-viewer"
             class="prototype-map-viewer"
             data-google-maps-key="{{ $googleMapsApiKey }}"
             data-google-maps-map-id="{{ $googleMapsMapId }}"
             data-overlay='@json($selected?->toMapConfig())'>
        </div>
        <div id="prototype-viewer-status" class="prototype-viewer-status" hidden></div>
    </div>

    <script>
        document.getElementById('prototype-overlay-switcher')?.addEventListener('change', function (e) {
            window.location.href = e.target.value;
        });
    </script>
    <script src="{{ asset('theme/js/prototype/MapManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/OverlayManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/SectionManager.js') }}"></script>
    <script src="{{ asset('theme/js/prototype/prototype-map-viewer.js') }}"></script>
</body>
</html>
