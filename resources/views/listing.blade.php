@extends('layouts.front')
@php
    $cmsPage = $cmsPage ?? null;
    $listingTitle = $cmsPage && $cmsPage->meta_title ? $cmsPage->meta_title : ('Listing – ' . config('app.name'));
    $bannerHeading = $cmsPage && $cmsPage->heading ? $cmsPage->heading : 'Listing';
    $bannerSubtext = $cmsPage && $cmsPage->content ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 120) : 'Browse listings. Use the filters below to find your match.';
    $bannerImage = ($cmsPage && $cmsPage->banner_image) ? url('storage/' . ltrim($cmsPage->banner_image, '/')) : asset('theme/images/bg/12.jpg');
@endphp

@section('title', $listingTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $listingTitle,
    'canonical' => url('/listing'),
    'image' => $bannerImage,
])])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/listing.css') }}">
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
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
            {{-- Hero (hidden — restore when needed)
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h1>{{ $bannerHeading }}</h1>
                                    <h5>{{ $bannerSubtext }}</h5>
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
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="{{ $bannerImage }}" data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            --}}

            <div class="container">
                {{-- Breadcrumbs (hidden — restore when needed)
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a><a href="{{ url('/listing') }}">Listing</a><span>Listings</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>
                --}}

                <div class="main-content ms_vir_height">
                    <div class="boxed-container">
                        @include('partials.listing-search-block', array_merge(compact('projectTypes', 'dhaPhases', 'lahoreCityId'), ['hideListingFilters' => true]))
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

    {{-- Theme panels (wishlist, register, map modal) so JS works --}}
    @include('partials.theme-panels')
</div>

{{-- Map (optional; scripts.js may expect it) --}}
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

<div class="progress-bar-wrap">
    <div class="progress-bar color-bg"></div>
</div>

<div id="wishlist-toast" aria-live="polite"></div>
@endsection

@push('scripts')
@include('partials.listing-page-scripts', ['dhaPhaseUrls' => $dhaPhaseUrls ?? []])
@endpush
