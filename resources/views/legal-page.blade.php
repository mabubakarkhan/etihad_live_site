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
            <div class="container">
                <div class="cms-page-head">
                    <h1>{{ $cmsPage->heading ?: $cmsPage->title }}</h1>
                    @if(!empty($cmsPage->content))
                    <p class="cms-page-head__lead">{{ \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 160) }}</p>
                    @endif
                </div>
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
                            <div class="boxed-content-title"><h2>{{ $cmsPage->title }}</h2></div>
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

