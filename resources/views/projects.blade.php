@extends('layouts.front')
@php
    $cmsPage = $cmsPage ?? null;
    $projectsTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : (($pageHeading ?? 'Our Projects') . ' – ' . config('app.name'));
@endphp

@section('title', $projectsTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $projectsTitle,
    'canonical' => url('/projects'),
])])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/projects.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/portal.css') }}">
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
                                    <h1>{{ $pageHeading ?? 'Our Projects' }}</h1>
                                    <h5>{{ $pageSubheading ?? 'Browse our featured projects.' }}</h5>
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
                            <div class="bg" data-bg="{{ asset('theme/images/bg/12.jpg') }}" data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            --}}

            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/projects') }}">Projects</a>
                    <span>{{ $pageHeading ?? 'Our Projects' }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="main-content projects-listing portal-projects-listing">
                    <div class="boxed-container">
                        <div class="list-main-wrap-header box-list-header">
                            <div class="list-main-wrap-title">
                                <h2>Results: <span>{{ $pageHeading ?? 'Our Projects' }}</span> <strong>{{ $projects->count() }}</strong></h2>
                            </div>
                        </div>

                        @if($projects->isEmpty())
                        <div class="boxed-content-item etihad-empty-state">
                            <p>No projects found.</p>
                            <a href="{{ url('/projects') }}" class="commentssubmit etihad-mt-24">View all projects</a>
                        </div>
                        @else
                        <div class="listing-item-container three-columns-grid etihad-mt-24">
                            @foreach($projects as $project)
                                @include('partials.portal-project-card', ['project' => $project])
                            @endforeach
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
@endsection
