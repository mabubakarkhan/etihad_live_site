(function (window) {
    'use strict';

    var IM = window.InteractiveMap = window.InteractiveMap || {};
    var loaders = {};

    function loadGoogleMaps(apiKey, callbackName, options) {
        options = options || {};
        var libraries = options.libraries || 'marker';
        var requirePlaces = options.requirePlaces === true;

        if (window.google && window.google.maps) {
            if (!requirePlaces || window.google.maps.places) {
                return Promise.resolve(window.google.maps);
            }
        }

        if (!apiKey) {
            return Promise.reject(new Error('Google Maps API key is not configured.'));
        }

        var promiseKey = apiKey + ':' + callbackName + ':' + libraries;
        if (loaders[promiseKey]) {
            return loaders[promiseKey];
        }

        loaders[promiseKey] = new Promise(function (resolve, reject) {
            window[callbackName] = function () {
                if (window.google && window.google.maps) {
                    if (!requirePlaces || window.google.maps.places) {
                        resolve(window.google.maps);
                        return;
                    }
                }
                reject(new Error('Google Maps failed to load.'));
            };

            var script = document.createElement('script');
            script.async = true;
            script.defer = true;
            script.onerror = function () {
                reject(new Error('Failed to load Google Maps JavaScript API.'));
            };
            var scriptUrl = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(apiKey) + '&loading=async&callback=' + encodeURIComponent(callbackName);
            if (libraries) {
                scriptUrl += '&libraries=' + encodeURIComponent(libraries);
            }
            script.src = scriptUrl;
            document.head.appendChild(script);
        });

        return loaders[promiseKey];
    }

    function boundsFromData(data) {
        if (!data) {
            return null;
        }

        var north = parseFloat(data.north);
        var south = parseFloat(data.south);
        var east = parseFloat(data.east);
        var west = parseFloat(data.west);

        if ([north, south, east, west].some(function (value) { return isNaN(value); })) {
            return null;
        }

        return { north: north, south: south, east: east, west: west };
    }

    function centerFromBounds(bounds) {
        if (!bounds) {
            return { lat: 31.5204, lng: 74.3587 };
        }
        return {
            lat: (bounds.north + bounds.south) / 2,
            lng: (bounds.east + bounds.west) / 2,
        };
    }

    function InteractiveMapEditor(root) {
        this.root = root;
        this.apiBase = root.getAttribute('data-api-base');
        this.csrf = root.getAttribute('data-csrf');
        this.apiKey = root.getAttribute('data-google-maps-key') || '';
        this.mapId = root.getAttribute('data-google-maps-map-id') || '';
        this.data = {};
        this.toolbar = new IM.ToolbarManager(root);
        this.mapManager = null;
        this.overlayManager = null;
        this.previewImg = root.querySelector('[data-overlay-preview-img]');
        this.previewEmpty = root.querySelector('[data-overlay-empty]');
        this.deleteBtn = root.querySelector('[data-overlay-delete]');
        this.fileInput = root.querySelector('[data-overlay-input]');
        this.saveBtn = root.querySelector('[data-save-settings]');
        this.searchInput = root.querySelector('[data-map-search]');
        this.searchManager = null;
        this.callbackName = 'initInteractiveMapEditor_' + root.id.replace(/[^a-zA-Z0-9_]/g, '_');

        try {
            this.data = JSON.parse(root.getAttribute('data-initial') || '{}');
        } catch (e) {
            this.data = {};
        }

        this.bindEvents();
        this.toolbar.write(this.data);
        this.updatePreview(this.data.overlay_url);
        this.scheduleMapInit();
    }

    InteractiveMapEditor.prototype.isVisible = function () {
        if (!this.root) {
            return false;
        }

        if (this.root.getClientRects && this.root.getClientRects().length > 0) {
            return true;
        }

        return !!this.root.offsetParent;
    };

    InteractiveMapEditor.prototype.scheduleMapInit = function () {
        var self = this;

        if (this._mapInitStarted) {
            return;
        }

        if (this.isVisible()) {
            this._mapInitStarted = true;
            this.initMap();
            return;
        }

        if (!window.IntersectionObserver) {
            this._mapInitStarted = true;
            this.initMap();
            return;
        }

        this._visibilityObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) {
                    return;
                }

                if (!self._mapInitStarted) {
                    self._mapInitStarted = true;
                    self.initMap();
                    return;
                }

                self.refreshMapLayout();
            });
        }, { threshold: 0.05 });

        this._visibilityObserver.observe(this.root);
    };

    InteractiveMapEditor.prototype.refreshMapLayout = function () {
        var map = this.mapManager && this.mapManager.getMap();
        var g = window.google && window.google.maps;
        if (!map || !g) {
            return;
        }

        g.event.trigger(map, 'resize');

        var bounds = boundsFromData(this.data);
        if (bounds) {
            this.mapManager.fitBounds(bounds);
        }

        if (this.searchManager && this.searchManager.binding && typeof this.searchManager.binding.syncBounds === 'function') {
            this.searchManager.binding.syncBounds();
        }
    };

    InteractiveMapEditor.prototype.bindEvents = function () {
        var self = this;

        this.toolbar.onFieldChange = function () {
            self.applyLivePreview();
        };

        if (this.saveBtn) {
            this.saveBtn.addEventListener('click', function () {
                self.saveSettings();
            });
        }

        if (this.fileInput) {
            this.fileInput.addEventListener('change', function () {
                if (self.fileInput.files && self.fileInput.files[0]) {
                    self.uploadOverlay(self.fileInput.files[0]);
                }
            });
        }

        if (this.deleteBtn) {
            this.deleteBtn.addEventListener('click', function () {
                if (confirm('Remove the overlay image?')) {
                    self.deleteOverlay();
                }
            });
        }
    };

    InteractiveMapEditor.prototype.initMap = function () {
        var self = this;
        var bounds = boundsFromData(this.data);

        this.toolbar.setStatus('Loading Google Maps…');

        loadGoogleMaps(this.apiKey, this.callbackName)
            .then(function () {
                self.mapManager = new IM.MapManager({
                    container: self.root.querySelector('[data-map-canvas]'),
                    useRasterMap: true,
                    minZoom: self.data.min_zoom || 10,
                    maxZoom: self.data.max_zoom || 20,
                    defaultZoom: self.data.default_zoom || 15,
                    center: centerFromBounds(bounds),
                });
                self.mapManager.init();

                self.overlayManager = new IM.OverlayManager({
                    map: self.mapManager.getMap(),
                    opacity: self.data.overlay_opacity,
                    draggable: true,
                    onBoundsDrag: function (bounds) {
                        self.onOverlayBoundsDragging(bounds);
                    },
                    onBoundsChange: function (bounds) {
                        self.onOverlayBoundsDragged(bounds);
                    },
                });

                self.applyLivePreview();

                if (bounds) {
                    self.mapManager.fitBounds(bounds);
                }

                var map = self.mapManager.getMap();
                var g = window.google && window.google.maps;
                if (map && g && g.event) {
                    g.event.addListenerOnce(map, 'idle', function () {
                        self.initSearch();
                    });
                } else {
                    self.initSearch();
                }

                self.toolbar.setStatus('Live preview ready — drag overlay to position, then save');
            })
            .catch(function (err) {
                self.toolbar.setStatus(err.message || 'Map failed to load');
                self.toolbar.showToast(err.message || 'Map failed to load', 'error');
            });
    };

    InteractiveMapEditor.prototype.initSearch = function () {
        if (!this.searchInput || !this.mapManager || !IM.SearchManager) {
            return;
        }

        if (this.searchManager) {
            return;
        }

        var self = this;
        this.searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });

        this.searchManager = new IM.SearchManager({
            map: this.mapManager.getMap(),
            input: this.searchInput,
            placesProxy: {
                autocompleteUrl: this.apiBase + '/places/autocomplete',
                placeDetailsUrl: this.apiBase + '/places/',
                csrf: this.csrf,
            },
            onPlaceSelected: function (place) {
                var label = place.name || place.address || 'Location found';
                self.toolbar.setStatus('Centered on: ' + label);
            },
        });

        if (!this.searchManager.init()) {
            this.toolbar.setStatus('Map ready (shared Places search unavailable)');
        }
    };

    InteractiveMapEditor.prototype.onOverlayBoundsDragging = function (bounds) {
        if (!bounds || !this.mapManager) {
            return;
        }

        this.mapManager.drawBoundsRectangle(bounds);
        this.toolbar.setStatus('Dragging overlay… release to place');
    };

    function roundCoord(value) {
        return Math.round(parseFloat(value) * 10000000) / 10000000;
    }

    function normalizeDraggedBounds(bounds) {
        if (!bounds) {
            return null;
        }

        return {
            north: roundCoord(bounds.north),
            south: roundCoord(bounds.south),
            east: roundCoord(bounds.east),
            west: roundCoord(bounds.west),
        };
    }

    InteractiveMapEditor.prototype.onOverlayBoundsDragged = function (bounds) {
        if (!bounds) {
            return;
        }

        var normalized = normalizeDraggedBounds(bounds);
        if (!normalized) {
            return;
        }

        this.data.north = normalized.north;
        this.data.south = normalized.south;
        this.data.east = normalized.east;
        this.data.west = normalized.west;

        this.toolbar.write(normalized, true);

        if (this.mapManager) {
            this.mapManager.drawBoundsRectangle(normalized);
        }

        if (this.overlayManager && typeof this.overlayManager.syncBounds === 'function') {
            this.overlayManager.syncBounds(normalized);
        }

        this.saveDraggedPosition();
    };

    InteractiveMapEditor.prototype.saveDraggedPosition = function () {
        var self = this;
        clearTimeout(this._saveDragTimer);

        this._saveDragTimer = setTimeout(function () {
            var payload = self.toolbar.readAll();
            self.toolbar.setStatus('Saving position…');

            self.request('PUT', '', payload)
                .then(function (json) {
                    if (json.data) {
                        self.data = json.data;
                        self.toolbar.write(json.data, true);
                    }
                    self.toolbar.setStatus('Position saved');
                    self.toolbar.showToast(json.message || 'Overlay position saved', 'success');
                })
                .catch(function (err) {
                    self.toolbar.setStatus('Position save failed');
                    self.toolbar.showToast(err.message, 'error');
                });
        }, 250);
    };

    InteractiveMapEditor.prototype.applyLivePreview = function () {
        if (!this.mapManager || !this.overlayManager) {
            return;
        }

        if (this.overlayManager.isDragging && this.overlayManager.isDragging()) {
            return;
        }

        var form = this.toolbar.readAll();
        var bounds = boundsFromData(form);

        this.mapManager.setZoomLimits(form.min_zoom || 0, form.max_zoom || 22);
        this.mapManager.drawBoundsRectangle(bounds);

        if (bounds) {
            this.mapManager.fitBounds(bounds);
        }

        if (this.data.overlay_url && bounds) {
            this.overlayManager.update({
                url: this.data.overlay_url,
                bounds: bounds,
                opacity: form.overlay_opacity,
            });
        } else {
            this.overlayManager.clear();
        }
    };

    InteractiveMapEditor.prototype.updatePreview = function (url) {
        var hasOverlay = !!url;
        if (this.previewImg) {
            if (hasOverlay) {
                this.previewImg.src = url;
                this.previewImg.classList.remove('hidden');
            } else {
                this.previewImg.removeAttribute('src');
                this.previewImg.classList.add('hidden');
            }
        }
        if (this.previewEmpty) {
            this.previewEmpty.classList.toggle('hidden', hasOverlay);
        }
        if (this.deleteBtn) {
            this.deleteBtn.disabled = !hasOverlay;
        }
    };

    InteractiveMapEditor.prototype.request = function (method, path, body, isFormData) {
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': this.csrf,
        };

        if (!isFormData) {
            headers['Content-Type'] = 'application/json';
        }

        return fetch(this.apiBase + path, {
            method: method,
            headers: headers,
            body: body ? (isFormData ? body : JSON.stringify(body)) : undefined,
            credentials: 'same-origin',
        }).then(function (response) {
            return response.json().then(function (json) {
                if (!response.ok) {
                    var message = json.message || 'Request failed';
                    if (json.errors) {
                        message = Object.values(json.errors).flat().join(' ');
                    }
                    throw new Error(message);
                }
                return json;
            });
        });
    };

    InteractiveMapEditor.prototype.mergeData = function (payload) {
        this.data = payload || this.data;
        this.toolbar.write(this.data);
        this.updatePreview(this.data.overlay_url);
        this.applyLivePreview();
    };

    InteractiveMapEditor.prototype.saveSettings = function () {
        var self = this;
        var payload = this.toolbar.readAll();
        this.toolbar.setStatus('Saving…');

        this.request('PUT', '', payload)
            .then(function (json) {
                self.mergeData(json.data);
                self.toolbar.setStatus('Settings saved');
                self.toolbar.showToast(json.message || 'Saved', 'success');
            })
            .catch(function (err) {
                self.toolbar.setStatus('Save failed');
                self.toolbar.showToast(err.message, 'error');
            });
    };

    InteractiveMapEditor.prototype.uploadOverlay = function (file) {
        var self = this;
        var formData = new FormData();
        formData.append('overlay', file);

        this.toolbar.setStatus('Uploading overlay…');

        this.request('POST', '/overlay', formData, true)
            .then(function (json) {
                self.fileInput.value = '';
                self.mergeData(json.data);
                self.toolbar.setStatus('Overlay uploaded');
                self.toolbar.showToast(json.message || 'Overlay uploaded', 'success');
            })
            .catch(function (err) {
                self.toolbar.setStatus('Upload failed');
                self.toolbar.showToast(err.message, 'error');
            });
    };

    InteractiveMapEditor.prototype.deleteOverlay = function () {
        var self = this;
        this.toolbar.setStatus('Removing overlay…');

        this.request('DELETE', '/overlay')
            .then(function (json) {
                self.mergeData(json.data);
                self.toolbar.setStatus('Overlay removed');
                self.toolbar.showToast(json.message || 'Overlay removed', 'success');
            })
            .catch(function (err) {
                self.toolbar.setStatus('Delete failed');
                self.toolbar.showToast(err.message, 'error');
            });
    };

    function bootEditors() {
        document.querySelectorAll('.interactive-map-editor').forEach(function (root) {
            if (root.__interactiveMapEditor) {
                return;
            }
            root.__interactiveMapEditor = new InteractiveMapEditor(root);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootEditors);
    } else {
        bootEditors();
    }

    IM.InteractiveMapEditor = InteractiveMapEditor;
    IM.bootEditors = bootEditors;
})(window);
