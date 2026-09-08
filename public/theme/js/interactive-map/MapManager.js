(function (window) {
    'use strict';

    function pathFromBounds(boundsLiteral) {
        if (!boundsLiteral) {
            return null;
        }
        return [
            { lat: boundsLiteral.north, lng: boundsLiteral.west },
            { lat: boundsLiteral.north, lng: boundsLiteral.east },
            { lat: boundsLiteral.south, lng: boundsLiteral.east },
            { lat: boundsLiteral.south, lng: boundsLiteral.west },
        ];
    }

    function normalizePath(path) {
        if (!Array.isArray(path) || path.length < 3) {
            return null;
        }
        var out = [];
        for (var i = 0; i < path.length; i += 1) {
            var point = path[i];
            if (!point) {
                continue;
            }
            var lat = typeof point.lat === 'function' ? point.lat() : parseFloat(point.lat);
            var lng = typeof point.lng === 'function' ? point.lng() : parseFloat(point.lng);
            if (isNaN(lat) || isNaN(lng)) {
                continue;
            }
            out.push({ lat: lat, lng: lng });
        }
        return out.length >= 3 ? out : null;
    }

    function boundsFromPath(path) {
        var normalized = normalizePath(path);
        if (!normalized) {
            return null;
        }
        var north = normalized[0].lat;
        var south = normalized[0].lat;
        var east = normalized[0].lng;
        var west = normalized[0].lng;
        for (var i = 1; i < normalized.length; i += 1) {
            north = Math.max(north, normalized[i].lat);
            south = Math.min(south, normalized[i].lat);
            east = Math.max(east, normalized[i].lng);
            west = Math.min(west, normalized[i].lng);
        }
        if (north <= south || east <= west) {
            return null;
        }
        return { north: north, south: south, east: east, west: west };
    }

    function MapManager(options) {
        this.container = options.container;
        this.mapId = options.mapId || null;
        this.useRasterMap = options.useRasterMap === true;
        this.minZoom = options.minZoom || 0;
        this.maxZoom = options.maxZoom || 22;
        this.defaultZoom = options.defaultZoom || 15;
        this.center = options.center || { lat: 31.5204, lng: 74.3587 };
        this.map = null;
        this.boundsPolygon = null;
        this.boundsEditable_ = false;
        this.onBoundsChanged_ = null;
        this.onBoundsDrag_ = null;
        this.boundsListeners_ = [];
        this.boundsMute_ = false;
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

    MapManager.prototype.setBoundsEditHandlers = function (handlers) {
        handlers = handlers || {};
        this.onBoundsChanged_ = typeof handlers.onBoundsChanged === 'function' ? handlers.onBoundsChanged : null;
        this.onBoundsDrag_ = typeof handlers.onBoundsDrag === 'function' ? handlers.onBoundsDrag : null;
        this.bindBoundsListeners_();
    };

    MapManager.prototype.setBoundsEditable = function (enabled) {
        this.boundsEditable_ = !!enabled;
        if (this.boundsPolygon) {
            this.boundsPolygon.setOptions({
                editable: this.boundsEditable_,
                draggable: this.boundsEditable_,
                clickable: this.boundsEditable_,
                fillOpacity: this.boundsEditable_ ? 0.18 : 0.08,
                strokeWeight: this.boundsEditable_ ? 3 : 2,
            });
        }
        this.bindBoundsListeners_();
    };

    MapManager.prototype.getPathLiteral = function () {
        if (!this.boundsPolygon || !this.boundsPolygon.getPath) {
            return null;
        }
        return normalizePath(this.boundsPolygon.getPath().getArray());
    };

    MapManager.prototype.getBoundsLiteral = function () {
        return boundsFromPath(this.getPathLiteral());
    };

    MapManager.prototype.clearBoundsListeners_ = function () {
        var g = window.google && window.google.maps;
        if (!g || !this.boundsListeners_.length) {
            this.boundsListeners_ = [];
            return;
        }
        this.boundsListeners_.forEach(function (listener) {
            g.event.removeListener(listener);
        });
        this.boundsListeners_ = [];
    };

    MapManager.prototype.emitBoundsUpdate_ = function (finalSave) {
        if (this.boundsMute_) {
            return;
        }
        var path = this.getPathLiteral();
        var literal = boundsFromPath(path);
        if (!literal) {
            return;
        }
        if (typeof this.onBoundsDrag_ === 'function') {
            this.onBoundsDrag_(literal, path);
        }
        if (!finalSave) {
            return;
        }
        clearTimeout(this._boundsSaveTimer);
        var self = this;
        this._boundsSaveTimer = setTimeout(function () {
            if (typeof self.onBoundsChanged_ === 'function') {
                self.onBoundsChanged_(literal, path);
            }
        }, 350);
    };

    MapManager.prototype.bindBoundsListeners_ = function () {
        var g = window.google && window.google.maps;
        this.clearBoundsListeners_();
        if (!g || !this.boundsPolygon || !this.boundsEditable_) {
            return;
        }

        var self = this;
        var path = this.boundsPolygon.getPath();
        var onPathChange = function () {
            self.emitBoundsUpdate_(true);
        };

        this.boundsListeners_.push(g.event.addListener(path, 'set_at', onPathChange));
        this.boundsListeners_.push(g.event.addListener(path, 'insert_at', onPathChange));
        this.boundsListeners_.push(g.event.addListener(path, 'remove_at', onPathChange));
        this.boundsListeners_.push(g.event.addListener(this.boundsPolygon, 'drag', function () {
            self.emitBoundsUpdate_(false);
        }));
        this.boundsListeners_.push(g.event.addListener(this.boundsPolygon, 'dragend', function () {
            self.emitBoundsUpdate_(true);
        }));
    };

    MapManager.prototype.pathsEqual_ = function (a, b) {
        var left = normalizePath(a);
        var right = normalizePath(b);
        if (!left || !right || left.length !== right.length) {
            return false;
        }
        for (var i = 0; i < left.length; i += 1) {
            if (Math.abs(left[i].lat - right[i].lat) > 1e-9 || Math.abs(left[i].lng - right[i].lng) > 1e-9) {
                return false;
            }
        }
        return true;
    };

    /**
     * Draw gold map-area outline as an editable polygon.
     * Accepts either NSEW bounds or an array of {lat,lng} corners.
     */
    MapManager.prototype.drawBoundsRectangle = function (boundsOrPath, options) {
        var g = window.google && window.google.maps;
        if (!g || !this.map) {
            return;
        }

        options = options || {};
        var path = null;
        if (Array.isArray(boundsOrPath)) {
            path = normalizePath(boundsOrPath);
        } else if (boundsOrPath && Array.isArray(boundsOrPath.path)) {
            path = normalizePath(boundsOrPath.path);
        } else if (boundsOrPath && boundsOrPath.north !== undefined) {
            path = pathFromBounds(boundsOrPath);
        }

        if (!path) {
            if (this.boundsPolygon) {
                this.clearBoundsListeners_();
                this.boundsPolygon.setMap(null);
                this.boundsPolygon = null;
            }
            return;
        }

        var editable = options.editable !== undefined ? !!options.editable : this.boundsEditable_;
        this.boundsEditable_ = editable;

        if (this.boundsPolygon) {
            if (!this.pathsEqual_(this.getPathLiteral(), path)) {
                this.boundsMute_ = true;
                this.boundsPolygon.setPath(path);
                this.boundsMute_ = false;
            }
            this.boundsPolygon.setOptions({
                editable: editable,
                draggable: editable,
                clickable: editable,
                fillOpacity: editable ? 0.18 : 0.08,
                strokeWeight: editable ? 3 : 2,
            });
            this.bindBoundsListeners_();
            return;
        }

        this.boundsPolygon = new g.Polygon({
            paths: path,
            strokeColor: '#a9823d',
            strokeOpacity: 0.95,
            strokeWeight: editable ? 3 : 2,
            fillColor: '#a9823d',
            fillOpacity: editable ? 0.18 : 0.08,
            clickable: editable,
            editable: editable,
            draggable: editable,
            zIndex: 1,
            map: this.map,
        });
        this.bindBoundsListeners_();
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
