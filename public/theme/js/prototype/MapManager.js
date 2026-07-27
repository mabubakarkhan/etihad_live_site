(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    function MapManager(options) {
        this.container = options.container;
        this.mapId = options.mapId || null;
        this.minZoom = options.minZoom || 0;
        this.maxZoom = options.maxZoom || 22;
        this.defaultZoom = options.defaultZoom || 15;
        this.center = options.center || { lat: 31.5204, lng: 74.3587 };
        this.map = null;
        this.boundsRectangle = null;
        this.listeners_ = [];
    }

    MapManager.prototype.init = function () {
        var g = window.google && window.google.maps;
        if (!g || !this.container) {
            return null;
        }

        var mapOpts = {
            center: this.center,
            zoom: this.defaultZoom,
            minZoom: this.minZoom,
            maxZoom: this.maxZoom,
            mapTypeId: 'roadmap',
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true,
            gestureHandling: 'greedy',
        };

        // Only use Cloud Map ID when it is a real ID — DEMO_MAP_ID breaks raster tiles.
        if (this.mapId && this.mapId !== 'DEMO_MAP_ID') {
            mapOpts.mapId = this.mapId;
        }

        this.map = new g.Map(this.container, mapOpts);
        return this.map;
    };

    MapManager.prototype.getMap = function () {
        return this.map;
    };

    MapManager.prototype.on = function (eventName, handler) {
        var g = window.google && window.google.maps;
        if (!g || !this.map) {
            return;
        }

        var listener = g.event.addListener(this.map, eventName, handler);
        this.listeners_.push(listener);
    };

    MapManager.prototype.fitBounds = function (boundsLiteral, padding) {
        var g = window.google && window.google.maps;
        if (!g || !this.map || !boundsLiteral) {
            return;
        }

        var bounds = new g.LatLngBounds(
            { lat: boundsLiteral.south, lng: boundsLiteral.west },
            { lat: boundsLiteral.north, lng: boundsLiteral.east }
        );
        this.map.fitBounds(bounds, padding || 48);
    };

    MapManager.prototype.setZoomLimits = function (minZoom, maxZoom) {
        this.minZoom = minZoom;
        this.maxZoom = maxZoom;
        if (this.map) {
            this.map.setOptions({ minZoom: minZoom, maxZoom: maxZoom });
        }
    };

    MapManager.prototype.setMapType = function (mapTypeId) {
        if (this.map) {
            this.map.setMapTypeId(mapTypeId);
        }
    };

    MapManager.prototype.getZoom = function () {
        return this.map ? this.map.getZoom() : null;
    };

    MapManager.prototype.drawBoundsRectangle = function (boundsLiteral) {
        var g = window.google && window.google.maps;
        if (!g || !this.map) {
            return;
        }

        if (this.boundsRectangle) {
            this.boundsRectangle.setMap(null);
            this.boundsRectangle = null;
        }

        if (!boundsLiteral) {
            return;
        }

        this.boundsRectangle = new g.Rectangle({
            bounds: {
                north: boundsLiteral.north,
                south: boundsLiteral.south,
                east: boundsLiteral.east,
                west: boundsLiteral.west,
            },
            strokeColor: '#a9823d',
            strokeOpacity: 0.9,
            strokeWeight: 2,
            fillColor: '#a9823d',
            fillOpacity: 0.08,
            clickable: false,
            map: this.map,
        });
    };

    MapManager.prototype.toggleBoundsRectangle = function (visible) {
        if (this.boundsRectangle) {
            this.boundsRectangle.setMap(visible ? this.map : null);
        }
    };

    MapManager.prototype.destroy = function () {
        var g = window.google && window.google.maps;
        if (g) {
            this.listeners_.forEach(function (listener) {
                g.event.removeListener(listener);
            });
        }
        this.listeners_ = [];

        if (this.boundsRectangle) {
            this.boundsRectangle.setMap(null);
            this.boundsRectangle = null;
        }

        this.map = null;
    };

    PM.MapManager = MapManager;
})(window);
