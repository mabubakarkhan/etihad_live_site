(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    function DrawingManager(map, options) {
        this.map = map;
        this.options = options || {};
        this.mode = null;
        this.onComplete = typeof options.onComplete === 'function' ? options.onComplete : function () {};
        this.listeners_ = [];
        this.googleListeners_ = [];
        this.manualListeners_ = [];
        this.hintEl = options.hintEl || null;
        this.tempShapes_ = [];
        this.polygonPath_ = [];
        this.rectStart_ = null;
        this.rectPreview_ = null;
        this.googleDrawing_ = null;
        this.useGoogleDrawing_ = false;
        this.mapOptionsBackup_ = null;
    }

    DrawingManager.prototype.init = function () {
        var g = window.google && window.google.maps;
        if (!g || !this.map) {
            return false;
        }

        // Use manual click-to-draw — works without the Google Drawing library.
        this.useGoogleDrawing_ = false;
        return true;
    };

    DrawingManager.prototype.shapeOptions = function () {
        return {
            fillColor: this.options.fillColor || '#a9823d',
            strokeColor: this.options.strokeColor || '#6c4815',
            fillOpacity: typeof this.options.fillOpacity === 'number' ? this.options.fillOpacity : 0.45,
            strokeOpacity: typeof this.options.strokeOpacity === 'number' ? this.options.strokeOpacity : 0.9,
            strokeWeight: this.options.strokeWeight || 2,
            editable: false,
            draggable: false,
        };
    };

    DrawingManager.prototype.setStyle = function (style) {
        this.options = Object.assign({}, this.options, style || {});
        if (this.googleDrawing_) {
            this.googleDrawing_.setOptions({
                polygonOptions: this.shapeOptions(),
                rectangleOptions: this.shapeOptions(),
            });
        }
        this.refreshTempStyles();
    };

    DrawingManager.prototype.setMode = function (mode) {
        this.clearManualState();
        this.mode = mode || null;

        if (this.useGoogleDrawing_ && this.googleDrawing_) {
            this.setGoogleMode(mode);
            this.setHint(this.hintForMode(mode));
            return;
        }

        this.setManualMode(mode);
        this.setHint(this.hintForMode(mode));
    };

    DrawingManager.prototype.cancel = function () {
        this.setMode(null);
        this.setHint('');
    };

    DrawingManager.prototype.setGoogleMode = function (mode) {
        var g = window.google.maps;
        var drawingMode = null;

        if (mode === 'polygon') {
            drawingMode = g.drawing.OverlayType.POLYGON;
        } else if (mode === 'rectangle') {
            drawingMode = g.drawing.OverlayType.RECTANGLE;
        } else if (mode === 'marker') {
            drawingMode = g.drawing.OverlayType.MARKER;
        }

        this.googleDrawing_.setDrawingMode(drawingMode);
    };

    DrawingManager.prototype.setManualMode = function (mode) {
        this.teardownManualListeners();
        this.restoreMapInteraction();

        if (!mode) {
            return;
        }

        var self = this;
        var g = window.google.maps;

        if (mode === 'polygon') {
            this.backupMapInteraction({ draggable: true, doubleClickZoom: false });
            this.manualListeners_.push(g.event.addListener(this.map, 'click', function (e) {
                self.addPolygonPoint(e.latLng);
            }));
            this.manualListeners_.push(g.event.addListener(this.map, 'dblclick', function (e) {
                e.stop();
                self.finishPolygon();
            }));
        } else if (mode === 'rectangle') {
            this.backupMapInteraction({ draggable: false, doubleClickZoom: false });
            this.manualListeners_.push(g.event.addListener(this.map, 'mousedown', function (e) {
                self.beginRectangle(e.latLng);
            }));
            this.manualListeners_.push(g.event.addListener(this.map, 'mousemove', function (e) {
                self.updateRectangle(e.latLng);
            }));
            this.manualListeners_.push(g.event.addListener(this.map, 'mouseup', function (e) {
                self.finishRectangle(e.latLng);
            }));
        } else if (mode === 'marker') {
            this.backupMapInteraction({ draggable: true, doubleClickZoom: true });
            this.manualListeners_.push(g.event.addListener(this.map, 'click', function (e) {
                self.complete({
                    section_type: 'marker',
                    geometry: { position: { lat: e.latLng.lat(), lng: e.latLng.lng() } },
                });
            }));
        }
    };

    DrawingManager.prototype.backupMapInteraction = function (opts) {
        if (!this.map) {
            return;
        }
        this.mapOptionsBackup_ = {
            draggable: this.map.get('draggable'),
            doubleClickZoom: this.map.get('doubleClickZoom'),
        };
        this.map.setOptions(opts);
    };

    DrawingManager.prototype.restoreMapInteraction = function () {
        if (!this.map || !this.mapOptionsBackup_) {
            return;
        }
        this.map.setOptions(this.mapOptionsBackup_);
        this.mapOptionsBackup_ = null;
    };

    DrawingManager.prototype.addPolygonPoint = function (latLng) {
        var g = window.google.maps;
        this.polygonPath_.push(latLng);

        var marker = new g.Marker({
            position: latLng,
            map: this.map,
            clickable: false,
            icon: {
                path: g.SymbolPath.CIRCLE,
                scale: 5,
                fillColor: this.options.strokeColor || '#6c4815',
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 1,
            },
        });
        this.tempShapes_.push(marker);

        if (this.polygonPreview_) {
            this.polygonPreview_.setMap(null);
        }

        if (this.polygonPath_.length >= 2) {
            this.polygonPreview_ = new g.Polygon({
                paths: this.polygonPath_,
                map: this.map,
                clickable: false,
                ...this.shapeOptions(),
            });
            this.tempShapes_.push(this.polygonPreview_);
        }

        this.setHint('Polygon: click to add points, double-click to finish (' + this.polygonPath_.length + ' points)');
    };

    DrawingManager.prototype.finishPolygon = function () {
        if (this.polygonPath_.length < 3) {
            this.setHint('Polygon needs at least 3 points. Keep clicking.');
            return;
        }

        var paths = this.polygonPath_.map(function (latLng) {
            return { lat: latLng.lat(), lng: latLng.lng() };
        });

        this.complete({
            section_type: 'polygon',
            geometry: { paths: paths },
        });
    };

    DrawingManager.prototype.beginRectangle = function (latLng) {
        this.rectStart_ = latLng;
    };

    DrawingManager.prototype.updateRectangle = function (latLng) {
        if (!this.rectStart_) {
            return;
        }

        var g = window.google.maps;
        var bounds = this.boundsFromPoints(this.rectStart_, latLng);

        if (this.rectPreview_) {
            this.rectPreview_.setBounds(bounds);
            return;
        }

        this.rectPreview_ = new g.Rectangle({
            bounds: bounds,
            map: this.map,
            clickable: false,
            ...this.shapeOptions(),
        });
        this.tempShapes_.push(this.rectPreview_);
    };

    DrawingManager.prototype.finishRectangle = function (latLng) {
        if (!this.rectStart_) {
            return;
        }

        var bounds = this.boundsFromPoints(this.rectStart_, latLng);
        var ne = bounds.getNorthEast();
        var sw = bounds.getSouthWest();

        if (Math.abs(ne.lat() - sw.lat()) < 0.00001 || Math.abs(ne.lng() - sw.lng()) < 0.00001) {
            this.clearManualState();
            this.setManualMode('rectangle');
            return;
        }

        this.complete({
            section_type: 'rectangle',
            geometry: {
                bounds: {
                    north: ne.lat(),
                    south: sw.lat(),
                    east: ne.lng(),
                    west: sw.lng(),
                },
            },
        });
    };

    DrawingManager.prototype.boundsFromPoints = function (a, b) {
        var g = window.google.maps;
        return new g.LatLngBounds(
            new g.LatLng(Math.min(a.lat(), b.lat()), Math.min(a.lng(), b.lng())),
            new g.LatLng(Math.max(a.lat(), b.lat()), Math.max(a.lng(), b.lng()))
        );
    };

    DrawingManager.prototype.handleGoogleOverlayComplete = function (event) {
        var g = window.google.maps;
        var payload = null;

        if (event.type === g.drawing.OverlayType.POLYGON) {
            payload = {
                section_type: 'polygon',
                geometry: {
                    paths: event.overlay.getPath().getArray().map(function (latLng) {
                        return { lat: latLng.lat(), lng: latLng.lng() };
                    }),
                },
            };
        } else if (event.type === g.drawing.OverlayType.RECTANGLE) {
            var bounds = event.overlay.getBounds();
            payload = {
                section_type: 'rectangle',
                geometry: {
                    bounds: {
                        north: bounds.getNorthEast().lat(),
                        south: bounds.getSouthWest().lat(),
                        east: bounds.getNorthEast().lng(),
                        west: bounds.getSouthWest().lng(),
                    },
                },
            };
        } else if (event.type === g.drawing.OverlayType.MARKER) {
            var pos = event.overlay.getPosition();
            payload = {
                section_type: 'marker',
                geometry: { position: { lat: pos.lat(), lng: pos.lng() } },
            };
        }

        event.overlay.setMap(null);
        if (payload) {
            this.complete(payload);
        }
    };

    DrawingManager.prototype.complete = function (payload) {
        this.clearManualState();
        this.setMode(null);
        this.onComplete(payload);
    };

    DrawingManager.prototype.clearManualState = function () {
        var self = this;
        this.tempShapes_.forEach(function (shape) {
            if (shape && shape.setMap) {
                shape.setMap(null);
            }
        });
        this.tempShapes_ = [];
        this.polygonPath_ = [];
        this.polygonPreview_ = null;
        this.rectStart_ = null;
        this.rectPreview_ = null;
        this.teardownManualListeners();
        this.restoreMapInteraction();

        if (this.googleDrawing_) {
            this.googleDrawing_.setDrawingMode(null);
        }
    };

    DrawingManager.prototype.teardownManualListeners = function () {
        var g = window.google && window.google.maps;
        if (!g) {
            return;
        }
        while (this.manualListeners_.length) {
            g.event.removeListener(this.manualListeners_.pop());
        }
    };

    DrawingManager.prototype.teardownGoogleListeners = function () {
        var g = window.google && window.google.maps;
        if (!g) {
            return;
        }
        while (this.googleListeners_.length) {
            g.event.removeListener(this.googleListeners_.pop());
        }
    };

    DrawingManager.prototype.refreshTempStyles = function () {
        if (this.polygonPreview_ && this.polygonPreview_.setOptions) {
            this.polygonPreview_.setOptions(this.shapeOptions());
        }
        if (this.rectPreview_ && this.rectPreview_.setOptions) {
            this.rectPreview_.setOptions(this.shapeOptions());
        }
    };

    DrawingManager.prototype.hintForMode = function (mode) {
        if (mode === 'polygon') {
            return 'Polygon mode: click map to add corners, double-click to finish.';
        }
        if (mode === 'rectangle') {
            return 'Rectangle mode: click and drag on the map to draw a box.';
        }
        if (mode === 'marker') {
            return 'Marker mode: click on the map to drop a plot marker.';
        }
        return '';
    };

    DrawingManager.prototype.setHint = function (text) {
        if (this.hintEl) {
            this.hintEl.textContent = text || '';
            this.hintEl.hidden = !text;
        }
    };

    DrawingManager.prototype.destroy = function () {
        this.clearManualState();
        this.teardownGoogleListeners();
        if (this.googleDrawing_) {
            this.googleDrawing_.setMap(null);
            this.googleDrawing_ = null;
        }
        this.map = null;
    };

    PM.DrawingManager = DrawingManager;
})(window);
