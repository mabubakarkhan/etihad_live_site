(function () {
    'use strict';

    function initIcons() {
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            lucide.createIcons();
        }
    }

    function parseJson(value, fallback) {
        try {
            return JSON.parse(value || '');
        } catch (e) {
            return fallback;
        }
    }

    function categoryColor(category) {
        var colors = {
            Schools: '#3b82f6',
            Mosques: '#22c55e',
            Markets: '#f59e0b',
            Hospitals: '#ef4444',
            Parks: '#14b8a6'
        };
        return colors[category] || '#c89b3c';
    }

    function initHub(root) {
        var tabs = root.querySelectorAll('[data-hub-tab]');
        var panels = root.querySelectorAll('[data-hub-panel]');
        var defaultTab = root.getAttribute('data-default-tab') || 'pdf';
        var nearbyReady = false;
        var map = null;
        var markers = [];
        var infoWindow = null;
        var activeFilter = 'all';
        var facilities = parseJson(root.getAttribute('data-facilities'), []);
        var center = parseJson(root.getAttribute('data-map-center'), { lat: 31.476723, lng: 74.384087 });
        var apiKey = root.getAttribute('data-google-maps-key') || '';

        function setActiveTab(name) {
            tabs.forEach(function (tab) {
                var isActive = tab.getAttribute('data-hub-tab') === name && !tab.disabled;
                tab.classList.toggle('is-active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            panels.forEach(function (panel) {
                var show = panel.getAttribute('data-hub-panel') === name;
                panel.hidden = !show;
            });
            if (name === 'nearby') {
                ensureNearbyMap();
            }
            initIcons();
        }

        function applyFilter(category) {
            activeFilter = category;
            root.querySelectorAll('[data-facility-filter]').forEach(function (btn) {
                btn.classList.toggle('is-active', btn.getAttribute('data-facility-filter') === category);
            });
            root.querySelectorAll('[data-facility-category]').forEach(function (item) {
                var match = category === 'all' || item.getAttribute('data-facility-category') === category;
                item.hidden = !match;
            });
            markers.forEach(function (entry) {
                var visible = category === 'all' || entry.category === category;
                entry.marker.setMap(visible ? map : null);
            });
        }

        function loadGoogleMaps() {
            if (window.google && window.google.maps) {
                return Promise.resolve(window.google.maps);
            }
            if (!apiKey) {
                return Promise.reject(new Error('Missing Google Maps API key'));
            }
            if (window.__dhaMapHubMapsPromise) {
                return window.__dhaMapHubMapsPromise;
            }
            window.__dhaMapHubMapsPromise = new Promise(function (resolve, reject) {
                var cbName = 'dhaMapHubMapsReady_' + Date.now();
                window[cbName] = function () {
                    resolve(window.google.maps);
                    try { delete window[cbName]; } catch (e) {}
                };
                var script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&callback=' + cbName;
                script.async = true;
                script.onerror = function () {
                    reject(new Error('Failed to load Google Maps'));
                };
                document.head.appendChild(script);
            });
            return window.__dhaMapHubMapsPromise;
        }

        function ensureNearbyMap() {
            if (nearbyReady) {
                if (map && window.google && window.google.maps) {
                    window.google.maps.event.trigger(map, 'resize');
                    map.setCenter(center);
                }
                return;
            }
            nearbyReady = true;
            var mapEl = root.querySelector('#dha-map-hub-nearby-map');
            if (!mapEl) {
                return;
            }

            loadGoogleMaps().then(function (gmaps) {
                map = new gmaps.Map(mapEl, {
                    center: center,
                    zoom: 14,
                    mapTypeControl: false,
                    streetViewControl: false,
                    fullscreenControl: true,
                    styles: [
                        { elementType: 'geometry', stylers: [{ color: '#1d1d1d' }] },
                        { elementType: 'labels.text.stroke', stylers: [{ color: '#1d1d1d' }] },
                        { elementType: 'labels.text.fill', stylers: [{ color: '#8a8a8a' }] },
                        { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#2c2c2c' }] },
                        { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#0f172a' }] },
                        { featureType: 'poi', stylers: [{ visibility: 'off' }] }
                    ]
                });
                infoWindow = new gmaps.InfoWindow();

                markers = facilities.map(function (facility, index) {
                    var marker = new gmaps.Marker({
                        position: { lat: Number(facility.lat), lng: Number(facility.lng) },
                        map: map,
                        title: facility.name,
                        icon: {
                            path: gmaps.SymbolPath.CIRCLE,
                            scale: 9,
                            fillColor: categoryColor(facility.category),
                            fillOpacity: 1,
                            strokeColor: '#ffffff',
                            strokeWeight: 2
                        }
                    });
                    marker.addListener('click', function () {
                        infoWindow.setContent(
                            '<div style="font-family:Poppins,sans-serif;padding:2px 4px;">' +
                            '<strong style="display:block;margin-bottom:2px;">' + facility.name + '</strong>' +
                            '<span style="color:#666;font-size:12px;">' + facility.category + '</span></div>'
                        );
                        infoWindow.open(map, marker);
                    });
                    return { marker: marker, category: facility.category, index: index };
                });

                root.querySelectorAll('[data-facility-index]').forEach(function (item) {
                    item.addEventListener('click', function () {
                        var idx = Number(item.getAttribute('data-facility-index'));
                        var entry = markers[idx];
                        if (!entry) return;
                        map.panTo(entry.marker.getPosition());
                        map.setZoom(16);
                        gmaps.event.trigger(entry.marker, 'click');
                    });
                });

                applyFilter(activeFilter);
            }).catch(function () {
                mapEl.innerHTML = '<div class="dha-map-hub__map-fallback">Map pins are listed on the right. Add GOOGLE_MAPS_API_KEY to enable the interactive nearby map.</div>';
            });
        }

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                if (tab.disabled) return;
                setActiveTab(tab.getAttribute('data-hub-tab'));
            });
        });

        root.querySelectorAll('[data-facility-filter]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                applyFilter(btn.getAttribute('data-facility-filter'));
            });
        });

        var initial = root.querySelector('[data-hub-tab="' + defaultTab + '"]:not([disabled])')
            ? defaultTab
            : (root.querySelector('[data-hub-tab]:not([disabled])') || {}).getAttribute
                ? root.querySelector('[data-hub-tab]:not([disabled])').getAttribute('data-hub-tab')
                : 'nearby';
        setActiveTab(initial || 'nearby');
    }

    function boot() {
        var root = document.getElementById('dha-map-hub');
        if (root) {
            initHub(root);
        }
        initIcons();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
