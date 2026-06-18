@php
    $isPopup = isset($isPopup) ? (bool) $isPopup : request()->boolean('popup');
@endphp

@if($isPopup)
    @include('partials.contact-page-content', ['cs' => $cs, 'cmsPage' => $cmsPage ?? null, 'isPopup' => true])
@else
    @extends('layouts.front')
    @php
        $cmsPage = $cmsPage ?? null;
        $contactTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : ('Contact Us - ' . config('app.name'));
    @endphp
    @section('title', $contactTitle)
    @push('meta')
    @include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
        'title' => $contactTitle,
        'canonical' => url('/contact-us'),
    ])])
    @endpush
    @section('content')
    <div id="main">
        @include('partials.header')
        @include('partials.contact-page-content', ['cs' => $cs, 'cmsPage' => $cmsPage ?? null, 'isPopup' => false])
    </div>
    @endsection
@endif

