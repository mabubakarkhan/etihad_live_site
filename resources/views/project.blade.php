@extends('layouts.front')

@php
    $metaTitle = is_string($project->meta_title) && $project->meta_title !== '' ? $project->meta_title : ($project->title . ' - ' . config('app.name'));

    $rawMetaDesc = null;
    if (is_string($project->meta_description) && $project->meta_description !== '') {
        $rawMetaDesc = $project->meta_description;
    } elseif (is_string($project->description) && $project->description !== '') {
        $rawMetaDesc = $project->description;
    }
    $metaDesc = $rawMetaDesc !== null ? \Illuminate\Support\Str::limit(strip_tags($rawMetaDesc), 160) : '';

    $featuredUrl = $project->featured_image
        ? url('storage/' . ltrim($project->featured_image, '/'))
        : ($project->homepage_listing_image
            ? url('storage/' . ltrim($project->homepage_listing_image, '/'))
            : asset('theme/images/all/1.jpg'));

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
        ? (is_numeric($project->price)
            ? config('app.currency', 'PKR') . ' ' . number_format((float) $project->price, 0)
            : (string) $project->price)
        : '';
    $priceString = trim((string) ($project->price_string ?? ''));
    if ($priceString === '') {
        $priceString = $price;
    }

    $typesText = $project->projectTypes->isNotEmpty() ? $project->projectTypes->pluck('name')->join(', ') : '';
    $shortDesc = \Illuminate\Support\Str::limit(trim(strip_tags((string) ($project->description ?? ''))), 220);

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

    $amenityTitles = is_array($project->amenity_titles ?? null) ? $project->amenity_titles : [];
    $amenityIcons = is_array($project->amenity_icons ?? null) ? $project->amenity_icons : [];
    $amenities = [];
    foreach ($amenityTitles as $idx => $title) {
        $title = trim((string) $title);
        $icon = trim((string) ($amenityIcons[$idx] ?? ''));
        if ($title !== '') {
            $amenities[] = ['title' => $title, 'icon' => $icon];
        }
    }

    $planItems = is_array($project->plans ?? null) ? $project->plans : [];

    $videoGalleryUrls = [];
    $videosValue = $project->videos ?? null;
    $videosRaw = [];
    if (is_array($videosValue)) {
        $videosRaw = $videosValue;
    } elseif (is_string($videosValue) && trim($videosValue) !== '') {
        $videosString = trim($videosValue);
        $decodedVideos = json_decode($videosString, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedVideos)) {
            $videosRaw = $decodedVideos;
        } else {
            $videosRaw = [$videosString];
        }
    }
    foreach ($videosRaw as $v) {
        $candidates = [];
        if (is_array($v)) {
            foreach (['url', 'link', 'video', 'value', 'iframe', 'embed'] as $k) {
                if (!empty($v[$k]) && is_string($v[$k])) {
                    $candidates[] = trim($v[$k]);
                }
            }
        } else {
            $raw = trim((string) $v);
            if ($raw !== '') {
                // Support comma-separated values saved in one row.
                $candidates = array_map('trim', explode(',', $raw));
            }
        }
        foreach ($candidates as $raw) {
            if ($raw === '') continue;
            if (strpos($raw, '<') !== false && preg_match('/src=["\']([^"\']+)["\']/', $raw, $m)) {
                $videoGalleryUrls[] = $m[1];
            } elseif (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $raw, $m)) {
                $videoGalleryUrls[] = 'https://www.youtube.com/embed/' . $m[1];
            } else {
                $idCandidate = trim(explode('?', $raw)[0]);
                if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', $idCandidate)) {
                    $videoGalleryUrls[] = 'https://www.youtube.com/embed/' . $idCandidate;
                }
            }
        }
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

    $vrTourUrl = is_string($project->vr_tour_url ?? null) ? trim($project->vr_tour_url) : '';
    if ($vrTourUrl !== '' && !preg_match('/^https?:\/\//i', $vrTourUrl)) {
        $vrTourUrl = 'https://' . $vrTourUrl;
    }
    $vrTourPageUrl = $vrTourUrl !== '' ? route('project.vr-tour', ['project' => $project->id]) : '';

    $aboutDevelopersText = is_string($project->about_developers ?? null) ? trim($project->about_developers) : '';
    $developerLogoUrl = (!empty($project->logo) && is_string($project->logo)) ? url('storage/' . ltrim($project->logo, '/')) : null;

    $cs = \App\Models\ContactSetting::instance();
    $projectPhone = $cs->phone ?: '';
    $projectWhatsapp = $cs->whatsapp ?: $projectPhone;
    $projectEmail = $cs->email ?: '';
    $projectPhoneClean = $projectPhone ? preg_replace('/\s+/', '', $projectPhone) : '';
    $projectWhatsappClean = $projectWhatsapp ? preg_replace('/\D/', '', $projectWhatsapp) : '';

    $pricePlanTitle = $project->price_plan_section_title ?? '';
    $pricePlanItems = is_array($project->price_plan_items ?? null) ? array_filter(array_map('trim', $project->price_plan_items)) : [];
    $faqList = is_array($project->faqs ?? null) ? $project->faqs : [];

    $similarProjects = \App\Models\Project::query()
        ->active()
        ->where('id', '!=', $project->id)
        ->latest('id')
        ->limit(4)
        ->get();
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
<link rel="stylesheet" href="{{ asset('theme/css/project-redesign.css') }}">
@endpush

@section('content')
<div id="main" class="project-redesign-page" data-project-page>
    @include('partials.header')
    @if(count($carouselImages) > 1)
    <div class="project-rd-gallery-modal" id="project-rd-gallery-modal" aria-hidden="true">
        <div class="project-rd-gallery-modal-inner">
            <button type="button" class="project-rd-gallery-close" id="project-rd-gallery-close" aria-label="Close gallery">&times;</button>
            <button type="button" class="project-rd-gallery-nav project-rd-gallery-nav-prev" id="project-rd-gallery-prev" aria-label="Previous image"><i class="fa-solid fa-angle-left"></i></button>
            <img id="project-rd-gallery-modal-image" src="" alt="">
            <button type="button" class="project-rd-gallery-nav project-rd-gallery-nav-next" id="project-rd-gallery-next" aria-label="Next image"><i class="fa-solid fa-angle-right"></i></button>
            <div class="project-rd-gallery-modal-thumbs" id="project-rd-gallery-modal-thumbs"></div>
        </div>
    </div>
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

            <section class="project-rd-section">
                <div class="container">
                    <div class="project-rd-grid">
                        <aside class="project-rd-leftnav project-rd-reveal">
                            <a href="#project-rd-overview">Overview</a>
                            <a href="#project-rd-gallery-section">Gallery</a>
                            <a href="#project-rd-main-video">Main Video</a>
                            <a href="#project-rd-videos">Videos</a>
                            <a href="#project-rd-pdf">PDF</a>
                            <a href="#project-rd-features">Features</a>
                            <a href="#project-rd-amenities">Amenities</a>
                            <a href="#project-rd-plans">Floors</a>
                            <a href="#project-rd-price-plan">Pricing</a>
                            <a href="#project-rd-noc">NOC</a>
                            <a href="#project-rd-title-descriptions">Insights</a>
                            <a href="#project-rd-future-note">Future Note</a>
                            <a href="#project-rd-extra-section">Xtra Section</a>
                            <a href="#project-rd-location">Location</a>
                            <a href="#project-rd-faqs">FAQs</a>
                            <a href="#project-rd-inquiry">Contact</a>
                        </aside>
                        <div class="project-rd-main">
                            <article class="project-rd-card project-rd-reveal">
                                <div class="project-rd-hero-shell">
                                    <div class="project-rd-hero-topbar">
                                        @if($typesText)
                                        <span class="project-rd-info-chip">{{ strtoupper($typesText) }}</span>
                                        @endif
                                        <div class="project-rd-hero-top-actions">
                                            <button type="button" class="project-rd-top-btn"><i class="fa-regular fa-heart"></i> Save</button>
                                            <button type="button" class="project-rd-top-btn"><i class="fa-solid fa-share-nodes"></i> Share</button>
                                        </div>
                                    </div>
                                    <h1 class="project-rd-hero-title">{{ $project->title }}</h1>
                                    @if($fullAddress)
                                    <div class="project-rd-hero-subline">
                                        <span><i class="fa-solid fa-location-dot"></i> {{ $fullAddress }}</span>
                                        <a href="#project-rd-location">View on Map <i class="fa-solid fa-angle-right"></i></a>
                                    </div>
                                    @endif
                                    <div class="project-rd-hero-stats">
                                        <div class="project-rd-hero-stat project-rd-hero-stat-price">
                                            <i class="fa-light fa-money-bill-wave"></i>
                                            <div>
                                                <strong>{{ $priceString !== '' ? $priceString : '—' }}</strong>
                                                <span>Price</span>
                                            </div>
                                        </div>
                                        @if($project->city)
                                        <div class="project-rd-hero-stat">
                                            <i class="fa-light fa-city"></i>
                                            <div><strong>{{ $project->city }}</strong><span>City</span></div>
                                        </div>
                                        @endif
                                        @if($typesText)
                                        <div class="project-rd-hero-stat">
                                            <i class="fa-light fa-layer-group"></i>
                                            <div><strong>{{ $typesText }}</strong><span>Property Type</span></div>
                                        </div>
                                        @endif
                                        @if($project->short_address)
                                        <div class="project-rd-hero-stat">
                                            <i class="fa-light fa-location-dot"></i>
                                            <div><strong>{{ $project->short_address }}</strong><span>Location</span></div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="project-rd-hero" id="project-rd-gallery">
                                    <img id="project-rd-main-image" src="{{ $carouselImages[0]['url'] ?? $featuredUrl }}" alt="{{ $project->title }}" loading="lazy">
                                    <div class="project-rd-overlay"></div>
                                    <button type="button" class="project-rd-slide project-rd-slide-prev" aria-label="Previous image"><i class="fa-solid fa-angle-left"></i></button>
                                    <button type="button" class="project-rd-slide project-rd-slide-next" aria-label="Next image"><i class="fa-solid fa-angle-right"></i></button>
                                    <div class="project-rd-hero-content">
                                        <div class="project-rd-actions">
                                            @if($projectPhoneClean)
                                            <a href="tel:{{ $projectPhoneClean }}" class="project-rd-btn project-rd-btn-dark"><i class="fa-solid fa-phone"></i> Call Now</a>
                                            @endif
                                            @if($projectWhatsappClean)
                                            <a href="https://wa.me/{{ $projectWhatsappClean }}" target="_blank" rel="noopener" class="project-rd-btn project-rd-btn-green"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                                            @endif
                                            @if($vrTourPageUrl !== '')
                                            <a href="{{ $vrTourPageUrl }}" target="_blank" rel="noopener" class="project-rd-btn project-rd-btn-dark"><i class="fa-solid fa-vr-cardboard"></i> VR Tour</a>
                                            @endif
                                            <a href="#project-rd-inquiry" class="project-rd-btn project-rd-btn-orange"><i class="fa-regular fa-paper-plane"></i> Send Inquiry</a>
                                        </div>
                                    </div>
                                </div>

                                @if(count($carouselImages) > 1)
                                <div class="project-rd-thumbs">
                                    @foreach($carouselImages as $idx => $img)
                                    <button type="button" class="project-rd-thumb {{ $idx === 0 ? 'is-active' : '' }}" data-index="{{ $idx }}" data-image="{{ $img['url'] }}" data-alt="{{ $img['alt'] }}">
                                        <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" loading="lazy">
                                    </button>
                                    @endforeach
                                    <button type="button" class="project-rd-view-all" id="project-rd-view-all-btn"><i class="fa-solid fa-table-cells-large"></i><span>View All Photos</span></button>
                                </div>
                                @endif

                                <div class="project-rd-facts">
                                    @if($typesText)
                                    <div class="project-rd-fact"><span>Type</span><strong>{{ $typesText }}</strong></div>
                                    @endif
                                    @if($price)
                                    <div class="project-rd-fact"><span>Price</span><strong>{{ $price }}</strong></div>
                                    @endif
                                    @if($project->city)
                                    <div class="project-rd-fact"><span>City</span><strong>{{ $project->city }}</strong></div>
                                    @endif
                                    @if($project->short_address)
                                    <div class="project-rd-fact"><span>Location</span><strong>{{ $project->short_address }}</strong></div>
                                    @endif
                                </div>
                            </article>

                            <section class="project-rd-card project-rd-reveal" id="project-rd-overview">
                                <h3>Overview</h3>
                                @if($project->description)
                                <div class="project-rd-richtext">{!! $project->description !!}</div>
                                @elseif($shortDesc !== '')
                                <p>{{ $shortDesc }}</p>
                                @endif
                            </section>

                            @if(count($carouselImages) > 1)
                            <section class="project-rd-card project-rd-reveal" id="project-rd-gallery-section">
                                <h3>Gallery</h3>
                                <div class="project-rd-gallery-grid">
                                    @foreach($carouselImages as $idx => $img)
                                    <a href="#" class="project-rd-gallery-item project-rd-open-gallery" data-index="{{ $idx }}">
                                        <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" loading="lazy">
                                    </a>
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @if($featuredVideoEmbedUrl)
                            <section class="project-rd-card project-rd-reveal" id="project-rd-main-video">
                                <h3>{{ $project->featured_video_title ?: 'Project Video Presentation' }}</h3>
                                <div class="project-rd-video-legacy-wrap banner-widget-wrap">
                                    <div class="bg-wrap bg-parallax-wrap-gradien fs-wrapper">
                                        <div class="bg" data-bg="https://img.youtube.com/vi/{{ $featuredVideoId }}/maxresdefault.jpg"></div>
                                    </div>
                                    <div class="banner-widget_content project-rd-video-legacy-content">
                                        <button type="button" class="video-box-btn" id="project-featured-video-btn" data-embed-url="{{ $featuredVideoEmbedUrl }}" aria-label="Play video"><i class="fas fa-play"></i></button>
                                        <div class="project-rd-video-legacy-caption">
                                            <h4>{{ strtoupper($project->featured_video_title ?: 'VIDEO TITLE') }}</h4>
                                            @if($project->featured_video_description)
                                            <div>{!! $project->featured_video_description !!}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </section>
                            @endif

                            @if(!empty($videoGalleryUrls))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-videos">
                                <h3>Video Gallery</h3>
                                <div class="project-rd-video-grid">
                                    @foreach($videoGalleryUrls as $idx => $embedUrl)
                                    @php
                                        $videoThumb = '';
                                        $videoId = '';
                                        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $embedUrl, $mThumb)) {
                                            $videoId = $mThumb[1];
                                            $videoThumb = 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
                                        }
                                    @endphp
                                    <button type="button" class="project-rd-video-card project-rd-video-open" data-embed-url="{{ $embedUrl }}" aria-label="Play video {{ $idx + 1 }}">
                                        <span class="project-rd-video-cover">
                                            @if($videoThumb !== '')
                                            <img src="{{ $videoThumb }}" alt="Video {{ $idx + 1 }}" loading="lazy">
                                            @else
                                            <span class="project-rd-video-fallback"><i class="fa-solid fa-film"></i></span>
                                            @endif
                                            <span class="project-rd-video-overlay"></span>
                                            <span class="project-rd-video-play"><i class="fas fa-play"></i></span>
                                        </span>
                                        <span class="project-rd-video-title">Project Video {{ $idx + 1 }}</span>
                                    </button>
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @if($project->project_file_pdf)
                            <section class="project-rd-card project-rd-reveal" id="project-rd-pdf">
                                <h3>Project PDF</h3>
                                <div class="project-rd-pdf-row">
                                    <p>Download the official project brochure for complete details.</p>
                                    <a href="{{ url('storage/' . ltrim($project->project_file_pdf, '/')) }}" class="project-rd-btn project-rd-btn-orange" download><i class="fa-light fa-file-pdf"></i> Download PDF</a>
                                </div>
                            </section>
                            @endif

                            @if(!empty($uniqueFeatureItems))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-features">
                                <h3>Key Features</h3>
                                <div class="project-rd-chip-list">
                                    @foreach($uniqueFeatureItems as $feat)
                                    @if(($feat['title'] ?? '') !== '')
                                    <span class="project-rd-chip">{{ $feat['title'] }}</span>
                                    @endif
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @if(!empty($amenities))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-amenities">
                                <h3>Amenities</h3>
                                <div class="project-rd-chip-list">
                                    @foreach($amenities as $item)
                                    <span class="project-rd-chip">{{ $item['title'] }}</span>
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @if($project->latitude && $project->longitude)
                            <section class="project-rd-card project-rd-reveal" id="project-rd-location">
                                <h3>Location</h3>
                                <div id="project-rd-map" class="project-rd-map" data-latitude="{{ $project->latitude }}" data-longitude="{{ $project->longitude }}" data-infotitle="{{ $project->title }}" data-infotext="{{ $fullAddress }}"></div>
                            </section>
                            @endif

                            @if(!empty($planItems))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-plans">
                                <h3>Floor Plans</h3>
                                <div class="accordion project-rd-accordion" id="project-plans-accordion">
                                    @foreach($planItems as $plan)
                                    @php
                                        $pTitle = is_array($plan) ? ($plan['title'] ?? '') : (string) $plan;
                                        $pImage = is_array($plan) ? ($plan['image'] ?? '') : '';
                                    @endphp
                                    @if($pImage)
                                    <a href="#" class="project-rd-acc-toggle {{ $loop->first ? 'act-accordion' : '' }}" data-accordion="project-plans">
                                        <span>{{ $pTitle ?: 'Plan ' . $loop->iteration }}</span>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </a>
                                    <div class="project-rd-acc-inner {{ $loop->first ? 'visible' : '' }}">
                                        <div class="project-rd-plan-item">
                                            <img src="{{ url('storage/' . ltrim($pImage, '/')) }}" alt="{{ $pTitle ?: 'Floor plan' }}" loading="lazy">
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @if($pricePlanTitle !== '' || !empty($pricePlanItems))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-price-plan">
                                <h3>{{ $pricePlanTitle !== '' ? $pricePlanTitle : 'Pricing Plan' }}</h3>
                                <div class="project-rd-chip-list">
                                    @foreach($pricePlanItems as $item)
                                    <span class="project-rd-chip">{{ $item }}</span>
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @if($project->noc_planning_content || $project->noc_planning_image)
                            <section class="project-rd-card project-rd-reveal" id="project-rd-noc">
                                <h3>NOC &amp; Planned Approval</h3>
                                <div class="project-rd-noc">
                                    <div class="project-rd-noc-text">
                                        {!! $project->noc_planning_content !!}
                                    </div>
                                    @if($project->noc_planning_image)
                                    <div class="project-rd-noc-image">
                                        <img src="{{ url('storage/' . ltrim($project->noc_planning_image, '/')) }}" alt="NOC & Planned Approval" loading="lazy">
                                    </div>
                                    @endif
                                </div>
                            </section>
                            @endif

                            @php
                                $td = is_array($project->title_descriptions ?? null) ? $project->title_descriptions : [];
                                $tdTitle = $td['section_title'] ?? '';
                                $tdDesc = $td['section_description'] ?? '';
                                $tdItems = is_array($td['items'] ?? null) ? $td['items'] : [];
                            @endphp
                            @if($tdTitle || $tdDesc || !empty($tdItems))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-title-descriptions">
                                @if($tdTitle)<h3>{{ $tdTitle }}</h3>@endif
                                @if($tdDesc)<p class="project-rd-td-sub">{{ $tdDesc }}</p>@endif
                                @if(!empty($tdItems))
                                <div class="project-rd-td-grid">
                                    @foreach($tdItems as $item)
                                    @php
                                        $itTitle = is_array($item) ? ($item['title'] ?? '') : '';
                                        $itDesc = is_array($item) ? ($item['description'] ?? '') : '';
                                    @endphp
                                    @if($itTitle !== '' || $itDesc !== '')
                                    <article class="project-rd-td-card">
                                        @if($itTitle)<h4>{{ $itTitle }}</h4>@endif
                                        @if($itDesc)<p>{{ $itDesc }}</p>@endif
                                    </article>
                                    @endif
                                    @endforeach
                                </div>
                                @endif
                            </section>
                            @endif

                            @if(!empty($project->future_note_title) || !empty($project->future_note_content))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-future-note">
                                <h3>{{ $project->future_note_title ?: 'Future Note' }}</h3>
                                <div class="project-rd-richtext">{!! nl2br(e($project->future_note_content)) !!}</div>
                            </section>
                            @endif

                            @if(!empty($project->extra_section_title) || !empty($project->extra_section_content))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-extra-section">
                                <h3>{{ $project->extra_section_title ?: 'Xtra Section' }}</h3>
                                <div class="project-rd-richtext">{!! $project->extra_section_content !!}</div>
                            </section>
                            @endif

                            @if($aboutDevelopersText !== '' || $developerLogoUrl)
                            <section class="project-rd-card project-rd-reveal" id="project-rd-developer">
                                <h3>About Developer</h3>
                                <div class="project-rd-dev-wrap">
                                    @if($developerLogoUrl)
                                    <img src="{{ $developerLogoUrl }}" alt="Developer logo" loading="lazy">
                                    @endif
                                    @if($aboutDevelopersText !== '')
                                    <div class="project-rd-richtext">{!! $aboutDevelopersText !!}</div>
                                    @endif
                                </div>
                            </section>
                            @endif

                            @if(!empty($faqList))
                            <section class="project-rd-card project-rd-reveal" id="project-rd-faqs">
                                <h3>FAQs</h3>
                                <div class="accordion project-rd-accordion" id="project-faqs-accordion">
                                    @foreach($faqList as $faq)
                                    @php
                                        $q = is_array($faq) ? trim((string) ($faq['question'] ?? '')) : '';
                                        $a = is_array($faq) ? trim((string) ($faq['answer'] ?? '')) : '';
                                    @endphp
                                    @if($q !== '' || $a !== '')
                                    <a href="#" class="project-rd-acc-toggle {{ $loop->first ? 'act-accordion' : '' }}" data-accordion="project-faqs">
                                        <span>{{ $q !== '' ? $q : 'Question ' . $loop->iteration }}</span>
                                        <i class="fa-solid fa-caret-down"></i>
                                    </a>
                                    <div class="project-rd-acc-inner {{ $loop->first ? 'visible' : '' }}">
                                        @if($a !== '')
                                        <div class="project-rd-faq-answer">{!! nl2br(e($a)) !!}</div>
                                        @endif
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </section>
                            @endif

                            @if($similarProjects->isNotEmpty())
                            <section class="project-rd-card project-rd-reveal" id="project-rd-similar">
                                <div class="project-rd-header-row">
                                    <h3>Similar Projects</h3>
                                    <a href="{{ url('/projects') }}">View All</a>
                                </div>
                                <div class="project-rd-similar-grid">
                                    @foreach($similarProjects as $sp)
                                    @php
                                        $spImage = $sp->featured_image
                                            ? url('storage/' . ltrim($sp->featured_image, '/'))
                                            : ($sp->homepage_listing_image
                                                ? url('storage/' . ltrim($sp->homepage_listing_image, '/'))
                                                : asset('theme/images/all/1.jpg'));
                                        $spPrice = $sp->price !== null && $sp->price !== ''
                                            ? (is_numeric($sp->price) ? config('app.currency', 'PKR') . ' ' . number_format((float) $sp->price, 0) : (string) $sp->price)
                                            : '';
                                    @endphp
                                    <a href="{{ url('/project/' . $sp->slug) }}" class="project-rd-similar-card">
                                        <img src="{{ $spImage }}" alt="{{ $sp->title }}" loading="lazy">
                                        <h4>{{ $sp->title }}</h4>
                                        @if($sp->short_address)<p>{{ $sp->short_address }}</p>@endif
                                        @if($spPrice)<strong>{{ $spPrice }}</strong>@endif
                                    </a>
                                    @endforeach
                                </div>
                            </section>
                            @endif
                        </div>

                        <aside class="project-rd-side" id="project-rd-inquiry">
                            <div class="project-rd-side-card project-rd-reveal">
                                <h3>Get in Touch</h3>
                                <p>Have questions or want to book a visit? We are here to help.</p>
                                <div class="project-rd-agent">Etihad Marketing</div>
                                <div class="project-rd-side-actions">
                                    @if($projectPhoneClean)
                                    <a href="tel:{{ $projectPhoneClean }}" class="project-rd-btn project-rd-btn-dark"><i class="fa-solid fa-phone"></i> Call Now</a>
                                    @endif
                                    @if($projectWhatsappClean)
                                    <a href="https://wa.me/{{ $projectWhatsappClean }}" target="_blank" rel="noopener" class="project-rd-btn project-rd-btn-green"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                                    @endif
                                    @if($vrTourPageUrl !== '')
                                    <a href="{{ $vrTourPageUrl }}" target="_blank" rel="noopener" class="project-rd-btn project-rd-btn-dark"><i class="fa-solid fa-vr-cardboard"></i> VR Tour</a>
                                    @endif
                                </div>

                                <h4>Send Inquiry</h4>
                                <form method="post" action="{{ route('project.request-info') }}" id="project-rd-form">
                                    @csrf
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                    <input type="text" name="name" placeholder="Your Name" required>
                                    <input type="text" name="phone" placeholder="Phone Number">
                                    <input type="email" name="email" placeholder="Email Address">
                                    <textarea name="message" rows="4" placeholder="Your Message"></textarea>
                                    <div class="project-rd-form-msg" id="project-rd-form-msg"></div>
                                    <button type="button" id="project-rd-submit" class="project-rd-btn project-rd-btn-orange">Submit Inquiry</button>
                                </form>

                                <div class="project-rd-share">
                                    <span>Share Project</span>
                                    <div>
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"><i class="fa-brands fa-facebook-f"></i></a>
                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener"><i class="fa-brands fa-linkedin-in"></i></a>
                                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($project->title) }}" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </section>

            <div class="project-rd-bottom-bar">
                @if($projectPhoneClean)
                <a href="tel:{{ $projectPhoneClean }}"><i class="fa-solid fa-phone"></i> Call Now</a>
                @endif
                @if($projectWhatsappClean)
                <a href="https://wa.me/{{ $projectWhatsappClean }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>
                @endif
                @if($vrTourPageUrl !== '')
                <a href="{{ $vrTourPageUrl }}" target="_blank" rel="noopener"><i class="fa-solid fa-vr-cardboard"></i> VR Tour</a>
                @endif
                <a href="#project-rd-inquiry"><i class="fa-regular fa-paper-plane"></i> Send Inquiry</a>
            </div>

        </div>

        @include('partials.footer')
    </div>

    @include('partials.theme-panels')
</div>

@if($project->latitude && $project->longitude)
@php $googleMapsKey = config('app.google_maps_api_key') ?: 'AIzaSyAYrLB-ltxWv32OFEF6c07B376JNrDyOIA'; @endphp
<script>
window.initProjectRedesignMap = window.initProjectRedesignMap || function () {
    if (typeof window.projectRedesignMapInit === 'function') {
        window.projectRedesignMapInit();
    }
};
</script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initProjectRedesignMap" async defer></script>
@endif

@push('scripts')
<script src="{{ asset('theme/js/project-redesign.js') }}"></script>
@endpush
@endsection
