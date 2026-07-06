(function (window) {
    'use strict';

    var LAHORE_CENTER = { lat: 31.5204, lng: 74.3587 };
    var LAHORE_BOUNDS = {
        south: 31.32,
        west: 74.14,
        north: 31.72,
        east: 74.56,
    };

    function getGoogleMaps() {
        return window.google && window.google.maps ? window.google.maps : null;
    }

    function getLahoreBounds(googleMaps) {
        var g = googleMaps || getGoogleMaps();
        if (!g) {
            return null;
        }

        return new g.LatLngBounds(
            new g.LatLng(LAHORE_BOUNDS.south, LAHORE_BOUNDS.west),
            new g.LatLng(LAHORE_BOUNDS.north, LAHORE_BOUNDS.east)
        );
    }

    function defaultAutocompleteOptions(googleMaps, extra) {
        var g = googleMaps || getGoogleMaps();
        var opts = {
            fields: ['formatted_address', 'geometry', 'name'],
            componentRestrictions: { country: 'pk' },
            strictBounds: false,
        };

        var bounds = getLahoreBounds(g);
        if (bounds) {
            opts.bounds = bounds;
        }

        if (extra) {
            Object.keys(extra).forEach(function (key) {
                opts[key] = extra[key];
            });
        }

        return opts;
    }

    function createAutocomplete(input, options) {
        var g = getGoogleMaps();
        if (!g || !g.places || !g.places.Autocomplete || !input) {
            return null;
        }

        return new g.places.Autocomplete(input, defaultAutocompleteOptions(g, options || {}));
    }

    function placeToPoint(place) {
        if (!place || !place.geometry || !place.geometry.location) {
            return null;
        }

        return {
            lat: place.geometry.location.lat(),
            lng: place.geometry.location.lng(),
            name: place.name || '',
            address: place.formatted_address || '',
            viewport: place.geometry.viewport || null,
            location: place.geometry.location,
        };
    }

    function formatPlaceLabel(place) {
        if (place.formatted_address) {
            return place.formatted_address;
        }
        if (place.name) {
            return place.name;
        }

        return '';
    }

    /** Same configuration as listing Place A / Place B landmark fields. */
    function bindLandmarkAutocomplete(input, handlers) {
        handlers = handlers || {};
        var ac = createAutocomplete(input);
        if (!ac) {
            return null;
        }

        ac.addListener('place_changed', function () {
            var place = ac.getPlace();
            var point = placeToPoint(place);
            if (!point) {
                return;
            }

            if (handlers.latEl) {
                handlers.latEl.value = String(point.lat);
            }
            if (handlers.lngEl) {
                handlers.lngEl.value = String(point.lng);
            }

            var label = formatPlaceLabel(place);
            if (label) {
                input.value = label;
            }

            if (typeof handlers.onPlaceChanged === 'function') {
                handlers.onPlaceChanged(point, place);
            }
        });

        if (handlers.latEl && handlers.lngEl) {
            input.addEventListener('input', function () {
                if (!input.value.trim()) {
                    handlers.latEl.value = '';
                    handlers.lngEl.value = '';
                    if (typeof handlers.onClear === 'function') {
                        handlers.onClear();
                    }
                }
            });
        }

        return ac;
    }

    function bindMapSearchAutocomplete(input, map, handlers) {
        handlers = handlers || {};
        var ac = createAutocomplete(input);
        if (!ac || !map) {
            return null;
        }

        var g = getGoogleMaps();
        var marker = null;
        var boundsListener = null;

        function syncAutocompleteBounds() {
            if (!map || typeof map.getBounds !== 'function' || typeof ac.setBounds !== 'function') {
                return;
            }

            var mapBounds = map.getBounds();
            if (mapBounds) {
                ac.setBounds(mapBounds);
            }
        }

        if (g && g.event && typeof g.event.addListener === 'function') {
            boundsListener = g.event.addListener(map, 'idle', syncAutocompleteBounds);
            syncAutocompleteBounds();
        }

        ac.addListener('place_changed', function () {
            var place = ac.getPlace();
            var point = placeToPoint(place);
            if (!point) {
                return;
            }

            var label = formatPlaceLabel(place);
            if (label) {
                input.value = label;
            }

            if (point.viewport) {
                map.fitBounds(point.viewport, 48);
            } else {
                map.setCenter(point.location);
                map.setZoom(Math.max(map.getZoom() || 15, 15));
            }

            if (marker) {
                marker.setMap(null);
                marker = null;
            }

            if (window.EtihadMap && typeof window.EtihadMap.createMarker === 'function') {
                marker = window.EtihadMap.createMarker({
                    map: map,
                    position: point.location,
                    title: 'Search result',
                });
            } else if (g && g.Marker) {
                marker = new g.Marker({
                    map: map,
                    position: point.location,
                    title: 'Search result',
                    zIndex: 999,
                });
            }

            if (typeof handlers.onPlaceChanged === 'function') {
                handlers.onPlaceChanged(point, place, marker);
            }
        });

        return {
            autocomplete: ac,
            syncBounds: syncAutocompleteBounds,
            clearMarker: function () {
                if (marker) {
                    marker.setMap(null);
                    marker = null;
                }
            },
            destroy: function () {
                if (boundsListener && g && g.event) {
                    g.event.removeListener(boundsListener);
                    boundsListener = null;
                }
                if (marker) {
                    marker.setMap(null);
                    marker = null;
                }
            },
        };
    }

    function normalizePlacesApiPlaceId(placeId) {
        if (!placeId) {
            return '';
        }

        return String(placeId).replace(/^places\//, '');
    }

    function newApiPlaceToPoint(place) {
        if (!place || !place.location) {
            return null;
        }

        var g = getGoogleMaps();
        var lat = place.location.latitude;
        var lng = place.location.longitude;
        var location = g ? new g.LatLng(lat, lng) : { lat: lat, lng: lng };
        var viewport = null;

        if (place.viewport && g && g.LatLngBounds) {
            viewport = new g.LatLngBounds(
                { lat: place.viewport.low.latitude, lng: place.viewport.low.longitude },
                { lat: place.viewport.high.latitude, lng: place.viewport.high.longitude }
            );
        }

        return {
            lat: lat,
            lng: lng,
            name: (place.displayName && place.displayName.text) || '',
            address: place.formattedAddress || '',
            viewport: viewport,
            location: location,
        };
    }

    /** Admin map search via server proxy to Places API (New) — key stays on server, no URL restrictions. */
    function bindMapSearchWithPlacesProxy(input, map, proxy, handlers) {
        handlers = handlers || {};
        if (!input || !map || !proxy || !proxy.autocompleteUrl || !proxy.placeDetailsUrl) {
            return null;
        }

        var g = getGoogleMaps();
        var marker = null;
        var dropdown = document.createElement('div');
        var debounceTimer = null;
        var activeController = null;
        var destroyed = false;
        var wrap = input.parentNode;

        dropdown.className = 'etihad-places-suggest';
        dropdown.setAttribute('role', 'listbox');
        dropdown.hidden = true;

        if (wrap) {
            if (!wrap.classList.contains('etihad-places-suggest-wrap')) {
                wrap.classList.add('etihad-places-suggest-wrap');
            }
            wrap.appendChild(dropdown);
        }

        function proxyHeaders() {
            var headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            };

            if (proxy.csrf) {
                headers['X-CSRF-TOKEN'] = proxy.csrf;
            }

            return headers;
        }

        function hideDropdown() {
            dropdown.hidden = true;
            dropdown.innerHTML = '';
        }

        function showDropdown() {
            dropdown.hidden = false;
        }

        function clearMarker() {
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
        }

        function placeMarkerAndCenter(point) {
            if (!point || !point.location) {
                return;
            }

            if (point.viewport) {
                map.fitBounds(point.viewport, 48);
            } else {
                map.setCenter(point.location);
                map.setZoom(Math.max(map.getZoom() || 15, 15));
            }

            clearMarker();

            if (window.EtihadMap && typeof window.EtihadMap.createMarker === 'function') {
                marker = window.EtihadMap.createMarker({
                    map: map,
                    position: point.location,
                    title: 'Search result',
                });
            } else if (g && g.Marker) {
                marker = new g.Marker({
                    map: map,
                    position: point.location,
                    title: 'Search result',
                    zIndex: 999,
                });
            }

            if (typeof handlers.onPlaceChanged === 'function') {
                handlers.onPlaceChanged(point);
            }
        }

        function fetchAutocomplete(query) {
            if (activeController) {
                activeController.abort();
            }

            activeController = new AbortController();

            return fetch(proxy.autocompleteUrl, {
                method: 'POST',
                headers: Object.assign({
                    'Content-Type': 'application/json',
                }, proxyHeaders()),
                body: JSON.stringify({ input: query }),
                credentials: 'same-origin',
                signal: activeController.signal,
            }).then(function (response) {
                return response.json().then(function (json) {
                    if (!response.ok) {
                        throw new Error((json && json.message) || 'Places search failed');
                    }

                    return json;
                });
            });
        }

        function fetchPlaceDetails(placeId) {
            var normalizedId = normalizePlacesApiPlaceId(placeId);

            return fetch(proxy.placeDetailsUrl + encodeURIComponent(normalizedId), {
                headers: proxyHeaders(),
                credentials: 'same-origin',
            }).then(function (response) {
                return response.json().then(function (json) {
                    if (!response.ok) {
                        throw new Error((json && json.message) || 'Place details failed');
                    }

                    return json;
                });
            });
        }

        function selectSuggestion(placeId, label) {
            hideDropdown();
            input.value = label;

            fetchPlaceDetails(placeId)
                .then(function (place) {
                    if (destroyed) {
                        return;
                    }

                    var point = newApiPlaceToPoint(place);
                    if (point) {
                        placeMarkerAndCenter(point);
                    }
                })
                .catch(function () {
                    /* ignore */
                });
        }

        function renderSuggestions(suggestions) {
            dropdown.innerHTML = '';

            if (!suggestions || !suggestions.length) {
                hideDropdown();
                return;
            }

            suggestions.forEach(function (item) {
                var prediction = item.placePrediction;
                if (!prediction || !prediction.placeId) {
                    return;
                }

                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'etihad-places-suggest__item';
                button.setAttribute('role', 'option');
                button.textContent = (prediction.text && prediction.text.text) || prediction.placeId;
                button.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                });
                button.addEventListener('click', function () {
                    selectSuggestion(prediction.placeId, button.textContent);
                });
                dropdown.appendChild(button);
            });

            showDropdown();
        }

        function onInput() {
            clearTimeout(debounceTimer);
            var query = input.value.trim();

            if (query.length < 2) {
                hideDropdown();
                return;
            }

            debounceTimer = setTimeout(function () {
                fetchAutocomplete(query)
                    .then(function (data) {
                        if (destroyed) {
                            return;
                        }

                        renderSuggestions(data.suggestions || []);
                    })
                    .catch(function () {
                        if (!destroyed) {
                            hideDropdown();
                        }
                    });
            }, 280);
        }

        function onDocumentClick(event) {
            if (!wrap || !wrap.contains(event.target)) {
                hideDropdown();
            }
        }

        input.addEventListener('input', onInput);
        document.addEventListener('click', onDocumentClick);

        return {
            syncBounds: function () {},
            clearMarker: clearMarker,
            destroy: function () {
                destroyed = true;
                clearTimeout(debounceTimer);

                if (activeController) {
                    activeController.abort();
                    activeController = null;
                }

                input.removeEventListener('input', onInput);
                document.removeEventListener('click', onDocumentClick);
                hideDropdown();

                if (dropdown.parentNode) {
                    dropdown.parentNode.removeChild(dropdown);
                }

                clearMarker();
            },
        };
    }

    /** @deprecated Use bindMapSearchWithPlacesProxy for admin editor. */
    function bindMapSearchWithPlacesApi(input, map, placesApiKey, handlers) {
        handlers = handlers || {};
        if (!input || !map || !placesApiKey) {
            return null;
        }

        var g = getGoogleMaps();
        var marker = null;
        var dropdown = document.createElement('div');
        var debounceTimer = null;
        var activeController = null;
        var destroyed = false;
        var wrap = input.parentNode;

        dropdown.className = 'etihad-places-suggest';
        dropdown.setAttribute('role', 'listbox');
        dropdown.hidden = true;

        if (wrap) {
            if (!wrap.classList.contains('etihad-places-suggest-wrap')) {
                wrap.classList.add('etihad-places-suggest-wrap');
            }
            wrap.appendChild(dropdown);
        }

        function hideDropdown() {
            dropdown.hidden = true;
            dropdown.innerHTML = '';
        }

        function showDropdown() {
            dropdown.hidden = false;
        }

        function clearMarker() {
            if (marker) {
                marker.setMap(null);
                marker = null;
            }
        }

        function placeMarkerAndCenter(point) {
            if (!point || !point.location) {
                return;
            }

            if (point.viewport) {
                map.fitBounds(point.viewport, 48);
            } else {
                map.setCenter(point.location);
                map.setZoom(Math.max(map.getZoom() || 15, 15));
            }

            clearMarker();

            if (window.EtihadMap && typeof window.EtihadMap.createMarker === 'function') {
                marker = window.EtihadMap.createMarker({
                    map: map,
                    position: point.location,
                    title: 'Search result',
                });
            } else if (g && g.Marker) {
                marker = new g.Marker({
                    map: map,
                    position: point.location,
                    title: 'Search result',
                    zIndex: 999,
                });
            }

            if (typeof handlers.onPlaceChanged === 'function') {
                handlers.onPlaceChanged(point);
            }
        }

        function fetchAutocomplete(query) {
            if (activeController) {
                activeController.abort();
            }

            activeController = new AbortController();

            return fetch('https://places.googleapis.com/v1/places:autocomplete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Goog-Api-Key': placesApiKey,
                },
                body: JSON.stringify({
                    input: query,
                    includedRegionCodes: ['pk'],
                    locationBias: {
                        circle: {
                            center: {
                                latitude: LAHORE_CENTER.lat,
                                longitude: LAHORE_CENTER.lng,
                            },
                            radius: 50000,
                        },
                    },
                }),
                signal: activeController.signal,
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Places search failed');
                }

                return response.json();
            });
        }

        function fetchPlaceDetails(placeId) {
            var normalizedId = normalizePlacesApiPlaceId(placeId);

            return fetch('https://places.googleapis.com/v1/places/' + encodeURIComponent(normalizedId), {
                headers: {
                    'X-Goog-Api-Key': placesApiKey,
                    'X-Goog-FieldMask': 'id,displayName,formattedAddress,location,viewport',
                },
            }).then(function (response) {
                if (!response.ok) {
                    throw new Error('Place details failed');
                }

                return response.json();
            });
        }

        function selectSuggestion(placeId, label) {
            hideDropdown();
            input.value = label;

            fetchPlaceDetails(placeId)
                .then(function (place) {
                    if (destroyed) {
                        return;
                    }

                    var point = newApiPlaceToPoint(place);
                    if (point) {
                        placeMarkerAndCenter(point);
                    }
                })
                .catch(function () {
                    /* ignore */
                });
        }

        function renderSuggestions(suggestions) {
            dropdown.innerHTML = '';

            if (!suggestions || !suggestions.length) {
                hideDropdown();
                return;
            }

            suggestions.forEach(function (item) {
                var prediction = item.placePrediction;
                if (!prediction || !prediction.placeId) {
                    return;
                }

                var button = document.createElement('button');
                button.type = 'button';
                button.className = 'etihad-places-suggest__item';
                button.setAttribute('role', 'option');
                button.textContent = (prediction.text && prediction.text.text) || prediction.placeId;
                button.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                });
                button.addEventListener('click', function () {
                    selectSuggestion(prediction.placeId, button.textContent);
                });
                dropdown.appendChild(button);
            });

            showDropdown();
        }

        function onInput() {
            clearTimeout(debounceTimer);
            var query = input.value.trim();

            if (query.length < 2) {
                hideDropdown();
                return;
            }

            debounceTimer = setTimeout(function () {
                fetchAutocomplete(query)
                    .then(function (data) {
                        if (destroyed) {
                            return;
                        }

                        renderSuggestions(data.suggestions || []);
                    })
                    .catch(function () {
                        if (!destroyed) {
                            hideDropdown();
                        }
                    });
            }, 280);
        }

        function onDocumentClick(event) {
            if (!wrap || !wrap.contains(event.target)) {
                hideDropdown();
            }
        }

        input.addEventListener('input', onInput);
        document.addEventListener('click', onDocumentClick);

        return {
            syncBounds: function () {},
            clearMarker: clearMarker,
            destroy: function () {
                destroyed = true;
                clearTimeout(debounceTimer);

                if (activeController) {
                    activeController.abort();
                    activeController = null;
                }

                input.removeEventListener('input', onInput);
                document.removeEventListener('click', onDocumentClick);
                hideDropdown();

                if (dropdown.parentNode) {
                    dropdown.parentNode.removeChild(dropdown);
                }

                clearMarker();
            },
        };
    }

    window.EtihadPlacesAutocomplete = {
        LAHORE_CENTER: LAHORE_CENTER,
        LAHORE_BOUNDS: LAHORE_BOUNDS,
        getLahoreBounds: getLahoreBounds,
        createAutocomplete: createAutocomplete,
        bindLandmarkAutocomplete: bindLandmarkAutocomplete,
        bindMapSearchAutocomplete: bindMapSearchAutocomplete,
        bindMapSearchWithPlacesProxy: bindMapSearchWithPlacesProxy,
        bindMapSearchWithPlacesApi: bindMapSearchWithPlacesApi,
        placeToPoint: placeToPoint,
    };
})(window);
