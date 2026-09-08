(function (window) {
    'use strict';

    var IM = window.InteractiveMap = window.InteractiveMap || {};

    function SectionPanel(root, options) {
        this.root = root;
        this.options = options || {};
        this.csrf = options.csrf || '';
        this.routes = options.routes || {};
        this.sections = (options.sections || []).slice().sort(compareSort);
        this.selected = null;
        this.hiddenIds = {};
        this.onDrawStyleChange = options.onDrawStyleChange || function () {};
        this.onSectionsChange = options.onSectionsChange || function () {};
        this.listEl = root.querySelector('[data-section-list]');
        this.formEl = root.querySelector('[data-section-form]');
        this.emptyEl = root.querySelector('[data-section-empty]');
        this.drawerEl = root.querySelector('[data-plot-drawer]');
        this.drawerTitleEl = root.querySelector('[data-plot-drawer-title]');
        this.vertexPanelEl = root.querySelector('[data-vertex-panel]');
        this.vertexListEl = root.querySelector('[data-vertex-list]');
        this.vertexEmptyEl = root.querySelector('[data-vertex-empty]');
        this.drawerListEl = root.querySelector('[data-drawer-section-list]');
        this.drawerEmptyEl = root.querySelector('[data-drawer-section-empty]');
        this.draftPoints = [];
        this.drawerStorageKey = options.drawerStorageKey || 'interactive-map-plot-drawer-pos';
        this.bindEvents();
        this.initDrawerDrag();
        this.renderList();
        this.renderVertices([]);
    }

    function compareSort(a, b) {
        var as = parseInt(a.sort_order, 10) || 0;
        var bs = parseInt(b.sort_order, 10) || 0;
        if (as !== bs) {
            return as - bs;
        }
        return (a.id || 0) - (b.id || 0);
    }

    SectionPanel.prototype.bindEvents = function () {
        var self = this;

        this.root.querySelectorAll('[data-draw-mode]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                self.selected = null;
                self.toggleForm(false);
                self.draftPoints = [];
                self.renderVertices([]);
                self.renderList();
                self.openDrawer('Drawing plot');
                if (self.options.onDrawMode) {
                    self.options.onDrawMode(btn.getAttribute('data-draw-mode'));
                }
                self.root.querySelectorAll('[data-draw-mode]').forEach(function (b) {
                    b.classList.toggle('is-active', b === btn);
                });
            });
        });

        var openDrawerBtn = this.root.querySelector('[data-open-plot-drawer]');
        if (openDrawerBtn) {
            openDrawerBtn.addEventListener('click', function () {
                self.openDrawer(self.selected ? 'Edit plot' : 'Add plot');
                if (self.options.onOpenDrawer) {
                    self.options.onOpenDrawer();
                }
            });
        }

        var closeDrawerBtn = this.root.querySelector('[data-close-plot-drawer]');
        if (closeDrawerBtn) {
            closeDrawerBtn.addEventListener('click', function () {
                self.closeDrawer();
                if (self.options.onDrawCancel) {
                    self.options.onDrawCancel();
                }
            });
        }

        var cancelBtn = this.root.querySelector('[data-draw-cancel]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                self.draftPoints = [];
                self.renderVertices(self.selected ? null : []);
                if (self.selected) {
                    self.refreshVerticesFromSelected();
                }
                if (self.options.onDrawCancel) {
                    self.options.onDrawCancel();
                }
                self.root.querySelectorAll('[data-draw-mode]').forEach(function (b) {
                    b.classList.remove('is-active');
                });
            });
        }

        var undoBtn = this.root.querySelector('[data-draw-undo]');
        if (undoBtn) {
            undoBtn.addEventListener('click', function () {
                if (self.options.onDrawUndo) {
                    self.options.onDrawUndo();
                }
            });
        }

        var finishBtn = this.root.querySelector('[data-draw-finish]');
        if (finishBtn) {
            finishBtn.addEventListener('click', function () {
                self.saveOrCreateFromDraft();
            });
        }

        this.root.querySelectorAll('[data-section-save]').forEach(function (saveBtn) {
            saveBtn.addEventListener('click', function () {
                self.saveOrCreateFromDraft();
            });
        });

        ['fill_color', 'stroke_color', 'fill_opacity'].forEach(function (field) {
            var input = self.root.querySelector('[data-draw-style="' + field + '"]');
            if (!input) {
                return;
            }
            input.addEventListener('input', function () {
                self.emitDrawStyle();
            });
            input.addEventListener('change', function () {
                self.emitDrawStyle();
            });
        });

        var deleteBtn = this.root.querySelector('[data-section-delete]');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function () {
                self.deleteSelected();
            });
        }

        var clearFocusBtn = this.root.querySelector('[data-section-clear-focus]');
        if (clearFocusBtn) {
            clearFocusBtn.addEventListener('click', function () {
                self.clearFocus();
            });
        }

        if (this.vertexListEl) {
            this.vertexListEl.addEventListener('click', function (e) {
                var focusBtn = e.target.closest('[data-vertex-focus]');
                var deleteBtn = e.target.closest('[data-vertex-delete]');
                var row = e.target.closest('[data-vertex-index]');
                if (!row) {
                    return;
                }
                var index = parseInt(row.getAttribute('data-vertex-index'), 10);
                if (isNaN(index)) {
                    return;
                }
                if (deleteBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.deleteVertexAt(index);
                    return;
                }
                if (focusBtn || row) {
                    e.preventDefault();
                    if (self.draftPoints.length && !self.selected) {
                        if (self.options.onDraftVertexFocus) {
                            self.options.onDraftVertexFocus(index, self.draftPoints[index]);
                        }
                        return;
                    }
                    if (self.selected && self.options.onVertexFocus) {
                        self.options.onVertexFocus(self.selected.id, index);
                    }
                }
            });
        }

        var bindPlotListClicks = function (listEl) {
            if (!listEl) {
                return;
            }
            listEl.addEventListener('click', function (e) {
                var deleteBtn = e.target.closest('[data-layer-delete]');
                var visBtn = e.target.closest('[data-layer-vis]');
                var selectBtn = e.target.closest('[data-layer-select]');
                var row = e.target.closest('[data-section-id]');
                if (!row) {
                    return;
                }
                var id = row.getAttribute('data-section-id');
                if (deleteBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    self.selectSection(id);
                    self.deleteSelected();
                    return;
                }
                if (visBtn) {
                    e.preventDefault();
                    self.toggleVisibility(id);
                    return;
                }
                if (selectBtn || row) {
                    self.draftPoints = [];
                    if (self.options.onDrawCancel) {
                        self.options.onDrawCancel();
                    }
                    self.root.querySelectorAll('[data-draw-mode]').forEach(function (b) {
                        b.classList.remove('is-active');
                    });
                    self.selectSection(id);
                    var section = self.sections.find(function (s) { return String(s.id) === String(id); });
                    if (section && self.options.onSectionSelect) {
                        self.options.onSectionSelect(section);
                    }
                    self.openDrawer('Edit plot');
                }
            });
        };

        bindPlotListClicks(this.listEl);
        bindPlotListClicks(this.drawerListEl);
    };

    SectionPanel.prototype.openDrawer = function (title) {
        if (this.drawerEl) {
            this.drawerEl.hidden = false;
            this.restoreDrawerPosition();
        }
        if (this.drawerTitleEl) {
            this.drawerTitleEl.textContent = title || 'Plot tools';
        }
    };

    SectionPanel.prototype.closeDrawer = function () {
        if (this.drawerEl) {
            this.drawerEl.hidden = true;
        }
        this.root.querySelectorAll('[data-draw-mode]').forEach(function (b) {
            b.classList.remove('is-active');
        });
    };

    SectionPanel.prototype.initDrawerDrag = function () {
        var drawer = this.drawerEl;
        if (!drawer) {
            return;
        }

        drawer.style.position = 'fixed';
        drawer.style.zIndex = '10050';

        var handle = drawer.querySelector('[data-plot-drawer-drag]') || drawer;
        var self = this;
        var dragging = false;
        var startX = 0;
        var startY = 0;
        var originLeft = 0;
        var originTop = 0;

        var clamp = function (left, top) {
            var width = drawer.offsetWidth || 280;
            var height = Math.min(drawer.offsetHeight || 200, window.innerHeight - 16);
            var maxLeft = Math.max(8, window.innerWidth - width - 8);
            var maxTop = Math.max(8, window.innerHeight - Math.min(height, 80) - 8);
            return {
                left: Math.min(Math.max(8, left), maxLeft),
                top: Math.min(Math.max(8, top), maxTop),
            };
        };

        var applyPos = function (left, top) {
            var pos = clamp(left, top);
            drawer.style.left = pos.left + 'px';
            drawer.style.top = pos.top + 'px';
            drawer.style.right = 'auto';
            drawer.style.bottom = 'auto';
            drawer.classList.add('is-dragged');
            return pos;
        };

        var onMove = function (event) {
            if (!dragging) {
                return;
            }
            if (event.cancelable) {
                event.preventDefault();
            }
            var point = event.touches && event.touches[0] ? event.touches[0] : event;
            applyPos(originLeft + (point.clientX - startX), originTop + (point.clientY - startY));
        };

        var onUp = function () {
            if (!dragging) {
                return;
            }
            dragging = false;
            document.removeEventListener('mousemove', onMove);
            document.removeEventListener('mouseup', onUp);
            document.removeEventListener('touchmove', onMove);
            document.removeEventListener('touchend', onUp);
            self.saveDrawerPosition();
        };

        var onDown = function (event) {
            if (event.target.closest('button, input, select, textarea, a, label')) {
                return;
            }
            var point = event.touches && event.touches[0] ? event.touches[0] : event;
            dragging = true;
            startX = point.clientX;
            startY = point.clientY;
            var rect = drawer.getBoundingClientRect();
            originLeft = rect.left;
            originTop = rect.top;
            applyPos(originLeft, originTop);
            document.addEventListener('mousemove', onMove);
            document.addEventListener('mouseup', onUp);
            document.addEventListener('touchmove', onMove, { passive: false });
            document.addEventListener('touchend', onUp);
            event.preventDefault();
        };

        handle.addEventListener('mousedown', onDown);
        handle.addEventListener('touchstart', onDown, { passive: false });
        this.restoreDrawerPosition();
    };

    SectionPanel.prototype.saveDrawerPosition = function () {
        if (!this.drawerEl || !window.localStorage) {
            return;
        }
        try {
            var left = parseFloat(this.drawerEl.style.left);
            var top = parseFloat(this.drawerEl.style.top);
            if (isNaN(left) || isNaN(top)) {
                return;
            }
            window.localStorage.setItem(this.drawerStorageKey, JSON.stringify({ left: left, top: top }));
        } catch (e) {
            // ignore quota / private mode
        }
    };

    SectionPanel.prototype.restoreDrawerPosition = function () {
        if (!this.drawerEl) {
            return;
        }

        this.drawerEl.style.position = 'fixed';
        this.drawerEl.style.zIndex = '10050';

        if (!window.localStorage) {
            return;
        }
        try {
            var raw = window.localStorage.getItem(this.drawerStorageKey);
            if (!raw) {
                // Default: top-right of the browser window
                var width = this.drawerEl.offsetWidth || 280;
                this.drawerEl.style.left = Math.max(8, window.innerWidth - width - 16) + 'px';
                this.drawerEl.style.top = '72px';
                this.drawerEl.style.right = 'auto';
                this.drawerEl.style.bottom = 'auto';
                return;
            }
            var pos = JSON.parse(raw);
            if (!pos || isNaN(pos.left) || isNaN(pos.top)) {
                return;
            }
            var width = this.drawerEl.offsetWidth || 280;
            var maxLeft = Math.max(8, window.innerWidth - width - 8);
            var maxTop = Math.max(8, window.innerHeight - 80);
            this.drawerEl.style.left = Math.min(Math.max(8, pos.left), maxLeft) + 'px';
            this.drawerEl.style.top = Math.min(Math.max(8, pos.top), maxTop) + 'px';
            this.drawerEl.style.right = 'auto';
            this.drawerEl.style.bottom = 'auto';
            this.drawerEl.classList.add('is-dragged');
        } catch (e) {
            // ignore bad storage
        }
    };

    SectionPanel.prototype.emitDrawStyle = function () {
        this.onDrawStyleChange(this.getDrawStyle());
    };

    SectionPanel.prototype.getDrawStyle = function () {
        var fill = this.root.querySelector('[data-draw-style="fill_color"]');
        var stroke = this.root.querySelector('[data-draw-style="stroke_color"]');
        var opacity = this.root.querySelector('[data-draw-style="fill_opacity"]');
        var fillOpacity = parseFloat(opacity ? opacity.value : '0.45');
        if (isNaN(fillOpacity)) {
            fillOpacity = 0.45;
        }
        return {
            fillColor: fill ? fill.value : '#a9823d',
            strokeColor: stroke ? stroke.value : '#6c4815',
            fillOpacity: fillOpacity,
            strokeOpacity: 0.9,
            strokeWeight: 2,
        };
    };

    SectionPanel.prototype.renderList = function () {
        var self = this;
        this.sections = this.sections.slice().sort(compareSort);

        var targets = [
            { list: this.listEl, empty: this.emptyEl },
            { list: this.drawerListEl, empty: this.drawerEmptyEl },
        ];

        targets.forEach(function (target) {
            if (!target.list) {
                return;
            }
            target.list.innerHTML = '';
            if (!self.sections.length) {
                if (target.empty) {
                    target.empty.hidden = false;
                }
                return;
            }
            if (target.empty) {
                target.empty.hidden = true;
            }
            self.sections.forEach(function (section) {
                target.list.appendChild(self.buildPlotRow_(section));
            });
        });
    };

    SectionPanel.prototype.buildPlotRow_ = function (section) {
        var hidden = !!this.hiddenIds[section.id];
        var active = this.selected && String(this.selected.id) === String(section.id);
        var row = document.createElement('div');
        row.className = 'prototype-section-item' + (active ? ' is-active' : '') + (hidden ? ' is-hidden-layer' : '');
        row.setAttribute('data-section-id', section.id);
        row.innerHTML =
            '<button type="button" class="prototype-layer-icon" data-layer-vis title="' + (hidden ? 'Show' : 'Hide') + '">' + (hidden ? '○' : '●') + '</button>' +
            '<span class="prototype-section-swatch" style="background:' + (section.fill_color || '#a9823d') + '"></span>' +
            '<button type="button" class="prototype-layer-select" data-layer-select>' +
                '<span class="block text-sm font-medium truncate">' + escapeHtml(section.title) + '</span>' +
                '<span class="block text-[11px] opacity-70">' + escapeHtml(section.section_type) + (section.label ? ' · ' + escapeHtml(section.label) : '') + '</span>' +
            '</button>' +
            '<button type="button" class="prototype-layer-icon prototype-layer-icon--danger" data-layer-delete title="Delete plot">✕</button>';
        return row;
    };

    SectionPanel.prototype.selectSection = function (id) {
        this.selected = id == null
            ? null
            : (this.sections.find(function (s) { return String(s.id) === String(id); }) || null);
        if (this.selected) {
            this.draftPoints = [];
        }
        this.renderList();
        this.populateForm();
        if (this.selected) {
            this.openDrawer('Edit plot');
        }
    };

    SectionPanel.prototype.clearFocus = function () {
        this.selected = null;
        this.draftPoints = [];
        this.renderList();
        this.toggleForm(false);
        this.renderVertices([]);
        this.root.querySelectorAll('[data-draw-mode]').forEach(function (b) {
            b.classList.remove('is-active');
        });
        if (this.options.onClearFocus) {
            this.options.onClearFocus();
        }
        this.closeDrawer();
    };

    SectionPanel.prototype.populateForm = function () {
        if (!this.formEl) {
            return;
        }

        if (!this.selected) {
            this.toggleForm(false);
            if (this.draftPoints.length) {
                this.renderVertices(this.draftPoints);
            } else {
                this.renderVertices([]);
            }
            return;
        }

        this.toggleForm(true);
        this.formEl.querySelector('[data-section-field="title"]').value = this.selected.title || '';
        this.formEl.querySelector('[data-section-field="label"]').value = this.selected.label || '';
        this.formEl.querySelector('[data-section-field="fill_color"]').value = this.selected.fill_color || '#a9823d';
        this.formEl.querySelector('[data-section-field="stroke_color"]').value = this.selected.stroke_color || '#6c4815';
        this.formEl.querySelector('[data-section-field="fill_opacity"]').value = this.selected.fill_opacity ?? 0.45;
        this.formEl.querySelector('[data-section-field="status"]').value = this.selected.status || 'active';
        this.formEl.querySelector('[data-section-field="notes"]').value = this.selected.notes || '';
        var zoomField = this.formEl.querySelector('[data-section-field="show_label_from_zoom"]');
        if (zoomField) {
            zoomField.value = this.selected.show_label_from_zoom != null ? this.selected.show_label_from_zoom : '';
        }
        this.refreshVerticesFromSelected();
    };

    SectionPanel.prototype.setDraftPoints = function (points) {
        this.draftPoints = Array.isArray(points) ? points : [];
        if (!this.selected) {
            this.renderVertices(this.draftPoints);
        }
    };

    SectionPanel.prototype.refreshVerticesFromSelected = function () {
        if (this.draftPoints.length && !this.selected) {
            this.renderVertices(this.draftPoints);
            return;
        }
        if (!this.selected) {
            this.renderVertices([]);
            return;
        }
        var vertices = [];
        if (typeof this.options.getVertices === 'function') {
            vertices = this.options.getVertices(this.selected.id) || [];
        } else if (this.selected.geometry && Array.isArray(this.selected.geometry.paths)) {
            vertices = this.selected.geometry.paths.map(function (point, index) {
                return {
                    index: index,
                    lat: point.lat,
                    lng: point.lng,
                    label: 'Point ' + (index + 1),
                };
            });
        }
        this.renderVertices(vertices);
    };

    SectionPanel.prototype.renderVertices = function (vertices) {
        if (!this.vertexListEl) {
            return;
        }
        vertices = vertices || [];
        this.vertexListEl.innerHTML = '';

        if (!vertices.length) {
            var empty = document.createElement('p');
            empty.className = 'interactive-map-editor__hint';
            empty.style.margin = '0';
            empty.textContent = 'No points yet — place corners on the map.';
            this.vertexListEl.appendChild(empty);
            return;
        }

        var isDraft = !this.selected;
        var canDelete = isDraft
            ? vertices.length > 0
            : (this.selected && this.selected.section_type === 'polygon' && vertices.length > 3);
        var self = this;
        vertices.forEach(function (vertex) {
            var row = document.createElement('div');
            row.className = 'interactive-map-plot-drawer__vertex-item';
            row.setAttribute('data-vertex-index', String(vertex.index));
            row.innerHTML =
                '<button type="button" class="interactive-map-plot-drawer__vertex-focus" data-vertex-focus>' +
                    '<span class="interactive-map-plot-drawer__vertex-label">' + escapeHtml(vertex.label || ('Point ' + (vertex.index + 1))) + '</span>' +
                    '<span class="interactive-map-plot-drawer__vertex-coords">' +
                        Number(vertex.lat).toFixed(5) + ', ' + Number(vertex.lng).toFixed(5) +
                    '</span>' +
                '</button>' +
                (canDelete
                    ? '<button type="button" class="prototype-layer-icon prototype-layer-icon--danger" data-vertex-delete title="Delete point">✕</button>'
                    : '<span class="interactive-map-plot-drawer__vertex-lock" title="Keep at least 3 points">–</span>');
            self.vertexListEl.appendChild(row);
        });
    };

    SectionPanel.prototype.deleteVertexAt = function (index) {
        if (this.draftPoints.length && !this.selected) {
            if (typeof this.options.onDraftVertexDelete === 'function') {
                this.options.onDraftVertexDelete(index);
            }
            return;
        }
        if (!this.selected || typeof this.options.onVertexDelete !== 'function') {
            return;
        }
        var result = this.options.onVertexDelete(this.selected.id, index);
        if (!result || !result.ok) {
            this.options.onAlert && this.options.onAlert((result && result.message) || 'Could not delete point.', 'error');
            return;
        }
        if (result.geometry) {
            this.selected.geometry = result.geometry;
            var idx = this.sections.findIndex(function (s) { return String(s.id) === String(this.selected.id); }.bind(this));
            if (idx > -1) {
                this.sections[idx].geometry = result.geometry;
            }
        }
        this.refreshVerticesFromSelected();
        this.options.onAlert && this.options.onAlert('Point removed. Drag remaining corners on map if needed.', 'success');
    };

    SectionPanel.prototype.saveOrCreateFromDraft = function () {
        var draftCount = this.draftPoints.length;
        var drawingActive = typeof this.options.hasActiveDraft === 'function'
            ? this.options.hasActiveDraft()
            : draftCount > 0;

        if (drawingActive || draftCount >= 3) {
            if (draftCount > 0 && draftCount < 3) {
                this.options.onAlert && this.options.onAlert('Need at least 3 points before saving this plot.', 'error');
                return;
            }
            if (typeof this.options.onDrawFinish === 'function') {
                this.options.onDrawFinish();
                return;
            }
        }

        if (this.selected) {
            this.saveSelected();
            return;
        }

        this.options.onAlert && this.options.onAlert('Place 3+ points on the map, then click Save plot.', 'error');
    };

    SectionPanel.prototype.toggleForm = function (show) {
        if (this.formEl) {
            this.formEl.hidden = !show;
        }
    };

    SectionPanel.prototype.toggleVisibility = function (id) {
        this.hiddenIds[id] = !this.hiddenIds[id];
        this.renderList();
        if (this.options.onToggleVisibility) {
            this.options.onToggleVisibility(id, !!this.hiddenIds[id]);
        }
    };

    SectionPanel.prototype.handleDrawComplete = function (payload) {
        var nextIndex = this.sections.length + 1;
        var title = 'Plot ' + nextIndex;
        var style = this.getDrawStyle();
        var body = Object.assign({}, payload, {
            title: title,
            label: title,
            fill_color: style.fillColor,
            stroke_color: style.strokeColor,
            fill_opacity: style.fillOpacity,
            stroke_opacity: style.strokeOpacity,
            stroke_weight: style.strokeWeight,
            status: 'active',
        });

        this.createSection(body);
    };

    SectionPanel.prototype.createSection = function (body) {
        var self = this;
        if (typeof body.fill_opacity === 'number' && isNaN(body.fill_opacity)) {
            body.fill_opacity = 0.45;
        }
        if (typeof body.stroke_weight !== 'number' || isNaN(body.stroke_weight)) {
            body.stroke_weight = 2;
        }

        fetch(this.routes.store, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body),
        })
            .then(parseJsonResponse)
            .then(function (data) {
                if (!data.section) {
                    throw new Error('Plot saved but response was incomplete.');
                }
                self.sections.push(data.section);
                self.selectSection(data.section.id);
                self.renderList();
                self.openDrawer('Edit plot');
                self.onSectionsChange(self.sections, data.section, 'create');
                self.options.onAlert && self.options.onAlert('Plot saved. Points are listed below — drag on map or delete from the list.', 'success');
                if (self.options.onSectionSelect) {
                    self.options.onSectionSelect(data.section);
                }
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message || 'Could not save plot.', 'error');
            });
    };

    SectionPanel.prototype.saveSelected = function () {
        if (!this.selected) {
            this.options.onAlert && this.options.onAlert('Select a plot first.', 'error');
            return;
        }

        var zoomEl = this.formEl.querySelector('[data-section-field="show_label_from_zoom"]');
        var zoomVal = zoomEl && zoomEl.value !== '' ? parseInt(zoomEl.value, 10) : null;
        var fillOpacity = parseFloat(this.formEl.querySelector('[data-section-field="fill_opacity"]').value);
        if (isNaN(fillOpacity)) {
            fillOpacity = 0.45;
        }

        var payload = {
            title: this.formEl.querySelector('[data-section-field="title"]').value || 'Untitled Plot',
            label: this.formEl.querySelector('[data-section-field="label"]').value,
            fill_color: this.formEl.querySelector('[data-section-field="fill_color"]').value,
            stroke_color: this.formEl.querySelector('[data-section-field="stroke_color"]').value,
            fill_opacity: fillOpacity,
            status: this.formEl.querySelector('[data-section-field="status"]').value,
            notes: this.formEl.querySelector('[data-section-field="notes"]').value,
            show_label_from_zoom: isNaN(zoomVal) ? null : zoomVal,
        };

        if (typeof this.options.getLiveGeometry === 'function') {
            var liveGeometry = this.options.getLiveGeometry(this.selected.id);
            if (liveGeometry) {
                payload.geometry = liveGeometry;
                payload.section_type = this.selected.section_type;
            }
        }

        var self = this;
        this.patchSection(this.selected.id, payload)
            .then(function (section) {
                var index = self.sections.findIndex(function (s) { return String(s.id) === String(section.id); });
                if (index > -1) {
                    self.sections[index] = section;
                }
                self.selected = section;
                self.renderList();
                self.refreshVerticesFromSelected();
                self.onSectionsChange(self.sections, section, 'update');
                self.options.onAlert && self.options.onAlert('Plot saved.', 'success');
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message || 'Could not save plot.', 'error');
            });
    };

    SectionPanel.prototype.saveGeometry = function (id, geometry) {
        var self = this;
        return this.patchSection(id, { geometry: geometry })
            .then(function (section) {
                var index = self.sections.findIndex(function (s) { return String(s.id) === String(section.id); });
                if (index > -1) {
                    self.sections[index] = section;
                }
                if (self.selected && String(self.selected.id) === String(section.id)) {
                    self.selected = section;
                    self.refreshVerticesFromSelected();
                }
                self.onSectionsChange(self.sections, section, 'geometry');
                return section;
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message || 'Could not save cutting.', 'error');
            });
    };

    SectionPanel.prototype.patchSection = function (id, payload) {
        var url = (this.routes.updatePost || this.routes.update || '').replace('__SECTION__', id);
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        }).then(parseJsonResponse).then(function (data) {
            if (!data.section) {
                throw new Error('Save response incomplete.');
            }
            return data.section;
        });
    };

    SectionPanel.prototype.deleteSelected = function () {
        if (!this.selected) {
            this.options.onAlert && this.options.onAlert('Select a plot first, then delete.', 'error');
            return;
        }
        if (!confirm('Delete this plot?')) {
            return;
        }

        var self = this;
        var id = this.selected.id;
        var deleteUrl = (this.routes.destroyPost || this.routes.destroy || '')
            .replace('__SECTION__', id);

        if (!deleteUrl) {
            this.options.onAlert && this.options.onAlert('Delete URL missing.', 'error');
            return;
        }

        // Remove from map immediately so UI always responds.
        self.sections = self.sections.filter(function (s) { return String(s.id) !== String(id); });
        delete self.hiddenIds[id];
        delete self.hiddenIds[String(id)];
        self.selected = null;
        self.renderList();
        self.toggleForm(false);
        self.onSectionsChange(self.sections, { id: id }, 'delete');

        fetch(deleteUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({}),
        })
            .then(function (res) {
                return res.text().then(function (text) {
                    var data = {};
                    if (text) {
                        try {
                            data = JSON.parse(text);
                        } catch (e) {
                            data = { message: text.slice(0, 160) || ('HTTP ' + res.status) };
                        }
                    }
                    if (!res.ok) {
                        throw new Error(extractError(data) || ('Delete failed (' + res.status + ')'));
                    }
                    return data;
                });
            })
            .then(function (data) {
                self.options.onAlert && self.options.onAlert((data && data.message) || 'Plot deleted.', 'success');
                self.openDrawer('Add plot');
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message || 'Could not delete plot on server.', 'error');
            });
    };

    function parseJsonResponse(res) {
        return res.text().then(function (text) {
            var data = {};
            if (text) {
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    data = { message: text.slice(0, 180) || ('HTTP ' + res.status) };
                }
            }
            if (!res.ok) {
                throw new Error(extractError(data) || ('Request failed (' + res.status + ')'));
            }
            return data;
        });
    }

    function extractError(data) {
        if (data && data.message) {
            return data.message;
        }
        if (data && data.errors) {
            var first = Object.keys(data.errors)[0];
            if (first && data.errors[first][0]) {
                return data.errors[first][0];
            }
        }
        return 'Request failed.';
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    IM.SectionPanel = SectionPanel;
})(window);
