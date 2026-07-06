(function (window) {
    'use strict';

    function MapManager(options) {
        this.container = options.container;
        this.mapId = options.mapId || null;
        this.useRasterMap = options.useRasterMap === true;
        this.minZoom = options.minZoom || 0;
        this.maxZoom = options.maxZoom || 22;
        this.defaultZoom = options.defaultZoom || 15;
        this.center = options.center || { lat: 31.5204, lng: 74.3587 };
        this.map = null;
        this.boundsRectangle = null;
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
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true,
        };

        if (this.useRasterMap) {
            if (window.EtihadMap && typeof window.EtihadMap.getStyles === 'function') {
                mapOpts.styles = window.EtihadMap.getStyles();
            }
        } else if (window.EtihadMap && typeof window.EtihadMap.applyToMapOptions === 'function') {
            mapOpts.mapId = this.mapId;
            window.EtihadMap.applyToMapOptions(mapOpts, { variant: 'default' });
        } else if (this.mapId) {
            mapOpts.mapId = this.mapId;
        }

        this.map = new g.Map(this.container, mapOpts);
        return this.map;
    };

    MapManager.prototype.getMap = function () {
        return this.map;
    };

    MapManager.prototype.fitBounds = function (boundsLiteral) {
        var g = window.google && window.google.maps;
        if (!g || !this.map || !boundsLiteral) {
            return;
        }

        var bounds = new g.LatLngBounds(
            { lat: boundsLiteral.south, lng: boundsLiteral.west },
            { lat: boundsLiteral.north, lng: boundsLiteral.east }
        );
        this.map.fitBounds(bounds, 48);
    };

    MapManager.prototype.setZoomLimits = function (minZoom, maxZoom) {
        this.minZoom = minZoom;
        this.maxZoom = maxZoom;
        if (this.map) {
            this.map.setOptions({ minZoom: minZoom, maxZoom: maxZoom });
        }
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

    MapManager.prototype.setCenterZoom = function (center, zoom) {
        if (!this.map) {
            return;
        }
        if (center) {
            this.map.setCenter(center);
        }
        if (typeof zoom === 'number') {
            this.map.setZoom(zoom);
        }
    };

    window.InteractiveMap = window.InteractiveMap || {};
    window.InteractiveMap.MapManager = MapManager;
})(window);
