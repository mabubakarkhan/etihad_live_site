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

@push('styles')
<meta name="description" content="{{ $metaDesc }}">
<link rel="canonical" href="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDesc }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:image" content="{{ $featuredUrl }}">
<meta property="og:type" content="website">
<style>
.property-description { text-align: left; }
.property-description img { max-width: 100%; height: auto; }
.boxed-content-item .property-description { text-align: left; }
.banner-widget-wrap .banner-widget_content h5 { text-align: center; }
/* Section headings: match theme from listing-single.html */
.boxed-content-title.section-heading-wrap { padding: 20px 50px; }
.boxed-content-title.section-heading-wrap h2,
.boxed-content-title.section-heading-wrap h3 { font-size: 1.15rem; font-weight: 700; color: #1f2937; margin: 0; }
/* Features & Nearby: each section with tag-style items (no bullets, no icons) */
.features-block { margin-bottom: 1.5rem; }
.features-block:last-child { margin-bottom: 0; }
.features-block-title { font-size: 1rem; font-weight: 700; color: #374151; margin: 0 0 10px 0; }
.features-block-tags { display: flex; flex-wrap: wrap; gap: 10px; margin: 0; list-style: none; padding: 0; }
.features-block-tag { display: inline-block; padding: 10px 18px; background: #fff; border: 1px solid #e8e8e8; border-radius: 999px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); font-size: 14px; color: #374151; }
/* Property Amenities pills + description (white pills, orange icon + text) */
.property-amenities-pills { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 0; }
.property-amenity-pill { display: inline-flex; align-items: center; gap: 10px; padding: 12px 20px; background: #fff; border: 1px solid #e8e8e8; border-radius: 999px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); font-size: 14px; }
.property-amenity-pill .property-amenity-icon { width: 22px; height: 22px; flex-shrink: 0; }
.property-amenity-pill .property-amenity-title { color: var(--main-color, #EE7838); font-weight: 600; }
.property-amenities-pills .property-amenity-pill img.property-amenity-icon { filter: invert(48%) sepia(79%) saturate(1200%) hue-rotate(350deg) brightness(98%) contrast(95%); }
.amenities-description { text-align: left; font-size: 15px; line-height: 1.6; color: #4b5563; }
.property-amenities-pills + .amenities-description { margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #eee; }
.amenities-description p { margin-bottom: 0.75em; }
.amenities-description p:last-child { margin-bottom: 0; }
.amenities-description img { max-width: 100%; height: auto; }
.banner-widget-wrap .banner-widget_content h5 { text-align: center; }
.property-video-embed { max-width: 100%; }
.property-video-embed iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }
.property-video-gallery-wrap { max-width: 100%; }
/* Property Gallery: match listing-single2.html (no tabs), asymmetric grid, no overlap */
#property-image-gallery .gallery-items { overflow: hidden; }
#property-image-gallery .gallery-items::after { content: ''; display: table; clear: both; }
#property-image-gallery .gallery-item,
#property-image-gallery .gallery-item-second { box-sizing: border-box; }
#property-image-gallery .gallery-item .grid-item-holder { width: 100%; }
#property-image-gallery .gallery-item img { width: 100%; height: auto; display: block; }
.map-container .single-map-container,
.map-container #singleMap { width: 100%; height: 100%; min-height: 400px; }
/* Contact (replaces Share): same column layout, icon links */
.property-contact-title { font-size: 9px; }
.property-contact-wrap { display: inline-block; border: 1px solid #eee; border-radius: 4px; overflow: hidden; vertical-align: top; }
.property-contact-wrap .property-contact-link { display: block; width: 42px; height: 42px; line-height: 42px; text-align: center; border-top: 1px solid #eee; color: #666; font-size: 1.1em; transition: color .2s, background .2s; }
.property-contact-wrap .property-contact-link:first-child { border-top: none; }
.property-contact-wrap .property-contact-link:hover { color: var(--main-color, #EE7838); background: #fafafa; }
.property-contacts-item { padding: 19px 10px 15px 10px; }
/* Property request form: loading & submitted state */
.property-request-message { margin-bottom: 12px; padding: 10px 12px; border-radius: 6px; font-size: 14px; }
.property-request-message.error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.property-request-message.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
#property-request-btn.is-loading { opacity: 0.7; pointer-events: none; }
.property-request-submitted { padding: 10px 0; }
.property-request-success-text { margin-bottom: 12px; font-weight: 600; color: #166534; }
.property-request-summary { margin-bottom: 16px; padding: 12px; background: #f8fafc; border-radius: 8px; font-size: 14px; color: #334155; }
.property-request-summary p { margin: 4px 0; }
.property-request-again-btn { background: #334155 !important; color: #fff !important; }
.pp-single-opt-wrap { margin-top: 1.5rem; }
.pp-single-opt-links ul { list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
.pp-single-opt-links a { display: inline-flex; align-items: center; gap: 8px; padding: 10px 18px; border-radius: 999px; border: 1px solid #e5e7eb; background: #fff; color: #374151; text-decoration: none; font-size: 14px; transition: border-color .2s, color .2s; }
.pp-single-opt-links a:hover { border-color: var(--main-color, #EE7838); color: var(--main-color, #EE7838); }
.pp-single-opt-links a i { color: var(--main-color, #EE7838); }
/* Wishlist: top (like-btn) + cards; saved state = filled heart */
.like-btn.wishlist-btn { cursor: pointer; border: none; background: transparent; padding: 0; }
/* Property header icons (heart + email): force single row alignment */
.list-single-opt_header .hero-opt-btnns { width: auto; display: flex; align-items: center; gap: 10px; }
.list-single-opt_header .hero-opt-btnns a { float: none; margin-bottom: 0; }
/* Make wishlist heart button match the circular email icon (hero-opt-btnns a) */
.hero-opt-btnns .like-btn.wishlist-btn {
    width: 46px;
    height: 46px;
    line-height: 46px;
    border-radius: 50%;
    box-shadow: 0px 0px 0px 8px rgba(255,255,255,0.2);
    background: #fff;
    transition: all .2s ease-in-out;
    margin-bottom: 0;
    display: block;
    text-align: center;
    outline: 0;
}
.hero-opt-btnns .like-btn.wishlist-btn:hover { box-shadow: 0px 0px 0px 0px rgba(255,255,255,0.2); }
.hero-opt-btnns .like-btn.wishlist-btn:focus,
.hero-opt-btnns .like-btn.wishlist-btn:focus-visible { outline: 0; }
.hero-opt-btnns .like-btn.wishlist-btn .wishlist-icon {
    line-height: 46px;
    font-size: 1.3em;
    font-weight: 400;
    transition: color .2s;
    color: #94a3b8;
}
.hero-opt-btnns .like-btn.wishlist-btn.wishlist-saved .wishlist-icon {
    font-weight: 900;
    color: var(--main-color, #EE7838);
}
.like-btn.wishlist-btn .wishlist-icon { transition: color .2s; color: #94a3b8; }
.like-btn.wishlist-btn.wishlist-saved .wishlist-icon { font-weight: 900; color: var(--main-color, #EE7838); }
#more-properties-section .geodir_save-btn.wishlist-btn .wishlist-icon { transition: color .2s; color: #94a3b8; font-weight: 400; }
#more-properties-section .geodir_save-btn.wishlist-btn.wishlist-saved .wishlist-icon { font-weight: 900; color: var(--main-color, #EE7838); }
/* Related properties: remove default button border/outline (keep shadow) */
#more-properties-section .geodir_save-btn.wishlist-btn { border: 0 !important; outline: 0 !important; }
#more-properties-section .geodir_save-btn.wishlist-btn:focus,
#more-properties-section .geodir_save-btn.wishlist-btn:focus-visible { outline: 0 !important; box-shadow: 0px 0px 0px 8px rgba(255,255,255,0.2) !important; }
/* More properties: slider nav/dots alignment (reference listing-single.html) */
.more-properties-carousel-wrap .ss-carousel-pagination_wrap { position: absolute; bottom: -1px; left: 40px; z-index: 10; background: #fff; padding: 0 30px; height: 40px; line-height: 40px; border-radius: 20px 20px 0 0; border: 1px solid #eee; border-bottom: 1px solid #fff; }
.more-properties-carousel-wrap .ss-carousel-button-wrap { position: absolute; bottom: 14px; right: 40px; z-index: 5; }
/* Property detail: full page scroll to end (main in flow, no inner scroll cutoff) */
.property-detail-main { position: relative !important; height: auto !important; min-height: 100vh; overflow: visible !important; }
.property-detail-main .wrapper { overflow: visible !important; }
body:has(.property-detail-main) { height: auto !important; min-height: 100vh; }
html:has(.property-detail-main) { height: auto !important; min-height: 100%; }
body.property-video-modal-open { overflow: hidden; }
html.property-video-modal-open { overflow: hidden; }
.property-video-modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 99999; display: none; align-items: center; justify-content: center; padding: 20px; }
.property-video-modal-overlay.is-open { display: flex; }
.property-video-modal-inner { position: relative; width: 100%; max-width: 900px; background: #000; border-radius: 12px; overflow: hidden; z-index: 100000; }
.property-video-modal-close { position: absolute; top: 10px; right: 10px; width: 40px; height: 40px; border: 0; background: rgba(0,0,0,0.6); color: #fff; border-radius: 50%; cursor: pointer; z-index: 10; font-size: 20px; line-height: 1; }
.property-video-modal-close:hover { background: rgba(0,0,0,0.9); }
.property-video-modal-inner iframe { width: 100%; height: 0; padding-bottom: 56.25%; position: relative; display: block; border: 0; }
.property-video-modal-inner iframe { height: 100%; min-height: 400px; padding-bottom: 0; }
.property-lazy-wrap { position: relative; background: #f0f0f0; min-height: 120px; }
.property-lazy-wrap .property-lazy-loader { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: #f0f0f0; }
.property-lazy-wrap.loaded .property-lazy-loader { display: none; }
.property-lazy-loader::after { content: ''; width: 36px; height: 36px; border: 3px solid #e0e0e0; border-top-color: var(--main-color, #EE7838); border-radius: 50%; animation: property-lazy-spin 0.8s linear infinite; }
@keyframes property-lazy-spin { to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div id="main" class="property-detail-main">
    @include('partials.header')
    {{-- Video modal at top level so it covers header and viewport; scroll lock via body class --}}
    @if(!empty($featuredVideoId))
    <div class="property-video-modal-overlay" id="featured-video-modal" aria-hidden="true">
        <div class="property-video-modal-inner">
            <button type="button" class="property-video-modal-close" id="featured-video-close" aria-label="Close">&times;</button>
            <iframe id="featured-video-iframe" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width:100%;min-height:400px;height:400px;border:0;"></iframe>
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
                                <div class="swiper-slide hov_zoom property-lazy-wrap" style="position:relative;">
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
                                                    <h2>{{ $property->title }}</h2>
                                                    @if($fullAddress)
                                                        <h4><i class="fa-solid fa-location-dot"></i> <span>{{ $fullAddress }}</span></h4>
                                                    @endif
                                                    <div class="property-single-header-price"><strong>Price:</strong> <span class="pshp_item">
                                                        @if($property->price_string)
                                                            {{ $property->price_string }}@if($property->price_digits !== null && $property->price_digits !== '') <span>{{ config('app.currency', 'PKR') }} {{ number_format((float) $property->price_digits, 2) }}</span>@endif
                                                        @else
                                                            {{ $priceFormatted }}
                                                        @endif
                                                    </span></div>
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
                                                        <img src="https://api.iconify.design/heroicons-outline/{{ $iconName }}.svg?height=22" alt="" class="property-amenity-icon" loading="lazy" onerror="this.style.display='none'">
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
                                        $featuresSections = [
                                            'features' => 'Features',
                                            'location_accessibility' => 'Location accessibility',
                                            'nearest_hospitals' => 'Nearest hospitals',
                                            'nearest_markets' => 'Nearest markets',
                                            'nearest_restaurants' => 'Nearest restaurants / cafes / bakeries',
                                        ];
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
                                            <div class="map-container mapC_vis" style="position:relative; min-height: 400px;">
                                                <div id="singleMap" class="single-map-container fs-wrapper" style="width:100%; height:400px; min-height:400px;"
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
                                    <div id="property-image-gallery" class="boxed-content" style="scroll-margin-top: 100px;">
                                        <div class="boxed-content-title">
                                            <h3>Property Gallery</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            @if(!empty($carouselImages))
                                                <div class="gallery-items gisp grid-small-pad list-single-gallery three-coulms lightgallery">
                                        @foreach($carouselImages as $idx => $img)
                                        <div class="gallery-item{{ $idx === 1 ? ' gallery-item-second' : '' }}">
                                            <div class="grid-item-holder hovzoom property-lazy-wrap" style="position:relative;">
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
                                    <div id="property-video-gallery" class="boxed-content" style="scroll-margin-top: 100px;">
                                        <div class="boxed-content-title">
                                            <h3>Video Gallery</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            @if(!empty($videoGalleryUrls))
                                                <div class="property-video-gallery-wrap" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                                                    @foreach($videoGalleryUrls as $embedUrl)
                                                    <div class="property-video-embed property-lazy-wrap" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; background: #111;">
                                                        <div class="property-lazy-loader"></div>
                                                        <iframe data-src="{{ $embedUrl }}" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;"></iframe>
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
                                                                <div class="property-request-message" id="property-request-message" style="display:none;"></div>
                                                                <button type="submit" class="commentssubmit commentssubmit_fw" id="property-request-btn">Send Request</button>
                                                            </form>
                                                        </div>
                                                        <div class="property-request-submitted" id="property-request-submitted" style="display:none;">
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
                            <div class="more-properties-grid row" style="margin-top: 60px; row-gap: 30px;">
                                @foreach($morePropertiesData as $p)
                                <div class="col-lg-4 col-md-4 col-sm-6" style="margin-bottom: 24px;">
                                    @include('partials.property-card', ['p' => $p, 'listing_base' => $listingBase])
                                </div>
                                @endforeach
                            </div>
                            @else
                            {{-- Slider only when more than 3 properties; custom init with loop: false --}}
                            <div class="single-carousel-wrap more-properties-carousel-wrap" style="padding-bottom: 60px;">
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
                    <div class="svg-corner svg-corner_white" style="top:0;left:-40px;transform:rotate(-90deg)"></div>
                    <div class="svg-corner svg-corner_white" style="top:0;right:-40px;transform:rotate(-180deg)"></div>
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
        if (formWrap) formWrap.style.display = 'none';
        if (submittedEl) submittedEl.style.display = 'block';
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
        if (formWrap) formWrap.style.display = '';
        if (submittedEl) submittedEl.style.display = 'none';
        if (messageEl) { messageEl.style.display = 'none'; messageEl.className = 'property-request-message'; messageEl.textContent = ''; }
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
            if (messageEl) { messageEl.style.display = 'none'; messageEl.className = 'property-request-message'; messageEl.textContent = ''; }

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
                            messageEl.style.display = 'block';
                        }
                    }
                })
                .catch(function() {
                    btnEl.classList.remove('is-loading');
                    btnEl.disabled = false;
                    if (messageEl) {
                        messageEl.textContent = 'Something went wrong. Please try again.';
                        messageEl.className = 'property-request-message error';
                        messageEl.style.display = 'block';
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
@php $googleMapsKey = config('app.google_maps_api_key') ?: 'AIzaSyDwJSRi0zFjDemECmFl9JtRj1FY7TiTRRo'; @endphp
<script>
window.initPropertyMap = function() {
    var el = document.getElementById('singleMap');
    if (!el || typeof google === 'undefined' || !google.maps) return;
    var lat = parseFloat(el.getAttribute('data-latitude')) || 0;
    var lng = parseFloat(el.getAttribute('data-longitude')) || 0;
    var title = el.getAttribute('data-infotitle') || '';
    var text = el.getAttribute('data-infotext') || '';
    var center = { lat: lat, lng: lng };
    var map = new google.maps.Map(el, {
        zoom: 14,
        center: center,
        scrollwheel: false,
        zoomControl: true,
        fullscreenControl: true,
        mapTypeControl: false,
        streetViewControl: true
    });
    var marker = new google.maps.Marker({ position: center, map: map });
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
