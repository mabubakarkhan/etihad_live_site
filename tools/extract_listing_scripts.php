<?php
$listing = file_get_contents(__DIR__ . '/../resources/views/listing.blade.php');
$lines = explode("\n", $listing);
$script_body = implode("\n", array_slice($lines, 352, 1376 - 352));
$script_body = str_replace(
    "var listingPath = '{{ url(\"/listing\") }}';",
    "var listingPath = '{{ \$listingPath }}';",
    $script_body
);
$inject = <<<'JS'

    var defaultDhaPhaseId = @json($defaultDhaPhaseId);
    var dhaPhaseUrls = @json($dhaPhaseUrls);
    var listingResultsLabelDefault = @json($listingResultsLabel);
JS;
$script_body = preg_replace('/    var listingPath = /', $inject . "\n    var listingPath = ", $script_body, 1);
$init_patch = <<<'JS'
        applyFiltersFromUrl();
        if (defaultDhaPhaseId && !parseUrlParams().dha_phase) {
            var dhaPhaseElDefault = document.getElementById('listing-dha-phase');
            if (dhaPhaseElDefault) {
                dhaPhaseElDefault.value = String(defaultDhaPhaseId);
                if (typeof $ !== 'undefined') $(dhaPhaseElDefault).niceSelect('update');
            }
        }
        var resultsLabelEl = document.getElementById('listing-results-label');
        if (resultsLabelEl && listingResultsLabelDefault) resultsLabelEl.textContent = listingResultsLabelDefault;
JS;
$script_body = str_replace('        applyFiltersFromUrl();', $init_patch, $script_body);
$dha_change = <<<'JS'

        (function initDhaPhaseFilter() {
            function onDhaPhaseChange() {
                var dhaEl = document.getElementById('listing-dha-phase');
                if (!dhaEl) return;
                var phaseId = dhaEl.value || '';
                if (dhaPhaseUrls && phaseId && dhaPhaseUrls[phaseId]) {
                    var filters = getFiltersFromForm();
                    var qs = buildQueryString(filters);
                    var base = dhaPhaseUrls[phaseId].replace(/\?.*$/, '');
                    var newUrl = qs ? base + qs : base;
                    window.location.href = newUrl;
                    return;
                }
                var filters = getFiltersFromForm();
                var qs = buildQueryString(filters);
                var path = listingPath;
                var newUrl = qs ? path + qs : path;
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({ listingFilters: filters }, '', newUrl);
                }
                loadListings();
            }
            if (typeof $ !== 'undefined') {
                $(document).on('change', '#listing-dha-phase', onDhaPhaseChange);
            } else {
                var dhaEl = document.getElementById('listing-dha-phase');
                if (dhaEl) dhaEl.addEventListener('change', onDhaPhaseChange);
            }
        })();
JS;
$script_body = str_replace(
    "        window.addEventListener('popstate', function() {",
    $dha_change . "        window.addEventListener('popstate', function() {",
    $script_body
);
$header = <<<'BLADE'
@php
    $listingPath = $listingPath ?? url('/listing');
    $defaultDhaPhaseId = $defaultDhaPhaseId ?? null;
    $listingResultsLabel = $listingResultsLabel ?? 'Listings';
    $dhaPhaseUrls = $dhaPhaseUrls ?? [];
    $googleMapsKey = $googleMapsKey ?? 'AIzaSyAUJRbRPd-O8U1B4fIfdfq8jRAUVcbn1-Q';
    $googleMapsMapId = $googleMapsMapId ?? config('app.google_maps_map_id', 'DEMO_MAP_ID');
@endphp
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@if($googleMapsKey)
<script>
function initMap() {
    if (typeof window._updateListingMapMarkers === 'function' && window._lastListingForMap) {
        window._updateListingMapMarkers(window._lastListingForMap);
    }
    if (typeof window.__initListingLandmarkAutocomplete === 'function') {
        window.__initListingLandmarkAutocomplete();
    }
}
window.initMap = initMap;
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places,marker&callback=initMap"></script>
@endif
<script>
(function() {
    var $ = window.jQuery;
    if ($) {
        $(function() {
            $(document).on('click', '.single-map-item', function(e) { e.preventDefault(); $('.map-modal-wrap').fadeIn(400); });
            $(document).on('click', '.map-modal-close, .map-modal-wrap-overlay', function() { $('.map-modal-wrap').fadeOut(400); });
        });
    }
})();
</script>
BLADE;
$out = $header . $script_body;
file_put_contents(__DIR__ . '/../resources/views/partials/listing-page-scripts.blade.php', $out);
echo 'done ' . strlen($out) . PHP_EOL;
