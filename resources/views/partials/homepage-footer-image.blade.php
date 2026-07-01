@php
    $footer = $footer ?? \App\Models\HomepageFooterSetting::instance();
    $assetBase = $assetBase ?? rtrim(request()->getSchemeAndHttpHost() . request()->getBaseUrl(), '/') . '/homepage/';
    $hasCustomImage = trim((string) ($footer->footer_image ?? '')) !== '';
    $desktopUrl = homepage_asset_url($footer->footer_image ?? null, $assetBase, 'footer-img-qOMPXiNy.webp');
    $mobileUrl = $hasCustomImage
        ? $desktopUrl
        : rtrim($assetBase, '/') . '/assets/footer-img-mobile-DOlkbjJO.webp';
    $tabletUrl = $hasCustomImage
        ? $desktopUrl
        : rtrim($assetBase, '/') . '/assets/footer-img-tab-Bb9g1T-Q.webp';
    $alt = trim((string) ($footer->footer_image_alt ?? '')) !== ''
        ? $footer->footer_image_alt
        : 'ETIHAD footer image';
@endphp
          <div class="footer-image__wrapper">
            <picture>
              <source
                srcset="{{ $mobileUrl }}"
                type="image/webp"
                media="(max-width: 481px) and (orientation: portrait)"
              />
              <source
                srcset="{{ $tabletUrl }}"
                type="image/webp"
                media="(min-width: 482px) and (max-width: 1024px) and (orientation: portrait)"
              />

              <img src="{{ $desktopUrl }}" alt="{{ $alt }}" />
            </picture>

            <button class="footer-back-to-top" data-scrollto="0">
              <span class="footer-back-to-top__background"></span>
              <span class="footer-back-to-top__text">Back to top</span>
            </button>
          </div>
