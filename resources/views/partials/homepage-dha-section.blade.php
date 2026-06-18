@php
    $setting = $setting ?? \App\Models\HomepageDhaSectionSetting::instance();
    $phases = $phases ?? collect();
@endphp
        <section class="dha-showcase">
          <div class="dha-showcase__inner">
            <div class="dha-showcase__header">
              <div class="dha-showcase__eyebrow">{{ e($setting->eyebrow) }}</div>
              <div class="dha-showcase__heading-row">
                <div>
                  <h2 class="dha-showcase__title">{{ e($setting->title_line_1) }} <span>{{ e($setting->title_highlight) }}</span></h2>
                  <p class="dha-showcase__text">
                    {{ e($setting->description) }}
                  </p>
                </div>

                <div class="dha-showcase__controls">
                  <button class="dha-showcase__control" type="button" aria-label="Show previous DHA phase" data-dha-prev>Prev</button>
                  <button class="dha-showcase__control" type="button" aria-label="Show next DHA phase" data-dha-next>Next</button>
                </div>
              </div>
            </div>

            <div class="dha-showcase__rail">
              <div class="dha-showcase__cards">
                @include('partials.homepage-dha-showcase-cards', ['phases' => $phases])
              </div>
            </div>

            <div class="dha-showcase__footer">
              <div class="dha-showcase__note">{{ e($setting->footer_note) }}</div>
              <div class="dha-showcase__progress" aria-hidden="true"><span></span></div>
            </div>
          </div>

          <div class="dha-showcase__orb dha-showcase__orb--one"></div>
          <div class="dha-showcase__orb dha-showcase__orb--two"></div>
        </section>
