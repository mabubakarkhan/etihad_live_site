@php
    $why = $why ?? \App\Models\HomepageWhySetting::instance();
@endphp
        <section class="why">
          <!-- floating images wrapper (targets for scroll animation from Contemporary section) -->
          <div class="why__floating-wrapper">
            <div class="why__floating-center">
            </div>
            <div class="why__floating-right">
            </div>
            <div class="why__floating-left">
            </div>
            <div class="why__floating-center-back">
            </div>
          </div>

          <!-- why flex wrapper  -->
          <div class="why-flex">
            <h3 class="why__heading">
              {{ e($why->heading_line_1) }} <span>{{ e($why->heading_line_2) }}</span>
            </h3>
            <p class="why__description">
              {{ e($why->description) }}
            </p>

            <div class="why-scroll__wrapper">
              <div class="why-scroll__square-1">
              </div>
              <div class="why-scroll__square-2">
              </div>
              <div class="why-scroll__square-3">
              </div>

              <div class="why-scroll__text">
                {{ e($why->scroll_label) }}
              </div>
            </div>
          </div>

          <!-- why lines  -->
          <img
            src="assets/lines-1-Qr38Z-nF.webp"
            alt="decorative lines pattern"
            class="why__lines"
          />
        </section>
