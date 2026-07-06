(function (window) {
    'use strict';

    var IM = window.InteractiveMap = window.InteractiveMap || {};
    var loaders = {};

    function loadGoogleMaps(apiKey, callbackName) {
        if (window.google && window.google.maps) {
            return Promise.resolve(window.google.maps);
        }

        if (!apiKey) {
            return Promise.reject(new Error('Google Maps API key is not configured.'));
        }

        var promiseKey = apiKey + ':' + callbackName + ':front';
        if (loaders[promiseKey]) {
            return loaders[promiseKey];
        }

        loaders[promiseKey] = new Promise(function (resolve, reject) {
            window[callbackName] = function () {
                resolve(window.google.maps);
            };

            var script = document.createElement('script');
            script.async = true;
            script.defer = true;
            script.onerror = function () {
                reject(new Error('Failed to load Google Maps JavaScript API.'));
            };
            script.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&callback=' + encodeURIComponent(callbackName);
            document.head.appendChild(script);
        });

        return loaders[promiseKey];
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

    function initElement(el) {
        if (!el || el.__portalInteractiveMapInit) {
            return;
        }

        el.__portalInteractiveMapInit = true;

        var config;
        try {
            config = JSON.parse(el.getAttribute('data-interactive-map-config') || '{}');
        } catch (e) {
            return;
        }

        if (!config.overlay_url || !config.bounds) {
            return;
        }

        var apiKey = el.getAttribute('data-google-maps-key') || '';
        var callbackName = 'initPortalInteractiveMap_' + String(el.id || 'x').replace(/[^a-zA-Z0-9_]/g, '_');

        loadGoogleMaps(apiKey, callbackName)
            .then(function () {
                if (!IM.MapManager || !IM.OverlayManager) {
                    return;
                }

                var mapManager = new IM.MapManager({
                    container: el,
                    useRasterMap: true,
                    minZoom: config.min_zoom || 10,
                    maxZoom: config.max_zoom || 20,
                    defaultZoom: config.default_zoom || 15,
                    center: centerFromBounds(config.bounds),
                });
                mapManager.init();

                var overlayManager = new IM.OverlayManager({
                    map: mapManager.getMap(),
                    opacity: config.overlay_opacity,
                });

                overlayManager.update({
                    url: config.overlay_url,
                    bounds: config.bounds,
                    opacity: config.overlay_opacity,
                });

                mapManager.fitBounds(config.bounds);
            })
            .catch(function () {
                el.classList.add('portal-map-section__map-canvas--error');
            });
    }

    function bootPortalInteractiveMaps() {
        document.querySelectorAll('[data-interactive-map-config]').forEach(initElement);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootPortalInteractiveMaps);
    } else {
        bootPortalInteractiveMaps();
    }

    IM.bootPortalInteractiveMaps = bootPortalInteractiveMaps;
})(window);
