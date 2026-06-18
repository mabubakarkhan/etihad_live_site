@extends('layouts.front')

@php
    $metaTitle = $property->meta_title ?: ($property->title . ' – ' . config('app.name'));
    $metaDesc = $property->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($property->description ?? ''), 160);
    $canonicalUrl = $property->canonical_url ?: url()->current();
    $featuredUrl = $property->featured_image ? url('storage/' . ltrim($property->featured_image, '/')) : asset('theme/images/all/1.jpg');
    $gallerySorted = collect($property->gallery ?? [])->sortBy('order')->values();
    $carouselImages = [];
    $carouselImages[] = ['url' => $featuredUrl, 'alt' => $property->title];
    foreach ($gallerySorted as $g) {
        $path = $g['path'] ?? null;
        if ($path) {
            $fullUrl = url('storage/' . ltrim($path, '/'));
            if ($fullUrl !== $featuredUrl) {
                $carouselImages[] = ['url' => $fullUrl, 'alt' => $property->title];
            }
        }
    }
    $purposeLabel = $property->purpose === \App\Models\Property::PURPOSE_RENT ? 'Rent' : ($property->purpose === \App\Models\Property::PURPOSE_SALE ? 'Sale' : 'Listing');
    $projectTypeNames = $property->projectTypes->pluck('name');
    $dealerName = $property->dealer ? $property->dealer->name : 'Etihad Marketing';
    $dealerImageUrl = $property->dealer && $property->dealer->profile_pic
        ? url('storage/' . ltrim($property->dealer->profile_pic, '/'))
        : asset('theme/images/avatar/1.jpg');
    $fullAddress = implode(', ', array_filter([$property->address ?? $property->short_address, $property->town, $property->city, $property->state]));
    $listingBase = url('/listing');
    $listingBreadcrumbLabel = 'Listing';
    $amenitiesList = is_array($property->amenities) ? $property->amenities : [];
    $videoGalleryItems = [];
    $videoGalleryUrls = [];
    $videoRawItems = is_array($property->video_gallery ?? []) ? $property->video_gallery : [];
    foreach ($videoRawItems as $v) {
        $raw = is_array($v) ? '' : trim((string) $v);
        if ($raw === '') continue;
        $embedUrl = null;
        if (strpos($raw, '<') !== false && preg_match('/src=["\']([^"\']+)["\']/', $raw, $m)) {
            $embedUrl = $m[1];
        } else {
            $videoId = null;
            if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $raw, $m)) {
                $videoId = $m[1];
            } else {
                $part = explode('?', $raw)[0];
                if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', $part)) {
                    $videoId = $part;
                }
            }
            if ($videoId) {
                $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
            }
        }
        if ($embedUrl) {
            $videoGalleryUrls[] = $embedUrl;
        }
    }
    $featuredVideoId = null;
    $featuredVideoEmbedUrl = null;
    $featuredVideoThumbUrl = null;
    $videosArr = is_array($property->videos ?? []) ? $property->videos : [];
    $featuredRaw = isset($videosArr[0]) ? trim((string) $videosArr[0]) : '';
    if ($featuredRaw !== '') {
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $featuredRaw, $m)) {
            $featuredVideoId = $m[1];
        } else {
            $part = explode('?', $featuredRaw)[0];
            if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', $part)) {
                $featuredVideoId = $part;
            }
        }
        if ($featuredVideoId) {
            $featuredVideoEmbedUrl = 'https://www.youtube.com/embed/' . $featuredVideoId;
            $featuredVideoThumbUrl = 'https://img.youtube.com/vi/' . $featuredVideoId . '/hqdefault.jpg';
        }
    }
@endphp

@section('title', $metaTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => [
    'title' => $metaTitle,
    'description' => $metaDesc,
    'keywords' => seo_str($property->meta_keywords ?? ''),
    'canonical' => $canonicalUrl,
    'image' => $featuredUrl,
    'type' => 'website',
]])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/property-page.css') }}">
@endpush

@section('content')
<div id="main" class="property-detail-main">
    @include('partials.header')
    {{-- Video modal at top level so it covers header and viewport; scroll lock via body class --}}
    @if(!empty($featuredVideoId))
    <div class="property-video-modal-overlay" id="featured-video-modal" aria-hidden="true">
        <div class="property-video-modal-inner">
            <button type="button" class="property-video-modal-close" id="featured-video-close" aria-label="Close">&times;</button>
            <iframe id="featured-video-iframe" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    @endif

    <div class="wrapper">
        <div class="content">
            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ $listingBase }}">{{ $listingBreadcrumbLabel }}</a>
                    @if($property->city)
                        <a href="{{ $listingBase }}?city={{ urlencode($property->city) }}">{{ $property->city }}</a>
                    @endif
                    <span>{{ $property->title }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>
            </div>

            {{-- Image carousel --}}
            <div class="fw-carousel-container">
                <div class="fw-carousel-wrap">
                    <div class="fw-carousel lightgallery">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach($carouselImages as $img)
                                <div class="swiper-slide hov_zoom property-lazy-wrap">
                                    <div class="property-lazy-loader"></div>
                                    <img class="property-lazy-img" data-src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" loading="lazy">
                                    <a href="{{ $img['url'] }}" class="box-media-zoom popup-image"><i class="fal fa-search"></i></a>
                                </div>
                                @endforeach
                                @if(empty($carouselImages))
                                <div class="swiper-slide hov_zoom">
                                    <img src="{{ asset('theme/images/all/1.jpg') }}" alt="{{ $property->title }}">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fw-carousel-button-prev slider-button"><i class="fa-solid fa-caret-left"></i></div>
                <div class="fw-carousel-button-next slider-button"><i class="fa-solid fa-caret-right"></i></div>
                <div class="fwc-controls_wrap">
                    <div class="solid-pagination_btns fwc-pagination"></div>
                </div>
            </div>

            <div class="container">
                <div class="main-content">
                    <div class="boxed-container">
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="scroll-content-wrap">
                                    <div class="share-holder init-fix-column">
                                        <span class="share-title property-contact-title">Contact</span>
                                        <div class="property-contact-wrap">
                                            @php
                                                $cs = \App\Models\ContactSetting::instance();
                                                $contactPhoneRaw = $cs->phone ?: '';
                                                $contactPhone = $contactPhoneRaw ? preg_replace('/\s+/', '', $contactPhoneRaw) : '';
                                                $whRaw = $cs->whatsapp ?: $contactPhoneRaw;
                                                $contactWhatsapp = $whRaw ? preg_replace('/\D/', '', $whRaw) : '';
                                                $contactEmail = $cs->email ?: '';
                                            @endphp
                                            @if($contactPhone)
                                            <a href="tel:{{ $contactPhone }}" class="property-contact-link tolt" title="Phone" data-microtip-position="right" data-tooltip="Phone"><i class="fa-solid fa-phone"></i></a>
                                            @endif
                                            @if($contactWhatsapp)
                                            <a href="https://wa.me/{{ $contactWhatsapp }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="WhatsApp" data-microtip-position="right" data-tooltip="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                            @endif
                                            @if($contactEmail)
                                            <a href="mailto:{{ $contactEmail }}" class="property-contact-link tolt" title="Email" data-microtip-position="right" data-tooltip="Email"><i class="fa-solid fa-envelope"></i></a>
                                            @endif
                                            @if($cs->facebook)
                                            <a href="{{ $cs->facebook }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="Facebook" data-microtip-position="right" data-tooltip="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                                            @endif
                                            @if($cs->instagram)
                                            <a href="{{ $cs->instagram }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="Instagram" data-microtip-position="right" data-tooltip="Instagram"><i class="fa-brands fa-instagram"></i></a>
                                            @endif
                                            @if($cs->linkedin)
                                            <a href="{{ $cs->linkedin }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="LinkedIn" data-microtip-position="right" data-tooltip="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                                            @endif
                                            @if($cs->youtube)
                                            <a href="{{ $cs->youtube }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="YouTube" data-microtip-position="right" data-tooltip="YouTube"><i class="fa-brands fa-youtube"></i></a>
                                            @endif
                                            @if($cs->twitter)
                                            <a href="{{ $cs->twitter }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="Twitter (X)" data-microtip-position="right" data-tooltip="Twitter (X)"><i class="fa-brands fa-x-twitter"></i></a>
                                            @endif
                                            @if($cs->tiktok)
                                            <a href="{{ $cs->tiktok }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="TikTok" data-microtip-position="right" data-tooltip="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="list-single-opt_header hsc_flat_bci">
                                        <div class="hero-section_categories">
                                            <a href="{{ $listingBase }}?purpose={{ $property->purpose ?? 'sale' }}">{{ strtoupper($purposeLabel) }}</a>
                                            @if(!empty($property->property_type))
                                                <a href="{{ $listingBase }}?property_type={{ urlencode($property->property_type) }}">{{ strtoupper($property->property_type) }}</a>
                                            @endif
                                            @foreach($property->projectTypes as $pt)
                                                <a href="{{ $listingBase }}?project_type={{ $pt->id }}">{{ strtoupper($pt->name) }}</a>
                                            @endforeach
                                        </div>
                                        <div class="hero-opt-btnns">
                                            <button type="button" class="like-btn tolt wishlist-btn" data-property-id="{{ $property->id }}" data-microtip-position="left" data-tooltip="Save" aria-label="Save to wishlist"><i class="fa-regular fa-heart wishlist-icon"></i></button>
                                            <a href="#single_cf" class="custom-scroll-link tolt" data-microtip-position="left" data-tooltip="Contact to View"><i class="fa-light fa-envelope"></i></a>
                                        </div>
                                    </div>

                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            <div class="hero-section-title_container hsc_flat">
                                                <div class="hero-section-title">
                                                    <h1>{{ $property->title }}</h1>
                                                    @if($fullAddress)
                                                        <h4><i class="fa-solid fa-location-dot"></i> <span>{{ $fullAddress }}</span></h4>
                                                    @endif
                                                    <div class="property-single-header-price"><strong>Price:</strong> <span class="pshp_item">{{ $priceFormatted }}</span></div>
                                                </div>
                                                <div class="hero-section-opt">
                                                    <div class="property-single-header-date author_avatar_ps">
                                                        @if($property->dealer && $property->dealer->slug)
                                                        <a href="{{ route('dealer.show', $property->dealer->slug) }}"><span><img src="{{ $dealerImageUrl }}" alt="{{ $dealerName }}" width="40" height="40"> By {{ $dealerName }}</span></a>
                                                        @else
                                                        <span><img src="{{ $dealerImageUrl }}" alt="{{ $dealerName }}" width="40" height="40"> By {{ $dealerName }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Facts --}}
                                    <div class="ps-facts-wrapper">
                                        @if($property->bedrooms !== null && $property->bedrooms !== '')
                                        <div class="ps-facts-item">
                                            <h4>Bedroom</h4>
                                            <h5>{{ $property->bedrooms }}</h5>
                                            <i class="fa-light fa-bed"></i>
                                        </div>
                                        @endif
                                        @if($property->bathrooms !== null && $property->bathrooms !== '')
                                        <div class="ps-facts-item">
                                            <h4>Bathroom</h4>
                                            <h5>{{ $property->bathrooms }}</h5>
                                            <i class="fa-light fa-bath"></i>
                                        </div>
                                        @endif
                                        @if($property->area_marla !== null && $property->area_marla !== '')
                                        <div class="ps-facts-item">
                                            <h4>Area</h4>
                                            <h5>{{ $property->area_marla }} Marla</h5>
                                            <i class="fa-light fa-chart-area"></i>
                                        </div>
                                        @endif
                                        @if($property->kitchen !== null && $property->kitchen !== '')
                                        <div class="ps-facts-item">
                                            <h4>Kitchen</h4>
                                            <h5>{{ $property->kitchen }}</h5>
                                            <i class="fa-light fa-utensils"></i>
                                        </div>
                                        @endif
                                    </div>

                                    {{-- About this Property --}}
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>About this Property</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            @if($property->description)
                                                <div class="property-description">
                                                    {!! $property->description !!}
                                                </div>
                                            @else
                                                <p class="text-muted">No description available for this property.</p>
                                            @endif
                                            <div class="pp-single-opt-wrap">
                                                <div class="pp-single-opt-links">
                                                    <ul>
                                                        <li><a href="#property-image-gallery" class="custom-scroll-link"><i class="fa-light fa-images"></i> Image Gallery</a></li>
                                                        <li><a href="#property-video-gallery" class="custom-scroll-link"><i class="fa-light fa-video"></i> Video Gallery</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Property Amenities (pills with icons + description at end) --}}
                                    @if(!empty($amenitiesList) || $property->amenities_description)
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Property Amenities</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            @if(!empty($amenitiesList))
                                            <div class="property-amenities-pills">
                                                @foreach($amenitiesList as $amenity)
                                                @php
                                                    $title = is_string($amenity) ? $amenity : ($amenity['title'] ?? $amenity['name'] ?? '');
                                                    $iconName = is_array($amenity) ? ($amenity['icon'] ?? '') : '';
                                                @endphp
                                                @if($title !== '' || $iconName !== '')
                                                <span class="property-amenity-pill">
                                                    @if($iconName)
                                                        <img src="{{ iconify_url($iconName, 22) }}" alt="" class="property-amenity-icon" loading="lazy" onerror="this.style.display='none'">
                                                    @endif
                                                    <span class="property-amenity-title">{{ $title }}</span>
                                                </span>
                                                @endif
                                                @endforeach
                                            </div>
                                            @endif
                                            @if($property->amenities_description)
                                                <div class="amenities-description">{!! $property->amenities_description !!}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Featured video (custom modal: close + body scroll lock) --}}
                                    @if($featuredVideoId)
                                    <div class="banner-widget-wrap">
                                        <div class="bg-wrap bg-parallax-wrap-gradien fs-wrapper">
                                            <div class="bg" data-bg="{{ $featuredVideoThumbUrl }}"></div>
                                        </div>
                                        <div class="banner-widget_content">
                                            <button type="button" class="video-box-btn" id="featured-video-btn" data-embed-url="{{ $featuredVideoEmbedUrl }}" aria-label="Play video"><i class="fas fa-play"></i></button>
                                            <h5><span>Property Video Presentation</span></h5>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Features & Nearby: each has its own section like Property Amenities (simple data, no icons) --}}
                                    @php
                                        // Hidden on request: Features/Nearby sections on property detail page.
                                        $featuresSections = [];
                                    @endphp
                                    @foreach($featuresSections as $key => $label)
                                        @php $items = $property->$key ?? []; $items = is_array($items) ? array_filter(array_map('trim', $items)) : []; @endphp
                                        @if(!empty($items))
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>{{ $label }}</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="features-block-tags">
                                                @foreach($items as $item)
                                                <span class="features-block-tag">{{ $item }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                        @endif
                                    @endforeach

                                    {{-- Location map (Google marker from lat/long) --}}
                                    @if($property->latitude && $property->longitude)
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Property Location</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="map-container mapC_vis">
                                                <div id="singleMap" class="single-map-container fs-wrapper"
                                                    data-latitude="{{ $property->latitude }}"
                                                    data-longitude="{{ $property->longitude }}"
                                                    data-mapTitle="Property Location"
                                                    data-infotitle="{{ $property->title }}"
                                                    data-infotext="{{ $fullAddress }}"></div>
                                                <div class="scrollContorl"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Property Gallery (listing-single2 design, no tabs) - inside main column --}}
                                    <div id="property-image-gallery" class="boxed-content etihad-scroll-anchor">
                                        <div class="boxed-content-title">
                                            <h3>Property Gallery</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            @if(!empty($carouselImages))
                                                <div class="gallery-items gisp grid-small-pad list-single-gallery three-coulms lightgallery">
                                        @foreach($carouselImages as $idx => $img)
                                        <div class="gallery-item{{ $idx === 1 ? ' gallery-item-second' : '' }}">
                                            <div class="grid-item-holder hovzoom property-lazy-wrap">
                                                <div class="property-lazy-loader"></div>
                                                <img class="property-lazy-img" data-src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" loading="lazy">
                                                <a href="{{ $img['url'] }}" class="gal-link popup-image"><i class="fa fa-search"></i></a>
                                            </div>
                                        </div>
                                        @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">No images in gallery.</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Video Gallery (YouTube / embeds) - inside main column --}}
                                    <div id="property-video-gallery" class="boxed-content etihad-scroll-anchor">
                                        <div class="boxed-content-title">
                                            <h3>Video Gallery</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            @if(!empty($videoGalleryUrls))
                                                <div class="property-video-gallery-wrap">
                                                    @foreach($videoGalleryUrls as $embedUrl)
                                                    <div class="property-video-embed property-lazy-wrap">
                                                        <div class="property-lazy-loader"></div>
                                                        <iframe data-src="{{ $embedUrl }}" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-muted">No videos in gallery.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="sb-container">
                                    @if($projectTypeNames->isNotEmpty() || $property->purpose || !empty($property->property_type))
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Property Tags</h3>
                                        </div>
                                        <div class="boxed-content-item bc-item_smal_pad">
                                            <div class="tags-widget">
                                                <a href="{{ $listingBase }}?purpose={{ $property->purpose ?? 'sale' }}">{{ strtoupper($purposeLabel) }}</a>
                                                @if(!empty($property->property_type))
                                                    <a href="{{ $listingBase }}?property_type={{ urlencode($property->property_type) }}">{{ strtoupper($property->property_type) }}</a>
                                                @endif
                                                @foreach($property->projectTypes as $pt)
                                                    <a href="{{ $listingBase }}?project_type={{ $pt->id }}">{{ strtoupper($pt->name) }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="fixed-form-wrap">
                                        <div class="fixed-form">
                                            <div class="boxed-content">
                                                <div class="boxed-content-title">
                                                    <h3>Request a Showing</h3>
                                                </div>
                                                <div class="boxed-content-item">
                                                    <div class="property-contacts-wrap">
                                                        <div class="property-contacts-item sh-links">
                                                            <div class="property-contacts_profile">
                                                                @if($property->dealer && $property->dealer->slug)
                                                                <a href="{{ route('dealer.show', $property->dealer->slug) }}" class="property-contacts_profile_link">
                                                                    <img src="{{ $dealerImageUrl }}" alt="{{ $dealerName }}" width="48" height="48">
                                                                    &nbsp;&nbsp;{{ $dealerName }}
                                                                </a>
                                                                @else
                                                                <span class="property-contacts_profile_link">
                                                                    <img src="{{ $dealerImageUrl }}" alt="{{ $dealerName }}" width="48" height="48">
                                                                    &nbsp;&nbsp;{{ $dealerName }}
                                                                </span>
                                                                @endif
                                                            </div>
                                                            @php
                                                                $cs = \App\Models\ContactSetting::instance();
                                                                $globalPhoneRaw = $cs->phone ?: '';
                                                                $globalPhone = $globalPhoneRaw ? preg_replace('/\s+/', '', $globalPhoneRaw) : '';
                                                                $globalWhRaw = $cs->whatsapp ?: $globalPhoneRaw;
                                                                $globalWhatsapp = $globalWhRaw ? preg_replace('/\D/', '', $globalWhRaw) : '';
                                                            @endphp
                                                            @if($property->dealer && ($property->dealer->phone || $property->dealer->whatsapp))
                                                            <div class="property-contacts-links">
                                                                @if($property->dealer->phone)
                                                                    <a href="tel:{{ preg_replace('/\s+/', '', $property->dealer->phone) }}" class="tolt pcl_btn" data-microtip-position="left" data-tooltip="Call"><i class="fa-solid fa-phone"></i></a>
                                                                @endif
                                                                @if($property->dealer->whatsapp)
                                                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $property->dealer->whatsapp) }}" target="_blank" rel="noopener" class="pcl_btn tolt" data-microtip-position="left" data-tooltip="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                                                @endif
                                                            </div>
                                                            @elseif($globalPhone || $globalWhatsapp)
                                                            <div class="property-contacts-links">
                                                                @if($globalPhone)
                                                                    <a href="tel:{{ $globalPhone }}" class="tolt pcl_btn" data-microtip-position="left" data-tooltip="Call"><i class="fa-solid fa-phone"></i></a>
                                                                @endif
                                                                @if($globalWhatsapp)
                                                                    <a href="https://wa.me/{{ $globalWhatsapp }}" target="_blank" rel="noopener" class="pcl_btn tolt" data-microtip-position="left" data-tooltip="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                                                @endif
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="log-separator"><span>or</span></div>
                                                        <p>Use the form below to request a viewing.</p>
                                                    </div>
                                                    <div id="property-request-wrap">
                                                        <div class="custom-form property-request-form no-icons" id="single_cf">
                                                            <form method="post" action="{{ route('property.request-showing') }}" name="contact-property-form" id="property-request-form">
                                                                @csrf
                                                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                                                <input type="hidden" name="type" value="{{ ($property->dealer_id && $property->dealer_id != 0) ? 'dealer' : 'own' }}">
                                                                <input type="hidden" name="dealer_id" value="{{ $property->dealer_id ?? 0 }}">
                                                                <div class="cs-intputwrap">
                                                                    <input name="name" type="text" placeholder="Your name" required>
                                                                </div>
                                                                <div class="cs-intputwrap">
                                                                    <input name="phone" type="text" placeholder="Your Phone">
                                                                </div>
                                                                <div class="cs-intputwrap">
                                                                    <input name="email" type="email" placeholder="Your Email">
                                                                </div>
                                                                <div class="cs-intputwrap">
                                                                    <textarea name="message" placeholder="Message" rows="3"></textarea>
                                                                </div>
                                                                <div class="property-request-message etihad-is-hidden" id="property-request-message"></div>
                                                                <button type="submit" class="commentssubmit commentssubmit_fw" id="property-request-btn">Send Request</button>
                                                            </form>
                                                        </div>
                                                        <div class="property-request-submitted etihad-is-hidden" id="property-request-submitted">
                                                            <p class="property-request-success-text">Your request has been sent successfully.</p>
                                                            <div class="property-request-summary" id="property-request-summary"></div>
                                                            <button type="button" class="commentssubmit commentssubmit_fw property-request-again-btn" id="property-request-again-btn">Request Again</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="limit-box"></div>

                        @if(!empty($morePropertiesData))
                        @php $moreCount = count($morePropertiesData); $useSlider = $moreCount > 3; @endphp
                        <div class="boxed-container" id="more-properties-section">
                            <div class="boxed-content-title bcst_ca">
                                <h3>{{ $morePropertiesHeading }}</h3>
                            </div>
                            @if(!$useSlider)
                            {{-- Static grid when 1–3 properties (no slider, no loop duplication) --}}
                            <div class="more-properties-grid row etihad-related-grid">
                                @foreach($morePropertiesData as $p)
                                <div class="col-lg-4 col-md-4 col-sm-6">
                                    @include('partials.property-card', ['p' => $p, 'listing_base' => $listingBase])
                                </div>
                                @endforeach
                            </div>
                            @else
                            {{-- Slider only when more than 3 properties; custom init with loop: false --}}
                            <div class="single-carousel-wrap more-properties-carousel-wrap etihad-carousel-pad">
                                <div class="more-properties-carousel">
                                    <div class="swiper-container" id="more-properties-swiper">
                                        <div class="swiper-wrapper">
                                            @foreach($morePropertiesData as $p)
                                            <div class="swiper-slide">
                                                @include('partials.property-card', ['p' => $p, 'listing_base' => $listingBase])
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="ss-carousel-pagination_wrap">
                                        <div class="solid-pagination_btns" id="more-properties-pagination"></div>
                                    </div>
                                    <div class="ss-carousel-button-wrap">
                                        <div class="ss-carousel-button ss-carousel-button-prev" id="more-properties-prev"><i class="fas fa-caret-left"></i></div>
                                        <div class="ss-carousel-button ss-carousel-button-next" id="more-properties-next"><i class="fas fa-caret-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @else
                        <div class="boxed-container">
                            <div class="boxed-content-title bcst_ca">
                                <h3>More Properties</h3>
                            </div>
                            <p class="text-muted"><a href="{{ $listingBase }}">Browse all properties</a> to find more listings.</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white hero-corner-tl"></div>
                    <div class="svg-corner svg-corner_white hero-corner-tr"></div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>

    @include('partials.theme-panels')
</div>

@push('scripts')
<script>
(function() {
    var el = document.getElementById('more-properties-swiper');
    if (!el || typeof Swiper === 'undefined') return;
    new Swiper('#more-properties-swiper', {
        preloadImages: false,
        slidesPerView: 3,
        spaceBetween: 20,
        loop: false,
        autoHeight: false,
        grabCursor: true,
        mousewheel: false,
        pagination: { el: '#more-properties-pagination', clickable: true },
        navigation: { nextEl: '#more-properties-next', prevEl: '#more-properties-prev' },
        breakpoints: {
            1064: { slidesPerView: 2, spaceBetween: 10 },
            768: { slidesPerView: 1, spaceBetween: 0, autoHeight: true }
        }
    });
})();

(function() {
    var modal = document.getElementById('featured-video-modal');
    var openBtn = document.getElementById('featured-video-btn');
    var closeBtn = document.getElementById('featured-video-close');
    var iframe = document.getElementById('featured-video-iframe');
    if (modal && openBtn) {
        function openModal() {
            var url = openBtn.getAttribute('data-embed-url');
            if (url && iframe) { iframe.src = url; }
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('property-video-modal-open');
            document.documentElement.classList.add('property-video-modal-open');
        }
        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('property-video-modal-open');
            document.documentElement.classList.remove('property-video-modal-open');
            if (iframe) { iframe.src = ''; }
        }
        openBtn.addEventListener('click', function(e) { e.preventDefault(); openModal(); });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal(); });
    }
})();

(function() {
    var propertyId = {{ $property->id }};
    var cookieName = 'etihad_property_requests';
    var formEl = document.getElementById('property-request-form');
    var formWrap = document.querySelector('.property-request-form');
    var submittedEl = document.getElementById('property-request-submitted');
    var summaryEl = document.getElementById('property-request-summary');
    var messageEl = document.getElementById('property-request-message');
    var btnEl = document.getElementById('property-request-btn');
    var againBtn = document.getElementById('property-request-again-btn');

    function getStoredRequests() {
        var match = document.cookie.match(new RegExp('\\b' + cookieName + '=([^;]+)'));
        if (!match) return {};
        try { return JSON.parse(decodeURIComponent(match[1])); } catch (e) { return {}; }
    }
    function setStoredRequest(pid, data) {
        var all = getStoredRequests();
        all[pid] = data;
        document.cookie = cookieName + '=' + encodeURIComponent(JSON.stringify(all)) + ';path=/;max-age=31536000;SameSite=Lax';
    }
    function showSubmitted(data) {
        if (!data) data = {};
        if (formWrap) formWrap.classList.add('etihad-is-hidden');
        if (submittedEl) submittedEl.classList.remove('etihad-is-hidden');
        if (summaryEl) {
            var html = '';
            if (data.name) html += '<p><strong>Name:</strong> ' + escapeHtml(data.name) + '</p>';
            if (data.phone) html += '<p><strong>Phone:</strong> ' + escapeHtml(data.phone) + '</p>';
            if (data.email) html += '<p><strong>Email:</strong> ' + escapeHtml(data.email) + '</p>';
            if (data.message) html += '<p><strong>Message:</strong> ' + escapeHtml(data.message) + '</p>';
            summaryEl.innerHTML = html || '<p>Your request was submitted.</p>';
        }
    }
    function showForm() {
        if (formWrap) formWrap.classList.remove('etihad-is-hidden');
        if (submittedEl) submittedEl.classList.add('etihad-is-hidden');
        if (messageEl) { messageEl.classList.add('etihad-is-hidden'); messageEl.className = 'property-request-message etihad-is-hidden'; messageEl.textContent = ''; }
    }
    function escapeHtml(s) {
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    if (formEl && submittedEl) {
        var stored = getStoredRequests()[propertyId];
        if (stored && (stored.name || stored.message)) {
            showSubmitted(stored);
        }

        formEl.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!btnEl) return;
            var fd = new FormData(formEl);
            btnEl.classList.add('is-loading');
            btnEl.disabled = true;
            if (messageEl) { messageEl.classList.add('etihad-is-hidden'); messageEl.className = 'property-request-message etihad-is-hidden'; messageEl.textContent = ''; }

            fetch(formEl.action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, json: j }; }); })
                .then(function(result) {
                    btnEl.classList.remove('is-loading');
                    btnEl.disabled = false;
                    if (result.ok && result.json.success) {
                        var data = { name: fd.get('name') || '', phone: fd.get('phone') || '', email: fd.get('email') || '', message: fd.get('message') || '' };
                        setStoredRequest(propertyId, data);
                        showSubmitted(data);
                    } else {
                        var msg = (result.json && result.json.message) || 'Something went wrong. Please try again.';
                        if (result.json && result.json.errors) {
                            var first = Object.keys(result.json.errors)[0];
                            if (first && result.json.errors[first][0]) msg = result.json.errors[first][0];
                        }
                        if (messageEl) {
                            messageEl.textContent = msg;
                            messageEl.className = 'property-request-message error';
                            messageEl.classList.remove('etihad-is-hidden');
                        }
                    }
                })
                .catch(function() {
                    btnEl.classList.remove('is-loading');
                    btnEl.disabled = false;
                    if (messageEl) {
                        messageEl.textContent = 'Something went wrong. Please try again.';
                        messageEl.className = 'property-request-message error';
                        messageEl.classList.remove('etihad-is-hidden');
                    }
                });
        });

        if (againBtn) {
            againBtn.addEventListener('click', function() {
                showForm();
            });
        }
    }
})();

(function() {
    function loadAfterPageReady() {
        document.querySelectorAll('.property-lazy-img[data-src]').forEach(function(img) {
            var wrap = img.closest('.property-lazy-wrap');
            var src = img.getAttribute('data-src');
            if (!src) return;
            img.onload = function() { if (wrap) wrap.classList.add('loaded'); };
            img.src = src;
        });
        document.querySelectorAll('.property-video-embed iframe[data-src]').forEach(function(iframe) {
            var wrap = iframe.closest('.property-lazy-wrap');
            var src = iframe.getAttribute('data-src');
            if (!src) return;
            iframe.onload = function() { if (wrap) wrap.classList.add('loaded'); };
            iframe.src = src;
        });
    }
    if (document.readyState === 'complete') {
        loadAfterPageReady();
    } else {
        window.addEventListener('load', loadAfterPageReady);
    }
})();
</script>
@if($property->latitude && $property->longitude)
@php $googleMapsKey = config('app.google_maps_api_key') ?: 'AIzaSyAYrLB-ltxWv32OFEF6c07B376JNrDyOIA'; @endphp
<script>
window.initPropertyMap = function() {
    var el = document.getElementById('singleMap');
    if (!el || typeof google === 'undefined' || !google.maps) return;
    var lat = parseFloat(el.getAttribute('data-latitude')) || 0;
    var lng = parseFloat(el.getAttribute('data-longitude')) || 0;
    var title = el.getAttribute('data-infotitle') || '';
    var text = el.getAttribute('data-infotext') || '';
    var center = { lat: lat, lng: lng };
    var mapOpts = {
        zoom: 14,
        center: center,
        scrollwheel: false,
        zoomControl: true,
        fullscreenControl: true,
        mapTypeControl: false,
        streetViewControl: true
    };
    if (window.EtihadMap) EtihadMap.applyToMapOptions(mapOpts);
    var map = new google.maps.Map(el, mapOpts);
    var marker = window.EtihadMap
        ? EtihadMap.createMarker({ position: center, map: map })
        : new google.maps.Marker({ position: center, map: map });
    if (title || text) {
        var info = new google.maps.InfoWindow({
            content: '<div class="info-window-content"><h3>' + (title || '') + '</h3><p>' + (text || '') + '</p></div>'
        });
        marker.addListener('click', function() { info.open(map, marker); });
    }
    var scrollBtn = document.querySelector('.map-container .scrollContorl');
    if (scrollBtn) {
        scrollBtn.addEventListener('click', function(e) {
            e.preventDefault();
            this.classList.toggle('enabledsroll');
            map.setOptions({ scrollwheel: this.classList.contains('enabledsroll') });
        });
    }
};
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initPropertyMap" async defer></script>
@endif
@endpush
@endsection
