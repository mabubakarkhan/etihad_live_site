@php
    $phase = $phase ?? null;
    if (!$phase) return;
    $valueProps = $phase->valuePropositions();
    $attractions = $phase->attractions();
    $investReasons = $phase->investmentReasons();
    $highlights = $phase->projectHighlights();
    $helpBar = $phase->helpBar();
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $phoneRaw = trim((string) ($cs->phone ?? ''));
    $phoneHref = $phoneRaw !== '' ? 'tel:' . preg_replace('/\s+/', '', $phoneRaw) : url('/contact-us');
    $waRaw = trim((string) ($cs->whatsapp ?? '')) ?: $phoneRaw;
    $waNumber = $waRaw !== '' ? preg_replace('/\D/', '', $waRaw) : '';
    $waText = urlencode('Hi, I have questions about ' . $phase->title);
    $waUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber . '?text=' . $waText : url('/contact-us');
    $registerWaText = urlencode('Hi, I would like to register my interest in ' . $phase->title);
    $registerWaUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber . '?text=' . $registerWaText : $waUrl;
@endphp

<div class="dha-phase-sections">
    {{-- 1. Value proposition bar --}}
    <section class="dha-prop-bar" aria-label="Key benefits">
        <div class="dha-prop-bar__inner">
            @foreach($valueProps as $index => $item)
                @if($index > 0)
                    <span class="dha-prop-bar__divider" aria-hidden="true"></span>
                @endif
                <article class="dha-prop-bar__item">
                    <span class="dha-prop-bar__icon"><i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i></span>
                    <div class="dha-prop-bar__text">
                        <strong>{{ $item['title'] }}</strong>
                        <span>{{ $item['text'] }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- 2. Attractions grid --}}
    <section class="dha-attractions" id="dha-attractions" aria-labelledby="dha-attractions-title">
        <div class="dha-attractions__head">
            <h2 class="dha-attractions__title" id="dha-attractions-title">{{ $phase->attractionsHeading() }}</h2>
            @if(!empty($hasGallery))
            <a href="#dha-gallery" class="dha-attractions__more">EXPLORE MORE <i data-lucide="arrow-right" aria-hidden="true"></i></a>
            @endif
        </div>
        <div class="dha-attractions__grid">
            @foreach($attractions as $item)
                @php
                    $bgImage = !empty($item['image']) ? $phase->attractionImageUrl($item['image']) : null;
                @endphp
                <article class="dha-attractions__card" @if($bgImage) style="--dha-card-bg: url('{{ $bgImage }}')" @endif>
                    <span class="dha-attractions__card-glow" aria-hidden="true"></span>
                    <span class="dha-attractions__card-icon"><i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i></span>
                    <h3 class="dha-attractions__card-title">{{ $item['title'] }}</h3>
                    <p class="dha-attractions__card-text">{{ $item['text'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    {{-- 3. Investment + highlights --}}
    <section class="dha-invest-block" aria-label="Investment highlights">
        <div class="dha-invest-block__grid">
            <div class="dha-invest-reasons">
                <h2 class="dha-invest-reasons__title">WHY INVEST IN {{ strtoupper($phase->title) }}?</h2>
                <div class="dha-invest-reasons__grid">
                    @foreach($investReasons as $item)
                        <article class="dha-invest-reasons__item">
                            <span class="dha-invest-reasons__icon"><i data-lucide="{{ $item['icon'] }}" aria-hidden="true"></i></span>
                            <div>
                                <strong>{{ $item['title'] }}</strong>
                                <span>{{ $item['text'] }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

            <aside class="dha-project-card">
                <h2 class="dha-project-card__title">PROJECT HIGHLIGHTS</h2>
                <div class="dha-project-card__tags">
                    @if(!empty($highlights['tag_primary']))
                        <span class="dha-project-card__tag dha-project-card__tag--gold">{{ $highlights['tag_primary'] }}</span>
                    @endif
                    @if(!empty($highlights['tag_secondary']))
                        <span class="dha-project-card__tag dha-project-card__tag--outline">{{ $highlights['tag_secondary'] }}</span>
                    @endif
                </div>
                <p class="dha-project-card__location">
                    <i data-lucide="map-pin" aria-hidden="true"></i>
                    {{ $highlights['location'] ?? $phase->stat_location }}
                </p>
                <div class="dha-project-card__stats">
                    <div class="dha-project-card__stat">
                        <i data-lucide="eye" aria-hidden="true"></i>
                        <div>
                            <strong>{{ $highlights['total_views'] ?? $phase->stat_total_plots }}</strong>
                            <span>Total Views</span>
                        </div>
                    </div>
                    <span class="dha-project-card__stat-divider" aria-hidden="true"></span>
                    <div class="dha-project-card__stat">
                        <i data-lucide="calendar" aria-hidden="true"></i>
                        <div>
                            <strong>{{ $highlights['developed_year'] ?? $phase->stat_year_developed }}</strong>
                            <span>Developed Year</span>
                        </div>
                    </div>
                </div>
                <div class="dha-project-card__register">
                    <div class="dha-project-card__register-icon"><i data-lucide="bell-ring" aria-hidden="true"></i></div>
                    <div class="dha-project-card__register-text">
                        <strong>{{ $highlights['register_title'] ?? 'Register Interest' }}</strong>
                        <span>{{ $highlights['register_text'] ?? 'Get updates and alerts about this listing.' }}</span>
                    </div>
                    <div class="dha-project-card__register-actions">
                        <a href="{{ $registerWaUrl }}" class="dha-project-card__register-btn" target="_blank" rel="noopener noreferrer">
                            REGISTER NOW <i data-lucide="arrow-right" aria-hidden="true"></i>
                        </a>
                        <a href="{{ $waUrl }}" class="dha-project-card__register-btn dha-project-card__register-btn--whatsapp" target="_blank" rel="noopener noreferrer">
                            <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WHATSAPP US
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    {{-- 4. Help bar --}}
    <section class="dha-help-bar" aria-label="Contact help">
        <div class="dha-help-bar__inner">
            <span class="dha-help-bar__icon"><i data-lucide="phone" aria-hidden="true"></i></span>
            <div class="dha-help-bar__content">
                <span class="dha-help-bar__eyebrow">{{ $helpBar['eyebrow'] }}</span>
                <strong class="dha-help-bar__title">{{ $helpBar['title'] }}</strong>
                <p class="dha-help-bar__text">{{ $helpBar['text'] }}</p>
            </div>
            <div class="dha-help-bar__actions">
                <a href="{{ $phoneHref }}" class="dha-help-bar__btn dha-help-bar__btn--gold">
                    CONTACT OUR EXPERTS <i data-lucide="arrow-right" aria-hidden="true"></i>
                </a>
                <a href="{{ $waUrl }}" class="dha-help-bar__btn dha-help-bar__btn--whatsapp" target="_blank" rel="noopener noreferrer">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> WHATSAPP US
                </a>
            </div>
        </div>
    </section>
</div>
