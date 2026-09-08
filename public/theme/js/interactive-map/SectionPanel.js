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
        this.bindEvents();
        this.renderList();
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
                if (self.options.onDrawFinish) {
                    self.options.onDrawFinish();
                }
            });
        }

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

        var saveBtn = this.root.querySelector('[data-section-save]');
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                self.saveSelected();
            });
        }

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

        if (this.listEl) {
            this.listEl.addEventListener('click', function (e) {
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
                    self.selectSection(id);
                    var section = self.sections.find(function (s) { return String(s.id) === String(id); });
                    if (section && self.options.onSectionSelect) {
                        self.options.onSectionSelect(section);
                    }
                }
            });
        }
    };

    SectionPanel.prototype.openDrawer = function (title) {
        if (this.drawerEl) {
            this.drawerEl.hidden = false;
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

    SectionPanel.prototype.emitDrawStyle = function () {
        this.onDrawStyleChange(this.getDrawStyle());
    };

    SectionPanel.prototype.getDrawStyle = function () {
        var fill = this.root.querySelector('[data-draw-style="fill_color"]');
        var stroke = this.root.querySelector('[data-draw-style="stroke_color"]');
        var opacity = this.root.querySelector('[data-draw-style="fill_opacity"]');
        return {
            fillColor: fill ? fill.value : '#a9823d',
            strokeColor: stroke ? stroke.value : '#6c4815',
            fillOpacity: parseFloat(opacity ? opacity.value : '0.45'),
            strokeOpacity: 0.9,
            strokeWeight: 2,
        };
    };

    SectionPanel.prototype.renderList = function () {
        if (!this.listEl) {
            return;
        }

        var self = this;
        this.listEl.innerHTML = '';
        this.sections = this.sections.slice().sort(compareSort);

        if (!this.sections.length) {
            if (this.emptyEl) {
                this.emptyEl.hidden = false;
            }
            return;
        }

        if (this.emptyEl) {
            this.emptyEl.hidden = true;
        }

        this.sections.forEach(function (section) {
            var hidden = !!self.hiddenIds[section.id];
            var active = self.selected && String(self.selected.id) === String(section.id);
            var row = document.createElement('div');
            row.className = 'prototype-section-item' + (active ? ' is-active' : '') + (hidden ? ' is-hidden-layer' : '');
            row.setAttribute('data-section-id', section.id);
            row.innerHTML =
                '<button type="button" class="prototype-layer-icon" data-layer-vis title="' + (hidden ? 'Show' : 'Hide') + '">' + (hidden ? '○' : '●') + '</button>' +
                '<span class="prototype-section-swatch" style="background:' + (section.fill_color || '#a9823d') + '"></span>' +
                '<button type="button" class="prototype-layer-select" data-layer-select>' +
                    '<span class="block text-sm font-medium truncate">' + escapeHtml(section.title) + '</span>' +
                    '<span class="block text-[11px] text-slate-500">' + escapeHtml(section.section_type) + (section.label ? ' · ' + escapeHtml(section.label) : '') + '</span>' +
                '</button>' +
                '<button type="button" class="prototype-layer-icon prototype-layer-icon--danger" data-layer-delete title="Delete plot">✕</button>';
            self.listEl.appendChild(row);
        });
    };

    SectionPanel.prototype.selectSection = function (id) {
        this.selected = id == null
            ? null
            : (this.sections.find(function (s) { return String(s.id) === String(id); }) || null);
        this.renderList();
        this.populateForm();
        if (this.selected) {
            this.openDrawer('Edit plot');
        }
    };

    SectionPanel.prototype.clearFocus = function () {
        this.selected = null;
        this.renderList();
        this.toggleForm(false);
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
        fetch(this.routes.store, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(body),
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) {
                        throw new Error(extractError(data));
                    }
                    return data;
                });
            })
            .then(function (data) {
                self.sections.push(data.section);
                self.selectSection(data.section.id);
                self.renderList();
                self.openDrawer('Edit plot');
                self.onSectionsChange(self.sections, data.section, 'create');
                self.options.onAlert && self.options.onAlert('Plot added — fix shape or rename in the panel.', 'success');
                if (self.options.onSectionSelect) {
                    self.options.onSectionSelect(data.section);
                }
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message, 'error');
            });
    };

    SectionPanel.prototype.saveSelected = function () {
        if (!this.selected) {
            return;
        }

        var zoomEl = this.formEl.querySelector('[data-section-field="show_label_from_zoom"]');
        var zoomVal = zoomEl && zoomEl.value !== '' ? parseInt(zoomEl.value, 10) : null;

        var payload = {
            title: this.formEl.querySelector('[data-section-field="title"]').value,
            label: this.formEl.querySelector('[data-section-field="label"]').value,
            fill_color: this.formEl.querySelector('[data-section-field="fill_color"]').value,
            stroke_color: this.formEl.querySelector('[data-section-field="stroke_color"]').value,
            fill_opacity: parseFloat(this.formEl.querySelector('[data-section-field="fill_opacity"]').value),
            status: this.formEl.querySelector('[data-section-field="status"]').value,
            notes: this.formEl.querySelector('[data-section-field="notes"]').value,
            show_label_from_zoom: isNaN(zoomVal) ? null : zoomVal,
        };

        var self = this;
        this.patchSection(this.selected.id, payload)
            .then(function (section) {
                var index = self.sections.findIndex(function (s) { return String(s.id) === String(section.id); });
                if (index > -1) {
                    self.sections[index] = section;
                }
                self.selected = section;
                self.renderList();
                self.onSectionsChange(self.sections, section, 'update');
                self.options.onAlert && self.options.onAlert('Plot saved.', 'success');
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message, 'error');
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
                }
                self.onSectionsChange(self.sections, section, 'geometry');
                return section;
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message || 'Could not save cutting.', 'error');
            });
    };

    SectionPanel.prototype.patchSection = function (id, payload) {
        return fetch(this.routes.update.replace('__SECTION__', id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        }).then(function (res) {
            return res.json().then(function (data) {
                if (!res.ok) {
                    throw new Error(extractError(data));
                }
                return data.section;
            });
        });
    };

    SectionPanel.prototype.deleteSelected = function () {
        if (!this.selected || !confirm('Delete this plot?')) {
            return;
        }

        var self = this;
        var id = this.selected.id;

        fetch(this.routes.destroy.replace('__SECTION__', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
        })
            .then(function (res) {
                return res.json().then(function (data) {
                    if (!res.ok) {
                        throw new Error(extractError(data));
                    }
                    return data;
                });
            })
            .then(function (data) {
                self.sections = self.sections.filter(function (s) { return String(s.id) !== String(id); });
                delete self.hiddenIds[id];
                self.selected = null;
                self.renderList();
                self.toggleForm(false);
                self.onSectionsChange(self.sections, { id: id }, 'delete');
                self.options.onAlert && self.options.onAlert(data.message || 'Plot deleted.', 'success');
                self.openDrawer('Add plot');
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message, 'error');
            });
    };

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
