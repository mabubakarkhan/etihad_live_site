@extends('layouts.front')

@php
    $metaTitle = is_string($project->meta_title) && $project->meta_title !== ''
        ? $project->meta_title
        : ($project->title . ' – ' . config('app.name'));

    $rawMetaDesc = null;
    if (is_string($project->meta_description) && $project->meta_description !== '') {
        $rawMetaDesc = $project->meta_description;
    } elseif (is_string($project->description) && $project->description !== '') {
        $rawMetaDesc = $project->description;
    }
    $metaDesc = $rawMetaDesc !== null
        ? \Illuminate\Support\Str::limit(strip_tags($rawMetaDesc), 160)
        : '';
    $featuredUrl = $project->featured_image ? url('storage/' . ltrim($project->featured_image, '/')) : ($project->homepage_listing_image ? url('storage/' . ltrim($project->homepage_listing_image, '/')) : asset('theme/images/all/1.jpg'));
    $gallerySorted = collect($project->gallery ?? [])->sortBy('order')->values();
    $carouselImages = [];
    $carouselImages[] = ['url' => $featuredUrl, 'alt' => $project->title];
    foreach ($gallerySorted as $g) {
        $path = is_array($g) ? ($g['path'] ?? null) : null;
        if ($path) {
            $fullUrl = url('storage/' . ltrim($path, '/'));
            if ($fullUrl !== $featuredUrl) {
                $carouselImages[] = ['url' => $fullUrl, 'alt' => $project->title];
            }
        }
    }
    $fullAddress = implode(', ', array_filter([
        is_string($project->full_address) && $project->full_address !== '' ? $project->full_address : $project->short_address,
        $project->city,
        $project->state,
    ]));

    $price = $project->price !== null && $project->price !== ''
        ? (is_numeric($project->price) ? config('app.currency', 'PKR') . ' ' . number_format((float) $project->price, 0) : (string) $project->price)
        : '';

    $rawUniqueFeatures = $project->unique_features ?? [];
    $uniqueFeatureItems = [];
    if (is_array($rawUniqueFeatures)) {
        foreach ($rawUniqueFeatures as $uf) {
            if (is_array($uf)) {
                $title = isset($uf['title']) && is_string($uf['title']) ? trim($uf['title']) : '';
                $icon = isset($uf['icon']) && is_string($uf['icon']) ? trim($uf['icon']) : '';
            } else {
                $title = trim((string) $uf);
                $icon = '';
            }
            if ($title !== '' || $icon !== '') {
                $uniqueFeatureItems[] = ['title' => $title, 'icon' => $icon];
            }
        }
    }

    $planItems = is_array($project->plans ?? null) ? $project->plans : [];

    $developerLogoUrl = null;
    if (!empty($project->logo) && is_string($project->logo)) {
        $developerLogoUrl = url('storage/' . ltrim($project->logo, '/'));
    }
    $featuredVideoId = null;
    $featuredVideoEmbedUrl = null;
    if ($project->featured_youtube_url && is_string($project->featured_youtube_url)) {
        $raw = trim($project->featured_youtube_url);
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $raw, $m)) {
            $featuredVideoId = $m[1];
        } else {
            $part = explode('?', $raw)[0];
            if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', trim($part))) {
                $featuredVideoId = trim($part);
            }
        }
        if ($featuredVideoId) {
            $featuredVideoEmbedUrl = 'https://www.youtube.com/embed/' . $featuredVideoId;
        }
    }
    $videoGalleryUrls = [];
    $videosRaw = is_array($project->videos ?? null) ? $project->videos : [];
    foreach ($videosRaw as $v) {
        $raw = is_array($v) ? '' : trim((string) $v);
        if ($raw === '') continue;
        if (strpos($raw, '<') !== false && preg_match('/src=["\']([^"\']+)["\']/', $raw, $m)) {
            $videoGalleryUrls[] = $m[1];
        } elseif (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $raw, $m)) {
            $videoGalleryUrls[] = 'https://www.youtube.com/embed/' . $m[1];
        } else {
            $part = explode('?', $raw)[0];
            if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', trim($part))) {
                $videoGalleryUrls[] = 'https://www.youtube.com/embed/' . trim($part);
            }
        }
    }

    $vrTourUrl = is_string($project->vr_tour_url ?? null) ? trim($project->vr_tour_url) : '';
    if ($vrTourUrl !== '' && !preg_match('/^https?:\/\//i', $vrTourUrl)) {
        $vrTourUrl = 'https://' . $vrTourUrl;
    }
    $vrTourPageUrl = $vrTourUrl !== '' ? route('project.vr-tour', ['project' => $project->id]) : '';
@endphp

@section('title', $metaTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => [
    'title' => $metaTitle,
    'description' => $metaDesc,
    'keywords' => seo_str($project->meta_keywords ?? ''),
    'canonical' => seo_str($project->canonical_url ?? '') ?: url()->current(),
    'image' => $featuredUrl,
    'type' => 'website',
]])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/project-old.css') }}">
@endpush

@section('content')
<div id="main" class="project-old-page">
    @include('partials.header')
    @if($vrTourPageUrl !== '')
    <a href="{{ $vrTourPageUrl }}" target="_blank" rel="noopener" class="project-vr-floating-btn" aria-label="Open VR Tour">
        <i class="fa-solid fa-vr-cardboard"></i>
        <span>VR Tour</span>
    </a>
    @endif
    @if($featuredVideoId && $featuredVideoEmbedUrl)
    <div class="project-video-modal-overlay" id="project-video-modal" aria-hidden="true">
        <div class="project-video-modal-inner">
            <button type="button" class="project-video-modal-close" id="project-video-close" aria-label="Close">&times;</button>
            <iframe id="project-video-iframe" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    @endif

    <div class="wrapper">
        <div class="content">
            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/projects') }}">Projects</a>
                    <span>{{ $project->title }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>
            </div>

            {{-- Carousel (listing-single.html) --}}
            <div class="fw-carousel-container">
                <div class="fw-carousel-wrap">
                    <div class="fw-carousel lightgallery">
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                @foreach($carouselImages as $img)
                                <div class="swiper-slide hov_zoom">
                                    <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}">
                                    <a href="{{ $img['url'] }}" class="box-media-zoom popup-image"><i class="fal fa-search"></i></a>
                                </div>
                                @endforeach
                                @if(empty($carouselImages))
                                <div class="swiper-slide hov_zoom">
                                    <img src="{{ asset('theme/images/all/1.jpg') }}" alt="{{ $project->title }}">
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
                        <div class="row justify-content-center">
                            <div class="col-9">
                                <div class="scroll-content-wrap">
                                    <div class="share-holder init-fix-column">
                                        <span class="share-title property-contact-title">Contact</span>
                                        <div class="property-contact-wrap">
                                            @php
                                                $cs = \App\Models\ContactSetting::instance();
                                                $projectPhone = $cs->phone ?: '';
                                                $projectWhatsapp = $cs->whatsapp ?: $projectPhone;
                                                $projectEmail = $cs->email ?: '';
                                                $projectPhoneClean = $projectPhone ? preg_replace('/\s+/', '', $projectPhone) : '';
                                                $projectWhatsappClean = $projectWhatsapp ? preg_replace('/\D/', '', $projectWhatsapp) : '';
                                            @endphp
                                            @if($projectPhoneClean)
                                            <a href="tel:{{ $projectPhoneClean }}" class="property-contact-link tolt" title="Phone" data-microtip-position="right" data-tooltip="Phone"><i class="fa-solid fa-phone"></i></a>
                                            @endif
                                            @if($projectWhatsappClean)
                                            <a href="https://wa.me/{{ $projectWhatsappClean }}" target="_blank" rel="noopener" class="property-contact-link tolt" title="WhatsApp" data-microtip-position="right" data-tooltip="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                            @endif
                                            @if($projectEmail)
                                            <a href="mailto:{{
                                                $projectEmail
                                            }}" class="property-contact-link tolt" title="Email" data-microtip-position="right" data-tooltip="Email"><i class="fa-solid fa-envelope"></i></a>
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
                                            @foreach($project->projectTypes as $pt)
                                            <a href="{{ url('/projects') }}?project_type={{ urlencode($pt->slug) }}">{{ strtoupper($pt->name) }}</a>
                                            @endforeach
                                        </div>
                                        <div class="hero-opt-btnns">
                                            <a href="{{ url('/projects') }}" class="custom-scroll-link tolt" data-microtip-position="left" data-tooltip="All Projects"><i class="fa-light fa-layer-group"></i></a>
                                        </div>
                                    </div>

                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            <div class="hero-section-title_container hsc_flat">
                                                <div class="hero-section-title">
                                                    <h1>{{ $project->title }}</h1>
                                                    @if($fullAddress)
                                                    <h4><i class="fa-solid fa-location-dot"></i> <span>{{ $fullAddress }}</span></h4>
                                                    @endif
                                                    @if($price)
                                                    <div class="property-single-header-price"><strong>Price:</strong> <span class="pshp_item">{{ $price }}</span></div>
                                                    @endif
                                                </div>
                                                <div class="hero-section-opt">
                                                    <div class="property-single-header-date author_avatar_ps"><span>Etihad Marketing</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Project Facts (type, price, city, location) --}}
                                    <div class="ps-facts-wrapper">
                                        @if($project->projectTypes->isNotEmpty())
                                        <div class="ps-facts-item">
                                            <h4>Type</h4>
                                            <h5>{{ $project->projectTypes->pluck('name')->join(', ') }}</h5>
                                            <i class="fa-light fa-layer-group"></i>
                                        </div>
                                        @endif
                                        @if($price)
                                        <div class="ps-facts-item">
                                            <h4>Price</h4>
                                            <h5>{{ $price }}</h5>
                                            <i class="fa-light fa-tag"></i>
                                        </div>
                                        @endif
                                        @if($project->city)
                                        <div class="ps-facts-item">
                                            <h4>City</h4>
                                            <h5>{{ $project->city }}</h5>
                                            <i class="fa-light fa-city"></i>
                                        </div>
                                        @endif
                                        @php $shortLocation = $project->short_address ?: $project->full_address; @endphp
                                        @if($shortLocation)
                                        <div class="ps-facts-item">
                                            <h4>Location</h4>
                                            <h5>{{ $shortLocation }}</h5>
                                            <i class="fa-light fa-location-dot"></i>
                                        </div>
                                        @endif
                                    </div>

                                    @if($project->description)
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>About this Project</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="property-description">{!! $project->description !!}</div>
                                            <div class="pp-single-opt-wrap">
                                                <div class="pp-single-opt-links">
                                                    <ul>
                                                        @if($project->project_file_pdf)
                                                        <li><a href="{{ url('storage/' . ltrim($project->project_file_pdf, '/')) }}" download><i class="fa-light fa-file-pdf"></i> Download Brochure</a></li>
                                                        @endif
                                                        @if(!empty($project->plans))
                                                        <li><a href="#project-gallery" class="custom-scroll-link"><i class="fa-light fa-layer-group"></i> View Plans / Gallery</a></li>
                                                        @endif
                                                        @if($vrTourPageUrl !== '')
                                                        <li><a href="{{ $vrTourPageUrl }}" target="_blank" rel="noopener"><i class="fa-light fa-vr-cardboard"></i> VR Tour</a></li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @php
                                        $aboutDevelopers = $project->about_developers ?? null;
                                        $aboutDevelopersText = is_string($aboutDevelopers) ? trim($aboutDevelopers) : '';
                                    @endphp
                                    @if($developerLogoUrl || $aboutDevelopersText !== '')
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>About Developers</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="project-developer-block">
                                                @if($developerLogoUrl)
                                                <div class="project-developer-logo">
                                                    <img src="{{ $developerLogoUrl }}" alt="Developer logo" loading="lazy">
                                                </div>
                                                @endif
                                                @if($aboutDevelopersText !== '')
                                                <div class="project-developer-text property-description">
                                                    {!! $aboutDevelopersText !!}
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($featuredVideoId && $featuredVideoEmbedUrl)
                                    <div class="banner-widget-wrap">
                                        <div class="bg-wrap bg-parallax-wrap-gradien fs-wrapper">
                                            <div class="bg" data-bg="https://img.youtube.com/vi/{{ $featuredVideoId }}/maxresdefault.jpg"></div>
                                        </div>
                                        <div class="banner-widget_content">
                                            <button type="button" class="video-box-btn" id="project-featured-video-btn" data-embed-url="{{ $featuredVideoEmbedUrl }}" aria-label="Play video"><i class="fas fa-play"></i></button>
                                            <div class="project-featured-video-text">
                                                <h5><span>{{ $project->featured_video_title ?: 'Project Video Presentation' }}</span></h5>
                                                @if($project->featured_video_description)
                                                <div class="project-featured-video-desc">{!! $project->featured_video_description !!}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($project->project_file_pdf)
                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            <div class="project-file-cta">
                                                <div class="project-file-cta-icon">
                                                    <i class="fa-light fa-file-pdf"></i>
                                                </div>
                                                <div class="project-file-cta-text">
                                                    <h3>Download Project File</h3>
                                                    <p>Download project file in PDF for extensive details &amp; information to read offline.</p>
                                                </div>
                                                <div class="project-file-cta-action">
                                                    <a href="{{ url('storage/' . ltrim($project->project_file_pdf, '/')) }}" class="commentssubmit commentssubmit_fw" download>Download PDF</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if($project->noc_planning_content || $project->noc_planning_image)
                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            <div class="project-noc-section">
                                                <div class="project-noc-text">
                                                    <h3>NOC &amp; Planned Approval</h3>
                                                    @if($project->noc_planning_content)
                                                    <div class="project-noc-body">
                                                        {!! $project->noc_planning_content !!}
                                                    </div>
                                                    @endif
                                                </div>
                                                @if($project->noc_planning_image)
                                                @php $nocImage = url('storage/' . ltrim($project->noc_planning_image, '/')); @endphp
                                                <div class="project-noc-image-wrap">
                                                    <div class="project-noc-image-inner">
                                                        <img src="{{ $nocImage }}" alt="NOC &amp; Planned Approval" loading="lazy">
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @php
                                        $td = is_array($project->title_descriptions ?? null) ? $project->title_descriptions : [];
                                        $tdTitle = $td['section_title'] ?? '';
                                        $tdDesc = $td['section_description'] ?? '';
                                        $tdItems = is_array($td['items'] ?? null) ? $td['items'] : [];
                                        $hasTd = ($tdTitle || $tdDesc || !empty($tdItems));
                                    @endphp
                                    @if($hasTd)
                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            @if($tdTitle)
                                            <h3 class="project-td-heading">{{ $tdTitle }}</h3>
                                            @endif
                                            @if($tdDesc)
                                            <p class="project-td-subtitle">{{ $tdDesc }}</p>
                                            @endif
                                            @if(!empty($tdItems))
                                            <div class="project-td-grid">
                                                @foreach($tdItems as $item)
                                                @php
                                                    $itTitle = is_array($item) ? ($item['title'] ?? '') : '';
                                                    $itDesc = is_array($item) ? ($item['description'] ?? '') : '';
                                                    if ($itTitle === '' && $itDesc === '') continue;
                                                @endphp
                                                <div class="project-td-card">
                                                    @if($itTitle !== '')
                                                    <h4>{{ $itTitle }}</h4>
                                                    @endif
                                                    @if($itDesc !== '')
                                                    <p>{{ $itDesc }}</p>
                                                    @endif
                                                </div>
                                                @endforeach
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($project->future_note_title) || !empty($project->future_note_content))
                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            @if(!empty($project->future_note_title))
                                            <h3 class="project-extra-heading">{{ $project->future_note_title }}</h3>
                                            @endif
                                            @if(!empty($project->future_note_content))
                                            <div class="project-extra-body">
                                                {!! nl2br(e($project->future_note_content)) !!}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($project->extra_section_title) || !empty($project->extra_section_content))
                                    <div class="boxed-content">
                                        <div class="boxed-content-item">
                                            @if(!empty($project->extra_section_title))
                                            <h3 class="project-extra-heading">{{ $project->extra_section_title }}</h3>
                                            @endif
                                            @if(!empty($project->extra_section_content))
                                            <div class="project-extra-body">
                                                {!! $project->extra_section_content !!}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif

                                    @if($project->latitude && $project->longitude)
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Project Location</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="map-container mapC_vis">
                                                <div id="projectMap" class="single-map-container fs-wrapper"
                                                    data-latitude="{{ $project->latitude }}"
                                                    data-longitude="{{ $project->longitude }}"
                                                    data-infotitle="{{ $project->title }}"
                                                    data-infotext="{{ $fullAddress }}"></div>
                                                <div class="scrollContorl"></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(count($carouselImages) > 1)
                                    <div id="project-gallery" class="boxed-content etihad-scroll-anchor">
                                        <div class="boxed-content-title">
                                            <h3>Project Gallery</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="gallery-items gisp grid-small-pad list-single-gallery three-coulms lightgallery">
                                                @foreach($carouselImages as $idx => $img)
                                                <div class="gallery-item{{ $idx === 1 ? ' gallery-item-second' : '' }}">
                                                    <div class="grid-item-holder hovzoom">
                                                        <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" loading="lazy">
                                                        <a href="{{ $img['url'] }}" class="gal-link popup-image"><i class="fa fa-search"></i></a>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($videoGalleryUrls))
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Video Gallery</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="property-video-gallery-wrap">
                                                @foreach($videoGalleryUrls as $embedUrl)
                                                <div class="property-video-embed">
                                                    <iframe src="{{ $embedUrl }}" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @if(!empty($uniqueFeatureItems))
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Unique Features</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="property-amenities-pills">
                                                @foreach($uniqueFeatureItems as $feat)
                                                @php
                                                    $ftitle = $feat['title'] ?? '';
                                                    $ficon = $feat['icon'] ?? '';
                                                @endphp
                                                @if($ftitle !== '' || $ficon !== '')
                                                <span class="property-amenity-pill">
                                                    @if($ficon)
                                                    <img src="{{ iconify_url($ficon, 22) }}" alt="" class="property-amenity-icon" loading="lazy" onerror="this.style.display='none'">
                                                    @endif
                                                    @if($ftitle !== '')
                                                    <span class="property-amenity-title">{{ $ftitle }}</span>
                                                    @endif
                                                </span>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @if(!empty($planItems))
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>Project Plans</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="accordion" id="project-plans-accordion">
                                                @foreach($planItems as $idx => $plan)
                                                @php
                                                    $pTitle = is_array($plan) ? ($plan['title'] ?? '') : (string) $plan;
                                                    $pImage = is_array($plan) ? ($plan['image'] ?? '') : '';
                                                @endphp
                                                @if($pTitle || $pImage)
                                                <a href="#" class="project-plans-toggle {{ $idx === 0 ? 'act-accordion' : '' }}" data-accordion="project-plans">
                                                    <span>{{ $pTitle ?: 'Plan '.($idx+1) }}</span>
                                                    <i class="fa-solid fa-caret-down"></i>
                                                </a>
                                                <div class="project-plans-inner {{ $idx === 0 ? 'visible' : '' }}">
                                                    @if($pImage)
                                                    <div class="project-plan-image">
                                                        <img src="{{ url('storage/' . ltrim($pImage, '/')) }}" alt="{{ $pTitle ?: 'Project plan' }}" loading="lazy">
                                                    </div>
                                                    @endif
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @php
                                        $pricePlanTitle = $project->price_plan_section_title ?? '';
                                        $pricePlanItems = is_array($project->price_plan_items ?? null) ? array_filter(array_map('trim', $project->price_plan_items)) : [];
                                        $hasPricePlan = $pricePlanTitle !== '' || !empty($pricePlanItems);
                                    @endphp
                                    @if($hasPricePlan)
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>{{ $pricePlanTitle ?: 'Price Plan' }}</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="property-amenities-pills">
                                                @foreach($pricePlanItems as $item)
                                                <span class="property-amenity-pill"><span class="property-amenity-title">{{ $item }}</span></span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    @php $faqList = is_array($project->faqs ?? null) ? $project->faqs : []; @endphp
                                    @if(!empty($faqList))
                                    <div class="boxed-content">
                                        <div class="boxed-content-title">
                                            <h3>FAQs</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="accordion" id="project-faqs-accordion">
                                                @foreach($faqList as $idx => $faq)
                                                @php
                                                    $q = is_array($faq) ? ($faq['question'] ?? '') : '';
                                                    $a = is_array($faq) ? ($faq['answer'] ?? '') : '';
                                                @endphp
                                                @if($q !== '' || $a !== '')
                                                <a href="#" class="project-faqs-toggle {{ $idx === 0 ? 'act-accordion' : '' }}" data-accordion="project-faqs">
                                                    <span>{{ $q ?: 'Question ' . ($idx + 1) }}</span>
                                                    <i class="fa-solid fa-caret-down"></i>
                                                </a>
                                                <div class="project-faqs-inner {{ $idx === 0 ? 'visible' : '' }}">
                                                    @if($a !== '')
                                                    <div class="project-faq-answer">{!! nl2br(e($a)) !!}</div>
                                                    @endif
                                                </div>
                                                @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endif

                                    <div class="boxed-content project-contact-form-section">
                                        <div class="boxed-content-title">
                                            <h3>Request a Showing</h3>
                                        </div>
                                        <div class="boxed-content-item">
                                            <div class="property-contacts-wrap">
                                                <div class="property-contacts-item sh-links project-contact-pill">
                                                    <div class="property-contacts_profile">
                                                        <span class="property-contacts_profile_link">
                                                            <span class="pcp-dev-name">Etihad Marketing</span>
                                                        </span>
                                                    </div>
                                                    @php
                                                        $cs = \App\Models\ContactSetting::instance();
                                                        $devPhoneRaw = $cs->phone ?: '';
                                                        $devPhone = $devPhoneRaw ? preg_replace('/\s+/', '', $devPhoneRaw) : '';
                                                        $devWhRaw = $cs->whatsapp ?: $devPhoneRaw;
                                                        $devWhatsapp = $devWhRaw ? preg_replace('/\D/', '', $devWhRaw) : '';
                                                    @endphp
                                                    @if($devPhone || $devWhatsapp)
                                                    <div class="property-contacts-links">
                                                        @if($devPhone)
                                                        <a href="tel:{{ $devPhone }}" class="tolt pcl_btn" data-microtip-position="left" data-tooltip="Call"><i class="fa-solid fa-phone"></i></a>
                                                        @endif
                                                        @if($devWhatsapp)
                                                        <a href="https://wa.me/{{ $devWhatsapp }}" target="_blank" rel="noopener" class="pcl_btn tolt" data-microtip-position="left" data-tooltip="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                                                        @endif
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="log-separator"><span>or</span></div>
                                                <p>Use the form below to request more information.</p>
                                            </div>
                                            <div id="project-request-wrap">
                                                <div class="custom-form property-request-form no-icons" id="project_cf">
                                                    <form method="post" action="{{ route('project.request-info') }}" name="contact-project-form" id="project-request-form">
                                                        @csrf
                                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
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
                                                        <div class="property-request-message etihad-is-hidden" id="project-request-message"></div>
                                                        <button type="button" class="commentssubmit commentssubmit_fw" id="project-request-btn">Send Request</button>
                                                    </form>
                                                </div>
                                                <div class="property-request-submitted etihad-is-hidden" id="project-request-submitted">
                                                    <p class="property-request-success-text">Your request has been sent successfully.</p>
                                                    <div class="property-request-summary" id="project-request-summary"></div>
                                                    <button type="button" class="commentssubmit commentssubmit_fw property-request-again-btn" id="project-request-again-btn">Request Again</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="limit-box"></div>
                    </div>

                    <div class="boxed-container">
                        <div class="boxed-content-title bcst_ca">
                            <h3>More Projects</h3>
                        </div>
                        <p class="text-muted"><a href="{{ url('/projects') }}">View all projects</a></p>
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

@if($project->latitude && $project->longitude)
@php $googleMapsKey = config('app.google_maps_api_key') ?: 'AIzaSyAYrLB-ltxWv32OFEF6c07B376JNrDyOIA'; @endphp
<script>
window.initProjectMap = function() {
    var el = document.getElementById('projectMap');
    if (!el || typeof google === 'undefined' || !google.maps) return;
    var lat = parseFloat(el.getAttribute('data-latitude')) || 0;
    var lng = parseFloat(el.getAttribute('data-longitude')) || 0;
    var title = el.getAttribute('data-infotitle') || '';
    var text = el.getAttribute('data-infotext') || '';
    var center = { lat: lat, lng: lng };
    var map = new google.maps.Map(el, { zoom: 14, center: center, scrollwheel: false, zoomControl: true, fullscreenControl: true, mapTypeControl: false, streetViewControl: true });
    var marker = new google.maps.Marker({ position: center, map: map });
    if (title || text) {
        var info = new google.maps.InfoWindow({ content: '<div><h3>' + (title || '') + '</h3><p>' + (text || '') + '</p></div>' });
        marker.addListener('click', function() { info.open(map, marker); });
    }
    var scrollBtn = document.querySelector('.map-container .scrollContorl');
    if (scrollBtn) scrollBtn.addEventListener('click', function(e) { e.preventDefault(); this.classList.toggle('enabledsroll'); map.setOptions({ scrollwheel: this.classList.contains('enabledsroll') }); });
};
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initProjectMap" async defer></script>
@endif

@if($featuredVideoId && $featuredVideoEmbedUrl)
@push('scripts')
<script>
(function() {
    var modal = document.getElementById('project-video-modal');
    var openBtn = document.getElementById('project-featured-video-btn');
    var closeBtn = document.getElementById('project-video-close');
    var iframe = document.getElementById('project-video-iframe');
    if (modal && openBtn) {
        function openModal() {
            var url = openBtn.getAttribute('data-embed-url');
            if (url && iframe) iframe.src = url;
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('project-video-modal-open');
            document.documentElement.classList.add('project-video-modal-open');
        }
        function closeModal() {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            if (iframe) iframe.src = '';
            document.body.classList.remove('project-video-modal-open');
            document.documentElement.classList.remove('project-video-modal-open');
        }
        openBtn.addEventListener('click', function(e) { e.preventDefault(); openModal(); });
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', function(e) { if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal(); });
    }
})();
</script>
@endpush
@endif

@push('scripts')
<script>
(function() {
    // Project Plans accordion – scoped to #project-plans-accordion only
    var plansAcc = document.getElementById('project-plans-accordion');
    if (plansAcc) {
        var planToggles = plansAcc.querySelectorAll('.project-plans-toggle');
        var planInners = plansAcc.querySelectorAll('.project-plans-inner');
        planToggles.forEach(function(tog, i) {
            tog.addEventListener('click', function(e) {
                e.preventDefault();
                var inner = planInners[i];
                var isOpen = inner && inner.classList.contains('visible');
                planToggles.forEach(function(t) { t.classList.remove('act-accordion'); });
                planInners.forEach(function(inr) { inr.classList.remove('visible'); inr.style.display = 'none'; });
                if (!isOpen && inner) {
                    this.classList.add('act-accordion');
                    inner.classList.add('visible');
                    inner.style.display = 'block';
                }
            });
        });
    }
    // Project FAQs accordion – scoped to #project-faqs-accordion only
    var faqsAcc = document.getElementById('project-faqs-accordion');
    if (faqsAcc) {
        var faqToggles = faqsAcc.querySelectorAll('.project-faqs-toggle');
        var faqInners = faqsAcc.querySelectorAll('.project-faqs-inner');
        faqToggles.forEach(function(tog, i) {
            tog.addEventListener('click', function(e) {
                e.preventDefault();
                var inner = faqInners[i];
                var isOpen = inner && inner.classList.contains('visible');
                faqToggles.forEach(function(t) { t.classList.remove('act-accordion'); });
                faqInners.forEach(function(inr) { inr.classList.remove('visible'); inr.style.display = 'none'; });
                if (!isOpen && inner) {
                    this.classList.add('act-accordion');
                    inner.classList.add('visible');
                    inner.style.display = 'block';
                }
            });
        });
    }
})();
</script>
<script>
(function() {
    function initProjectRequestForm() {
        var formEl = document.getElementById('project-request-form');
        var formWrap = document.getElementById('project_cf');
        var submittedEl = document.getElementById('project-request-submitted');
        var summaryEl = document.getElementById('project-request-summary');
        var messageEl = document.getElementById('project-request-message');
        var btnEl = document.getElementById('project-request-btn');
        var againBtn = document.getElementById('project-request-again-btn');
        if (!formEl || !submittedEl) return;

        var projectId = {{ $project->id }};
        var cookieName = 'etihad_project_requests';

        function getStoredRequests() {
            var cookies = document.cookie ? document.cookie.split('; ') : [];
            for (var i = 0; i < cookies.length; i++) {
                var parts = cookies[i].split('=');
                var name = parts.shift();
                var value = parts.join('=');
                if (name === cookieName) {
                    try { return JSON.parse(decodeURIComponent(value)) || {}; }
                    catch(e) { return {}; }
                }
            }
            return {};
        }
        function setStoredRequest(pid, data) {
            var all = getStoredRequests();
            all[pid] = data;
            var encoded = encodeURIComponent(JSON.stringify(all));
            var expires = new Date();
            expires.setFullYear(expires.getFullYear() + 1);
            document.cookie = cookieName + '=' + encoded + '; path=/; expires=' + expires.toUTCString();
        }
        function escapeHtml(s) {
            var d = document.createElement('div');
            d.textContent = String(s || '');
            return d.innerHTML;
        }
        function showSubmitted(data) {
            if (formWrap) formWrap.classList.add('etihad-is-hidden');
            if (submittedEl) submittedEl.classList.remove('etihad-is-hidden');
            if (messageEl) { messageEl.classList.add('etihad-is-hidden'); messageEl.textContent = ''; messageEl.className = 'property-request-message etihad-is-hidden'; }
            if (summaryEl) {
                var html = '';
                if (data.name) html += '<p><strong>Name:</strong> ' + escapeHtml(data.name) + '</p>';
                if (data.phone) html += '<p><strong>Phone:</strong> ' + escapeHtml(data.phone) + '</p>';
                if (data.email) html += '<p><strong>Email:</strong> ' + escapeHtml(data.email) + '</p>';
                if (data.message) html += '<p><strong>Message:</strong> ' + escapeHtml(data.message) + '</p>';
                summaryEl.innerHTML = html;
            }
        }
        function showForm() {
            if (submittedEl) submittedEl.classList.add('etihad-is-hidden');
            if (formWrap) formWrap.classList.remove('etihad-is-hidden');
            if (messageEl) { messageEl.classList.add('etihad-is-hidden'); messageEl.textContent = ''; messageEl.className = 'property-request-message etihad-is-hidden'; }
        }

        var stored = getStoredRequests()[projectId];
        if (stored && (stored.name || stored.message)) {
            showSubmitted(stored);
        }

        function submitViaAjax(e) {
            if (e) { e.preventDefault(); e.stopPropagation(); }
            if (!btnEl) return;
            var fd = new FormData(formEl);
            btnEl.classList.add('is-loading');
            btnEl.disabled = true;
            if (messageEl) { messageEl.classList.add('etihad-is-hidden'); messageEl.className = 'property-request-message etihad-is-hidden'; messageEl.textContent = ''; }

            fetch(formEl.action, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(function(r) {
                var ct = r.headers.get('content-type');
                if (ct && ct.indexOf('application/json') !== -1) {
                    return r.json().then(function(j) { return { ok: r.ok, json: j }; });
                }
                return { ok: false, json: { message: 'Invalid response. Please try again.' } };
            }).then(function(result) {
                btnEl.classList.remove('is-loading');
                btnEl.disabled = false;
                if (result.ok && result.json && result.json.success) {
                    var data = {
                        name: fd.get('name') || '',
                        phone: fd.get('phone') || '',
                        email: fd.get('email') || '',
                        message: fd.get('message') || ''
                    };
                    setStoredRequest(projectId, data);
                    showSubmitted(data);
                } else {
                    var msg = (result.json && result.json.message) || 'Something went wrong. Please try again.';
                    if (messageEl) {
                        messageEl.textContent = msg;
                        messageEl.className = 'property-request-message error';
                        messageEl.classList.remove('etihad-is-hidden');
                    }
                }
            }).catch(function() {
                btnEl.classList.remove('is-loading');
                btnEl.disabled = false;
                if (messageEl) {
                    messageEl.textContent = 'Something went wrong. Please try again.';
                    messageEl.className = 'property-request-message error';
                    messageEl.classList.remove('etihad-is-hidden');
                }
            });
            return false;
        }

        // Safety: prevent normal submit, always AJAX
        formEl.addEventListener('submit', submitViaAjax);
        if (btnEl) btnEl.addEventListener('click', submitViaAjax);

        if (againBtn) {
            againBtn.addEventListener('click', function() { showForm(); });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProjectRequestForm);
    } else {
        initProjectRequestForm();
    }
})();
</script>
@endpush
@endsection
