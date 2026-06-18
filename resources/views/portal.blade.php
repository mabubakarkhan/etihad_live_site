@extends('layouts.front')

@php
    $cmsPage = $cmsPage ?? null;
    $portalTitle = ($cmsPage && $cmsPage->meta_title) ? $cmsPage->meta_title : ('Portal – ' . config('app.name'));
@endphp

@section('title', $portalTitle)

@push('meta')
@include('partials.seo-meta', ['seo' => seo_from_record($cmsPage, [
    'title' => $portalTitle,
    'description' => config('app.name') . ' portal home.',
    'canonical' => url('/portal'),
])])
@endpush

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Cinzel:wght@700&family=Dancing+Script:wght@700&family=Great+Vibes&family=Lobster&family=Orbitron:wght@700&family=Pacifico&family=Permanent+Marker&family=Playfair+Display:wght@700&family=Sacramento&family=Yellowtail&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('theme/css/pages/portal.css') }}">
<link rel="stylesheet" href="{{ asset('theme/css/pages/dha.css') }}">
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    @include('partials.portal-index3-content')

    @include('partials.footer')
    </div>

    @include('partials.theme-panels')
</div>

{{-- Portal hero quick search modal (form preserved for future use) --}}
<div class="portal-search-modal-wrap" id="portal-search-modal-wrap" aria-hidden="true">
    <div class="portal-search-modal-overlay"></div>
    <div class="portal-search-modal-item">
        <div class="portal-search-modal-panel">
            <button type="button" class="portal-search-modal-close" aria-label="Close search"><i class="fa-regular fa-xmark"></i></button>
            @include('partials.portal-hero-search-form')
        </div>
    </div>
</div>

{{-- Map modal for demo “On the map” links (same pattern as listing) --}}
<div class="map-modal-wrap">
    <div class="map-modal-wrap-overlay"></div>
    <div class="map-modal-item">
        <div class="map-modal-container fl-wrap">
            <h3><span>Property</span></h3>
            <div class="map-modal-close"><i class="fa-regular fa-xmark"></i></div>
            <div class="map-modal fl-wrap">
                <div id="singleMap" data-latitude="40.7" data-longitude="-73.1"></div>
                <div class="scrollContorl"></div>
            </div>
        </div>
    </div>
</div>

<div class="progress-bar-wrap">
    <div class="progress-bar color-bg"></div>
</div>
@endsection

@push('scripts')
@php
    $portalMapPoints = collect($portalMapProperties ?? [])->map(function ($p) {
        $title = data_get($p, 'title', 'Property');
        $lat = (float) data_get($p, 'latitude', 0);
        $lng = (float) data_get($p, 'longitude', 0);
        $primaryAddress = data_get($p, 'short_address') ?: data_get($p, 'address');
        $town = data_get($p, 'town');
        $city = data_get($p, 'city');
        $state = data_get($p, 'state');
        $purposeLabel = data_get($p, 'purpose_label');
        $projectTypeNames = data_get($p, 'project_type_names', []);
        $price = data_get($p, 'price');
        $detailUrl = data_get($p, 'detail_url');

        $address = trim(implode(', ', array_filter([
            $primaryAddress,
            $town,
            $city,
            $state,
        ])));

        if (!$detailUrl || !$lat || !$lng) {
            return null;
        }

        return [
            'title' => $title,
            'lat' => $lat,
            'lng' => $lng,
            'url' => $detailUrl,
            'address' => $address,
            'purpose_label' => $purposeLabel,
            'project_type_names' => is_array($projectTypeNames) ? $projectTypeNames : [],
            'price' => $price,
        ];
    })->filter()->values();
@endphp
<script>
(function () {
    var $ = window.jQuery;
    if ($) {
        $(function () {
            $(document).on('click', '.single-map-item', function (e) {
                e.preventDefault();
                $('.map-modal-wrap').fadeIn(400);
            });
            $(document).on('click', '.map-modal-close, .map-modal-wrap-overlay', function () {
                $('.map-modal-wrap').fadeOut(400);
            });
            $(document).on('click', '#portal-hero-search-open', function (e) {
                e.preventDefault();
                var $modal = $('#portal-search-modal-wrap');
                $modal.fadeIn(400).attr('aria-hidden', 'false');
                if (typeof jQuery !== 'undefined') {
                    var inst = jQuery('#portal-area-range').data('ionRangeSlider');
                    if (inst) { try { inst.update(); } catch (err) {} }
                }
            });
            $(document).on('click', '.portal-search-modal-close, .portal-search-modal-overlay', function () {
                $('#portal-search-modal-wrap').fadeOut(400).attr('aria-hidden', 'true');
            });
            $(document).on('keydown', function (e) {
                if (e.key === 'Escape' && $('#portal-search-modal-wrap').is(':visible')) {
                    $('#portal-search-modal-wrap').fadeOut(400).attr('aria-hidden', 'true');
                }
            });
        });
    }

    function initPortalRangeSlidersOnce() {
        if (window.__portalIonRangeInited) return;
        window.__portalIonRangeInited = true;
        if (typeof jQuery === 'undefined' || !jQuery.fn.ionRangeSlider) return;
        var $aIn = $('#portal-area-range-panel .listing-range-dropdown-panel-inner');
        if (!$aIn.length) return;
        var $host = $('<div class="portal-ion-temp-host" style="position:absolute;left:-9999px;top:0;width:420px;padding:20px;visibility:hidden;"></div>').appendTo('body');
        $aIn.appendTo($host);
        $('#portal-area-range').ionRangeSlider({
            type: 'double',
            onFinish: function () { $('#portal-area-range').trigger('change'); }
        });
        $('#portal-area-range-panel').append($aIn);
        $host.remove();
    }

    function updatePortalRangeSummaries() {
        if (typeof jQuery === 'undefined') return;
        var $a = $('#portal-area-range');
        var $aSum = $('#portal-area-range-summary');
        if ($a.length && $aSum.length) {
            if ($a.data('ionRangeSlider')) {
                var ar = $a.data('ionRangeSlider').result;
                if (ar) $aSum.text(ar.from + ' – ' + ar.to + ' Marla');
            } else if ($a.val()) {
                var ap = String($a.val()).split(';');
                if (ap.length >= 2) $aSum.text(ap[0] + ' – ' + ap[1] + ' Marla');
            }
        }
    }

    function bindPortalRangeDropdowns() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;
        function closeAll() {
            $('#portal-quick-search-wrap .listing-range-dropdown-panel').removeClass('is-open');
            $('#portal-area-range-toggle').attr('aria-expanded', 'false');
        }
        $(document).on('click', function (e) {
            if (!$(e.target).closest('#portal-quick-search-wrap .listing-range-dropdown').length) closeAll();
        });
        $('#portal-area-range-toggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var open = !$('#portal-area-range-panel').hasClass('is-open');
            closeAll();
            if (open) {
                $('#portal-area-range-panel').addClass('is-open');
                $(this).attr('aria-expanded', 'true');
                var inst = $('#portal-area-range').data('ionRangeSlider');
                if (inst) { try { inst.update(); } catch (err) {} }
            }
        });
    }

    function portalQuickSearchInit() {
        var wrap = document.getElementById('portal-quick-search-wrap');
        if (!wrap) return;
        if (typeof jQuery !== 'undefined') {
            initPortalRangeSlidersOnce();
            bindPortalRangeDropdowns();
            updatePortalRangeSummaries();
            $(document).on('change input', '#portal-area-range', function () {
                updatePortalRangeSummaries();
            });
        }

        var listingBase = wrap.getAttribute('data-listing-url') || '';
        var cityEl = document.getElementById('portal-default-city-id');
        var defaultCityId = cityEl && cityEl.value ? String(cityEl.value) : '';

        function buildListingUrl() {
            var purpose = 'sale';
            var pr = document.querySelector('input[name="portalPurpose"]:checked');
            if (pr && pr.value) purpose = pr.value;
            if (purpose !== 'rent' && purpose !== 'sale') purpose = 'sale';
            var projectType = '';
            var ptEl = document.getElementById('portal-project-type');
            if (ptEl && ptEl.value) projectType = String(ptEl.value);
            var addressEl = document.getElementById('portal-quick-address');
            var addressHidden = document.getElementById('portal-quick-address-value');
            var address = (addressEl && addressEl.value) ? addressEl.value.trim() : '';
            if (!address && addressHidden && addressHidden.value) address = String(addressHidden.value).trim();
            var marlaMin = '';
            var marlaMax = '';
            if (typeof jQuery !== 'undefined') {
                var $m = jQuery('#portal-area-range');
                if ($m.length && $m.data('ionRangeSlider')) {
                    var mr = $m.data('ionRangeSlider').result;
                    if (mr) {
                        marlaMin = mr.from;
                        marlaMax = mr.to;
                    }
                } else if ($m.val()) {
                    var mparts = String($m.val()).split(';');
                    if (mparts.length >= 2) {
                        marlaMin = mparts[0];
                        marlaMax = mparts[1];
                    }
                }
            }
            var q = [];
            q.push('purpose=' + encodeURIComponent(purpose));
            if (projectType) q.push('project_type=' + encodeURIComponent(projectType));
            if (defaultCityId) q.push('city=' + encodeURIComponent(defaultCityId));
            if (address) q.push('address=' + encodeURIComponent(address));
            if (marlaMin !== '' && marlaMin != null) q.push('marla_min=' + encodeURIComponent(marlaMin));
            if (marlaMax !== '' && marlaMax != null) q.push('marla_max=' + encodeURIComponent(marlaMax));
            q.push('sort=latest');
            return listingBase + (q.length ? '?' + q.join('&') : '');
        }

        var searchBtn = document.getElementById('portal-quick-search-btn');
        if (searchBtn) {
            searchBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.location.href = buildListingUrl();
            });
        }

        var resetBtn = document.getElementById('portal-quick-search-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var sale = document.getElementById('portal-purpose-sale');
                if (sale) sale.checked = true;
                var aEl = document.getElementById('portal-quick-address');
                var aHid = document.getElementById('portal-quick-address-value');
                if (aEl) aEl.value = '';
                if (aHid) aHid.value = '';
                var pt = document.getElementById('portal-project-type');
                if (pt) {
                    pt.value = '';
                    if (typeof jQuery !== 'undefined' && jQuery.fn.niceSelect) {
                        jQuery(pt).niceSelect('update');
                    }
                }
                if (typeof jQuery !== 'undefined') {
                    var $ma = jQuery('#portal-area-range');
                    if ($ma.length && $ma.data('ionRangeSlider')) {
                        $ma.data('ionRangeSlider').update({ from: 1, to: 20 });
                    }
                    updatePortalRangeSummaries();
                    jQuery('#portal-area-range-panel').removeClass('is-open');
                    jQuery('#portal-area-range-toggle').attr('aria-expanded', 'false');
                }
                var sug = document.getElementById('portal-address-suggestions');
                if (sug) {
                    sug.classList.remove('show');
                    sug.setAttribute('aria-hidden', 'true');
                    sug.innerHTML = '';
                }
            });
        }

        var portalAddrTimer = null;
        var portalAddrXhr = null;
        function portalFetchSuggestions(query, callback) {
            if (portalAddrXhr) portalAddrXhr.abort();
            if (!query || query.length < 2) { callback([]); return; }
            var url = '{{ url("/api/listing/address-suggestions") }}?q=' + encodeURIComponent(query);
            portalAddrXhr = new XMLHttpRequest();
            portalAddrXhr.open('GET', url, true);
            portalAddrXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            portalAddrXhr.setRequestHeader('Accept', 'application/json');
            portalAddrXhr.onreadystatechange = function () {
                if (portalAddrXhr.readyState !== 4) return;
                var xhr = portalAddrXhr;
                portalAddrXhr = null;
                var list = [];
                try {
                    var data = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                    list = data.suggestions || [];
                } catch (err) {}
                callback(list);
            };
            portalAddrXhr.send();
        }
        function escapeHtmlPortal(s) {
            if (!s) return '';
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
        function escapeRegexPortal(s) {
            return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }
        function highlightMatchPortal(label, query) {
            if (!label) return '';
            var q = (query && typeof query === 'string') ? query.trim() : '';
            if (!q) return escapeHtmlPortal(label);
            var words = q.split(/\s+/).filter(function (w) { return w.length > 0; });
            if (!words.length) return escapeHtmlPortal(label);
            try {
                var pattern = words.map(function (w) { return escapeRegexPortal(w); }).join('|');
                var re = new RegExp('(' + pattern + ')', 'gi');
                return String(label).replace(re, function (m) {
                    return '<span class="suggestion-match">' + escapeHtmlPortal(m) + '</span>';
                });
            } catch (e2) {
                return escapeHtmlPortal(label);
            }
        }
        function showPortalSuggestions(list, isLoading, searchQuery) {
            var box = document.getElementById('portal-address-suggestions');
            if (!box) return;
            if (isLoading) {
                box.innerHTML = '<div class="suggestion-loading">Searching…</div>';
                box.classList.add('show');
                box.setAttribute('aria-hidden', 'false');
                return;
            }
            if (!list || !list.length) {
                box.classList.remove('show');
                box.setAttribute('aria-hidden', 'true');
                box.innerHTML = '';
                return;
            }
            var qq = (searchQuery && typeof searchQuery === 'string') ? searchQuery.trim() : '';
            box.innerHTML = list.map(function (item) {
                var raw = item.label || item.value || '';
                var attr = raw.replace(/"/g, '&quot;');
                var content = highlightMatchPortal(raw, qq);
                return '<div class="suggestion-item" role="option" data-value="' + attr + '">' + content + '</div>';
            }).join('');
            box.classList.add('show');
            box.setAttribute('aria-hidden', 'false');
        }
        var addrInput = document.getElementById('portal-quick-address');
        var addrBox = document.getElementById('portal-address-suggestions');
        if (addrInput && addrBox) {
            addrInput.addEventListener('input', function () {
                var val = addrInput.value.trim();
                clearTimeout(portalAddrTimer);
                if (val.length < 2) { showPortalSuggestions([]); return; }
                showPortalSuggestions([], true);
                portalAddrTimer = setTimeout(function () {
                    portalFetchSuggestions(val, function (list) { showPortalSuggestions(list, false, val); });
                }, 280);
            });
            addrInput.addEventListener('focus', function () {
                if (addrBox.classList.contains('show') && addrBox.children.length) return;
                var val = addrInput.value.trim();
                if (val.length >= 2) {
                    showPortalSuggestions([], true);
                    portalFetchSuggestions(val, function (list) { showPortalSuggestions(list, false, val); });
                }
            });
            addrInput.addEventListener('blur', function () {
                var hidden = document.getElementById('portal-quick-address-value');
                if (hidden) hidden.value = addrInput.value ? addrInput.value.trim() : '';
            });
            addrInput.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    addrBox.classList.remove('show');
                    addrBox.setAttribute('aria-hidden', 'true');
                    return;
                }
                if (e.key === 'Enter' && !addrBox.querySelector('.suggestion-item')) {
                    e.preventDefault();
                    if (searchBtn) searchBtn.click();
                    return;
                }
                if (!addrBox.classList.contains('show') || !addrBox.children.length) return;
                var items = addrBox.querySelectorAll('.suggestion-item');
                var current = addrBox.querySelector('.suggestion-item.selected');
                var idx = current ? Array.prototype.indexOf.call(items, current) : -1;
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    idx = idx < items.length - 1 ? idx + 1 : 0;
                    items.forEach(function (el) { el.classList.remove('selected'); });
                    if (items[idx]) items[idx].classList.add('selected');
                    return;
                }
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    idx = idx <= 0 ? items.length - 1 : idx - 1;
                    items.forEach(function (el) { el.classList.remove('selected'); });
                    if (items[idx]) items[idx].classList.add('selected');
                    return;
                }
                if (e.key === 'Enter' && current) {
                    e.preventDefault();
                    var v = current.getAttribute('data-value');
                    if (v) addrInput.value = v;
                    showPortalSuggestions([]);
                }
            });
            addrBox.addEventListener('mousedown', function (e) {
                var item = e.target.closest('.suggestion-item');
                if (item) e.preventDefault();
            });
            addrBox.addEventListener('click', function (e) {
                var item = e.target.closest('.suggestion-item');
                if (!item) return;
                var v = item.getAttribute('data-value');
                if (v) {
                    addrInput.value = v;
                    var h = document.getElementById('portal-quick-address-value');
                    if (h) h.value = v;
                }
                showPortalSuggestions([]);
                addrInput.focus();
            });
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.listing-address-wrap')) showPortalSuggestions([]);
            });
        }
    }

    function portalQuickSearchInitWhy() {
        var wrap = document.getElementById('why-portal-quick-search-wrap');
        if (!wrap) return;
        if (typeof jQuery !== 'undefined' && jQuery.fn.ionRangeSlider) {
            var $ = jQuery;
            var $aIn = $('#why-portal-area-range-panel .listing-range-dropdown-panel-inner');
            if ($aIn.length) {
                var $host = $('<div style="position:absolute;left:-9999px;top:0;width:420px;padding:20px;visibility:hidden;"></div>').appendTo('body');
                $aIn.appendTo($host);
                $('#why-portal-area-range').ionRangeSlider({ type: 'double', onFinish: function () { $('#why-portal-area-range').trigger('change'); } });
                $('#why-portal-area-range-panel').append($aIn);
                $host.remove();
            }
            function updateWhySummary() {
                var $a = $('#why-portal-area-range');
                var $sum = $('#why-portal-area-range-summary');
                if (!$a.length || !$sum.length) return;
                if ($a.data('ionRangeSlider')) {
                    var ar = $a.data('ionRangeSlider').result;
                    if (ar) $sum.text(ar.from + ' – ' + ar.to + ' Marla');
                } else if ($a.val()) {
                    var ap = String($a.val()).split(';');
                    if (ap.length >= 2) $sum.text(ap[0] + ' – ' + ap[1] + ' Marla');
                }
            }
            function closeWhyDropdown() {
                $('#why-portal-quick-search-wrap .listing-range-dropdown-panel').removeClass('is-open');
                $('#why-portal-area-range-toggle').attr('aria-expanded', 'false');
            }
            $(document).on('click', function (e) {
                if (!$(e.target).closest('#why-portal-quick-search-wrap .listing-range-dropdown').length) closeWhyDropdown();
            });
            $('#why-portal-area-range-toggle').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var open = !$('#why-portal-area-range-panel').hasClass('is-open');
                closeWhyDropdown();
                if (open) {
                    $('#why-portal-area-range-panel').addClass('is-open');
                    $(this).attr('aria-expanded', 'true');
                }
            });
            $(document).on('change input', '#why-portal-area-range', updateWhySummary);
            updateWhySummary();
        }

        var listingBase = wrap.getAttribute('data-listing-url') || '';
        function buildWhyUrl() {
            var purpose = 'sale';
            var pr = document.querySelector('input[name="whyPortalPurpose"]:checked');
            if (pr && pr.value) purpose = pr.value;
            if (purpose !== 'rent' && purpose !== 'sale') purpose = 'sale';
            var ptEl = document.getElementById('why-portal-project-type');
            var addressEl = document.getElementById('why-portal-quick-address');
            var addressHidden = document.getElementById('why-portal-quick-address-value');
            var cityEl = document.getElementById('why-portal-default-city-id');
            var projectType = ptEl && ptEl.value ? String(ptEl.value) : '';
            var address = (addressEl && addressEl.value) ? addressEl.value.trim() : '';
            if (!address && addressHidden && addressHidden.value) address = String(addressHidden.value).trim();
            var defaultCityId = cityEl && cityEl.value ? String(cityEl.value) : '';
            var marlaMin = '';
            var marlaMax = '';
            if (typeof jQuery !== 'undefined') {
                var $m = jQuery('#why-portal-area-range');
                if ($m.length && $m.data('ionRangeSlider')) {
                    var mr = $m.data('ionRangeSlider').result;
                    if (mr) { marlaMin = mr.from; marlaMax = mr.to; }
                }
            }
            var q = [];
            q.push('purpose=' + encodeURIComponent(purpose));
            if (projectType) q.push('project_type=' + encodeURIComponent(projectType));
            if (defaultCityId) q.push('city=' + encodeURIComponent(defaultCityId));
            if (address) q.push('address=' + encodeURIComponent(address));
            if (marlaMin !== '') q.push('marla_min=' + encodeURIComponent(marlaMin));
            if (marlaMax !== '') q.push('marla_max=' + encodeURIComponent(marlaMax));
            q.push('sort=latest');
            return listingBase + (q.length ? '?' + q.join('&') : '');
        }

        var searchBtn = document.getElementById('why-portal-quick-search-btn');
        if (searchBtn) {
            searchBtn.addEventListener('click', function (e) {
                e.preventDefault();
                window.location.href = buildWhyUrl();
            });
        }

        var resetBtn = document.getElementById('why-portal-quick-search-reset');
        if (resetBtn) {
            resetBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var sale = document.getElementById('why-portal-purpose-sale');
                if (sale) sale.checked = true;
                var aEl = document.getElementById('why-portal-quick-address');
                var aHid = document.getElementById('why-portal-quick-address-value');
                if (aEl) aEl.value = '';
                if (aHid) aHid.value = '';
                var pt = document.getElementById('why-portal-project-type');
                if (pt) {
                    pt.value = '';
                    if (typeof jQuery !== 'undefined' && jQuery.fn.niceSelect) jQuery(pt).niceSelect('update');
                }
                if (typeof jQuery !== 'undefined') {
                    var $ma = jQuery('#why-portal-area-range');
                    if ($ma.length && $ma.data('ionRangeSlider')) $ma.data('ionRangeSlider').update({ from: 1, to: 20 });
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            portalQuickSearchInit();
            portalQuickSearchInitWhy();
        });
    } else {
        portalQuickSearchInit();
        portalQuickSearchInitWhy();
    }
})();

(function () {
    var mapPoints = @json($portalMapPoints);

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function buildCardHtml(item) {
        var purposeTag = item.purpose_label ? '<span class="listing-marker-card-tag is-primary">' + esc(item.purpose_label) + '</span>' : '';
        var pt = (item.project_type_names && item.project_type_names.length) ? item.project_type_names[0] : '';
        var typeTag = pt ? '<span class="listing-marker-card-tag is-secondary">' + esc(pt) + '</span>' : '';
        var price = item.price ? '<span class="listing-marker-card-price">' + esc(item.price) + '</span>' : '<span></span>';
        return '<div class="listing-marker-card">'
            + '<div class="listing-marker-card-head"><h4 class="listing-marker-card-title">' + esc(item.title) + '</h4></div>'
            + '<div class="listing-marker-card-body">'
            + '<div class="listing-marker-card-tags">' + purposeTag + typeTag + '</div>'
            + '<p class="listing-marker-card-address"><i class="fa-solid fa-location-dot"></i> ' + esc(item.address || '') + '</p>'
            + '<div class="listing-marker-card-foot">' + price + '<a href="' + esc(item.url) + '" class="listing-marker-card-link">View details <i class="fa-solid fa-arrow-right"></i></a></div>'
            + '</div></div>';
    }

    window.initPortalHomepageMap = function () {
        if (!window.google || !window.google.maps) return;
        var mapEl = document.getElementById('portal-home-map');
        if (!mapEl || !mapPoints.length) return;
        var center = { lat: mapPoints[0].lat, lng: mapPoints[0].lng };
        var mapOpts = {
            zoom: 12,
            center: center,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        };
        if (window.EtihadMap) EtihadMap.applyToMapOptions(mapOpts);
        var map = new google.maps.Map(mapEl, mapOpts);
        var bounds = new google.maps.LatLngBounds();
        var infowindow = new google.maps.InfoWindow();
        var closeTimer = null;
        function holdOpen() {
            if (closeTimer) { clearTimeout(closeTimer); closeTimer = null; }
        }
        function scheduleClose() {
            holdOpen();
            closeTimer = setTimeout(function () { infowindow.close(); }, 220);
        }
        mapPoints.forEach(function (item) {
            if (!item.lat || !item.lng) return;
            var pos = { lat: Number(item.lat), lng: Number(item.lng) };
            var marker = window.EtihadMap
                ? EtihadMap.createMarker({ map: map, position: pos, title: item.title || '' })
                : new google.maps.Marker({ map: map, position: pos, title: item.title || '' });
            bounds.extend(pos);
            marker.addListener('mouseover', function () {
                holdOpen();
                infowindow.setContent(buildCardHtml(item));
                infowindow.open(map, marker);
            });
            marker.addListener('click', function () {
                holdOpen();
                infowindow.setContent(buildCardHtml(item));
                infowindow.open(map, marker);
            });
            marker.addListener('mouseout', scheduleClose);
        });
        google.maps.event.addListener(infowindow, 'domready', function () {
            var iw = document.querySelector('.gm-style-iw, .gm-style-iw-d');
            if (!iw) return;
            iw.addEventListener('mouseenter', holdOpen);
            iw.addEventListener('mouseleave', scheduleClose);
        });
        if (!bounds.isEmpty()) map.fitBounds(bounds, 80);
    };

    if (mapPoints.length) {
        var key = '{{ config('app.google_maps_api_key') ?: "AIzaSyAYrLB-ltxWv32OFEF6c07B376JNrDyOIA" }}';
        if (key && !document.getElementById('portal-home-map-api')) {
            var s = document.createElement('script');
            s.id = 'portal-home-map-api';
            s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) + '&callback=initPortalHomepageMap';
            s.async = true;
            s.defer = true;
            document.head.appendChild(s);
        } else if (window.google && window.google.maps) {
            window.initPortalHomepageMap();
        }
    }

    if (typeof Swiper !== 'undefined' && document.querySelector('.dha-phase-carousel .swiper-container')) {
        new Swiper('.dha-phase-carousel .swiper-container', {
            loop: false,
            grabCursor: true,
            slidesPerView: 4,
            spaceBetween: 20,
            speed: 800,
            navigation: {
                nextEl: '.dha-phase-button-next',
                prevEl: '.dha-phase-button-prev',
            },
            breakpoints: {
                1200: { slidesPerView: 3, spaceBetween: 16 },
                768: { slidesPerView: 2, spaceBetween: 14 },
                575: { slidesPerView: 1.15, spaceBetween: 12 },
            },
        });
    }

    var etihadWord = document.getElementById('portal-hero-etihad-word');
    if (etihadWord) {
        var etihadFull = 'ETIHAD';
        var etihadFonts = [
            { family: "'Playfair Display', serif", weight: '700' },
            { family: "'Pacifico', cursive", weight: '400' },
            { family: "'Dancing Script', cursive", weight: '700' },
            { family: "'Bebas Neue', sans-serif", weight: '400' },
            { family: "'Cinzel', serif", weight: '700' },
            { family: "'Great Vibes', cursive", weight: '400' },
            { family: "'Permanent Marker', cursive", weight: '400' },
            { family: "'Orbitron', sans-serif", weight: '700' },
            { family: "'Lobster', cursive", weight: '400' },
            { family: "'Sacramento', cursive", weight: '400' },
            { family: "'Yellowtail', cursive", weight: '400' },
        ];
        var etihadIndex = 0;
        var etihadTypeMs = 90;
        var etihadDeleteMs = 70;
        var etihadPauseMs = 2400;

        function applyEtihadFont(index) {
            var font = etihadFonts[index];
            etihadWord.style.fontFamily = font.family;
            etihadWord.style.fontWeight = font.weight;
        }

        function etihadSetTyping(active) {
            etihadWord.classList.toggle('is-typing', !!active);
        }

        function etihadDelete(done) {
            etihadSetTyping(true);
            var text = etihadWord.textContent || '';
            function step() {
                if (text.length > 0) {
                    text = text.slice(0, -1);
                    etihadWord.textContent = text;
                    setTimeout(step, etihadDeleteMs);
                } else if (typeof done === 'function') {
                    done();
                }
            }
            step();
        }

        function etihadType(done) {
            etihadSetTyping(true);
            var i = 0;
            function step() {
                if (i <= etihadFull.length) {
                    etihadWord.textContent = etihadFull.slice(0, i);
                    i += 1;
                    setTimeout(step, etihadTypeMs);
                } else {
                    etihadSetTyping(false);
                    if (typeof done === 'function') {
                        done();
                    }
                }
            }
            step();
        }

        function etihadCycle() {
            etihadDelete(function () {
                etihadIndex = (etihadIndex + 1) % etihadFonts.length;
                applyEtihadFont(etihadIndex);
                etihadType(function () {
                    setTimeout(etihadCycle, etihadPauseMs);
                });
            });
        }

        applyEtihadFont(0);
        etihadWord.textContent = etihadFull;
        setTimeout(etihadCycle, etihadPauseMs);
    }
})();
</script>
@endpush
