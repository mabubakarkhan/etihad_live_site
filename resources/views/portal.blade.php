@extends('layouts.front')

@section('title', 'Portal – ' . config('app.name'))

@push('meta')
<meta name="description" content="{{ config('app.name') }} portal home.">
<link rel="canonical" href="{{ url('/portal') }}">
@endpush

@push('styles')
<style>
#portal-quick-search-wrap { position: relative; z-index: 30; overflow: visible; }
#portal-quick-search-wrap .list-searh-input-wrap-title_wrap { align-items: flex-start; }
@media (max-width: 1067px) {
    #portal-quick-search-wrap .header-search-radio { flex-wrap: wrap; }
}
/* Match listing: address suggestions = absolute dropdown (no layout jump) */
#portal-quick-search-wrap .listing-address-suggestions {
    position: absolute; left: 0; right: 0; top: 100%; margin-top: 4px;
    background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 200;
    max-height: 260px; overflow-y: auto; display: none;
    text-align: left;
}
#portal-quick-search-wrap .listing-address-suggestions.show { display: block; }
#portal-quick-search-wrap .listing-address-suggestions .suggestion-item {
    padding: 10px 14px 12px 50px; cursor: pointer; font-size: 12px; color: #1f2937;
    border-bottom: 1px solid #f1f5f9; transition: background 0.15s;
    text-align: left; line-height: 1.4;
    margin-bottom: 4px;
}
#portal-quick-search-wrap .listing-address-suggestions .suggestion-item:last-child { margin-bottom: 0; border-bottom: none; }
#portal-quick-search-wrap .listing-address-suggestions .suggestion-item:hover,
#portal-quick-search-wrap .listing-address-suggestions .suggestion-item.selected { background: #f8fafc; }
#portal-quick-search-wrap .listing-address-suggestions .suggestion-item .suggestion-match {
    font-weight: 700; color: var(--main-color, #EE7838); background: rgba(238, 120, 56, 0.08);
    padding: 0 1px; border-radius: 2px;
}
#portal-quick-search-wrap .listing-address-suggestions .suggestion-loading {
    padding: 12px 50px; color: #64748b; font-size: 12px; text-align: left;
}
/* Area dropdown (same behavior as listing page) */
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap {
    padding: 0 20px 14px 110px; position: relative; overflow: visible;
    background: #f9f9f9; border: 1px solid #eee; border-radius: 4px;
}
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap label {
    position: absolute; left: 5px; top: 24px; font-size: 0.9em; color: #666; line-height: 1.2;
    text-align: left; white-space: nowrap;
}
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap .price-rage-item,
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap .irs { margin-top: 1px; max-width: 100%; }
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap .irs-line { background: #eee; border-radius: 4px; }
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap .irs-bar,
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap .irs-slider { background: var(--main-color, #EE7838) !important; }
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap .irs-from,
#portal-quick-search-wrap .list-searh-input-wrap .listing-range-wrap .irs-to { background: #000 !important; color: #fff !important; }
#portal-quick-search-wrap .listing-range-dropdown { position: relative; width: 100%; }
#portal-quick-search-wrap .listing-range-dropdown-btn {
    display: flex; align-items: center; gap: 10px; width: 100%; min-height: 48px;
    padding: 8px 14px 8px 44px; border: 1px solid #e8e8e8; border-radius: 6px; background: #f9f9f9;
    cursor: pointer; text-align: left; transition: border-color 0.2s, box-shadow 0.2s;
    font-size: 13px; color: #1e1e1e;
}
#portal-quick-search-wrap .listing-range-dropdown-btn:hover { border-color: var(--main-color, #EE7838); }
#portal-quick-search-wrap .listing-range-dropdown-btn[aria-expanded="true"] { border-color: var(--main-color, #EE7838); box-shadow: 0 0 0 2px rgba(238, 120, 56, 0.15); }
#portal-quick-search-wrap .listing-range-dropdown-btn .listing-range-dropdown-title { font-weight: 600; flex: 0 0 auto; }
#portal-quick-search-wrap .listing-range-dropdown-btn .listing-range-dropdown-summary {
    flex: 1 1 auto; text-align: right; color: #64748b; font-weight: 500;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
#portal-quick-search-wrap .listing-range-dropdown-btn .listing-range-dropdown-caret { flex: 0 0 auto; font-size: 0.75em; color: #94a3b8; transition: transform 0.2s; }
#portal-quick-search-wrap .listing-range-dropdown-btn[aria-expanded="true"] .listing-range-dropdown-caret { transform: rotate(180deg); }
#portal-quick-search-wrap .listing-range-dropdown-panel {
    display: none; position: absolute; left: 0; right: 0; top: calc(100% + 6px);
    z-index: 200; padding: 16px; background: #fff; border: 1px solid #eee; border-radius: 10px;
    box-shadow: 0 12px 40px rgba(15, 23, 42, 0.12);
}
#portal-quick-search-wrap .listing-range-dropdown-panel.is-open { display: block; }
#portal-quick-search-wrap .listing-range-dropdown-panel .listing-range-wrap { margin-bottom: 0; }
#portal-quick-search-wrap .listing-range-dropdown-cswrap { position: relative; padding: 0 !important; border: none !important; background: transparent !important; }
#portal-quick-search-wrap .listing-range-dropdown-cswrap > i.fa-light {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%); z-index: 2;
    color: var(--main-color, #EE7838); pointer-events: none; font-size: 1.1em;
}
.home-hero-section .col-lg-4 { position: relative; z-index: 25; overflow: visible; }
</style>
@endpush

@section('content')
<div id="main">
    @include('partials.header')
    @include('partials.portal-index3-content')

    @include('partials.footer')
    </div>

    @include('partials.theme-panels')
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
                        $ma.data('ionRangeSlider').update({ from: 5, to: 20 });
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', portalQuickSearchInit);
    } else {
        portalQuickSearchInit();
    }
})();
</script>
@endpush
