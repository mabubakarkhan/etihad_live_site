(function (window) {
    'use strict';

    var IM = window.InteractiveMap = window.InteractiveMap || {};

    function SectionManager(map, options) {
        this.map = map;
        this.options = options || {};
        this.sections = [];
        this.shapes_ = {};
        this.labels_ = {};
        this.hidden_ = {};
        this.selectedId = null;
        this.editingId_ = null;
        this.interactive_ = true;
        this.defaultLabelFromZoom_ = 16;
        this.onSelect = options.onSelect || function () {};
        this.onGeometryChange = options.onGeometryChange || function () {};
        this.listeners_ = [];
        this.geometryListeners_ = [];
    }

    SectionManager.prototype.setDefaultLabelZoom = function (zoom) {
        if (zoom === null || zoom === undefined || zoom === '') {
            this.defaultLabelFromZoom_ = 16;
        } else {
            var parsed = parseInt(zoom, 10);
            this.defaultLabelFromZoom_ = isNaN(parsed) ? 16 : parsed;
        }
        this.applyLabelVisibility();
    };

    SectionManager.prototype.load = function (sections) {
        this.clear();
        this.sections = sections || [];
        var self = this;
        this.sections.forEach(function (section) {
            self.renderSection(section);
        });
        this.applyLabelVisibility();
    };

    SectionManager.prototype.renderSection = function (section) {
        var g = window.google && window.google.maps;
        if (!g || !this.map || !section) {
            return;
        }

        var wasEditing = String(this.editingId_) === String(section.id);
        this.removeShape(section.id);

        var shape = null;
        var style = this.styleFromSection(section);
        var zIndex = 2 + (parseInt(section.sort_order, 10) || 0);
        var hidden = !!this.hidden_[section.id];
        var mapRef = hidden ? null : this.map;

        if (section.section_type === 'polygon' && section.geometry && section.geometry.paths) {
            shape = new g.Polygon({
                paths: section.geometry.paths,
                map: mapRef,
                clickable: this.interactive_,
                editable: false,
                draggable: false,
                zIndex: zIndex,
                fillColor: style.fillColor,
                strokeColor: style.strokeColor,
                fillOpacity: style.fillOpacity,
                strokeOpacity: style.strokeOpacity,
                strokeWeight: style.strokeWeight,
            });
        } else if (section.section_type === 'rectangle' && section.geometry && section.geometry.bounds) {
            var b = section.geometry.bounds;
            shape = new g.Rectangle({
                bounds: {
                    north: b.north,
                    south: b.south,
                    east: b.east,
                    west: b.west,
                },
                map: mapRef,
                clickable: this.interactive_,
                editable: false,
                draggable: false,
                zIndex: zIndex,
                fillColor: style.fillColor,
                strokeColor: style.strokeColor,
                fillOpacity: style.fillOpacity,
                strokeOpacity: style.strokeOpacity,
                strokeWeight: style.strokeWeight,
            });
        } else if (section.section_type === 'marker' && section.geometry && section.geometry.position) {
            shape = new g.Marker({
                position: section.geometry.position,
                map: mapRef,
                clickable: this.interactive_,
                draggable: false,
                zIndex: zIndex + 1,
                title: this.labelText(section),
            });
        }

        if (!shape) {
            return;
        }

        var self = this;
        var clickListener = g.event.addListener(shape, 'click', function () {
            if (!self.interactive_) {
                return;
            }
            self.select(section.id);
        });
        this.listeners_.push(clickListener);
        this.shapes_[String(section.id)] = shape;

        if (String(this.selectedId) === String(section.id)) {
            this.applySelectionStyle(section.id);
        }

        if (wasEditing && this.interactive_) {
            this.setEditing(section.id);
        }

        this.updateSectionLabelVisibility(section, this.currentZoom());
    };

    SectionManager.prototype.labelText = function (section) {
        var label = section && section.label ? String(section.label).trim() : '';
        if (label) {
            return label;
        }
        return section && section.title ? String(section.title).trim() : '';
    };

    SectionManager.prototype.labelThreshold = function (section) {
        if (section && section.show_label_from_zoom !== null && section.show_label_from_zoom !== undefined && section.show_label_from_zoom !== '') {
            var parsed = parseInt(section.show_label_from_zoom, 10);
            if (!isNaN(parsed)) {
                return parsed;
            }
        }
        return this.defaultLabelFromZoom_;
    };

    SectionManager.prototype.currentZoom = function () {
        if (!this.map || typeof this.map.getZoom !== 'function') {
            return 0;
        }
        return this.map.getZoom() || 0;
    };

    SectionManager.prototype.applyLabelVisibility = function (zoom) {
        var current = typeof zoom === 'number' ? zoom : this.currentZoom();
        var self = this;
        this.sections.forEach(function (section) {
            self.updateSectionLabelVisibility(section, current);
        });
    };

    SectionManager.prototype.updateSectionLabelVisibility = function (section, zoom) {
        if (!section) {
            return;
        }

        if (this.hidden_[section.id]) {
            this.removeLabel(section.id);
            return;
        }

        var text = this.labelText(section);
        var show = !!text && zoom >= this.labelThreshold(section);

        if (section.section_type === 'marker') {
            var marker = this.shapes_[section.id];
            if (marker && marker.setLabel) {
                marker.setLabel(show ? {
                    text: text,
                    color: section.stroke_color || '#ffffff',
                    fontSize: '12px',
                    fontWeight: 'bold',
                } : null);
            }
            return;
        }

        if (show) {
            this.renderLabel(section);
        } else {
            this.removeLabel(section.id);
        }
    };

    SectionManager.prototype.renderLabel = function (section) {
        var g = window.google && window.google.maps;
        var text = this.labelText(section);
        var center = this.getSectionCenter(section);
        if (!g || !center || !text || this.hidden_[section.id]) {
            return;
        }

        this.removeLabel(section.id);

        var label = new g.Marker({
            position: center,
            map: this.map,
            clickable: false,
            optimized: false,
            zIndex: 999,
            icon: {
                path: g.SymbolPath.CIRCLE,
                scale: 0,
            },
            label: {
                text: text,
                color: section.stroke_color || '#ffffff',
                fontSize: '12px',
                fontWeight: 'bold',
            },
        });

        this.labels_[String(section.id)] = label;
    };

    SectionManager.prototype.getSectionCenter = function (section) {
        if (section.section_type === 'marker' && section.geometry && section.geometry.position) {
            return section.geometry.position;
        }

        if (section.section_type === 'rectangle' && section.geometry && section.geometry.bounds) {
            var b = section.geometry.bounds;
            return {
                lat: (b.north + b.south) / 2,
                lng: (b.east + b.west) / 2,
            };
        }

        if (section.section_type === 'polygon' && section.geometry && section.geometry.paths && section.geometry.paths.length) {
            var latSum = 0;
            var lngSum = 0;
            section.geometry.paths.forEach(function (p) {
                latSum += parseFloat(p.lat);
                lngSum += parseFloat(p.lng);
            });
            return {
                lat: latSum / section.geometry.paths.length,
                lng: lngSum / section.geometry.paths.length,
            };
        }

        return null;
    };

    SectionManager.prototype.styleFromSection = function (section) {
        return {
            fillColor: section.fill_color || '#a9823d',
            strokeColor: section.stroke_color || '#6c4815',
            fillOpacity: typeof section.fill_opacity === 'number' ? section.fill_opacity : 0.45,
            strokeOpacity: typeof section.stroke_opacity === 'number' ? section.stroke_opacity : 0.9,
            strokeWeight: section.stroke_weight || 2,
        };
    };

    SectionManager.prototype.applySelectionStyle = function (id) {
        var self = this;
        Object.keys(this.shapes_).forEach(function (key) {
            var shape = self.shapes_[key];
            var section = self.sections.find(function (s) { return String(s.id) === String(key); });
            if (!section || !shape.setOptions) {
                return;
            }

            var selected = String(key) === String(id);
            shape.setOptions({
                strokeWeight: selected ? (section.stroke_weight || 2) + 2 : (section.stroke_weight || 2),
                strokeColor: selected ? '#22d3ee' : (section.stroke_color || '#6c4815'),
            });
        });
    };

    SectionManager.prototype.select = function (id) {
        this.selectedId = id;
        this.applySelectionStyle(id);
        if (id && this.interactive_) {
            this.setEditing(id);
        } else {
            this.stopEditing();
        }
        var section = this.sections.find(function (s) { return String(s.id) === String(id); });
        this.onSelect(section || null);
    };

    SectionManager.prototype.clearSelection = function () {
        this.selectedId = null;
        this.stopEditing();
        this.applySelectionStyle(null);
        this.onSelect(null);
    };

    SectionManager.prototype.setInteractive = function (enabled) {
        this.interactive_ = !!enabled;
        var self = this;
        Object.keys(this.shapes_).forEach(function (id) {
            var shape = self.shapes_[id];
            if (!shape) {
                return;
            }
            if (shape.setOptions) {
                shape.setOptions({
                    clickable: self.interactive_,
                    editable: false,
                    draggable: false,
                });
            }
            if (shape.setClickable) {
                shape.setClickable(self.interactive_);
            }
            if (shape.setDraggable) {
                shape.setDraggable(false);
            }
        });
        if (!this.interactive_) {
            this.stopEditing();
        } else if (this.selectedId) {
            this.applySelectionStyle(this.selectedId);
            this.setEditing(this.selectedId);
        }
    };

    SectionManager.prototype.setEditing = function (id) {
        this.stopEditing();
        if (!id || !this.interactive_ || this.hidden_[id] || this.hidden_[String(id)]) {
            return;
        }

        var key = String(id);
        var shape = this.shapes_[key] || this.shapes_[id];
        var section = this.sections.find(function (s) { return String(s.id) === key; });
        if (!shape || !section) {
            return;
        }

        this.editingId_ = key;

        if (section.section_type === 'polygon' || section.section_type === 'rectangle') {
            shape.setOptions({ editable: true, draggable: true });
        } else if (section.section_type === 'marker' && shape.setDraggable) {
            shape.setDraggable(true);
        }

        this.bindGeometryListeners(id, shape, section.section_type);
    };

    SectionManager.prototype.stopEditing = function () {
        if (!this.editingId_) {
            this.clearGeometryListeners();
            return;
        }

        var shape = this.shapes_[this.editingId_];
        if (shape && shape.setOptions) {
            shape.setOptions({ editable: false, draggable: false });
        }
        if (shape && shape.setDraggable) {
            shape.setDraggable(false);
        }

        this.clearGeometryListeners();
        this.editingId_ = null;
    };

    SectionManager.prototype.bindGeometryListeners = function (id, shape, type) {
        var g = window.google && window.google.maps;
        if (!g || !shape) {
            return;
        }

        this.clearGeometryListeners();
        var self = this;
        var emit = function () {
            var geometry = self.getGeometryFromShape(id);
            if (!geometry) {
                return;
            }
            self.syncGeometry(id, geometry);
            self.onGeometryChange(id, geometry);
        };

        if (type === 'polygon' && shape.getPath) {
            var path = shape.getPath();
            ['set_at', 'insert_at', 'remove_at'].forEach(function (evt) {
                self.geometryListeners_.push(g.event.addListener(path, evt, emit));
            });
            self.geometryListeners_.push(g.event.addListener(shape, 'dragend', emit));
        } else if (type === 'rectangle') {
            self.geometryListeners_.push(g.event.addListener(shape, 'bounds_changed', emit));
            self.geometryListeners_.push(g.event.addListener(shape, 'dragend', emit));
        } else if (type === 'marker') {
            self.geometryListeners_.push(g.event.addListener(shape, 'dragend', emit));
        }
    };

    SectionManager.prototype.clearGeometryListeners = function () {
        var g = window.google && window.google.maps;
        if (g) {
            this.geometryListeners_.forEach(function (listener) {
                g.event.removeListener(listener);
            });
        }
        this.geometryListeners_ = [];
    };

    SectionManager.prototype.getVertices = function (id) {
        var key = String(id);
        var section = this.sections.find(function (s) { return String(s.id) === key; });
        if (!section || !section.geometry) {
            return [];
        }

        if (section.section_type === 'polygon' && Array.isArray(section.geometry.paths)) {
            return section.geometry.paths.map(function (point, index) {
                return {
                    index: index,
                    lat: parseFloat(point.lat),
                    lng: parseFloat(point.lng),
                    label: 'Point ' + (index + 1),
                };
            }).filter(function (point) {
                return !isNaN(point.lat) && !isNaN(point.lng);
            });
        }

        if (section.section_type === 'rectangle' && section.geometry.bounds) {
            var b = section.geometry.bounds;
            return [
                { index: 0, lat: parseFloat(b.north), lng: parseFloat(b.west), label: 'NW' },
                { index: 1, lat: parseFloat(b.north), lng: parseFloat(b.east), label: 'NE' },
                { index: 2, lat: parseFloat(b.south), lng: parseFloat(b.east), label: 'SE' },
                { index: 3, lat: parseFloat(b.south), lng: parseFloat(b.west), label: 'SW' },
            ];
        }

        if (section.section_type === 'marker' && section.geometry.position) {
            return [{
                index: 0,
                lat: parseFloat(section.geometry.position.lat),
                lng: parseFloat(section.geometry.position.lng),
                label: 'Marker',
            }];
        }

        return [];
    };

    SectionManager.prototype.focusVertex = function (id, index) {
        var vertices = this.getVertices(id);
        var point = vertices[index];
        if (!point || !this.map) {
            return;
        }
        this.map.panTo({ lat: point.lat, lng: point.lng });
        this.select(id);
        this.pulseVertexHint_(point);
    };

    SectionManager.prototype.pulseVertexHint_ = function (point) {
        var g = window.google && window.google.maps;
        if (!g || !this.map || !point) {
            return;
        }
        if (this._vertexHint_) {
            this._vertexHint_.setMap(null);
            this._vertexHint_ = null;
        }
        this._vertexHint_ = new g.Marker({
            map: this.map,
            position: { lat: point.lat, lng: point.lng },
            clickable: false,
            zIndex: 999,
            icon: {
                path: g.SymbolPath.CIRCLE,
                scale: 9,
                fillColor: '#0ea5e9',
                fillOpacity: 0.95,
                strokeColor: '#ffffff',
                strokeWeight: 2,
            },
        });
        var self = this;
        clearTimeout(this._vertexHintTimer_);
        this._vertexHintTimer_ = setTimeout(function () {
            if (self._vertexHint_) {
                self._vertexHint_.setMap(null);
                self._vertexHint_ = null;
            }
        }, 1600);
    };

    SectionManager.prototype.removeVertexAt = function (id, index) {
        var key = String(id);
        var section = this.sections.find(function (s) { return String(s.id) === key; });
        var shape = this.shapes_[key] || this.shapes_[id];
        if (!section || !shape) {
            return { ok: false, message: 'Plot not found.' };
        }

        if (section.section_type !== 'polygon' || !shape.getPath) {
            return { ok: false, message: 'Only polygon points can be deleted from the list.' };
        }

        var path = shape.getPath();
        if (path.getLength() <= 3) {
            return { ok: false, message: 'A polygon needs at least 3 points.' };
        }
        if (index < 0 || index >= path.getLength()) {
            return { ok: false, message: 'Point not found.' };
        }

        path.removeAt(index);
        this.select(key);
        var geometry = this.getGeometryFromShape(key);
        if (geometry) {
            this.syncGeometry(key, geometry);
            this.onGeometryChange(key, geometry);
        }
        return { ok: true, geometry: geometry };
    };

    SectionManager.prototype.getGeometryFromShape = function (id) {
        var key = String(id);
        var shape = this.shapes_[key] || this.shapes_[id];
        var section = this.sections.find(function (s) { return String(s.id) === key; });
        if (!shape || !section) {
            return null;
        }

        if (section.section_type === 'polygon' && shape.getPath) {
            return {
                paths: shape.getPath().getArray().map(function (latLng) {
                    return { lat: latLng.lat(), lng: latLng.lng() };
                }),
            };
        }

        if (section.section_type === 'rectangle' && shape.getBounds) {
            var bounds = shape.getBounds();
            if (!bounds) {
                return null;
            }
            return {
                bounds: {
                    north: bounds.getNorthEast().lat(),
                    south: bounds.getSouthWest().lat(),
                    east: bounds.getNorthEast().lng(),
                    west: bounds.getSouthWest().lng(),
                },
            };
        }

        if (section.section_type === 'marker' && shape.getPosition) {
            var pos = shape.getPosition();
            return { position: { lat: pos.lat(), lng: pos.lng() } };
        }

        return null;
    };

    SectionManager.prototype.syncGeometry = function (id, geometry) {
        var section = this.sections.find(function (s) { return String(s.id) === String(id); });
        if (!section) {
            return;
        }
        section.geometry = geometry;
        this.updateSectionLabelVisibility(section, this.currentZoom());
        if (this.labels_[id] && this.labels_[id].setPosition) {
            var center = this.getSectionCenter(section);
            if (center) {
                this.labels_[id].setPosition(center);
            }
        }
    };

    SectionManager.prototype.syncData = function (section) {
        if (!section) {
            return;
        }
        var index = this.sections.findIndex(function (s) { return String(s.id) === String(section.id); });
        if (index > -1) {
            this.sections[index] = Object.assign({}, this.sections[index], section);
        }
    };

    SectionManager.prototype.setHidden = function (id, hidden) {
        this.hidden_[id] = !!hidden;
        var shape = this.shapes_[id];
        if (shape) {
            shape.setMap(hidden ? null : this.map);
        }
        if (hidden) {
            this.removeLabel(id);
            if (String(this.editingId_) === String(id)) {
                this.stopEditing();
            }
        } else {
            var section = this.sections.find(function (s) { return String(s.id) === String(id); });
            this.updateSectionLabelVisibility(section, this.currentZoom());
        }
    };

    SectionManager.prototype.upsert = function (section) {
        var index = this.sections.findIndex(function (s) { return String(s.id) === String(section.id); });
        if (index > -1) {
            this.sections[index] = section;
        } else {
            this.sections.push(section);
        }
        this.renderSection(section);
    };

    SectionManager.prototype.remove = function (id) {
        var key = String(id);
        this.stopEditing();
        this.sections = this.sections.filter(function (s) { return String(s.id) !== key; });
        this.removeShape(key);
        this.removeLabel(key);
        delete this.hidden_[key];
        delete this.hidden_[id];
        if (String(this.selectedId) === key) {
            this.selectedId = null;
        }
    };

    SectionManager.prototype.removeShape = function (id) {
        var key = String(id);
        var shape = this.shapes_[key] || this.shapes_[id];
        if (shape) {
            shape.setMap(null);
            delete this.shapes_[key];
            delete this.shapes_[id];
        }
    };

    SectionManager.prototype.removeLabel = function (id) {
        var key = String(id);
        var label = this.labels_[key] || this.labels_[id];
        if (label) {
            label.setMap(null);
            delete this.labels_[key];
            delete this.labels_[id];
        }
    };

    SectionManager.prototype.clear = function () {
        var self = this;
        this.stopEditing();
        Object.keys(this.shapes_).forEach(function (id) {
            self.removeShape(id);
        });
        Object.keys(this.labels_).forEach(function (id) {
            self.removeLabel(id);
        });
        this.shapes_ = {};
        this.labels_ = {};
        this.hidden_ = {};
        this.sections = [];
        this.selectedId = null;
    };

    SectionManager.prototype.destroy = function () {
        var g = window.google && window.google.maps;
        this.clearGeometryListeners();
        if (g && this.listeners_.length) {
            this.listeners_.forEach(function (listener) {
                g.event.removeListener(listener);
            });
        }
        this.listeners_ = [];
        this.clear();
        this.map = null;
    };

    IM.SectionManager = SectionManager;
})(window);
