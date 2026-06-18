@extends('layouts.front')
@php
    $cmsPage = $cmsPage ?? null;
    $pageTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : ('Careers – ' . config('app.name'));
    $pageHeading = ($cmsPage && $cmsPage->heading) ? $cmsPage->heading : 'Careers';
    $pageSubheading = $cmsPage && $cmsPage->content ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 120) : 'Explore open positions.';
    $bannerImage = ($cmsPage && $cmsPage->banner_image) ? url('storage/' . ltrim($cmsPage->banner_image, '/')) : asset('theme/images/bg/8.jpg');
@endphp
@section('title', $pageTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $pageTitle,
    'canonical' => url('/careers'),
    'image' => $bannerImage,
])])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/careers.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <div class="section hero-section hero-section_sin">
                <div class="hero-section-wrap">
                    <div class="hero-section-wrap-item">
                        <div class="container">
                            <div class="hero-section-container">
                                <div class="hero-section-title">
                                    <h1>{{ $pageHeading }}</h1>
                                    <h5>{{ $pageSubheading }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper"><div class="bg" data-bg="{{ $bannerImage }}"></div></div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <span>Careers</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>
                <div class="main-content boxed-container careers-page">
                    @if($jobs->isEmpty())
                    <div class="boxed-content"><div class="boxed-content-item"><p>No open positions at the moment. Check back later.</p></div></div>
                    @else
                    <div class="boxed-content">
                        <div class="boxed-content-item">
                            <div class="careers-grid">
                                @foreach($jobs as $job)
                                @php
                                    $dept = $job->department ?? '';
                                    $loc = $job->location ?? '';
                                    $date = $job->apply_before ? \Carbon\Carbon::parse($job->apply_before)->format('d M Y') : ($job->created_at ? $job->created_at->format('d M Y') : '');
                                    $status = $job->status ?? 'active';
                                    $statusLabel = $status === 'active' ? 'Open' : ($status === 'closed' ? 'Closed' : 'Draft');
                                    $statusClass = 'status-' . $status;
                                @endphp
                                <a href="{{ $job->slug ? route('careers.job', $job->slug) : url('/careers') }}" class="career-card">
                                    <span class="career-card_status {{ $statusClass }}"><i class="fa-light fa-circle-dot"></i> {{ $statusLabel }}</span>
                                    <div class="career-card_top">
                                        <h3 class="career-card_title">{{ $job->title }}</h3>
                                        @if($dept)
                                        <span class="career-card_badge"><i class="fa-light fa-briefcase"></i> {{ $dept }}</span>
                                        @endif
                                    </div>
                                    <div class="career-card_meta">
                                        @if($loc)
                                        <span><i class="fa-light fa-location-dot"></i> {{ $loc }}</span>
                                        @endif
                                        @if($date)
                                        <span><i class="fa-light fa-calendar-days"></i> {{ $date }}</span>
                                        @endif
                                    </div>
                                    <span class="career-card_cta">View details <i class="fa-solid fa-caret-right"></i></span>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
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
