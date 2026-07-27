@php
    $mapHeading = trim((string) ($heading ?? ''));
    $mapTagline = trim((string) ($tagline ?? ''));
    $interactiveMap = $interactiveMap ?? null;
@endphp
@if($interactiveMap && $interactiveMap->isReadyForFront())
    @php
        $mapConfig = $interactiveMap->toFrontConfig();
        $mapCanvasId = 'portal-interactive-map-' . ($interactiveMap->id ?? uniqid());
        $googleMapsKey = config('app.google_maps_api_key', '');
        $googleMapsMapId = config('app.google_maps_map_id', 'DEMO_MAP_ID');
    @endphp
    <section class="portal-map-section" @if(!empty($mapSectionId)) id="{{ $mapSectionId }}" @endif aria-labelledby="portal-map-section-title">
        <div class="portal-map-section__inner">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-11">
                        @if($mapHeading !== '' || $mapTagline !== '')
                            <header class="portal-map-section__head">
                                @if($mapHeading !== '')
                                    <h2 class="portal-map-section__title" id="portal-map-section-title">{{ $mapHeading }}</h2>
                                @endif
                                @if($mapTagline !== '')
                                    <p class="portal-map-section__tagline">{{ $mapTagline }}</p>
                                @endif
                            </header>
                        @endif
                        <div
                            id="{{ $mapCanvasId }}"
                            class="portal-map-section__map-canvas"
                            data-interactive-map-config='@json($mapConfig)'
                            data-google-maps-key="{{ $googleMapsKey }}"
                            data-google-maps-map-id="{{ $googleMapsMapId }}"
                            role="region"
                            aria-label="{{ $mapHeading !== '' ? $mapHeading : 'Interactive map' }}"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @once('portal-interactive-map-assets')
        @push('scripts')
            <script src="{{ asset('theme/js/interactive-map/MapManager.js') }}?v=2"></script>
            <script src="{{ asset('theme/js/interactive-map/OverlayManager.js') }}?v=6"></script>
            <script src="{{ asset('theme/js/interactive-map/interactive-map-front.js') }}?v=4"></script>
        @endpush
    @endonce
@endif
