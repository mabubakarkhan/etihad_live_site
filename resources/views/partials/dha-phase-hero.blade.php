@php
    $phase = $phase ?? null;
    if (!$phase) return;
    $titleParts = $phase->heroTitleParts();
    $heroLead = $phase->hero_lead ?: 'A perfect blend of prime location, modern infrastructure, and high investment potential.';
    $statLocation = $phase->stat_location ?: 'Lahore, Pakistan';
    $statArea = $phase->stat_total_area ?: '5,987 Kanal';
    $statPlots = $phase->stat_total_plots ?: '54,541+';
    $statYear = $phase->stat_year_developed ?: '2002';
    $heroImage = $phase->heroVisualUrl();
    $hasPhasePdf = $phase->hasPhasePdf();
    $hasVrTour = $phase->hasVrTour();
    $hasMapButton = $phase->showMapButton();
    $hasHeroActions = $hasPhasePdf || $hasVrTour || $hasMapButton || !empty($hasGallery);
@endphp
<div class="dha-lux-hero-wrap" id="dha-phase-hero">
    <section class="dha-lux-hero">
        <img src="{{ $heroImage }}" alt="{{ $phase->title }}" class="dha-lux-hero__bg" loading="eager" />
        <div class="dha-lux-hero__shade" aria-hidden="true"></div>

        <div class="dha-lux-hero__inner">
            <div class="dha-lux-hero__content">
                <nav class="dha-lux-hero__crumb" aria-label="Breadcrumb">
                    <a href="{{ url('/') }}">Home</a>
                    <span>&rsaquo;</span>
                    <a href="{{ route('dha.index') }}">DHA</a>
                    <span>&rsaquo;</span>
                    <span>{{ $phase->title }}</span>
                </nav>

                <h1 class="dha-lux-hero__title">
                    @if($titleParts['gold'])
                        <span class="dha-lux-hero__title-gold">{{ $titleParts['gold'] }}</span>
                        <span class="dha-lux-hero__title-white">{{ $titleParts['white'] }}</span>
                    @else
                        <span class="dha-lux-hero__title-white">{{ $titleParts['white'] }}</span>
                    @endif
                </h1>

                <p class="dha-lux-hero__lead">{{ $heroLead }}</p>

                <div class="dha-lux-hero__stats">
                    <article class="dha-lux-hero__stat">
                        <i data-lucide="map-pin" aria-hidden="true"></i>
                        <div>
                            <span class="dha-lux-hero__stat-label">Prime Location</span>
                            <strong class="dha-lux-hero__stat-value">{{ $statLocation }}</strong>
                        </div>
                    </article>
                    <article class="dha-lux-hero__stat">
                        <i data-lucide="layout-grid" aria-hidden="true"></i>
                        <div>
                            <span class="dha-lux-hero__stat-label">Total Area</span>
                            <strong class="dha-lux-hero__stat-value">{{ $statArea }}</strong>
                        </div>
                    </article>
                    <article class="dha-lux-hero__stat">
                        <i data-lucide="map" aria-hidden="true"></i>
                        <div>
                            <span class="dha-lux-hero__stat-label">Total Plots</span>
                            <strong class="dha-lux-hero__stat-value">{{ $statPlots }}</strong>
                        </div>
                    </article>
                    <article class="dha-lux-hero__stat">
                        <i data-lucide="calendar" aria-hidden="true"></i>
                        <div>
                            <span class="dha-lux-hero__stat-label">Developed</span>
                            <strong class="dha-lux-hero__stat-value">{{ $statYear }}</strong>
                        </div>
                    </article>
                </div>

                @if($hasHeroActions)
                <div class="dha-lux-hero__actions" aria-label="Phase actions">
                    @if($hasPhasePdf)
                    <a href="{{ $phase->phasePdfUrl() }}" class="dha-lux-hero__btn dha-lux-hero__btn--primary" target="_blank" rel="noopener noreferrer">
                        <i class="fa-light fa-file-pdf" aria-hidden="true"></i>
                        View PDF
                    </a>
                    @endif
                    @if($hasVrTour)
                    <a href="{{ $phase->vrTourPageUrl() }}" class="dha-lux-hero__btn dha-lux-hero__btn--ghost" target="_blank" rel="noopener noreferrer">
                        <i class="fa-solid fa-vr-cardboard" aria-hidden="true"></i>
                        VR Tour
                    </a>
                    @endif
                    @if($hasMapButton)
                    <a href="{{ $phase->mapPageUrl() }}" class="dha-lux-hero__btn dha-lux-hero__btn--ghost">
                        <i data-lucide="map" aria-hidden="true"></i>
                        View Map
                    </a>
                    @endif
                    @if(!empty($hasGallery))
                    <a href="#dha-gallery" class="dha-lux-hero__btn {{ $hasPhasePdf ? 'dha-lux-hero__btn--ghost' : 'dha-lux-hero__btn--primary' }}">
                        <i data-lucide="image" aria-hidden="true"></i>
                        View Gallery
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </section>
</div>
