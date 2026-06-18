@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
@endphp
          <div class="footer-socials">
            <h4 class="footer-socials__title">
              SOCIAL MEDIA
            </h4>

            <div class="footer-socials__container">
@include('partials.homepage-social-links', ['cs' => $cs, 'linkClass' => 'footer-socials__link'])
            </div>
          </div>
