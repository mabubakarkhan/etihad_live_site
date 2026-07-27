(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    function SectionManager(map, options) {
        this.map = map;
        this.options = options || {};
        this.sections = [];
        this.shapes_ = {};
        this.labels_ = {};
        this.selectedId = null;
        this.onSelect = options.onSelect || function () {};
        this.listeners_ = [];
    }

    SectionManager.prototype.load = function (sections) {
        this.clear();
        this.sections = sections || [];
        var self = this;
        this.sections.forEach(function (section) {
            self.renderSection(section);
        });
    };

    SectionManager.prototype.renderSection = function (section) {
        var g = window.google && window.google.maps;
        if (!g || !this.map || !section) {
            return;
        }

        this.removeShape(section.id);

        var shape = null;
        var style = this.styleFromSection(section);

        if (section.section_type === 'polygon' && section.geometry && section.geometry.paths) {
            shape = new g.Polygon({
                paths: section.geometry.paths,
                map: this.map,
                clickable: true,
                zIndex: 2,
                ...style,
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
                map: this.map,
                clickable: true,
                zIndex: 2,
                ...style,
            });
        } else if (section.section_type === 'marker' && section.geometry && section.geometry.position) {
            shape = new g.Marker({
                position: section.geometry.position,
                map: this.map,
                clickable: true,
                zIndex: 3,
                title: section.label || section.title,
            });
        }

        if (!shape) {
            return;
        }

        var self = this;
        var clickListener = g.event.addListener(shape, 'click', function () {
            self.select(section.id);
        });
        this.listeners_.push(clickListener);
        this.shapes_[section.id] = shape;

        if (section.label && section.section_type !== 'marker') {
            this.renderLabel(section);
        }
    };

    SectionManager.prototype.renderLabel = function (section) {
        var g = window.google && window.google.maps;
        var center = this.getSectionCenter(section);
        if (!g || !center) {
            return;
        }

        this.removeLabel(section.id);

        var label = new g.Marker({
            position: center,
            map: this.map,
            clickable: false,
            zIndex: 4,
            icon: {
                path: g.SymbolPath.CIRCLE,
                scale: 0,
            },
            label: {
                text: section.label,
                color: section.stroke_color || '#ffffff',
                fontSize: '12px',
                fontWeight: 'bold',
            },
        });

        this.labels_[section.id] = label;
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
                latSum += p.lat;
                lngSum += p.lng;
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

    SectionManager.prototype.select = function (id) {
        this.selectedId = id;
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

        var section = this.sections.find(function (s) { return String(s.id) === String(id); });
        this.onSelect(section || null);
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
        this.sections = this.sections.filter(function (s) { return String(s.id) !== String(id); });
        this.removeShape(id);
        this.removeLabel(id);
        if (String(this.selectedId) === String(id)) {
            this.selectedId = null;
        }
    };

    SectionManager.prototype.removeShape = function (id) {
        if (this.shapes_[id]) {
            this.shapes_[id].setMap(null);
            delete this.shapes_[id];
        }
    };

    SectionManager.prototype.removeLabel = function (id) {
        if (this.labels_[id]) {
            this.labels_[id].setMap(null);
            delete this.labels_[id];
        }
    };

    SectionManager.prototype.clear = function () {
        var self = this;
        Object.keys(this.shapes_).forEach(function (id) {
            self.removeShape(id);
        });
        Object.keys(this.labels_).forEach(function (id) {
            self.removeLabel(id);
        });
        this.shapes_ = {};
        this.labels_ = {};
        this.sections = [];
        this.selectedId = null;
    };

    SectionManager.prototype.destroy = function () {
        var g = window.google && window.google.maps;
        if (g && this.listeners_.length) {
            this.listeners_.forEach(function (listener) {
                g.event.removeListener(listener);
            });
        }
        this.listeners_ = [];
        this.clear();
        this.map = null;
    };

    PM.SectionManager = SectionManager;
})(window);
