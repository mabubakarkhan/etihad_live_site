@php
    $choice = $choice ?? \App\Models\HomepageChoiceSetting::instance();
    $slides = $slides ?? \App\Models\HomepageChoiceSetting::orderedSlides();
    $assetBase = $assetBase ?? rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') . '/homepage/';
    $slideTotal = max($slides->count(), 1);
    $backgroundLandscape = $choice->backgroundUrl($assetBase);
    $backgroundPortrait = $choice->backgroundUrl($assetBase, true);
@endphp
        <section class="choice" data-fade-out data-fade-out-scale="1">
          <picture>
            <source
              media="(orientation: portrait)"
              srcset="{{ $backgroundPortrait }}"
            />
            <img
              class="choice__background"
              src="{{ $backgroundLandscape }}"
              alt="ETIHAD choice background"
            />
          </picture>

          <div
            class="choice-swiper-container"
            data-swiper-class="choice"
            data-swiper-slides-per-view="auto"
            data-swiper-space-between="0"
            data-swiper-initial-slide="1"
            data-swiper-slides-per-view-mobile="auto"
            data-swiper-initial-slide-mobile="0"
            data-swiper-centered-mobile="true"
            data-swiper-space-between-mobile="20"
          >
            <div class="choice-swiper-wrapper">
@foreach($slides as $index => $slide)
@php
    $cardImageUrl = ! empty($slide->card_image) ? public_storage_url($slide->card_image) : null;
@endphp
              <div class="choice-swiper-slide{{ $cardImageUrl ? ' choice-swiper-slide--has-image' : '' }}"@if($cardImageUrl) style="--choice-slide-bg-image: url('{{ e($cardImageUrl) }}');"@endif>
                <div class="choice-swiper-slide__number">
                  <span>{{ $index + 1 }}</span>/{{ $slideTotal }}
                </div>

                <div class="choice-swiper-slide__heading">
                  <span class="choice-swiper-slide__heading-icon"></span>
                  <span class="choice-swiper-slide__heading-text">{{ e($slide->heading_text) }}</span>
                </div>

                <p class="choice-swiper-slide__description">
                  <span data-choice-to="{{ (int) $slide->counter_to }}">{{ e($slide->counter_text) }}</span>{{ e($slide->description) }}
                </p>
              </div>
@endforeach
            </div>

            <div class="choice-swiper-pagination">
            </div>
          </div>

          <h3 class="choice__heading">
            <span>{{ e($choice->section_heading) }}</span>
          </h3>

          <div class="choice__scroll-wrapper">
            <div class="choice__scroll-background">
            </div>
            <span class="choice__scroll-text" data-text-desktop="{{ e($choice->scroll_label_desktop) }}" data-text-mobile="{{ e($choice->scroll_label_mobile) }}"></span>
          </div>
        </section>
