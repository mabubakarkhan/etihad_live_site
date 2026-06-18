@extends('layouts.front')
@php
    $dha = $dha ?? \App\Models\DhaSetting::instance();
    $phases = $phases ?? collect();
    $heroImage = $dha->heroVisualUrl();
    $phasesHead = $dha->phasesHeading();
    $pageTitle = $dha->meta_title ?: ($dha->title . ' | ' . config('app.name'));
@endphp
@section('title', $pageTitle)
@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($dha, [
    'title' => $pageTitle,
    'description' => seo_desc($dha->meta_description ?: strip_tags($dha->content ?? '')),
    'canonical' => $dha->canonical_url ?: url()->current(),
    'image' => $heroImage,
])])
@endpush
@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-main-hero.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-phases-lux.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-main-sections.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-main-layout.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
@endpush
@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <div class="dha-main-page">
            <div class="dha-main-lux-flow">
                @include('partials.dha-main-hero', ['dha' => $dha])
                <section class="dha-phases-lux-section" id="dha-phases">
                    @include('partials.dha-main-stats-strip', ['dha' => $dha])
                    <div class="container dha-phases-lux-section__body">
                        <header class="dha-phases-lux-head">
                            <div class="dha-phases-lux-head__center">
                                <p class="dha-phases-lux-head__eyebrow">{{ $phasesHead['eyebrow'] }}</p>
                                <div class="dha-phases-lux-head__title-row">
                                    <span class="dha-phases-lux-head__line dha-phases-lux-head__line--left" aria-hidden="true"></span>
                                    <h2 class="dha-phases-lux-head__title">
                                        <span class="dha-phases-lux-head__title-gold">{{ $phasesHead['gold'] }}</span>
                                        <span class="dha-phases-lux-head__title-white">{{ $phasesHead['white'] }}</span>
                                    </h2>
                                    <span class="dha-phases-lux-head__line dha-phases-lux-head__line--right" aria-hidden="true"></span>
                                </div>
                            </div>
                        </header>
                        <div class="dha-phase-lux-grid-wrap">
                            @include('partials.dha-phase-lux-grid', ['phases' => $phases])
                        </div>
                    </div>
                </section>
                @include('partials.dha-main-sections', ['dha' => $dha])
            </div>
            </div>

            <div class="container dha-main-page-footer-tab">
                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white hero-corner-tl"></div>
                    <div class="svg-corner svg-corner_white hero-corner-tr"></div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</div>
@endsection
@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script src="{{ asset('theme/js/dha-phase-hero.js') }}"></script>
@endpush
