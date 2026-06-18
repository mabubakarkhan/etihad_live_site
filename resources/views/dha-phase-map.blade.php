@extends('layouts.front')

@php
    $pageTitle = ($phase->title ?? 'DHA Phase') . ' Map | ' . config('app.name');
@endphp

@section('title', $pageTitle)

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha-phase-map.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <div class="dha-phase-map-page">
                <div class="dha-phase-map-page__head">
                    <a href="{{ route('dha.phase.show', $phase->slug) }}" class="dha-phase-map-page__back">
                        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        Back to {{ $phase->title }}
                    </a>
                    <h1 class="dha-phase-map-page__title">{{ $phase->title }} Map</h1>
                </div>

                @if($phase->mapEmbedUrl())
                <div class="dha-phase-map-page__frame">
                    <iframe src="{{ $phase->mapEmbedUrl() }}" title="{{ $phase->title }} map" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
                @endif

                @if($phase->plotMapItems() !== [])
                <div class="dha-phase-map-page__plots">
                    @foreach($phase->plotMapItems() as $plot)
                    <figure class="dha-phase-map-page__plot">
                        @if($plot['title'] !== '')
                        <figcaption>{{ $plot['title'] }}</figcaption>
                        @endif
                        <a href="{{ $plot['url'] }}" target="_blank" rel="noopener noreferrer">
                            <img src="{{ $plot['url'] }}" alt="{{ $plot['title'] ?: ($phase->title . ' map') }}" loading="lazy" decoding="async">
                        </a>
                    </figure>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="container">
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
