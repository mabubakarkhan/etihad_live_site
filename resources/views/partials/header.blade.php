{{-- Front header extracted from html/index.html. Use @include('partials.header') inside #main. Not tied to index page. --}}
<!--header-->
<header class="main-header">
    <div class="container">
        <div class="header-inner">
            <a href="{{ route('portal') }}" class="logo-holder"><img src="{{ asset('theme/images/logo.png') }}" alt="{{ config('app.name') }}"></a>
            <!--  navigation -->
            <div class="nav-holder main-menu">
                <nav>
                    <ul class="no-list-style">
                        <li><a href="{{ route('portal') }}">Home</a></li>
                        <li><a href="{{ url('/listing') }}">Listing</a></li>
                        <li><a href="{{ route('dha.index') }}">DHA</a></li>
                        <li><a href="{{ url('/projects') }}">Projects</a></li>
                        <li><a href="{{ route('team') }}">Our Team</a></li>
                    </ul>
                </nav>
            </div>
            <!-- navigation end -->
            <div class="nav-button-wrap">
                <div class="nav-button">
                    <span></span><span></span><span></span>
                </div>
            </div>
            @php
                $cs = \App\Models\ContactSetting::instance();
                $headerPhoneRaw = $cs->phone ?: '';
                $headerPhoneClean = $headerPhoneRaw ? preg_replace('/\s+/', '', $headerPhoneRaw) : '';
                $headerWhatsappRaw = $cs->whatsapp ?: $headerPhoneRaw;
                $headerWhatsappClean = $headerWhatsappRaw ? preg_replace('/\D/', '', $headerWhatsappRaw) : '';
            @endphp
            @if($headerPhoneClean)
            <a href="tel:{{ $headerPhoneClean }}" class="show-reg-form header-call-now">
                <i class="fa-solid fa-phone"></i>
                <span>{{ $headerPhoneRaw }}</span>
            </a>
            @endif
            @if($headerWhatsappClean)
            <a href="https://wa.me/{{ $headerWhatsappClean }}" target="_blank" rel="noopener" class="show-reg-form header-call-now" style="margin-right: 10px;">
                <i class="fa-brands fa-whatsapp"></i>
                <span>{{ $headerWhatsappRaw }}</span>
            </a>
            @endif
            <!-- header-search-wrap -->
            <div class="header-search-wrap novis_search">
                <div class="header-search">
                    <div class="header-search-nav">
                        <div class="header-search-nav_container">
                            <div class="header-search-radio">
                                <input class="hidden radio-label" type="radio" name="accept-offers" id="sale-button" checked="checked">
                                <label class="button-label" for="sale-button">Sale</label>
                                <input class="hidden radio-label" type="radio" name="accept-offers" id="rent-button">
                                <label class="button-label" for="rent-button">Rent</label>
                                <input class="hidden radio-label" type="radio" name="accept-offers" id="comm-button">
                                <label class="button-label" for="comm-button">Commercial</label>
                            </div>
                        </div>
                    </div>
                    <div class="header-search-container">
                        <div class="custom-form">
                            <div class="cs-intputwrap">
                                <i class="fa-light fa-house"></i>
                                <input type="text" placeholder="Keywords..." value="">
                            </div>
                            <div class="cs-intputwrap">
                                <i class="fa-light fa-location-dot"></i>
                                <input type="text" placeholder="Location..." value="">
                            </div>
                            <div class="cs-intputwrap">
                                <div class="price-range-wrap ">
                                    <label>Price Range</label>
                                    <div class="price-rage-item">
                                        <input type="text" class="price-range-double" data-min="100" data-max="100000" name="price-range1" data-step="1" value="1" data-prefix="$">
                                    </div>
                                </div>
                            </div>
                            <a href="{{ url('/listing') }}" class="commentssubmit commentssubmit_fw">Search</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- header-search-wrap end -->
        </div>
    </div>
</header>
<div class="body-overlay fs-wrapper search-form-overlay close-search-form"></div>
<!--header end-->
