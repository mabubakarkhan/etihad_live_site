<div class="wrapper">
                <div class="content">
                    <!--section-->
                    <div class="section hero-section home-hero-section">
                        <div class="hero-section-wrap">
                            <div class="hero-section-wrap-item">
                                <div class="container">
                                    <div class="hero-section-container">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="hero-section-title hs_align-title">
                                                    <div class="hero-section-title_sub">Welcome to  the Renstate Agency</div>
                                                    <h2>Find The House of Your Dream   Using <br>  Our RealEatate   Platform</h2>
                                                    <h5>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut nec tincidunt arcu, sit amet fermentum sem.</h5>
                                                    <a href="#sec1" class="commentssubmit csb_color  custom-scroll-link" style="margin-top: 40px">Start Explore</a>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mob-hid">
                                                <!-- list-searh-input-wrap-->
                                                <div class="list-searh-input-wrap box_list-searh-input-wrap lws_column lsiw_dec" id="portal-quick-search-wrap" data-listing-url="{{ route('listing') }}">
                                                    <div class="list-searh-input-wrap-title_wrap">
                                                        <div class="list-searh-input-wrap-title"><i class="far fa-sliders-h"></i><span>Use Quick Search</span></div>
                                                        <div class="list-searh-input-radio_wrap">
                                                            <div class="header-search-radio">
                                                                <input class="hidden radio-label" type="radio" name="portalPurpose" id="portal-purpose-sale" value="sale" checked="checked">
                                                                <label class="button-label" for="portal-purpose-sale">Sale</label>
                                                                <input class="hidden radio-label" type="radio" name="portalPurpose" id="portal-purpose-rent" value="rent">
                                                                <label class="button-label" for="portal-purpose-rent">Rent</label>
                                                            </div>
                                                            <button type="button" class="reset-form reset-btn tolt" id="portal-quick-search-reset" data-microtip-position="bottom" data-tooltip="Reset Filters" aria-label="Reset filters"><i class="fa-solid fa-arrows-rotate"></i></button>
                                                        </div>
                                                    </div>
                                                    <div class="custom-form">
                                                        <input type="hidden" id="portal-default-city-id" value="{{ $lahoreCityId ?? '' }}" data-default="{{ $lahoreCityId ?? '' }}" data-city-name="Lahore" autocomplete="off">
                                                        <div class="row g-2">
                                                            <div class="col-12">
                                                                <div class="cs-intputwrap listing-address-wrap" style="position: relative;">
                                                                    <i class="fa-light fa-location-dot"></i>
                                                                    <input type="text" id="portal-quick-address" placeholder="Address, street, area…" value="" autocomplete="off">
                                                                    <input type="hidden" id="portal-quick-address-value" value="">
                                                                    <div id="portal-address-suggestions" class="listing-address-suggestions" role="listbox" aria-hidden="true"></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="cs-intputwrap">
                                                                    <i class="fa-light fa-layer-group"></i>
                                                                    <select id="portal-project-type" data-placeholder="All Categories" class="chosen-select on-radius no-search-select">
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
                                                                        <button type="button" class="listing-range-dropdown-btn" id="portal-area-range-toggle" aria-expanded="false" aria-controls="portal-area-range-panel">
                                                                            <span class="listing-range-dropdown-title">Area</span>
                                                                            <span class="listing-range-dropdown-summary" id="portal-area-range-summary"></span>
                                                                            <i class="fa-solid fa-chevron-down listing-range-dropdown-caret" aria-hidden="true"></i>
                                                                        </button>
                                                                        <div class="listing-range-dropdown-panel" id="portal-area-range-panel" role="region" aria-label="Area range">
                                                                            <div class="listing-range-dropdown-panel-inner">
                                                                                <div class="price-range-wrap fl-wrap listing-range-wrap">
                                                                                    <label>Area/Marla</label>
                                                                                    <div class="price-rage-item pr-nopad fl-wrap">
                                                                                        <input type="text" id="portal-area-range" class="price-range-double listing-deferred-range" data-min="1" data-max="2000" data-from="5" data-to="20" name="portal_area_range" data-step="1" data-prefix="" autocomplete="off">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <button type="button" id="portal-quick-search-btn" class="commentssubmit commentssubmit_fw">Search</button>
                                                    </div>
                                                </div>
                                                <!-- list-searh-input-wrap end-->							
                                                <div class="hero-notifer">Explore the complete listing catalogue <a href="{{ route('listing') }}">Browse all listings</a></div>
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
                                        <div class="svg-corner svg-corner_white"  style="bottom:0;right: -39px; transform: rotate( 90deg)" ></div>
                                        <div class="svg-corner svg-corner_white"  style="bottom:0;left:  -39px;"></div>
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
                    <!--container-->
                    <div class="container">
                        <!--breadcrumbs-list-->
                        <div class="breadcrumbs-list bl_flat">
                            <a href="{{ route('portal') }}">Home</a><span>Slideshow</span>
                            <div class="breadcrumbs-list_dec"><i class="fa-thin fa-arrow-up"></i></div>
                        </div>
                        <!--breadcrumbs-list end-->		
                        <!--main-content-->
                        <div class="main-content ms_vir_height" id="sec1">
                            <!--boxed-container-->
                            <div class="boxed-container">
                                <div class="listing-grid_heroheader">
                                    <h3>Browse Hot  Properties</h3>
                                    <div class="gallery-filters">
                                        <a href="#" class="gallery-filter gallery-filter-active"  data-filter="*"> All Properties</a>
                                        <a href="#" class="gallery-filter " data-filter=".cat-sale">Sale</a>
                                        <a href="#" class="gallery-filter" data-filter=".cat-rent">Rent</a>
                                    </div>
                                </div>
                                @php $hotPropertyCards = $hotPropertyCards ?? collect(); @endphp
                                <!-- listing-grid-->
                                <div class="listing-grid gisp">
                                    @forelse($hotPropertyCards as $p)
                                    <div class="listing-grid-item {{ $p['filter_class'] ?? '' }}">
                                        @include('partials.property-card', ['p' => $p, 'listing_base' => route('listing')])
                                    </div>
                                    @empty
                                    <div class="col-12" style="padding: 24px 0;">
                                        <p style="margin:0;color:#64748b;font-size:14px;">No hot listings to show yet. Mark listings as hot in the admin panel.</p>
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
                    @if(isset($portalCarouselProjects) && $portalCarouselProjects->isNotEmpty())
                    <div class="dark-bg half-carousel-container">
                        <div class="city-carousel-wrap">
                            <div class="half-carousel-title-wrap">
                                <div class="half-carousel-title">
                                    <h2>Explore Projects</h2>
                                    <p>Discover our projects and open any card for full details.</p>
                                    <a href="{{ route('projects') }}" class="commentssubmit" style="margin-top: 20px">View All Projects<i class="fa-solid fa-caret-right"></i></a>
                                </div>
                                <div class="abs_bg"></div>
                            </div>
                            <div class="city-carousel">
                                <div class="swiper-container">
                                    <div class="swiper-wrapper">
                                        @foreach($portalCarouselProjects as $project)
                                        <div class="swiper-slide">
                                            <a href="{{ route('project.show', $project->slug) }}" class="city-carousel-item" style="display:block;text-decoration:none;color:inherit;">
                                                <div class="bg-wrap fs-wrapper">
                                                    <div class="bg" data-bg="{{ url('storage/' . ltrim($project->homepage_listing_image, '/')) }}" data-swiper-parallax="10%"></div>
                                                    <div class="overlay"></div>
                                                </div>
                                                <div class="city-carousel-content">
                                                    <h3>{{ $project->title }}</h3>
                                                    @if($project->city)
                                                    <div class="city-carousel-city-line" style="margin:0;padding-top:4px;font-size:1.05em;display:flex;align-items:center;gap:8px;color:rgba(255,255,255,.9);"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>{{ $project->city }}</span></div>
                                                    @endif
                                                </div>
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="sc-controls city-pag-init"></div>
                            </div>
                        </div>
                        <div class="city-carousel_controls">
                            <div class="csc-button csc-button-prev"><i class="fas fa-caret-left"></i></div>
                            <div class="csc-button csc-button-next"><i class="fas fa-caret-right"></i></div>
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
                                            <div class="col-lg-5">
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
                                            <div class="col-lg-7">
                                                <div class="about-img">
                                                    <img src="{{ asset('theme/images/all/15.jpg') }}" class="respimg" alt="">
                                                    <div class="about-img-hotifer">
                                                        <p>Your website is fully responsive so visitors can view your content from their choice of device.</p>
                                                        <h4>Mark Antony</h4>
                                                        <h5>Renstate CEO</h5>
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
                        <!--container end-->
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
                                            <a href="{{ url('/listing') }}" class="commentssubmit csb_color " style="margin-top: 20px">Add Your Propperty</a>
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
                            <div class="api-wrap">
                                <div class="api-container">
                                    <div class="api-img">
                                        <img src="{{ asset('theme/images/api.png') }}" alt="" class="respimg">
                                    </div>
                                    <div class="api-text">
                                        <h3>Our App Available Now</h3>
                                        <p>In ut odio libero, at vulputate urna. Nulla tristique mi a massa convallis cursus. Nulla eu mi magna. Etiam suscipit commodo gravida. Lorem ipsum dolor sit amet, conse ctetuer adipiscing elit, sed diam nonu mmy nibh euismod tincidunt ut laoreet dolore magna aliquam erat.</p>
                                        <div class="api-text-links">
                                            <a href="#"><span> On Apple Store</span><i class="fa-brands fa-apple"></i></a>
                                            <a href="#"><span> On Google PLay</span><i class="fa-brands fa-google-play"></i></a>												
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
                            <div class="to_top-btn-wrap">
                                <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                                <div class="svg-corner svg-corner_white"  style="top:0;left:  -40px; transform: rotate(-90deg)"></div>
                                <div class="svg-corner svg-corner_white"  style="top:0;right: -40px; transform: rotate(-180deg)"></div>
                            </div>
                        </div>
                        <!--container end-->
                    </div>
                    <!--main-content end-->
                </div>
                <!--content end-->
