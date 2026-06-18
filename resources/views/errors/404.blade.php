@extends('layouts.front')

@section('title', 'Page Not Found (404) – ' . config('app.name'))

@push('meta')
@include('partials.seo-meta', ['seo' => [
    'title' => 'Page Not Found (404) – ' . config('app.name'),
    'description' => '',
    'keywords' => '',
    'canonical' => url()->current(),
    'image' => '',
    'type' => 'website',
]])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/error-404.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')

    <div class="wrapper">
        <div class="content">
            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ url('/') }}">Home</a>
                    <span>Page Not Found</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>
                <div class="main-content error-page-main">
                    <div class="boxed-container">
                        <div class="boxed-content">
                            <div class="boxed-content-item error-page-inner">
                                <div class="error-page-code">404</div>
                                <h1 class="error-page-title">Page Not Found</h1>
                                <p class="error-page-text">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                                <div class="etihad-cta-row">
                                    <a href="{{ url('/') }}" class="commentssubmit">Back to Home</a>
                                    <a href="{{ url('/listing') }}" class="commentssubmit etihad-cta-dark">View Property Listing</a>
                                </div>
                            </div>
                        </div>
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
