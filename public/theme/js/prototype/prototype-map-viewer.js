(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};
    var loader = null;

    function loadGoogleMaps(apiKey) {
        if (window.google && window.google.maps) {
            return Promise.resolve(window.google.maps);
        }

        if (!apiKey) {
            return Promise.reject(new Error('Google Maps API key is not configured.'));
        }

        if (loader) {
            return loader;
        }

        var callbackName = 'initPrototypeMapViewer';

        loader = new Promise(function (resolve, reject) {
            window[callbackName] = function () {
                if (window.google && window.google.maps) {
                    resolve(window.google.maps);
                    return;
                }
                reject(new Error('Google Maps failed to load.'));
            };

            var script = document.createElement('script');
            script.async = true;
            script.defer = true;
            script.onerror = function () {
                reject(new Error('Failed to load Google Maps JavaScript API.'));
            };
            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&loading=async&callback=' + encodeURIComponent(callbackName);
            document.head.appendChild(script);
        });

        return loader;
    }

    function boundsFromData(data) {
        if (!data || !data.bounds) {
            return null;
        }
        return data.bounds;
    }

    function centerFromBounds(bounds) {
        if (!bounds) {
            return { lat: 31.5204, lng: 74.3587 };
        }
        return {
            lat: (bounds.north + bounds.south) / 2,
            lng: (bounds.east + bounds.west) / 2,
        };
    }

    function showStatus(message) {
        var el = document.getElementById('prototype-viewer-status');
        if (el) {
            el.textContent = message;
            el.hidden = false;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('prototype-map-viewer');
        if (!root) {
            return;
        }

        var apiKey = root.getAttribute('data-google-maps-key') || '';
        var mapId = root.getAttribute('data-google-maps-map-id') || '';
        var overlayData = JSON.parse(root.getAttribute('data-overlay') || 'null');

        loadGoogleMaps(apiKey)
            .then(function () {
                var bounds = boundsFromData(overlayData);

                var mapManager = new PM.MapManager({
                    container: root,
                    mapId: mapId,
                    minZoom: overlayData ? parseInt(overlayData.min_zoom, 10) : 0,
                    maxZoom: overlayData ? parseInt(overlayData.max_zoom, 10) : 22,
                    defaultZoom: overlayData ? parseInt(overlayData.default_zoom, 10) : 15,
                    center: centerFromBounds(bounds),
                });
                mapManager.init();

                if (bounds) {
                    mapManager.fitBounds(bounds);
                }

                var overlayManager = new PM.OverlayManager(mapManager.getMap());
                var sectionManager = null;

                if (overlayData && overlayData.overlay_url && bounds && overlayData.status === 'active') {
                    overlayManager.show(overlayData);
                } else if (!overlayData) {
                    showStatus('No overlay selected.');
                } else if (overlayData.status !== 'active') {
                    showStatus('This overlay is not active.');
                } else if (!overlayData.overlay_url) {
                    showStatus('No overlay image uploaded.');
                }

                if (PM.SectionManager && overlayData && overlayData.sections && overlayData.sections.length) {
                    sectionManager = new PM.SectionManager(mapManager.getMap());
                    sectionManager.load(overlayData.sections);
                }

                mapManager.on('zoom_changed', function () {
                    if (overlayManager.overlay_) {
                        overlayManager.overlay_.draw();
                    }
                });

                mapManager.on('idle', function () {
                    if (overlayManager.overlay_) {
                        overlayManager.overlay_.draw();
                    }
                });
            })
            .catch(function (err) {
                showStatus(err.message);
            });
    });
})(window);
