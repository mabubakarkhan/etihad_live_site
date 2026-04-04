@extends('layouts.front')
@php
    $cmsPage = $cmsPage ?? null;
    $listingTitle = $cmsPage && $cmsPage->meta_title ? $cmsPage->meta_title : ('Listing – ' . config('app.name'));
    $bannerHeading = $cmsPage && $cmsPage->heading ? $cmsPage->heading : 'Listing';
    $bannerSubtext = $cmsPage && $cmsPage->content ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 120) : 'Browse listings. Use the filters below to find your match.';
    $bannerImage = ($cmsPage && $cmsPage->banner_image) ? url('storage/' . ltrim($cmsPage->banner_image, '/')) : asset('theme/images/bg/12.jpg');
@endphp

@section('title', $listingTitle)

@if($cmsPage)
@push('meta')
@if(!empty($cmsPage->meta_description))<meta name="description" content="{{ e($cmsPage->meta_description) }}">@endif
@if(!empty($cmsPage->meta_keywords))<meta name="keywords" content="{{ e($cmsPage->meta_keywords) }}">@endif
@if(!empty($cmsPage->canonical_url))<link rel="canonical" href="{{ e($cmsPage->canonical_url) }}">@endif
@endpush
@endif

@push('styles')
<style>
/* Listing grid: 3 columns per row (match html/listing.html) – override theme’s 2-column breakpoint */
.listing-area-wrap .listing-item-container.three-columns-grid,
#listing-grid.three-columns-grid {
    grid-template-columns: repeat(3, 1fr) !important;
}
@media (max-width: 1200px) {
    .listing-area-wrap .listing-item-container.three-columns-grid,
    #listing-grid.three-columns-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}
@media (max-width: 768px) {
    .listing-area-wrap .listing-item-container.three-columns-grid,
    #listing-grid.three-columns-grid {
        grid-template-columns: 1fr !important;
    }
}
/* Container: sidebar + .listing-area-wrap for better management */
.listing-area-container { margin-top: 0; }
.listing-page-row { align-items: stretch; }
@media (min-width: 992px) {
    .listing-page-row .listing-sidebar {
        flex: 0 0 50%;
        max-width: 50%;
    }
    .listing-page-row .listing-main {
        flex: 0 0 50%;
        max-width: 50%;
    }
}
.listing-sidebar {
    position: sticky;
    top: 100px;
    display: flex;
    flex-direction: column;
}
.listing-sidebar-inner {
    flex: 1;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    min-height: 0;
}
.listing-sidebar-map {
    flex: 1;
    width: 100%;
    min-height: 280px;
    background: #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}
.listing-main { display: flex; flex-direction: column; }
.listing-main .listing-area-wrap { flex: 1; }
.listing-main .listing-item-container.three-columns-grid,
.listing-main #listing-grid.three-columns-grid {
    grid-template-columns: repeat(2, 1fr) !important;
}
/* Listing filters: one row on lg+ (address, category, area, more + search) */
.listing-filter-main-row > [class*="col-"] { min-width: 0; }
@media (min-width: 992px) {
    .listing-filter-main-row { flex-wrap: nowrap; align-items: center; }
}
/* Same visual height (56px) + spacing for all filter controls */
.list-searh-input-wrap .listing-filter-main-row .cs-intputwrap {
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.list-searh-input-wrap .listing-filter-main-row .listing-range-dropdown,
.list-searh-input-wrap .listing-filter-main-row .listing-range-dropdown-cswrap {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.list-searh-input-wrap .listing-actions-wrap {
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    justify-content: flex-start;
    gap: 10px;
    height: 56px;
    width: 100%;
    box-sizing: border-box;
}
.list-searh-input-wrap .listing-actions-wrap .listing-more-btn,
.list-searh-input-wrap .listing-actions-wrap .commentssubmit_fw {
    flex: 1 1 0;
    min-width: 0;
    width: auto;
    height: 56px !important;
    min-height: 56px;
    max-height: 56px;
    line-height: 1.2 !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    border-radius: 10px;
}
.list-searh-input-wrap .listing-actions-wrap .listing-more-btn {
    justify-content: flex-start;
    padding-left: 18px;
    padding-right: 44px;
    border: 1px solid #e5e7eb;
    background: #fff;
}
.list-searh-input-wrap .listing-actions-wrap .commentssubmit_fw {
    padding: 0 20px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.04em;
    background: #000 !important;
    color: #fff !important;
    border: none !important;
}
.list-searh-input-wrap .listing-actions-wrap .listing-more-btn i {
    right: 16px;
}
.listing-filter-actions-row {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    justify-content: flex-end;
    gap: 10px;
    width: 100%;
}
@media (max-width: 991px) {
    .listing-filter-actions-row {
        justify-content: stretch;
    }
    .listing-filter-actions-row .commentssubmit_fw {
        flex: 1;
        min-width: 140px;
    }
    .list-searh-input-wrap .listing-actions-wrap {
        height: auto;
        min-height: 56px;
    }
}
@media (max-width: 992px) {
    .listing-main .listing-item-container.three-columns-grid,
    .listing-main #listing-grid.three-columns-grid {
        grid-template-columns: 1fr !important;
    }
}
/* Project type (first row) and purpose (second row) – stacked, no overlap */
.listing-card-cats { position: absolute; left: 30px; top: 30px; z-index: 2; display: flex; flex-direction: column; gap: 8px; }
.listing-card-cats .list-single-opt_header_cat { position: static; display: flex; flex-wrap: wrap; gap: 6px 8px; }
.geodir-category-img .geodir-category-location { left: 20px; }
/* Wishlist heart: original design – white circle, orange heart, no black border, soft gray ring */
.geodir_save-btn.wishlist-btn {
    cursor: pointer;
    border: none;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 8px rgba(255,255,255,0.45), 0 2px 8px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.04);
    transition: box-shadow 0.2s ease;
}
.geodir_save-btn.wishlist-btn:hover {
    box-shadow: 0 0 0 10px rgba(255,255,255,0.5), 0 4px 14px rgba(0,0,0,0.1), 0 0 0 1px rgba(0,0,0,0.06);
}
.geodir_save-btn .wishlist-icon {
    font-family: "Font Awesome 6 Pro";
    font-weight: 400;
    color: #94a3b8;
    transition: transform 0.15s ease, color 0.15s ease;
}
.geodir_save-btn.wishlist-saved .wishlist-icon {
    font-weight: 900;
    color: #EE7838;
}
.geodir_save-btn.wishlist-btn:hover .wishlist-icon {
    transform: scale(1.12);
    color: #94a3b8;
}
.geodir_save-btn.wishlist-btn.wishlist-saved:hover .wishlist-icon {
    color: #EE7838;
}
.geodir_save-btn.wishlist-btn.wishlist-vibrate .wishlist-icon {
    animation: wishlist-vibrate 0.4s ease;
}
@keyframes wishlist-vibrate {
    0%, 100% { transform: scale(1); }
    15% { transform: scale(1.25); }
    30% { transform: scale(0.92); }
    45% { transform: scale(1.1); }
    60% { transform: scale(0.96); }
    75% { transform: scale(1.05); }
}
/* Marker hover card – exact design: orange header, pills, location, price + link (values only, no labels) */
.gm-style-iw .listing-marker-card,
.gm-style-iw-d .listing-marker-card,
.listing-marker-card {
    padding: 0;
    min-width: 260px;
    max-width: 320px;
    width: 100%;
    font-family: 'Poppins', 'Jost', sans-serif;
    box-sizing: border-box;
    word-wrap: break-word;
    overflow-wrap: break-word;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    overflow: hidden;
}
.listing-marker-card-header {
    position: relative;
    padding: 18px 16px 20px;
    background: linear-gradient(135deg, #f5a962 0%, var(--main-color, #EE7838) 50%, #d96a2e 100%);
    border-radius: 12px 12px 0 0;
}
.listing-marker-card-header::before {
    content: '';
    position: absolute;
    top: -8px;
    left: -8px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(255,255,255,0.12);
}
.listing-marker-card-header::after {
    content: '';
    position: absolute;
    top: 4px;
    right: 8px;
    width: 40px;
    height: 40px;
    background-image: radial-gradient(circle, rgba(0,0,0,0.15) 1.5px, transparent 1.5px);
    background-size: 6px 6px;
}
.listing-marker-card-title {
    position: relative;
    z-index: 1;
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    text-align: center;
    line-height: 1.3;
}
.listing-marker-card-body {
    padding: 14px 16px 16px;
    background: #fff;
}
.listing-marker-card-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
}
.listing-marker-card-tag {
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 999px;
    white-space: nowrap;
}
.listing-marker-card-tag.tag-primary {
    background: var(--main-color, #EE7838);
    color: #fff;
    box-shadow: 0 1px 3px rgba(238, 120, 56, 0.35);
}
.listing-marker-card-tag.tag-secondary {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.listing-marker-card-location {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    font-size: 12px;
    color: #334155;
}
.listing-marker-card-location i {
    color: var(--main-color, #EE7838);
    font-size: 14px;
}
.listing-marker-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.listing-marker-card-price {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
}
.listing-marker-card-link {
    font-size: 12px;
    font-weight: 500;
    color: var(--main-color, #EE7838);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.listing-marker-card-link:hover {
    text-decoration: underline;
}
/* Hide InfoWindow close (cross) button and remove container padding */
.gm-style-iw + button,
.gm-style-iw-c button,
button.gm-ui-hover-effect {
    display: none !important;
}
.gm-style-iw-tc::after,
.gm-style-iw .gm-style-iw-c {
    box-shadow: none;
    border-radius: 0;
    overflow: visible;
    padding: 0 !important;
}
.gm-style-iw .gm-style-iw-d {
    overflow: visible;
    padding: 0 !important;
}
/* Map marker hover: vibrate + zoom when hovering a listing card */
#listing-sidebar-map .listing-marker-hover,
.listing-marker-hover {
    animation: listing-marker-hover 0.6s ease;
}
@keyframes listing-marker-hover {
    0%, 100% { transform: scale(1); }
    20% { transform: scale(1.25); }
    40% { transform: scale(0.9); }
    60% { transform: scale(1.15); }
    80% { transform: scale(1.05); }
}
/* Marker highlight when hovering a listing card: color + slightly larger */
.listing-marker-highlight,
#listing-sidebar-map .listing-marker-highlight {
    transform: scale(1.2);
    transition: transform 0.2s ease;
    filter: drop-shadow(0 2px 8px rgba(238, 120, 56, 0.4));
}
[class*="GMAMP"] .listing-marker-highlight,
[class*="gm-style"] .listing-marker-highlight {
    transform: scale(1.2);
}
/* Toast */
#wishlist-toast { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%) translateY(80px); background: #0f172a; color: #fff; padding: 12px 24px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); z-index: 9999; opacity: 0; transition: transform 0.3s ease, opacity 0.3s ease; pointer-events: none; font-size: 14px; }
#wishlist-toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
/* City select: white field, light grey border, orange icon left + orange chevron right (reference) */
.list-searh-input-wrap .cs-intputwrap .ts-wrapper { flex: 1; min-width: 0; }
.list-searh-input-wrap .cs-intputwrap .ts-wrapper .ts-control {
    height: 56px; padding-left: 50px; padding-right: 44px; line-height: 56px; border-radius: 10px;
    border: 1px solid #e5e7eb; background: #fff;
    font-size: 14px; color: #1f2937; text-align: left;
}
.list-searh-input-wrap .cs-intputwrap .ts-wrapper .ts-control:hover {
    border-color: #d1d5db; box-shadow: 0 1px 3px rgba(0,0,0,0.06);
}
.list-searh-input-wrap .cs-intputwrap .ts-wrapper .ts-control::after {
    content: "\f078"; font-family: "Font Awesome 6 Pro"; font-weight: 400;
    color: var(--main-color, #EE7838); position: absolute; right: 18px; top: 50%;
    transform: translateY(-50%); pointer-events: none; font-size: 12px;
}
.list-searh-input-wrap .cs-intputwrap .ts-wrapper .ts-control .ts-dropdown { border-radius: 10px; box-shadow: 0 10px 14px rgba(12,0,46,0.04); }
.list-searh-input-wrap .cs-intputwrap input[type="text"] { padding-left: 50px; text-align: left; }
/* Range sliders: label left, grey track, orange fill/handles, black value bubbles (reference) */
.list-searh-input-wrap .listing-range-wrap {
    padding: 0 20px 14px 110px; position: relative; overflow: visible;
    background: #f9f9f9; border: 1px solid #eee; border-radius: 4px;
}
.list-searh-input-wrap .listing-range-wrap label {
    position: absolute; left: 5px; top: 24px; font-size: 0.9em; color: #666; line-height: 1.2;
    text-align: left; white-space: nowrap;
}
.list-searh-input-wrap .listing-range-wrap .price-rage-item,
.list-searh-input-wrap .listing-range-wrap .irs { margin-top: 1px; max-width: 100%; }
.list-searh-input-wrap .listing-range-wrap .irs-line { background: #eee; border-radius: 4px; }
.list-searh-input-wrap .listing-range-wrap .irs-bar,
.list-searh-input-wrap .listing-range-wrap .irs-slider { background: var(--main-color, #EE7838) !important; }
.list-searh-input-wrap .listing-range-wrap .irs-from,
.list-searh-input-wrap .listing-range-wrap .irs-to { background: #000 !important; color: #fff !important; }
/* Price / area: toggle buttons + dropdown panels */
.listing-range-dropdown { position: relative; width: 100%; }
.listing-range-dropdown-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    height: 56px;
    min-height: 56px;
    box-sizing: border-box;
    padding: 0 14px 0 44px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    text-align: left;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-size: 13px;
    color: #1e1e1e;
}
.listing-range-dropdown-btn:hover { border-color: var(--main-color, #EE7838); }
.listing-range-dropdown-btn[aria-expanded="true"] { border-color: var(--main-color, #EE7838); box-shadow: 0 0 0 2px rgba(238, 120, 56, 0.15); }
.listing-range-dropdown-btn .listing-range-dropdown-title { display: flex; align-items: center; gap: 8px; font-weight: 600; flex: 0 0 auto; }
.listing-range-dropdown-btn .listing-range-dropdown-title i { color: var(--main-color, #EE7838); font-size: 1.1em; }
.listing-range-dropdown-btn .listing-range-dropdown-summary {
    flex: 1 1 auto;
    text-align: right;
    color: #64748b;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.listing-range-dropdown-btn .listing-range-dropdown-caret { flex: 0 0 auto; font-size: 0.75em; color: #94a3b8; transition: transform 0.2s; }
.listing-range-dropdown-btn[aria-expanded="true"] .listing-range-dropdown-caret { transform: rotate(180deg); }
.listing-range-dropdown-panel {
    display: none;
    position: absolute;
    left: 0;
    right: 0;
    top: calc(100% + 6px);
    z-index: 120;
    padding: 16px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 10px;
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.12);
}
.listing-range-dropdown-panel.is-open { display: block; }
.listing-range-dropdown-panel .listing-range-wrap { margin-bottom: 0; }
.listing-range-dropdown-panel .listing-range-wrap label { margin-bottom: 8px; }
.list-searh-input-wrap .listing-range-dropdown .cs-intputwrap { padding: 0; border: none; background: transparent; }
.listing-range-dropdown-cswrap { position: relative; padding: 0 !important; border: none !important; background: transparent !important; }
.listing-range-dropdown-cswrap > i.fa-light {
    position: absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    color: var(--main-color, #EE7838);
    pointer-events: none;
    font-size: 1.1em;
}
/* More Options: icon clearance (heights/flex for actions bar are set above with .listing-filter-main-row) */
.list-searh-input-wrap .listing-more-btn { padding-right: 44px; box-sizing: border-box; }
/* Purpose (Sale/Rent) + refresh: right side like html/listing.html, clear selected state */
.list-searh-input-wrap .list-searh-input-radio_wrap { position: absolute; top: 0; right: 0; left: auto; width: auto; padding-right: 0; display: flex; align-items: stretch; gap: 0; }
.list-searh-input-wrap .list-searh-input-wrap-title { text-align: left; }
.list-searh-input-wrap .list-searh-input-radio_wrap .header-search-radio { flex: 0 0 auto; border: 1px solid #eee; border-right: none; border-radius: 4px 0 0 4px; overflow: hidden; }
.list-searh-input-wrap .list-searh-input-radio_wrap .header-search-radio .button-label {
    width: 100px; background: #f9f9f9; border-right: 1px solid #eee; color: #000;
    font-weight: 600; text-align: center;
}
.list-searh-input-wrap .list-searh-input-radio_wrap .header-search-radio .radio-label:checked + .button-label {
    background: #fff; color: #000; box-shadow: inset 0 -2px 0 0 transparent;
}
.list-searh-input-wrap .list-searh-input-radio_wrap .reset-btn {
    position: relative; right: auto; margin-left: 0;
    border-radius: 0 4px 4px 0; border: 1px solid #eee;
    background: #f9f9f9; height: 42px; width: 42px;
}
.list-searh-input-wrap .list-searh-input-radio_wrap .reset-btn:hover { background: #000; color: var(--main-color); }
/* City dropdown options left-aligned */
.list-searh-input-wrap .ts-wrapper .ts-dropdown .option { text-align: left; }
/* Address suggestions dropdown */
.listing-address-suggestions {
    position: absolute; left: 0; right: 0; top: 100%; margin-top: 4px;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 100;
    max-height: 260px; overflow-y: auto; display: none;
    text-align: left;
}
.listing-address-suggestions.show { display: block; }
.listing-address-suggestions .suggestion-item {
    padding: 10px 14px 12px 50px; cursor: pointer; font-size: 12px; color: #1f2937;
    border-bottom: 1px solid #f1f5f9; transition: background 0.15s;
    text-align: left; line-height: 1.4;
    margin-bottom: 4px;
}
.listing-address-suggestions .suggestion-item:last-child { margin-bottom: 0; border-bottom: none; }
.listing-address-suggestions .suggestion-item:hover,
.listing-address-suggestions .suggestion-item.selected { background: #f8fafc; }
.listing-address-suggestions .suggestion-item .suggestion-match {
    font-weight: 700; color: var(--main-color, #EE7838); background: rgba(238, 120, 56, 0.08);
    padding: 0 1px; border-radius: 2px;
}
.listing-address-suggestions .suggestion-loading {
    padding: 12px 50px; color: #64748b; font-size: 12px; text-align: left;
}
/* Theme hides .more_search-btn globally – show it on listing page */
.list-searh-input-wrap .more_search-btn { display: block !important; }
/* Notifying dot when any More Options filter is in use */
.list-searh-input-wrap .listing-more-btn { position: relative; }
.list-searh-input-wrap .more-options-dot {
    position: absolute; top: 10px; right: 10px; width: 8px; height: 8px;
    border-radius: 50%; background: var(--main-color, #EE7838);
    display: none; pointer-events: none;
}
.list-searh-input-wrap .more-options-dot.active { display: block; }
/* More Options panel: card look (match html/listing) */
.list-searh-input-wrap .hidden-listing-filter {
    margin-top: 16px; padding: 20px; background: #fff; border: 1px solid #eee;
    border-radius: 8px; box-shadow: 0 4px 14px rgba(12,0,46,0.06);
}
.list-searh-input-wrap .hidden-listing-item .filter-tags ul { display: flex; flex-wrap: wrap; gap: 4px 16px; }
.list-searh-input-wrap .hidden-listing-item .filter-tags li { margin: 4px 0; width: auto; }
</style>
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
@endpush

@section('content')
<div id="main">
    @include('partials.header')

    <div class="wrapper">
        <div class="content">
            {{-- Hero --}}
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h2>{{ $bannerHeading }}</h2>
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
                            <div class="svg-corner svg-corner_white" style="bottom:0;right: -39px; transform: rotate(90deg)"></div>
                            <div class="svg-corner svg-corner_white" style="bottom:0;left: -39px;"></div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="{{ $bannerImage }}" data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a><a href="{{ url('/listing') }}">Listing</a><span>Listings</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="main-content">
                    <div class="boxed-container">
                        <div class="show-mob-filter"><i class="far fa-sliders-h"></i> Search listings</div>

                        {{-- Filters --}}
                        <div class="list-searh-input-wrap box_list-searh-input-wrap lws_mobile lsw_mb-btn">
                            <div class="close_mob-filter cmf"><i class="fa-regular fa-xmark"></i></div>
                            <div class="list-searh-input-wrap-title_wrap">
                                <div class="list-searh-input-wrap-title"><i class="far fa-sliders-h"></i><span>Search listings</span></div>
                                <div class="list-searh-input-radio_wrap">
                                    <div class="header-search-radio">
                                        <input class="hidden radio-label" type="radio" name="listing_purpose" id="sale-button2" value="sale" checked="checked">
                                        <label class="button-label" for="sale-button2">Sale</label>
                                        <input class="hidden radio-label" type="radio" name="listing_purpose" id="rent-button2" value="rent">
                                        <label class="button-label" for="rent-button2">Rent</label>
                                    </div>
                                    <div class="reset-form reset-btn tolt" data-microtip-position="bottom" data-tooltip="Reset Filters" id="listing-reset-filters"><i class="fa-solid fa-arrows-rotate"></i></div>
                                </div>
                            </div>
                            <div class="custom-form">
                                <input type="hidden" id="listing-filter-property-type" value="">
                                <input type="hidden" id="listing-default-city-id" value="{{ $lahoreCityId ?? '' }}" data-default="{{ $lahoreCityId ?? '' }}" data-city-name="Lahore">
                                <div class="row g-3 align-items-center listing-filter-main-row">
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <div class="cs-intputwrap listing-address-wrap" style="position: relative;">
                                            <i class="fa-light fa-location-dot"></i>
                                            <input type="text" id="listing-address" name="listing_address" class="listing-filter" placeholder="Address, street, area…" value="" autocomplete="off">
                                            <input type="hidden" id="listing-address-value" name="listing_address_value" value="">
                                            <div id="listing-address-suggestions" class="listing-address-suggestions" role="listbox" aria-hidden="true"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-3">
                                        <div class="cs-intputwrap">
                                            <i class="fa-light fa-layer-group"></i>
                                            <select id="listing-project-type" name="listing_project_type" data-placeholder="All Categories" class="chosen-select on-radius no-search-select listing-filter">
                                                <option value="">All Categories</option>
                                                @foreach($projectTypes ?? [] as $pt)
                                                    <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-2">
                                        <div class="cs-intputwrap listing-range-dropdown-cswrap">
                                            <i class="fa-light fa-ruler-combined" aria-hidden="true"></i>
                                            <div class="listing-range-dropdown">
                                                <button type="button" class="listing-range-dropdown-btn" id="listing-area-range-toggle" aria-expanded="false" aria-controls="listing-area-range-panel">
                                                    <span class="listing-range-dropdown-title">Area</span>
                                                    <span class="listing-range-dropdown-summary" id="listing-area-range-summary"></span>
                                                    <i class="fa-solid fa-chevron-down listing-range-dropdown-caret" aria-hidden="true"></i>
                                                </button>
                                                <div class="listing-range-dropdown-panel" id="listing-area-range-panel" role="region" aria-label="Area range">
                                                    <div class="listing-range-dropdown-panel-inner">
                                                        <div class="price-range-wrap fl-wrap listing-range-wrap">
                                                            <label>Area/Marla</label>
                                                            <div class="price-rage-item pr-nopad fl-wrap">
                                                                <input type="text" id="listing-area-range" class="price-range-double listing-filter listing-deferred-range" data-min="1" data-max="2000" data-from="5" data-to="20" name="area_range" data-step="1" data-prefix="" autocomplete="off">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="hidden-listing_search_wrap fl-wrap listing-actions-wrap listing-filter-actions-row">
                                            <div class="more_search-btn listing-more-btn" id="listing-more-options">More Options <i class="fa-regular fa-plus"></i><span class="more-options-dot" id="more-options-dot" aria-hidden="true"></span></div>
                                            <div class="hidden-listing-filter" style="display: none; width: 100%;">
                                                <div class="quantity_wrap">
                                                    <div class="quantity_wrap_title"><i class="fa-light fa-bed"></i><span>Bedrooms</span></div>
                                                    <div class="quantity">
                                                        <div class="quantity-item">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="text" name="listing_bedrooms" id="listing_bedrooms" title="Qty" class="qty" data-min="0" data-max="20" data-step="1" value="0">
                                                            <input type="button" value="+" class="plus">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="quantity_wrap">
                                                    <div class="quantity_wrap_title"><i class="fa-light fa-bath"></i><span>Bathrooms</span></div>
                                                    <div class="quantity">
                                                        <div class="quantity-item">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="text" name="listing_bathrooms" id="listing_bathrooms" title="Qty" class="qty" data-min="0" data-max="20" data-step="1" value="0">
                                                            <input type="button" value="+" class="plus">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="quantity_wrap">
                                                    <div class="quantity_wrap_title"><i class="fa-light fa-utensils"></i><span>Kitchen</span></div>
                                                    <div class="quantity">
                                                        <div class="quantity-item">
                                                            <input type="button" value="-" class="minus">
                                                            <input type="text" name="listing_kitchen" id="listing_kitchen" title="Qty" class="qty" data-min="0" data-max="20" data-step="1" value="0">
                                                            <input type="button" value="+" class="plus">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="hidden-listing-item">
                                                    <div class="filter-tags-title">Amenities</div>
                                                    <div class="filter-tags">
                                                        <ul class="no-list-style">
                                                            <li><input id="check-aa" type="checkbox" name="listing_amenity[]" value="elevator"><label for="check-aa">Elevator in building</label></li>
                                                            <li><input id="check-b" type="checkbox" name="listing_amenity[]" value="laundry"><label for="check-b">Laundry Room</label></li>
                                                            <li><input id="check-c" type="checkbox" name="listing_amenity[]" value="kitchen"><label for="check-c">Equipped Kitchen</label></li>
                                                            <li><input id="check-d" type="checkbox" name="listing_amenity[]" value="ac"><label for="check-d">Air Conditioned</label></li>
                                                            <li><input id="check-d2" type="checkbox" name="listing_amenity[]" value="parking"><label for="check-d2">Parking</label></li>
                                                            <li><input id="check-d3" type="checkbox" name="listing_amenity[]" value="pool"><label for="check-d3">Swimming Pool</label></li>
                                                            <li><input id="check-d4" type="checkbox" name="listing_amenity[]" value="gym"><label for="check-d4">Fitness Gym</label></li>
                                                            <li><input id="check-d5" type="checkbox" name="listing_amenity[]" value="security"><label for="check-d5">Security</label></li>
                                                            <li><input id="check-d6" type="checkbox" name="listing_amenity[]" value="garage"><label for="check-d6">Garage Attached</label></li>
                                                            <li><input id="check-d7" type="checkbox" name="listing_amenity[]" value="backyard"><label for="check-d7">Back yard</label></li>
                                                            <li><input id="check-d8" type="checkbox" name="listing_amenity[]" value="fireplace"><label for="check-d8">Fireplace</label></li>
                                                            <li><input id="check-d9" type="checkbox" name="listing_amenity[]" value="window_covering"><label for="check-d9">Window Covering</label></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" id="listing-search-btn" class="commentssubmit commentssubmit_fw">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mob-filter-overlay cmf fs-wrapper"></div>

                        {{-- Results header (count updated via JS) --}}
                        <div class="list-main-wrap-header box-list-header">
                            <div class="list-main-wrap-title">
                                <h2>Results: <span id="listing-results-label">Listings</span> <strong id="listing-count">0</strong></h2>
                            </div>
                            <div class="list-main-wrap-opt">
                                <div class="price-opt">
                                    <span class="price-opt-title">Sort by:</span>
                                    <div class="cs-intputwrap" style="margin-bottom: 0">
                                        <i class="fa-light fa-arrow-down-small-big"></i>
                                        <select id="listing-sort" name="listing_sort" data-placeholder="Latest" class="chosen-select no-search-select listing-filter">
                                            <option value="latest" selected>Latest</option>
                                            <option value="price_asc">Price: low to high</option>
                                            <option value="price_desc">Price: high to low</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Container: sidebar + listing area for better management --}}
                        <div class="listing-area-container">
                            <div class="row listing-page-row">
                                <aside class="col-lg-6 col-xl-6 order-2 order-lg-1 listing-sidebar">
                                    <div class="listing-sidebar-inner">
                                        <div id="listing-sidebar-map" class="listing-sidebar-map" aria-label="Map of listed properties"></div>
                                    </div>
                                </aside>
                                <div class="col-lg-6 col-xl-6 order-1 order-lg-2 listing-main">
                                    <div class="listing-area-wrap" style="position: relative; min-height: 320px;">
                                        <div id="listing-loader" class="loader-wrap" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 5; background: rgba(255,255,255,0.85);">
                                            <div class="loader-inner">
                                                <svg>
                                                    <defs>
                                                        <filter id="goo">
                                                            <fegaussianblur in="SourceGraphic" stdDeviation="2" result="blur" />
                                                            <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 5 -2" result="gooey" />
                                                            <fecomposite in="SourceGraphic" in2="gooey" operator="atop"/>
                                                        </filter>
                                                    </defs>
                                                </svg>
                                            </div>
                                        </div>
                                        <div id="listing-empty" style="display: none; padding: 2rem; text-align: center; color: #64748b;">
                                            <p>No listings found.</p>
                                        </div>
                                        <div id="listing-grid" class="listing-item-container three-columns-grid" style="display: none;">
                                            {{-- Filled via AJAX --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="listing-pagination-wrap" class="pagination-wrap" style="display: none;"></div>
                    </div>
                </div>

                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white" style="top:0;left: -40px; transform: rotate(-90deg)"></div>
                    <div class="svg-corner svg-corner_white" style="top:0;right: -40px; transform: rotate(-180deg)"></div>
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
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@php
    $googleMapsKey = config('app.google_maps_api_key') ?: 'AIzaSyDwJSRi0zFjDemECmFl9JtRj1FY7TiTRRo';
@endphp
@php
    $googleMapsMapId = config('app.google_maps_map_id', 'DEMO_MAP_ID');
@endphp
@if($googleMapsKey)
{{-- Async load: callback runs when API is ready. For RefererNotAllowedMapError, add http://localhost/etihad/public/* to key referrers in Google Cloud Console. --}}
<script>
function initMap() {
    if (typeof window._updateListingMapMarkers === 'function' && window._lastListingForMap) {
        window._updateListingMapMarkers(window._lastListingForMap);
    }
}
window.initMap = initMap;
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places,marker&callback=initMap">
</script>
@endif
{{-- Do not load map-single.js on listing: it inits a map in a hidden modal and causes Google Maps errors. Modal open/close handled below. --}}
<script>
(function() {
    var $ = window.jQuery;
    if ($) {
        $(function() {
            $(document).on('click', '.single-map-item', function(e) { e.preventDefault(); $('.map-modal-wrap').fadeIn(400); });
            $(document).on('click', '.map-modal-close, .map-modal-wrap-overlay', function() { $('.map-modal-wrap').fadeOut(400); });
        });
    }
})();
</script>
<script>
(function() {
    function esc(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
    var $loader = document.getElementById('listing-loader');
    var $empty = document.getElementById('listing-empty');
    var $grid = document.getElementById('listing-grid');
    var $countEl = document.getElementById('listing-count');
    var $paginationWrap = document.getElementById('listing-pagination-wrap');
    var detailBaseUrl = '{{ url("/property") }}' + '/';
    var defaultAvatarUrl = '{{ asset("theme/images/avatar/1.jpg") }}';
    var listingPath = '{{ url("/listing") }}';
    var listingMapId = '{{ $googleMapsMapId }}';
    var listingSidebarMap = null;
    var listingSidebarMarkers = [];
    var listingSidebarInfoWindow = null;
    var defaultMapCenter = { lat: 31.5204, lng: 74.3587 };
    var defaultMapZoom = 10;

    function initListingRangeSlidersOnce() {
        if (window.__listingIonRangeInited) return;
        window.__listingIonRangeInited = true;
        if (typeof jQuery === 'undefined' || !jQuery.fn.ionRangeSlider) return;
        var $ = jQuery;
        var $aIn = $('#listing-area-range-panel .listing-range-dropdown-panel-inner');
        if (!$aIn.length) return;
        var $host = $('<div class="listing-ion-temp-host" style="position:absolute;left:-9999px;top:0;width:420px;padding:20px;visibility:hidden;"></div>').appendTo('body');
        $aIn.appendTo($host);
        $('#listing-area-range').ionRangeSlider({
            type: 'double',
            onFinish: function () { $('#listing-area-range').trigger('change'); }
        });
        $('#listing-area-range-panel').append($aIn);
        $host.remove();
    }

    function updateListingRangeSummaries() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;
        var $a = $('#listing-area-range');
        var $aSum = $('#listing-area-range-summary');
        if ($a.length && $aSum.length) {
            if ($a.data('ionRangeSlider')) {
                var ar = $a.data('ionRangeSlider').result;
                if (ar) $aSum.text(ar.from + ' – ' + ar.to + ' Marla');
            } else if ($a.val()) {
                var ap = String($a.val()).split(';');
                if (ap.length >= 2) $aSum.text(ap[0] + ' – ' + ap[1] + ' Marla');
            }
        }
    }

    function bindListingRangeDropdowns() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;
        function closeAll() {
            $('.listing-range-dropdown-panel').removeClass('is-open');
            $('.listing-range-dropdown-btn').attr('aria-expanded', 'false');
        }
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.listing-range-dropdown').length) closeAll();
        });
        $('#listing-area-range-toggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var open = !$('#listing-area-range-panel').hasClass('is-open');
            closeAll();
            if (open) {
                $('#listing-area-range-panel').addClass('is-open');
                $(this).attr('aria-expanded', 'true');
                var inst = $('#listing-area-range').data('ionRangeSlider');
                if (inst) { try { inst.update(); } catch (err) {} }
            }
        });
    }

    function renderListing(properties) {
        if (!$grid) return;
        $grid.innerHTML = '';
        properties.forEach(function(p) {
            var imgStyle = p.featured_image_url ? 'background-image:url(' + esc(p.featured_image_url) + ')' : '';
            var lat = p.latitude || 40.7;
            var lng = p.longitude || -73.1;
            var latLngAttrs = (p.latitude != null && p.longitude != null && !isNaN(parseFloat(p.latitude)) && !isNaN(parseFloat(p.longitude)))
                ? ' data-lat="' + esc(String(p.latitude)) + '" data-lng="' + esc(String(p.longitude)) + '"' : '';
            var card = '<div class="listing-item"' + latLngAttrs + '>' +
                '<div class="geodir-category-listing">' +
                '<div class="geodir-category-img">' +
                '<a href="' + esc(p.detail_url) + '" class="geodir-category-img_item">' +
                '<div class="bg" style="' + imgStyle + '"></div>' +
                '<div class="overlay"></div>' +
                '</a>' +
                (p.short_address ? '<div class="geodir-category-location">' +
                '<a href="#4" class="map-item tolt single-map-item" data-newlatitude="' + lat + '" data-newlongitude="' + lng + '" data-microtip-position="top" data-tooltip="On the map"><i class="fas fa-map-marker-alt"></i> ' + esc(p.short_address) + '</a>' +
                '</div>' : '') +
                '<div class="listing-card-cats">' +
                (Array.isArray(p.project_type_names) && p.project_type_names.length ? '<ul class="list-single-opt_header_cat">' + p.project_type_names.map(function(n){ return '<li><a href="#" class="cat-opt">' + esc(n) + '</a></li>'; }).join('') + '</ul>' : '') +
                (p.purpose_label ? '<ul class="list-single-opt_header_cat list-single-opt_purpose">' + '<li><a href="#" class="cat-opt">' + esc(p.purpose_label) + '</a></li>' + '</ul>' : '') +
                '</div>' +
                '<button type="button" class="geodir_save-btn tolt wishlist-btn" data-property-id="' + esc(String(p.id)) + '" data-microtip-position="left" data-tooltip="Save" aria-label="Save to wishlist"><span><i class="fa-regular fa-heart wishlist-icon"></i></span></button>' +
                '</div>' +
                '<div class="geodir-category-content">' +
                '<h3><a href="' + esc(p.detail_url) + '">' + esc(p.title) + '</a></h3>' +
                '<div class="geodir-category-content_price">' + esc(p.price) + '</div>' +
                (p.description ? '<p>' + esc(p.description) + '</p>' : '') +
                '<div class="geodir-category-content-details"><ul>' +
                '<li><i class="fa-light fa-bed"></i><span>' + (p.bedrooms || 0) + '</span></li>' +
                '<li><i class="fa-light fa-bath"></i><span>' + (p.bathrooms || 0) + '</span></li>' +
                '<li><i class="fa-light fa-utensils"></i><span>' + (p.kitchen || 0) + '</span></li>' +
                '</ul></div>' +
                '</div>' +
                '<div class="geodir-category-footer">' +
                '<span class="gcf-company"><img src="' + esc((p.dealer_image_url && p.dealer_name) ? p.dealer_image_url : defaultAvatarUrl) + '" alt=""><span>By ' + esc((p.dealer_name && p.dealer_name.trim()) ? p.dealer_name : 'Etihad Marketing') + '</span></span>' +
                '<a href="' + esc(p.detail_url) + '" class="gid_link"><span>View Details</span> <i class="fa-solid fa-caret-right"></i></a>' +
                '</div>' +
                '</div></div>';
            $grid.insertAdjacentHTML('beforeend', card);
        });
    }

    function hasMoreOptionsActive() {
        var bedEl = document.getElementById('listing_bedrooms');
        var bathEl = document.getElementById('listing_bathrooms');
        var kitchenEl = document.getElementById('listing_kitchen');
        if (bedEl && bedEl.value !== '' && bedEl.value !== '0') return true;
        if (bathEl && bathEl.value !== '' && bathEl.value !== '0') return true;
        if (kitchenEl && kitchenEl.value !== '' && kitchenEl.value !== '0') return true;
        var checked = document.querySelectorAll('.hidden-listing-filter input[type="checkbox"]:checked');
        return checked && checked.length > 0;
    }
    function updateMoreOptionsDot() {
        var dot = document.getElementById('more-options-dot');
        if (!dot) return;
        if (hasMoreOptionsActive()) dot.classList.add('active');
        else dot.classList.remove('active');
    }
    var addressSuggestionsTimer = null;
    var addressSuggestionsXhr = null;
    function fetchAddressSuggestions(query, callback) {
        if (addressSuggestionsXhr) addressSuggestionsXhr.abort();
        if (!query || query.length < 2) { callback([]); return; }
        var url = '{{ url("/api/listing/address-suggestions") }}?q=' + encodeURIComponent(query);
        addressSuggestionsXhr = new XMLHttpRequest();
        addressSuggestionsXhr.open('GET', url, true);
        addressSuggestionsXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        addressSuggestionsXhr.setRequestHeader('Accept', 'application/json');
        addressSuggestionsXhr.onreadystatechange = function() {
            if (addressSuggestionsXhr.readyState !== 4) return;
            var xhr = addressSuggestionsXhr;
            addressSuggestionsXhr = null;
            var list = [];
            try {
                var data = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                list = data.suggestions || [];
            } catch (e) {}
            callback(list);
        };
        addressSuggestionsXhr.send();
    }
    function escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function escapeRegex(s) {
        return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    function highlightMatch(label, query) {
        if (!label) return '';
        var safe = escapeHtml(label);
        var q = (query && typeof query === 'string') ? query.trim() : '';
        if (!q) return safe;
        var words = q.split(/\s+/).filter(function(w) { return w.length > 0; });
        if (words.length === 0) return safe;
        try {
            var pattern = words.map(function(w) { return escapeRegex(w); }).join('|');
            var re = new RegExp('(' + pattern + ')', 'gi');
            return label.replace(re, function(m) { return '<span class="suggestion-match">' + escapeHtml(m) + '</span>'; });
        } catch (e) {
            return safe;
        }
    }
    function showAddressSuggestions(list, isLoading, searchQuery) {
        var box = document.getElementById('listing-address-suggestions');
        if (!box) return;
        if (isLoading) {
            box.innerHTML = '<div class="suggestion-loading">Searching...</div>';
            box.classList.add('show');
            box.setAttribute('aria-hidden', 'false');
            return;
        }
        if (!list || list.length === 0) {
            box.classList.remove('show');
            box.setAttribute('aria-hidden', 'true');
            box.innerHTML = '';
            return;
        }
        var q = (searchQuery && typeof searchQuery === 'string') ? searchQuery.trim() : '';
        box.innerHTML = list.map(function(item) {
            var raw = item.label || item.value || '';
            var attr = raw.replace(/"/g, '&quot;');
            var content = highlightMatch(raw, q);
            return '<div class="suggestion-item" role="option" data-value="' + attr + '">' + content + '</div>';
        }).join('');
        box.classList.add('show');
        box.setAttribute('aria-hidden', 'false');
    }
    function initAddressAutocomplete() {
        var input = document.getElementById('listing-address');
        var box = document.getElementById('listing-address-suggestions');
        if (!input || !box) return;
        input.addEventListener('input', function() {
            var val = input.value.trim();
            clearTimeout(addressSuggestionsTimer);
            if (val.length < 2) { showAddressSuggestions([]); return; }
            showAddressSuggestions([], true);
            addressSuggestionsTimer = setTimeout(function() {
                fetchAddressSuggestions(val, function(list) { showAddressSuggestions(list, false, val); });
            }, 280);
        });
        input.addEventListener('focus', function() {
            if (box.classList.contains('show') && box.children.length) return;
            var val = input.value.trim();
            if (val.length >= 2) {
                showAddressSuggestions([], true);
                fetchAddressSuggestions(val, function(list) { showAddressSuggestions(list, false, val); });
            }
        });
        input.addEventListener('blur', function() {
            var hidden = document.getElementById('listing-address-value');
            if (hidden) hidden.value = input.value ? input.value.trim() : '';
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { box.classList.remove('show'); box.setAttribute('aria-hidden', 'true'); return; }
            if (!box.classList.contains('show') || !box.children.length) return;
            var items = box.querySelectorAll('.suggestion-item');
            var current = box.querySelector('.suggestion-item.selected');
            var idx = current ? Array.prototype.indexOf.call(items, current) : -1;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                idx = idx < items.length - 1 ? idx + 1 : 0;
                items.forEach(function(el) { el.classList.remove('selected'); });
                if (items[idx]) items[idx].classList.add('selected');
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                idx = idx <= 0 ? items.length - 1 : idx - 1;
                items.forEach(function(el) { el.classList.remove('selected'); });
                if (items[idx]) items[idx].classList.add('selected');
                return;
            }
            if (e.key === 'Enter' && current) {
                e.preventDefault();
                var value = current.getAttribute('data-value');
                if (value) input.value = value;
                showAddressSuggestions([]);
            }
        });
        box.addEventListener('click', function(e) {
            var item = e.target.closest('.suggestion-item');
            if (!item) return;
            var value = item.getAttribute('data-value');
            if (value) {
                input.value = value;
                var hidden = document.getElementById('listing-address-value');
                if (hidden) hidden.value = value;
            }
            showAddressSuggestions([]);
            input.focus();
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.listing-address-wrap')) showAddressSuggestions([]);
        });
    }
    function getFiltersFromForm() {
        var purpose = '';
        var purposeRadio = document.querySelector('input[name="listing_purpose"]:checked');
        if (purposeRadio) purpose = purposeRadio.value || '';
        var projectTypeEl = document.getElementById('listing-project-type');
        var projectType = (projectTypeEl && projectTypeEl.value) ? projectTypeEl.value : '';
        var city = '';
        var cityHiddenEl = document.getElementById('listing-default-city-id');
        if (cityHiddenEl && cityHiddenEl.value) city = String(cityHiddenEl.value);
        else if (window.listingCitySelect && window.listingCitySelect.getValue) city = window.listingCitySelect.getValue() || '';
        var marlaMin = '', marlaMax = '';
        var sortEl = document.getElementById('listing-sort');
        var sort = 'latest';
        if (sortEl) {
            var opt = sortEl.options[sortEl.selectedIndex];
            sort = (opt && opt.value) ? opt.value : (sortEl.value || 'latest');
        }
        var addressEl = document.getElementById('listing-address');
        var addressValueEl = document.getElementById('listing-address-value');
        var address = (addressEl && addressEl.value) ? addressEl.value.trim() : '';
        if (!address && addressValueEl && addressValueEl.value) address = addressValueEl.value.trim();
        var bedrooms = '', bathrooms = '', kitchen = '';
        var bedEl = document.getElementById('listing_bedrooms');
        var bathEl = document.getElementById('listing_bathrooms');
        var kitchenEl = document.getElementById('listing_kitchen');
        if (bedEl && bedEl.value !== '' && bedEl.value !== '0') bedrooms = bedEl.value;
        if (bathEl && bathEl.value !== '' && bathEl.value !== '0') bathrooms = bathEl.value;
        if (kitchenEl && kitchenEl.value !== '' && kitchenEl.value !== '0') kitchen = kitchenEl.value;
        if (typeof $ !== 'undefined') {
            var $marla = $('#listing-area-range');
            if ($marla.length && $marla.data('ionRangeSlider')) {
                var mr = $marla.data('ionRangeSlider').result;
                if (mr) { marlaMin = mr.from; marlaMax = mr.to; }
            }
        }
        if ((marlaMin === '' || marlaMax === '') && typeof $ !== 'undefined') {
            var $marlaIn = $('#listing-area-range');
            if ($marlaIn.length && $marlaIn.val()) {
                var mparts = String($marlaIn.val()).split(';');
                if (mparts.length >= 2) {
                    var m0 = parseFloat(mparts[0]), m1 = parseFloat(mparts[1]);
                    if (!isNaN(m0)) marlaMin = m0;
                    if (!isNaN(m1)) marlaMax = m1;
                }
            }
        }
        return { purpose: purpose, project_type: projectType, city: city, address: address, marla_min: marlaMin, marla_max: marlaMax, bedrooms: bedrooms, bathrooms: bathrooms, kitchen: kitchen, sort: sort };
    }

    function buildQueryString(filters) {
        var q = [];
        if (filters.purpose) q.push('purpose=' + encodeURIComponent(filters.purpose));
        if (filters.project_type) q.push('project_type=' + encodeURIComponent(filters.project_type));
        if (filters.city) q.push('city=' + encodeURIComponent(filters.city));
        if (filters.address) q.push('address=' + encodeURIComponent(filters.address));
        if (filters.marla_min !== '' && filters.marla_min != null) q.push('marla_min=' + encodeURIComponent(filters.marla_min));
        if (filters.marla_max !== '' && filters.marla_max != null) q.push('marla_max=' + encodeURIComponent(filters.marla_max));
        if (filters.bedrooms) q.push('bedrooms=' + encodeURIComponent(filters.bedrooms));
        if (filters.bathrooms) q.push('bathrooms=' + encodeURIComponent(filters.bathrooms));
        if (filters.kitchen) q.push('kitchen=' + encodeURIComponent(filters.kitchen));
        q.push('sort=' + encodeURIComponent(filters.sort || 'latest'));
        return '?' + q.join('&');
    }

    function parseUrlParams() {
        var params = {};
        var search = window.location.search;
        if (!search) return params;
        search.slice(1).split('&').forEach(function(pair) {
            var i = pair.indexOf('=');
            var k = i >= 0 ? decodeURIComponent(pair.slice(0, i)) : pair;
            var v = i >= 0 ? decodeURIComponent(pair.slice(i + 1)) : '';
            params[k] = v;
        });
        return params;
    }

    function applyFiltersFromUrl() {
        var params = parseUrlParams();
        var purpose = params.purpose || 'sale';
        if (purpose === 'rent' || purpose === 'sale') {
            var radio = document.querySelector('input[name="listing_purpose"][value="' + purpose + '"]');
            if (radio) radio.checked = true;
        }
        var projectTypeEl = document.getElementById('listing-project-type');
        if (projectTypeEl && params.project_type) {
            projectTypeEl.value = params.project_type;
            if (typeof $ !== 'undefined') $(projectTypeEl).niceSelect('update');
        }
        var cityHiddenApply = document.getElementById('listing-default-city-id');
        if (cityHiddenApply) {
            if (params.city) {
                cityHiddenApply.value = params.city;
            } else {
                var defCity = cityHiddenApply.getAttribute('data-default');
                if (defCity !== null && defCity !== '') cityHiddenApply.value = defCity;
            }
        } else if (window.listingCitySelect && params.city) {
            window.listingCitySelect.setValue(params.city);
        }
        var addressEl = document.getElementById('listing-address');
        var addressValueEl = document.getElementById('listing-address-value');
        if (params.address) {
            if (addressEl) addressEl.value = params.address;
            if (addressValueEl) addressValueEl.value = params.address;
        }
        if (typeof $ !== 'undefined') {
            var $marla = $('#listing-area-range');
            if ($marla.length && $marla.data('ionRangeSlider')) {
                var marlaMin = params.marla_min ? parseFloat(params.marla_min) : 5;
                var marlaMax = params.marla_max ? parseFloat(params.marla_max) : 20;
                $marla.data('ionRangeSlider').update({ from: marlaMin, to: marlaMax });
            }
            updateListingRangeSummaries();
        }
        var sortEl = document.getElementById('listing-sort');
        if (sortEl && params.sort) {
            sortEl.value = params.sort;
            if (typeof $ !== 'undefined') $(sortEl).niceSelect('update');
        }
        var bedEl = document.getElementById('listing_bedrooms');
        if (bedEl && params.bedrooms !== undefined) bedEl.value = params.bedrooms;
        var bathEl = document.getElementById('listing_bathrooms');
        if (bathEl && params.bathrooms !== undefined) bathEl.value = params.bathrooms;
        var kitchenEl = document.getElementById('listing_kitchen');
        if (kitchenEl && params.kitchen !== undefined) kitchenEl.value = params.kitchen;
    }

    function loadListings() {
        if (!$loader || !$grid) return;
        var filters = getFiltersFromForm();
        var baseUrl = '{{ url("/api/listing/dealers") }}';
        var q = [];
        if (filters.purpose) q.push('purpose=' + encodeURIComponent(filters.purpose));
        if (filters.project_type) q.push('project_type=' + encodeURIComponent(filters.project_type));
        if (filters.city) q.push('city=' + encodeURIComponent(filters.city));
        if (filters.address) q.push('address=' + encodeURIComponent(filters.address));
        if (filters.marla_min !== '' && filters.marla_min != null) q.push('marla_min=' + encodeURIComponent(filters.marla_min));
        if (filters.marla_max !== '' && filters.marla_max != null) q.push('marla_max=' + encodeURIComponent(filters.marla_max));
        if (filters.bedrooms) q.push('bedrooms=' + encodeURIComponent(filters.bedrooms));
        if (filters.bathrooms) q.push('bathrooms=' + encodeURIComponent(filters.bathrooms));
        if (filters.kitchen) q.push('kitchen=' + encodeURIComponent(filters.kitchen));
        q.push('sort=' + encodeURIComponent(filters.sort || 'latest'));
        var url = baseUrl + '?' + q.join('&');
        $loader.style.display = 'flex';
        $grid.style.display = 'none';
        if ($empty) $empty.style.display = 'none';
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            if ($loader) $loader.style.display = 'none';
            var data;
            try {
                data = xhr.responseText ? JSON.parse(xhr.responseText) : {};
            } catch (e) {
                data = { properties: [], count: 0 };
            }
            var list = data.properties || [];
            var count = data.count != null ? data.count : list.length;
            if ($countEl) $countEl.textContent = count;
            if (list.length === 0) {
                if ($empty) $empty.style.display = 'block';
            } else {
                if ($empty) $empty.style.display = 'none';
                $grid.style.display = '';
                renderListing(list);
                if (window.__etihadWishlistSyncUi) {
                    try { window.__etihadWishlistSyncUi(); } catch (e) {}
                }
            }
            updateListingMapMarkers(list);
            updateMoreOptionsDot();
        };
        xhr.send();
    }

    function showMapError(msg) {
        var mapEl = document.getElementById('listing-sidebar-map');
        if (!mapEl) return;
        mapEl.innerHTML = '<div class="listing-map-error" style="display:flex;align-items:center;justify-content:center;height:100%;padding:16px;text-align:center;color:#64748b;font-size:13px;line-height:1.5;">' + (msg || 'Map unavailable.') + '</div>';
    }
    function updateListingMapMarkers(properties) {
        window._lastListingForMap = properties || [];
        window._updateListingMapMarkers = updateListingMapMarkers;
        var mapEl = document.getElementById('listing-sidebar-map');
        if (!mapEl) return;
        if (typeof google === 'undefined' || !google.maps) return;
        if (mapEl.offsetWidth <= 0 || mapEl.offsetHeight <= 0) {
            clearTimeout(window._listingMapDeferred);
            window._listingMapDeferred = setTimeout(function() {
                window._listingMapDeferred = null;
                updateListingMapMarkers(window._lastListingForMap);
            }, 200);
            return;
        }
        try {
            if (!listingSidebarMap) {
                var mapOpts = {
                    zoom: defaultMapZoom,
                    center: defaultMapCenter,
                    scrollwheel: false,
                    mapTypeControl: true,
                    streetViewControl: false,
                    fullscreenControl: true,
                    styles: [
                        { featureType: 'poi', stylers: [{ visibility: 'off' }] },
                        { featureType: 'transit', stylers: [{ visibility: 'off' }] },
                        { featureType: 'landscape', stylers: [{ visibility: 'off' }] },
                        { featureType: 'water', stylers: [{ visibility: 'off' }] },
                        { featureType: 'administrative', stylers: [{ visibility: 'off' }] }
                    ]
                };
                if (listingMapId) mapOpts.mapId = listingMapId;
                listingSidebarMap = new google.maps.Map(mapEl, mapOpts);
                listingSidebarInfoWindow = new google.maps.InfoWindow();
            }
            listingSidebarMarkers.forEach(function(m) {
                if (m.setMap) m.setMap(null);
                else if (m.map !== undefined) m.map = null;
            });
            listingSidebarMarkers = [];
            var bounds = null;
            function buildMarkerInfoContent(p) {
                if (!p) return '<div class="listing-marker-card"><div class="listing-marker-card-header"><h2 class="listing-marker-card-title">Property</h2></div></div>';
                var parts = [p.address || p.short_address || '', p.town || '', p.city || '', p.state || ''].filter(function(s) { return s && String(s).trim(); });
                var fullAddress = parts.length ? parts.join(', ') : (p.short_address || '');
                var projectTypes = Array.isArray(p.project_type_names) && p.project_type_names.length ? p.project_type_names : [];
                var tagPurpose = p.purpose_label ? '<span class="listing-marker-card-tag tag-primary">' + esc(p.purpose_label) + '</span>' : '';
                var tagProject = projectTypes.length ? '<span class="listing-marker-card-tag tag-secondary">' + esc(projectTypes.join(', ')) + '</span>' : '';
                var tagsHtml = (tagPurpose || tagProject) ? '<div class="listing-marker-card-tags">' + tagPurpose + tagProject + '</div>' : '';
                var locationHtml = fullAddress ? '<div class="listing-marker-card-location"><i class="fa-solid fa-location-dot"></i><span>' + esc(fullAddress) + '</span></div>' : '';
                var priceHtml = p.price ? '<span class="listing-marker-card-price">' + esc(p.price) + '</span>' : '';
                var linkHtml = p.detail_url ? '<a href="' + esc(p.detail_url) + '" class="listing-marker-card-link">View details <i class="fa-solid fa-arrow-right"></i></a>' : '';
                var footerHtml = (priceHtml || linkHtml) ? '<div class="listing-marker-card-footer">' + priceHtml + linkHtml + '</div>' : '';
                return '<div class="listing-marker-card">' +
                    '<div class="listing-marker-card-header"><h2 class="listing-marker-card-title">' + esc(p.title || 'Property') + '</h2></div>' +
                    '<div class="listing-marker-card-body">' + tagsHtml + locationHtml + footerHtml + '</div></div>';
            }
            window._listingBuildMarkerInfoContent = buildMarkerInfoContent;
            var useAdvancedMarker = typeof google.maps.marker !== 'undefined' && google.maps.marker.AdvancedMarkerElement;
            (properties || []).forEach(function(p) {
                var lat = parseFloat(p.latitude);
                var lng = parseFloat(p.longitude);
                if (isNaN(lat) || isNaN(lng)) return;
                var pos = { lat: lat, lng: lng };
                var marker;
                if (useAdvancedMarker) {
                    marker = new google.maps.marker.AdvancedMarkerElement({
                        map: listingSidebarMap,
                        position: pos,
                        title: p.title || ''
                    });
                } else {
                    marker = new google.maps.Marker({
                        position: pos,
                        map: listingSidebarMap,
                        title: p.title || ''
                    });
                }
                marker._property = p;
                var markerPos = pos;
                function openMarkerInfo() {
                    if (!listingSidebarInfoWindow || !marker._property) return;
                    listingSidebarInfoWindow.setContent(buildMarkerInfoContent(marker._property));
                    if (marker.getPosition && typeof marker.getPosition === 'function') {
                        listingSidebarInfoWindow.open(listingSidebarMap, marker);
                    } else {
                        listingSidebarInfoWindow.setPosition(markerPos);
                        listingSidebarInfoWindow.open(listingSidebarMap);
                    }
                }
                var markerInfoCloseTimer = null;
                function closeMarkerInfo() {
                    if (markerInfoCloseTimer) clearTimeout(markerInfoCloseTimer);
                    markerInfoCloseTimer = setTimeout(function() {
                        markerInfoCloseTimer = null;
                        if (listingSidebarInfoWindow) listingSidebarInfoWindow.close();
                    }, 150);
                }
                function cancelCloseMarkerInfo() {
                    if (markerInfoCloseTimer) {
                        clearTimeout(markerInfoCloseTimer);
                        markerInfoCloseTimer = null;
                    }
                }
                if (marker.addListener) {
                    marker.addListener('mouseover', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.addListener('mouseout', closeMarkerInfo);
                }
                if (marker.content && typeof marker.content.addEventListener === 'function') {
                    marker.content.addEventListener('mouseenter', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.content.addEventListener('mouseleave', closeMarkerInfo);
                } else if (marker.addEventListener) {
                    marker.addEventListener('mouseenter', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.addEventListener('mouseleave', closeMarkerInfo);
                    marker.addEventListener('mouseover', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.addEventListener('mouseout', closeMarkerInfo);
                }
                if (marker.addListener) {
                    marker.addListener('click', function() {
                        cancelCloseMarkerInfo();
                        openMarkerInfo();
                    });
                }
                if (marker.addEventListener) {
                    marker.addEventListener('click', function() {
                        cancelCloseMarkerInfo();
                        openMarkerInfo();
                    });
                }
                if (marker.content && marker.content.addEventListener) {
                    marker.content.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        cancelCloseMarkerInfo();
                        openMarkerInfo();
                    });
                }
                listingSidebarMarkers.push(marker);
                if (!bounds) bounds = new google.maps.LatLngBounds();
                bounds.extend(pos);
            });
            if (bounds && listingSidebarMarkers.length > 0) {
                listingSidebarMap.fitBounds(bounds, { top: 20, right: 20, bottom: 20, left: 20 });
            } else {
                listingSidebarMap.setCenter(defaultMapCenter);
                listingSidebarMap.setZoom(defaultMapZoom);
            }
            try { google.maps.event.trigger(listingSidebarMap, 'resize'); } catch (e) {}
        } catch (err) {
            var isReferrer = (err && (err.message || '').indexOf('RefererNotAllowed') !== -1);
            showMapError(isReferrer
                ? 'Map could not load. In Google Cloud Console, add this URL to your API key\u2019s allowed referrers: ' + (window.location.origin || '') + '/*'
                : 'Map could not load. Check the console for details.');
        }
    }
    window._updateListingMapMarkers = updateListingMapMarkers;

    var listingCardHoverBounceTimer = null;
    var listingHighlightedMarker = null;
    function initListingCardMapHover() {
        var grid = document.getElementById('listing-grid');
        if (!grid) return;
        grid.removeEventListener('mouseover', _onListingCardMouseOver);
        grid.removeEventListener('mouseout', _onListingCardMouseOut);
        grid.addEventListener('mouseover', _onListingCardMouseOver);
        grid.addEventListener('mouseout', _onListingCardMouseOut);
    }
    function _onListingCardMouseOver(e) {
        var card = e.target && e.target.closest ? e.target.closest('.listing-item') : null;
        if (!card) return;
        var lat = card.getAttribute('data-lat');
        var lng = card.getAttribute('data-lng');
        if (lat === null || lat === '' || lng === null || lng === '') return;
        var latN = parseFloat(lat);
        var lngN = parseFloat(lng);
        if (isNaN(latN) || isNaN(lngN)) return;
        if (!listingSidebarMap || !listingSidebarMarkers.length) return;
        var pos = { lat: latN, lng: lngN };
        var marker = null;
        function getLatLng(p) {
            if (!p) return null;
            var la = typeof p.lat === 'function' ? p.lat() : p.lat;
            var ln = typeof p.lng === 'function' ? p.lng() : p.lng;
            return (la != null && ln != null) ? { lat: Number(la), lng: Number(ln) } : null;
        }
        for (var i = 0; i < listingSidebarMarkers.length; i++) {
            var m = listingSidebarMarkers[i];
            var p = getLatLng(m.getPosition ? m.getPosition() : m.position);
            if (p && Math.abs(p.lat - latN) < 1e-5 && Math.abs(p.lng - lngN) < 1e-5) {
                marker = m;
                break;
            }
        }
        if (!marker) return;
        if (listingCardHoverBounceTimer) clearTimeout(listingCardHoverBounceTimer);
        if (listingHighlightedMarker) {
            if (listingHighlightedMarker.content) listingHighlightedMarker.content.classList.remove('listing-marker-highlight');
            if (listingHighlightedMarker.setZIndex) listingHighlightedMarker.setZIndex(null);
        }
        listingHighlightedMarker = marker;
        if (marker.content) marker.content.classList.add('listing-marker-highlight');
        if (marker.setZIndex) marker.setZIndex(999);
        listingSidebarMap.panTo(pos);
        if (listingSidebarInfoWindow && marker._property && typeof window._listingBuildMarkerInfoContent === 'function') {
            listingSidebarInfoWindow.setContent(window._listingBuildMarkerInfoContent(marker._property));
            if (marker.getPosition && typeof marker.getPosition === 'function') {
                listingSidebarInfoWindow.open(listingSidebarMap, marker);
            } else {
                listingSidebarInfoWindow.setPosition(pos);
                listingSidebarInfoWindow.open(listingSidebarMap);
            }
        }
    }
    function _onListingCardMouseOut(e) {
        var card = e.target && e.target.closest ? e.target.closest('.listing-item') : null;
        if (!card) return;
        var grid = document.getElementById('listing-grid');
        var related = e.relatedTarget;
        if (grid && related && grid.contains(related) && related.closest && related.closest('.listing-item')) return;
        if (listingCardHoverBounceTimer) {
            clearTimeout(listingCardHoverBounceTimer);
            listingCardHoverBounceTimer = null;
        }
        if (listingHighlightedMarker) {
            if (listingHighlightedMarker.content) listingHighlightedMarker.content.classList.remove('listing-marker-highlight');
            if (listingHighlightedMarker.setZIndex) listingHighlightedMarker.setZIndex(null);
            listingHighlightedMarker = null;
        }
        if (listingSidebarInfoWindow) listingSidebarInfoWindow.close();
    }

    function updateListingUrl() {
        var filters = getFiltersFromForm();
        var qs = buildQueryString(filters);
        var path = listingPath;
        var newUrl = qs ? path + qs : path.replace(/\?.*$/, '');
        if (window.history && window.history.replaceState) {
            window.history.replaceState({ listingFilters: filters }, '', newUrl);
        }
    }

    function onSearchClick() {
        var filters = getFiltersFromForm();
        var qs = buildQueryString(filters);
        var path = listingPath;
        var newUrl = qs ? path + qs : path;
        if (window.history && window.history.pushState) {
            window.history.pushState({ listingFilters: filters }, '', newUrl);
        }
        updateMoreOptionsDot();
        loadListings();
    }

    function onResetClick() {
        var saleRadio = document.querySelector('input[name="listing_purpose"][value="sale"]');
        if (saleRadio) saleRadio.checked = true;
        var addressEl = document.getElementById('listing-address');
        var addressValueEl = document.getElementById('listing-address-value');
        if (addressEl) addressEl.value = '';
        if (addressValueEl) addressValueEl.value = '';
        var projectTypeEl = document.getElementById('listing-project-type');
        if (projectTypeEl) {
            projectTypeEl.value = '';
            if (typeof $ !== 'undefined') $(projectTypeEl).niceSelect('update');
        }
        var cityHiddenReset = document.getElementById('listing-default-city-id');
        if (cityHiddenReset) {
            var defCity = cityHiddenReset.getAttribute('data-default');
            if (defCity !== null && defCity !== '') cityHiddenReset.value = defCity;
        } else if (window.listingCitySelect) window.listingCitySelect.clear();
        if (typeof $ !== 'undefined') {
            var $marla = $('#listing-area-range');
            if ($marla.length && $marla.data('ionRangeSlider')) {
                $marla.data('ionRangeSlider').update({ from: 5, to: 20 });
            }
            updateListingRangeSummaries();
        }
        var sortEl = document.getElementById('listing-sort');
        if (sortEl) { sortEl.value = 'latest'; if (typeof $ !== 'undefined') $(sortEl).niceSelect('update'); }
        var bedEl = document.getElementById('listing_bedrooms');
        if (bedEl) bedEl.value = '0';
        var bathEl = document.getElementById('listing_bathrooms');
        if (bathEl) bathEl.value = '0';
        var kitchenEl = document.getElementById('listing_kitchen');
        if (kitchenEl) kitchenEl.value = '0';
        document.querySelectorAll('.hidden-listing-filter input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });
        updateMoreOptionsDot();
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', listingPath);
        }
        loadListings();
    }

    function initListingFilters() {
        var cityEl = document.getElementById('listing-city');
        if (cityEl && typeof TomSelect !== 'undefined') {
            window.listingCitySelect = new TomSelect(cityEl, {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                placeholder: 'All Cities',
                maxOptions: null
            });
        }
        initListingRangeSlidersOnce();
        if (typeof jQuery !== 'undefined') bindListingRangeDropdowns();
        applyFiltersFromUrl();
        if (typeof jQuery !== 'undefined') updateListingRangeSummaries();
        updateMoreOptionsDot();
        initAddressAutocomplete();
        initListingCardMapHover();
        loadListings();
        var searchBtn = document.getElementById('listing-search-btn');
        if (searchBtn) searchBtn.addEventListener('click', function(e) { e.preventDefault(); onSearchClick(); });
        document.querySelectorAll('input[name="listing_purpose"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var filters = getFiltersFromForm();
                var qs = buildQueryString(filters);
                var path = listingPath;
                var newUrl = qs ? path + qs : path;
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({ listingFilters: filters }, '', newUrl);
                }
                loadListings();
            });
        });
        var resetBtn = document.getElementById('listing-reset-filters');
        if (resetBtn) resetBtn.addEventListener('click', function(e) { e.preventDefault(); onResetClick(); });
        function bindMoreOptionsDotUpdates() {
            [ 'listing_bedrooms', 'listing_bathrooms', 'listing_kitchen' ].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) { el.addEventListener('change', updateMoreOptionsDot); el.addEventListener('input', updateMoreOptionsDot); }
            });
            document.querySelectorAll('.hidden-listing-filter input[type="checkbox"]').forEach(function(cb) {
                cb.addEventListener('change', updateMoreOptionsDot);
            });
        }
        bindMoreOptionsDotUpdates();
        (function initSortFilter() {
            function applySort() {
                var filters = getFiltersFromForm();
                var qs = buildQueryString(filters);
                var path = listingPath;
                var newUrl = qs ? path + qs : path;
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({ listingFilters: filters }, '', newUrl);
                }
                loadListings();
            }
            if (typeof $ !== 'undefined') {
                $(document).on('change', '#listing-sort', applySort);
            } else {
                var sortEl = document.getElementById('listing-sort');
                if (sortEl) sortEl.addEventListener('change', applySort);
            }
        })();
        (function initAreaRangeFilter() {
            var areaRangeTimer = null;
            function applyAreaRange() {
                var filters = getFiltersFromForm();
                var qs = buildQueryString(filters);
                var path = listingPath;
                var newUrl = qs ? path + qs : path;
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({ listingFilters: filters }, '', newUrl);
                }
                loadListings();
            }
            function onAreaRangeChange() {
                if (typeof jQuery !== 'undefined') updateListingRangeSummaries();
                clearTimeout(areaRangeTimer);
                areaRangeTimer = setTimeout(applyAreaRange, 400);
            }
            if (typeof $ !== 'undefined') {
                $(document).on('change input', '#listing-area-range', onAreaRangeChange);
            }
        })();
        window.addEventListener('popstate', function() {
            applyFiltersFromUrl();
            loadListings();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initListingFilters);
    } else {
        initListingFilters();
    }
})();
</script>
@endpush
