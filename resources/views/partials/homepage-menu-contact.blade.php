@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $linkClass = $linkClass ?? 'menu-socials__link';
@endphp
                <div class="menu-socials__container">
@include('partials.homepage-social-links', ['cs' => $cs, 'linkClass' => $linkClass])
                </div>

              <a class="CTA-btn" href="{{ route('projects') }}">
                <div class="CTA-btn__border">
                </div>
                <div class="CTA-btn__blur">
                </div>
                <div class="CTA-btn__background">
                </div>

                <div class="CTA-btn__inner">
                  <span class="CTA-btn__icon"></span>
                  <span class="CTA-btn__text">Want to know about our projects</span>
                </div>
              </a>

              <p class="menu-terms">
@if(!empty($cs->email))
                <a href="mailto:{{ e($cs->email) }}" class="menu-terms__email">{{ e($cs->email) }}</a>
@endif
@if(!empty($cs->phone))
                <a href="{{ contact_tel_href($cs->phone) }}" class="menu-terms__tel">{{ e($cs->phone) }}</a>
@endif
@if(!empty($cs->whatsapp))
                <a href="{{ contact_whatsapp_href($cs->whatsapp) }}" class="menu-terms__tel" target="_blank" rel="noopener noreferrer">WhatsApp</a>
@endif
              </p>
