@php
    $dha = $dha ?? \App\Models\DhaSetting::instance();
    $viewAll = $dha->viewAllPropertiesButton();
    $whyItems = $dha->whyChooseItems();
    $lifestyle = $dha->lifestyleBlock();
    $lifestyleCards = $dha->lifestyleCards();
    $growthStats = $dha->growthStats();
    $cta = $dha->ctaBanner();
    $ctaImage = $dha->ctaBannerUrl();
@endphp
<section class="dha-main-sections">
    <div class="container">
        <div class="dha-main-view-all-wrap">
            <a href="{{ $viewAll['url'] }}" class="dha-main-view-all-btn">
                {{ $viewAll['label'] }}
                <i data-lucide="arrow-right" aria-hidden="true"></i>
            </a>
        </div>

        <div class="dha-main-why">
            <h2 class="dha-main-why__heading">{{ $dha->whyChooseHeading() }}</h2>
            <div class="dha-main-why__grid">
                @foreach($whyItems as $item)
                    <article class="dha-main-why__item">
                        <span class="dha-main-why__icon"><i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i></span>
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="dha-main-lifestyle">
            <div class="dha-main-lifestyle__copy">
                <p class="dha-main-lifestyle__eyebrow">{{ $lifestyle['eyebrow'] }}</p>
                <h2 class="dha-main-lifestyle__heading">{{ $lifestyle['heading'] }}</h2>
                @if($lifestyle['description'])
                    <p class="dha-main-lifestyle__desc">{{ $lifestyle['description'] }}</p>
                @endif
                <a href="{{ $lifestyle['btn']['url'] }}" class="dha-main-lifestyle__btn">
                    {{ $lifestyle['btn']['label'] }}
                    <i data-lucide="arrow-right" aria-hidden="true"></i>
                </a>
            </div>
            <div class="dha-main-lifestyle__cards">
                @foreach($lifestyleCards as $card)
                    <article class="dha-main-lifestyle__card">
                        <div class="dha-main-lifestyle__card-img" style="background-image:url('{{ $card['image_url'] }}')"></div>
                        <span class="dha-main-lifestyle__card-label">{{ $card['label'] }}</span>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="dha-main-growth">
            <h2 class="dha-main-growth__heading">{{ $dha->growthHeading() }}</h2>
            <div class="dha-main-growth__grid">
                @foreach($growthStats as $stat)
                    <article class="dha-main-growth__item">
                        <span class="dha-main-growth__icon"><i data-lucide="{{ $stat['icon'] }}" aria-hidden="true"></i></span>
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $stat['label'] }}</span>
                    </article>
                @endforeach
            </div>
        </div>
    </div>

    <section class="dha-main-cta-banner">
        <img src="{{ $ctaImage }}" alt="{{ $cta['gold'] }} {{ $cta['white'] }}" class="dha-main-cta-banner__bg" loading="lazy" />
        <div class="dha-main-cta-banner__shade" aria-hidden="true"></div>
        <div class="container dha-main-cta-banner__inner">
            <div class="dha-main-cta-banner__copy">
                <h2 class="dha-main-cta-banner__title">
                    <span class="dha-main-cta-banner__title-gold">{{ $cta['gold'] }}</span>
                    <span class="dha-main-cta-banner__title-white">{{ $cta['white'] }}</span>
                </h2>
                @if(!empty($cta['lines']))
                    @foreach($cta['lines'] as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                @elseif($cta['description'])
                    <p>{{ $cta['description'] }}</p>
                @endif
            </div>
            <div class="dha-main-cta-banner__actions">
                @if($cta['primary']['label'])
                    <a href="{{ $cta['primary']['url'] }}" class="dha-main-cta-banner__btn dha-main-cta-banner__btn--primary">
                        {{ $cta['primary']['label'] }}
                        <i data-lucide="arrow-right" aria-hidden="true"></i>
                    </a>
                @endif
                @if($cta['secondary']['label'])
                    <a href="{{ $cta['secondary']['url'] }}" class="dha-main-cta-banner__btn dha-main-cta-banner__btn--outline">
                        <i data-lucide="calendar" aria-hidden="true"></i>
                        {{ $cta['secondary']['label'] }}
                    </a>
                @endif
            </div>
        </div>
    </section>
</section>
