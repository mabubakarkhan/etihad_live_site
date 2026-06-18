@php
    $about = $about ?? \App\Models\HomepageAboutSetting::instance();
    $assetBase = $assetBase ?? rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') . '/homepage/';
    $heroImage = $heroImage ?? ($assetBase . 'assets/hero-screen-1-D7I92d4H.webp');
    $centerImage = $about->mediaUrl('center_image', $assetBase, 'hero-background-Rk6TMBAb.webp');
    $secondaryImage = $about->mediaUrl('secondary_image', $assetBase, 'contemporary-right-BGFk98DL.avif');
    $videoUrl = $about->mediaUrl('video', $assetBase, 'uptown-showcase-SvDi3Pul.mp4');
    $ctaUrl = trim((string) ($about->cta_url ?? '')) !== '' ? $about->cta_url : 'javascript:void(0);';
    $affiliatedUrl = trim((string) ($about->affiliated_url ?? '')) !== '' ? $about->affiliated_url : 'javascript://';
@endphp
          <div class="hero__screen hero__screen-2">
            <!-- hero clip  -->
            <div class="hero__background-clip">
              <div class="hero__image-wrapper hero__image-wrapper-1">
                <img
                  class="hero__image-1"
                  src="{{ $heroImage }}"
                  alt="ETIHAD hero screen 2"
                />

                <img
                  class="hero__image-2"
                  src="{{ $heroImage }}"
                  alt="ETIHAD hero screen 3"
                />
                
              </div>
            </div>

            <!-- hero lines  -->
            <img
              src="assets/lines-1-Qr38Z-nF.webp"
              alt="decorative lines pattern"
              class="hero__screen-2__lines"
            />

            <!-- hero content wrapper -->
            <div class="hero__content">
              <!--  flex wrapper  -->
              <div class="hero__flex">
                <!-- left content  -->
                <div class="hero__flex-left">
                  <div class="hero__tagline">
                    <span>{{ e($about->tagline_about) }}</span>
                    <span>{{ e($about->tagline_vision) }}</span>
                  </div>

                  <div class="hero__heading">
                    <span>{{ e($about->heading_line_1) }}</span>
                    <span>{{ e($about->heading_line_2) }}</span>
                  </div>

                  <div class="hero__media">
                    <video src="{{ $videoUrl }}" autoplay muted loop playsinline>
                    </video>

                    <div class="hero__media-text">
@if(!empty($about->media_caption_1))
                      <p class="hero__media-text-1">
                        {{ e($about->media_caption_1) }}
                      </p>
@endif
@if(!empty($about->media_caption_2))
                      <p class="hero__media-text-2">
                        {{ e($about->media_caption_2) }}
                      </p>
@endif
                    </div>
                  </div>
                </div>

                <!-- right content  -->
                <div class="hero__flex-right">
                  <div class="hero__flex-right-overflow">
                    <!-- first text block  -->
                    <div class="hero__flex-block hero__flex-block-1">
                      <p class="hero__description">
                        {{ e($about->about_para_1_lead) }}<span>{{ e($about->about_para_1_highlight) }}</span>
                      </p>

                      <p class="hero__description">
                        {{ e($about->about_para_2_lead) }}<span>{{ e($about->about_para_2_highlight) }}</span>
                      </p>
                    </div>

                    <!-- second text block  -->
                    <div class="hero__flex-block hero__flex-block-2">
                      <p class="hero__description">
                        <span>{{ e($about->vision_para_1_highlight) }}</span>{{ e($about->vision_para_1_body) }}
                      </p>

                      <p class="hero__description">
                        {{ e($about->vision_para_2_lead) }}<span>{{ e($about->vision_para_2_highlight) }}</span>{{ e($about->vision_para_2_body) }}
                      </p>

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
                    
                  </div>
                </div>

                <!-- second image wrapper  -->
                <div class="hero__image-wrapper hero__image-wrapper-2">
                  <img
                    class="hero__image-1"
                    src="{{ $centerImage }}"
                    alt="ETIHAD hero screen 2"
                  />

                  <img
                    class="hero__image-2"
                    src="{{ $secondaryImage }}"
                    alt="ETIHAD hero screen 3"
                  />
                </div>
              </div>
            </div>

            <!-- hero rotating line  -->
            <div class="hero__rotating-line">
            </div>

            <!-- hero screen 2 cta button  -->
            <div class="hero__cta">
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
          </div>
