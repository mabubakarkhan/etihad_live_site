{{-- Front footer: include inside .wrapper after .content (same as portal). --}}
<!--main-footer-->
<div class="height-emulator"></div>
<footer class="main-footer">
    <div class="container">
        <div class="footer-inner">
            <div class="row">
                @php
                    $footerProjects = \App\Models\Project::query()->frontOrdered()->limit(10)->get(['title', 'slug']);
                    $cs = isset($cs) ? $cs : \App\Models\ContactSetting::instance();
                @endphp
                <div class="col-lg-4 col-md-12">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Our Projects</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box">
                                <ul class="footer-projects-grid">
                                    @foreach($footerProjects as $fp)
                                        <li><a href="{{ route('project.show', $fp->slug) }}">{{ $fp->title }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Links</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box">
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
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Listing</div>
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
                <div class="col-lg-4 col-md-12">
                    <div class="footer-widget">
                        <div class="footer-widget-title">Our Contacts</div>
                        <div class="footer-widget-content">
                            <div class="footer-list footer-box">
                                <ul class="footer-contacts">
                                    @if(!empty($cs->email))
                                        <li><span>Mail :</span><a href="mailto:{{ $cs->email }}" target="_blank">{{ $cs->email }}</a></li>
                                    @endif
                                    @if(!empty($cs->address))
                                        <li><span>Address :</span><a href="#" target="_blank">{{ $cs->address }}</a></li>
                                    @endif
                                    @if(!empty($cs->phone))
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
            <div class="copyright"><span>&#169; {{ date('Y') }} Etihad Marketing</span> · By Etihad</div>
            <div class="footer-social">
                <span class="footer-social-title">Follow Us</span>
                <div class="footer-social-wrap">
                    @if($cs->facebook)<a href="{{ $cs->facebook }}" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>@endif
                    @if($cs->instagram)<a href="{{ $cs->instagram }}" target="_blank"><i class="fa-brands fa-instagram"></i></a>@endif
                    @if($cs->linkedin)<a href="{{ $cs->linkedin }}" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>@endif
                    @if($cs->youtube)<a href="{{ $cs->youtube }}" target="_blank"><i class="fa-brands fa-youtube"></i></a>@endif
                    @if($cs->twitter)<a href="{{ $cs->twitter }}" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>@endif
                    @if($cs->tiktok)<a href="{{ $cs->tiktok }}" target="_blank"><i class="fa-brands fa-tiktok"></i></a>@endif
                </div>
            </div>
        </div>
    </div>
</footer>
<!--main-footer end-->
