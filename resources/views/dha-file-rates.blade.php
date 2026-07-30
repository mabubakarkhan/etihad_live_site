@extends('layouts.front')

@php
    $setting = $setting ?? null;
    $groupedRates = $groupedRates ?? collect();
    $phasesForFilter = $phasesForFilter ?? collect();
    $pageTitle = $pageTitle ?? ('DHA File Rates – ' . config('app.name'));
    $callPhone = $callPhone ?? '';
    $whatsappPhone = $whatsappPhone ?? '';
@endphp

@section('title', $pageTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => array_merge(
    seo_from_record($setting, [
        'title' => $pageTitle,
        'description' => $setting->details ?? '',
        'canonical' => url('/dha-file-rates'),
        'keywords' => $setting->meta_keywords ?? 'DHA file rates',
    ]),
    [
        'robots' => $setting->meta_robots ?: 'index, follow',
        'og_title' => $setting->og_title ?: $pageTitle,
        'og_description' => $setting->og_description ?: ($setting->meta_description ?: ''),
        'twitter_card' => $setting->twitter_card ?: 'summary_large_image',
    ]
)])
@endpush

@push('styles')
<style>
.dha-file-rates-page {
    padding: 10px 0 70px;
}
.dha-file-rates-page .dha-file-rates-col {
    float: none;
    margin-left: auto;
    margin-right: auto;
}
.dha-file-rates-page .cms-page-head {
    margin-bottom: 14px;
    text-align: center;
}
.dha-file-rates-page .cms-page-head h1 {
    font-size: clamp(1.55rem, 2.6vw, 2.15rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    color: #0b1b33;
    margin-bottom: 8px;
}
.dha-file-rates-page .cms-page-head__lead {
    max-width: 36rem;
    margin: 0 auto;
    color: #64748b;
    font-size: 1rem;
    line-height: 1.55;
}
.dha-file-rates-page .breadcrumbs-list {
    justify-content: center;
    margin-bottom: 18px;
}
.dha-file-rates-details {
    max-width: 40rem;
    margin: 0 auto 22px;
    text-align: center;
    color: #475569;
    line-height: 1.7;
    font-size: 0.95rem;
}
.dha-file-rates-toolbar {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 0 0 28px;
    align-items: center;
    padding: 12px;
    border-radius: 16px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
}
.dha-file-rates-toolbar select,
.dha-file-rates-toolbar input[type="search"] {
    min-height: 44px;
    border: 1px solid rgba(15, 23, 42, 0.12);
    border-radius: 12px;
    padding: 0 14px;
    background: #fff;
    font-size: 14px;
    color: #0f172a;
    outline: none;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.dha-file-rates-toolbar select:focus,
.dha-file-rates-toolbar input[type="search"]:focus {
    border-color: rgba(200, 155, 60, 0.7);
    box-shadow: 0 0 0 3px rgba(200, 155, 60, 0.15);
}
.dha-file-rates-toolbar select { min-width: 190px; }
.dha-file-rates-toolbar input[type="search"] { min-width: 200px; flex: 1; }
.dha-file-rates-toolbar button {
    min-height: 44px;
    padding: 0 18px;
    border: 0;
    border-radius: 12px;
    background: linear-gradient(135deg, #13233f 0%, #0b1b33 100%);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: transform 0.15s ease, filter 0.15s ease;
}
.dha-file-rates-toolbar button:hover {
    filter: brightness(1.08);
    transform: translateY(-1px);
}
.dha-file-rates-toolbar a.reset {
    font-size: 13px;
    color: #64748b;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.dha-file-rates-block { margin-bottom: 26px; }
.dha-file-rates-table-wrap {
    overflow: hidden;
    border-radius: 16px;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow:
        0 1px 0 rgba(255, 255, 255, 0.7) inset,
        0 14px 36px rgba(15, 23, 42, 0.07);
}
.dha-file-rates-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    min-width: 520px;
}
.dha-file-rates-table th,
.dha-file-rates-table td {
    padding: 15px 12px;
    text-align: center;
    border-bottom: 1px solid rgba(15, 23, 42, 0.06);
    font-size: 15px;
    vertical-align: middle;
    width: 25%;
}
.dha-file-rates-table thead.phase-head th {
    background: linear-gradient(135deg, #13233f 0%, #0b1b33 70%, #1a2d4d 100%);
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 0.01em;
    text-transform: none;
    border-bottom: 0;
    position: relative;
    width: auto;
}
.dha-file-rates-table thead.phase-head th::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 2px;
    background: linear-gradient(90deg, #c89b3c 0%, #e0b85a 50%, #c89b3c 100%);
}
.dha-file-rates-table thead.cols-head th {
    background: #101a2b;
    color: rgba(255, 255, 255, 0.78);
    font-weight: 600;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding-top: 11px;
    padding-bottom: 11px;
}
.dha-file-rates-table tbody tr {
    transition: background 0.15s ease;
}
.dha-file-rates-table tbody tr:nth-child(even) td { background: #f8fafc; }
.dha-file-rates-table tbody tr:hover td { background: #f3f0e8; }
.dha-file-rates-table tbody tr:last-child td { border-bottom: 0; }
.dha-file-rates-table .size-main {
    display: block;
    font-weight: 700;
    color: #0b1b33;
    margin-bottom: 3px;
    font-size: 15px;
    text-align: center;
}
.dha-file-rates-table .size-cat {
    display: inline-block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    padding: 2px 7px;
    border-radius: 999px;
}
.dha-file-rates-table .size-cat.is-residential {
    color: #047857;
    background: rgba(16, 185, 129, 0.12);
}
.dha-file-rates-table .size-cat.is-commercial {
    color: #b91c1c;
    background: rgba(239, 68, 68, 0.12);
}
.dha-file-rates-table .type-cell {
    color: #64748b;
    text-align: center;
    font-weight: 500;
}
.dha-file-rates-table .price-main {
    display: block;
    font-weight: 700;
    font-size: 16px;
    color: #0b1b33;
    text-align: center;
}
.dha-file-rates-table .price-digits {
    display: block;
    margin-top: 2px;
    font-size: 12px;
    color: #94a3b8;
    text-align: center;
}
.dha-file-rates-cta-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
}
.dha-file-rates-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-height: 34px;
    padding: 0 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none !important;
    white-space: nowrap;
    transition: transform 0.15s ease, filter 0.15s ease;
}
.dha-file-rates-action:hover {
    filter: brightness(1.05);
    transform: translateY(-1px);
}
.dha-file-rates-action--call {
    background: linear-gradient(135deg, #e0b85a 0%, #c89b3c 50%, #a67c2a 100%);
    color: #111 !important;
    box-shadow: 0 8px 18px rgba(168, 124, 42, 0.28);
}
.dha-file-rates-action--wa {
    background: #25d366;
    color: #fff !important;
    box-shadow: 0 8px 18px rgba(37, 211, 102, 0.28);
}
.dha-file-rates-action i {
    font-size: 13px;
}
.dha-file-rates-empty {
    display: none;
    margin-top: 8px;
}
.dha-file-rates-empty.is-visible {
    display: block;
}
.dha-file-rates-toolbar .dha-file-rates-clear {
    min-height: 44px;
    padding: 0 16px;
    border: 1px solid rgba(15, 23, 42, 0.14);
    border-radius: 12px;
    background: #fff;
    color: #475569;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
}
@media (max-width: 991.98px) {
    .dha-file-rates-page .cms-page-head,
    .dha-file-rates-details { text-align: left; }
    .dha-file-rates-page .cms-page-head__lead,
    .dha-file-rates-details { margin-left: 0; margin-right: 0; max-width: none; }
    .dha-file-rates-page .breadcrumbs-list { justify-content: flex-start; }
}
</style>
@endpush

@section('content')
<div id="main">
    @include('partials.header')

    <div class="wrapper">
        <div class="content">
            <div class="container dha-file-rates-page">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-9 dha-file-rates-col">
                        <div class="cms-page-head">
                            <h1>{{ $setting->heading ?: 'DHA File Rates' }}</h1>
                            @if($setting->subheading)
                                <p class="cms-page-head__lead">{{ $setting->subheading }}</p>
                            @endif
                        </div>

                        <div class="breadcrumbs-list bl_flat">
                            <a href="{{ route('portal') }}">Home</a>
                            <a href="{{ route('dha.index') }}">DHA</a>
                            <span>{{ $setting->heading ?: 'File Rates' }}</span>
                            <div class="breadcrumbs-list_dec"><i class="fa-solid fa-angle-right"></i></div>
                        </div>

                        @if($setting->details)
                            <p class="dha-file-rates-details">{{ $setting->details }}</p>
                        @endif

                        <form class="dha-file-rates-toolbar" id="dha-file-rates-filter" action="#" onsubmit="return false;">
                            <select id="dha-file-rates-phase" name="phase" aria-label="Filter by DHA phase">
                                <option value="">All DHA phases</option>
                                @foreach($phasesForFilter as $phase)
                                    <option value="{{ $phase->id }}">{{ $phase->title }}</option>
                                @endforeach
                            </select>
                            <input type="search" id="dha-file-rates-q" name="q" value="" placeholder="Search size, type, phase…" aria-label="Search file rates" autocomplete="off" />
                            <button type="button" id="dha-file-rates-clear" class="dha-file-rates-clear" hidden>Clear</button>
                        </form>

                        @if($groupedRates->isEmpty())
                            <div class="boxed-content-item etihad-empty-state">
                                <p>File rates will be published soon. Please contact us for the latest prices.</p>
                            </div>
                        @else
                            <div id="dha-file-rates-list">
                                @foreach($groupedRates as $phaseId => $phaseRates)
                                    @php
                                        $phase = $phaseRates->first()?->dhaPhase;
                                        $phaseTitle = $phase?->title ?: 'Other / Unassigned';
                                    @endphp
                                    <div class="dha-file-rates-block" data-phase-id="{{ $phaseId }}" data-phase-title="{{ mb_strtolower($phaseTitle) }}">
                                        <div class="dha-file-rates-table-wrap">
                                            <table class="dha-file-rates-table">
                                                <thead class="phase-head">
                                                    <tr>
                                                        <th colspan="4">{{ $phaseTitle }}</th>
                                                    </tr>
                                                </thead>
                                                <thead class="cols-head">
                                                    <tr>
                                                        <th>Size</th>
                                                        <th>Type</th>
                                                        <th>Price</th>
                                                        <th>Contact</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($phaseRates as $rate)
                                                        @php
                                                            $catClass = $rate->category === 'commercial' ? 'is-commercial' : 'is-residential';
                                                            $searchBlob = mb_strtolower(trim(implode(' ', array_filter([
                                                                $rate->plot_size,
                                                                $rate->categoryLabel(),
                                                                $rate->file_type,
                                                                $rate->price,
                                                                $rate->price_digits,
                                                                $phaseTitle,
                                                            ]))));
                                                            $waText = rawurlencode(trim(
                                                                'Hi, I am interested in DHA file: '
                                                                . ($rate->plot_size ?: '')
                                                                . ($rate->file_type ? ' (' . $rate->file_type . ')' : '')
                                                                . ($phaseTitle ? ' — ' . $phaseTitle : '')
                                                                . ($rate->price ? ' — ' . $rate->price : '')
                                                            ));
                                                        @endphp
                                                        <tr class="dha-file-rates-row" data-search="{{ e($searchBlob) }}">
                                                            <td>
                                                                <span class="size-main">{{ $rate->plot_size }}</span>
                                                                @if($rate->categoryLabel())
                                                                    <span class="size-cat {{ $catClass }}">{{ $rate->categoryLabel() }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="type-cell">{{ $rate->file_type ?: '—' }}</td>
                                                            <td>
                                                                <span class="price-main">{{ $rate->price ?: '—' }}</span>
                                                                @if($rate->price_digits)
                                                                    <span class="price-digits">{{ $rate->price_digits }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <div class="dha-file-rates-cta-wrap">
                                                                    @if($callPhone)
                                                                        <a class="dha-file-rates-action dha-file-rates-action--call" href="tel:{{ $callPhone }}" aria-label="Call">
                                                                            <i class="fa-solid fa-phone"></i> Call
                                                                        </a>
                                                                    @endif
                                                                    @if($whatsappPhone)
                                                                        <a class="dha-file-rates-action dha-file-rates-action--wa" href="https://wa.me/{{ $whatsappPhone }}?text={{ $waText }}" target="_blank" rel="noopener" aria-label="WhatsApp">
                                                                            <i class="fa-brands fa-whatsapp"></i> WhatsApp
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="boxed-content-item etihad-empty-state dha-file-rates-empty" id="dha-file-rates-empty">
                                <p>No file rates match your filters. Try another phase or search term.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @include('partials.footer')
    </div>
</div>
@endsection

@push('scripts')
<script>
(function ($) {
    function applyDhaFileRatesFilter() {
        var phase = String($('#dha-file-rates-phase').val() || '');
        var q = String($('#dha-file-rates-q').val() || '').toLowerCase().trim();
        var anyVisible = false;

        $('#dha-file-rates-clear').prop('hidden', !(phase || q));

        $('.dha-file-rates-block').each(function () {
            var $block = $(this);
            var blockPhase = String($block.data('phase-id') || '');
            var phaseOk = !phase || blockPhase === phase;
            var visibleRows = 0;

            $block.find('.dha-file-rates-row').each(function () {
                var $row = $(this);
                var hay = String($row.data('search') || '');
                var textOk = !q || hay.indexOf(q) !== -1;
                var show = phaseOk && textOk;
                $row.toggle(show);
                if (show) visibleRows += 1;
            });

            var showBlock = phaseOk && visibleRows > 0;
            $block.toggle(showBlock);
            if (showBlock) anyVisible = true;
        });

        $('#dha-file-rates-empty').toggleClass('is-visible', !anyVisible);
    }

    $(function () {
        if (!$('#dha-file-rates-list').length) return;

        $('#dha-file-rates-phase').on('change', applyDhaFileRatesFilter);
        $('#dha-file-rates-q').on('input keyup search', applyDhaFileRatesFilter);
        $('#dha-file-rates-clear').on('click', function () {
            $('#dha-file-rates-phase').val('');
            $('#dha-file-rates-q').val('');
            applyDhaFileRatesFilter();
        });
        $('#dha-file-rates-filter').on('submit', function (e) {
            e.preventDefault();
            applyDhaFileRatesFilter();
        });
    });
})(jQuery);
</script>
@endpush
