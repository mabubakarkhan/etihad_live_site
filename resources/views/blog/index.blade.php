@extends('layouts.front')

@php
    $cmsPage = $cmsPage ?? null;
    $pageHeading = ($cmsPage && $cmsPage->heading) ? $cmsPage->heading : 'News';
    $pageLead = $cmsPage && $cmsPage->content
        ? \Illuminate\Support\Str::limit(strip_tags($cmsPage->content), 160)
        : 'Insights, market updates, and guides from our team.';
    $pageTitle = ($cmsPage && $cmsPage->meta_title)
        ? $cmsPage->meta_title
        : ($pageHeading . ' & Blogs – ' . config('app.name'));
    $bannerImage = ($cmsPage && $cmsPage->banner_image)
        ? url('storage/' . ltrim($cmsPage->banner_image, '/'))
        : null;
    $canonical = ($cmsPage && $cmsPage->canonical_url) ? $cmsPage->canonical_url : url('/blogs');
@endphp

@section('title', $pageTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $pageTitle,
    'description' => ($cmsPage && $cmsPage->meta_description)
        ? $cmsPage->meta_description
        : 'Latest real estate news, guides, and updates from Etihad Marketing.',
    'canonical' => $canonical,
    'keywords' => ($cmsPage && $cmsPage->meta_keywords)
        ? $cmsPage->meta_keywords
        : 'Etihad Marketing blog, DHA news, real estate Pakistan',
    'image' => $bannerImage ?: '',
    'type' => 'website',
])])
@endpush

@push('styles')
<link type="text/css" rel="stylesheet" href="{{ asset('theme/css/pages/blog.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <section class="blog-hero">
                <div class="blog-hero__bg" aria-hidden="true"></div>
                <div class="container">
                    <div class="blog-hero__inner">
                        <p class="blog-hero__eyebrow">Etihad Marketing</p>
                        <h1>{{ $pageHeading }}</h1>
                        @if ($pageLead)
                            <p class="blog-hero__lead">{{ $pageLead }}</p>
                        @endif
                    </div>
                </div>
            </section>

            <div class="container blog-page">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ route('portal') }}">Home</a>
                    <span>{{ $pageHeading }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="row blog-layout">
                    <div class="col-lg-9">
                        @include('blog.partials.ajax-list', ['posts' => $posts])
                    </div>
                    <div class="col-lg-3">
                        @include('blog.partials.sidebar-categories', ['categories' => $categories, 'tags' => $tags])
                    </div>
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('theme/js/blog-ajax.js') }}"></script>
@endpush
