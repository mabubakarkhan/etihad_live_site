(function (window) {
    'use strict';

    var GeographicImageOverlayClass = null;

    function copyBounds(bounds) {
        return {
            north: bounds.north,
            south: bounds.south,
            east: bounds.east,
            west: bounds.west,
        };
    }

    function ensureOverlayClass(g) {
        if (GeographicImageOverlayClass) {
            return GeographicImageOverlayClass;
        }

        function GeographicImageOverlay(boundsLiteral, imageUrl, opacity) {
            this.boundsLiteral_ = boundsLiteral;
            this.imageUrl_ = imageUrl;
            this.opacity_ = typeof opacity === 'number' ? opacity : 0.85;
            this.div_ = null;
            this.img_ = null;
            this.hitLayer_ = null;
            this.draggable_ = false;
            this.onBoundsChange_ = null;
            this.onBoundsDrag_ = null;
            this._dragHandlersBound_ = false;
            this._dragging_ = false;
            this._dragStartBounds_ = null;
            this._dragStartPixel_ = null;
            this._onMouseMove_ = null;
            this._onMouseUp_ = null;
            this._onTouchMove_ = null;
            this._onTouchEnd_ = null;
            this._onMouseDown_ = null;
            this._onTouchStart_ = null;
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
            img.style.position = 'relative';
            img.style.zIndex = '1';

            div.appendChild(img);
            this.div_ = div;
            this.img_ = img;

            this._mountToPane();
            this._ensureHitLayer();
            this._applyInteractionStyles();
            this._bindDragHandlers();
        };

        GeographicImageOverlay.prototype._mountToPane = function () {
            if (!this.div_) {
                return;
            }

            var panes = this.getPanes();
            if (!panes) {
                return;
            }

            var pane = this.draggable_ && panes.overlayMouseTarget
                ? panes.overlayMouseTarget
                : panes.overlayLayer;

            if (pane && this.div_.parentNode !== pane) {
                pane.appendChild(this.div_);
            }
        };

        GeographicImageOverlay.prototype._ensureHitLayer = function () {
            if (!this.div_) {
                return;
            }

            if (!this.draggable_) {
                if (this.hitLayer_ && this.hitLayer_.parentNode) {
                    this.hitLayer_.parentNode.removeChild(this.hitLayer_);
                }
                this.hitLayer_ = null;
                return;
            }

            if (!this.hitLayer_) {
                var hit = document.createElement('div');
                hit.className = 'interactive-map-overlay__drag-hit';
                hit.style.position = 'absolute';
                hit.style.left = '0';
                hit.style.top = '0';
                hit.style.width = '100%';
                hit.style.height = '100%';
                hit.style.zIndex = '2';
                hit.style.cursor = 'grab';
                hit.style.background = 'rgba(169, 130, 61, 0.18)';
                hit.style.pointerEvents = 'auto';
                hit.style.touchAction = 'none';
                this.div_.appendChild(hit);
                this.hitLayer_ = hit;
            }
        };

        GeographicImageOverlay.prototype.draw = function () {
            if (!this.div_ || !this.boundsLiteral_) {
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
            this.div_.style.zIndex = this.draggable_ ? '5' : '1';
        };

        GeographicImageOverlay.prototype.onRemove = function () {
            this._teardownDragHandlers();
            if (this.div_ && this.div_.parentNode) {
                this.div_.parentNode.removeChild(this.div_);
            }
            this.div_ = null;
            this.img_ = null;
            this.hitLayer_ = null;
        };

        GeographicImageOverlay.prototype.setConfig = function (boundsLiteral, imageUrl, opacity) {
            if (this._dragging_) {
                return;
            }

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
            this.draw();
        };

        GeographicImageOverlay.prototype.setInteraction = function (draggable, onBoundsChange, onBoundsDrag) {
            this.draggable_ = !!draggable;
            this.onBoundsChange_ = typeof onBoundsChange === 'function' ? onBoundsChange : null;
            this.onBoundsDrag_ = typeof onBoundsDrag === 'function' ? onBoundsDrag : null;
            this._mountToPane();
            this._ensureHitLayer();
            this._applyInteractionStyles();
            this._bindDragHandlers();
        };

        GeographicImageOverlay.prototype.isDragging = function () {
            return !!this._dragging_;
        };

        GeographicImageOverlay.prototype._applyInteractionStyles = function () {
            if (!this.div_) {
                return;
            }

            if (this.draggable_) {
                this.div_.style.pointerEvents = 'auto';
                this.div_.classList.add('interactive-map-overlay--draggable');
                if (this.hitLayer_) {
                    this.hitLayer_.style.cursor = this._dragging_ ? 'grabbing' : 'grab';
                }
            } else {
                this.div_.style.pointerEvents = 'none';
                this.div_.classList.remove('interactive-map-overlay--draggable');
            }
        };

        GeographicImageOverlay.prototype._getDragTarget = function () {
            return this.hitLayer_ || this.div_;
        };

        GeographicImageOverlay.prototype._bindDragHandlers = function () {
            var target = this._getDragTarget();
            if (!target || !this.draggable_) {
                return;
            }

            if (this._dragHandlersBound_) {
                return;
            }

            var self = this;
            this._dragHandlersBound_ = true;

            this._onMouseMove_ = function (e) {
                self._handleDragMove(e.clientX, e.clientY);
            };
            this._onMouseUp_ = function () {
                self._handleDragEnd();
            };
            this._onTouchMove_ = function (e) {
                if (!e.touches || !e.touches[0]) {
                    return;
                }
                e.preventDefault();
                self._handleDragMove(e.touches[0].clientX, e.touches[0].clientY);
            };
            this._onTouchEnd_ = function () {
                self._handleDragEnd();
            };
            this._onMouseDown_ = function (e) {
                self._handleDragStart(e.clientX, e.clientY, e);
            };
            this._onTouchStart_ = function (e) {
                if (!e.touches || !e.touches[0]) {
                    return;
                }
                self._handleDragStart(e.touches[0].clientX, e.touches[0].clientY, e);
            };

            target.addEventListener('mousedown', this._onMouseDown_);
            target.addEventListener('touchstart', this._onTouchStart_, { passive: false });
        };

        GeographicImageOverlay.prototype._teardownDragHandlers = function () {
            var target = this._getDragTarget();
            if (target && this._onMouseDown_) {
                target.removeEventListener('mousedown', this._onMouseDown_);
            }
            if (target && this._onTouchStart_) {
                target.removeEventListener('touchstart', this._onTouchStart_);
            }
            if (this._onMouseMove_) {
                document.removeEventListener('mousemove', this._onMouseMove_);
            }
            if (this._onMouseUp_) {
                document.removeEventListener('mouseup', this._onMouseUp_);
            }
            if (this._onTouchMove_) {
                document.removeEventListener('touchmove', this._onTouchMove_);
            }
            if (this._onTouchEnd_) {
                document.removeEventListener('touchend', this._onTouchEnd_);
            }
            this._dragHandlersBound_ = false;
            this._dragging_ = false;
        };

        GeographicImageOverlay.prototype._handleDragStart = function (clientX, clientY, e) {
            if (!this.draggable_ || !this.boundsLiteral_) {
                return;
            }

            if (e && e.cancelable) {
                e.preventDefault();
            }
            if (e && e.stopPropagation) {
                e.stopPropagation();
            }

            var map = this.getMap();
            if (map) {
                map.setOptions({ draggable: false, gestureHandling: 'none' });
            }

            this._dragging_ = true;
            this._dragStartBounds_ = copyBounds(this.boundsLiteral_);
            this._dragStartPixel_ = { x: clientX, y: clientY };
            this._applyInteractionStyles();

            document.addEventListener('mousemove', this._onMouseMove_);
            document.addEventListener('mouseup', this._onMouseUp_);
            document.addEventListener('touchmove', this._onTouchMove_, { passive: false });
            document.addEventListener('touchend', this._onTouchEnd_);
        };

        GeographicImageOverlay.prototype._handleDragMove = function (clientX, clientY) {
            if (!this._dragging_ || !this._dragStartBounds_ || !this._dragStartPixel_) {
                return;
            }

            var projection = this.getProjection();
            if (!projection) {
                return;
            }

            var startBounds = this._dragStartBounds_;
            var centerLat = (startBounds.north + startBounds.south) / 2;
            var centerLng = (startBounds.east + startBounds.west) / 2;
            var startCenter = projection.fromLatLngToDivPixel(new g.LatLng(centerLat, centerLng));

            if (!startCenter) {
                return;
            }

            var deltaX = clientX - this._dragStartPixel_.x;
            var deltaY = clientY - this._dragStartPixel_.y;
            var newCenter = projection.fromDivPixelToLatLng(
                new g.Point(startCenter.x + deltaX, startCenter.y + deltaY)
            );

            if (!newCenter) {
                return;
            }

            var deltaLat = newCenter.lat() - centerLat;
            var deltaLng = newCenter.lng() - centerLng;

            this.boundsLiteral_ = {
                north: startBounds.north + deltaLat,
                south: startBounds.south + deltaLat,
                east: startBounds.east + deltaLng,
                west: startBounds.west + deltaLng,
            };

            this.draw();

            if (typeof this.onBoundsDrag_ === 'function') {
                this.onBoundsDrag_(copyBounds(this.boundsLiteral_));
            }
        };

        GeographicImageOverlay.prototype._handleDragEnd = function () {
            if (!this._dragging_) {
                return;
            }

            this._dragging_ = false;
            this._dragStartBounds_ = null;
            this._dragStartPixel_ = null;

            document.removeEventListener('mousemove', this._onMouseMove_);
            document.removeEventListener('mouseup', this._onMouseUp_);
            document.removeEventListener('touchmove', this._onTouchMove_);
            document.removeEventListener('touchend', this._onTouchEnd_);

            var map = this.getMap();
            if (map) {
                map.setOptions({ draggable: true, gestureHandling: 'auto' });
            }

            this._applyInteractionStyles();

            if (this.boundsLiteral_ && typeof this.onBoundsChange_ === 'function') {
                this.onBoundsChange_(copyBounds(this.boundsLiteral_));
            }
        };

        GeographicImageOverlayClass = GeographicImageOverlay;
        return GeographicImageOverlayClass;
    }

    function normalizeBounds(boundsLiteral) {
        if (!boundsLiteral) {
            return null;
        }

        var north = parseFloat(boundsLiteral.north);
        var south = parseFloat(boundsLiteral.south);
        var east = parseFloat(boundsLiteral.east);
        var west = parseFloat(boundsLiteral.west);

        if ([north, south, east, west].some(function (v) { return isNaN(v); })) {
            return null;
        }

        if (north <= south) {
            return null;
        }

        return { north: north, south: south, east: east, west: west };
    }

    function OverlayManager(options) {
        this.map = options.map;
        this.overlay = null;
        this.opacity = typeof options.opacity === 'number' ? options.opacity : 0.85;
        this.draggable = !!options.draggable;
        this.onBoundsChange = typeof options.onBoundsChange === 'function' ? options.onBoundsChange : null;
        this.onBoundsDrag = typeof options.onBoundsDrag === 'function' ? options.onBoundsDrag : null;
        this.currentConfig = null;
    }

    OverlayManager.prototype.isDragging = function () {
        return this.overlay && typeof this.overlay.isDragging === 'function' && this.overlay.isDragging();
    };

    OverlayManager.prototype.clear = function () {
        if (this.overlay) {
            this.overlay.setMap(null);
            this.overlay = null;
        }
        this.currentConfig = null;
    };

    OverlayManager.prototype.setMap = function (map) {
        this.map = map;
        if (this.overlay) {
            this.overlay.setMap(map);
        }
    };

    OverlayManager.prototype._applyInteraction = function () {
        if (this.overlay && typeof this.overlay.setInteraction === 'function') {
            this.overlay.setInteraction(this.draggable, this.onBoundsChange, this.onBoundsDrag);
        }
    };

    OverlayManager.prototype.syncBounds = function (boundsLiteral) {
        var bounds = normalizeBounds(boundsLiteral);
        if (!bounds || !this.overlay || !this.currentConfig) {
            return;
        }

        this.currentConfig.bounds = bounds;
        this.overlay.setConfig(bounds, this.currentConfig.url, this.opacity);
    };

    OverlayManager.prototype.update = function (config) {
        var g = window.google && window.google.maps;
        if (!g || !this.map) {
            return;
        }

        if (this.isDragging()) {
            return;
        }

        if (!config || !config.url || !config.bounds) {
            this.clear();
            return;
        }

        var bounds = normalizeBounds(config.bounds);
        if (!bounds) {
            this.clear();
            return;
        }

        this.opacity = typeof config.opacity === 'number' ? config.opacity : this.opacity;
        this.currentConfig = {
            url: config.url,
            bounds: bounds,
            opacity: this.opacity,
        };

        if (this.overlay) {
            this.overlay.setConfig(bounds, config.url, this.opacity);
            this._applyInteraction();
            return;
        }

        var OverlayClass = ensureOverlayClass(g);
        this.overlay = new OverlayClass(bounds, config.url, this.opacity);
        this.overlay.draggable_ = this.draggable;
        this.overlay.onBoundsChange_ = this.onBoundsChange;
        this.overlay.onBoundsDrag_ = this.onBoundsDrag;
        this.overlay.setMap(this.map);
        this._applyInteraction();
    };

    window.InteractiveMap = window.InteractiveMap || {};
    window.InteractiveMap.OverlayManager = OverlayManager;
})(window);
