@php
    $about = $about ?? \App\Models\HomepageAboutSetting::instance();
    $assetBase = $assetBase ?? rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') . '/homepage/';
    $centerImage = $about->mediaUrl('center_image', $assetBase, 'hero-background-Rk6TMBAb.webp');
    $secondaryImage = $about->mediaUrl('secondary_image', $assetBase, 'contemporary-right-BGFk98DL.avif');
    $videoUrl = $about->mediaUrl('video', $assetBase, 'uptown-showcase-SvDi3Pul.mp4');
    $ctaUrl = trim((string) ($about->cta_url ?? '')) !== '' ? $about->cta_url : 'javascript:void(0);';
    $affiliatedUrl = trim((string) ($about->affiliated_url ?? '')) !== '' ? $about->affiliated_url : 'javascript://';
@endphp
        <section class="about --is-mobile">
          <div class="about-content about-content__top">
            <div class="about-tagline">
              <span>{{ e($about->tagline_about) }}</span>
            </div>

            <div class="about-heading">
              <span>{{ e($about->heading_line_1) }}</span>
              <span>{{ e($about->heading_line_2) }}</span>
            </div>

            <div class="about-image">
              <img
                src="{{ $centerImage }}"
                alt="ETIHAD hero screen 2"
              />
            </div>

            <div class="about-description">
              <p class="about-description__text">
                <span>{{ e($about->about_para_1_lead) }}</span>{{ e($about->about_para_1_highlight) }}
              </p>

              <p class="about-description__text">
                {{ e($about->about_para_2_lead) }}<span>{{ e($about->about_para_2_highlight) }}</span>
              </p>
            </div>

            <div class="about__cta">
              <a class="CTA-btn" href="{{ $ctaUrl }}">
                <div class="CTA-btn__border">
                </div>
                <div class="CTA-btn__blur">
                </div>
                <div class="CTA-btn__background">
                </div>

                <div class="CTA-btn__inner">
                  <span class="CTA-btn__icon"></span>
                  <span class="CTA-btn__text">{{ e($about->cta_text) }}</span>
                </div>
              </a>
            </div>

            <div class="about-media__wrapper">
              <video src="{{ $videoUrl }}" autoplay muted loop playsinline>
              </video>

              <p class="about__media-text">
                {{ e($about->media_caption_1) }}
              </p>
            </div>
          </div>

          <div class="about-content about-content__bottom" data-fade-out data-fade-out-scale="1">
            <div class="about-tagline">
              <span>{{ e($about->tagline_about) }}</span>
            </div>

            <div class="about-heading">
              <span>{{ e($about->heading_line_1) }}</span>
              <span>{{ e($about->heading_line_2) }}</span>
            </div>

            <div class="about-image">
              <img
                src="{{ $secondaryImage }}"
                alt="ETIHAD hero screen 2"
              />
            </div>

            <div class="about__cta">
              <a class="CTA-btn" href="{{ $ctaUrl }}">
                <div class="CTA-btn__border">
                </div>
                <div class="CTA-btn__blur">
                </div>
                <div class="CTA-btn__background">
                </div>

                <div class="CTA-btn__inner">
                  <span class="CTA-btn__icon"></span>
                  <span class="CTA-btn__text">{{ e($about->cta_text) }}</span>
                </div>
              </a>
            </div>

            <div class="about-description">
              <p class="about-description__text">
                <span>{{ e($about->vision_para_1_highlight) }}</span>{{ e($about->vision_para_1_body) }}
              </p>

              <p class="about-description__text">
                {{ e($about->vision_para_2_lead) }}<span>{{ e($about->vision_para_2_highlight) }}</span>{{ e($about->vision_para_2_body) }}
              </p>
            </div>

            <a
              class="hero__affiliated"
              href="{{ $affiliatedUrl }}"
              target="_blank"
              rel="noopener noreferrer"
            >
              <span class="hero__affiliated-icon"></span>
              <span class="hero__affiliated-text">{{ e($about->affiliated_text) }}</span>
              <span class="hero__affiliated-line"></span>
            </a>
          </div>
        </section>
