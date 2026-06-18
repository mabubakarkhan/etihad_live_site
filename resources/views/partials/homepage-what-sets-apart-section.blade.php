@php
    $apart = $apart ?? \App\Models\HomepageWhatSetsApartSetting::instance();
    $cards = $cards ?? \App\Models\HomepageWhatSetsApartSetting::orderedCards();
@endphp
        <section class="what-sets-apart">
          <div class="animated-bg-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
          </div>
          <div class="what-sets-apart__container">
            <div class="what-sets-apart__header">
              <h2 class="what-sets-apart__title">{{ e($apart->title_line_1) }} <span>{{ e($apart->title_highlight) }}</span></h2>
              <p class="what-sets-apart__subtitle">
                {{ e($apart->subtitle) }}
              </p>
            </div>

            <div class="what-sets-apart__cards-grid">
@foreach($cards as $index => $card)
              <div class="what-sets-apart__card" style="--delay: {{ $index * 0.1 }}s;">
                <div class="what-sets-apart__card-icon">
@if($card->iconImageUrl())
                  <img src="{{ $card->iconImageUrl() }}" alt="" style="width:100%;height:100%;object-fit:contain" />
@elseif(!empty($card->icon_svg))
                  {!! $card->icon_svg !!}
@endif
                </div>
                <h3 class="what-sets-apart__card-title">{{ e($card->title) }}</h3>
                <p class="what-sets-apart__card-description">
                  {{ e($card->description) }}
                </p>
              </div>
@endforeach
            </div>
          </div>
        </section>
