(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    var GeographicImageOverlayClass = null;

    function copyBounds(bounds) {
        return {
            north: bounds.north,
            south: bounds.south,
            east: bounds.east,
            west: bounds.west,
        };
    }

    function parseZoomValue(value) {
        if (value === null || value === undefined || value === '') {
            return null;
        }
        var parsed = parseInt(value, 10);
        return isNaN(parsed) ? null : parsed;
    }

    function resolveHideFromZoom(config) {
        var hide = parseZoomValue(config && config.hide_overlay_from_zoom);
        if (hide !== null) {
            return hide;
        }
        return parseZoomValue(config && config.max_zoom);
    }

    function ensureOverlayClass(g) {
        if (GeographicImageOverlayClass) {
            return GeographicImageOverlayClass;
        }

        function GeographicImageOverlay(boundsLiteral, imageUrl, opacity, rotation) {
            this.boundsLiteral_ = boundsLiteral;
            this.imageUrl_ = imageUrl;
            this.opacity_ = typeof opacity === 'number' ? opacity : 0.85;
            this.rotation_ = typeof rotation === 'number' ? rotation : 0;
            this.div_ = null;
            this.img_ = null;
            this.visibleFromZoom_ = null;
            this.hideFromZoom_ = null;
        }

        GeographicImageOverlay.prototype = Object.create(g.OverlayView.prototype);
        GeographicImageOverlay.prototype.constructor = GeographicImageOverlay;

        GeographicImageOverlay.prototype.onAdd = function () {
            var div = document.createElement('div');
            div.style.position = 'absolute';
            div.style.border = 'none';
            div.style.margin = '0';
            div.style.padding = '0';
            div.style.overflow = 'hidden';
            div.style.pointerEvents = 'none';
            div.style.boxSizing = 'border-box';
            div.style.transformOrigin = 'center center';

            var img = document.createElement('img');
            img.src = this.imageUrl_;
            img.alt = '';
            img.draggable = false;
            img.style.display = 'block';
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.opacity = String(this.opacity_);
            img.style.userSelect = 'none';
            img.style.pointerEvents = 'none';

            div.appendChild(img);
            this.div_ = div;
            this.img_ = img;

            var panes = this.getPanes();
            if (panes && panes.overlayLayer) {
                panes.overlayLayer.appendChild(div);
            }
        };

        GeographicImageOverlay.prototype.draw = function () {
            if (!this.div_ || !this.boundsLiteral_) {
                return;
            }

            var map = this.getMap();
            if (!map) {
                return;
            }

            var zoom = map.getZoom();
            if (this.visibleFromZoom_ !== null && zoom < this.visibleFromZoom_) {
                this.div_.style.display = 'none';
                return;
            }
            if (this.hideFromZoom_ !== null && zoom >= this.hideFromZoom_) {
                this.div_.style.display = 'none';
                return;
            }

            var projection = this.getProjection();
            if (!projection) {
                return;
            }

            var b = this.boundsLiteral_;
            var sw = projection.fromLatLngToDivPixel(new g.LatLng(b.south, b.west));
            var ne = projection.fromLatLngToDivPixel(new g.LatLng(b.north, b.east));

            if (!sw || !ne) {
                return;
            }

            var left = Math.min(sw.x, ne.x);
            var top = Math.min(sw.y, ne.y);
            var width = Math.abs(ne.x - sw.x);
            var height = Math.abs(sw.y - ne.y);

            if (width < 1 || height < 1) {
                this.div_.style.display = 'none';
                return;
            }

            this.div_.style.display = 'block';
            this.div_.style.left = left + 'px';
            this.div_.style.top = top + 'px';
            this.div_.style.width = width + 'px';
            this.div_.style.height = height + 'px';

            if (this.rotation_) {
                this.div_.style.transform = 'rotate(' + this.rotation_ + 'deg)';
            } else {
                this.div_.style.transform = '';
            }
        };

        GeographicImageOverlay.prototype.onRemove = function () {
            if (this.div_ && this.div_.parentNode) {
                this.div_.parentNode.removeChild(this.div_);
            }
            this.div_ = null;
            this.img_ = null;
        };

        GeographicImageOverlay.prototype.setConfig = function (boundsLiteral, imageUrl, opacity, rotation, visibleFromZoom, hideFromZoom) {
            this.boundsLiteral_ = boundsLiteral;
            if (imageUrl && this.img_ && this.img_.src !== imageUrl) {
                this.imageUrl_ = imageUrl;
                this.img_.src = imageUrl;
            } else if (imageUrl) {
                this.imageUrl_ = imageUrl;
            }
            if (typeof opacity === 'number') {
                this.opacity_ = opacity;
                if (this.img_) {
                    this.img_.style.opacity = String(opacity);
                }
            }
            if (typeof rotation === 'number') {
                this.rotation_ = rotation;
            }
            if (visibleFromZoom === null || typeof visibleFromZoom === 'number') {
                this.visibleFromZoom_ = visibleFromZoom;
            }
            if (hideFromZoom === null || typeof hideFromZoom === 'number') {
                this.hideFromZoom_ = hideFromZoom;
            }
            this.draw();
        };

        GeographicImageOverlayClass = GeographicImageOverlay;
        return GeographicImageOverlayClass;
    }

    function OverlayManager(map) {
        this.map = map;
        this.overlay_ = null;
    }

    OverlayManager.prototype.show = function (config) {
        var g = window.google && window.google.maps;
        if (!g || !this.map || !config || !config.overlay_url || !config.bounds) {
            this.hide();
            return;
        }

        var OverlayClass = ensureOverlayClass(g);
        var visibleFromZoom = parseZoomValue(config.show_overlay_from_zoom);
        var hideFromZoom = resolveHideFromZoom(config);

        if (!this.overlay_) {
            this.overlay_ = new OverlayClass(
                copyBounds(config.bounds),
                config.overlay_url,
                parseFloat(config.overlay_opacity),
                parseFloat(config.overlay_rotation) || 0
            );
            this.overlay_.visibleFromZoom_ = visibleFromZoom;
            this.overlay_.hideFromZoom_ = hideFromZoom;
            this.overlay_.setMap(this.map);
        } else {
            this.overlay_.setConfig(
                copyBounds(config.bounds),
                config.overlay_url,
                parseFloat(config.overlay_opacity),
                parseFloat(config.overlay_rotation) || 0,
                visibleFromZoom,
                hideFromZoom
            );
        }
    };

    OverlayManager.prototype.updateLive = function (partial) {
        if (!this.overlay_) {
            return;
        }

        var bounds = partial.bounds || this.overlay_.boundsLiteral_;
        var url = partial.overlay_url || this.overlay_.imageUrl_;
        var opacity = partial.overlay_opacity !== undefined ? parseFloat(partial.overlay_opacity) : this.overlay_.opacity_;
        var rotation = partial.overlay_rotation !== undefined ? parseFloat(partial.overlay_rotation) : this.overlay_.rotation_;
        var visibleFromZoom = partial.show_overlay_from_zoom !== undefined
            ? parseZoomValue(partial.show_overlay_from_zoom)
            : this.overlay_.visibleFromZoom_;
        var hideFromZoom = (partial.hide_overlay_from_zoom !== undefined || partial.max_zoom !== undefined)
            ? resolveHideFromZoom(Object.assign({
                hide_overlay_from_zoom: this.overlay_.hideFromZoom_,
                max_zoom: null,
            }, partial))
            : this.overlay_.hideFromZoom_;

        this.overlay_.setConfig(bounds, url, opacity, rotation, visibleFromZoom, hideFromZoom);
    };

    OverlayManager.prototype.hide = function () {
        if (this.overlay_) {
            this.overlay_.setMap(null);
            this.overlay_ = null;
        }
    };

    OverlayManager.prototype.destroy = function () {
        this.hide();
        this.map = null;
    };

    PM.OverlayManager = OverlayManager;
})(window);
