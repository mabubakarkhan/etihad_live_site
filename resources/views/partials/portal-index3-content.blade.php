<div class="wrapper">
                <div class="content">
                    <!--section-->
                    <div class="section hero-section home-hero-section">
                        <div class="hero-section-wrap">
                            <div class="hero-section-wrap-item">
                                <div class="container">
                                    <div class="hero-section-container portal-home-hero-container">
                                        <div class="row justify-content-center">
                                            <div class="col-lg-8 col-md-10 col-12 portal-home-hero-search-col">
                                                <h1 class="portal-home-hero-heading">Start Your Journey With <span class="portal-hero-etihad-word" id="portal-hero-etihad-word">ETIHAD</span></h1>
                                                @php
                                                    $portalListingCity = !empty($lahoreCityId) ? '&city=' . $lahoreCityId : '';
                                                    $portalBuyUrl = route('listing') . '?purpose=sale' . $portalListingCity;
                                                    $portalSellUrl = route('sell-property');
                                                @endphp
                                                <div class="portal-home-hero-actions">
                                                    <a href="{{ $portalBuyUrl }}" class="portal-hero-action-btn">Buy</a>
                                                    <a href="{{ $portalSellUrl }}" class="portal-hero-action-btn">Sell</a>
                                                    <a href="{{ route('projects') }}" class="portal-hero-action-btn">Invest</a>
                                                    <a href="{{ route('team') }}" class="portal-hero-action-btn">Agents</a>
                                                    <button type="button" class="portal-hero-action-btn portal-hero-action-btn-search" id="portal-hero-search-open" aria-label="Open search">
                                                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                                                        <span>Search</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hs-scroll-down-wrap">
                                        <div class="scroll-down-item">
                                            <div class="mousey">
                                                <div class="scroller"></div>
                                            </div>
                                            <span>Scroll Down To Discover</span>
                                        </div>
                                        <div class="svg-corner svg-corner_white hero-corner-br"></div>
                                        <div class="svg-corner svg-corner_white hero-corner-bl"></div>
                                    </div>
                                    <div class="sc-controls shc_controls2 slideshow-container-pag-init  "></div>
                                </div>
                                <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper">
                                    <!--ms-container-->
                                    <div class="slideshow-container_wrap fl-wrap full-height">
                                        <div class="swiper-container full-height">
                                            <div class="swiper-wrapper">
                                                @if(isset($portalHeroSlides) && $portalHeroSlides->isNotEmpty())
                                                    @foreach($portalHeroSlides as $heroSlide)
                                                <div class="swiper-slide">
                                                    <div class="ms-item_fs  full-height fl-wrap">
                                                        <div class="bg" data-bg="{{ url('storage/' . ltrim($heroSlide->image, '/')) }}"></div>
                                                    </div>
                                                </div>
                                                    @endforeach
                                                @else
                                                <div class="swiper-slide">
                                                    <div class="ms-item_fs  full-height fl-wrap">
                                                        <div class="bg" data-bg="{{ asset('theme/images/bg/10.jpg') }}"></div>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide ">
                                                    <div class="ms-item_fs full-height fl-wrap">
                                                        <div class="bg" data-bg="{{ asset('theme/images/bg/9.jpg') }}"></div>
                                                    </div>
                                                </div>
                                                <div class="swiper-slide">
                                                    <div class="ms-item_fs full-height fl-wrap">
                                                        <div class="bg" data-bg="{{ asset('theme/images/bg/2.jpg') }}"></div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!--ms-container end-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--section-end-->	
                    @php
                        $portalAdsBySlug = collect($portalAds ?? [])->keyBy('slug');
                        $propertiesAd = $portalAdsBySlug->get('properties');
                        $dealersAd = $portalAdsBySlug->get('dealers');
                    @endphp
                    @if(($propertiesAd && !empty($propertiesAd->image)) || ($dealersAd && !empty($dealersAd->image)))
                    <div class="container">
                        <div class="portal-ads-wrap">
                            @if($propertiesAd && !empty($propertiesAd->image))
                                <div class="portal-ad-card portal-ad-card-properties">
                                    <span class="portal-ad-badge">NEW</span>
                                    <img src="{{ url('storage/' . ltrim($propertiesAd->image, '/')) }}" alt="Properties ad banner" class="portal-ad-image">
                                    <div class="portal-ad-overlay"></div>
                                    <div class="portal-ad-content">
                                        <h4 class="portal-ad-title">Sell or Rent Your Property with Confidence</h4>
                                        <p class="portal-ad-subtitle">Connect with a trusted agent to secure the best deal, faster.</p>
                                    </div>
                                    <a href="{{ route('listing') }}" class="portal-ad-btn">Get Started <i class="fa-regular fa-angle-right"></i></a>
                                </div>
                            @endif
                            @if($dealersAd && !empty($dealersAd->image))
                                @php $portalHomepageAdDealers = $portalHomepageAdDealers ?? collect(); @endphp
                                <div class="portal-ad-card portal-ad-card-dealers{{ $portalHomepageAdDealers->isNotEmpty() ? ' has-ad-avatars' : '' }}">
                                    <img src="{{ url('storage/' . ltrim($dealersAd->image, '/')) }}" alt="Dealers ad banner" class="portal-ad-image">
                                    <div class="portal-ad-overlay"></div>
                                    @if($portalHomepageAdDealers->isNotEmpty())
                                    <div class="portal-ad-avatar-stack" aria-label="Featured agents">
                                        @foreach($portalHomepageAdDealers as $adDealer)
                                        @php
                                            $adDealerImg = $adDealer->profile_pic
                                                ? url('storage/' . ltrim($adDealer->profile_pic, '/'))
                                                : asset('theme/images/avatar/1.jpg');
                                        @endphp
                                        <span class="portal-ad-avatar-item" tabindex="0" role="img" aria-label="{{ $adDealer->name }}">
                                            <img src="{{ $adDealerImg }}" alt="" class="portal-ad-avatar-img" loading="lazy">
                                            <span class="portal-ad-avatar-tooltip">{{ $adDealer->name }}</span>
                                        </span>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div class="portal-ad-content{{ $portalHomepageAdDealers->isNotEmpty() ? ' portal-ad-content--with-avatars' : '' }}">
                                        <h4 class="portal-ad-title">Find a Trusted Agent</h4>
                                        <p class="portal-ad-subtitle">Find trusted agents awarded for excellent performance.</p>
                                    </div>
                                    <a href="{{ route('team') }}" class="portal-ad-btn">Explore Our Agents <i class="fa-regular fa-angle-right"></i></a>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                    <!--container-->
                    <div class="container">
                        <!--main-content-->
                        <div class="main-content ms_vir_height" id="sec1">
                            <!--boxed-container-->
                            <div class="boxed-container">
                                <div class="listing-grid_heroheader">
                                    <h3>Browse New Properties in DHA</h3>
                                    <div class="gallery-filters">
                                        <a href="#" class="gallery-filter gallery-filter-active"  data-filter="*"> All Properties</a>
                                        <a href="#" class="gallery-filter " data-filter=".cat-sale">Sale</a>
                                        <a href="#" class="gallery-filter" data-filter=".cat-rent">Rent</a>
                                    </div>
                                </div>
                                @php $hotPropertyCards = $hotPropertyCards ?? collect(); @endphp
                                <!-- listing-grid-->
                                <div class="listing-grid gisp portal-property-listing">
                                    @forelse($hotPropertyCards as $p)
                                    <div class="listing-grid-item {{ $p['filter_class'] ?? '' }}">
                                        @include('partials.property-card', ['p' => $p, 'listing_base' => route('listing')])
                                    </div>
                                    @empty
                                    <div class="col-12 portal-home-empty-note">
                                        <p>No hot listings to show yet. Mark listings as hot in the admin panel.</p>
                                    </div>
                                    @endforelse
                                </div>
                                <!-- listing-grid end-->
                                <a href="{{ route('listing') }}" class="commentssubmit csb-no-align">View All Properties <i class="fa-solid fa-caret-right"></i></a>
                            </div>
                            <!--boxed-container end-->
                        </div>
                        <!--main-content end-->	
                    </div>
                    <!--container end-->				 
                    @if(isset($portalDealers) && $portalDealers->isNotEmpty())
                    <div class="portal-dealers-strip">
                        <div class="dealer-marquee">
                            <div class="dealer-marquee-track">
                                @for($loopCopy = 0; $loopCopy < 6; $loopCopy++)
                                    @foreach($portalDealers as $dealer)
                                        @php
                                            $dealerImg = $dealer->profile_pic
                                                ? url('storage/' . ltrim($dealer->profile_pic, '/'))
                                                : asset('theme/images/avatar/1.jpg');
                                        @endphp
                                        <a href="{{ route('dealer.show', $dealer->slug) }}" class="dealer-chip" title="{{ $dealer->name }}" {{ $loopCopy > 0 ? 'aria-hidden=true' : '' }}>
                                            <img src="{{ $dealerImg }}" alt="{{ $dealer->name }}" class="dealer-chip-avatar" loading="lazy">
                                            <span class="dealer-chip-name">{{ $dealer->name }}</span>
                                        </a>
                                    @endforeach
                                @endfor
                            </div>
                        </div>
                    </div>
                    @endif
                    @if(isset($portalCarouselProjects) && $portalCarouselProjects->isNotEmpty())
                    <div class="main-content portal-projects-listing">
                        <div class="container">
                            <div class="boxed-container">
                                <div class="listing-grid_heroheader portal-home-section-head">
                                    <h3>Our Projects</h3>
                                    <a href="{{ route('projects') }}" class="commentssubmit csb-no-align">View All Projects <i class="fa-solid fa-caret-right"></i></a>
                                </div>
                                <div class="listing-item-container three-columns-grid etihad-mt-24">
                                    @foreach($portalCarouselProjects as $project)
                                        @include('partials.portal-project-card', ['project' => $project])
                                    @endforeach
                                </div>
                                <a href="{{ route('projects') }}" class="commentssubmit csb-no-align portal-home-view-all">View All Projects <i class="fa-solid fa-caret-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($dhaPhases) && $dhaPhases->isNotEmpty())
                    <div class="main-content dha-home-section portal-dha-phases-section">
                        <div class="container">
                            <div class="dha-home-section-head listing-grid_heroheader portal-home-section-head">
                                <h3>DHA Lahore Phases</h3>
                                <a href="{{ route('dha.index') }}" class="commentssubmit csb-no-align">Explore DHA <i class="fa-solid fa-caret-right"></i></a>
                            </div>
                            @include('partials.dha-phase-slider', ['phases' => $dhaPhases])
                        </div>
                    </div>
                    @endif

                    @if(isset($portalMapProperties) && $portalMapProperties->isNotEmpty())
                    <div class="portal-map-section">
                        <div class="portal-map-wrap">
                            <div id="portal-home-map" class="portal-home-map" aria-label="Map of properties"></div>
                        </div>
                    </div>
                    @endif

                    @if(isset($portalPopularDealers) && $portalPopularDealers->isNotEmpty())
                    <div class="portal-home-agents-section">
                        <div class="container">
                            <div class="portal-home-agents-wrap">
                                <div class="portal-home-agents-header">
                                    <h3>Explore Our Popular Agents</h3>
                                    <a href="{{ route('team') }}" class="commentssubmit csb-no-align">View All Agents <i class="fa-solid fa-caret-right"></i></a>
                                </div>
                                <div class="portal-home-agents-grid">
                                    @foreach($portalPopularDealers as $dealer)
                                        @php
                                            $hasImage = !empty($dealer->profile_pic);
                                            $imgUrl = $hasImage ? url('storage/' . ltrim($dealer->profile_pic, '/')) : '';
                                            $propsCount = (int) ($dealer->properties_count ?? 0);
                                            $viewsCount = (int) ($dealer->view_count ?? 0);
                                            $desc = $dealer->info_detail ? \Illuminate\Support\Str::limit(strip_tags($dealer->info_detail), 120) : '';
                                        @endphp
                                        <div class="agent-card-item">
                                            <div class="agent-card-item_media">
                                                <div class="agent-card-item_media-wrap">
                                                    @if($hasImage)
                                                        <img src="{{ $imgUrl }}" alt="{{ e($dealer->name) }}" class="dealer-portrait-img" loading="lazy">
                                                    @else
                                                        <div class="team-card-avatar-placeholder">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="3"/><path d="M5 20v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/></svg>
                                                        </div>
                                                    @endif
                                                    <div class="overlay"></div>
                                                </div>
                                            </div>
                                            <div class="agent-card-item_text">
                                                <div class="agent-card-item_text-item">
                                                    <h4>{{ $dealer->name }}</h4>
                                                    @if($desc)
                                                    <p>{{ $desc }}</p>
                                                    @endif
                                                    <div class="post-card-details">
                                                        <ul>
                                                            <li><i class="fa-regular fa-house-building"></i><span>{{ $propsCount }} Properties</span></li>
                                                            <li><i class="fa-light fa-eye"></i><span>{{ number_format($viewsCount) }} Views</span></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="agent-card-item_footer sh-links">
                                                <a href="{{ route('dealer.show', $dealer->slug) }}" class="post-card_link">View Details <i class="fa-solid fa-caret-right"></i></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!--main-content-->
                    <div class="main-content ms_vir_height">
                        <!--container -->
                        <div class="container">
                            <div class="boxed-container">
                                <div class="boxed-content "  >
                                    <div class="about-wrap boxed-content-item">
                                        <div class="row">
                                            <div class="col-lg-7">
                                                <div class="about-title ab-hero">
                                                    <h2>Why Choose Our Properties </h2>
                                                    <h4>Check video presentation to find   out more about us .</h4>
                                                </div>
                                                <div class="services-opions">
                                                    <ul>
                                                        <li>
                                                            <i class="fal fa-headset"></i>
                                                            <h4>24 Hours Support  </h4>
                                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                                        </li>
                                                        <li>
                                                            <i class="fal fa-users-cog"></i>
                                                            <h4>User Admin Panel</h4>
                                                            <p>Nulla posuere sapien vitae lectus suscipit, et pulvinar nisi tincidunt. Curabitur convallis fringilla diam sed aliquam. </p>
                                                        </li>
                                                        <li>
                                                            <i class="fal fa-phone-laptop"></i>
                                                            <h4>Mobile Friendly</h4>
                                                            <p>Curabitur convallis fringilla diam sed aliquam. Sed tempor iaculis massa faucibus feugiat. In fermentum facilisis massa.</p>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="about-img about-img-search">
                                                    <div class="list-searh-input-wrap box_list-searh-input-wrap lws_column lsiw_dec" id="why-portal-quick-search-wrap" data-listing-url="{{ route('listing') }}">
                                                        <div class="list-searh-input-wrap-title_wrap">
                                                            <div class="list-searh-input-wrap-title"><i class="far fa-sliders-h"></i><span>Use Quick Search</span></div>
                                                            <div class="list-searh-input-radio_wrap">
                                                                <div class="header-search-radio">
                                                                    <input class="hidden radio-label" type="radio" name="whyPortalPurpose" id="why-portal-purpose-sale" value="sale" checked="checked">
                                                                    <label class="button-label" for="why-portal-purpose-sale">Sale</label>
                                                                    <input class="hidden radio-label" type="radio" name="whyPortalPurpose" id="why-portal-purpose-rent" value="rent">
                                                                    <label class="button-label" for="why-portal-purpose-rent">Rent</label>
                                                                </div>
                                                                <button type="button" class="reset-form reset-btn tolt" id="why-portal-quick-search-reset" data-microtip-position="bottom" data-tooltip="Reset Filters" aria-label="Reset filters"><i class="fa-solid fa-arrows-rotate"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="custom-form">
                                                            <input type="hidden" id="why-portal-default-city-id" value="{{ $lahoreCityId ?? '' }}" data-default="{{ $lahoreCityId ?? '' }}" data-city-name="Lahore" autocomplete="off">
                                                            <div class="row g-2">
                                                                <div class="col-12">
                                                                    <div class="cs-intputwrap listing-address-wrap">
                                                                        <i class="fa-light fa-location-dot"></i>
                                                                        <input type="text" id="why-portal-quick-address" placeholder="Address, street, area…" value="" autocomplete="off">
                                                                        <input type="hidden" id="why-portal-quick-address-value" value="">
                                                                        <div id="why-portal-address-suggestions" class="listing-address-suggestions" role="listbox" aria-hidden="true"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="cs-intputwrap">
                                                                        <i class="fa-light fa-layer-group"></i>
                                                                        <select id="why-portal-project-type" data-placeholder="All Categories" class="chosen-select on-radius no-search-select">
                                                                            <option value="">All Categories</option>
                                                                            @foreach(($projectTypes ?? []) as $pt)
                                                                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="cs-intputwrap listing-range-dropdown-cswrap">
                                                                        <i class="fa-light fa-ruler-combined" aria-hidden="true"></i>
                                                                        <div class="listing-range-dropdown">
                                                                            <button type="button" class="listing-range-dropdown-btn" id="why-portal-area-range-toggle" aria-expanded="false" aria-controls="why-portal-area-range-panel">
                                                                                <span class="listing-range-dropdown-title">Area</span>
                                                                                <span class="listing-range-dropdown-summary" id="why-portal-area-range-summary"></span>
                                                                                <i class="fa-solid fa-chevron-down listing-range-dropdown-caret" aria-hidden="true"></i>
                                                                            </button>
                                                                            <div class="listing-range-dropdown-panel" id="why-portal-area-range-panel" role="region" aria-label="Area range">
                                                                                <div class="listing-range-dropdown-panel-inner">
                                                                                    <div class="price-range-wrap fl-wrap listing-range-wrap">
                                                                                        <label>Area/Marla</label>
                                                                                        <div class="price-rage-item pr-nopad fl-wrap">
                                                                                            <input type="text" id="why-portal-area-range" class="price-range-double listing-deferred-range" data-min="1" data-max="400" data-from="1" data-to="20" name="why_portal_area_range" data-step="1" data-prefix="" autocomplete="off">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <button type="button" id="why-portal-quick-search-btn" class="commentssubmit commentssubmit_fw">Search</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- clients-carousel-wrap-->
                                @if(isset($portalPartners) && $portalPartners->isNotEmpty())
                                <div class="clients-carousel-wrap">
                                    <div class="clients-carousel-title">Our Trusted Partners  </div>
                                    <div class="clients-carousel">
                                        <div class="swiper-container">
                                            <div class="swiper-wrapper">
                                                @foreach($portalPartners as $partner)
                                                <div class="swiper-slide">
                                                    <div class="client-item"><img src="{{ url('storage/' . ltrim($partner->image, '/')) }}" alt="{{ $partner->title }}"></div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="cc-button cc-button-next"><i class="fal fa-angle-right"></i></div>
                                        <div class="cc-button cc-button-prev"><i class="fal fa-angle-left"></i></div>
                                    </div>
                                </div>
                                @endif
                                <!-- clients-carousel-wrap end-->									
                            </div>
                        </div>

                        <!--container-->
                        <div class="container portal-home-inner-pad">
                            <div class="api-wrap">
                                <div class="api-container">
                                    <div class="api-img">
                                        <img src="{{ asset('theme/images/api.png') }}" alt="" class="respimg">
                                    </div>
                                    <div class="api-text">
                                        <h3>Explore Projects &amp; Properties</h3>
                                        <p>Browse our latest projects and property listings in one place. Discover verified options, compare locations, and open full details to find the right opportunity for your needs.</p>
                                        <div class="api-text-links">
                                            <a href="{{ route('projects') }}"><span> Projects</span><i class="fa-light fa-buildings"></i></a>
                                            <a href="{{ route('listing') }}"><span> Properties Listing</span><i class="fa-light fa-house-building"></i></a>
                                            <a href="{{ route('team') }}"><span> Dealers</span><i class="fa-light fa-users"></i></a>
                                        </div>
                                    </div>
                                    <div class="api-wrap-bg" data-run="2">
                                        <div class="api-wrap-bg-container">
                                            <span class="api-bg-pin"></span><span class="api-bg-pin"></span>
                                            <div class="abs_bg"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--container end-->
                        <br>
                        <br>
                        <!--section   -->
                        <div class="parallax-section-wrap">
                            <div class="bg-wrap    fs-wrapper" data-scrollax-parent="true">
                                <div class="bg" data-bg="{{ asset('theme/images/bg/3.jpg') }}" data-scrollax="properties: { translateY: '20%' }"></div>
                                <div class="overlay"></div>
                            </div>
                            <div class="container  ">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="parallax-section-content">
                                            <h3>How Our Platform  Works</h3>
                                            <p>In ut odio libero, at vulputate urna. Nulla tristique mi a massa convallis cursus. Nulla eu mi magna. Etiam suscipit commodo gravida. Lorem ipsum dolor sit amet, conse ctetuer adipiscing elit, sed diam nonu mmy nibh euismod tincidunt ut laoreet dolore magna aliquam erat</p>
                                            <a href="{{ url('/listing') }}" class="commentssubmit csb_color portal-home-listing-cta">Add Your Propperty</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-8">
                                        <div class="process-wrap">
                                            <ul>
                                                <li>
                                                    <div class="process-item">
                                                        <span class="process-count">01 . </span>
                                                        <div class="process-item-icon"><i class="fa-light fa-house-building"></i></div>
                                                        <h4> Find Interesting Place</h4>
                                                        <p>Proin dapibus nisl ornare diam varius tempus. Aenean a quam luctus, finibus tellus ut, convallis eros sollicitudin turpis.</p>
                                                    </div>
                                                    <span class="pr-dec"></span>
                                                </li>
                                                <li>
                                                    <div class="process-item">
                                                        <span class="process-count">02 .</span>
                                                        <div class="process-item-icon"><i class="fa-light fa-mailbox"></i></div>
                                                        <h4> Contact a Few Owners</h4>
                                                        <p>Faucibus ante, in porttitor tellus blandit et. Phasellus tincidunt metus lectus sollicitudin feugiat pharetra consectetur.</p>
                                                    </div>
                                                    <span class="pr-dec"></span>
                                                </li>
                                                <li>
                                                    <div class="process-item">
                                                        <span class="process-count">03 .</span>
                                                        <div class="process-item-icon"><i class="fa-light fa-layer-plus"></i></div>
                                                        <h4> Make a Listing</h4>
                                                        <p>Maecenas pulvinar, risus in facilisis dignissim, quam nisi hendrerit nulla, id vestibulum metus nullam viverra porta.</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- section   -->
                        @if(isset($portalTestimonials) && $portalTestimonials->isNotEmpty())
                        <div class="content-section">
                            <div class="container  ">
                                <div class="section-title">
                                    <h4>What said about us</h4>
                                    <h2>Testimonials and Clients</h2>
                                </div>
                            </div>
                            <div class="testimonilas-carousel-wrap">
                                <div class="testimonilas-carousel">
                                    <div class="swiper-container">
                                        <div class="swiper-wrapper">
                                            @foreach($portalTestimonials as $testimonial)
                                            <div class="swiper-slide">
                                                <div class="testi-item">
                                                    <div class="testimonilas-text">
                                                        <div class="testi-header">
                                                            <div class="testi-avatar"><img src="{{ url('storage/' . ltrim($testimonial->image, '/')) }}" alt="{{ $testimonial->name }}"></div>
                                                            <h3>{{ $testimonial->name }}</h3>
                                                        </div>
                                                        <div class="testimonilas-text-item">
                                                            <div class="testimonilas-text-item-wrap">
                                                                <p>"{{ $testimonial->comment }}"</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="testi-footer">
                                                        @if($testimonial->city)
                                                        <span class="testi-link">{{ $testimonial->city }}</span>
                                                        @endif
                                                        <span class="testi-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}.</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="tc-button tc-button-next"><i class="fas fa-caret-right"></i></div>
                                    <div class="tc-button tc-button-prev"><i class="fas fa-caret-left"></i></div>
                                </div>
                                <div class="fwc-controls_wrap">
                                    <div class="solid-pagination_btns tcs-pagination_init"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- section end  -->										
                        <!--container-->
                        <div class="container">
                            <div class="to_top-btn-wrap">
                                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                                <div class="svg-corner svg-corner_white hero-corner-tl"></div>
                                <div class="svg-corner svg-corner_white hero-corner-tr"></div>
                            </div>
                        </div>
                        <!--container end-->
                    </div>
                    <!--main-content end-->
                </div>
                <!--content end-->
