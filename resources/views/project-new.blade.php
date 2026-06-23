@extends('layouts.front')

@php
    $featuredUrl = $project->featured_image
        ? url('storage/' . ltrim($project->featured_image, '/'))
        : ($project->homepage_listing_image
            ? url('storage/' . ltrim($project->homepage_listing_image, '/'))
            : asset('theme/images/all/1.jpg'));

    $typesFull = $project->projectTypes->isNotEmpty() ? trim((string) $project->projectTypes->pluck('name')->join(', ')) : '';
    $typesText = $typesFull;
    $shortDesc = \Illuminate\Support\Str::limit(trim(strip_tags((string) ($project->description ?? ''))), 145, '...');
    $priceText = \Illuminate\Support\Str::limit(trim((string) ($project->price ?? '')), 28, '...');
    $cs = \App\Models\ContactSetting::instance();
    $projectPhoneClean = preg_replace('/\s+/', '', (string) ($cs->phone ?? ''));
    $projectPhone = trim((string) ($cs->phone ?? ''));
    $projectEmail = trim((string) ($cs->email ?? ''));
    $visitAddress = trim((string) ($project->full_address ?? $project->short_address ?? $cs->address ?? ''));
    $inquiryPropertyTypes = \App\Models\ProjectType::query()->orderBy('name')->pluck('name')
        ->map(fn ($n) => trim((string) $n))->filter()->unique()->values()->all();
    if (empty($inquiryPropertyTypes)) {
        $inquiryPropertyTypes = ['Apartment', 'Villa', 'Plot', 'Commercial'];
    }
    $inquiryBudgetOptions = [
        'Up to PKR 50 Lakh',
        'PKR 50 Lakh – 1 Crore',
        'PKR 1 – 2 Crore',
        'PKR 2 – 5 Crore',
        'PKR 5 Crore+',
    ];
    $projectLocation = trim((string) ($project->city ?? $project->short_address ?? ''));
    $projectLocation = \Illuminate\Support\Str::limit($projectLocation, 22, '...');
    $typesText = \Illuminate\Support\Str::limit($typesText, 18, '...');
    $statusText = strtoupper(str_replace('_', ' ', (string) ($project->status ?? 'UNDER CONSTRUCTION')));
    $vrTourUrl = is_string($project->vr_tour_url ?? null) ? trim($project->vr_tour_url) : '';
    if ($vrTourUrl !== '' && !preg_match('/^https?:\/\//i', $vrTourUrl)) {
        $vrTourUrl = 'https://' . $vrTourUrl;
    }
    $vrTourPageUrl = $vrTourUrl !== '' ? route('project.vr-tour', ['project' => $project->id]) : '';

    $metaType = \Illuminate\Support\Str::limit($typesFull !== '' ? $typesFull : 'N/A', 28, '...');
    $metaPrice = \Illuminate\Support\Str::limit(trim((string) ($project->price_string ?? $project->price ?? 'N/A')), 28, '...');
    $metaCity = \Illuminate\Support\Str::limit(trim((string) ($project->city ?? 'N/A')), 24, '...');
    $metaLocation = \Illuminate\Support\Str::limit(trim((string) ($project->short_address ?? $project->location ?? $project->city ?? 'N/A')), 28, '...');

    $hasMap = !empty($project->latitude) && !empty($project->longitude);
    $mapEmbedUrl = $hasMap
        ? 'https://www.google.com/maps?q=' . $project->latitude . ',' . $project->longitude . '&z=15&output=embed'
        : '';
    $inquiryMapUrl = $hasMap ? $mapEmbedUrl : '';
    if ($inquiryMapUrl === '' && !empty($cs->latitude) && !empty($cs->longitude)) {
        $inquiryMapUrl = 'https://www.google.com/maps?q=' . $cs->latitude . ',' . $cs->longitude . '&z=15&output=embed';
    }
    $highlightCards = [
        ['icon' => 'fa-location-dot', 'title' => 'Prime Location', 'desc' => \Illuminate\Support\Str::limit($metaLocation !== 'N/A' ? $metaLocation : 'Easy access to key amenities', 34, '...')],
        ['icon' => 'fa-sparkles', 'title' => 'Lifestyle Amenities', 'desc' => 'Built for comfort & wellbeing'],
        ['icon' => 'fa-house-heart', 'title' => 'Modern Living', 'desc' => 'Elegant & functional homes'],
        ['icon' => 'fa-bolt', 'title' => 'Future Ready', 'desc' => 'Smart & sustainable design'],
        ['icon' => 'fa-shield-check', 'title' => 'Secure Community', 'desc' => 'Gated community with 24/7 security'],
    ];

    $gallerySorted = collect($project->gallery ?? [])->sortBy('order')->values();
    $mediaGallery = [];
    $mediaGallery[] = ['url' => $featuredUrl, 'alt' => $project->title];
    foreach ($gallerySorted as $g) {
        $path = is_array($g) ? ($g['path'] ?? null) : null;
        if ($path) {
            $fullUrl = url('storage/' . ltrim($path, '/'));
            if ($fullUrl !== $featuredUrl) {
                $mediaGallery[] = ['url' => $fullUrl, 'alt' => $project->title];
            }
        }
    }
    $mediaPreviewImages = array_slice($mediaGallery, 0, 3);
    $remainingPhotoCount = max(count($mediaGallery) - count($mediaPreviewImages), 0);

    $featuredVideoId = null;
    $featuredVideoEmbedUrl = null;
    if ($project->featured_youtube_url && is_string($project->featured_youtube_url)) {
        $rawVideo = trim($project->featured_youtube_url);
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $rawVideo, $mVideo)) {
            $featuredVideoId = $mVideo[1];
        } else {
            $videoPart = explode('?', $rawVideo)[0];
            if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', trim($videoPart))) {
                $featuredVideoId = trim($videoPart);
            }
        }
        if ($featuredVideoId) {
            $featuredVideoEmbedUrl = 'https://www.youtube.com/embed/' . $featuredVideoId;
        }
    }
    $videoGalleryItems = [];
    if ($featuredVideoId) {
        $videoGalleryItems[] = [
            'id' => $featuredVideoId,
            'embed' => 'https://www.youtube.com/embed/' . $featuredVideoId,
            'thumb' => 'https://img.youtube.com/vi/' . $featuredVideoId . '/hqdefault.jpg',
            'title' => trim((string) ($project->featured_video_title ?? 'Featured Video')),
        ];
    }
    $rawVideoList = is_array($project->videos ?? null) ? array_values($project->videos) : [];
    foreach ($rawVideoList as $idx => $videoRawItem) {
        $videoRaw = trim((string) $videoRawItem);
        if ($videoRaw === '') {
            continue;
        }
        $videoId = null;
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $videoRaw, $mVideo)) {
            $videoId = $mVideo[1];
        } elseif (preg_match('/src=["\']([^"\']+)["\']/i', $videoRaw, $mSrc) && preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $mSrc[1], $mFromSrc)) {
            $videoId = $mFromSrc[1];
        } else {
            $videoPart = explode('?', $videoRaw)[0];
            if (preg_match('/^[a-zA-Z0-9_-]{10,}$/', trim($videoPart))) {
                $videoId = trim($videoPart);
            }
        }
        if (!$videoId) {
            continue;
        }
        $videoGalleryItems[] = [
            'id' => $videoId,
            'embed' => 'https://www.youtube.com/embed/' . $videoId,
            'thumb' => 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg',
            'title' => 'Video ' . ($idx + 1),
        ];
    }

    $pricingPlaceCards = is_array($project->pricing_place_cards ?? null) ? array_values($project->pricing_place_cards) : [];
    $planCards = [];
    $heroLocationLine = trim(implode(', ', array_filter([
        trim((string) ($project->city ?? '')),
        trim((string) ($project->state ?? '')),
    ])));
    $heroAreaLine = trim((string) ($project->short_address ?? ''));
    if ($heroAreaLine === '') {
        $heroAreaLine = trim((string) ($project->full_address ?? ''));
    }
    $heroUnitsCount = count($pricingPlaceCards) ?: count($planCards);
    $heroQuickMeta = array_values(array_filter([
        $typesFull !== '' ? ['icon' => 'fa-house', 'text' => \Illuminate\Support\Str::limit($typesFull, 24, '...')] : null,
        $heroLocationLine !== '' ? ['icon' => 'fa-location-dot', 'text' => \Illuminate\Support\Str::limit($heroLocationLine, 28, '...')] : null,
        $heroAreaLine !== '' ? ['icon' => 'fa-chart-area', 'text' => \Illuminate\Support\Str::limit($heroAreaLine, 22, '...')] : null,
    ]));
    $heroBedroomsLine = '—';
    foreach ($pricingPlaceCards as $card) {
        $cardTitle = trim((string) ($card['title'] ?? ''));
        if (preg_match('/(\d+\s*(?:&\s*\d+)?\s*(?:BHK|Bed|Bedroom))/i', $cardTitle, $bedMatch)) {
            $heroBedroomsLine = trim($bedMatch[1]);
            break;
        }
    }
    if ($heroBedroomsLine === '—' && !empty($project->price_plan_items)) {
        foreach ((array) $project->price_plan_items as $planItem) {
            if (preg_match('/(\d+\s*(?:&\s*\d+)?\s*(?:BHK|Bed|Bedroom))/i', (string) $planItem, $bedMatch)) {
                $heroBedroomsLine = trim($bedMatch[1]);
                break;
            }
        }
    }
    $heroCompletion = trim((string) ($project->future_note_title ?? ''));
    if ($heroCompletion === '') {
        $heroCompletion = trim((string) ($project->future_note_content ?? ''));
        $heroCompletion = $heroCompletion !== '' ? \Illuminate\Support\Str::limit(strip_tags($heroCompletion), 18, '...') : '—';
    }
    $computedHeroStatCards = [
        ['icon' => 'fa-building', 'label' => 'Total Units', 'value' => $heroUnitsCount > 0 ? (string) $heroUnitsCount : '—'],
        ['icon' => 'fa-bed', 'label' => 'Bedrooms', 'value' => $heroBedroomsLine],
        ['icon' => 'fa-tag', 'label' => 'Starting Price', 'value' => $metaPrice],
        ['icon' => 'fa-calendar', 'label' => 'Expected Completion', 'value' => $heroCompletion !== '' ? \Illuminate\Support\Str::limit($heroCompletion, 20, '...') : '—'],
    ];
    $storedHeroStatCards = is_array($project->hero_stat_cards ?? null) ? array_values($project->hero_stat_cards) : [];
    if (!empty($storedHeroStatCards)) {
        $projectMetaStats = [];
        foreach (array_slice($storedHeroStatCards, 0, 4) as $statIdx => $statCard) {
            if (!is_array($statCard)) {
                continue;
            }
            $statValue = trim((string) ($statCard['value'] ?? ''));
            $fallback = $computedHeroStatCards[$statIdx] ?? null;
            if ($statValue === '' && is_array($fallback)) {
                $statValue = (string) ($fallback['value'] ?? '—');
            }
            $projectMetaStats[] = [
                'icon' => trim((string) ($statCard['icon'] ?? '')) !== '' ? trim((string) $statCard['icon']) : (string) ($fallback['icon'] ?? 'fa-circle-info'),
                'label' => trim((string) ($statCard['label'] ?? '')) !== '' ? trim((string) $statCard['label']) : (string) ($fallback['label'] ?? 'Label'),
                'value' => $statValue !== '' ? $statValue : '—',
            ];
        }
    }
    if (empty($projectMetaStats)) {
        $projectMetaStats = $computedHeroStatCards;
    }
    $defaultHeroFeatureCards = [
        ['icon' => 'fa-leaf-heart', 'title' => 'Eco Friendly', 'color' => 'green'],
        ['icon' => 'fa-house-chimney-window', 'title' => 'Smart Home', 'color' => 'purple'],
        ['icon' => 'fa-shield-check', 'title' => '24/7 Security', 'color' => 'orange'],
        ['icon' => 'fa-compass-drafting', 'title' => 'Modern Design', 'color' => 'blue'],
    ];
    $storedHeroFeatureCards = is_array($project->hero_feature_cards ?? null) ? array_values($project->hero_feature_cards) : [];
    $heroFeatureCards = [];
    if (!empty($storedHeroFeatureCards)) {
        foreach (array_slice($storedHeroFeatureCards, 0, 4) as $featureCard) {
            if (!is_array($featureCard) || trim((string) ($featureCard['title'] ?? '')) === '') {
                continue;
            }
            $color = trim((string) ($featureCard['color'] ?? 'green'));
            if (!in_array($color, ['green', 'purple', 'orange', 'blue'], true)) {
                $color = 'green';
            }
            $heroFeatureCards[] = [
                'icon' => trim((string) ($featureCard['icon'] ?? 'fa-star')) ?: 'fa-star',
                'title' => trim((string) $featureCard['title']),
                'color' => $color,
            ];
        }
    }
    if (empty($heroFeatureCards)) {
        $heroFeatureCards = $defaultHeroFeatureCards;
    }
    $testimonials = is_array($project->testimonial_items ?? null) ? array_values(array_filter($project->testimonial_items, function ($item) {
        return is_array($item) && (
            trim((string) ($item['quote'] ?? '')) !== '' ||
            trim((string) ($item['name'] ?? '')) !== '' ||
            trim((string) ($item['role'] ?? '')) !== '' ||
            trim((string) ($item['image'] ?? '')) !== ''
        );
    })) : [];
    if (empty($testimonials)) {
        $testimonials[] = [
            'quote' => 'The best decision I made this year! Great location, excellent design, and the team has been amazing.',
            'name' => 'Jane W.',
            'role' => 'Verified Buyer',
            'image' => '',
        ];
    }
    $investTitle = trim((string) ($project->invest_title ?? 'Why Invest in First?'));
    $investPoints = is_array($project->invest_points ?? null) ? array_values(array_filter(array_map(function ($v) {
        return trim((string) $v);
    }, $project->invest_points))) : [];
    if (empty($investPoints)) {
        $investPoints = ['High appreciation potential', 'Strategic & prime location', 'Trusted developer', 'Flexible payment plans'];
    }
    $investImagePath = trim((string) ($project->invest_image ?? ''));
    $investImageUrl = $investImagePath !== '' ? url('storage/' . ltrim($investImagePath, '/')) : $featuredUrl;

    $uniqueFeatureItems = [];
    if (is_array($project->unique_features ?? null)) {
        foreach ($project->unique_features as $uf) {
            if (is_array($uf)) {
                $title = trim((string) ($uf['title'] ?? ''));
            } else {
                $title = trim((string) $uf);
            }
            if ($title !== '') {
                $uniqueFeatureItems[] = $title;
            }
        }
    }
    $amenityItems = array_slice($uniqueFeatureItems, 0, 8);
    if (empty($amenityItems)) {
        $amenityItems = ['Swimming Pool', 'Rooftop Lounge', 'Gym & Fitness', 'CCTV Surveillance', "Children's Play Area", 'Ample Parking', 'IoT Security', 'Power Backup'];
    }
    $keyFeatureItems = array_slice($uniqueFeatureItems, 0, 5);
    if (empty($keyFeatureItems)) {
        $keyFeatureItems = ['Premium Residential Units', 'Modern architecture & finishes', 'Green & sustainable design', 'Secure gated community', 'High rental yield potential'];
    }
    $locationPoints = [
        '5 mins to ' . ($project->city ?: 'City Centre'),
        '10 mins to Main Boulevard',
        'Close to top schools',
        'Near hospitals & malls',
    ];
    $locationDesc = trim((string) ($project->short_address ?? ''));
    if ($locationDesc === '') {
        $locationDesc = \Illuminate\Support\Str::limit(trim(strip_tags((string) ($project->description ?? ''))), 115, '...');
    }
    $resolveAmenityIcon = function (string $label): string {
        $l = strtolower($label);
        if (str_contains($l, 'swim') || str_contains($l, 'pool')) return 'fa-person-swimming';
        if (str_contains($l, 'gym') || str_contains($l, 'fitness')) return 'fa-dumbbell';
        if (str_contains($l, 'child') || str_contains($l, 'play')) return 'fa-child-reaching';
        if (str_contains($l, 'rooftop') || str_contains($l, 'lounge')) return 'fa-martini-glass';
        if (str_contains($l, 'cctv') || str_contains($l, 'surveillance') || str_contains($l, 'security')) return 'fa-camera-cctv';
        if (str_contains($l, 'parking') || str_contains($l, 'car')) return 'fa-car';
        if (str_contains($l, 'lift') || str_contains($l, 'elevator') || str_contains($l, 'speed')) return 'fa-elevator';
        if (str_contains($l, 'power') || str_contains($l, 'backup')) return 'fa-bolt';
        return 'fa-star';
    };
    $resolveFeatureIcon = function (string $label): string {
        $l = strtolower($label);
        if (str_contains($l, 'unit')) return 'fa-building';
        if (str_contains($l, 'architect') || str_contains($l, 'finish')) return 'fa-compass-drafting';
        if (str_contains($l, 'green') || str_contains($l, 'sustain')) return 'fa-leaf';
        if (str_contains($l, 'secure') || str_contains($l, 'gated')) return 'fa-shield-check';
        if (str_contains($l, 'rental') || str_contains($l, 'yield') || str_contains($l, 'invest')) return 'fa-chart-line-up';
        return 'fa-badge-check';
    };
@endphp

@php
    $metaTitle = seo_str($project->meta_title ?? '') ?: ($project->title . ' - ' . config('app.name'));
    $metaDesc = seo_str($project->meta_description ?? '') ?: seo_desc($project->description ?? '');
    $metaKeywords = seo_str($project->meta_keywords ?? '');
    $canonicalUrl = seo_str($project->canonical_url ?? '') ?: url()->current();
@endphp

@section('title', $metaTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => [
    'title' => $metaTitle,
    'description' => $metaDesc,
    'keywords' => $metaKeywords,
    'canonical' => $canonicalUrl,
    'image' => $featuredUrl,
    'type' => 'website',
]])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/project-new.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')

    <div class="wrapper project-new-page">
        <div class="content">
            <section class="project-new-hero" style="--hero-bg: url('{{ $featuredUrl }}')">
                <div class="container">
                    <div class="project-new-hero-inner">
                        <div class="project-new-hero-left">
                            <div class="project-new-hero-top">
                                <div class="project-new-breadcrumb">
                                    <a href="{{ url('/') }}">Home</a>
                                    <span class="project-new-breadcrumb-sep">/</span>
                                    <a href="{{ url('/projects') }}">Projects</a>
                                    <span class="project-new-breadcrumb-sep">/</span>
                                    <span>{{ $project->title }}</span>
                                </div>
                                <span class="project-new-chip"><i class="fa-solid fa-circle"></i> {{ $statusText }}</span>
                                <h1>{{ $project->title }}</h1>
                                @if(!empty($heroQuickMeta))
                                <div class="project-new-hero-meta">
                                    @foreach($heroQuickMeta as $metaLine)
                                        <span><i class="fa-light {{ $metaLine['icon'] }}"></i> {{ $metaLine['text'] }}</span>
                                    @endforeach
                                </div>
                                @endif
                                @if($shortDesc !== '')
                                <p class="project-new-hero-desc">{{ $shortDesc }}</p>
                                @endif
                                <div class="project-new-feature-row">
                                    @foreach($heroFeatureCards as $heroFeature)
                                    <div class="project-new-feature-card feature-{{ $heroFeature['color'] }}">
                                        <i class="fa-light {{ $heroFeature['icon'] }}"></i>
                                        <em>{{ $heroFeature['title'] }}</em>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="project-new-bottom-stats">
                                @foreach($projectMetaStats as $metaItem)
                                    <div class="project-new-stat-card">
                                        <span class="project-new-stat-icon"><i class="fa-light {{ $metaItem['icon'] }}"></i></span>
                                        <span class="project-new-stat-copy">
                                            <strong class="project-new-stat-value">{{ $metaItem['value'] }}</strong>
                                            <small class="project-new-stat-label">{{ $metaItem['label'] }}</small>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <aside class="project-new-hero-card">
                            <h3>Get In Touch</h3>
                            <p>Interested in this project? Let's connect with our team.</p>
                            <a href="#" class="project-new-hero-btn primary project-new-open-inquiry-trigger"><span>Schedule a Visit</span><i class="fa-light fa-calendar project-new-right-icon"></i></a>
                            @if($vrTourPageUrl !== '')
                            <a href="{{ $vrTourPageUrl }}" target="_blank" rel="noopener" class="project-new-hero-btn vr"><span>VR Tour</span><i class="fa-solid fa-vr-cardboard project-new-right-icon"></i></a>
                            @endif
                            @if(!empty($project->project_file_pdf))
                            <a href="{{ url('storage/' . ltrim((string) $project->project_file_pdf, '/')) }}" class="project-new-hero-btn light" download><span>Download Brochure</span><i class="fa-light fa-download project-new-right-icon"></i></a>
                            @else
                            <span class="project-new-hero-btn light is-disabled"><span>Download Brochure</span><i class="fa-light fa-download project-new-right-icon"></i></span>
                            @endif
                            <a href="#" class="project-new-hero-btn light project-new-open-inquiry-trigger" id="project-new-open-inquiry"><span>Send Inquiry</span><i class="fa-solid fa-paper-plane-top project-new-right-icon"></i></a>
                            <ul class="project-new-contact-list">
                                <li><i class="fa-light fa-user-tie"></i> Sales Team</li>
                                @if($projectPhone !== '')<li><i class="fa-light fa-phone"></i> {{ $projectPhone }}</li>@endif
                                @if($projectEmail !== '')<li><i class="fa-light fa-envelope"></i> {{ $projectEmail }}</li>@endif
                                <li><i class="fa-light fa-clock"></i> Mon - Fri (8AM - 6PM)</li>
                            </ul>
                            @if($projectPhoneClean !== '')
                            <a href="tel:{{ $projectPhoneClean }}" class="project-new-hero-btn dark"><span>Book a Call</span><i class="fa-light fa-phone project-new-right-icon"></i></a>
                            @endif
                        </aside>
                    </div>
                </div>
            </section>

            <section class="project-new-media-strip">
                <div class="container">
                    <div class="project-new-media-grid">
                        @if($featuredVideoEmbedUrl)
                            <a href="#" class="project-new-media-card project-new-media-video" id="project-new-open-video" data-video="{{ $featuredVideoEmbedUrl }}">
                                <img src="https://img.youtube.com/vi/{{ $featuredVideoId }}/hqdefault.jpg" alt="{{ $project->featured_video_title ?: 'Project Video Tour' }}" loading="lazy">
                                <span class="project-new-media-video-btn"><i class="fa-solid fa-circle-play"></i></span>
                                <span class="project-new-media-video-title">{{ $project->featured_video_title ?: 'Project Video Tour' }}</span>
                            </a>
                        @endif

                        @foreach($mediaPreviewImages as $imgIndex => $imgItem)
                            <a href="#" class="project-new-media-card project-new-open-gallery" data-index="{{ $imgIndex }}">
                                <img src="{{ $imgItem['url'] }}" alt="{{ $imgItem['alt'] ?? $project->title }}" loading="lazy">
                            </a>
                        @endforeach

                        @if(count($mediaGallery) > 0)
                            <a href="#" class="project-new-media-card project-new-media-more project-new-open-gallery" data-index="0">
                                <i class="fa-light fa-images"></i>
                                <strong>+{{ $remainingPhotoCount > 0 ? $remainingPhotoCount : count($mediaGallery) }}</strong>
                                <span>More Photos</span>
                            </a>
                        @endif
                    </div>
                </div>
            </section>

            <section class="project-new-highlights">
                <div class="container">
                    <div class="project-new-highlights-wrap">
                        <div class="project-new-highlights-head">
                            <h4>Project Highlights</h4>
                            <button type="button" class="project-new-view-map-btn" id="project-new-open-map" {{ $hasMap ? '' : 'disabled' }}>
                                <i class="fa-light fa-location-dot"></i> View on Map <i class="fa-light fa-angle-right"></i>
                            </button>
                        </div>
                        @foreach($highlightCards as $card)
                            <div class="project-new-highlight-card">
                                <span class="project-new-highlight-icon"><i class="fa-light {{ $card['icon'] }}"></i></span>
                                <span class="project-new-highlight-title">{{ $card['title'] }}</span>
                                <span class="project-new-highlight-desc">{{ $card['desc'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            @if(!empty($pricingPlaceCards))
            <section class="project-new-pricing-place">
                <div class="container">
                    <div class="project-new-pricing-head">
                        <h3>Pricing Place</h3>
                        <div class="project-new-pricing-nav">
                            <button type="button" id="project-new-pricing-prev" aria-label="Previous"><i class="fa-solid fa-angle-left"></i></button>
                            <button type="button" id="project-new-pricing-next" aria-label="Next"><i class="fa-solid fa-angle-right"></i></button>
                        </div>
                    </div>
                    <div class="project-new-pricing-track-wrap">
                        <div class="project-new-pricing-track" id="project-new-pricing-track">
                            @foreach($pricingPlaceCards as $idx => $card)
                                @php
                                    $cardImage = trim((string) ($card['image'] ?? ''));
                                    $cardImageUrl = $cardImage !== '' ? url('storage/' . ltrim($cardImage, '/')) : $featuredUrl;
                                    $cardPrice = trim((string) ($card['price'] ?? 'Price on request'));
                                    $cardTitle = trim((string) ($card['title'] ?? ('Plan ' . ($idx + 1))));
                                    $cardFeatures = isset($card['features']) && is_array($card['features']) ? array_values(array_filter($card['features'])) : [];
                                    $cardButton = trim((string) ($card['button_text'] ?? 'View Plan'));
                                    $cardPopular = !empty($card['is_popular']);
                                @endphp
                                <article class="project-new-pricing-card {{ $cardPopular ? 'is-popular' : '' }}">
                                    @if($cardPopular)<span class="project-new-pricing-popular">Most Popular</span>@endif
                                    <div class="project-new-pricing-content">
                                        <h4 class="project-new-pricing-title">{{ $cardTitle }}</h4>
                                        <p class="project-new-pricing-from">from</p>
                                        <p class="project-new-pricing-price">{{ $cardPrice }}</p>
                                        <ul class="project-new-pricing-features">
                                            @foreach(array_slice($cardFeatures, 0, 4) as $f)
                                                <li><i class="fa-light fa-check"></i>{{ $f }}</li>
                                            @endforeach
                                        </ul>
                                        <a href="#" class="project-new-pricing-btn project-new-open-plan" data-index="{{ $idx }}">
                                            {{ $cardButton !== '' ? $cardButton : 'View Plan' }} <i class="fa-light fa-expand"></i>
                                        </a>
                                    </div>
                                    <div class="project-new-pricing-image">
                                        <img src="{{ $cardImageUrl }}" alt="{{ $cardTitle }}" loading="lazy">
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <section class="project-new-kfl-section">
                <div class="container">
                    <div class="project-new-kfl-layout">
                        <div class="project-new-kfl-amenities-bar">
                            <h4>World-Class Amenities</h4>
                            <ul class="project-new-kfl-amenities-icons">
                                @foreach($amenityItems as $amenity)
                                    <li>
                                        <span class="project-new-kfl-amenity-icon"><i class="fa-light {{ $resolveAmenityIcon($amenity) }}"></i></span>
                                        <span class="project-new-kfl-amenity-label">{{ $amenity }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="project-new-kfl-grid">
                            <div class="project-new-kfl-card project-new-kfl-location">
                                <div class="project-new-kfl-location-body">
                                    <div class="project-new-kfl-location-copy">
                                        <h4>Prime Location</h4>
                                        @if($locationDesc !== '')
                                        <p class="project-new-kfl-location-desc">{{ $locationDesc }}</p>
                                        @endif
                                        <ul>
                                            @foreach($locationPoints as $point)
                                                <li><i class="fa-light fa-bullseye"></i> {{ $point }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="project-new-kfl-map-btn project-new-view-map-btn" {{ $hasMap ? '' : 'disabled' }}>
                                            View on Map <i class="fa-light fa-angle-right"></i>
                                        </button>
                                    </div>
                                    <div class="project-new-kfl-map">
                                        @if($hasMap)
                                            <iframe src="{{ $mapEmbedUrl }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        @else
                                            <div class="project-new-kfl-map-fallback">Map will appear when latitude/longitude is added.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="project-new-kfl-card project-new-kfl-features">
                                <div class="project-new-kfl-features-body">
                                    <div class="project-new-kfl-features-copy">
                                        <h4>Key Features</h4>
                                        <ul>
                                            @foreach($keyFeatureItems as $feature)
                                                <li><i class="fa-light {{ $resolveFeatureIcon($feature) }}"></i> {{ $feature }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="project-new-kfl-features-media">
                                        <img src="{{ $featuredUrl }}" alt="{{ $project->title }}" class="project-new-kfl-building" loading="lazy">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="project-new-meta-strip">
                <div class="container">
                    <div class="project-new-meta-strip-wrap">
                        @foreach($projectMetaStats as $metaItem)
                            <div class="project-new-meta-strip-item">
                                <span class="project-new-meta-strip-icon"><i class="fa-light {{ $metaItem['icon'] }}"></i></span>
                                <span>
                                    <strong class="project-new-meta-strip-value">{{ $metaItem['value'] }}</strong>
                                    <small class="project-new-meta-strip-label">{{ $metaItem['label'] }}</small>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="project-new-social-proof">
                <div class="container">
                    <div class="project-new-social-proof-grid">
                        <div class="project-new-social-card project-new-social-testimonial">
                            <h3 class="project-new-social-head">What Our Clients Say</h3>
                            <div id="project-new-testimonial-wrap"></div>
                            <div class="project-new-testimonial-controls">
                                <div class="project-new-testimonial-dots" id="project-new-testimonial-dots"></div>
                                <div class="etihad-inline-flex-gap">
                                    <button type="button" class="project-new-testimonial-nav" id="project-new-testimonial-prev" aria-label="Previous"><i class="fa-light fa-angle-left"></i></button>
                                    <button type="button" class="project-new-testimonial-nav" id="project-new-testimonial-next" aria-label="Next"><i class="fa-light fa-angle-right"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="project-new-social-card project-new-social-invest">
                            <img src="{{ $investImageUrl }}" alt="{{ $project->title }}" loading="lazy">
                            <div class="project-new-social-invest-body">
                                <h4>{{ $investTitle !== '' ? $investTitle : 'Why Invest in First?' }}</h4>
                                <ul>
                                    @foreach($investPoints as $point)
                                        <li><i class="fa-solid fa-circle-check"></i> {{ $point }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if(!empty($videoGalleryItems))
            <section class="project-new-video-gallery">
                <div class="container">
                    <div class="project-new-video-gallery-head">
                        <h3>Video Gallery</h3>
                    </div>
                    <div class="project-new-video-gallery-slider swiper-container" id="project-new-video-gallery-slider">
                        <div class="swiper-wrapper">
                        @foreach($videoGalleryItems as $vIdx => $videoItem)
                            <div class="swiper-slide">
                                <a href="#" class="project-new-video-gallery-card project-new-open-video-gallery" data-index="{{ $vIdx }}">
                                    <img src="{{ $videoItem['thumb'] }}" alt="Gallery video" loading="lazy">
                                    <span class="project-new-video-gallery-play"><i class="fa-solid fa-play"></i></span>
                                </a>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <section class="project-new-git" id="project-new-inquiry">
                <div class="container">
                    <header class="project-new-git__head">
                        <p class="project-new-git__eyebrow">GET IN TOUCH</p>
                        <h2 class="project-new-git__title">Let's find your <span class="project-new-git__title-accent">next address</span></h2>
                    </header>
                    <div class="project-new-git__grid">
                        <div class="project-new-git__aside">
                            @if($visitAddress !== '')
                            <article class="project-new-git__info-card">
                                <span class="project-new-git__info-icon" aria-hidden="true"><i class="fa-solid fa-location-dot"></i></span>
                                <div>
                                    <h3>Visit Us</h3>
                                    <p>{{ $visitAddress }}</p>
                                </div>
                            </article>
                            @endif
                            @if($projectPhone !== '')
                            <article class="project-new-git__info-card">
                                <span class="project-new-git__info-icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></span>
                                <div>
                                    <h3>Call Us</h3>
                                    <p><a href="tel:{{ $projectPhoneClean }}">{{ $projectPhone }}</a></p>
                                </div>
                            </article>
                            @endif
                            @if($projectEmail !== '')
                            <article class="project-new-git__info-card">
                                <span class="project-new-git__info-icon" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
                                <div>
                                    <h3>Email Us</h3>
                                    <p><a href="mailto:{{ $projectEmail }}">{{ $projectEmail }}</a></p>
                                </div>
                            </article>
                            @endif
                            @if($inquiryMapUrl !== '')
                            <div class="project-new-git__map">
                                <iframe src="{{ $inquiryMapUrl }}" title="Project location map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                            </div>
                            @endif
                        </div>
                        <div class="project-new-git__form-card">
                            <form method="post" action="{{ route('project.request-info') }}" class="project-new-git__form" id="project-new-inline-inquiry-form">
                                @csrf
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <div class="project-new-git__form-row">
                                    <input type="text" name="name" placeholder="Full Name" required>
                                    <input type="text" name="phone" placeholder="Phone">
                                </div>
                                <input type="email" name="email" placeholder="Email">
                                <div class="project-new-git__form-row">
                                    <select name="property_type" aria-label="Property type">
                                        <option value="">Property Type</option>
                                        @foreach($inquiryPropertyTypes as $ptype)
                                            <option value="{{ $ptype }}">{{ $ptype }}</option>
                                        @endforeach
                                    </select>
                                    <select name="budget" aria-label="Budget">
                                        <option value="">Budget</option>
                                        @foreach($inquiryBudgetOptions as $budgetOpt)
                                            <option value="{{ $budgetOpt }}">{{ $budgetOpt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <textarea name="message" rows="4" placeholder="Tell us what you're looking for..."></textarea>
                                <div class="project-new-git__form-msg" id="project-new-inline-inquiry-msg" aria-live="polite"></div>
                                <button type="submit" class="project-new-git__submit" id="project-new-inline-inquiry-submit">
                                    Send Message <i class="fa-solid fa-paper-plane"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            @if(!empty($videoGalleryItems))
            <div class="project-new-videos-modal" id="project-new-videos-modal" aria-hidden="true">
                <div class="project-new-videos-inner">
                    <button type="button" class="project-new-videos-close" id="project-new-videos-close" aria-label="Close">&times;</button>
                    <div class="project-new-videos-stage">
                        <button type="button" class="project-new-videos-nav prev" id="project-new-videos-prev" aria-label="Previous video"><i class="fa-solid fa-angle-left"></i></button>
                        <iframe id="project-new-videos-frame" class="project-new-videos-frame" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        <button type="button" class="project-new-videos-nav next" id="project-new-videos-next" aria-label="Next video"><i class="fa-solid fa-angle-right"></i></button>
                    </div>
                    <div class="project-new-videos-thumbs" id="project-new-videos-thumbs"></div>
                </div>
            </div>
            @endif

            @if($featuredVideoEmbedUrl)
            <div class="project-new-video-modal" id="project-new-video-modal" aria-hidden="true">
                <div class="project-new-video-inner">
                    <button type="button" class="project-new-video-close" id="project-new-video-close" aria-label="Close">&times;</button>
                    <iframe id="project-new-video-iframe" class="project-new-video-iframe" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
            </div>
            @endif

            @if(count($mediaGallery) > 0)
            <div class="project-new-gallery-modal" id="project-new-gallery-modal" aria-hidden="true">
                <div class="project-new-gallery-inner">
                    <button type="button" class="project-new-gallery-close" id="project-new-gallery-close" aria-label="Close">&times;</button>
                    <div class="project-new-gallery-stage">
                        <button type="button" class="project-new-gallery-nav prev" id="project-new-gallery-prev" aria-label="Previous image"><i class="fa-solid fa-angle-left"></i></button>
                        <img src="" alt="" id="project-new-gallery-image">
                        <button type="button" class="project-new-gallery-nav next" id="project-new-gallery-next" aria-label="Next image"><i class="fa-solid fa-angle-right"></i></button>
                    </div>
                    <div class="project-new-gallery-thumbs" id="project-new-gallery-thumbs"></div>
                </div>
            </div>
            @endif

            @if($hasMap)
            <div class="project-new-map-modal" id="project-new-map-modal" aria-hidden="true">
                <div class="project-new-map-inner">
                    <button type="button" class="project-new-map-close" id="project-new-map-close" aria-label="Close">&times;</button>
                    <iframe class="project-new-map-frame" src="{{ $mapEmbedUrl }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            @endif

            @if(!empty($pricingPlaceCards))
            <div class="project-new-plan-modal" id="project-new-plan-modal" aria-hidden="true">
                <div class="project-new-plan-modal-inner">
                    <button type="button" class="project-new-plan-modal-close" id="project-new-plan-close" aria-label="Close">&times;</button>
                    <div class="project-new-plan-stage">
                        <button type="button" class="project-new-plan-nav prev" id="project-new-plan-prev" aria-label="Previous plan"><i class="fa-solid fa-angle-left"></i></button>
                        <img src="" alt="" id="project-new-plan-image">
                        <button type="button" class="project-new-plan-nav next" id="project-new-plan-next" aria-label="Next plan"><i class="fa-solid fa-angle-right"></i></button>
                    </div>
                </div>
            </div>
            @endif

            <div class="project-new-inquiry-modal" id="project-new-inquiry-modal">
                <div class="project-new-inquiry-dialog">
                    <button type="button" class="project-new-inquiry-close" id="project-new-close-inquiry" aria-label="Close">&times;</button>
                    <h4>Send Inquiry</h4>
                    <p class="project-new-inquiry-sub">Share your details and our team will get back to you shortly.</p>
                    <form method="post" action="{{ route('project.request-info') }}" id="project-new-inquiry-form" class="project-new-inquiry-form">
                        @csrf
                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="text" name="phone" placeholder="Phone Number">
                        <input type="email" name="email" placeholder="Email Address">
                        <textarea name="message" rows="4" placeholder="Your Message"></textarea>
                        <div class="project-new-inquiry-msg" id="project-new-inquiry-msg"></div>
                        <button type="submit" class="project-new-hero-btn primary project-new-modal-submit" id="project-new-inquiry-submit">
                            <span>Submit Inquiry</span>
                            <i class="fa-light fa-paper-plane project-new-right-icon"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>

    @include('partials.theme-panels')
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var mediaGallery = @json(array_values($mediaGallery));
    var pricingPlaceCards = @json(array_values($pricingPlaceCards));
    var testimonialItems = @json(array_values($testimonials));
    var videoGalleryItems = @json(array_values($videoGalleryItems));
    var htmlEl = document.documentElement;
    var bodyEl = document.body;
    var openModalCount = 0;
    var videoModal = document.getElementById('project-new-video-modal');
    var galleryModal = document.getElementById('project-new-gallery-modal');
    var mapModal = document.getElementById('project-new-map-modal');
    var planModal = document.getElementById('project-new-plan-modal');
    var plansModal = null;
    var videosModal = document.getElementById('project-new-videos-modal');
    var inquiryModal = document.getElementById('project-new-inquiry-modal');

    // Move modals to <body> so no parent stacking context can overlap them.
    [videoModal, galleryModal, mapModal, planModal, plansModal, videosModal, inquiryModal].forEach(function (node) {
        if (node && node.parentNode !== bodyEl) bodyEl.appendChild(node);
    });

    function lockPageScroll() {
        openModalCount += 1;
        htmlEl.classList.add('project-new-no-scroll');
        bodyEl.classList.add('project-new-no-scroll');
    }

    function unlockPageScroll() {
        openModalCount = Math.max(0, openModalCount - 1);
        if (openModalCount === 0) {
            htmlEl.classList.remove('project-new-no-scroll');
            bodyEl.classList.remove('project-new-no-scroll');
        }
    }

    var videoTrigger = document.getElementById('project-new-open-video');
    var videoClose = document.getElementById('project-new-video-close');
    var videoFrame = document.getElementById('project-new-video-iframe');

    if (videoTrigger && videoModal && videoFrame) {
        videoTrigger.addEventListener('click', function (e) {
            e.preventDefault();
            var src = videoTrigger.getAttribute('data-video') || '';
            if (!src) return;
            videoFrame.src = src + (src.indexOf('?') > -1 ? '&autoplay=1' : '?autoplay=1');
            videoModal.classList.add('is-open');
            lockPageScroll();
        });
    }
    if (videoClose && videoModal) {
        videoClose.addEventListener('click', function () {
            videoModal.classList.remove('is-open');
            if (videoFrame) videoFrame.src = '';
            unlockPageScroll();
        });
    }
    if (videoModal) {
        videoModal.addEventListener('click', function (e) {
            if (e.target === videoModal) {
                videoModal.classList.remove('is-open');
                if (videoFrame) videoFrame.src = '';
                unlockPageScroll();
            }
        });
    }

    var videosFrame = document.getElementById('project-new-videos-frame');
    var videosClose = document.getElementById('project-new-videos-close');
    var videosPrev = document.getElementById('project-new-videos-prev');
    var videosNext = document.getElementById('project-new-videos-next');
    var videosThumbs = document.getElementById('project-new-videos-thumbs');
    var videoGalleryTriggers = document.querySelectorAll('.project-new-open-video-gallery');
    var activeVideoIndex = 0;

    function renderVideoGalleryThumbs() {
        if (!videosThumbs) return;
        videosThumbs.innerHTML = '';
        videoGalleryItems.forEach(function (item, idx) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = idx === activeVideoIndex ? 'is-active' : '';
            btn.innerHTML = '<img src="' + (item.thumb || '') + '" alt="' + (item.title || 'Video') + '">';
            btn.addEventListener('click', function () {
                showVideoByIndex(idx);
            });
            videosThumbs.appendChild(btn);
        });
    }
    function showVideoByIndex(index) {
        if (!videosFrame || !videoGalleryItems.length) return;
        activeVideoIndex = (index + videoGalleryItems.length) % videoGalleryItems.length;
        var item = videoGalleryItems[activeVideoIndex] || {};
        var src = (item.embed || '').toString();
        videosFrame.src = src ? (src + (src.indexOf('?') > -1 ? '&autoplay=1' : '?autoplay=1')) : '';
        if (videosThumbs) {
            Array.prototype.forEach.call(videosThumbs.querySelectorAll('button'), function (el, idx) {
                el.classList.toggle('is-active', idx === activeVideoIndex);
            });
        }
    }
    if (videoGalleryTriggers.length && videosModal && videosFrame && videoGalleryItems.length) {
        renderVideoGalleryThumbs();
        videoGalleryTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                var idx = parseInt(trigger.getAttribute('data-index') || '0', 10);
                showVideoByIndex(isNaN(idx) ? 0 : idx);
                videosModal.classList.add('is-open');
                lockPageScroll();
            });
        });
    }
    var videoSliderEl = document.getElementById('project-new-video-gallery-slider');
    if (videoSliderEl && typeof Swiper !== 'undefined' && videoGalleryItems.length) {
        new Swiper('#project-new-video-gallery-slider', {
            slidesPerView: 4,
            spaceBetween: 16,
            loop: videoGalleryItems.length > 4,
            speed: 700,
            watchOverflow: true,
            grabCursor: true,
            autoplay: videoGalleryItems.length > 1 ? {
                delay: 2800,
                disableOnInteraction: false
            } : false,
            breakpoints: {
                991: {
                    slidesPerView: 1,
                    spaceBetween: 12
                }
            }
        });
    }
    if (videosClose && videosModal) {
        videosClose.addEventListener('click', function () {
            videosModal.classList.remove('is-open');
            if (videosFrame) videosFrame.src = '';
            unlockPageScroll();
        });
    }
    if (videosModal) {
        videosModal.addEventListener('click', function (e) {
            if (e.target === videosModal) {
                videosModal.classList.remove('is-open');
                if (videosFrame) videosFrame.src = '';
                unlockPageScroll();
            }
        });
    }
    if (videosPrev) videosPrev.addEventListener('click', function () { showVideoByIndex(activeVideoIndex - 1); });
    if (videosNext) videosNext.addEventListener('click', function () { showVideoByIndex(activeVideoIndex + 1); });

    var galleryImage = document.getElementById('project-new-gallery-image');
    var galleryThumbs = document.getElementById('project-new-gallery-thumbs');
    var galleryPrev = document.getElementById('project-new-gallery-prev');
    var galleryNext = document.getElementById('project-new-gallery-next');
    var galleryClose = document.getElementById('project-new-gallery-close');
    var galleryTriggers = document.querySelectorAll('.project-new-open-gallery');
    var activeGalleryIndex = 0;

    function renderGalleryThumbs() {
        if (!galleryThumbs || !mediaGallery.length) return;
        galleryThumbs.innerHTML = '';
        mediaGallery.forEach(function (item, idx) {
            var thumb = document.createElement('img');
            thumb.src = item.url;
            thumb.alt = item.alt || '';
            thumb.className = idx === activeGalleryIndex ? 'is-active' : '';
            thumb.addEventListener('click', function () { showGalleryImage(idx); });
            galleryThumbs.appendChild(thumb);
        });
    }

    function showGalleryImage(index) {
        if (!galleryImage || !mediaGallery.length) return;
        activeGalleryIndex = (index + mediaGallery.length) % mediaGallery.length;
        var item = mediaGallery[activeGalleryIndex];
        galleryImage.src = item.url;
        galleryImage.alt = item.alt || '';
        if (galleryThumbs) {
            Array.prototype.forEach.call(galleryThumbs.querySelectorAll('img'), function (imgEl, idx) {
                imgEl.classList.toggle('is-active', idx === activeGalleryIndex);
            });
        }
    }

    if (galleryTriggers.length && galleryModal && galleryImage && mediaGallery.length) {
        renderGalleryThumbs();
        galleryTriggers.forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                var idx = parseInt(trigger.getAttribute('data-index') || '0', 10);
                showGalleryImage(isNaN(idx) ? 0 : idx);
                galleryModal.classList.add('is-open');
                lockPageScroll();
            });
        });
    }
    if (galleryPrev) galleryPrev.addEventListener('click', function () { showGalleryImage(activeGalleryIndex - 1); });
    if (galleryNext) galleryNext.addEventListener('click', function () { showGalleryImage(activeGalleryIndex + 1); });
    if (galleryClose && galleryModal) {
        galleryClose.addEventListener('click', function () {
            galleryModal.classList.remove('is-open');
            unlockPageScroll();
        });
    }
    if (galleryModal) {
        galleryModal.addEventListener('click', function (e) {
            if (e.target === galleryModal) {
                galleryModal.classList.remove('is-open');
                unlockPageScroll();
            }
        });
    }

    var closeMapBtn = document.getElementById('project-new-map-close');
    document.querySelectorAll('.project-new-view-map-btn').forEach(function (openMapBtn) {
        if (!mapModal) return;
        openMapBtn.addEventListener('click', function () {
            if (openMapBtn.disabled) return;
            mapModal.classList.add('is-open');
            lockPageScroll();
        });
    });
    if (closeMapBtn && mapModal) {
        closeMapBtn.addEventListener('click', function () {
            mapModal.classList.remove('is-open');
            unlockPageScroll();
        });
    }
    if (mapModal) {
        mapModal.addEventListener('click', function (e) {
            if (e.target === mapModal) {
                mapModal.classList.remove('is-open');
                unlockPageScroll();
            }
        });
    }

    var pricingTrack = document.getElementById('project-new-pricing-track');
    var pricingPrev = document.getElementById('project-new-pricing-prev');
    var pricingNext = document.getElementById('project-new-pricing-next');
    var pricingPage = 0;
    function pricingVisibleCount() {
        if (!pricingTrack) return 1;
        var w = window.innerWidth || 1200;
        if (w <= 991) return 1;
        return 3;
    }
    function pricingStepWidth() {
        if (!pricingTrack) return 0;
        var wrap = pricingTrack.parentElement;
        if (!wrap) return 0;
        return wrap.getBoundingClientRect().width;
    }
    function syncPricingCardWidths() {
        if (!pricingTrack) return;
        var wrap = pricingTrack.parentElement;
        if (!wrap) return;
        var w = window.innerWidth || 1200;
        if (w <= 991) {
            wrap.style.setProperty('--pricing-slide-width', wrap.clientWidth + 'px');
        } else {
            wrap.style.removeProperty('--pricing-slide-width');
        }
        Array.prototype.forEach.call(pricingTrack.children, function (card) {
            card.style.flex = '';
            card.style.minWidth = '';
            card.style.maxWidth = '';
            card.style.width = '';
        });
    }
    function updatePricingTrack() {
        if (!pricingTrack) return;
        syncPricingCardWidths();
        var total = pricingTrack.children.length;
        var per = pricingVisibleCount();
        var pages = Math.max(1, Math.ceil(total / per));
        var maxPage = Math.max(0, pages - 1);
        pricingPage = Math.max(0, Math.min(pricingPage, maxPage));
        var w = window.innerWidth || 1200;
        var wrap = pricingTrack.parentElement;
        if (w <= 991 && wrap) {
            pricingTrack.classList.remove('is-center-two');
            pricingTrack.style.transform = 'none';
            var card = pricingTrack.children[pricingPage];
            if (card) {
                wrap.scrollTo({ left: card.offsetLeft, behavior: pricingPage === 0 ? 'auto' : 'smooth' });
            } else {
                wrap.scrollLeft = 0;
            }
        } else {
            pricingTrack.classList.toggle('is-center-two', total === 2 && per === 3);
            if (wrap) wrap.scrollLeft = 0;
            pricingTrack.style.transform = 'translateX(' + (-pricingPage * pricingStepWidth()) + 'px)';
        }
        if (pricingPrev) pricingPrev.disabled = pricingPage <= 0;
        if (pricingNext) pricingNext.disabled = pricingPage >= maxPage;
    }
    if (pricingPrev && pricingTrack) {
        pricingPrev.addEventListener('click', function () {
            pricingPage = Math.max(0, pricingPage - 1);
            updatePricingTrack();
        });
    }
    if (pricingNext && pricingTrack) {
        pricingNext.addEventListener('click', function () {
            var total = pricingTrack.children.length;
            var per = pricingVisibleCount();
            var maxPage = Math.max(0, Math.ceil(total / per) - 1);
            pricingPage = Math.min(maxPage, pricingPage + 1);
            updatePricingTrack();
        });
    }
    if (pricingTrack) {
        window.addEventListener('resize', updatePricingTrack);
        window.addEventListener('load', updatePricingTrack);
        updatePricingTrack();
    }

    var openPlanBtns = document.querySelectorAll('.project-new-open-plan');
    var planImage = document.getElementById('project-new-plan-image');
    var planClose = document.getElementById('project-new-plan-close');
    var planPrev = document.getElementById('project-new-plan-prev');
    var planNext = document.getElementById('project-new-plan-next');
    var activePlanIndex = 0;
    function showPlanByIndex(index) {
        if (!planImage || !pricingPlaceCards.length) return;
        activePlanIndex = (index + pricingPlaceCards.length) % pricingPlaceCards.length;
        var item = pricingPlaceCards[activePlanIndex] || {};
        var imagePath = (item.image || '').toString().trim();
        var imageUrl = imagePath !== '' ? '{{ url('storage') }}/' + imagePath.replace(/^\/+/, '') : '{{ $featuredUrl }}';
        var title = (item.title || '{{ $project->title }}').toString();
        planImage.src = imageUrl;
        planImage.alt = title;
    }
    if (openPlanBtns.length && planModal) {
        openPlanBtns.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var idx = parseInt(btn.getAttribute('data-index') || '0', 10);
                showPlanByIndex(isNaN(idx) ? 0 : idx);
                planModal.classList.add('is-open');
                lockPageScroll();
            });
        });
    }
    if (planClose && planModal) {
        planClose.addEventListener('click', function () {
            planModal.classList.remove('is-open');
            unlockPageScroll();
        });
    }
    if (planModal) {
        planModal.addEventListener('click', function (e) {
            if (e.target === planModal) {
                planModal.classList.remove('is-open');
                unlockPageScroll();
            }
        });
    }
    if (planPrev) planPrev.addEventListener('click', function () { showPlanByIndex(activePlanIndex - 1); });
    if (planNext) planNext.addEventListener('click', function () { showPlanByIndex(activePlanIndex + 1); });


    var openBtns = document.querySelectorAll('.project-new-open-inquiry-trigger');
    var closeBtn = document.getElementById('project-new-close-inquiry');
    var modal = inquiryModal;
    var form = document.getElementById('project-new-inquiry-form');
    var submitBtn = document.getElementById('project-new-inquiry-submit');
    var message = document.getElementById('project-new-inquiry-msg');

    if (openBtns.length && modal) {
        openBtns.forEach(function (openBtn) {
            openBtn.addEventListener('click', function (e) {
                e.preventDefault();
                modal.classList.add('is-open');
                lockPageScroll();
                var firstInput = form ? form.querySelector('input[name="name"]') : null;
                if (firstInput) setTimeout(function () { firstInput.focus(); }, 120);
            });
        });
    }
    if (closeBtn && modal) {
        closeBtn.addEventListener('click', function () {
            modal.classList.remove('is-open');
            unlockPageScroll();
        });
    }

    var testimonialWrap = document.getElementById('project-new-testimonial-wrap');
    var testimonialDots = document.getElementById('project-new-testimonial-dots');
    var testimonialPrev = document.getElementById('project-new-testimonial-prev');
    var testimonialNext = document.getElementById('project-new-testimonial-next');
    var testimonialIndex = 0;

    function renderTestimonialDots() {
        if (!testimonialDots) return;
        testimonialDots.innerHTML = '';
        testimonialItems.forEach(function (_, idx) {
            var dot = document.createElement('span');
            dot.className = 'project-new-testimonial-dot' + (idx === testimonialIndex ? ' is-active' : '');
            dot.addEventListener('click', function () {
                testimonialIndex = idx;
                renderTestimonialCard();
            });
            testimonialDots.appendChild(dot);
        });
    }
    function renderTestimonialCard() {
        if (!testimonialWrap || !testimonialItems.length) return;
        testimonialIndex = (testimonialIndex + testimonialItems.length) % testimonialItems.length;
        var item = testimonialItems[testimonialIndex] || {};
        testimonialWrap.innerHTML =
            '<div class="project-new-testimonial-quote">“ ' + ((item.quote || '').toString()) + ' ”</div>' +
            '<div class="project-new-testimonial-meta">' +
                '<span><strong>' + ((item.name || 'Our Client').toString()) + '</strong><span>' + ((item.role || 'Verified Buyer').toString()) + '</span></span>' +
            '</div>';
        if (testimonialDots) {
            Array.prototype.forEach.call(testimonialDots.querySelectorAll('.project-new-testimonial-dot'), function (dot, idx) {
                dot.classList.toggle('is-active', idx === testimonialIndex);
            });
        }
    }
    if (testimonialWrap && testimonialItems.length) {
        renderTestimonialDots();
        renderTestimonialCard();
        if (testimonialPrev) {
            testimonialPrev.addEventListener('click', function () {
                testimonialIndex -= 1;
                renderTestimonialCard();
            });
        }
        if (testimonialNext) {
            testimonialNext.addEventListener('click', function () {
                testimonialIndex += 1;
                renderTestimonialCard();
            });
        }
    }
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.classList.remove('is-open');
                unlockPageScroll();
            }
        });
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
            modal.classList.remove('is-open');
            unlockPageScroll();
        }
        if (e.key === 'Escape' && videoModal) {
            if (videoModal.classList.contains('is-open')) {
                videoModal.classList.remove('is-open');
                if (videoFrame) videoFrame.src = '';
                unlockPageScroll();
            }
        }
        if (e.key === 'Escape' && videosModal && videosModal.classList.contains('is-open')) {
            videosModal.classList.remove('is-open');
            if (videosFrame) videosFrame.src = '';
            unlockPageScroll();
        }
        if (e.key === 'Escape' && galleryModal && galleryModal.classList.contains('is-open')) {
            galleryModal.classList.remove('is-open');
            unlockPageScroll();
        }
        if (e.key === 'Escape' && mapModal && mapModal.classList.contains('is-open')) {
            mapModal.classList.remove('is-open');
            unlockPageScroll();
        }
        if (e.key === 'Escape' && planModal && planModal.classList.contains('is-open')) {
            planModal.classList.remove('is-open');
            unlockPageScroll();
        }
        if (e.key === 'Escape' && plansModal && plansModal.classList.contains('is-open')) {
            plansModal.classList.remove('is-open');
            unlockPageScroll();
        }
        if (galleryModal && galleryModal.classList.contains('is-open')) {
            if (e.key === 'ArrowLeft') showGalleryImage(activeGalleryIndex - 1);
            if (e.key === 'ArrowRight') showGalleryImage(activeGalleryIndex + 1);
        }
        if (planModal && planModal.classList.contains('is-open')) {
            if (e.key === 'ArrowLeft') showPlanByIndex(activePlanIndex - 1);
            if (e.key === 'ArrowRight') showPlanByIndex(activePlanIndex + 1);
        }
    });

    var inlineForm = document.getElementById('project-new-inline-inquiry-form');
    var inlineSubmit = document.getElementById('project-new-inline-inquiry-submit');
    var inlineMessage = document.getElementById('project-new-inline-inquiry-msg');
    function bindInquiryForm(targetForm, targetSubmit, targetMessage) {
        if (!targetForm || !targetSubmit || !targetMessage) return;
        targetForm.addEventListener('submit', function (e) {
            e.preventDefault();
            targetSubmit.disabled = true;
            targetMessage.className = targetMessage.classList.contains('project-new-git__form-msg')
                ? 'project-new-git__form-msg'
                : 'project-new-inquiry-msg';
            targetMessage.textContent = '';
            var fd = new FormData(targetForm);
            fetch(targetForm.action, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            }).then(function (res) {
                return res.json().catch(function () { return { success: false, message: 'Invalid response.' }; });
            }).then(function (json) {
                if (json && json.success) {
                    targetMessage.className += ' success';
                    targetMessage.textContent = json.message || 'Your request has been sent successfully.';
                    targetForm.reset();
                } else {
                    targetMessage.className += ' error';
                    targetMessage.textContent = (json && json.message) || 'Something went wrong. Please try again.';
                }
            }).catch(function () {
                targetMessage.className += ' error';
                targetMessage.textContent = 'Something went wrong. Please try again.';
            }).finally(function () {
                targetSubmit.disabled = false;
            });
        });
    }
    bindInquiryForm(inlineForm, inlineSubmit, inlineMessage);

    if (!form || !submitBtn || !message) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        submitBtn.disabled = true;
        message.className = 'project-new-inquiry-msg';
        message.textContent = '';
        var fd = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function (res) {
            return res.json().catch(function () { return { success: false, message: 'Invalid response.' }; });
        }).then(function (json) {
            if (json && json.success) {
                message.className = 'project-new-inquiry-msg success';
                message.textContent = json.message || 'Your request has been sent successfully.';
                form.reset();
            } else {
                message.className = 'project-new-inquiry-msg error';
                message.textContent = (json && json.message) || 'Something went wrong. Please try again.';
            }
        }).catch(function () {
            message.className = 'project-new-inquiry-msg error';
            message.textContent = 'Something went wrong. Please try again.';
        }).finally(function () {
            submitBtn.disabled = false;
        });
    });
});
</script>
@endpush
