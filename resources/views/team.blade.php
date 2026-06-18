@extends('layouts.front')
{{-- Meta tags and hero banner come from cms_pages id 9 (Admin > CMS Pages). --}}
@php
    $cmsPage = $cmsPage ?? null;
    $teamTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : ('Our Team – ' . config('app.name'));
    $pageHeading = ($cmsPage && $cmsPage->heading) ? $cmsPage->heading : 'Our Team';
    $pageSubheading = $cmsPage && $cmsPage->content ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 120) : 'Meet our dedicated team of professionals.';
    $bannerImage = ($cmsPage && $cmsPage->banner_image) ? url('storage/' . ltrim($cmsPage->banner_image, '/')) : asset('theme/images/bg/8.jpg');
@endphp

@section('title', $teamTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $teamTitle,
    'canonical' => url('/team'),
    'image' => $bannerImage,
])])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/team.css') }}">
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

            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/our-team') }}">Our Team</a>
                    <span>{{ $pageHeading }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="main-content team-page">
                    @if($dealers->isEmpty())
                    <div class="boxed-content-item etihad-empty-state">
                        <p>No team members to display yet.</p>
                    </div>
                    @else
                    <div class="team-grid">
                                @foreach($dealers as $dealer)
                                @php
                                    $hasImage = !empty($dealer->profile_pic);
                                    $imgUrl = $hasImage ? url('storage/' . ltrim($dealer->profile_pic, '/')) : '';
                                    $propsCount = (int) ($dealer->properties_count ?? 0);
                                    $viewsCount = (int) ($dealer->view_count ?? 0);
                                    $desc = $dealer->info_detail ? \Illuminate\Support\Str::limit(strip_tags($dealer->info_detail), 120) : '';
                                    $avatarPlaceholder = 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="3"/><path d="M5 20v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/></svg>');
                                @endphp
                                <div class="agent-card-item">
                                    <div class="agent-card-item_media">
                                        <div class="agent-card-item_media-wrap">
                                            @if($hasImage)
                                            <img src="{{ $imgUrl }}" alt="{{ e($dealer->name) }}" class="dealer-portrait-img" loading="lazy" onerror="this.onerror=null;this.src=this.dataset.fallback||'';" data-fallback="{{ $avatarPlaceholder }}">
                                            @else
                                            <div class="team-card-avatar-placeholder"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="3"/><path d="M5 20v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2"/></svg></div>
                                            @endif
                                            <div class="overlay"></div>
                                        </div>
                                    </div>
                                    <div class="agent-card-item_text">
                                        <div class="agent-card-item_text-item">
                                            <h4>{{ $dealer->name }}</h4>
                                            @if($desc)
                                            <p>{{ $desc }}</p>
                                            @endif
                                            <div class="post-card-details">
                                                <ul>
                                                    <li><i class="fa-regular fa-house-building"></i><span>{{ $propsCount }} Properties</span></li>
                                                    <li><i class="fa-light fa-eye"></i><span>{{ number_format($viewsCount) }} Views</span></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="agent-card-item_footer sh-links">
                                        <a href="{{ $dealer->slug ? route('dealer.show', $dealer->slug) : url('/listing') }}" class="post-card_link">View Details <i class="fa-solid fa-caret-right"></i></a>
                                    </div>
                                </div>
                                @endforeach
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
