@php
    if (request()->boolean('popup')) {
        return;
    }
    try {
        $fvp = \App\Models\FirstVisitPopupSetting::instance();
    } catch (\Throwable $e) {
        $fvp = null;
    }
    $fvpPayload = $fvp ? $fvp->toFrontPayload() : [
        'enabled' => false,
        'trackUrl' => route('site-analytics.track'),
        'delayMs' => 0,
        'showLogo' => true,
    ];

    // Homepage include can pass fvpSurface=home; portal view/route uses dark theme + homepage logo
    $isPortal = ($fvpSurface ?? null) === 'portal'
        || request()->routeIs('portal')
        || request()->is('portal')
        || request()->is('portal/*');
    $isHome = ($fvpSurface ?? null) === 'home';
    $themeClass = $isPortal ? 'etihad-fvp--dark' : 'etihad-fvp--light';

    // Portal only: homepage/assets/logo.png | everywhere else: theme logo
    if ($isPortal) {
        $fvpPayload['logo'] = asset('homepage/assets/logo.png');
        $fvpPayload['showLogo'] = true;
    } else {
        $fvpPayload['logo'] = asset('theme/images/logo.png');
    }
    $fvpPayload['theme'] = $isPortal ? 'dark' : 'light';
    $fvpPayload['surface'] = $isPortal ? 'portal' : ($isHome ? 'home' : 'site');

    $fvpHasBg = ! empty($fvpPayload['bg']);
    $fvpShowUi = ! empty($fvpPayload['enabled']);
@endphp
<link rel="stylesheet" href="{{ asset('theme/css/first-visit-popup.css') }}?v=20260728m">
@if($fvpShowUi)
<div
    id="etihad-fvp"
    class="etihad-fvp {{ $themeClass }}"
    hidden
    aria-hidden="true"
    role="dialog"
    aria-modal="true"
    aria-labelledby="etihad-fvp-title"
    data-fvp='@json($fvpPayload)'
>
    <div class="etihad-fvp__backdrop" data-fvp-close tabindex="-1"></div>
    <div class="etihad-fvp__stage" id="etihad-fvp-stage">
        <div class="etihad-fvp__card {{ $fvpHasBg ? 'has-bg' : 'no-bg' }}" id="etihad-fvp-card">
            <button type="button" class="etihad-fvp__close" data-fvp-close aria-label="Close">&times;</button>

            <div class="etihad-fvp__face etihad-fvp__face--front" @if($fvpHasBg) style="--fvp-bg:url('{{ $fvpPayload['bg'] }}')" @endif>
                <div class="etihad-fvp__front-inner">
                    @if(!empty($fvpPayload['showLogo']))
                        <img class="etihad-fvp__logo" src="{{ $fvpPayload['logo'] }}" alt="Etihad" width="180" height="60" decoding="async">
                    @endif
                    @if(($fvpPayload['eyebrow'] ?? '') !== '')
                        <p class="etihad-fvp__eyebrow">{{ $fvpPayload['eyebrow'] }}</p>
                    @endif
                    @if(($fvpPayload['subheading'] ?? '') !== '')
                        <p class="etihad-fvp__sub">{{ $fvpPayload['subheading'] }}</p>
                    @endif
                    @if(($fvpPayload['heading'] ?? '') !== '')
                        <h2 class="etihad-fvp__title" id="etihad-fvp-title">{{ $fvpPayload['heading'] }}</h2>
                    @endif
                    @if(($fvpPayload['body'] ?? '') !== '')
                        <p class="etihad-fvp__body">{{ $fvpPayload['body'] }}</p>
                    @endif
                    <button type="button" class="etihad-fvp__cta" id="etihad-fvp-cta">{{ $fvpPayload['cta'] ?? 'Contact Us' }}</button>
                </div>
            </div>

            <div class="etihad-fvp__face etihad-fvp__face--back">
                <div class="etihad-fvp__back-inner">
                    @if(!empty($fvpPayload['showLogo']))
                        <img class="etihad-fvp__logo etihad-fvp__logo--sm" src="{{ $fvpPayload['logo'] }}" alt="Etihad" width="140" height="48" decoding="async">
                    @endif
                    <h3 class="etihad-fvp__form-title">{{ $fvpPayload['formHeading'] ?? 'Get in touch' }}</h3>
                    <p class="etihad-fvp__form-lead">Share your details and our advisors will reach out.</p>
                    <form class="etihad-fvp__form" id="etihad-fvp-form" method="post" action="{{ $fvpPayload['submitUrl'] ?? '#' }}" novalidate>
                        @csrf
                        <input type="text" name="name" placeholder="Name" required autocomplete="name" maxlength="255">
                        <input type="tel" name="phone" placeholder="Phone" required autocomplete="tel" maxlength="60">
                        <input type="text" name="city" placeholder="City" required autocomplete="address-level2" maxlength="120">
                        <div class="etihad-fvp__msg" id="etihad-fvp-msg" aria-live="polite"></div>
                        <button type="submit" class="etihad-fvp__submit" id="etihad-fvp-submit">{{ $fvpPayload['formSubmit'] ?? 'Submit' }}</button>
                        <button type="button" class="etihad-fvp__back-btn" id="etihad-fvp-back">Back</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div id="etihad-fvp-track" hidden data-fvp='@json($fvpPayload)'></div>
@endif
<script>
window.__ETIHAD_FVP__ = @json($fvpPayload);
</script>
<script src="{{ asset('theme/js/first-visit-popup.js') }}?v=20260728n"></script>
