@php
    $phase = $phase ?? null;
    $defaultDhaPhaseId = $phase?->id;
    $listingResultsLabel = $phase ? ('Listings in ' . $phase->title) : 'Listings';
@endphp
@if($phase)
<section class="dha-phase-listings-wrap" id="dha-phase-listings">
    <div class="container">
        <div class="main-content dha-phase-listing-main">
            <div class="boxed-container">
                @include('partials.listing-search-block', [
                    'projectTypes' => $projectTypes ?? collect(),
                    'dhaPhases' => $dhaPhases ?? collect(),
                    'lahoreCityId' => $lahoreCityId ?? null,
                    'defaultDhaPhaseId' => $defaultDhaPhaseId,
                    'listingResultsLabel' => $listingResultsLabel,
                    'hideListingFilters' => true,
                ])
            </div>
        </div>
    </div>
</section>
@endif
