@extends('layouts.front')

@section('title', 'Page Not Found (404) – ' . config('app.name'))

@push('styles')
<style>
@media (max-width: 768px) {
    .error-page-code { font-size: 72px !important; }
}
</style>
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
                <div class="main-content" style="margin-top: 30px; margin-bottom: 60px;">
                    <div class="boxed-container">
                        <div class="boxed-content">
                            <div class="boxed-content-item" style="text-align: center; padding: 50px 30px 60px;">
                                <div class="error-page-code" style="font-size: 120px; font-weight: 700; line-height: 1; color: var(--main-color, #EE7838); margin-bottom: 16px;">404</div>
                                <h2 style="font-size: 1.75rem; font-weight: 600; color: #1e1e1e; margin-bottom: 12px;">Page Not Found</h2>
                                <p style="font-size: 1rem; color: #5e646a; margin-bottom: 28px; max-width: 480px; margin-left: auto; margin-right: auto;">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                                <div style="display: flex; flex-wrap: wrap; gap: 12px; justify-content: center;">
                                    <a href="{{ url('/') }}" class="commentssubmit" style="margin: 0;">Back to Home</a>
                                    <a href="{{ url('/listing') }}" class="commentssubmit" style="margin: 0; background: #1e1e1e;">View Property Listing</a>
                                </div>
                            </div>
                        </div>
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
