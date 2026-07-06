(function (window) {
    'use strict';

    function ToolbarManager(root) {
        this.root = root;
        this.statusEl = root.querySelector('[data-status]');
        this.toastEl = root.querySelector('[data-toast]');
        this.fields = {};
        var self = this;

        root.querySelectorAll('[data-field]').forEach(function (input) {
            var key = input.getAttribute('data-field');
            self.fields[key] = input;
            input.addEventListener('input', function () {
                if (self._silentWrite) {
                    return;
                }
                self.onFieldChange && self.onFieldChange(key, self.readField(key));
            });
            input.addEventListener('change', function () {
                if (self._silentWrite) {
                    return;
                }
                self.onFieldChange && self.onFieldChange(key, self.readField(key));
            });
        });
    }

    ToolbarManager.prototype.readField = function (key) {
        var input = this.fields[key];
        if (!input) {
            return null;
        }
        if (input.type === 'checkbox') {
            return input.checked;
        }
        if (input.type === 'number') {
            var num = parseFloat(input.value);
            return isNaN(num) ? null : num;
        }
        return input.value;
    };

    ToolbarManager.prototype.readAll = function () {
        var data = {};
        for (var key in this.fields) {
            if (Object.prototype.hasOwnProperty.call(this.fields, key)) {
                data[key] = this.readField(key);
            }
        }
        return data;
    };

    ToolbarManager.prototype.write = function (data, silent) {
        this._silentWrite = !!silent;
        for (var key in data) {
            if (!Object.prototype.hasOwnProperty.call(data, key)) {
                continue;
            }
            var input = this.fields[key];
            if (!input) {
                continue;
            }
            if (input.type === 'checkbox') {
                input.checked = !!data[key];
            } else if (data[key] !== null && data[key] !== undefined) {
                input.value = data[key];
            }
        }
        this._silentWrite = false;
    };

    ToolbarManager.prototype.setStatus = function (message) {
        if (this.statusEl) {
            this.statusEl.textContent = message || '';
        }
    };

    ToolbarManager.prototype.showToast = function (message, type) {
        if (!this.toastEl) {
            return;
        }
        this.toastEl.textContent = message;
        this.toastEl.classList.remove('hidden', 'is-error', 'is-success');
        if (type === 'error') {
            this.toastEl.classList.add('is-error');
        } else {
            this.toastEl.classList.add('is-success');
        }
        var toast = this.toastEl;
        clearTimeout(this._toastTimer);
        this._toastTimer = setTimeout(function () {
            toast.classList.add('hidden');
        }, 3200);
    };

    window.InteractiveMap = window.InteractiveMap || {};
    window.InteractiveMap.ToolbarManager = ToolbarManager;
})(window);
