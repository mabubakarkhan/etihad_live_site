@php
    $setting = $setting ?? \App\Models\HomepageDealersSectionSetting::instance();
    $dealers = $dealers ?? collect();
@endphp
@if($dealers->isNotEmpty())
        <section class="popular-listings popular-listings--dealers">
          <div class="popular-listings__inner">
            <div class="popular-listings__intro">
              <div class="popular-listings__eyebrow">{{ e($setting->eyebrow) }}</div>
              <div class="popular-listings__heading-row">
                <div>
                  <h2 class="popular-listings__heading">{{ e($setting->title_line_1) }} <span>{{ e($setting->title_highlight) }}</span></h2>
                  <p class="popular-listings__lede">
                    {{ e($setting->description) }}
                  </p>
                </div>

                <div class="popular-listings__controls">
                  <button class="popular-listings__control" type="button" aria-label="Show previous agent" data-dealers-prev>Prev</button>
                  <button class="popular-listings__control" type="button" aria-label="Show next agent" data-dealers-next>Next</button>
                  <a href="{{ route('team') }}" class="popular-listings__view-all">{{ e($setting->view_all_label) }}</a>
                </div>
              </div>
            </div>

            <div class="popular-listings__rail">
              <div class="popular-listings__grid">
@foreach($dealers as $dealer)
@include('partials.homepage-dealers-card', [
    'dealer' => $dealer,
    'badge' => $setting->card_badge,
    'ctaLabel' => $setting->cta_label,
])
@endforeach
              </div>
            </div>

            <div class="popular-listings__footer">
              <div class="popular-listings__note">{{ e($setting->footer_note) }}</div>
              <div class="popular-listings__progress" aria-hidden="true"><span></span></div>
            </div>
          </div>

          <div class="popular-listings__panel-glow"></div>
        </section>
@endif
