@php
    $listingPath = $listingPath ?? url('/listing');
    $defaultDhaPhaseId = $defaultDhaPhaseId ?? null;
    $listingResultsLabel = $listingResultsLabel ?? 'Listings';
    $dhaPhaseUrls = $dhaPhaseUrls ?? [];
    $googleMapsKey = $googleMapsKey ?? 'AIzaSyAUJRbRPd-O8U1B4fIfdfq8jRAUVcbn1-Q';
    $googleMapsMapId = $googleMapsMapId ?? config('app.google_maps_map_id', 'DEMO_MAP_ID');
    $listingCs = \App\Models\ContactSetting::instance();
    $listingWaRaw = trim((string) ($listingCs->whatsapp ?? '')) ?: trim((string) ($listingCs->phone ?? ''));
    $listingWaNumber = $listingWaRaw !== '' ? preg_replace('/\D/', '', $listingWaRaw) : '';
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
</script><script>
(function() {
    function esc(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
    var $loader = document.getElementById('listing-loader');
    var $empty = document.getElementById('listing-empty');
    var $grid = document.getElementById('listing-grid');
    var $countEl = document.getElementById('listing-count');
    var $paginationWrap = document.getElementById('listing-pagination-wrap');
    var detailBaseUrl = '{{ url("/property") }}' + '/';
    var defaultAvatarUrl = '{{ asset("theme/images/avatar/1.jpg") }}';

    var defaultDhaPhaseId = @json($defaultDhaPhaseId);
    var dhaPhaseUrls = @json($dhaPhaseUrls);
    var listingResultsLabelDefault = @json($listingResultsLabel);
    var listingPath = '{{ $listingPath }}';
    var listingMapId = '{{ $googleMapsMapId }}';
    var listingWaNumber = @json($listingWaNumber);
    var listingSidebarMap = null;
    var listingSidebarMarkers = [];
    var listingSidebarInfoWindow = null;
    var defaultMapCenter = { lat: 31.5204, lng: 74.3587 };
    var defaultMapZoom = 10;
    var listingLandmarkAutocompleteInited = false;
    var listingDistanceReqToken = 0;

    function hasValidLatLng(lat, lng) {
        return lat != null && lng != null && !isNaN(parseFloat(lat)) && !isNaN(parseFloat(lng));
    }

    function getLandmarkPoint(key) {
        var input = document.getElementById('listing-location-' + key);
        var latEl = document.getElementById('listing-location-' + key + '-lat');
        var lngEl = document.getElementById('listing-location-' + key + '-lng');
        var label = input && input.value ? String(input.value).trim() : '';
        var lat = latEl && latEl.value !== '' ? parseFloat(latEl.value) : null;
        var lng = lngEl && lngEl.value !== '' ? parseFloat(lngEl.value) : null;
        if (!label || !hasValidLatLng(lat, lng)) return null;
        return { key: key.toUpperCase(), label: label, lat: lat, lng: lng };
    }

    function updateDriveBadgesInDom(resultsById) {
        document.querySelectorAll('.listing-drive-times').forEach(function (box) {
            var propertyId = box.getAttribute('data-property-id');
            var items = resultsById[propertyId] || [];
            if (!items.length) {
                box.innerHTML = '';
                return;
            }
            box.innerHTML = items.map(function (item) {
                return '<span class="listing-drive-badge"><i class="fa-light fa-car"></i>' + esc(item.text) + '</span>';
            }).join('');
        });
    }

    function computeDriveTimesForListings(properties) {
        var originA = getLandmarkPoint('a');
        var originB = getLandmarkPoint('b');
        if ((!originA && !originB) || typeof google === 'undefined' || !google.maps || !google.maps.DistanceMatrixService) {
            updateDriveBadgesInDom({});
            return;
        }
        var destinations = [];
        var propertyIndex = [];
        (properties || []).forEach(function (p) {
            if (!hasValidLatLng(p.latitude, p.longitude)) return;
            destinations.push(new google.maps.LatLng(parseFloat(p.latitude), parseFloat(p.longitude)));
            propertyIndex.push(String(p.id));
        });
        if (!destinations.length) {
            updateDriveBadgesInDom({});
            return;
        }
        var token = ++listingDistanceReqToken;
        var matrixService = new google.maps.DistanceMatrixService();
        var origins = [];
        if (originA) origins.push(new google.maps.LatLng(originA.lat, originA.lng));
        if (originB) origins.push(new google.maps.LatLng(originB.lat, originB.lng));
        var resultsById = {};
        propertyIndex.forEach(function (id) { resultsById[id] = []; });

        matrixService.getDistanceMatrix({
            origins: origins,
            destinations: destinations,
            travelMode: google.maps.TravelMode.DRIVING,
            unitSystem: google.maps.UnitSystem.METRIC
        }, function (response, status) {
            if (token !== listingDistanceReqToken) return;
            if (status !== 'OK' || !response || !response.rows) {
                updateDriveBadgesInDom(resultsById);
                return;
            }
            response.rows.forEach(function (row, originIdx) {
                var originKey = originIdx === 0 ? (originA ? 'A' : 'B') : 'B';
                (row.elements || []).forEach(function (el, destIdx) {
                    if (!el || el.status !== 'OK') return;
                    var pid = propertyIndex[destIdx];
                    if (!pid) return;
                    var duration = el.duration && el.duration.text ? el.duration.text : null;
                    if (!duration) return;
                    resultsById[pid].push({ text: duration + ' to ' + originKey });
                });
            });
            updateDriveBadgesInDom(resultsById);
        });
    }

    function initListingLandmarkAutocomplete() {
        if (listingLandmarkAutocompleteInited) return;
        if (typeof google === 'undefined' || !google.maps || !google.maps.places || !google.maps.places.Autocomplete) return;
        listingLandmarkAutocompleteInited = true;
        var lahoreCenter = new google.maps.LatLng(31.5204, 74.3587);
        var lahoreBounds = new google.maps.LatLngBounds(
            new google.maps.LatLng(31.32, 74.14),
            new google.maps.LatLng(31.72, 74.56)
        );
        [['a', 'listing-location-a'], ['b', 'listing-location-b']].forEach(function (def) {
            var id = def[1];
            var input = document.getElementById(id);
            var latEl = document.getElementById(id + '-lat');
            var lngEl = document.getElementById(id + '-lng');
            if (!input || !latEl || !lngEl) return;
            var ac = new google.maps.places.Autocomplete(input, {
                fields: ['formatted_address', 'geometry', 'name'],
                componentRestrictions: { country: 'pk' },
                bounds: lahoreBounds,
                strictBounds: false
            });
            // Keep inputs empty; only bias autocomplete to Lahore/Pakistan.
            ac.addListener('place_changed', function () {
                var place = ac.getPlace();
                if (!place || !place.geometry || !place.geometry.location) return;
                latEl.value = String(place.geometry.location.lat());
                lngEl.value = String(place.geometry.location.lng());
                if (place.formatted_address) input.value = place.formatted_address;
                else if (place.name) input.value = place.name;
            });
            input.addEventListener('input', function () {
                if (!input.value.trim()) { latEl.value = ''; lngEl.value = ''; }
            });
        });
    }
    window.__initListingLandmarkAutocomplete = initListingLandmarkAutocomplete;

    function initListingRangeSlidersOnce() {
        if (window.__listingIonRangeInited) return;
        window.__listingIonRangeInited = true;
        if (typeof jQuery === 'undefined' || !jQuery.fn.ionRangeSlider) return;
        var $ = jQuery;
        var $aIn = $('#listing-area-range-panel .listing-range-dropdown-panel-inner');
        if (!$aIn.length) return;
        var $host = $('<div class="listing-ion-temp-host" style="position:absolute;left:-9999px;top:0;width:420px;padding:20px;visibility:hidden;"></div>').appendTo('body');
        $aIn.appendTo($host);
        $('#listing-area-range').ionRangeSlider({
            type: 'double',
            onFinish: function () { $('#listing-area-range').trigger('change'); }
        });
        $('#listing-area-range-panel').append($aIn);
        $host.remove();
    }

    function updateListingRangeSummaries() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;
        var $a = $('#listing-area-range');
        var $aSum = $('#listing-area-range-summary');
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

    function bindListingRangeDropdowns() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;
        function closeAll() {
            $('.listing-range-dropdown-panel').removeClass('is-open');
            $('.listing-range-dropdown-btn').attr('aria-expanded', 'false');
        }
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.listing-range-dropdown').length) closeAll();
        });
        $('#listing-area-range-toggle').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            var open = !$('#listing-area-range-panel').hasClass('is-open');
            closeAll();
            if (open) {
                $('#listing-area-range-panel').addClass('is-open');
                $(this).attr('aria-expanded', 'true');
                var inst = $('#listing-area-range').data('ionRangeSlider');
                if (inst) { try { inst.update(); } catch (err) {} }
            }
        });
    }

    function formatListingCardPrice(price) {
        if (!price) return '';
        var s = String(price).trim();
        s = s.replace(/^PKR\s*/i, '');
        s = s.replace(/\.\d+$/, '');
        return s.trim();
    }

    function buildListingWaUrl(title, detailUrl) {
        if (!listingWaNumber) return detailUrl || '#';
        var text = encodeURIComponent('Hi, I am interested in ' + (title || 'this property'));
        return 'https://wa.me/' + listingWaNumber + '?text=' + text;
    }

    function buildListingDealerAvatarHtml(p) {
        var name = (p.dealer_name || '').trim();
        var img = (p.dealer_image_url || '').trim();
        var url = (p.dealer_url || '').trim();
        if (!name || !img || !url) return '';
        return '<a href="' + esc(url) + '" class="listing-card-dealer-avatar" aria-label="' + esc(name) + '">' +
            '<img src="' + esc(img) + '" alt="' + esc(name) + '" class="etihad-lazy-skip" loading="lazy" decoding="async">' +
            '<span class="listing-card-dealer-tooltip">' + esc(name) + '</span>' +
            '</a>';
    }

    function renderListing(properties) {
        if (!$grid) return;
        $grid.innerHTML = '';
        properties.forEach(function(p) {
            var imgBg = p.featured_image_url
                ? ' data-lazy-bg="' + esc(p.featured_image_url) + '"'
                : '';
            var lat = p.latitude || 40.7;
            var lng = p.longitude || -73.1;
            var latLngAttrs = (p.latitude != null && p.longitude != null && !isNaN(parseFloat(p.latitude)) && !isNaN(parseFloat(p.longitude)))
                ? ' data-lat="' + esc(String(p.latitude)) + '" data-lng="' + esc(String(p.longitude)) + '"' : '';
            var card = '<div class="listing-item"' + latLngAttrs + '>' +
                '<div class="geodir-category-listing">' +
                '<div class="geodir-category-img">' +
                '<a href="' + esc(p.detail_url) + '" class="geodir-category-img_item">' +
                '<div class="bg etihad-lazy etihad-lazy-bg"' + imgBg + '></div>' +
                '<div class="overlay"></div>' +
                '</a>' +
                (p.short_address ? '<div class="geodir-category-location">' +
                '<a href="#4" class="map-item tolt single-map-item" data-newlatitude="' + lat + '" data-newlongitude="' + lng + '" data-microtip-position="top" data-tooltip="On the map"><i class="fas fa-map-marker-alt"></i> ' + esc(p.short_address) + '</a>' +
                '</div>' : '') +
                '<div class="listing-card-cats">' +
                (Array.isArray(p.project_type_names) && p.project_type_names.length ? '<ul class="list-single-opt_header_cat">' + p.project_type_names.map(function(n){ return '<li><a href="#" class="cat-opt">' + esc(n) + '</a></li>'; }).join('') + '</ul>' : '') +
                (p.purpose_label ? '<ul class="list-single-opt_header_cat list-single-opt_purpose">' + '<li><a href="#" class="cat-opt">' + esc(p.purpose_label) + '</a></li>' + '</ul>' : '') +
                '</div>' +
                '<button type="button" class="geodir_save-btn tolt wishlist-btn" data-property-id="' + esc(String(p.id)) + '" data-microtip-position="left" data-tooltip="Save" aria-label="Save to wishlist"><span><i class="fa-regular fa-heart wishlist-icon"></i></span></button>' +
                buildListingDealerAvatarHtml(p) +
                '</div>' +
                '<div class="geodir-category-content">' +
                '<h3 class="listing-card-title"><a href="' + esc(p.detail_url) + '">' + esc(p.title) + '</a></h3>' +
                '<div class="listing-card-meta-row">' +
                '<div class="geodir-category-content_price listing-card-meta-price">' + esc(formatListingCardPrice(p.price)) + '</div>' +
                '<a href="' + esc(p.detail_url) + '" class="listing-card-view-details gid_link"><span>View Details</span> <i class="fa-solid fa-caret-right"></i></a>' +
                '</div>' +
                '<div class="listing-drive-times" data-property-id="' + esc(String(p.id)) + '"></div>' +
                '</div>' +
                '<div class="geodir-category-footer">' +
                '<a href="' + esc(buildListingWaUrl(p.title, p.detail_url)) + '" class="portal-project-card-wa" target="_blank" rel="noopener noreferrer">' +
                '<i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>Register Interest</span></a>' +
                '</div>' +
                '</div></div>';
            $grid.insertAdjacentHTML('beforeend', card);
        });
        if (window.EtihadLazy && typeof window.EtihadLazy.scan === 'function') {
            window.EtihadLazy.scan($grid);
        }
    }

    function hasMoreOptionsActive() {
        var bedEl = document.getElementById('listing_bedrooms');
        var bathEl = document.getElementById('listing_bathrooms');
        var kitchenEl = document.getElementById('listing_kitchen');
        if (bedEl && bedEl.value !== '' && bedEl.value !== '0') return true;
        if (bathEl && bathEl.value !== '' && bathEl.value !== '0') return true;
        if (kitchenEl && kitchenEl.value !== '' && kitchenEl.value !== '0') return true;
        var checked = document.querySelectorAll('.hidden-listing-filter input[type="checkbox"]:checked');
        return checked && checked.length > 0;
    }
    function updateMoreOptionsDot() {
        var dot = document.getElementById('more-options-dot');
        if (!dot) return;
        if (hasMoreOptionsActive()) dot.classList.add('active');
        else dot.classList.remove('active');
    }
    var addressSuggestionsTimer = null;
    var addressSuggestionsXhr = null;
    function fetchAddressSuggestions(query, callback) {
        if (addressSuggestionsXhr) addressSuggestionsXhr.abort();
        if (!query || query.length < 2) { callback([]); return; }
        var url = '{{ url("/api/listing/address-suggestions") }}?q=' + encodeURIComponent(query);
        addressSuggestionsXhr = new XMLHttpRequest();
        addressSuggestionsXhr.open('GET', url, true);
        addressSuggestionsXhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        addressSuggestionsXhr.setRequestHeader('Accept', 'application/json');
        addressSuggestionsXhr.onreadystatechange = function() {
            if (addressSuggestionsXhr.readyState !== 4) return;
            var xhr = addressSuggestionsXhr;
            addressSuggestionsXhr = null;
            var list = [];
            try {
                var data = xhr.responseText ? JSON.parse(xhr.responseText) : {};
                list = data.suggestions || [];
            } catch (e) {}
            callback(list);
        };
        addressSuggestionsXhr.send();
    }
    function escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
    function escapeRegex(s) {
        return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    function highlightMatch(label, query) {
        if (!label) return '';
        var safe = escapeHtml(label);
        var q = (query && typeof query === 'string') ? query.trim() : '';
        if (!q) return safe;
        var words = q.split(/\s+/).filter(function(w) { return w.length > 0; });
        if (words.length === 0) return safe;
        try {
            var pattern = words.map(function(w) { return escapeRegex(w); }).join('|');
            var re = new RegExp('(' + pattern + ')', 'gi');
            return label.replace(re, function(m) { return '<span class="suggestion-match">' + escapeHtml(m) + '</span>'; });
        } catch (e) {
            return safe;
        }
    }
    function showAddressSuggestions(list, isLoading, searchQuery) {
        var box = document.getElementById('listing-address-suggestions');
        if (!box) return;
        if (isLoading) {
            box.innerHTML = '<div class="suggestion-loading">Searching...</div>';
            box.classList.add('show');
            box.setAttribute('aria-hidden', 'false');
            return;
        }
        if (!list || list.length === 0) {
            box.classList.remove('show');
            box.setAttribute('aria-hidden', 'true');
            box.innerHTML = '';
            return;
        }
        var q = (searchQuery && typeof searchQuery === 'string') ? searchQuery.trim() : '';
        box.innerHTML = list.map(function(item) {
            var raw = item.label || item.value || '';
            var attr = raw.replace(/"/g, '&quot;');
            var content = highlightMatch(raw, q);
            return '<div class="suggestion-item" role="option" data-value="' + attr + '">' + content + '</div>';
        }).join('');
        box.classList.add('show');
        box.setAttribute('aria-hidden', 'false');
    }
    function initAddressAutocomplete() {
        var input = document.getElementById('listing-address');
        var box = document.getElementById('listing-address-suggestions');
        if (!input || !box) return;
        input.addEventListener('input', function() {
            var val = input.value.trim();
            clearTimeout(addressSuggestionsTimer);
            if (val.length < 2) { showAddressSuggestions([]); return; }
            showAddressSuggestions([], true);
            addressSuggestionsTimer = setTimeout(function() {
                fetchAddressSuggestions(val, function(list) { showAddressSuggestions(list, false, val); });
            }, 280);
        });
        input.addEventListener('focus', function() {
            if (box.classList.contains('show') && box.children.length) return;
            var val = input.value.trim();
            if (val.length >= 2) {
                showAddressSuggestions([], true);
                fetchAddressSuggestions(val, function(list) { showAddressSuggestions(list, false, val); });
            }
        });
        input.addEventListener('blur', function() {
            var hidden = document.getElementById('listing-address-value');
            if (hidden) hidden.value = input.value ? input.value.trim() : '';
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { box.classList.remove('show'); box.setAttribute('aria-hidden', 'true'); return; }
            if (!box.classList.contains('show') || !box.children.length) return;
            var items = box.querySelectorAll('.suggestion-item');
            var current = box.querySelector('.suggestion-item.selected');
            var idx = current ? Array.prototype.indexOf.call(items, current) : -1;
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                idx = idx < items.length - 1 ? idx + 1 : 0;
                items.forEach(function(el) { el.classList.remove('selected'); });
                if (items[idx]) items[idx].classList.add('selected');
                return;
            }
            if (e.key === 'ArrowUp') {
                e.preventDefault();
                idx = idx <= 0 ? items.length - 1 : idx - 1;
                items.forEach(function(el) { el.classList.remove('selected'); });
                if (items[idx]) items[idx].classList.add('selected');
                return;
            }
            if (e.key === 'Enter' && current) {
                e.preventDefault();
                var value = current.getAttribute('data-value');
                if (value) input.value = value;
                showAddressSuggestions([]);
            }
        });
        box.addEventListener('click', function(e) {
            var item = e.target.closest('.suggestion-item');
            if (!item) return;
            var value = item.getAttribute('data-value');
            if (value) {
                input.value = value;
                var hidden = document.getElementById('listing-address-value');
                if (hidden) hidden.value = value;
            }
            showAddressSuggestions([]);
            input.focus();
        });
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.listing-address-wrap')) showAddressSuggestions([]);
        });
    }
    function getFiltersFromForm() {
        var purpose = '';
        var purposeRadio = document.querySelector('input[name="listing_purpose"]:checked');
        if (purposeRadio) purpose = purposeRadio.value || '';
        var projectTypeEl = document.getElementById('listing-project-type');
        var projectType = (projectTypeEl && projectTypeEl.value) ? projectTypeEl.value : '';
        var dhaPhaseEl = document.getElementById('listing-dha-phase');
        var dhaPhase = (dhaPhaseEl && dhaPhaseEl.value) ? dhaPhaseEl.value : '';
        var city = '';
        var cityHiddenEl = document.getElementById('listing-default-city-id');
        if (cityHiddenEl && cityHiddenEl.value) city = String(cityHiddenEl.value);
        else if (window.listingCitySelect && window.listingCitySelect.getValue) city = window.listingCitySelect.getValue() || '';
        var marlaMin = '', marlaMax = '';
        var sortEl = document.getElementById('listing-sort');
        var sort = 'latest';
        if (sortEl) {
            var opt = sortEl.options[sortEl.selectedIndex];
            sort = (opt && opt.value) ? opt.value : (sortEl.value || 'latest');
        }
        var addressEl = document.getElementById('listing-address');
        var addressValueEl = document.getElementById('listing-address-value');
        var address = (addressEl && addressEl.value) ? addressEl.value.trim() : '';
        if (!address && addressValueEl && addressValueEl.value) address = addressValueEl.value.trim();
        var bedrooms = '', bathrooms = '', kitchen = '';
        var bedEl = document.getElementById('listing_bedrooms');
        var bathEl = document.getElementById('listing_bathrooms');
        var kitchenEl = document.getElementById('listing_kitchen');
        if (bedEl && bedEl.value !== '' && bedEl.value !== '0') bedrooms = bedEl.value;
        if (bathEl && bathEl.value !== '' && bathEl.value !== '0') bathrooms = bathEl.value;
        if (kitchenEl && kitchenEl.value !== '' && kitchenEl.value !== '0') kitchen = kitchenEl.value;
        if (typeof $ !== 'undefined') {
            var $marla = $('#listing-area-range');
            if ($marla.length && $marla.data('ionRangeSlider')) {
                var mr = $marla.data('ionRangeSlider').result;
                if (mr) { marlaMin = mr.from; marlaMax = mr.to; }
            }
        }
        if ((marlaMin === '' || marlaMax === '') && typeof $ !== 'undefined') {
            var $marlaIn = $('#listing-area-range');
            if ($marlaIn.length && $marlaIn.val()) {
                var mparts = String($marlaIn.val()).split(';');
                if (mparts.length >= 2) {
                    var m0 = parseFloat(mparts[0]), m1 = parseFloat(mparts[1]);
                    if (!isNaN(m0)) marlaMin = m0;
                    if (!isNaN(m1)) marlaMax = m1;
                }
            }
        }
        var locAEl = document.getElementById('listing-location-a');
        var locALatEl = document.getElementById('listing-location-a-lat');
        var locALngEl = document.getElementById('listing-location-a-lng');
        var locBEl = document.getElementById('listing-location-b');
        var locBLatEl = document.getElementById('listing-location-b-lat');
        var locBLngEl = document.getElementById('listing-location-b-lng');
        var locationA = locAEl && locAEl.value ? locAEl.value.trim() : '';
        var locationALat = locALatEl && locALatEl.value ? locALatEl.value : '';
        var locationALng = locALngEl && locALngEl.value ? locALngEl.value : '';
        var locationB = locBEl && locBEl.value ? locBEl.value.trim() : '';
        var locationBLat = locBLatEl && locBLatEl.value ? locBLatEl.value : '';
        var locationBLng = locBLngEl && locBLngEl.value ? locBLngEl.value : '';
        return { purpose: purpose, project_type: projectType, dha_phase: dhaPhase, city: city, address: address, marla_min: marlaMin, marla_max: marlaMax, bedrooms: bedrooms, bathrooms: bathrooms, kitchen: kitchen, sort: sort, location_a: locationA, location_a_lat: locationALat, location_a_lng: locationALng, location_b: locationB, location_b_lat: locationBLat, location_b_lng: locationBLng };
    }

    function buildQueryString(filters) {
        var q = [];
        if (filters.purpose) q.push('purpose=' + encodeURIComponent(filters.purpose));
        if (filters.project_type) q.push('project_type=' + encodeURIComponent(filters.project_type));
        if (filters.dha_phase) q.push('dha_phase=' + encodeURIComponent(filters.dha_phase));
        if (filters.city) q.push('city=' + encodeURIComponent(filters.city));
        if (filters.address) q.push('address=' + encodeURIComponent(filters.address));
        if (filters.marla_min !== '' && filters.marla_min != null) q.push('marla_min=' + encodeURIComponent(filters.marla_min));
        if (filters.marla_max !== '' && filters.marla_max != null) q.push('marla_max=' + encodeURIComponent(filters.marla_max));
        if (filters.bedrooms) q.push('bedrooms=' + encodeURIComponent(filters.bedrooms));
        if (filters.bathrooms) q.push('bathrooms=' + encodeURIComponent(filters.bathrooms));
        if (filters.kitchen) q.push('kitchen=' + encodeURIComponent(filters.kitchen));
        if (filters.location_a) q.push('location_a=' + encodeURIComponent(filters.location_a));
        if (filters.location_a_lat) q.push('location_a_lat=' + encodeURIComponent(filters.location_a_lat));
        if (filters.location_a_lng) q.push('location_a_lng=' + encodeURIComponent(filters.location_a_lng));
        if (filters.location_b) q.push('location_b=' + encodeURIComponent(filters.location_b));
        if (filters.location_b_lat) q.push('location_b_lat=' + encodeURIComponent(filters.location_b_lat));
        if (filters.location_b_lng) q.push('location_b_lng=' + encodeURIComponent(filters.location_b_lng));
        q.push('sort=' + encodeURIComponent(filters.sort || 'latest'));
        return '?' + q.join('&');
    }

    function parseUrlParams() {
        var params = {};
        var search = window.location.search;
        if (!search) return params;
        search.slice(1).split('&').forEach(function(pair) {
            var i = pair.indexOf('=');
            var k = i >= 0 ? decodeURIComponent(pair.slice(0, i)) : pair;
            var v = i >= 0 ? decodeURIComponent(pair.slice(i + 1)) : '';
            params[k] = v;
        });
        return params;
    }

    function applyFiltersFromUrl() {
        var params = parseUrlParams();
        var purpose = params.purpose || 'sale';
        if (purpose === 'rent' || purpose === 'sale') {
            var radio = document.querySelector('input[name="listing_purpose"][value="' + purpose + '"]');
            if (radio) radio.checked = true;
        }
        var projectTypeEl = document.getElementById('listing-project-type');
        if (projectTypeEl && params.project_type) {
            projectTypeEl.value = params.project_type;
            if (typeof $ !== 'undefined') $(projectTypeEl).niceSelect('update');
        }
        var dhaPhaseEl = document.getElementById('listing-dha-phase');
        if (dhaPhaseEl && params.dha_phase) {
            dhaPhaseEl.value = params.dha_phase;
            if (typeof $ !== 'undefined') $(dhaPhaseEl).niceSelect('update');
        }
        var cityHiddenApply = document.getElementById('listing-default-city-id');
        if (cityHiddenApply) {
            if (params.city) {
                cityHiddenApply.value = params.city;
            } else {
                var defCity = cityHiddenApply.getAttribute('data-default');
                if (defCity !== null && defCity !== '') cityHiddenApply.value = defCity;
            }
        } else if (window.listingCitySelect && params.city) {
            window.listingCitySelect.setValue(params.city);
        }
        var addressEl = document.getElementById('listing-address');
        var addressValueEl = document.getElementById('listing-address-value');
        if (params.address) {
            if (addressEl) addressEl.value = params.address;
            if (addressValueEl) addressValueEl.value = params.address;
        }
        var locAEl = document.getElementById('listing-location-a');
        var locALatEl = document.getElementById('listing-location-a-lat');
        var locALngEl = document.getElementById('listing-location-a-lng');
        var locBEl = document.getElementById('listing-location-b');
        var locBLatEl = document.getElementById('listing-location-b-lat');
        var locBLngEl = document.getElementById('listing-location-b-lng');
        if (locAEl && params.location_a) locAEl.value = params.location_a;
        if (locALatEl && params.location_a_lat) locALatEl.value = params.location_a_lat;
        if (locALngEl && params.location_a_lng) locALngEl.value = params.location_a_lng;
        if (locBEl && params.location_b) locBEl.value = params.location_b;
        if (locBLatEl && params.location_b_lat) locBLatEl.value = params.location_b_lat;
        if (locBLngEl && params.location_b_lng) locBLngEl.value = params.location_b_lng;
        if (typeof $ !== 'undefined') {
            var $marla = $('#listing-area-range');
            if ($marla.length && $marla.data('ionRangeSlider')) {
                var marlaMin = params.marla_min ? parseFloat(params.marla_min) : 1;
                var marlaMax = params.marla_max ? parseFloat(params.marla_max) : 20;
                $marla.data('ionRangeSlider').update({ from: marlaMin, to: marlaMax });
            }
            updateListingRangeSummaries();
        }
        var sortEl = document.getElementById('listing-sort');
        if (sortEl && params.sort) {
            sortEl.value = params.sort;
            if (typeof $ !== 'undefined') $(sortEl).niceSelect('update');
        }
        var bedEl = document.getElementById('listing_bedrooms');
        if (bedEl && params.bedrooms !== undefined) bedEl.value = params.bedrooms;
        var bathEl = document.getElementById('listing_bathrooms');
        if (bathEl && params.bathrooms !== undefined) bathEl.value = params.bathrooms;
        var kitchenEl = document.getElementById('listing_kitchen');
        if (kitchenEl && params.kitchen !== undefined) kitchenEl.value = params.kitchen;
    }

    function loadListings() {
        if (!$loader || !$grid) return;
        var filters = getFiltersFromForm();
        var baseUrl = '{{ url("/api/listing/dealers") }}';
        var q = [];
        if (filters.purpose) q.push('purpose=' + encodeURIComponent(filters.purpose));
        if (filters.project_type) q.push('project_type=' + encodeURIComponent(filters.project_type));
        if (filters.dha_phase) q.push('dha_phase=' + encodeURIComponent(filters.dha_phase));
        if (filters.city) q.push('city=' + encodeURIComponent(filters.city));
        if (filters.address) q.push('address=' + encodeURIComponent(filters.address));
        if (filters.marla_min !== '' && filters.marla_min != null) q.push('marla_min=' + encodeURIComponent(filters.marla_min));
        if (filters.marla_max !== '' && filters.marla_max != null) q.push('marla_max=' + encodeURIComponent(filters.marla_max));
        if (filters.bedrooms) q.push('bedrooms=' + encodeURIComponent(filters.bedrooms));
        if (filters.bathrooms) q.push('bathrooms=' + encodeURIComponent(filters.bathrooms));
        if (filters.kitchen) q.push('kitchen=' + encodeURIComponent(filters.kitchen));
        q.push('sort=' + encodeURIComponent(filters.sort || 'latest'));
        var url = baseUrl + '?' + q.join('&');
        $loader.classList.remove('etihad-is-hidden');
        $grid.classList.add('etihad-is-hidden');
        if ($empty) $empty.classList.add('etihad-is-hidden');
        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState !== 4) return;
            if ($loader) $loader.classList.add('etihad-is-hidden');
            var data;
            try {
                data = xhr.responseText ? JSON.parse(xhr.responseText) : {};
            } catch (e) {
                data = { properties: [], count: 0 };
            }
            var list = data.properties || [];
            var count = data.count != null ? data.count : list.length;
            if ($countEl) $countEl.textContent = count;
            if (list.length === 0) {
                if ($empty) $empty.classList.remove('etihad-is-hidden');
            } else {
                if ($empty) $empty.classList.add('etihad-is-hidden');
                $grid.classList.remove('etihad-is-hidden');
                renderListing(list);
                if (window.__etihadWishlistSyncUi) {
                    try { window.__etihadWishlistSyncUi(); } catch (e) {}
                }
            }
            if (typeof window.__etihadSyncHeightEmulator === 'function') {
                try { window.__etihadSyncHeightEmulator(); } catch (e) {}
            }
            updateListingMapMarkers(list);
            computeDriveTimesForListings(list);
            updateMoreOptionsDot();
        };
        xhr.send();
    }

    function showMapError(msg) {
        var mapEl = document.getElementById('listing-sidebar-map');
        if (!mapEl) return;
        mapEl.innerHTML = '<div class="listing-map-error" style="display:flex;align-items:center;justify-content:center;height:100%;padding:16px;text-align:center;color:#64748b;font-size:13px;line-height:1.5;">' + (msg || 'Map unavailable.') + '</div>';
    }
    function updateListingMapMarkers(properties) {
        window._lastListingForMap = properties || [];
        window._updateListingMapMarkers = updateListingMapMarkers;
        var mapEl = document.getElementById('listing-sidebar-map');
        if (!mapEl) return;
        if (typeof google === 'undefined' || !google.maps) return;
        if (mapEl.offsetWidth <= 0 || mapEl.offsetHeight <= 0) {
            clearTimeout(window._listingMapDeferred);
            window._listingMapDeferred = setTimeout(function() {
                window._listingMapDeferred = null;
                updateListingMapMarkers(window._lastListingForMap);
            }, 200);
            return;
        }
        try {
            if (!listingSidebarMap) {
                var mapOpts = {
                    zoom: defaultMapZoom,
                    center: defaultMapCenter,
                    scrollwheel: false,
                    mapTypeControl: true,
                    streetViewControl: false,
                    fullscreenControl: true,
                    mapId: listingMapId
                };
                if (window.EtihadMap) {
                    EtihadMap.applyToMapOptions(mapOpts, 'minimal');
                } else if (listingMapId && listingMapId !== 'DEMO_MAP_ID') {
                    mapOpts.mapId = listingMapId;
                }
                listingSidebarMap = new google.maps.Map(mapEl, mapOpts);
                listingSidebarInfoWindow = new google.maps.InfoWindow();
            }
            listingSidebarMarkers.forEach(function(m) {
                if (m.setMap) m.setMap(null);
                else if (m.map !== undefined) m.map = null;
            });
            listingSidebarMarkers = [];
            var bounds = null;
            function buildMarkerInfoContent(p) {
                if (!p) return '<div class="listing-marker-card"><div class="listing-marker-card-header"><h2 class="listing-marker-card-title">Property</h2></div></div>';
                var parts = [p.address || p.short_address || '', p.town || '', p.city || '', p.state || ''].filter(function(s) { return s && String(s).trim(); });
                var fullAddress = parts.length ? parts.join(', ') : (p.short_address || '');
                var projectTypes = Array.isArray(p.project_type_names) && p.project_type_names.length ? p.project_type_names : [];
                var tagPurpose = p.purpose_label ? '<span class="listing-marker-card-tag tag-primary">' + esc(p.purpose_label) + '</span>' : '';
                var tagProject = projectTypes.length ? '<span class="listing-marker-card-tag tag-secondary">' + esc(projectTypes.join(', ')) + '</span>' : '';
                var tagsHtml = (tagPurpose || tagProject) ? '<div class="listing-marker-card-tags">' + tagPurpose + tagProject + '</div>' : '';
                var locationHtml = fullAddress ? '<div class="listing-marker-card-location"><i class="fa-solid fa-location-dot"></i><span>' + esc(fullAddress) + '</span></div>' : '';
                var priceHtml = p.price ? '<span class="listing-marker-card-price">' + esc(p.price) + '</span>' : '';
                var linkHtml = p.detail_url ? '<a href="' + esc(p.detail_url) + '" class="listing-marker-card-link">View details <i class="fa-solid fa-arrow-right"></i></a>' : '';
                var footerHtml = (priceHtml || linkHtml) ? '<div class="listing-marker-card-footer">' + priceHtml + linkHtml + '</div>' : '';
                return '<div class="listing-marker-card">' +
                    '<div class="listing-marker-card-header"><h2 class="listing-marker-card-title">' + esc(p.title || 'Property') + '</h2></div>' +
                    '<div class="listing-marker-card-body">' + tagsHtml + locationHtml + footerHtml + '</div></div>';
            }
            window._listingBuildMarkerInfoContent = buildMarkerInfoContent;
            var useAdvancedMarker = listingMapId && listingMapId !== 'DEMO_MAP_ID' && typeof google.maps.marker !== 'undefined' && google.maps.marker.AdvancedMarkerElement;
            (properties || []).forEach(function(p) {
                var lat = parseFloat(p.latitude);
                var lng = parseFloat(p.longitude);
                if (isNaN(lat) || isNaN(lng)) return;
                var pos = { lat: lat, lng: lng };
                var marker;
                if (useAdvancedMarker) {
                    marker = new google.maps.marker.AdvancedMarkerElement({
                        map: listingSidebarMap,
                        position: pos,
                        title: p.title || ''
                    });
                } else if (window.EtihadMap) {
                    marker = EtihadMap.createMarker({
                        position: pos,
                        map: listingSidebarMap,
                        title: p.title || ''
                    });
                } else {
                    marker = new google.maps.Marker({
                        position: pos,
                        map: listingSidebarMap,
                        title: p.title || ''
                    });
                }
                marker._property = p;
                var markerPos = pos;
                function openMarkerInfo() {
                    if (!listingSidebarInfoWindow || !marker._property) return;
                    listingSidebarInfoWindow.setContent(buildMarkerInfoContent(marker._property));
                    if (marker.getPosition && typeof marker.getPosition === 'function') {
                        listingSidebarInfoWindow.open(listingSidebarMap, marker);
                    } else {
                        listingSidebarInfoWindow.setPosition(markerPos);
                        listingSidebarInfoWindow.open(listingSidebarMap);
                    }
                }
                var markerInfoCloseTimer = null;
                function closeMarkerInfo() {
                    if (markerInfoCloseTimer) clearTimeout(markerInfoCloseTimer);
                    markerInfoCloseTimer = setTimeout(function() {
                        markerInfoCloseTimer = null;
                        if (listingSidebarInfoWindow) listingSidebarInfoWindow.close();
                    }, 150);
                }
                function cancelCloseMarkerInfo() {
                    if (markerInfoCloseTimer) {
                        clearTimeout(markerInfoCloseTimer);
                        markerInfoCloseTimer = null;
                    }
                }
                if (marker.addListener) {
                    marker.addListener('mouseover', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.addListener('mouseout', closeMarkerInfo);
                }
                if (marker.content && typeof marker.content.addEventListener === 'function') {
                    marker.content.addEventListener('mouseenter', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.content.addEventListener('mouseleave', closeMarkerInfo);
                } else if (marker.addEventListener) {
                    marker.addEventListener('mouseenter', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.addEventListener('mouseleave', closeMarkerInfo);
                    marker.addEventListener('mouseover', function() { cancelCloseMarkerInfo(); openMarkerInfo(); });
                    marker.addEventListener('mouseout', closeMarkerInfo);
                }
                if (marker.addListener) {
                    marker.addListener('click', function() {
                        cancelCloseMarkerInfo();
                        openMarkerInfo();
                    });
                }
                if (marker.addEventListener) {
                    marker.addEventListener('click', function() {
                        cancelCloseMarkerInfo();
                        openMarkerInfo();
                    });
                }
                if (marker.content && marker.content.addEventListener) {
                    marker.content.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        cancelCloseMarkerInfo();
                        openMarkerInfo();
                    });
                }
                listingSidebarMarkers.push(marker);
                if (!bounds) bounds = new google.maps.LatLngBounds();
                bounds.extend(pos);
            });
            if (bounds && listingSidebarMarkers.length > 0) {
                listingSidebarMap.fitBounds(bounds, { top: 20, right: 20, bottom: 20, left: 20 });
            } else {
                listingSidebarMap.setCenter(defaultMapCenter);
                listingSidebarMap.setZoom(defaultMapZoom);
            }
            try { google.maps.event.trigger(listingSidebarMap, 'resize'); } catch (e) {}
        } catch (err) {
            var isReferrer = (err && (err.message || '').indexOf('RefererNotAllowed') !== -1);
            showMapError(isReferrer
                ? 'Map could not load. In Google Cloud Console, add this URL to your API key\u2019s allowed referrers: ' + (window.location.origin || '') + '/*'
                : 'Map could not load. Check the console for details.');
        }
    }
    window._updateListingMapMarkers = updateListingMapMarkers;

    var listingCardHoverBounceTimer = null;
    var listingHighlightedMarker = null;
    function initListingCardMapHover() {
        var grid = document.getElementById('listing-grid');
        if (!grid) return;
        grid.removeEventListener('mouseover', _onListingCardMouseOver);
        grid.removeEventListener('mouseout', _onListingCardMouseOut);
        grid.addEventListener('mouseover', _onListingCardMouseOver);
        grid.addEventListener('mouseout', _onListingCardMouseOut);
    }
    function _onListingCardMouseOver(e) {
        var card = e.target && e.target.closest ? e.target.closest('.listing-item') : null;
        if (!card) return;
        var lat = card.getAttribute('data-lat');
        var lng = card.getAttribute('data-lng');
        if (lat === null || lat === '' || lng === null || lng === '') return;
        var latN = parseFloat(lat);
        var lngN = parseFloat(lng);
        if (isNaN(latN) || isNaN(lngN)) return;
        if (!listingSidebarMap || !listingSidebarMarkers.length) return;
        var pos = { lat: latN, lng: lngN };
        var marker = null;
        function getLatLng(p) {
            if (!p) return null;
            var la = typeof p.lat === 'function' ? p.lat() : p.lat;
            var ln = typeof p.lng === 'function' ? p.lng() : p.lng;
            return (la != null && ln != null) ? { lat: Number(la), lng: Number(ln) } : null;
        }
        for (var i = 0; i < listingSidebarMarkers.length; i++) {
            var m = listingSidebarMarkers[i];
            var p = getLatLng(m.getPosition ? m.getPosition() : m.position);
            if (p && Math.abs(p.lat - latN) < 1e-5 && Math.abs(p.lng - lngN) < 1e-5) {
                marker = m;
                break;
            }
        }
        if (!marker) return;
        if (listingCardHoverBounceTimer) clearTimeout(listingCardHoverBounceTimer);
        if (listingHighlightedMarker) {
            if (listingHighlightedMarker.content) listingHighlightedMarker.content.classList.remove('listing-marker-highlight');
            if (listingHighlightedMarker.setZIndex) listingHighlightedMarker.setZIndex(null);
        }
        listingHighlightedMarker = marker;
        if (marker.content) marker.content.classList.add('listing-marker-highlight');
        if (marker.setZIndex) marker.setZIndex(999);
        listingSidebarMap.panTo(pos);
        if (listingSidebarInfoWindow && marker._property && typeof window._listingBuildMarkerInfoContent === 'function') {
            listingSidebarInfoWindow.setContent(window._listingBuildMarkerInfoContent(marker._property));
            if (marker.getPosition && typeof marker.getPosition === 'function') {
                listingSidebarInfoWindow.open(listingSidebarMap, marker);
            } else {
                listingSidebarInfoWindow.setPosition(pos);
                listingSidebarInfoWindow.open(listingSidebarMap);
            }
        }
    }
    function _onListingCardMouseOut(e) {
        var card = e.target && e.target.closest ? e.target.closest('.listing-item') : null;
        if (!card) return;
        var grid = document.getElementById('listing-grid');
        var related = e.relatedTarget;
        if (grid && related && grid.contains(related) && related.closest && related.closest('.listing-item')) return;
        if (listingCardHoverBounceTimer) {
            clearTimeout(listingCardHoverBounceTimer);
            listingCardHoverBounceTimer = null;
        }
        if (listingHighlightedMarker) {
            if (listingHighlightedMarker.content) listingHighlightedMarker.content.classList.remove('listing-marker-highlight');
            if (listingHighlightedMarker.setZIndex) listingHighlightedMarker.setZIndex(null);
            listingHighlightedMarker = null;
        }
        if (listingSidebarInfoWindow) listingSidebarInfoWindow.close();
    }

    function updateListingUrl() {
        var filters = getFiltersFromForm();
        var qs = buildQueryString(filters);
        var path = listingPath;
        var newUrl = qs ? path + qs : path.replace(/\?.*$/, '');
        if (window.history && window.history.replaceState) {
            window.history.replaceState({ listingFilters: filters }, '', newUrl);
        }
    }

    function onSearchClick() {
        var filters = getFiltersFromForm();
        var qs = buildQueryString(filters);
        var path = listingPath;
        var newUrl = qs ? path + qs : path;
        if (window.history && window.history.pushState) {
            window.history.pushState({ listingFilters: filters }, '', newUrl);
        }
        updateMoreOptionsDot();
        loadListings();
    }

    function onResetClick() {
        var saleRadio = document.querySelector('input[name="listing_purpose"][value="sale"]');
        if (saleRadio) saleRadio.checked = true;
        var addressEl = document.getElementById('listing-address');
        var addressValueEl = document.getElementById('listing-address-value');
        if (addressEl) addressEl.value = '';
        if (addressValueEl) addressValueEl.value = '';
        var locAResetEl = document.getElementById('listing-location-a');
        var locALatResetEl = document.getElementById('listing-location-a-lat');
        var locALngResetEl = document.getElementById('listing-location-a-lng');
        var locBResetEl = document.getElementById('listing-location-b');
        var locBLatResetEl = document.getElementById('listing-location-b-lat');
        var locBLngResetEl = document.getElementById('listing-location-b-lng');
        if (locAResetEl) locAResetEl.value = '';
        if (locALatResetEl) locALatResetEl.value = '';
        if (locALngResetEl) locALngResetEl.value = '';
        if (locBResetEl) locBResetEl.value = '';
        if (locBLatResetEl) locBLatResetEl.value = '';
        if (locBLngResetEl) locBLngResetEl.value = '';
        var projectTypeEl = document.getElementById('listing-project-type');
        if (projectTypeEl) {
            projectTypeEl.value = '';
            if (typeof $ !== 'undefined') $(projectTypeEl).niceSelect('update');
        }
        var dhaPhaseResetEl = document.getElementById('listing-dha-phase');
        if (dhaPhaseResetEl) {
            dhaPhaseResetEl.value = defaultDhaPhaseId ? String(defaultDhaPhaseId) : '';
            if (typeof $ !== 'undefined') $(dhaPhaseResetEl).niceSelect('update');
        }
        var cityHiddenReset = document.getElementById('listing-default-city-id');
        if (cityHiddenReset) {
            var defCity = cityHiddenReset.getAttribute('data-default');
            if (defCity !== null && defCity !== '') cityHiddenReset.value = defCity;
        } else if (window.listingCitySelect) window.listingCitySelect.clear();
        if (typeof $ !== 'undefined') {
            var $marla = $('#listing-area-range');
            if ($marla.length && $marla.data('ionRangeSlider')) {
                $marla.data('ionRangeSlider').update({ from: 1, to: 20 });
            }
            updateListingRangeSummaries();
        }
        var sortEl = document.getElementById('listing-sort');
        if (sortEl) { sortEl.value = 'latest'; if (typeof $ !== 'undefined') $(sortEl).niceSelect('update'); }
        var bedEl = document.getElementById('listing_bedrooms');
        if (bedEl) bedEl.value = '0';
        var bathEl = document.getElementById('listing_bathrooms');
        if (bathEl) bathEl.value = '0';
        var kitchenEl = document.getElementById('listing_kitchen');
        if (kitchenEl) kitchenEl.value = '0';
        document.querySelectorAll('.hidden-listing-filter input[type="checkbox"]').forEach(function(cb) { cb.checked = false; });
        updateMoreOptionsDot();
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, '', listingPath);
        }
        loadListings();
    }

    function initListingFilters() {
        var cityEl = document.getElementById('listing-city');
        if (cityEl && typeof TomSelect !== 'undefined') {
            window.listingCitySelect = new TomSelect(cityEl, {
                create: false,
                sortField: { field: 'text', direction: 'asc' },
                placeholder: 'All Cities',
                maxOptions: null
            });
        }
        initListingRangeSlidersOnce();
        if (typeof jQuery !== 'undefined') bindListingRangeDropdowns();
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
        if (typeof jQuery !== 'undefined') updateListingRangeSummaries();
        updateMoreOptionsDot();
        initAddressAutocomplete();
        initListingLandmarkAutocomplete();
        initListingCardMapHover();
        loadListings();
        var searchBtn = document.getElementById('listing-search-btn');
        if (searchBtn) searchBtn.addEventListener('click', function(e) { e.preventDefault(); onSearchClick(); });
        document.querySelectorAll('input[name="listing_purpose"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                var filters = getFiltersFromForm();
                var qs = buildQueryString(filters);
                var path = listingPath;
                var newUrl = qs ? path + qs : path;
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({ listingFilters: filters }, '', newUrl);
                }
                loadListings();
            });
        });
        var resetBtn = document.getElementById('listing-reset-filters');
        if (resetBtn) resetBtn.addEventListener('click', function(e) { e.preventDefault(); onResetClick(); });
        function bindMoreOptionsDotUpdates() {
            [ 'listing_bedrooms', 'listing_bathrooms', 'listing_kitchen' ].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) { el.addEventListener('change', updateMoreOptionsDot); el.addEventListener('input', updateMoreOptionsDot); }
            });
            document.querySelectorAll('.hidden-listing-filter input[type="checkbox"]').forEach(function(cb) {
                cb.addEventListener('change', updateMoreOptionsDot);
            });
        }
        bindMoreOptionsDotUpdates();
        (function initSortFilter() {
            function applySort() {
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
                $(document).on('change', '#listing-sort', applySort);
            } else {
                var sortEl = document.getElementById('listing-sort');
                if (sortEl) sortEl.addEventListener('change', applySort);
            }
        })();
        (function initAreaRangeFilter() {
            var areaRangeTimer = null;
            function applyAreaRange() {
                var filters = getFiltersFromForm();
                var qs = buildQueryString(filters);
                var path = listingPath;
                var newUrl = qs ? path + qs : path;
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({ listingFilters: filters }, '', newUrl);
                }
                loadListings();
            }
            function onAreaRangeChange() {
                if (typeof jQuery !== 'undefined') updateListingRangeSummaries();
                clearTimeout(areaRangeTimer);
                areaRangeTimer = setTimeout(applyAreaRange, 400);
            }
            if (typeof $ !== 'undefined') {
                $(document).on('change input', '#listing-area-range', onAreaRangeChange);
            }
        })();

        (function initDhaPhaseFilter() {
            function onDhaPhaseChange() {
                var dhaEl = document.getElementById('listing-dha-phase');
                if (!dhaEl) return;
                var phaseId = dhaEl.value || '';
                if (dhaPhaseUrls && Object.keys(dhaPhaseUrls).length && !phaseId) {
                    window.location.href = listingPath;
                    return;
                }
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
        window.addEventListener('popstate', function() {
            applyFiltersFromUrl();
            loadListings();
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initListingFilters);
    } else {
        initListingFilters();
    }

    function syncListingFooterEmulator() {
        if (typeof window.__etihadSyncHeightEmulator === 'function') {
            try { window.__etihadSyncHeightEmulator(); } catch (e) {}
        }
    }
    window.addEventListener('load', syncListingFooterEmulator);
    setTimeout(syncListingFooterEmulator, 300);
})();
</script>