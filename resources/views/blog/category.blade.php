@extends('layouts.front')

@php
    $pageHeading = $category->name;
    $pageLead = $category->description
        ? \Illuminate\Support\Str::limit(strip_tags($category->description), 160)
        : ('Articles filed under ' . $category->name . '.');
    $pageTitle = $category->meta_title ?: ($category->name . ' – News – ' . config('app.name'));
    $pageDescription = $category->meta_description ?: ($category->description ?: ('Posts in category ' . $category->name));
    $canonical = $category->canonical_url ?: $category->url();
@endphp

@section('title', $pageTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($category, [
    'title' => $pageTitle,
    'description' => $pageDescription,
    'canonical' => $canonical,
    'keywords' => $category->meta_keywords ?: ($category->name . ', Etihad Marketing blog'),
    'image' => $category->og_image ?: '',
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
            <section class="blog-hero blog-hero--category">
                <div class="blog-hero__bg" aria-hidden="true"></div>
                <div class="container">
                    <div class="blog-hero__inner">
                        <p class="blog-hero__eyebrow">Category</p>
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
                    <a href="{{ route('blog.index') }}">News</a>
                    <span>{{ $category->name }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="row blog-layout">
                    <div class="col-lg-9">
                        @include('blog.partials.ajax-list', ['posts' => $posts])
                    </div>
                    <div class="col-lg-3">
                        @include('blog.partials.sidebar-categories', [
                            'categories' => $categories,
                            'tags' => $tags,
                            'activeCategory' => $category,
                        ])
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
