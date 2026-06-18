@extends('layouts.front')

@section('title', ($cmsPage->meta_title ?: $cmsPage->title) . ' - ' . config('app.name'))

@php
    $legalTitle = ($cmsPage->meta_title ?: $cmsPage->title) . ' - ' . config('app.name');
@endphp

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $legalTitle,
    'canonical' => request()->url(),
])])
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
                                    <h1>{{ $cmsPage->heading ?: $cmsPage->title }}</h1>
                                    <h5>{{ !empty($cmsPage->content) ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 120) : $cmsPage->title }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="hs-scroll-down-wrap">
                            <div class="scroll-down-item">
                                <div class="mousey"><div class="scroller"></div></div>
                                <span>Scroll Down To Discover</span>
                            </div>
                            <div class="svg-corner svg-corner_white legal-hero-corner-right"></div>
                            <div class="svg-corner svg-corner_white legal-hero-corner-left"></div>
                        </div>
                        <div class="bg-wrap bg-hero bg-parallax-wrap-gradien fs-wrapper" data-scrollax-parent="true">
                            <div class="bg" data-bg="{{ asset('theme/images/bg/14.jpg') }}" data-scrollax="properties: { translateY: '30%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="breadcrumbs-list bl_flat">
                    @php
                        $legalCrumbTitle = $cmsPage->title ?: (request()->is('terms-of-use') ? 'Terms Of Use' : (request()->is('privacy-policy') ? 'Privacy Policy' : 'Legal'));
                    @endphp
                    <a href="{{ url('/') }}">Home</a><a href="#">Pages</a><span>{{ $legalCrumbTitle }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>
                <section class="gray-bg small-padding">
                    <div class="boxed-container">
                        <div class="boxed-content">
                            <div class="boxed-content-title"><h3>{{ $cmsPage->title }}</h3></div>
                            <div class="boxed-content-item">
                                @php
                                    $legalHtml = (string) ($cmsPage->content ?? '');
                                    $openDivCount = substr_count(strtolower($legalHtml), '<div');
                                    $closeDivCount = substr_count(strtolower($legalHtml), '</div>');
                                    $canRenderRawLegalHtml = $openDivCount === $closeDivCount;
                                @endphp
                                @if($canRenderRawLegalHtml)
                                    {!! $legalHtml !!}
                                @else
                                    {!! nl2br(e(strip_tags($legalHtml))) !!}
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
                <div class="to_top-btn-wrap">
                    <div class="to-top to-top_btn"><span>Back to top</span> <i class="fa-solid fa-arrow-up"></i></div>
                    <div class="svg-corner svg-corner_white legal-top-corner-left"></div>
                    <div class="svg-corner svg-corner_white legal-top-corner-right"></div>
                </div>
            </div>
        </div>

        @include('partials.footer')
    </div>
</div>
@endsection

