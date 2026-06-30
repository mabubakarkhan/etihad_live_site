@php
    $hero = $hero ?? \App\Models\HomepageHeroSetting::instance();
    $assetBase = $assetBase ?? rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') . '/homepage/';
    $heroImage = $heroImage ?? homepage_asset_url($hero->hero_image ?? null, $assetBase, 'hero-screen-1-D7I92d4H.webp');
    $ctaUrl = trim((string) ($hero->cta_url ?? ''));
    if ($ctaUrl === '') {
        $ctaUrl = route('contact-us');
    } elseif (! str_starts_with($ctaUrl, 'http') && ! str_starts_with($ctaUrl, 'javascript')) {
        $ctaUrl = url($ctaUrl);
    }
    $imageAlt = trim((string) ($hero->hero_image_alt ?? '')) !== '' ? $hero->hero_image_alt : 'ETIHAD hero screen 1';
@endphp
          <div class="hero__screen hero__screen-1">
            <picture>
              <source
                srcset="{{ $heroImage }}"
                media="(orientation: portrait)"
              />
              <img
                class="hero__background"
                src="{{ $heroImage }}"
                alt="{{ $imageAlt }}"
              />
            </picture>

            <div class="hero__flex">
              <h1 class="hero__tagline">
                {{ $hero->tagline }}
              </h1>

              <div class="hero__heading">
                <span>{{ $hero->heading_line_1 }}</span>
              </div>

              <div class="hero__divider">
                <div class="hero__line">
                </div>
                <img
                  class="hero__shadow"
                  src="{{ $assetBase }}assets/hero-shadow-C_dxtPeI.avif"
                  alt="shadow divider"
                />

                <div class="hero__divider-img">
                  <img src="{{ $assetBase }}assets/hero-center-BCaaP5iL.png" alt="decorative image" />
                </div>
              </div>

              <div class="hero__subheading">
                <span>{{ $hero->heading_line_2 }}</span>
              </div>

              <div class="hero__button">
                <a class="CTA-btn" href="{{ $ctaUrl }}">
                  <div class="CTA-btn__border">
                  </div>
                  <div class="CTA-btn__blur">
                  </div>
                  <div class="CTA-btn__background">
                  </div>

                  <div class="CTA-btn__inner">
                    <span class="CTA-btn__icon"></span>
                    <span class="CTA-btn__text">{{ $hero->cta_text }}</span>
                  </div>
                </a>
              </div>
            </div>

            <p class="hero__text">
              {{ $hero->description }}
            </p>

            <div class="hero__scroll-wrapper">
              <span class="hero__scroll-text">{{ $hero->scroll_text }}</span>
              <span class="hero__scroll-icon">            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="23" viewBox="0 0 20 23" fill="none">
                  <path d="M10 0L10 21" stroke="white" stroke-opacity="0.8" stroke-width="1.5" />
                  <path d="M1 12.5L10 21.5L19 12.5" stroke="white" stroke-opacity="0.8" stroke-width="1.5" />
                </svg></span>
            </div>
          </div>
