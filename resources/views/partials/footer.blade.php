{{-- Front footer extracted from html/index.html. Use @include('partials.footer') inside .wrapper (before closing). Not tied to index page. --}}
<!--main-footer-->
<div class="height-emulator"></div>
<footer class="main-footer">
    <div class="container">
        <div class="footer-inner">
            <div class="row">
                <div class="col-lg-5">
                    <div class="footer-widget">
                        <div class="footer-widget-title">
                            <a href="{{ url('/') }}" class="logo-holder">
                                <img src="{{ asset('theme/images/logo.png') }}" alt="{{ config('app.name') }}" style="max-height: 40px; width: auto;">
                            </a>
                        </div>
                        <div class="footer-widget-content">
                            <p>A well-known consortium of established and emerging real estate professional agents striving to fulfill clients’ living needs and standards by offering them a variety of options on a competitive and flexible basis.</p>
                            @php $cs = isset($cs) ? $cs : \App\Models\ContactSetting::instance(); @endphp
                            @if(!empty($cs->timings))
                                <p style="margin-top: 10px;"><strong>Timings:</strong> {{ $cs->timings }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="footer-widget">
                        <div class="footer-widget-title">About Etihad</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box  ">
                                <ul>
                                    <li><a href="{{ url('/about-us') }}">About Us</a></li>
                                    <li><a href="{{ url('/careers') }}">Careers</a></li>
                                    <li><a href="{{ url('/terms-of-use') }}">Terms of use</a></li>
                                    <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                                    <li><a href="{{ url('/contact-us') }}">Contact Us</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Browse</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box">
                                <ul>
                                    <li><a href="{{ url('/listing') }}">Listings</a></li>
                                    <li><a href="{{ url('/projects') }}">Projects</a></li>
                                    <li><a href="{{ url('/our-team') }}">Our Team</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    @php $cs = \App\Models\ContactSetting::instance(); @endphp
                    <div class="footer-widget">
                        <div class="footer-widget-title">Our Contacts</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box">
                                <ul class="footer-contacts">
                                    @if($cs->email)
                                    <li><span>Mail :</span><a href="mailto:{{ $cs->email }}" target="_blank">{{ $cs->email }}</a></li>
                                    @endif
                                    @if($cs->address)
                                    <li><span>Address :</span><a href="#" target="_blank">{{ $cs->address }}</a></li>
                                    @endif
                                    @if($cs->phone)
                                    @php $footerPhoneClean = preg_replace('/\s+/', '', $cs->phone); @endphp
                                    <li><span>Phone :</span><a href="tel:{{ $footerPhoneClean }}">{{ $cs->phone }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <a href="{{ url('/') }}" class="footer-home_link"><i class="fa-regular fa-house"></i></a>
            <div class="copyright"><span>&#169; {{ date('Y') }} Etihad Marketing</span> · Developed by <a href="https://hildes.io" target="_blank" rel="noopener" style="color:inherit;">HilDes</a></div>
            @php
                $cs = isset($cs) ? $cs : \App\Models\ContactSetting::instance();
            @endphp
            <div class="footer-social">
                <span class="footer-social-title">Follow Us</span>
                <div class="footer-social-wrap">
                    @if($cs->facebook)<a href="{{ $cs->facebook }}" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>@endif
                    @if($cs->instagram)<a href="{{ $cs->instagram }}" target="_blank"><i class="fa-brands fa-instagram"></i></a>@endif
                    @if($cs->linkedin)<a href="{{ $cs->linkedin }}" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>@endif
                    @if($cs->youtube)<a href="{{ $cs->youtube }}" target="_blank"><i class="fa-brands fa-youtube"></i></a>@endif
                </div>
            </div>
        </div>
    </div>
</footer>
<!--main-footer end-->
