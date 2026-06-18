@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $websiteHref = contact_website_href($cs->website ?? '');
    $websiteLabel = contact_website_label($cs->website ?? '');
@endphp
          <div class="footer-contact">
            <h4 class="footer-contact__title">
              Contact
            </h4>

@if(!empty($cs->email))
            <a class="footer-contact__link" href="mailto:{{ e($cs->email) }}">{{ e($cs->email) }}</a>
@endif
@if(!empty($cs->phone))
            <a class="footer-contact__link" href="{{ contact_tel_href($cs->phone) }}">{{ e($cs->phone) }}</a>
@endif
@if(!empty($cs->whatsapp))
            <a class="footer-contact__link" href="{{ contact_whatsapp_href($cs->whatsapp) }}" target="_blank" rel="noopener noreferrer">WhatsApp: {{ e($cs->whatsapp) }}</a>
@endif
@if($websiteHref !== '')
            <a class="footer-contact__link" href="{{ e($websiteHref) }}" target="_blank" rel="noopener noreferrer">{{ e($websiteLabel) }}</a>
@endif
          </div>
