@extends('layouts.front')
@php
    $phase = $phase ?? null;
    $heroImage = $phase->bannerImageUrl();
    $pageTitle = $phase->meta_title ?: ($phase->title . ' | ' . config('app.name'));
    $cs = \App\Models\ContactSetting::instance();
    $waRaw = trim((string) ($cs->whatsapp ?? '')) ?: trim((string) ($cs->phone ?? ''));
    $waNumber = $waRaw !== '' ? preg_replace('/\D/', '', $waRaw) : '';
    $waText = urlencode('Hi, I am interested in ' . $phase->title);
    $waUrl = $waNumber !== '' ? 'https://wa.me/' . $waNumber . '?text=' . $waText : url('/contact-us');
    $galleryImages = $phase->galleryImages();
    $hasGallery = count($galleryImages) > 0;
@endphp
@section('title', $pageTitle)
@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($phase, [
    'title' => $pageTitle,
    'description' => seo_desc($phase->meta_description ?: strip_tags($phase->description ?? '')),
    'canonical' => $phase->canonical_url ?: url()->current(),
    'image' => $heroImage,
])])
@endpush
@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-lux-hero.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-phase-sections.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-phase-gallery.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/portal-map-section.css') }}?v=2">
@if(!empty($hasPhaseListings))
<link rel="stylesheet" href="{{ asset('theme/css/pages/listing.css') }}">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
@endif
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
<style>
.to_top-btn-wrap {
    bottom: -110px;
    right: 36px;
}
</style>
@endpush
@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <div class="dha-phase-page">
            @include('partials.dha-phase-hero', compact('phase', 'hasGallery', 'hasPhaseListings'))
            @include('partials.dha-phase-sections', compact('phase', 'cs', 'waUrl', 'hasGallery'))
            @include('partials.portal-map-section', [
                'heading' => $phase->map_section_heading,
                'tagline' => $phase->map_section_tagline,
                'imageUrl' => $phase->mapSectionImageUrl(),
                'viewerUrl' => $phase->mapSectionViewerUrl(),
            ])
            @include('partials.dha-phase-gallery', compact('phase', 'galleryImages'))

            @if(!empty($hasPhaseListings))
            <div class="dha-phase-properties-head">
                <div class="container">
                    <h2 class="dha-phase-properties-title">Properties in {{ $phase->title }}</h2>
                </div>
            </div>

            @include('partials.dha-phase-listings', compact('phase', 'projectTypes', 'dhaPhases', 'lahoreCityId'))
            @endif
            </div>

            <div class="container">
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
<div class="map-modal-wrap">
    <div class="map-modal-wrap-overlay"></div>
    <div class="map-modal-item">
        <div class="map-modal-container fl-wrap">
            <h3><span>Property</span></h3>
            <div class="map-modal-close"><i class="fa-regular fa-xmark"></i></div>
            <div class="map-modal fl-wrap">
                <div id="singleMap" data-latitude="40.7" data-longitude="-73.1"></div>
                <div class="scrollContorl"></div>
            </div>
        </div>
    </div>
</div>
<div id="wishlist-toast" aria-live="polite"></div>
@endsection
@push('scripts')
@if(!empty($hasPhaseListings))
@include('partials.listing-page-scripts', [
    'listingPath' => route('dha.phase.show', $phase->slug),
    'defaultDhaPhaseId' => $phase->id,
    'listingResultsLabel' => 'Listings in ' . $phase->title,
    'dhaPhaseUrls' => $dhaPhaseUrls ?? [],
])
@endif
<script src="https://unpkg.com/lucide@latest"></script>
<script src="{{ asset('theme/js/dha-phase-hero.js') }}"></script>
@if($hasGallery)
<script src="{{ asset('theme/js/dha-phase-gallery.js') }}"></script>
@endif
@endpush
