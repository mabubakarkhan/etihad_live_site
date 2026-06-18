@php
    $socials = [
        ['url' => $cs->facebook ?? null, 'icon' => 'fa-brands fa-facebook-f'],
        ['url' => $cs->instagram ?? null, 'icon' => 'fa-brands fa-instagram'],
        ['url' => $cs->linkedin ?? null, 'icon' => 'fa-brands fa-linkedin-in'],
        ['url' => $cs->youtube ?? null, 'icon' => 'fa-brands fa-youtube'],
        ['url' => $cs->twitter ?? null, 'icon' => 'fa-brands fa-x-twitter'],
        ['url' => $cs->tiktok ?? null, 'icon' => 'fa-brands fa-tiktok'],
    ];
    $cleanPhone = preg_replace('/\s+/', '', (string) ($cs->phone ?? ''));
@endphp

<div class="wrapper {{ !empty($isPopup) ? 'contact-popup-mode' : '' }}">
    <div class="content">
        @if(empty($isPopup))
        <div class="section hero-section hero-section_sin">
            <div class="hero-section-wrap">
                <div class="hero-section-wrap-item">
                    <div class="container">
                        <div class="hero-section-container">
                            <div class="hero-section-title">
                                <h1>{{ $cmsPage->heading ?? 'Our Contacts' }}</h1>
                                <h5>{{ !empty($cmsPage->content) ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 120) : 'Get in touch with our team for projects, listings, and support.' }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="hs-scroll-down-wrap">
                        <div class="scroll-down-item">
                            <div class="mousey"><div class="scroller"></div></div>
                            <span>Scroll Down To Discover</span>
                        </div>
                        <div class="svg-corner svg-corner_white contact-hero-corner-right"></div>
                        <div class="svg-corner svg-corner_white contact-hero-corner-left"></div>
                    </div>
                    <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                        <div class="bg" data-bg="{{ asset('theme/images/bg/14.jpg') }}" data-scrollax="properties: { translateY: '30%' }"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="container">
            @if(empty($isPopup))
            <div class="breadcrumbs-list bl_flat">
                <a href="{{ url('/') }}">Home</a><a href="#">Pages</a><span>Contact Us</span>
                <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
            </div>
            @endif

            <div class="main-content ms_vir_height">
                <div class="boxed-container">
                    <div class="contacts-cards-wrap">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="contacts-card-item">
                                    <i class="fa-regular fa-location-dot"></i>
                                    <span>Our Location</span>
                                    <p>Visit our office for consultations and on-site support.</p>
                                    <a href="#">{{ $cs->address ?: 'Location details unavailable' }}</a>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="contacts-card-item">
                                    <i class="fa-regular fa-phone-rotary"></i>
                                    <span>Our Phone</span>
                                    <p>Call us directly for quick assistance.</p>
                                    <a href="tel:{{ $cleanPhone }}">{{ $cs->phone ?: 'N/A' }}</a>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="contacts-card-item">
                                    <i class="fa-regular fa-mailbox"></i>
                                    <span>Our Mail</span>
                                    <p>Send your queries and we will respond promptly.</p>
                                    <a href="mailto:{{ $cs->email }}">{{ $cs->email ?: 'N/A' }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-7">
                            <div class="contacts-opt-wrap">
                                <div class="contact-wh_title">Working Hours</div>
                                <div class="contact-wh">
                                    <div class="contact-wh-item">Office Timings:<strong> {{ $cs->timings ?: '9am - 6pm' }}</strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="contacts-opt-wrap">
                                <div class="contact-social">
                                    <span class="cs-title">Find us on: </span>
                                    <div class="contact-social-container">
                                        @foreach($socials as $social)
                                            @if(!empty($social['url']))
                                                <a href="{{ $social['url'] }}" target="_blank" rel="noopener"><i class="{{ $social['icon'] }}"></i></a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="contacts-form-wrap">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="boxed-content">
                                    <div class="boxed-content-title">
                                        <h3>Get In Touch</h3>
                                    </div>
                                    <div class="boxed-content-item">
                                        <div class="comment-form custom-form contactform-wrap">
                                            <form id="contact-us-form" method="post" action="{{ route('contact-us.submit') }}" class="comment-form">
                                                @csrf
                                                <fieldset>
                                                    <div id="contact-form-status" class="contact-form-status"></div>
                                                    <div class="row">
                                                        <div class="col-sm-6">
                                                            <div class="cs-intputwrap">
                                                                <i class="fa-light fa-user"></i>
                                                                <input name="name" type="text" placeholder="Your name *" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="cs-intputwrap">
                                                                <i class="fa-light fa-envelope"></i>
                                                                <input type="email" name="email" placeholder="Email Address">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="cs-intputwrap">
                                                        <i class="fa-light fa-phone"></i>
                                                        <input type="text" name="phone" placeholder="Phone">
                                                    </div>
                                                    <textarea name="message" cols="40" rows="5" placeholder="Your Message *" required></textarea>
                                                    <button type="submit" class="commentssubmit contact-submit-btn" id="contact-submit-btn">Send Message</button>
                                                </fieldset>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                @if(!empty($cs->latitude) && !empty($cs->longitude))
                                    <div class="map-container mapC_vis3">
                                        <div id="contact-map" class="single-map-container fs-wrapper"></div>
                                        <div class="scrollContorl"></div>
                                    </div>
                                @else
                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            <div class="list-single-facts">
                                                @if(!empty($cs->address))<div><span>Address</span><p>{{ $cs->address }}</p></div>@endif
                                                @if(!empty($cs->phone))<div><span>Phone</span><p>{{ $cs->phone }}</p></div>@endif
                                                @if(!empty($cs->email))<div><span>Email</span><p>{{ $cs->email }}</p></div>@endif
                                                @if(!empty($cs->timings))<div><span>Timings</span><p>{{ $cs->timings }}</p></div>@endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(empty($isPopup))
            <div class="to_top-btn-wrap">
                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                <div class="svg-corner svg-corner_white contact-top-corner-left"></div>
                <div class="svg-corner svg-corner_white contact-top-corner-right"></div>
            </div>
            @endif
        </div>
    </div>

    @if(empty($isPopup))
        @include('partials.footer')
    @endif
</div>

@push('scripts')
<script>
(function () {
    var form = document.getElementById('contact-us-form');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = document.getElementById('contact-submit-btn');
            var statusEl = document.getElementById('contact-form-status');
            btn.disabled = true;
            var old = btn.textContent;
            btn.textContent = 'Sending...';
            statusEl.innerHTML = '';
            fetch(form.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form),
                credentials: 'same-origin'
            }).then(function (r) {
                return r.json().then(function (j) { return { ok: r.ok, data: j }; });
            }).then(function (resp) {
                if (resp.ok && resp.data && resp.data.success) {
                    statusEl.innerHTML = '<span class="contact-form-msg success">' + (resp.data.message || 'Submitted successfully.') + '</span>';
                    form.reset();
                } else {
                    statusEl.innerHTML = '<span class="contact-form-msg error">' + ((resp.data && resp.data.message) || 'Unable to submit. Please check your input.') + '</span>';
                }
            }).catch(function () {
                statusEl.innerHTML = '<span class="contact-form-msg error">Unable to submit right now. Please try again.</span>';
            }).finally(function () {
                btn.disabled = false;
                btn.textContent = old;
            });
        });
    }

    @if(!empty($cs->latitude) && !empty($cs->longitude))
    window.initContactUsMap = function () {
        var mapEl = document.getElementById('contact-map');
        if (!mapEl || !window.google || !window.google.maps) return;
        var center = { lat: Number('{{ $cs->latitude }}'), lng: Number('{{ $cs->longitude }}') };
        var mapOpts = { zoom: 14, center: center };
        if (window.EtihadMap) EtihadMap.applyToMapOptions(mapOpts);
        var map = new google.maps.Map(mapEl, mapOpts);
        if (window.EtihadMap) {
            EtihadMap.createMarker({ position: center, map: map });
        } else {
            new google.maps.Marker({ position: center, map: map });
        }
    };
    var gKey = '{{ config('app.google_maps_api_key') ?: "AIzaSyAYrLB-ltxWv32OFEF6c07B376JNrDyOIA" }}';
    if (gKey && !document.getElementById('contact-map-api')) {
        var s = document.createElement('script');
        s.id = 'contact-map-api';
        s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(gKey) + '&callback=initContactUsMap';
        s.async = true;
        s.defer = true;
        document.head.appendChild(s);
    }
    @endif
})();
</script>
@endpush

