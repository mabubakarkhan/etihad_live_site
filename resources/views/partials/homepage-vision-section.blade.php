@php
    $vision = $vision ?? \App\Models\HomepageVisionSetting::instance();
    $assetBase = $assetBase ?? rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') . '/homepage/';
    $ceoImage = homepage_asset_url($vision->ceo_image ?? null, $assetBase, 'ceo-zeeshan-butt.png');
    $ceoAlt = trim((string) ($vision->ceo_name ?? '')) !== ''
        ? 'CEO - ' . $vision->ceo_name
        : 'CEO photo';
@endphp
        <section class="ceo-message">
          <div class="ceo-message__container">
            <div class="ceo-message__content">
              <div class="ceo-message__tagline">
                <span>{{ e($vision->tagline) }}</span>
              </div>

              <div class="ceo-message__heading">
                <span>{{ e($vision->heading_line_1) }}</span>
                <span>{{ e($vision->heading_line_2) }}</span>
              </div>

              <div class="ceo-message__split">
                <div class="ceo-message__image-container">
                  <div class="ceo-message__image-wrapper">
                    <img src="{{ $ceoImage }}" alt="{{ e($ceoAlt) }}" class="ceo-message__image" />
                    <div class="ceo-message__image-border"></div>
                    <div class="ceo-message__image-accent"></div>
                  </div>
                </div>

                <div class="ceo-message__text-container">
                  <div class="ceo-message__description">
@if(!empty($vision->message_paragraph_1))
                    <p class="ceo-message__text">
                      <span>{{ e($vision->message_paragraph_1) }}</span>
                    </p>
@endif
@if(!empty($vision->message_paragraph_2_body) || !empty($vision->message_paragraph_2_highlight))
                    <p class="ceo-message__text">
@if(!empty($vision->message_paragraph_2_highlight))
                      <span>{{ e($vision->message_paragraph_2_highlight) }}</span>
@endif
@if(!empty($vision->message_paragraph_2_body)) {{ e($vision->message_paragraph_2_body) }}@endif
                    </p>
@endif
                  </div>

                  <div class="ceo-message__signature">
@if(!empty($vision->ceo_name))
                    <div class="ceo-message__name">{{ e($vision->ceo_name) }}</div>
@endif
@if(!empty($vision->ceo_title))
                    <div class="ceo-message__title">{{ e($vision->ceo_title) }}</div>
@endif
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!--  decorative elements  -->
          <div class="ceo-message__glow"></div>
          <img
            src="assets/lines-2-y9dv42Ce.webp"
            alt="decorative lines pattern"
            class="ceo-message__lines"
          />
        </section>
