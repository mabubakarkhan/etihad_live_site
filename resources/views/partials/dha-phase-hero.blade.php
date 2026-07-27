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

    $hasLiveMap = $phase->hasMapSection();
    $hasMapPage = $phase->showMapButton();
    $mapHref = $hasLiveMap
        ? '#dha-phase-live-map'
        : ($hasMapPage ? $phase->mapPageUrl() : null);

    $hasListingsOnPage = !empty($hasPhaseListings);
    $listingsHref = $hasListingsOnPage
        ? '#dha-phase-listings'
        : (route('listing') . '?dha_phase=' . urlencode((string) $phase->id));

    // Same contact number as listing detail pages + Contact settings.
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $agentPhone = trim((string) ($cs->phone ?: ''));
    $agentPhoneClean = $agentPhone !== '' ? preg_replace('/\s+/', '', $agentPhone) : '';
    $agentTelHref = $agentPhoneClean !== '' ? 'tel:' . $agentPhoneClean : '';
    $hasAgentPhone = $agentTelHref !== '';
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

                <div class="dha-lux-hero__actions" aria-label="Phase actions">
                    @if($hasPhasePdf)
                    <a href="{{ $phase->phasePdfUrl() }}" class="dha-lux-hero__btn dha-lux-hero__btn--primary" target="_blank" rel="noopener noreferrer">
                        <i class="fa-light fa-file-pdf" aria-hidden="true"></i>
                        View PDF
                    </a>
                    @endif

                    @if($mapHref)
                    <a href="{{ $mapHref }}" class="dha-lux-hero__btn dha-lux-hero__btn--ghost">
                        <i data-lucide="map" aria-hidden="true"></i>
                        View Map
                    </a>
                    @endif

                    <a href="{{ $listingsHref }}" class="dha-lux-hero__btn dha-lux-hero__btn--ghost">
                        <i data-lucide="search" aria-hidden="true"></i>
                        Explore Listings
                    </a>

                    @if($hasAgentPhone)
                    <button
                        type="button"
                        class="dha-lux-hero__btn dha-lux-hero__btn--ghost"
                        data-dha-agent-trigger
                        data-tel="{{ $agentTelHref }}"
                        aria-haspopup="dialog"
                    >
                        <i data-lucide="phone" aria-hidden="true"></i>
                        Talk to an Agent
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($hasAgentPhone)
    <div
        class="dha-agent-modal"
        id="dha-agent-modal"
        hidden
        aria-hidden="true"
        role="dialog"
        aria-modal="true"
        aria-labelledby="dha-agent-modal-title"
    >
        <div class="dha-agent-modal__backdrop" data-dha-agent-close tabindex="-1"></div>
        <div class="dha-agent-modal__panel">
            <button type="button" class="dha-agent-modal__close" data-dha-agent-close aria-label="Close">
                <i class="fa-regular fa-xmark" aria-hidden="true"></i>
            </button>
            <div class="dha-agent-modal__icon" aria-hidden="true">
                <i data-lucide="phone-call"></i>
            </div>
            <h2 class="dha-agent-modal__title" id="dha-agent-modal-title">Talk to an Agent</h2>
            <p class="dha-agent-modal__lead">Speak with our team about {{ $phase->title }}.</p>
            <a href="{{ $agentTelHref }}" class="dha-agent-modal__number">{{ $agentPhone }}</a>
            <a href="{{ $agentTelHref }}" class="dha-agent-modal__call-btn">
                <i class="fa-solid fa-phone" aria-hidden="true"></i>
                Call Now
            </a>
        </div>
    </div>
    @endif
</div>
