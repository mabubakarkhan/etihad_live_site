@php
    $mapHeading = trim((string) ($heading ?? ''));
    $mapTagline = trim((string) ($tagline ?? ''));
    $mapImageUrl = ! empty($imageUrl) ? (string) $imageUrl : null;
    $mapViewerUrl = ! empty($viewerUrl) ? (string) $viewerUrl : null;
@endphp
@if($mapImageUrl && $mapViewerUrl)
<section class="portal-map-section" aria-labelledby="portal-map-section-title">
    <div class="portal-map-section__inner">
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
        <a href="{{ $mapViewerUrl }}" target="_blank" rel="noopener noreferrer" class="portal-map-section__link">
            <span class="portal-map-section__image-wrap">
                <img
                    src="{{ $mapImageUrl }}"
                    alt="{{ $mapHeading !== '' ? $mapHeading : 'Interactive map' }}"
                    class="portal-map-section__image"
                    loading="lazy"
                >
                <span class="portal-map-section__overlay" aria-hidden="true">
                    <span class="portal-map-section__icon"><i class="fa-solid fa-map-location-dot"></i></span>
                    <span class="portal-map-section__cta-text">Open interactive map</span>
                </span>
            </span>
        </a>
    </div>
</section>
@endif
