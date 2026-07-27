(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    function SectionPanel(root, options) {
        this.root = root;
        this.options = options || {};
        this.csrf = options.csrf || '';
        this.routes = options.routes || {};
        this.sections = options.sections || [];
        this.selected = null;
        this.onDrawStyleChange = options.onDrawStyleChange || function () {};
        this.onSectionsChange = options.onSectionsChange || function () {};
        this.listEl = root.querySelector('[data-section-list]');
        this.formEl = root.querySelector('[data-section-form]');
        this.emptyEl = root.querySelector('[data-section-empty]');
        this.bindEvents();
        this.renderList();
    }

    SectionPanel.prototype.bindEvents = function () {
        var self = this;

        this.root.querySelectorAll('[data-draw-mode]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                self.options.onDrawMode && self.options.onDrawMode(btn.getAttribute('data-draw-mode'));
                self.root.querySelectorAll('[data-draw-mode]').forEach(function (b) {
                    b.classList.toggle('is-active', b === btn);
                });
            });
        });

        var cancelBtn = this.root.querySelector('[data-draw-cancel]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function () {
                self.options.onDrawCancel && self.options.onDrawCancel();
                self.root.querySelectorAll('[data-draw-mode]').forEach(function (b) {
                    b.classList.remove('is-active');
                });
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
    };

    SectionPanel.prototype.emitDrawStyle = function () {
        this.onDrawStyleChange(this.getDrawStyle());
    };

    SectionPanel.prototype.getDrawStyle = function () {
        return {
            fillColor: this.root.querySelector('[data-draw-style="fill_color"]')?.value || '#a9823d',
            strokeColor: this.root.querySelector('[data-draw-style="stroke_color"]')?.value || '#6c4815',
            fillOpacity: parseFloat(this.root.querySelector('[data-draw-style="fill_opacity"]')?.value || '0.45'),
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

        if (!this.sections.length) {
            if (this.emptyEl) {
                this.emptyEl.hidden = false;
            }
            this.toggleForm(false);
            return;
        }

        if (this.emptyEl) {
            this.emptyEl.hidden = true;
        }

        this.sections.forEach(function (section) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'prototype-section-item' + (self.selected && String(self.selected.id) === String(section.id) ? ' is-active' : '');
            btn.innerHTML = '<span class="prototype-section-swatch" style="background:' + (section.fill_color || '#a9823d') + '"></span>' +
                '<span class="flex-1 text-left"><span class="block text-sm font-medium truncate">' + escapeHtml(section.title) + '</span>' +
                '<span class="block text-[11px] text-slate-500">' + escapeHtml(section.section_type) + (section.label ? ' · ' + escapeHtml(section.label) : '') + '</span></span>';
            btn.addEventListener('click', function () {
                self.selectSection(section.id);
                self.options.onSectionSelect && self.options.onSectionSelect(section);
            });
            self.listEl.appendChild(btn);
        });
    };

    SectionPanel.prototype.selectSection = function (id) {
        this.selected = this.sections.find(function (s) { return String(s.id) === String(id); }) || null;
        this.renderList();
        this.populateForm();
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
    };

    SectionPanel.prototype.toggleForm = function (show) {
        if (this.formEl) {
            this.formEl.hidden = !show;
        }
    };

    SectionPanel.prototype.handleDrawComplete = function (payload) {
        var title = prompt('Section / slot name:', 'Plot ' + (this.sections.length + 1));
        if (!title || !title.trim()) {
            return;
        }

        var label = prompt('Label on map (optional):', title.trim()) || title.trim();
        var style = this.getDrawStyle();

        var body = Object.assign({}, payload, {
            title: title.trim(),
            label: label,
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
                self.onSectionsChange(self.sections, data.section, 'create');
                self.options.onAlert && self.options.onAlert(data.message, 'success');
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message, 'error');
            });
    };

    SectionPanel.prototype.saveSelected = function () {
        if (!this.selected) {
            return;
        }

        var self = this;
        var payload = {
            title: this.formEl.querySelector('[data-section-field="title"]').value,
            label: this.formEl.querySelector('[data-section-field="label"]').value,
            fill_color: this.formEl.querySelector('[data-section-field="fill_color"]').value,
            stroke_color: this.formEl.querySelector('[data-section-field="stroke_color"]').value,
            fill_opacity: parseFloat(this.formEl.querySelector('[data-section-field="fill_opacity"]').value),
            status: this.formEl.querySelector('[data-section-field="status"]').value,
            notes: this.formEl.querySelector('[data-section-field="notes"]').value,
        };

        fetch(this.routes.update.replace('__SECTION__', this.selected.id), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
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
                var index = self.sections.findIndex(function (s) { return String(s.id) === String(data.section.id); });
                if (index > -1) {
                    self.sections[index] = data.section;
                }
                self.selected = data.section;
                self.renderList();
                self.onSectionsChange(self.sections, data.section, 'update');
                self.options.onAlert && self.options.onAlert(data.message, 'success');
            })
            .catch(function (err) {
                self.options.onAlert && self.options.onAlert(err.message, 'error');
            });
    };

    SectionPanel.prototype.deleteSelected = function () {
        if (!this.selected || !confirm('Delete this section/slot?')) {
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
                self.selected = null;
                self.renderList();
                self.toggleForm(false);
                self.onSectionsChange(self.sections, { id: id }, 'delete');
                self.options.onAlert && self.options.onAlert(data.message, 'success');
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

    PM.SectionPanel = SectionPanel;
})(window);
