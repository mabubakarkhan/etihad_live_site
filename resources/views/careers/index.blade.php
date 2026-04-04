@extends('layouts.front')
@php
    $cmsPage = $cmsPage ?? null;
    $pageTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : ('Careers – ' . config('app.name'));
    $pageHeading = ($cmsPage && $cmsPage->heading) ? $cmsPage->heading : 'Careers';
    $pageSubheading = $cmsPage && $cmsPage->content ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 120) : 'Explore open positions.';
    $bannerImage = ($cmsPage && $cmsPage->banner_image) ? url('storage/' . ltrim($cmsPage->banner_image, '/')) : asset('theme/images/bg/8.jpg');
@endphp
@section('title', $pageTitle)
@if($cmsPage)
@push('meta')
@if(!empty($cmsPage->meta_description))<meta name="description" content="{{ e($cmsPage->meta_description) }}">@endif
@if(!empty($cmsPage->meta_keywords))<meta name="keywords" content="{{ e($cmsPage->meta_keywords) }}">@endif
@if(!empty($cmsPage->canonical_url))<link rel="canonical" href="{{ e($cmsPage->canonical_url) }}">@endif
@endpush
@endif

@push('styles')
<style>
.careers-page .careers-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }
@media (max-width: 1200px) { .careers-page .careers-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .careers-page .careers-grid { grid-template-columns: 1fr; } }

.careers-page .career-card {
    display: block;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 14px;
    padding: 18px 18px 16px;
    text-decoration: none;
    color: inherit;
    transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
    height: 100%;
}
.careers-page .career-card:hover {
    transform: translateY(-2px);
    border-color: rgba(232, 93, 4, .35);
    box-shadow: 0 14px 30px rgba(2, 6, 23, .08);
}
.careers-page .career-card_top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
.careers-page .career-card_title {
    margin: 0;
    font-size: 1.05rem;
    line-height: 1.35;
    font-weight: 700;
    color: #0f172a;
}
.careers-page .career-card_badge {
    flex-shrink: 0;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    background: rgba(232, 93, 4, .10);
    border: 1px solid rgba(232, 93, 4, .20);
    color: var(--theme-color, #e85d04);
    white-space: nowrap;
}
.careers-page .career-card_status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin-bottom: 12px;
    width: fit-content;
}
.careers-page .career-card_status.status-open {
    background: rgba(34, 197, 94, .15);
    border: 1px solid rgba(34, 197, 94, .35);
    color: #15803d;
}
.careers-page .career-card_status.status-closed {
    background: rgba(100, 116, 139, .15);
    border: 1px solid rgba(100, 116, 139, .3);
    color: #475569;
}
.careers-page .career-card_status.status-draft {
    background: rgba(245, 158, 11, .15);
    border: 1px solid rgba(245, 158, 11, .35);
    color: #b45309;
}
.careers-page .career-card_meta { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px 14px; color: #64748b; font-size: 13px; }
.careers-page .career-card_meta span { display: inline-flex; align-items: center; gap: 7px; }
.careers-page .career-card_meta i { color: var(--theme-color, #e85d04); opacity: .95; }
.careers-page .career-card_cta {
    margin-top: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--theme-color, #e85d04);
}
.careers-page .career-card_cta i { font-size: 12px; }
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
