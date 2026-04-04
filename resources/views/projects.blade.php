@extends('layouts.front')
@php
    $cmsPage = $cmsPage ?? null;
    $projectsTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : (($pageHeading ?? 'Our Projects') . ' – ' . config('app.name'));
@endphp

@section('title', $projectsTitle)

@if($cmsPage)
@push('meta')
@if(!empty($cmsPage->meta_description))<meta name="description" content="{{ e($cmsPage->meta_description) }}">@endif
@if(!empty($cmsPage->meta_keywords))<meta name="keywords" content="{{ e($cmsPage->meta_keywords) }}">@endif
@if(!empty($cmsPage->canonical_url))<link rel="canonical" href="{{ e($cmsPage->canonical_url) }}">@endif
@endpush
@endif

@push('styles')
<style>
.projects-listing .listing-item-container.three-columns-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
@media (max-width: 1200px) {
    .projects-listing .listing-item-container.three-columns-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .projects-listing .listing-item-container.three-columns-grid { grid-template-columns: 1fr; }
}
.projects-listing .geodir-category-img .bg { background-size: cover; background-position: center; }
/* Project card footer: align company name (left) and View Details (right) */
.projects-listing .geodir-category-footer { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
.projects-listing .geodir-category-footer .gcf-company { float: none; }
.projects-listing .geodir-category-footer .gid_link { position: relative; bottom: auto; right: auto; flex-shrink: 0; }
</style>
@endpush

@section('content')
<div id="main">
    @include('partials.header')

    <div class="wrapper">
        <div class="content">
            {{-- Hero (listing.html style) --}}
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h2>{{ $pageHeading ?? 'Our Projects' }}</h2>
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
                            <div class="svg-corner svg-corner_white" style="bottom:0;right: -39px; transform: rotate(90deg)"></div>
                            <div class="svg-corner svg-corner_white" style="bottom:0;left: -39px;"></div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="{{ asset('theme/images/bg/12.jpg') }}" data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/projects') }}">Projects</a>
                    <span>{{ $pageHeading ?? 'Our Projects' }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="main-content projects-listing">
                    <div class="boxed-container">
                        <div class="list-main-wrap-header box-list-header">
                            <div class="list-main-wrap-title">
                                <h2>Results: <span>{{ $pageHeading ?? 'Our Projects' }}</span> <strong>{{ $projects->count() }}</strong></h2>
                            </div>
                        </div>

                        @if($projects->isEmpty())
                        <div class="boxed-content-item" style="padding: 48px 24px; text-align: center;">
                            <p style="margin: 0; color: #64748b;">No projects found.</p>
                            <a href="{{ url('/projects') }}" class="commentssubmit" style="margin-top: 16px; display: inline-block;">View all projects</a>
                        </div>
                        @else
                        <div class="listing-item-container three-columns-grid" style="margin-top: 24px;">
                            @foreach($projects as $project)
                            @php
                                $imageUrl = $project->homepage_listing_image
                                    ? url('storage/' . ltrim($project->homepage_listing_image, '/'))
                                    : ($project->featured_image ? url('storage/' . ltrim($project->featured_image, '/')) : asset('theme/images/all/1.jpg'));
                                $price = $project->price !== null && $project->price !== ''
                                    ? (is_numeric($project->price) ? config('app.currency', 'PKR') . ' ' . number_format((float) $project->price, 0) : $project->price)
                                    : '';
                                $location = $project->short_address ?: $project->city ?: $project->state ?: '';
                                $desc = $project->description ? \Illuminate\Support\Str::limit(strip_tags($project->description), 120) : '';
                            @endphp
                            <div class="listing-item">
                                <div class="geodir-category-listing">
                                    <div class="geodir-category-img">
                                        <a href="{{ route('project.show', $project->slug) }}" class="geodir-category-img_item">
                                            <div class="bg" style="background-image: url({{ $imageUrl }});"></div>
                                            <div class="overlay"></div>
                                        </a>
                                        @if($location)
                                        <div class="geodir-category-location">
                                            <a href="{{ route('project.show', $project->slug) }}" class="map-item"><i class="fas fa-map-marker-alt"></i> {{ $location }}</a>
                                        </div>
                                        @endif
                                        @if($project->projectTypes->isNotEmpty())
                                        <ul class="list-single-opt_header_cat">
                                            @foreach($project->projectTypes as $pt)
                                            <li><a href="{{ url('/projects') }}?project_type={{ urlencode($pt->slug) }}" class="cat-opt">{{ $pt->name }}</a></li>
                                            @endforeach
                                        </ul>
                                        @endif
                                        <div class="geodir-category-listing_media-list">
                                            <span><i class="fas fa-building"></i> Project</span>
                                        </div>
                                    </div>
                                    <div class="geodir-category-content">
                                        <h3><a href="{{ route('project.show', $project->slug) }}">{{ $project->title }}</a></h3>
                                        @if($price)
                                        <div class="geodir-category-content_price">{{ $price }}</div>
                                        @endif
                                        @if($desc)
                                        <p>{{ $desc }}</p>
                                        @endif
                                        @if($project->city || $project->state)
                                        <div class="geodir-category-content-details">
                                            <ul>
                                                @if($project->city)<li><i class="fa-light fa-city"></i><span>{{ $project->city }}</span></li>@endif
                                                @if($project->state)<li><i class="fa-light fa-location-dot"></i><span>{{ $project->state }}</span></li>@endif
                                            </ul>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="geodir-category-footer">
                                        <span class="gcf-company"><span>Etihad Marketing</span></span>
                                        <a href="{{ route('project.show', $project->slug) }}" class="gid_link"><span>View Details</span> <i class="fa-solid fa-caret-right"></i></a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
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

    @include('partials.theme-panels')
</div>
@endsection
