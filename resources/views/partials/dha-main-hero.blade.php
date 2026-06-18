@php
    $dha = $dha ?? \App\Models\DhaSetting::instance();
    $titleParts = $dha->heroTitleParts();
    $heroImage = $dha->heroVisualUrl();
    $eyebrow = $dha->heroEyebrow();
    $subtitle = $dha->heroSubtitle();
    $description = $dha->heroDescription();
    $primaryBtn = $dha->heroPrimaryButton();
    $secondaryBtn = $dha->heroSecondaryButton();
@endphp
<div class="dha-main-hero-wrap" id="dha-main-hero">
    <section class="dha-main-hero">
        <img src="{{ $heroImage }}" alt="{{ $titleParts['gold'] }} {{ $titleParts['white'] }}" class="dha-main-hero__bg" loading="eager" />
        <div class="dha-main-hero__shade" aria-hidden="true"></div>

        <div class="dha-main-hero__inner">
            <div class="dha-main-hero__content">
                @if($eyebrow)
                    <p class="dha-main-hero__eyebrow">{{ $eyebrow }}</p>
                @endif

                <h1 class="dha-main-hero__title">
                    @if($titleParts['gold'])
                        <span class="dha-main-hero__title-gold">{{ $titleParts['gold'] }}</span>
                    @endif
                    @if($titleParts['white'])
                        <span class="dha-main-hero__title-white">{{ $titleParts['white'] }}</span>
                    @endif
                </h1>

                @if($subtitle)
                    <p class="dha-main-hero__subtitle">{{ $subtitle }}</p>
                @endif

                @if($description)
                    <p class="dha-main-hero__desc">{{ $description }}</p>
                @endif

                @if($primaryBtn['label'] || $secondaryBtn['label'])
                    <div class="dha-main-hero__actions">
                        @if($primaryBtn['label'])
                            <a href="{{ $primaryBtn['url'] }}" class="dha-main-hero__btn dha-main-hero__btn--primary">
                                {{ $primaryBtn['label'] }}
                                <i data-lucide="arrow-right" aria-hidden="true"></i>
                            </a>
                        @endif
                        @if($secondaryBtn['label'])
                            <a href="{{ $secondaryBtn['url'] }}" class="dha-main-hero__btn dha-main-hero__btn--outline">
                                <i data-lucide="map" aria-hidden="true"></i>
                                {{ $secondaryBtn['label'] }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </section>
</div>
