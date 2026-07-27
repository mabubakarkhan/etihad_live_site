(function (window) {
    'use strict';

    var PM = window.PrototypeMap = window.PrototypeMap || {};

    function OverlaySettings(root, options) {
        this.root = root;
        this.updateUrl = options.updateUrl;
        this.csrf = options.csrf;
        this.onLiveUpdate = options.onLiveUpdate || function () {};
        this.onSuccess = options.onSuccess || function () {};
        this.onError = options.onError || function () {};
        this.originalData = JSON.parse(JSON.stringify(options.data || {}));
        this.currentData = JSON.parse(JSON.stringify(options.data || {}));
        this.saveBtn = root.querySelector('[data-save-settings]');
        this.resetBtn = root.querySelector('[data-reset-settings]');
        this.opacityLabel = root.querySelector('[data-opacity-label]');
        this.debounceTimer = null;
        this.bindEvents();
    }

    OverlaySettings.prototype.bindEvents = function () {
        var self = this;

        this.root.querySelectorAll('[data-setting]').forEach(function (input) {
            input.addEventListener('input', function () {
                self.handleInput(input);
            });
            input.addEventListener('change', function () {
                self.handleInput(input);
            });
        });

        if (this.saveBtn) {
            this.saveBtn.addEventListener('click', function () {
                self.save();
            });
        }

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', function () {
                self.reset();
            });
        }
    };

    OverlaySettings.prototype.handleInput = function (input) {
        var key = input.getAttribute('data-setting');
        var value = input.value;

        if (['north', 'south', 'east', 'west', 'overlay_opacity', 'overlay_rotation'].indexOf(key) > -1) {
            value = value === '' ? null : parseFloat(value);
        } else if (['default_zoom', 'min_zoom', 'max_zoom', 'show_overlay_from_zoom'].indexOf(key) > -1) {
            value = value === '' ? null : parseInt(value, 10);
        }

        this.currentData[key] = value;

        if (key === 'overlay_opacity' && this.opacityLabel) {
            this.opacityLabel.textContent = Math.round(parseFloat(value || 0) * 100) + '%';
        }

        this.scheduleLiveUpdate();
    };

    OverlaySettings.prototype.scheduleLiveUpdate = function () {
        var self = this;
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(function () {
            self.onLiveUpdate(self.getLiveConfig());
        }, 120);
    };

    OverlaySettings.prototype.getLiveConfig = function () {
        return {
            bounds: this.getBoundsFromData(this.currentData),
            overlay_url: this.currentData.overlay_url,
            overlay_opacity: this.currentData.overlay_opacity,
            overlay_rotation: this.currentData.overlay_rotation,
            show_overlay_from_zoom: this.currentData.show_overlay_from_zoom,
            status: this.currentData.status,
            default_zoom: this.currentData.default_zoom,
            min_zoom: this.currentData.min_zoom,
            max_zoom: this.currentData.max_zoom,
        };
    };

    OverlaySettings.prototype.getBoundsFromData = function (data) {
        if ([data.north, data.south, data.east, data.west].some(function (v) { return v === null || v === undefined || isNaN(v); })) {
            return null;
        }
        return {
            north: parseFloat(data.north),
            south: parseFloat(data.south),
            east: parseFloat(data.east),
            west: parseFloat(data.west),
        };
    };

    OverlaySettings.prototype.save = function () {
        var self = this;
        var payload = Object.assign({}, this.currentData);

        fetch(this.updateUrl, {
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
                        throw new Error(self.extractError(data));
                    }
                    return data;
                });
            })
            .then(function (data) {
                self.originalData = JSON.parse(JSON.stringify(data.overlay));
                self.currentData = JSON.parse(JSON.stringify(data.overlay));
                self.onSuccess(data);
            })
            .catch(function (err) {
                self.onError(err.message || 'Save failed.');
            });
    };

    OverlaySettings.prototype.reset = function () {
        this.currentData = JSON.parse(JSON.stringify(this.originalData));
        this.syncInputs();
        this.onLiveUpdate(this.getLiveConfig());
    };

    OverlaySettings.prototype.syncInputs = function () {
        var self = this;
        this.root.querySelectorAll('[data-setting]').forEach(function (input) {
            var key = input.getAttribute('data-setting');
            if (self.currentData[key] !== undefined && self.currentData[key] !== null) {
                input.value = self.currentData[key];
            } else {
                input.value = '';
            }
        });
        if (this.opacityLabel) {
            this.opacityLabel.textContent = Math.round((parseFloat(this.currentData.overlay_opacity) || 0) * 100) + '%';
        }
    };

    OverlaySettings.prototype.updateData = function (data) {
        this.originalData = JSON.parse(JSON.stringify(data));
        this.currentData = JSON.parse(JSON.stringify(data));
        this.syncInputs();
    };

    OverlaySettings.prototype.extractError = function (data) {
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
    };

    PM.OverlaySettings = OverlaySettings;
})(window);
