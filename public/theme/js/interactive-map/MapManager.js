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
        this.boundsEditable_ = false;
        this.onBoundsChanged_ = null;
        this.onBoundsDrag_ = null;
        this.boundsListener_ = null;
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
        if (this.boundsRectangle) {
            this.boundsRectangle.setOptions({
                editable: this.boundsEditable_,
                draggable: this.boundsEditable_,
                clickable: this.boundsEditable_,
                fillOpacity: this.boundsEditable_ ? 0.18 : 0.08,
                strokeWeight: this.boundsEditable_ ? 3 : 2,
            });
        }
        this.bindBoundsListeners_();
    };

    MapManager.prototype.getBoundsLiteral = function () {
        if (!this.boundsRectangle || !this.boundsRectangle.getBounds) {
            return null;
        }
        var bounds = this.boundsRectangle.getBounds();
        if (!bounds) {
            return null;
        }
        return {
            north: bounds.getNorthEast().lat(),
            south: bounds.getSouthWest().lat(),
            east: bounds.getNorthEast().lng(),
            west: bounds.getSouthWest().lng(),
        };
    };

    MapManager.prototype.bindBoundsListeners_ = function () {
        var g = window.google && window.google.maps;
        if (this.boundsListener_ && g) {
            g.event.removeListener(this.boundsListener_);
            this.boundsListener_ = null;
        }
        if (!g || !this.boundsRectangle || !this.boundsEditable_) {
            return;
        }

        var self = this;
        this.boundsListener_ = g.event.addListener(this.boundsRectangle, 'bounds_changed', function () {
            if (self.boundsMute_) {
                return;
            }
            var literal = self.getBoundsLiteral();
            if (!literal) {
                return;
            }
            if (typeof self.onBoundsDrag_ === 'function') {
                self.onBoundsDrag_(literal);
            }
            clearTimeout(self._boundsSaveTimer);
            self._boundsSaveTimer = setTimeout(function () {
                if (typeof self.onBoundsChanged_ === 'function') {
                    self.onBoundsChanged_(literal);
                }
            }, 350);
        });
    };

    MapManager.prototype.drawBoundsRectangle = function (boundsLiteral, options) {
        var g = window.google && window.google.maps;
        if (!g || !this.map) {
            return;
        }

        options = options || {};
        if (!boundsLiteral) {
            if (this.boundsRectangle) {
                this.boundsRectangle.setMap(null);
                this.boundsRectangle = null;
            }
            return;
        }

        var editable = options.editable !== undefined ? !!options.editable : this.boundsEditable_;
        this.boundsEditable_ = editable;

        if (this.boundsRectangle) {
            this.boundsMute_ = true;
            this.boundsRectangle.setBounds({
                north: boundsLiteral.north,
                south: boundsLiteral.south,
                east: boundsLiteral.east,
                west: boundsLiteral.west,
            });
            this.boundsRectangle.setOptions({
                editable: editable,
                draggable: editable,
                clickable: editable,
                fillOpacity: editable ? 0.18 : 0.08,
                strokeWeight: editable ? 3 : 2,
            });
            this.boundsMute_ = false;
            this.bindBoundsListeners_();
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
