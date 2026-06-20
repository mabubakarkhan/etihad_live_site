<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @stack('meta')

    {{-- Front theme CSS (from html template) --}}
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/plugins.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/style.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/db-style.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ asset('theme/css/etihad-front.css') }}">
    @stack('styles')

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('theme/images/favicon_io/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('theme/images/favicon_io/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('theme/images/favicon_io/favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('theme/images/favicon_io/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('theme/images/favicon_io/site.webmanifest') }}">
</head>
<body
    data-base-url="{{ url('/') }}"
    data-theme-base="{{ asset('theme') }}"
    data-wishlist-panel-url="{{ url('/wishlist/panel') }}"
    data-contact-popup-url="{{ route('contact-us', ['popup' => 1]) }}"
>
    <div class="loader-wrap">
        <div class="loader-inner">
            <svg>
                <defs>
                    <filter id="goo">
                        <fegaussianblur in="SourceGraphic" stdDeviation="2" result="blur" />
                        <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 5 -2" result="gooey" />
                        <fecomposite in="SourceGraphic" in2="gooey" operator="atop"/>
                    </filter>
                </defs>
            </svg>
        </div>
    </div>
    <!--loader end-->

    @yield('content')
    @php
        $isPopupEmbed = request()->boolean('popup');
        $floatingCs = \App\Models\ContactSetting::instance();
        $floatingPhoneRaw = $floatingCs->phone ?? '';
        $floatingWhatsappRaw = $floatingCs->whatsapp ?: $floatingPhoneRaw;
        $floatingPhoneClean = preg_replace('/\s+/', '', (string) $floatingPhoneRaw);
        $floatingWhatsappClean = preg_replace('/\D/', '', (string) $floatingWhatsappRaw);
    @endphp
    @if(!$isPopupEmbed)
    <div class="floating-contact-actions" aria-label="Quick contact actions">
        @if($floatingPhoneClean)
        <a href="tel:{{ $floatingPhoneClean }}" class="floating-contact-action is-call" aria-label="Call us" title="Call us"><i class="fa-solid fa-phone"></i></a>
        @endif
        @if($floatingWhatsappClean)
        <a href="https://wa.me/{{ $floatingWhatsappClean }}" target="_blank" rel="noopener" class="floating-contact-action is-whatsapp" aria-label="WhatsApp us" title="WhatsApp us"><i class="fa-brands fa-whatsapp"></i></a>
        @endif
        <button type="button" class="floating-contact-action is-top" id="floating-back-to-top" aria-label="Back to top" title="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
    </div>
    <div class="contact-popup-overlay" id="contact-popup-overlay" aria-hidden="true">
        <div class="contact-popup-modal" role="dialog" aria-modal="true" aria-label="Contact us form popup">
            <button type="button" class="contact-popup-close" id="contact-popup-close" aria-label="Close contact popup"><i class="fa-regular fa-xmark"></i></button>
            <iframe class="contact-popup-frame" id="contact-popup-frame" title="Contact Us"></iframe>
        </div>
    </div>
    @endif

    {{-- Front theme JS: jQuery once globally; site logic in etihad-common.js --}}
    <script src="{{ asset('theme/js/jquery.min.js') }}"></script>
    <script src="{{ asset('theme/js/plugins.js') }}"></script>
    <script src="{{ asset('theme/js/scripts.js') }}"></script>
    <script src="{{ asset('theme/js/etihad-map-styles.js') }}"></script>
    <script src="{{ asset('theme/js/etihad-common.js') }}"></script>
    @stack('scripts')
</body>
</html>
