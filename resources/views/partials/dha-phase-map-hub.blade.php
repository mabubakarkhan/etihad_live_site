@php
    $phase = $phase ?? null;
    if (!$phase) {
        return;
    }

    $pdfUrl = $phase->phasePdfUrl();
    $mapEmbedUrl = $phase->mapEmbedUrl();
    $nearbyFacilities = $phase->nearbyFacilities();
    $nearbyCategories = $phase->nearbyFacilityCategories();
    $mapCenter = [
        'lat' => $phase->latitude !== null ? (float) $phase->latitude : 31.476723,
        'lng' => $phase->longitude !== null ? (float) $phase->longitude : 74.384087,
    ];
    $googleMapsKey = config('app.google_maps_api_key', '');

    $defaultTab = $pdfUrl ? 'pdf' : ($mapEmbedUrl ? 'google' : 'nearby');
@endphp
<section class="dha-map-hub" id="dha-map-hub" aria-labelledby="dha-map-hub-title"
    data-default-tab="{{ $defaultTab }}"
    data-map-center='@json($mapCenter)'
    data-facilities='@json($nearbyFacilities)'
    data-google-maps-key="{{ $googleMapsKey }}">
    <div class="dha-map-hub__shell">
        <header class="dha-map-hub__head">
            <h2 class="dha-map-hub__title" id="dha-map-hub-title">Interactive Map Hub</h2>
            <p class="dha-map-hub__lead">Explore the official layout, live location, and nearby facilities for {{ $phase->title }}.</p>
        </header>

        <div class="dha-map-hub__tabs" role="tablist" aria-label="Map hub views">
            <button type="button" class="dha-map-hub__tab" role="tab" id="dha-map-hub-tab-pdf" data-hub-tab="pdf" aria-controls="dha-map-hub-panel-pdf" aria-selected="false" @disabled(! $pdfUrl)>
                <i data-lucide="file-text" aria-hidden="true"></i>
                Official DHA Map
            </button>
            <button type="button" class="dha-map-hub__tab" role="tab" id="dha-map-hub-tab-google" data-hub-tab="google" aria-controls="dha-map-hub-panel-google" aria-selected="false" @disabled(! $mapEmbedUrl)>
                <i data-lucide="map" aria-hidden="true"></i>
                Google Map
            </button>
            <button type="button" class="dha-map-hub__tab" role="tab" id="dha-map-hub-tab-nearby" data-hub-tab="nearby" aria-controls="dha-map-hub-panel-nearby" aria-selected="false">
                <i data-lucide="map-pinned" aria-hidden="true"></i>
                Nearby Facilities
            </button>
        </div>

        <div class="dha-map-hub__panels">
            <div class="dha-map-hub__panel" id="dha-map-hub-panel-pdf" role="tabpanel" aria-labelledby="dha-map-hub-tab-pdf" data-hub-panel="pdf" hidden>
                @if($pdfUrl)
                    <div class="dha-map-hub__viewer dha-map-hub__viewer--pdf">
                        <iframe
                            src="{{ $pdfUrl }}#toolbar=0&navpanes=0&scrollbar=0&statusbar=0&messages=0&view=FitH"
                            title="{{ $phase->title }} official map PDF"
                            class="dha-map-hub__pdf"
                        ></iframe>
                    </div>
                @else
                    <p class="dha-map-hub__empty">Official DHA map PDF is not available for this phase yet.</p>
                @endif
            </div>

            <div class="dha-map-hub__panel" id="dha-map-hub-panel-google" role="tabpanel" aria-labelledby="dha-map-hub-tab-google" data-hub-panel="google" hidden>
                @if($mapEmbedUrl)
                    <div class="dha-map-hub__viewer">
                        <iframe
                            src="{{ $mapEmbedUrl }}"
                            title="{{ $phase->title }} Google Map"
                            class="dha-map-hub__map-frame"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    </div>
                @else
                    <p class="dha-map-hub__empty">Google Map is not configured for this phase yet.</p>
                @endif
            </div>

            <div class="dha-map-hub__panel" id="dha-map-hub-panel-nearby" role="tabpanel" aria-labelledby="dha-map-hub-tab-nearby" data-hub-panel="nearby" hidden>
                <div class="dha-map-hub__filters" role="group" aria-label="Facility categories">
                    <button type="button" class="dha-map-hub__filter is-active" data-facility-filter="all">All</button>
                    @foreach($nearbyCategories as $category)
                        <button type="button" class="dha-map-hub__filter" data-facility-filter="{{ $category }}">{{ $category }}</button>
                    @endforeach
                </div>
                <div class="dha-map-hub__nearby-layout">
                    <div id="dha-map-hub-nearby-map" class="dha-map-hub__nearby-map" role="region" aria-label="Nearby facilities map"></div>
                    <ul class="dha-map-hub__facility-list">
                        @foreach($nearbyFacilities as $index => $facility)
                            <li class="dha-map-hub__facility-item" data-facility-category="{{ $facility['category'] }}" data-facility-index="{{ $index }}">
                                <span class="dha-map-hub__facility-cat">{{ $facility['category'] }}</span>
                                <strong class="dha-map-hub__facility-name">{{ $facility['name'] }}</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
