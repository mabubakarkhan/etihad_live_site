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

@if($cmsPage)
@push('meta')
@if(!empty($cmsPage->meta_description))<meta name="description" content="{{ e($cmsPage->meta_description) }}">@endif
@if(!empty($cmsPage->meta_keywords))<meta name="keywords" content="{{ e($cmsPage->meta_keywords) }}">@endif
@if(!empty($cmsPage->canonical_url))<link rel="canonical" href="{{ e($cmsPage->canonical_url) }}">@endif
@endpush
@endif

@push('styles')
<style>
.team-page .agent-card-item { margin-bottom: 0; display: flex; flex-direction: column; height: 100%; }
.team-page .agent-card-item_media-wrap { position: relative; width: 100%; height: 160px; flex-shrink: 0; overflow: hidden; background: #e2e8f0; }
.team-page .agent-card-item_media-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; }
.team-page .agent-card-item_media-wrap .team-card-avatar-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #cbd5e1; color: #64748b; }
.team-page .agent-card-item_media-wrap .team-card-avatar-placeholder svg { width: 64px; height: 64px; opacity: 0.7; }
.team-page .agent-card-item_footer .property-contacts-links { display: none !important; }
.team-page .agent-card-item_text { flex: 1; display: flex; flex-direction: column; min-height: 0; padding: 12px 0 8px; }
.team-page .agent-card-item_text-item h4 { margin: 0 0 6px; font-size: 1rem; line-height: 1.3; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.team-page .agent-card-item_text-item p { margin: 0 0 8px; font-size: 0.8125rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; flex: 1; min-height: 0; }
.team-page .post-card-details { flex-shrink: 0; }
.team-page .post-card-details ul { list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: 8px 12px; }
.team-page .post-card-details ul li { display: flex; align-items: center; gap: 4px; font-size: 0.8125rem; color: var(--theme-color, #e85d04); }
.team-page .post-card-details ul li i { opacity: 0.9; }
.team-page .agent-card-item_footer { flex-shrink: 0; padding-top: 8px; }
.team-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
@media (max-width: 1200px) { .team-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 576px) { .team-grid { grid-template-columns: 1fr; } .team-page .agent-card-item_media-wrap { height: 140px; } }
</style>
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
                                    <h2>{{ $pageHeading }}</h2>
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
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ url('/our-team') }}">Our Team</a>
                    <span>{{ $pageHeading }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="main-content team-page">
                    @if($dealers->isEmpty())
                    <div class="boxed-content-item" style="padding: 48px 24px; text-align: center;">
                        <p style="margin: 0; color: #64748b;">No team members to display yet.</p>
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
                                            <img src="{{ $imgUrl }}" alt="{{ e($dealer->name) }}" class="respimg" onerror="this.onerror=null;this.src=this.dataset.fallback||'';" data-fallback="{{ $avatarPlaceholder }}">
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
