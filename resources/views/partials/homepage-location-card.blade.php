@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $locationSection = $locationSection ?? \App\Models\HomepageLocationSectionSetting::instance();
    $assetBase = $assetBase ?? rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') . '/homepage/';
    $cardStoragePath = $locationSection->card_image ?? null;
    $locationImage = homepage_asset_url($cardStoragePath, $assetBase, 'menu-image-o9Z7G9pj.webp');
    $mapHref = contact_map_href($cs);
    $officeTitle = trim((string) ($cs->office_title ?? '')) ?: 'LAHORE OFFICE';
    $officeNotes = trim((string) ($cs->timings ?? ''));
@endphp
          <div class="location-card">
            <img
              class="location-card__image"
              src="{{ $locationImage }}"
              alt="{{ e($officeTitle) }} location image"
              loading="lazy"
            />

            <div class="location-card__content">
              <span class="location-card__content-title">Etihad</span>
              <h4 class="location-card__content-name">
                {{ e($officeTitle) }}
              </h4>

@if(!empty($cs->address))
              <div class="location-card__content-address">
                <span class="location-card__address-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="19" height="23" viewBox="0 0 19 23" fill="none">
                    <path d="M8.60115 22.3252L8.32054 22.1354C6.91612 21.1539 5.6111 20.0377 4.42408 18.8026C2.33846 16.6202 0 13.3487 0 9.48744C0 4.73715 3.85992 0 9.5 0C15.1401 0 19 4.73715 19 9.4889C19 13.3501 16.6615 16.6216 14.5759 18.8012C13.3889 20.0362 12.0839 21.1525 10.6795 22.1339C10.5606 22.2167 10.467 22.2799 10.3988 22.3237C10.1022 22.5208 9.79961 22.7077 9.5 22.8974C9.20038 22.7077 8.89785 22.5208 8.60115 22.3252Z" fill="#1DBF73" />
                    <circle cx="9.50078" cy="9.4998" r="2.67949" fill="white" />
                  </svg>
                </span>

                <p class="location-card__address-text">
                  {{ e($cs->address) }}
                </p>
              </div>
@endif

              <div class="location-card_content-line">
              </div>

              <div class="location-card__content-office">
                <span class="location-card__office-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="19" height="26" viewBox="0 0 19 26" fill="none">
                    <g clip-path="url(#clip0_1409_647)">
                      <path d="M11.9067 25.3333V19H7.09333V25.3333H0V0H18.8108L19 0.189156V25.1425L18.8108 25.3316H11.9067V25.3333ZM6.33333 4.68667H3.16667V7.98H6.33333V4.68667ZM15.8333 4.68667H12.6667V7.98H15.8333V4.68667ZM11.1467 4.81333H7.85333V7.78916L8.04249 7.97831H10.9558L11.145 7.78916V4.81333H11.1467ZM6.33333 14.3133V11.2092L6.14418 11.02H3.35751L3.16836 11.2092V14.3133H6.33502H6.33333ZM8.04249 11.02L7.85333 11.2092V14.1225L8.04249 14.3116H10.9558L11.145 14.1225V11.2092L10.9558 11.02H8.04249ZM15.8333 14.3133V11.2092L15.6442 11.02H12.8575L12.6684 11.2092V14.3133H15.835H15.8333Z" fill="#bb9c46" />
                    </g>
                    <defs>
                      <clipPath id="clip0_1409_647">
                        <rect width="19" height="25.3333" fill="white" />
                      </clipPath>
                    </defs>
                  </svg>
                </span>

                <div class="location-card__block">
                  <p class="location-card__office-text">Office</p>
                  <p class="location-card__office-text">
                    @if($officeNotes !== '')
                      {{ e($officeNotes) }}
                    @else
                      Visiting us? Google Maps will get you here easily. Just search our location!
                    @endif
                  </p>
                </div>
              </div>

@if($mapHref !== '')
              <a
                class="CTA-btn"
                href="{{ e($mapHref) }}"
                target="_blank"
                rel="noopener noreferrer"
              >
                <div class="CTA-btn__border"></div>
                <div class="CTA-btn__blur"></div>
                <div class="CTA-btn__background"></div>
                <div class="CTA-btn__inner">
                  <span class="CTA-btn__icon"></span>
                  <span class="CTA-btn__text">Take me there</span>
                </div>
              </a>
@endif
            </div>
          </div>
