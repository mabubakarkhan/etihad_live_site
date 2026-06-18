(function () {
    var wrap = document.getElementById('dha-phase-listings');
    if (!wrap) return;
    var phaseId = wrap.dataset.phaseId;
    var apiUrl = wrap.dataset.apiUrl;
    var grid = document.getElementById('dha-phase-listing-grid');
    var emptyEl = document.getElementById('dha-phase-listing-empty');
    var sortEl = document.getElementById('dha-phase-listing-sort');
    var mapEl = document.getElementById('dha-phase-listing-map');
    var mapInstance = null;
    var markers = [];

    function esc(s) {
        if (!s) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function renderCards(properties) {
        if (!grid) return;
        grid.innerHTML = '';
        properties.forEach(function (p) {
            var imgStyle = p.featured_image_url ? 'background-image:url(' + esc(p.featured_image_url) + ')' : '';
            var latAttrs = (p.latitude != null && p.longitude != null)
                ? ' data-lat="' + esc(String(p.latitude)) + '" data-lng="' + esc(String(p.longitude)) + '"' : '';
            grid.insertAdjacentHTML('beforeend',
                '<div class="listing-item"' + latAttrs + '><div class="geodir-category-listing">' +
                '<div class="geodir-category-img"><a href="' + esc(p.detail_url) + '" class="geodir-category-img_item">' +
                '<div class="bg" style="' + imgStyle + '"></div><div class="overlay"></div></a></div>' +
                '<div class="geodir-category-content"><h3 class="listing-card-title"><a href="' + esc(p.detail_url) + '">' + esc(p.title) + '</a></h3>' +
                '<div class="geodir-category-content_price">' + esc(p.price) + '</div></div>' +
                '<div class="geodir-category-footer"><span class="gcf-company"><span>By ' + esc(p.dealer_name || 'Etihad Marketing') + '</span></span>' +
                '<a href="' + esc(p.detail_url) + '" class="gid_link"><span>View Details</span></a></div></div></div>'
            );
        });
        if (emptyEl) emptyEl.style.display = properties.length ? 'none' : 'block';
        updateMap(properties);
    }

    function updateMap(properties) {
        if (!mapEl || typeof google === 'undefined' || !google.maps) return;
        markers.forEach(function (m) { if (m.setMap) m.setMap(null); });
        markers = [];
        if (!mapInstance) {
            var mapOpts = { zoom: 12, center: { lat: 31.47, lng: 74.27 }, scrollwheel: false };
            if (window.EtihadMap) EtihadMap.applyToMapOptions(mapOpts);
            mapInstance = new google.maps.Map(mapEl, mapOpts);
        }
        var bounds = null;
        properties.forEach(function (p) {
            if (p.latitude == null || p.longitude == null) return;
            var lat = parseFloat(p.latitude), lng = parseFloat(p.longitude);
            if (isNaN(lat) || isNaN(lng)) return;
            var pos = { lat: lat, lng: lng };
            var marker = window.EtihadMap
                ? EtihadMap.createMarker({ position: pos, map: mapInstance, title: p.title || '' })
                : new google.maps.Marker({ position: pos, map: mapInstance, title: p.title || '' });
            markers.push(marker);
            if (!bounds) bounds = new google.maps.LatLngBounds();
            bounds.extend(pos);
        });
        if (bounds && markers.length) mapInstance.fitBounds(bounds, 40);
    }

    function load() {
        var sort = sortEl && sortEl.value ? sortEl.value : 'latest';
        var url = apiUrl + '?dha_phase=' + encodeURIComponent(phaseId) + '&sort=' + encodeURIComponent(sort);
        fetch(url, { headers: { Accept: 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) { renderCards(data.properties || []); })
            .catch(function () { if (emptyEl) { emptyEl.style.display = 'block'; emptyEl.textContent = 'Could not load listings.'; } });
    }

    if (sortEl) sortEl.addEventListener('change', load);
    load();
})();
