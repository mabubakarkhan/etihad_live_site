@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $phoneClean = preg_replace('/\s+/', '', (string) ($cs->phone ?? ''));
    $whatsappHref = ! empty($cs->whatsapp) ? contact_whatsapp_href($cs->whatsapp) : '';
@endphp
<div class="portal-why-contact">
    <h4 class="portal-why-contact__title">Get In Touch</h4>
    <ul class="portal-why-contact__list">
        @if(!empty($cs->phone))
        <li>
            <span class="portal-why-contact__icon"><i class="fa-light fa-phone"></i></span>
            <span class="portal-why-contact__body">
                <span class="portal-why-contact__label">Phone</span>
                <a href="{{ contact_tel_href($cs->phone) }}">{{ $cs->phone }}</a>
            </span>
        </li>
        @endif
        @if(!empty($cs->email))
        <li>
            <span class="portal-why-contact__icon"><i class="fa-light fa-envelope"></i></span>
            <span class="portal-why-contact__body">
                <span class="portal-why-contact__label">Email</span>
                <a href="mailto:{{ e($cs->email) }}">{{ $cs->email }}</a>
            </span>
        </li>
        @endif
        @if(!empty($cs->address))
        <li>
            <span class="portal-why-contact__icon"><i class="fa-light fa-location-dot"></i></span>
            <span class="portal-why-contact__body">
                <span class="portal-why-contact__label">Address</span>
                <span class="portal-why-contact__text">{{ $cs->address }}</span>
            </span>
        </li>
        @endif
        @if(!empty($cs->whatsapp))
        <li>
            <span class="portal-why-contact__icon"><i class="fa-brands fa-whatsapp"></i></span>
            <span class="portal-why-contact__body">
                <span class="portal-why-contact__label">WhatsApp</span>
                <a href="{{ $whatsappHref }}" target="_blank" rel="noopener noreferrer">{{ $cs->whatsapp }}</a>
            </span>
        </li>
        @endif
        @if(!empty($cs->timings))
        <li>
            <span class="portal-why-contact__icon"><i class="fa-light fa-clock"></i></span>
            <span class="portal-why-contact__body">
                <span class="portal-why-contact__label">Office hours</span>
                <span class="portal-why-contact__text">{{ $cs->timings }}</span>
            </span>
        </li>
        @endif
    </ul>
    <a href="{{ route('contact-us') }}" class="commentssubmit commentssubmit_fw portal-why-contact__btn">Contact Us</a>
</div>
