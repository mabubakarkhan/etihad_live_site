@php
    $cs = $cs ?? \App\Models\ContactSetting::instance();
    $ctaHeading = $ctaHeading ?? 'Need help finding the right place?';
    $ctaText = $ctaText ?? 'Speak with our team for expert guidance on listings, projects, and investment opportunities.';
    $primaryLabel = $primaryLabel ?? 'Contact Us';
    $primaryUrl = $primaryUrl ?? route('contact-us');
    $secondaryLabel = $secondaryLabel ?? null;
    $secondaryUrl = $secondaryUrl ?? null;
@endphp
<section class="listing-projects-cta" aria-labelledby="listing-projects-cta-title">
    <div class="listing-projects-cta__inner">
        <div class="listing-projects-cta__copy">
            <h3 id="listing-projects-cta-title">{{ $ctaHeading }}</h3>
            <p>{{ $ctaText }}</p>
            @if(!empty($cs->phone) || !empty($cs->email))
            <ul class="listing-projects-cta__meta">
                @if(!empty($cs->phone))
                <li><i class="fa-light fa-phone"></i> <a href="{{ contact_tel_href($cs->phone) }}">{{ $cs->phone }}</a></li>
                @endif
                @if(!empty($cs->email))
                <li><i class="fa-light fa-envelope"></i> <a href="mailto:{{ e($cs->email) }}">{{ $cs->email }}</a></li>
                @endif
            </ul>
            @endif
        </div>
        <div class="listing-projects-cta__actions">
            <a href="{{ $primaryUrl }}" class="listing-projects-cta__btn listing-projects-cta__btn--primary">{{ $primaryLabel }}</a>
            @if($secondaryLabel && $secondaryUrl)
            <a href="{{ $secondaryUrl }}" class="listing-projects-cta__btn listing-projects-cta__btn--secondary">{{ $secondaryLabel }}</a>
            @endif
            @if(!empty($cs->whatsapp))
            <a href="{{ contact_whatsapp_href($cs->whatsapp) }}" class="listing-projects-cta__btn listing-projects-cta__btn--whatsapp" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
            @endif
        </div>
    </div>
</section>
