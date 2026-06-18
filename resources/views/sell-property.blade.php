@extends('layouts.front')

@php
    $cmsPage = $cmsPage ?? null;
    $pageSettings = $pageSettings ?? \App\Models\SellRentPageSetting::instance();
    $pageTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : ('Sell or Rent Property | ' . config('app.name'));
    $valuationMeta = $pageSettings->valuationMeta();
    $transactionStats = $pageSettings->transactionStats();
    $transactions = $pageSettings->transactions();
    $faqs = $pageSettings->faqs();
    $valuationChartUrl = $pageSettings->valuationChartUrl();
@endphp

@section('title', $pageTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $pageTitle,
    'canonical' => url('/sell-or-rent-property'),
])])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('theme/css/pages/sell-property.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    <div class="wrapper">
        <div class="content">
            <div class="sell-property-page">
                <div class="sell-property-page__layout">
                    <div class="sell-property-page__scroll">
                        <section class="sell-property-hero" id="sell-property-hero">
                            <h1 class="sell-property-hero__title">{{ $cmsPage->heading ?? 'Sell or rent your property with confidence!' }}</h1>
                            <p class="sell-property-hero__lead">{{ !empty($cmsPage->content) ? strip_tags($cmsPage->content) : 'Expert help, zero hassle. We\'ll match you with a trusted real estate agent to lead your sale from start to finish in DHA Lahore.' }}</p>
                            <div class="sell-property-hero__illus">
                                @if($pageSettings->hasHeroImage() && $pageSettings->heroImageUrl())
                                <img
                                    src="{{ $pageSettings->heroImageUrl() }}"
                                    alt="{{ $cmsPage->heading ?? 'Sell or rent property in DHA Lahore' }}"
                                    class="sell-property-hero__visual"
                                    loading="lazy"
                                    decoding="async"
                                >
                                @else
                                <div class="sell-property-hero__visual sell-property-hero__visual--placeholder" aria-hidden="true"></div>
                                @endif
                            </div>
                        </section>

                        @if($pageSettings->valuation_heading || $pageSettings->valuation_price)
                        <section class="sell-property-block sell-property-valuation" id="sell-property-valuation">
                            @if($pageSettings->valuation_heading)
                            <h2 class="sell-property-block__title">{{ $pageSettings->valuation_heading }}</h2>
                            @endif
                            <div class="sell-property-valuation__card">
                                <div class="sell-property-valuation__head">
                                    @if($pageSettings->valuation_price)
                                    <strong class="sell-property-valuation__price">{{ $pageSettings->valuation_price }}</strong>
                                    @endif
                                    @if($pageSettings->valuation_badge)
                                    <span class="sell-property-valuation__badge">{{ $pageSettings->valuation_badge }}</span>
                                    @endif
                                </div>
                                @if($valuationMeta !== [])
                                <ul class="sell-property-valuation__meta">
                                    @foreach($valuationMeta as $row)
                                    <li>
                                        {{ $row['label'] }}
                                        @if($row['highlight'])
                                            <span class="is-up">{{ $row['value'] }}</span>
                                        @else
                                            <strong>{{ $row['value'] }}</strong>
                                        @endif
                                    </li>
                                    @endforeach
                                </ul>
                                @endif
                                <div class="sell-property-valuation__chart">
                                    @if($valuationChartUrl)
                                    <img src="{{ $valuationChartUrl }}" alt="Property valuation trend chart" loading="lazy" decoding="async">
                                    @else
                                    <div class="sell-property-valuation__chart-placeholder" aria-hidden="true"></div>
                                    @endif
                                </div>
                            </div>
                            @if($pageSettings->valuation_copy)
                            <p class="sell-property-block__copy">{{ $pageSettings->valuation_copy }}</p>
                            @endif
                        </section>
                        @endif

                        @if($pageSettings->transactions_heading || $transactionStats !== [] || $transactions !== [])
                        <section class="sell-property-block sell-property-transactions" id="sell-property-transactions">
                            @if($pageSettings->transactions_heading)
                            <h2 class="sell-property-block__title">{{ $pageSettings->transactions_heading }}</h2>
                            @endif
                            @if($transactionStats !== [])
                            <div class="sell-property-transactions__stats">
                                @foreach($transactionStats as $stat)
                                <article>
                                    <span>{{ $stat['label'] }}</span>
                                    <strong>{{ $stat['value'] }}</strong>
                                    @if($stat['change'] !== '')
                                    <em class="{{ $stat['is_up'] ? 'is-up' : '' }}">{{ $stat['change'] }}</em>
                                    @endif
                                </article>
                                @endforeach
                            </div>
                            @endif
                            @if($transactions !== [])
                            <div class="sell-property-transactions__table-wrap">
                                <table class="sell-property-transactions__table">
                                    <thead>
                                        <tr><th>Date</th><th>Location</th><th>Price (PKR)</th><th>Type</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($transactions as $row)
                                        <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['location'] }}</td>
                                            <td>{{ $row['price'] }}</td>
                                            <td>{{ $row['type'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @endif
                            @if($pageSettings->transactions_copy)
                            <p class="sell-property-block__copy">{{ $pageSettings->transactions_copy }}</p>
                            @endif
                        </section>
                        @endif

                        @if($faqs !== [])
                        <section class="sell-property-faqs" id="sell-property-faqs">
                            <h2 class="sell-property-block__title">{{ $pageSettings->faqs_heading ?: 'Frequently Asked Questions' }}</h2>
                            <div class="sell-property-faqs__list">
                                @foreach($faqs as $i => $faq)
                                <details class="sell-property-faq" {{ $i === 0 ? 'open' : '' }}>
                                    <summary>{{ $faq['question'] }}</summary>
                                    <p>{{ $faq['answer'] }}</p>
                                </details>
                                @endforeach
                            </div>
                        </section>
                        @endif
                    </div>

                    <aside class="sell-property-page__form-col" id="sell-property-aside" aria-label="List your property">
                        <div class="sell-property-page__form-stick">
                            @include('partials.sell-property-form', [
                                'dhaPhases' => $dhaPhases ?? collect(),
                                'formSubmitLabel' => $pageSettings->formSubmitLabel(),
                            ])
                        </div>
                    </aside>
                </div>
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

@push('scripts')
<script src="{{ asset('theme/js/sell-property.js') }}"></script>
@endpush
