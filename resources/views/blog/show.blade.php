@extends('layouts.front')

@php
    $pageTitle = $post->meta_title ?: ($post->title . ' – ' . config('app.name'));
    $pageDescription = $post->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 160);
    $canonical = $post->canonical_url ?: $post->url();
    $image = $post->seoImage() ?: $post->displayImage();
@endphp

@section('title', $pageTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => array_merge(
    seo_from_record($post, [
        'title' => $pageTitle,
        'description' => $pageDescription,
        'canonical' => $canonical,
        'keywords' => $post->meta_keywords ?: ($post->focus_keyphrase ?: ''),
        'image' => $image,
        'type' => 'article',
    ]),
    [
        'robots' => $post->meta_robots ?: 'index, follow',
        'og_title' => $post->og_title ?: $pageTitle,
        'og_description' => $post->og_description ?: $pageDescription,
        'twitter_card' => $post->twitter_card ?: 'summary_large_image',
        'twitter_title' => $post->twitter_title ?: ($post->og_title ?: $pageTitle),
        'twitter_description' => $post->twitter_description ?: ($post->og_description ?: $pageDescription),
        'twitter_image' => $post->twitter_image ?: $image,
    ]
)])
@endpush

@push('styles')
<link type="text/css" rel="stylesheet" href="{{ asset('theme/css/pages/blog.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <div class="container blog-page blog-post-page">
                <div class="breadcrumbs-list bl_flat">
                    <a href="{{ route('portal') }}">Home</a>
                    <a href="{{ route('blog.index') }}">News</a>
                    @foreach ($post->categories as $cat)
                        <a href="{{ $cat->url() }}">{{ $cat->name }}</a>
                    @endforeach
                    <span>{{ \Illuminate\Support\Str::limit($post->title, 48) }}</span>
                    <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                </div>

                <div class="row blog-layout">
                    <div class="col-lg-9">
                        <article class="blog-single">
                            @if ($post->featured_image || $post->featured_image_source || $post->og_image)
                                <div class="blog-single__media">
                                    <img src="{{ $post->displayImage() }}" alt="{{ $post->title }}" loading="eager" width="960" height="540">
                                </div>
                            @endif

                            <header class="blog-single__header">
                                @if ($post->categories->isNotEmpty())
                                    <div class="blog-single__cats">
                                        @foreach ($post->categories as $cat)
                                            <a href="{{ $cat->url() }}">{{ $cat->name }}</a>
                                        @endforeach
                                    </div>
                                @endif
                                <h1>{{ $post->title }}</h1>
                                <div class="blog-single__meta">
                                    @if ($post->published_at)
                                        <span><i class="fa-regular fa-calendar"></i> {{ $post->published_at->format('F j, Y') }}</span>
                                    @endif
                                    <span><i class="fa-regular fa-user"></i> {{ optional($post->author)->name ?: 'Admin' }}</span>
                                </div>
                            </header>

                            <div class="blog-single__content blog-content">
                                {!! $post->content !!}
                            </div>

                            @if ($post->tags->isNotEmpty())
                                <div class="tagcloud_single fl-wrap">
                                    <div class="tc_single_title"><i class="fa-solid fa-tags"></i> Tags</div>
                                    <div class="tags-widget blog-tags-cloud">
                                        @foreach ($post->tags as $tag)
                                            <a href="{{ $tag->url() }}" class="blog-tag">{{ $tag->name }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </article>
                    </div>
                    <div class="col-lg-3">
                        @include('blog.partials.sidebar-post', [
                            'post' => $post,
                            'recentInCategory' => $recentInCategory,
                            'sidebarCategories' => $sidebarCategories,
                        ])
                    </div>
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
</div>
@endsection
